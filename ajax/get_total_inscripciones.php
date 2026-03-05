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

// Año actual y rango epoch [inicio, fin)
$year = (int)date('Y');
$start = strtotime($year . '-01-01 00:00:00');
$end   = strtotime(($year + 1) . '-01-01 00:00:00');

$params = [
    'start'  => $start,
    'end'    => $end,
    'siteid' => SITEID,
];

// Opción A (tu lógica actual): cuenta enrolments (puede inflarse si hay duplicados por distintos enrol)
$sql = "
    SELECT c.id AS courseid,
           c.fullname AS coursename,
           COUNT(ue.id) AS totalenrollments
      FROM {user_enrolments} ue
      JOIN {enrol} e ON e.id = ue.enrolid
      JOIN {course} c ON c.id = e.courseid
     WHERE ue.timecreated >= :start
       AND ue.timecreated <  :end
       AND c.id <> :siteid
  GROUP BY c.id, c.fullname
  ORDER BY totalenrollments DESC
     LIMIT 10
";

$enrollmentsByCourse = $DB->get_records_sql($sql, $params);

if (!$enrollmentsByCourse) {
    echo json_encode(["error" => "No se encontraron datos para el año $year"]);
    exit;
}

$data = [];
foreach ($enrollmentsByCourse as $course) {
    $data[] = [
        'CourseId' => (int)$course->courseid,
        'CourseName' => (string)$course->coursename,
        'TotalEnrollments' => (int)$course->totalenrollments
    ];
}

echo json_encode(['data' => $data], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
exit;
