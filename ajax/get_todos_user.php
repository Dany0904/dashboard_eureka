<?php

global $DB;
require_once(__DIR__ . '/../../../config.php');
require_once(__DIR__ . '/lib.php');
require_login();

// Verifica permisos de usuario
$context_system = context_system::instance();
if (!isloggedin() || isguestuser() || 
    (!has_capability('moodle/site:config', $context_system) && 
    !has_capability('moodle/role:manager', $context_system) && 
    !has_capability('moodle/course:manageactivities', context_course::instance(SITEID)))) {
    redirect('/', 'No tienes permiso para ver esta sección', 'error', 0);
}

// Configurar cabeceras para respuesta JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Parámetros de paginación
$start = isset($_GET['start']) ? intval($_GET['start']) : 0;
$length = isset($_GET['length']) ? intval($_GET['length']) : 100; // Por defecto, 100 usuarios por página

// Validar paginación
if ($length < 1 || $length > 1000) { 
    $length = 100;
}
if ($start < 0) {
    $start = 0;
}

// 🚀 Consulta SQL optimizada con paginación
$sql = "SELECT id, username, firstname, lastname, email, FROM_UNIXTIME(timecreated) AS created_at
        FROM {user}
        WHERE deleted = 0 AND suspended = 0 AND username != 'guest'
        ORDER BY timecreated ASC
        LIMIT $length OFFSET $start";

// Usar `get_recordset_sql()` para manejar grandes volúmenes de datos sin sobrecargar la memoria
$recordset = $DB->get_recordset_sql($sql);

$data = [];
foreach ($recordset as $user) {
    $data[] = [
        'ID' => $user->id,
        'Username' => $user->username,
        'FirstName' => $user->firstname,
        'LastName' => $user->lastname,
        'Email' => $user->email,
        'CreatedAt' => $user->created_at
    ];
}
$recordset->close(); // Cerrar el recordset para liberar memoria

// Obtener el total de usuarios (sin paginación)
$total_users = $DB->count_records_select('user', "deleted = 0 AND suspended = 0 AND username != 'guest'");

// 🚀 Respuesta JSON corregida para DataTables
$response = [
    "draw" => isset($_GET['draw']) ? intval($_GET['draw']) : 1,
    "recordsTotal" => intval($total_users),
    "recordsFiltered" => intval($total_users),
    "data" => $data // DataTables espera esta clave
];

// Convertir a JSON correctamente
echo json_encode($response, JSON_UNESCAPED_UNICODE);
exit;
