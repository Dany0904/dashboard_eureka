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

// Año actual en rango epoch [inicio, fin)
$year  = (int)date('Y');
$start = strtotime($year . '-01-01 00:00:00');
$end   = strtotime(($year + 1) . '-01-01 00:00:00');

$params = [
    'start'  => $start,
    'end'    => $end,
    'siteid' => SITEID,
];

// NOTA:
// - gg.finalgrade: nota final (puede ser NULL)
// - gg.timemodified suele ser más confiable que timecreated en varias instalaciones
//   (en algunas versiones timecreated puede estar 0). Uso timemodified por rendimiento/realidad.
// - Limito a ítems que realmente pertenecen al curso y a notas reales.
$sql = "
    SELECT c.id,
           c.fullname,
           AVG(gg.finalgrade) AS average
      FROM {grade_grades} gg
      JOIN {grade_items} gi ON gi.id = gg.itemid
      JOIN {course} c       ON c.id = gi.courseid
     WHERE c.id <> :siteid
       AND c.category > 0
       AND gg.finalgrade IS NOT NULL
       AND gg.timemodified >= :start
       AND gg.timemodified <  :end
  GROUP BY c.id, c.fullname
  ORDER BY average DESC
     LIMIT 10
";

$rows = $DB->get_records_sql($sql, $params);

// Si no hay datos, regresar vacío como tú lo haces
if (!$rows) {
    echo json_encode(["data" => [], "status" => 200], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

// Formateo + position
$data = [];
$pos = 1;
foreach ($rows as $r) {
    $data[] = [
        'id' => (int)$r->id,
        'fullname' => (string)$r->fullname,
        'average' => round((float)$r->average, 2),
        'position' => $pos++,
    ];
}

echo json_encode([
    "data" => $data,
    "status" => 200
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

exit;
