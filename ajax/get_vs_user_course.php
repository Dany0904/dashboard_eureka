<?php
// /local/dashboard/ajax/get_vs_user_course.php

define('AJAX_SCRIPT', true);

require_once(__DIR__ . '/../../../config.php');

global $DB, $CFG;

require_login();

$contextsystem = context_system::instance();

// Mantengo tu intención de control de acceso, pero lo correcto aquí es exigir algo a nivel sistema.
// Ajusta la capability a la que uses realmente en tu plugin/dashboard.
if (!has_capability('moodle/site:config', $contextsystem) &&
    !has_capability('moodle/site:viewreports', $contextsystem) &&
    !has_capability('moodle/role:manager', $contextsystem)) {
    throw new required_capability_exception($contextsystem, 'moodle/site:viewreports', 'nopermissions', '');
}

// Validación de sesskey (recomendado si lo estás llamando desde UI Moodle).
$sesskey = optional_param('sesskey', '', PARAM_ALPHANUM);
if (!empty($sesskey) && !confirm_sesskey($sesskey)) {
    throw new moodle_exception('invalidsesskey', 'error');
}

header('Content-Type: application/json; charset=utf-8');

// Query agregada:
// - participants: usuarios con enrol + user_enrolments activos, y usuario no eliminado/suspendido
// - completed: course_completions.timecompleted no nulo
$sql = "
    SELECT
        c.id,
        c.fullname,
        cat.name AS category,
        COUNT(DISTINCT u.id) AS participants,
        COUNT(DISTINCT CASE WHEN cc.timecompleted IS NOT NULL THEN u.id END) AS completed
    FROM {course} c
    JOIN {course_categories} cat
      ON cat.id = c.category
    LEFT JOIN {enrol} e
      ON e.courseid = c.id
     AND e.status = 0
    LEFT JOIN {user_enrolments} ue
      ON ue.enrolid = e.id
     AND ue.status = 0
    LEFT JOIN {user} u
      ON u.id = ue.userid
     AND u.deleted = 0
     AND u.suspended = 0
    LEFT JOIN {course_completions} cc
      ON cc.course = c.id
     AND cc.userid = u.id
    WHERE c.category > 0
    GROUP BY c.id, c.fullname, cat.name
    ORDER BY cat.name ASC, c.fullname ASC
";

$records = $DB->get_records_sql($sql);

$data = [];
foreach ($records as $r) {
    $participants = (int)$r->participants;
    $completed    = (int)$r->completed;

    // Por seguridad numérica (evita negativos por datos raros).
    $notcompleted = max(0, $participants - $completed);

    $data[] = [
        'id'            => (int)$r->id,
        'fullname'      => $r->fullname,
        'category'      => $r->category,
        'participants'  => $participants,
        'completed'     => $completed,
        'not_completed' => $notcompleted,
    ];
}

echo json_encode(['data' => $data], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
exit;
