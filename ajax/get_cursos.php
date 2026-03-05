<?php
define('AJAX_SCRIPT', true);
require_once(__DIR__ . '/../../../config.php');

require_login();

header('Content-Type: application/json');

$context = context_system::instance();

if (!has_capability('moodle/site:config', $context) &&
    !has_capability('moodle/role:manager', $context)) {
    echo json_encode(['data' => []]);
    exit;
}

$sql = "
    SELECT id, fullname
    FROM {course}
    WHERE visible = 1
      AND id <> :siteid
    ORDER BY fullname
";

$courses = $DB->get_records_sql($sql, ['siteid' => SITEID]);

$data = [];
foreach ($courses as $c) {
    $data[] = [
        'id' => $c->id,
        'fullname' => format_string($c->fullname)
    ];
}

echo json_encode(['data' => $data]);
