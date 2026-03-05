<?php
global $DB;

require_once(__DIR__ . '/../../../config.php');
require_login();

// Verifica permisos (dejo tu lógica para que no se rompa)
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
 * REGLAS (AJUSTADAS PARA INCLUIR GUEST)
 * - timecreated > 0 (evita 1970/1969)
 * - deleted = 0 (no contar usuarios eliminados)
 * - (YA NO se excluye u.id = 1; ahora SÍ cuenta guest)
 */

// Usuarios por año
$sql = "
    SELECT YEAR(FROM_UNIXTIME(u.timecreated)) AS year,
           COUNT(1) AS total_users
      FROM {user} u
     WHERE u.timecreated > 0
       AND u.deleted = 0
  GROUP BY YEAR(FROM_UNIXTIME(u.timecreated))
  ORDER BY year ASC
";
$usersByYear = $DB->get_records_sql($sql);

// Total usuarios
$sql_total_users = "
    SELECT COUNT(1) AS total
      FROM {user} u
     WHERE u.timecreated > 0
       AND u.deleted = 0
";
$totalUsuarios = $DB->get_record_sql($sql_total_users);

if (!$usersByYear || !$totalUsuarios) {
    echo json_encode(["error" => "No se encontraron datos"]);
    exit;
}

$data = [];
foreach ($usersByYear as $user) {
    $data[] = [
        'Year' => (int)$user->year,
        'TotalUsers' => (int)$user->total_users
    ];
}

// IMPORTANTE: dejo totalUsuarios como objeto (como en tu versión que “sí jala”)
$jsonResponse = json_encode([
    'data' => $data,
    'totalUsuarios' => $totalUsuarios
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(["error" => "Error en json_encode: " . json_last_error_msg()]);
    exit;
}

echo $jsonResponse;
exit;
