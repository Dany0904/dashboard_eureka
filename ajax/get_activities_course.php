<?php
// Incluir las bibliotecas necesarias de Moodle
require_once(__DIR__ . '/../../../config.php');
require_once(__DIR__ . '/lib.php');
require "$CFG->libdir/tablelib.php";
use core_completion\progress;
require_once("{$CFG->libdir}/completionlib.php");

global $DB, $CFG;
require_login();

// Verifica si el usuario está logueado y tiene el rol de administrador del sitio, manager o es manager de curso
$context_system = context_system::instance();

if (!isloggedin() || isguestuser() || 
    (!has_capability('moodle/site:config', $context_system) && 
    !has_capability('moodle/role:manager', $context_system) && 
    !has_capability('moodle/course:manageactivities', context_course::instance(SITEID)))) {
    
    redirect('/', 'No tienes permiso para ver esta sección', 'error', 0);
}

// Asegurar que la respuesta sea JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Permitir acceso desde AJAX

// Obtener el ID del curso desde la URL
$id_course = optional_param('id', 0, PARAM_INT);

// Verificar si se proporcionó un ID de curso válido
if (!$id_course) {
    echo json_encode(["error" => "No se proporcionó un ID de curso válido"]);
    exit;
}

// Obtener información del curso
$courseObj = $DB->get_record("course", ['id' => $id_course]);

if (!$courseObj) {
    echo json_encode(["error" => "El curso no existe"]);
    exit;
}

// Obtener todas las actividades del curso
$sql = "SELECT cm.id, cm.course, cm.module, cm.instance, cm.visible, cm.completion, m.name AS modname
        FROM {course_modules} cm 
        JOIN {modules} m ON cm.module = m.id 
        WHERE cm.course = :courseid AND cm.visible = 1";

$all_activities = $DB->get_records_sql($sql, ['courseid' => $id_course]);
$num_participants = count(enrol_get_course_users($id_course));

$data = [];

foreach ($all_activities as $activity) {
    // Obtener el nombre de la actividad
    $activity_name = $DB->get_field($activity->modname, 'name', ['id' => $activity->instance]) ?? "Sin nombre";

    // Calcular los que no han completado la actividad
    $not_completed = max(0, $num_participants - $activity->completion);

    // Agregar la actividad a la respuesta
    $data[] = [
        'id' => $activity->id,
        'modname' => ucfirst($activity->modname), // Capitalizar nombre del módulo
        'name' => $activity_name,
        'completion' => $activity->completion,
        'not_completed' => $not_completed
    ];
}

// Enviar la respuesta JSON con el nombre del curso
echo json_encode([
    "course_name" => $courseObj->fullname,
    "data" => $data
]);
exit;
