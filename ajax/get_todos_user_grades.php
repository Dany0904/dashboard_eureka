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

$userid    = isset($_GET['userid']) ? intval($_GET['userid']) : 0;
$courseid  = isset($_GET['courseid']) ? intval($_GET['courseid']) : 0;
$sectionid = isset($_GET['sectionid']) ? intval($_GET['sectionid']) : 0;
$datestart = isset($_GET['datestart']) ? $_GET['datestart'] : null;
$dateend   = isset($_GET['dateend']) ? $_GET['dateend'] : null;

// Validar paginación
if ($length < 1 || $length > 1000) { 
    $length = 100;
}
if ($start < 0) {
    $start = 0;
}
// ------------------------
// FILTROS
// ------------------------
$where = "u.deleted = 0 AND u.suspended = 0 AND u.username != 'guest'";
$params = [];
$joins = "";

// Usuario
if ($userid > 0) {
    $where .= " AND u.id = :userid";
    $params['userid'] = $userid;
}

// Fechas
if (!empty($datestart)) {
    $where .= " AND u.timecreated >= :datestart";
    $params['datestart'] = strtotime($datestart . " 00:00:00");
}

if (!empty($dateend)) {
    $where .= " AND u.timecreated <= :dateend";
    $params['dateend'] = strtotime($dateend . " 23:59:59");
}

// Curso
if ($courseid > 0) {
    $joins .= "
        JOIN {user_enrolments} ue ON ue.userid = u.id
        JOIN {enrol} e ON e.id = ue.enrolid
        JOIN {course} c ON c.id = e.courseid
    ";

    $where .= " AND c.id = :courseid";
    $params['courseid'] = $courseid;
}

// Sección
if ($sectionid > 0) {
    $joins .= "
        JOIN {course_modules} cm ON cm.course = c.id
        JOIN {course_sections} cs ON cs.id = cm.section
    ";

    $where .= " AND cs.id = :sectionid";
    $params['sectionid'] = $sectionid;
}

// ------------------------
// QUERY
// ------------------------
$sql = "SELECT DISTINCT u.id, u.username, u.firstname, u.lastname, u.email,
        FROM_UNIXTIME(u.timecreated) AS created_at
        FROM {user} u
        $joins
        WHERE $where
        ORDER BY u.timecreated ASC
        LIMIT $length OFFSET $start";

// Ejecutar
$recordset = $DB->get_recordset_sql($sql, $params);

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
$count_sql = "SELECT COUNT(DISTINCT u.id)
              FROM {user} u
              $joins
              WHERE $where";

$total_users = $DB->count_records_sql($count_sql, $params);

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
