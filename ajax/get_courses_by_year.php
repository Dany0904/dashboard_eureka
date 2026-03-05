<?php
require_once(__DIR__.'/../../../config.php');

// Asegurar que la respuesta sea JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Permitir acceso desde AJAX

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

$year = isset($_GET['year']) ? intval($_GET['year']) : 0;

if ($year > 0) {
    // Consulta para obtener los cursos creados en el año especificado
    $sql = "SELECT id, fullname, timecreated 
            FROM {course} 
            WHERE YEAR(FROM_UNIXTIME(timecreated)) = :year";

    $courses = $DB->get_records_sql($sql, ['year' => $year]);

    $coursesArray = [];
    foreach ($courses as $course) {
        $coursesArray[] = [
            'id' => $course->id,
            'fullname' => $course->fullname,
            'timecreated' => date('Y-m-d', $course->timecreated)
        ];
    }

    echo json_encode(['data' => $coursesArray]);
} else {
    echo json_encode(['data' => []]);
}
?>
