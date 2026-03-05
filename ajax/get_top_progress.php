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

// Año actual por rango epoch [inicio, fin)
$year  = (int)date('Y');
$start = strtotime($year . '-01-01 00:00:00');
$end   = strtotime(($year + 1) . '-01-01 00:00:00');

$params = [
    'start'   => $start,
    'end'     => $end,
    'siteid'  => SITEID, // Front page
    'guestid' => 1,      // Guest user
];

/**
 * Qué hace:
 * 1) Toma cursos con actividad en logs en el año (courses_active)
 * 2) Calcula total de módulos "completables" por curso (totalmods)
 * 3) Lista usuarios inscritos por curso (enrolledusers)
 * 4) Cuenta módulos completados por usuario y curso (completedmods)
 * 5) Promedia progreso por curso: AVG(completed/totalmods)*100
 *
 * Nota: Solo cursos con completion habilitado en al menos 1 actividad (totalmods > 0).
 */
$sql = "
    SELECT
        c.id,
        c.fullname,
        ROUND(AVG(COALESCE(cc.completedmods, 0) / tm.totalmods) * 100, 2) AS avgprogress
    FROM
        (SELECT DISTINCT l.courseid
           FROM {logstore_standard_log} l
          WHERE l.timecreated >= :start
            AND l.timecreated <  :end
            AND l.courseid <> :siteid
        ) ca
    JOIN {course} c
      ON c.id = ca.courseid
    JOIN
        (SELECT cm.course, COUNT(1) AS totalmods
           FROM {course_modules} cm
          WHERE cm.deletioninprogress = 0
            AND cm.completion <> 0
          GROUP BY cm.course
        ) tm
      ON tm.course = c.id AND tm.totalmods > 0
    JOIN
        (SELECT e.courseid, ue.userid
           FROM {enrol} e
           JOIN {user_enrolments} ue ON ue.enrolid = e.id
           JOIN {user} u ON u.id = ue.userid
          WHERE e.status = 0
            AND ue.status = 0
            AND u.deleted = 0
            AND u.id <> :guestid
        ) eu
      ON eu.courseid = c.id
    LEFT JOIN
        (SELECT cm.course, cmc.userid, COUNT(DISTINCT cmc.coursemoduleid) AS completedmods
           FROM {course_modules_completion} cmc
           JOIN {course_modules} cm ON cm.id = cmc.coursemoduleid
          WHERE cm.deletioninprogress = 0
            AND cm.completion <> 0
            AND cmc.completionstate IN (1, 2)
          GROUP BY cm.course, cmc.userid
        ) cc
      ON cc.course = c.id AND cc.userid = eu.userid
    WHERE
        c.category > 0
    GROUP BY
        c.id, c.fullname
    ORDER BY
        avgprogress DESC
    LIMIT 10
";

$rows = $DB->get_records_sql($sql, $params);

if (!$rows) {
    echo json_encode([
        "data" => [],
        "status" => 200
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

$data = [];
$pos = 1;
foreach ($rows as $r) {
    $avg = (float)$r->avgprogress; // 0..100
    $data[] = [
        "id" => (int)$r->id,
        "fullname" => (string)$r->fullname,
        "tprogress" => $avg,                    // número (0..100)
        "pprogress" => floor($avg) . "%",       // string "NN%"
        "position" => $pos++,
    ];
}

echo json_encode([
    "data" => $data,
    "status" => 200
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

exit;
