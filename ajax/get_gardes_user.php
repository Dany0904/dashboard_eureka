<?php

require_once(__DIR__ . '/../../../config.php');
require_once(__DIR__ . '/lib.php');
require "$CFG->libdir/tablelib.php";
use core_completion\progress;
require_once("{$CFG->libdir}/completionlib.php");

global $DB;
require_login();

// Verifica si el usuario está logueado y tiene el rol de administrador del sitio, manager o es manager de curso
$context_system = context_system::instance();

if (!isloggedin() || isguestuser() || 
    (!has_capability('moodle/site:config', $context_system) && 
    !has_capability('moodle/role:manager', $context_system) && 
    !has_capability('moodle/course:manageactivities', context_course::instance(SITEID)))) {
    
    redirect('/', 'No tienes permiso para ver esta sección', 'error', 0);
}

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Verificar si se recibió un ID de usuario
if (!isset($_GET['userid']) || empty($_GET['userid'])) {
    echo json_encode(["error" => "No se proporcionó un ID de usuario"]);
    exit;
}

$userid = intval($_GET['userid']); // Sanitizar el input

// Obtener información del usuario (nombre completo)
$user = $DB->get_record('user', ['id' => $userid], 'firstname, lastname');

$username = ($user) ? $user->firstname . ' ' . $user->lastname : 'Usuario desconocido';

// Obtener los cursos en los que el usuario está inscrito
$courses = enrol_get_users_courses($userid);
$cursos = [];

foreach ($courses as $course) {
    $curso = new stdClass();
    $curso->courseid = $course->id;
    $curso->course_name = isset($course->fullname) ? $course->fullname : 'Curso Desconocido'; // Asegura el nombre del curso
    $curso->category = $course->category;
    $curso->userid = $userid;
    $curso->username = $username; // Agregamos el nombre del usuario

    // Obtener el nombre de la categoría del curso
    $category = $DB->get_record('course_categories', ['id' => $course->category]);
    $curso->category_name = $category ? $category->name : 'Sin categoría';

    // Obtener la calificación final del curso
    $grade = grade_get_course_grade($userid, $course->id);
    $curso->grade = ($grade !== false && !empty($grade->grade)) ? round($grade->grade, 2) : "Sin promedio";
    $curso->aprobado = (is_numeric($curso->grade) && $curso->grade >= 80) ? 'Aprobado' : 'Desaprobado';

    // Obtener las actividades del curso con sus calificaciones
    $activities = $DB->get_records_sql("
        SELECT gi.id AS item_id, gi.itemname AS activity_name, 
               COALESCE(ROUND(gg.finalgrade, 2), 'No hay calificación') AS final_grade, 
               COALESCE(FROM_UNIXTIME(gg.timemodified), 'Sin información disponible') AS date_graded
        FROM {grade_items} gi
        LEFT JOIN {grade_grades} gg ON gg.itemid = gi.id AND gg.userid = :userid
        WHERE gi.courseid = :courseid AND gi.itemname IS NOT NULL AND gi.itemname != ''
        ORDER BY gi.itemname
    ", ['userid' => $userid, 'courseid' => $course->id]);

    $curso->activities = []; // Asegurar que siempre esté definido

    foreach ($activities as $activity) {
        $curso->activities[] = [
            "activity_name" => $activity->activity_name ?? "Sin nombre",
            "final_grade" => $activity->final_grade ?? "No hay calificación",
            "date_graded" => $activity->date_graded ?? "Sin información disponible"
        ];
    }

    // Si no hay actividades, agregar un array vacío
    if (empty($curso->activities)) {
        $curso->activities = [];
    }

    $cursos[] = $curso;
}

// Limpiar el buffer de salida antes de enviar JSON (evita errores en la conversión)
if (ob_get_length()) ob_end_clean();

// Verificar si la respuesta JSON es válida antes de enviarla
$jsonResponse = json_encode(["courses" => $cursos], JSON_UNESCAPED_UNICODE);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(["error" => "Error en la conversión a JSON: " . json_last_error_msg()]);
    exit;
}

echo $jsonResponse;
exit;
