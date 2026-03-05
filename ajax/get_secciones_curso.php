<?php
define('AJAX_SCRIPT', true);
require_once(__DIR__ . '/../../../config.php');

global $DB;
require_login();

header('Content-Type: application/json');

$courseid = optional_param('courseid', 0, PARAM_INT);
$actividad = optional_param('actividad', 'hvp', PARAM_ALPHA);

// Solo permitir módulos válidos
$modulospermitidos = ['hvp', 'quiz'];
if (!in_array($actividad, $modulospermitidos)) {
    $actividad = 'hvp';
}

if ($courseid <= 0) {
    echo json_encode(['data' => []]);
    exit;
}

/* ---------------------------
   Público objetivo del curso
---------------------------- */
$sql_objetivo = "
    SELECT COUNT(DISTINCT ue.userid)
    FROM {user_enrolments} ue
    JOIN {enrol} e ON e.id = ue.enrolid
    WHERE e.courseid = :courseid
      AND ue.status = 0
      AND e.status = 0
";

$publicoobjetivo = $DB->count_records_sql($sql_objetivo, ['courseid' => $courseid]);

/* ---------------------------
   Secciones
---------------------------- */
$sql = "
    SELECT
        cs.id,
        cs.section,
        cs.name AS sectionname,
        MAX(cm.added) AS lastmodified
    FROM {course_sections} cs
    LEFT JOIN {course_modules} cm
        ON cm.section = cs.id
    WHERE cs.course = :courseid
      AND cs.section <> 0
    GROUP BY cs.id, cs.section, cs.name
    ORDER BY cs.section
";

$sections = $DB->get_records_sql($sql, ['courseid' => $courseid]);

$data = [];

foreach ($sections as $s) {

    /* Público impactado */
    $sql_impactado = "
        SELECT COUNT(1)
        FROM (
            SELECT cmc.userid
            FROM {course_modules} cm
            JOIN {modules} m
                ON m.id = cm.module
                AND m.name = :actividad
            JOIN {course_modules_completion} cmc
                ON cmc.coursemoduleid = cm.id
                AND cmc.completionstate = 1
            JOIN {user_enrolments} ue
                ON ue.userid = cmc.userid
            JOIN {enrol} e
                ON e.id = ue.enrolid
            WHERE cm.section = :sectionid
            AND e.courseid = :courseid
            AND ue.status = 0
            AND e.status = 0
            AND cm.deletioninprogress = 0
            GROUP BY cmc.userid
            HAVING COUNT(cmc.id) = (
                SELECT COUNT(1)
                FROM {course_modules} cm2
                JOIN {modules} m2
                    ON m2.id = cm2.module
                    AND m2.name = :actividad2
                WHERE cm2.section = :sectionid2
                AND cm2.deletioninprogress = 0
            )
        ) t
    ";

    $publicoimpactado = $DB->count_records_sql($sql_impactado, [
        'sectionid'   => $s->id,
        'sectionid2'  => $s->id,
        'courseid'    => $courseid,
        'actividad'   => $actividad,
        'actividad2'  => $actividad
    ]);

    /* Nuevos cálculos */
    $horasprogramadas = $publicoobjetivo * 2;
    $horasrealizadas = $publicoimpactado * 2;

    $porcentaje = ($horasprogramadas > 0)
        ? round(($horasrealizadas / $horasprogramadas) * 100)
        : 0;

    $data[] = [
        'section'            => $s->sectionname ?: 'Sin nombre',
        'lastmodified'       => $s->lastmodified
            ? userdate($s->lastmodified, '%d/%m/%Y')
            : '—',
        'objetivo'           => $publicoobjetivo,
        'impactado'          => $publicoimpactado,
        'falta'              => max(0, $publicoobjetivo - $publicoimpactado),
        'horas_programadas'  => $horasprogramadas,
        'horas_realizadas'   => $horasrealizadas,
        'porcentaje'         => $porcentaje . '%'
    ];
}

echo json_encode(['data' => $data]);
exit;
