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

$top_progress = isset($_GET['status']) ? (int)$_GET['status'] : 0;

if ($top_progress === 0) {

    // 1) Top 10 cursos por participantes (inscritos activos)
    // Excluye Front page (SITEID) y cursos fuera de categorías reales.
    $sql_topcourses = "
        SELECT
            c.id,
            c.fullname,
            COUNT(DISTINCT ue.userid) AS participants
        FROM {course} c
        JOIN {enrol} e ON e.courseid = c.id AND e.status = 0
        JOIN {user_enrolments} ue ON ue.enrolid = e.id AND ue.status = 0
        JOIN {user} u ON u.id = ue.userid AND u.deleted = 0 AND u.id <> 1
        WHERE c.category > 0
          AND c.id <> :siteid
        GROUP BY c.id, c.fullname
        ORDER BY participants DESC
        LIMIT 10
    ";

    $topcourses = $DB->get_records_sql($sql_topcourses, ['siteid' => SITEID]);

    if (!$topcourses) {
        echo json_encode([
            "data" => [],
            "status" => 200,
            "labels_course" => [],
            "data_complete" => [],
            "data_notcomplete" => []
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    $courseids = array_map(fn($c) => (int)$c->id, $topcourses);
    list($insql, $inparams) = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED, 'cid');
    $params = $inparams;

    // 2) Conteo de completados por curso (solo usuarios completados)
    // course_completions: 1 fila por (course, userid) cuando completion está habilitado.
    $sql_completed = "
        SELECT
            cc.course,
            COUNT(DISTINCT cc.userid) AS completed
        FROM {course_completions} cc
        JOIN {user} u ON u.id = cc.userid AND u.deleted = 0 AND u.id <> 1
        WHERE cc.course $insql
          AND cc.timecompleted IS NOT NULL
        GROUP BY cc.course
    ";

    $completedmap = $DB->get_records_sql($sql_completed, $params);

    // 3) Armar salida exactamente como tu JSON espera
    $array_names_course = [];
    $array_complete = [];
    $array_notcomplete = [];

    $data = [];
    foreach ($topcourses as $course) {
        $cid = (int)$course->id;

        $completed = isset($completedmap[$cid]) ? (int)$completedmap[$cid]->completed : 0;
        $participants = (int)$course->participants;
        $notcompleted = max(0, $participants - $completed);

        // Mantengo propiedades que tú estabas agregando al objeto curso
        $course->completed = $completed;
        $course->notcompleted = $notcompleted;

        $data[] = $course;

        $array_names_course[] = (string)$course->fullname;
        $array_complete[] = $completed;
        $array_notcomplete[] = $notcompleted;
    }

    echo json_encode([
        "data" => $data,
        "status" => 200,
        "labels_course" => $array_names_course,
        "data_complete" => $array_complete,
        "data_notcomplete" => $array_notcomplete
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    exit;
}

// Si existe más lógica para status != 0, pégamela y la optimizo igual.
echo json_encode(["error" => "Parámetro status no soportado en este fragmento", "status" => 400]);
exit;
