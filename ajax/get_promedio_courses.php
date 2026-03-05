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

// Obtener todos los cursos (excepto la categoría 0 que suele ser cursos ocultos o sin categoría válida)
$all_courses_average = $DB->get_records_sql('SELECT * FROM {course} WHERE category > 0');
$all_categories = $DB->get_records_sql('SELECT * FROM {course_categories}');

$data = [];

foreach ($all_courses_average as $course_average) {
    $all_usersgrades = local_sub_dashboard_get_grades_users_courses($course_average->id);

    // Verificación para evitar división por cero
    $average = (is_array($all_usersgrades) && count($all_usersgrades) > 0) 
        ? local_sub_dashboard_average_course($all_usersgrades) 
        : 0; // Si no hay calificaciones, el promedio es 0

    // Registrar en el log para depuración
    error_log("Curso ID: {$course_average->id} - Calificaciones: " . json_encode($all_usersgrades));

    // Buscar la categoría del curso
    $categorytype = "Desconocida";
    foreach ($all_categories as $category) {
        if ($category->id == $course_average->category) {
            $categorytype = $category->name;
            break;
        }
    }

    // Convertir fechas UNIX a formato legible
    $startdate = ($course_average->startdate > 0) ? date('Y/m/d', $course_average->startdate) : "Sin fecha de inicio";
    $enddate = ($course_average->enddate > 0) ? date('Y/m/d', $course_average->enddate) : "Sin fecha de fin";

    // Construir la respuesta JSON
    $data[] = [
        'id' => $course_average->id,
        'fullname' => $course_average->fullname,
        'shortname' => $course_average->shortname,
        'category' => $categorytype,
        'startdate' => $startdate,
        'enddate' => $enddate,
        'average' => number_format($average, 2)
    ];
}

// Enviar la respuesta JSON
echo json_encode(["data" => $data]);
exit;
