<?php
require_once(__DIR__ . '/../../../config.php');

global $DB;
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

// Últimos 5 minutos
$time_threshold = time() - 300;

// Contar usuarios "en línea" (último acceso reciente), excluyendo:
// - guest (id=1)
// - deleted=1
$totalOnlineUsers = (int)$DB->get_field_sql("
    SELECT COUNT(DISTINCT ul.userid)
      FROM {user_lastaccess} ul
      JOIN {user} u ON u.id = ul.userid
     WHERE ul.timeaccess >= ?
       AND u.deleted = 0
       AND u.id <> 1
", [$time_threshold]);

echo json_encode([
    "total_online_users" => $totalOnlineUsers
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

exit;
