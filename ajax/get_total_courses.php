<?php
global $DB;

require_once(__DIR__ . '/../../../config.php');
require_login();

$context_system = context_system::instance();

if (!isloggedin() || isguestuser() ||
    (!has_capability('moodle/site:config', $context_system) &&
     !has_capability('moodle/role:manager', $context_system) &&
     !has_capability('moodle/course:manageactivities', context_course::instance(SITEID)))) {

    redirect('/', 'No tienes permiso para ver esta sección', 'error', 0);
}

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

if (!$DB) {
    echo json_encode(["error" => "Error: No se pudo conectar a la base de datos"]);
    exit;
}

/**
 * Reglas recomendadas:
 * - Excluir el curso "Front page" (id = SITEID, normalmente 1)
 * - Excluir timecreated = 0 (evita años raros)
 */
$params = [
    'siteid' => SITEID, // normalmente 1
];

// Cursos por año
$sql = "
    SELECT YEAR(FROM_UNIXTIME(c.timecreated)) AS year,
           COUNT(1) AS total_courses
      FROM {course} c
     WHERE c.id <> :siteid
       AND c.timecreated > 0
  GROUP BY YEAR(FROM_UNIXTIME(c.timecreated))
  ORDER BY year ASC
";
$coursesByYear = $DB->get_records_sql($sql, $params);

// Total cursos (mantengo objeto con alias total)
$sql_total_courses = "
    SELECT COUNT(1) AS total
      FROM {course} c
     WHERE c.id <> :siteid
       AND c.timecreated > 0
";
$totalCourses = $DB->get_record_sql($sql_total_courses, $params);

if (!$coursesByYear || !$totalCourses) {
    echo json_encode(["error" => "No se encontraron datos"]);
    exit;
}

// Formatear datos (mantengo misma estructura)
$data = [];
foreach ($coursesByYear as $course) {
    $data[] = [
        'Year' => (int)$course->year,
        'TotalCourses' => (int)$course->total_courses
    ];
}

$jsonResponse = json_encode([
    'data' => $data,
    'totalCourses' => $totalCourses, // objeto con ->total (como tu estilo)
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(["error" => "Error en json_encode: " . json_last_error_msg()]);
    exit;
}

echo $jsonResponse;
exit;
