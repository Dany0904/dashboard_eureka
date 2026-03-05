<?php
require_once(__DIR__.'/../../../config.php');

// Asegurar que la respuesta sea JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Permitir acceso desde AJAX

global $DB;
require_login();

// Verifica si el usuario está logueado y tiene el rol de administrador del sitio, manager o es manager de curso
$context_system = context_system::instance();

if (!isloggedin() || isguestuser() || 
    (!has_capability('moodle/site:config', $context_system) && 
    !has_capability('moodle/role:manager', $context_system) && 
    !has_capability('moodle/course:manageactivities', context_course::instance(SITEID)))) {
    
    redirect('/', 'No tienes permiso para ver esta sección', 'error', 0);
}

// Consulta para obtener los cursos con número de usuarios inscritos
$sql = "SELECT 
            c.id, 
            c.fullname AS nombre, 
            cc.name AS categoria, 
            c.startdate AS fecha_inicio, 
            c.enddate AS fecha_fin,
            (SELECT COUNT(ue.id) 
             FROM {enrol} e 
             JOIN {user_enrolments} ue ON e.id = ue.enrolid 
             WHERE e.courseid = c.id) AS usuarios_inscritos
        FROM {course} c
        JOIN {course_categories} cc ON c.category = cc.id
        ORDER BY c.fullname ASC";

$cursos = $DB->get_records_sql($sql);

$data = [];
foreach ($cursos as $curso) {
    $data[] = [
        'id' => $curso->id,
        'nombre' => $curso->nombre,
        'usuarios_inscritos' => $curso->usuarios_inscritos,
        'categoria' => $curso->categoria,
        'fecha_inicio' => ($curso->fecha_inicio > 0) ? userdate($curso->fecha_inicio, '%Y-%m-%d') : 'No definida',
        'fecha_fin' => ($curso->fecha_fin > 0) ? userdate($curso->fecha_fin, '%Y-%m-%d') : 'No definida'
    ];
}

// Enviar la respuesta JSON
echo json_encode(['data' => $data]);
exit;
