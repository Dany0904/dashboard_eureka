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

// Año actual en rango epoch [inicio, fin)
$year  = (int)date('Y');
$start = strtotime($year . '-01-01 00:00:00');
$end   = strtotime(($year + 1) . '-01-01 00:00:00');

$params = [
    'start'   => $start,
    'end'     => $end,
    'siteid'  => SITEID,
    'guestid' => 1,
];

// OJO: logstore_standard_log puede tener MUCHÍSIMO volumen.
// Esto reduce costo:
// - rango por timecreated (usa índice)
// - excluir curso front page
// - excluir guest
// Opcional: filtrar acciones relevantes (ver comentario dentro)
$sql = "
    SELECT
        c.id AS course_id,
        c.fullname AS course_name,
        COUNT(DISTINCT l.userid) AS active_users
    FROM {logstore_standard_log} l
    JOIN {course} c ON c.id = l.courseid
    WHERE l.timecreated >= :start
      AND l.timecreated <  :end
      AND l.courseid <> :siteid
      AND l.userid <> :guestid
      AND c.category > 0
      -- Opcional (reduce MUCHO): solo eventos típicos de actividad del usuario
      -- AND l.edulevel = 2
    GROUP BY c.id, c.fullname
    ORDER BY active_users DESC
    LIMIT 10
";

$courses_activity = $DB->get_records_sql($sql, $params);

if (!$courses_activity) {
    echo json_encode(["error" => "No se encontraron datos para el año $year"]);
    exit;
}

$data = [];
foreach ($courses_activity as $activity) {
    $data[] = [
        'CourseId' => (int)$activity->course_id,
        'CourseName' => (string)$activity->course_name,
        'Activity' => (int)$activity->active_users
    ];
}

echo json_encode(['data' => $data], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
exit;
