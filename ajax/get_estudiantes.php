<?php
define('AJAX_SCRIPT', true);
require_once(__DIR__ . '/../../../config.php');

require_login();

header('Content-Type: application/json');

$courseid = required_param('courseid', PARAM_INT);

$sql = "
    SELECT DISTINCT u.id,
           CONCAT(u.firstname, ' ', u.lastname) AS fullname
    FROM {user} u
    INNER JOIN {user_enrolments} ue ON ue.userid = u.id
    INNER JOIN {enrol} e ON e.id = ue.enrolid
    WHERE e.courseid = :courseid
      AND u.deleted = 0
      AND ue.status = 0
    ORDER BY u.firstname, u.lastname
";

$users = $DB->get_records_sql($sql, ['courseid' => $courseid]);

$data = [];
foreach ($users as $u) {
    $data[] = [
        'id' => $u->id,
        'fullname' => format_string($u->fullname)
    ];
}

echo json_encode(['data' => $data]);
