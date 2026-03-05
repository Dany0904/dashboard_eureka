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

global $DB;


// Obtener el parámetro del curso desde GET
$id_course = optional_param('id', 0, PARAM_INT);

if (!$id_course) {
    echo json_encode(["error" => "No se proporcionó un ID de curso válido"]);
    exit;
}

// Obtener el curso desde la base de datos
$courseObj = $DB->get_record("course", ['id' => $id_course]);

if (!$courseObj) {
    echo json_encode(["error" => "El curso no existe"]);
    exit;
}

// Obtener la lista de participantes
$participants_query = enrol_get_course_users($courseObj->id);
$gradescourse = local_sub_dashboard_get_grades_users_courses($id_course);
$coursecomplete = $DB->get_records_sql("SELECT * FROM {course_completions} WHERE course = ? AND timecompleted IS NOT NULL ORDER BY timecompleted ASC", [$id_course]);

$participants = [];

foreach ($participants_query as $participant) {
    $percentageuser = core_completion\progress::get_course_progress_percentage($courseObj, $participant->id);
    $participant_data = [
        'id' => $participant->id,
        'fullname' => fullname($participant),
        'email' => $participant->email ?? "No disponible",
        'progress' => !is_null($percentageuser) ? floor($percentageuser) . "%" : "No hay avance",
        'grade' => "No disponible",
        'datecomplete' => "No completado",
        'year' => "N/A",
        'month' => "N/A"
    ];

    // Asignar calificación del usuario
    foreach ($gradescourse as $gradeuser) {
        if ($gradeuser->userid == $participant->id && isset($gradeuser->finalgrade)) {
            $participant_data['grade'] = substr($gradeuser->finalgrade, 0, -6);
            break;
        }
    }

    // Asignar fecha de finalización del curso
    foreach ($coursecomplete as $info) {
        if ($participant->id == $info->userid) {
            $participant_data['datecomplete'] = date('Y/m/d', $info->timecompleted);
            $participant_data['year'] = date('Y', $info->timecompleted);
            $participant_data['month'] = date('m', $info->timecompleted);
            break;
        }
    }

    $participants[] = $participant_data;
}

// Respuesta JSON
echo json_encode([
    "course" => [
        "id" => $courseObj->id,
        "fullname" => $courseObj->fullname,
        "shortname" => $courseObj->shortname,
    ],
    "participants" => $participants
]);
exit;
