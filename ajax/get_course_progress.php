<?php

global $DB, $CFG;
require_once(__DIR__ . '/../../../config.php');
require_once(__DIR__ . '/lib.php');
require "$CFG->libdir/tablelib.php";
use core_completion\progress;
require_once("{$CFG->libdir}/completionlib.php");

require_login();

// Verifica si el usuario está logueado y tiene el rol de administrador del sitio, manager o es manager de curso
$context_system = context_system::instance();

if (!isloggedin() || isguestuser() || 
    (!has_capability('moodle/site:config', $context_system) && 
    !has_capability('moodle/role:manager', $context_system) && 
    !has_capability('moodle/course:manageactivities', context_course::instance(SITEID)))) {
    
    redirect('/', 'No tienes permiso para ver esta sección', 'error', 0);
}


// Configurar JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// 🔹 Obtener parámetros de paginación desde DataTables
$start = isset($_GET['start']) ? intval($_GET['start']) : 0;
$length = isset($_GET['length']) ? intval($_GET['length']) : 10;
$draw = isset($_GET['draw']) ? intval($_GET['draw']) : 1;  // 🔹 Convertir `draw` en número entero

// Asegurar que los valores sean válidos
if ($length < 1 || $length > 100) { 
    $length = 10;
}
if ($start < 0) {
    $start = 0;
}

// 🚀 Obtener el total de cursos sin filtros
$total_courses = $DB->count_records_sql("SELECT COUNT(*) FROM {course} WHERE category > 0");

// 🚀 Obtener cursos con paginación
$all_courses_progress = $DB->get_records_sql("
    SELECT c.id, c.fullname, c.shortname, c.startdate, c.enddate, 
           cat.name AS category
    FROM {course} c
    LEFT JOIN {course_categories} cat ON c.category = cat.id
    WHERE c.category > 0
    ORDER BY c.startdate DESC
    LIMIT $length OFFSET $start
");

// 🚀 Verificar si hay cursos
if (!$all_courses_progress) {
    echo json_encode([
        "draw" => $draw,
        "recordsTotal" => 0,
        "recordsFiltered" => 0,
        "data" => []
    ]);
    exit;
}

// 🚀 Obtener progreso por curso SIN decimales y ordenado de mayor a menor
$course_progress_data = $DB->get_records_sql("
    SELECT e.courseid, FLOOR(AVG(gg.finalgrade)) AS avg_progress
    FROM {user_enrolments} ue
    JOIN {enrol} e ON ue.enrolid = e.id
    LEFT JOIN {grade_grades} gg ON gg.userid = ue.userid
    LEFT JOIN {grade_items} gi ON gi.id = gg.itemid AND gi.courseid = e.courseid
    WHERE gi.itemtype = 'course'
    GROUP BY e.courseid
    ORDER BY avg_progress DESC  -- 🔹 Ordenar por progreso de mayor a menor
");

// 🚀 Obtener actividades por curso
$activities_query = $DB->get_records_sql("
    SELECT gi.courseid, gi.itemname AS activity_name, 
           COALESCE(ROUND(gg.finalgrade), 'No hay calificación') AS final_grade, 
           COALESCE(FROM_UNIXTIME(gg.timemodified), 'Sin información disponible') AS date_graded
    FROM {grade_items} gi
    LEFT JOIN {grade_grades} gg ON gg.itemid = gi.id
    WHERE gi.itemname IS NOT NULL AND gi.itemname != ''
    ORDER BY gi.courseid, gi.itemname
");

// Reestructurar progreso por curso
$progress_per_course = [];
foreach ($course_progress_data as $row) {
    $progress_per_course[$row->courseid] = intval($row->avg_progress) . "%"; // 🔹 Convertir a entero y agregar "%"
}

// Reestructurar actividades por curso
$activities_per_course = [];
foreach ($activities_query as $activity) {
    $activities_per_course[$activity->courseid][] = [
        "activity_name" => $activity->activity_name ?? "Sin nombre",
        "final_grade" => $activity->final_grade ?? "No hay calificación",
        "date_graded" => $activity->date_graded ?? "Sin información disponible"
    ];
}

// 🚀 Construir la respuesta para DataTables
$data = [];
foreach ($all_courses_progress as $course) {
    $id_course = $course->id;

    // Calcular progreso sin decimales
    $percentage_progress = $progress_per_course[$id_course] ?? "0%";

    // Obtener actividades
    $course_activities = $activities_per_course[$id_course] ?? [];

    // Formato de fechas
    $startdate = ($course->startdate > 0) ? date('Y/m/d', $course->startdate) : "Sin fecha de inicio";
    $enddate = ($course->enddate > 0) ? date('Y/m/d', $course->enddate) : "Sin fecha de fin";

    $data[] = [
        'id' => $id_course,
        'fullname' => $course->fullname,
        'shortname' => $course->shortname,
        'category' => $course->category ?? "Desconocida",
        'startdate' => $startdate,
        'enddate' => $enddate,
        'progress' => $percentage_progress
    ];
}

// 🔹 Asegurar el orden por progreso en PHP también
usort($data, function ($a, $b) {
    return intval($b['progress']) <=> intval($a['progress']);
});

// 🔹 Enviar JSON corregido con `draw`, `recordsTotal` y `recordsFiltered`
echo json_encode([
    "draw" => $draw, 
    "recordsTotal" => $total_courses, 
    "recordsFiltered" => $total_courses, 
    "data" => $data
], JSON_UNESCAPED_UNICODE);
exit;
