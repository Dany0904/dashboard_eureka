<?php

require_once(__DIR__ . '/../../../config.php');

use core_completion\progress;
require_once $CFG->libdir . '/gradelib.php';
require_once $CFG->dirroot . '/grade/querylib.php';

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

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Verificar si se recibió un ID de usuario
if (!isset($_GET['userid']) || empty($_GET['userid'])) {
    echo json_encode(["error" => "No se proporcionó un ID de usuario"]);
    exit;
}

$userid = intval($_GET['userid']); // Sanitizar el input

// Obtener los cursos en los que el usuario está inscrito
$courses = enrol_get_users_courses($userid);
$cursos = array();

foreach ($courses as $course) {
    $curso = new stdClass();
    $curso->course_name = $course->fullname;
    $curso->category = $course->category;
    $curso->courseid = $course->id;
    $curso->userid = $userid;

    // Obtener la categoría del curso
    $category = $DB->get_record('course_categories', array('id' => $course->category));
    $curso->category_name = $category ? $category->name : 'Sin categoría';

    // Obtener la calificación del curso
    $grade = grade_get_course_grade($userid, $course->id);
    if ($grade !== false && !empty($grade->grade)) {
        $curso->grade = round($grade->grade, 2);
        $curso->aprobado = ($curso->grade >= 80) ? 'Aprobado' : 'Desaprobado';
    } else {
        $curso->grade = "Sin promedio";
        $curso->aprobado = 'Sin información';
    }

    // Calcular el progreso del usuario en el curso
    $courseObj = $DB->get_record("course", array('id' => $course->id));
    $percentageuser = progress::get_course_progress_percentage($courseObj, $userid);
    $curso->percentage = (!is_null($percentageuser)) ? floor($percentageuser) . "%" : "No hay avance";

    // Obtener los accesos del usuario
    $user = $DB->get_record('user', array('id' => $userid), 'firstaccess, lastaccess');
    $curso->primer_ingreso = (!empty($user->firstaccess)) ? date('Y-m-d H:i:s', $user->firstaccess) : 'Sin información';
    $curso->ultimo_ingreso = (!empty($user->lastaccess)) ? date('Y-m-d H:i:s', $user->lastaccess) : 'Sin información';

    $cursos[] = $curso;
}

// Devolver la respuesta en JSON
echo json_encode(["courses" => $cursos]);
exit;
