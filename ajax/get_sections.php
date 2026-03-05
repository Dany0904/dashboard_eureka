<?php
define('AJAX_SCRIPT', true);
require_once(__DIR__ . '/../../../config.php');

require_login();
header('Content-Type: application/json');

$courseid = required_param('courseid', PARAM_INT);

$sections = $DB->get_records('course_sections', ['course'=>$courseid], 'section ASC');

$data = [];

foreach ($sections as $s) {

    // Evitar sección 0 si no la quieres
    if ($s->section == 0) continue;

    $data[] = [
        'id' => $s->id,
        'name' => $s->name ?: 'Sección '.$s->section
    ];
}

echo json_encode(['data'=>$data]);
exit;
