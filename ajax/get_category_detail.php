<?php
// Incluir las bibliotecas necesarias de Moodle
require_once(__DIR__ . '/../../../config.php');
require_once(__DIR__ . '/lib.php');
require "$CFG->libdir/tablelib.php";
use core_completion\progress;
require_once("{$CFG->libdir}/completionlib.php");

global $DB, $CFG;
require_login();

// Verifica si el usuario está logueado y tiene el rol de administrador del sitio, manager o es manager de curso
$context_system = context_system::instance();

if (!isloggedin() || isguestuser() || 
    (!has_capability('moodle/site:config', $context_system) && 
    !has_capability('moodle/role:manager', $context_system) && 
    !has_capability('moodle/course:manageactivities', context_course::instance(SITEID)))) {
    
    redirect('/', 'No tienes permiso para ver esta sección', 'error', 0);
}

// Asegurar que la respuesta sea JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Permitir acceso desde AJAX



// Obtener parámetros desde la URL
$id_cat = isset($_GET['idcategory']) ? intval($_GET['idcategory']) : 0;

if ($id_cat <= 0) {
    echo json_encode(["error" => "ID de categoría no válido"]);
    exit;
}

// Función que obtiene los cursos de la categoría
$result = curses_category($id_cat);

if (!$result) {
    echo json_encode(["error" => "No se encontraron cursos para esta categoría"]);
    exit;
}

// Convertir los datos en formato JSON
echo json_encode([
    "data" => array_values($result) // Convertimos el array asociativo en indexado
]);

exit;
