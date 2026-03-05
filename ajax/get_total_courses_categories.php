<?php
global $DB;

require_once(__DIR__ . '/../../../config.php');
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

if (!$DB) {
    echo json_encode(["error" => "Error: No se pudo conectar a la base de datos"]);
    exit;
}

// Total de categorías (rápido)
$totalCategories = (int)$DB->count_records('course_categories');

// Top 10 categorías por número de cursos
// Reglas recomendadas:
// - Excluir curso Front page (id = SITEID)
// - (Opcional) excluir cursos ocultos: AND co.visible = 1
$params = ['siteid' => SITEID];

$sql = "
    SELECT c.id,
           c.name,
           COUNT(co.id) AS total_courses
      FROM {course_categories} c
 LEFT JOIN {course} co
        ON co.category = c.id
       AND co.id <> :siteid
       AND co.timecreated > 0
     GROUP BY c.id, c.name
     ORDER BY total_courses DESC, c.id ASC
     LIMIT 10
";

$coursesByCategory = $DB->get_records_sql($sql, $params);

if (!$coursesByCategory) {
    echo json_encode(["error" => "No se encontraron datos"]);
    exit;
}

$data = [];
foreach ($coursesByCategory as $category) {
    $data[] = [
        'CategoryId' => (int)$category->id,
        'CategoryName' => (string)$category->name,
        'TotalCourses' => (int)$category->total_courses
    ];
}

echo json_encode([
    'total_categories' => $totalCategories,
    'data' => $data
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

exit;
