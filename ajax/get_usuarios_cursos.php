<?php
define('AJAX_SCRIPT', true);

require_once(__DIR__ . '/../../../config.php');
require_once("{$CFG->libdir}/completionlib.php");

global $DB;

require_login();

$context = context_system::instance();
if (
    !has_capability('moodle/site:config', $context) &&
    !has_capability('moodle/role:manager', $context)
) {
    throw new required_capability_exception($context, 'moodle/site:config', 'nopermissions', '');
}

header('Content-Type: application/json');

$courseid  = optional_param('courseid', 0, PARAM_INT);
$userid    = optional_param('userid', 0, PARAM_INT);
$sectionid = optional_param('sectionid', 0, PARAM_INT);
$datestart = optional_param('datestart', '', PARAM_TEXT);
$dateend   = optional_param('dateend', '', PARAM_TEXT);

$dateFilter = '';

if ($datestart) {
    $starttimestamp = strtotime($datestart . ' 00:00:00');
}

if ($dateend) {
    $endtimestamp = strtotime($dateend . ' 23:59:59');
}

if ($courseid <= 0) {
    echo json_encode([
        "data" => [],
        "kpis" => ["totalh5p"=>0,"completed"=>0,"pending"=>0,"percentage"=>0,"avggrade"=>0],
        "chart" => ["labels"=>[],"compliance"=>[],"grades"=>[]],
        "sectionchart" => ["labels"=>[],"compliance"=>[]]
    ]);
    exit;
}

/* ======================================================
   OBTENER IDS H5P
====================================================== */

$h5pmodules = $DB->get_fieldset_select(
    'modules',
    'id',
    "name IN ('hvp','h5pactivity')"
);

if (empty($h5pmodules)) {
    $h5pmodules = [0];
}

list($inSqlTotal, $inParamsTotal) =
    $DB->get_in_or_equal($h5pmodules, SQL_PARAMS_NAMED, 'modt');

list($inSqlCompleted, $inParamsCompletedIn) =
    $DB->get_in_or_equal($h5pmodules, SQL_PARAMS_NAMED, 'modc');

list($inSqlGrades, $inParamsGradesIn) =
    $DB->get_in_or_equal($h5pmodules, SQL_PARAMS_NAMED, 'modg');

/* ======================================================
   TOTAL H5P
====================================================== */

$paramsTotal = ['courseid_total'=>$courseid] + $inParamsTotal;

$sqlTotalH5P = "
SELECT COUNT(1)
FROM {course_modules}
WHERE course = :courseid_total
AND deletioninprogress = 0
AND module $inSqlTotal
";

if ($sectionid > 0) {
    $sqlTotalH5P .= " AND section = :sectionid_total";
    $paramsTotal['sectionid_total'] = $sectionid;
}

$totalh5p = (int)$DB->get_field_sql($sqlTotalH5P, $paramsTotal);

/* ======================================================
   COMPLETADOS POR USUARIO
====================================================== */

$paramsCompleted = ['courseid_completed'=>$courseid] + $inParamsCompletedIn;

$sqlCompleted = "
SELECT
    cmc.userid,
    COUNT(cmc.id) AS completed_h5p
FROM {course_modules_completion} cmc
JOIN {course_modules} cm
    ON cm.id = cmc.coursemoduleid
WHERE cm.course = :courseid_completed
AND cm.module $inSqlCompleted
AND cmc.completionstate = 1
";

if ($sectionid > 0) {
    $sqlCompleted .= " AND cm.section = :sectionid_completed";
    $paramsCompleted['sectionid_completed'] = $sectionid;
}

if ($datestart) {
    $sqlCompleted .= " AND cmc.timemodified >= :datestart";
    $paramsCompleted['datestart'] = $starttimestamp;
}

if ($dateend) {
    $sqlCompleted .= " AND cmc.timemodified <= :dateend";
    $paramsCompleted['dateend'] = $endtimestamp;
}

$sqlCompleted .= " GROUP BY cmc.userid";

/* ======================================================
   NOTAS PROMEDIO
====================================================== */

$paramsGrades = ['courseid_grades'=>$courseid] + $inParamsGradesIn;

$sqlGrades = "
SELECT
    gg.userid,
    AVG((gg.finalgrade / gi.grademax) * 100) AS avg_grade
FROM {grade_grades} gg
JOIN {grade_items} gi ON gi.id = gg.itemid
JOIN {course_modules} cm ON cm.instance = gi.iteminstance
JOIN {course_modules_completion} cmc 
    ON cmc.coursemoduleid = cm.id
    AND cmc.userid = gg.userid
WHERE gi.courseid = :courseid_grades
AND cm.module $inSqlGrades
AND gi.grademax > 0
AND cmc.completionstate = 1
";

if ($sectionid > 0) {
    $sqlGrades .= " AND cm.section = :sectionid_grades";
    $paramsGrades['sectionid_grades'] = $sectionid;
}

if ($datestart) {
    $sqlGrades .= " AND cmc.timemodified >= :datestart_grades";
    $paramsGrades['datestart_grades'] = $starttimestamp;
}

if ($dateend) {
    $sqlGrades .= " AND cmc.timemodified <= :dateend_grades";
    $paramsGrades['dateend_grades'] = $endtimestamp;
}

$sqlGrades .= " GROUP BY gg.userid";

/* ======================================================
   CONSULTA PRINCIPAL
====================================================== */

$paramsUsers = ['courseid_main'=>$courseid];

$userFilter = '';
if ($userid > 0) {
    $userFilter = " AND u.id = :userid_main";
    $paramsUsers['userid_main'] = $userid;
}

$sqlUsers = "
SELECT
    u.id,
    u.username,
    CONCAT(u.firstname,' ',u.lastname) AS fullname,
    u.city,
    ue.status AS enrolstatus,
    u.lastlogin,
    COALESCE(c.completed_h5p,0) AS completed_h5p,
    ROUND(g.avg_grade,2) AS avg_grade
FROM {user} u
JOIN {user_enrolments} ue
    ON ue.userid = u.id AND ue.status = 0
JOIN {enrol} e
    ON e.id = ue.enrolid AND e.courseid = :courseid_main
INNER JOIN ($sqlCompleted) c
    ON c.userid = u.id
LEFT JOIN ($sqlGrades) g
    ON g.userid = u.id
WHERE u.deleted = 0
$userFilter
ORDER BY u.lastname, u.firstname
";

$paramsUsers = array_merge(
    $paramsUsers,
    $paramsCompleted,
    $paramsGrades
);

$users = $DB->get_records_sql($sqlUsers, $paramsUsers);

/* ======================================================
   TOTAL USUARIOS
====================================================== */

$totalusers = (int)$DB->get_field_sql("
SELECT COUNT(1)
FROM {user_enrolments} ue
JOIN {enrol} e ON e.id = ue.enrolid
JOIN {user} u ON u.id = ue.userid
WHERE e.courseid = :courseid_count
AND ue.status = 0
AND u.deleted = 0
", ['courseid_count'=>$courseid]);

/* ======================================================
   GRAFICA H5P/HVP POR ACTIVIDAD (COBERTURA REAL)
====================================================== */

$paramsActivity = ['courseid_activity' => $courseid] + $inParamsTotal;

$sqlActivityChart = "
SELECT
    cm.id AS cmid,
    COALESCE(h5.name, hvp.name, 'Actividad') AS name,
    COUNT(DISTINCT cmc.userid) AS completed_users
FROM {course_modules} cm

JOIN {modules} m
    ON m.id = cm.module

LEFT JOIN {course_modules_completion} cmc
    ON cmc.coursemoduleid = cm.id
    AND cmc.completionstate = 1

LEFT JOIN {h5pactivity} h5
    ON h5.id = cm.instance
    AND m.name = 'h5pactivity'

LEFT JOIN {hvp} hvp
    ON hvp.id = cm.instance
    AND m.name = 'hvp'

WHERE cm.course = :courseid_activity
AND cm.module $inSqlTotal
AND cm.deletioninprogress = 0
";

if ($sectionid > 0) {
    $sqlActivityChart .= " AND cm.section = :sectionid_activity";
    $paramsActivity['sectionid_activity'] = $sectionid;
}

if ($datestart) {
    $sqlActivityChart .= " AND cmc.timemodified >= :datestart_activity";
    $paramsActivity['datestart_activity'] = $starttimestamp;
}

if ($dateend) {
    $sqlActivityChart .= " AND cmc.timemodified <= :dateend_activity";
    $paramsActivity['dateend_activity'] = $endtimestamp;
}

$sqlActivityChart .= "
GROUP BY cm.id, h5.name, hvp.name
ORDER BY name
";

$activityRecords = $DB->get_records_sql($sqlActivityChart, $paramsActivity);

$sectionLabels = [];
$sectionCompliance = [];

foreach ($activityRecords as $r) {

    $coverage = ($totalusers > 0)
        ? round(($r->completed_users / $totalusers) * 100)
        : 0;

    $sectionLabels[] = $r->name;
    $sectionCompliance[] = $coverage;
}

/* ======================================================
   PROCESAMIENTO FINAL
====================================================== */

$data = [];
$sumCompleted = 0;
$sumGrades = 0;
$gradeCount = 0;

$chartLabels = [];
$chartCompliance = [];
$chartGrades = [];

foreach ($users as $u) {

    $completed = (int)$u->completed_h5p;
    $percent = ($totalh5p > 0) ? round(($completed / $totalh5p) * 100) : 0;
    $pending = max(0, $totalh5p - $completed);

    $sumCompleted += $completed;

    if ($u->avg_grade !== null) {
        $sumGrades += $u->avg_grade;
        $gradeCount++;
    }

    $data[] = [
        $u->username,
        $u->fullname,
        $u->city ?: '—',
        $u->enrolstatus == 0 ? 'Activo' : 'Suspendido',
        $u->lastlogin ? userdate($u->lastlogin) : 'Nunca',
        $totalh5p,
        $completed,
        $pending,
        $percent . '%',
        $u->avg_grade !== null ? number_format($u->avg_grade,2) : '—'
    ];

    $chartLabels[] = $u->fullname;
    $chartCompliance[] = $percent;
    $chartGrades[] = $u->avg_grade !== null ? (float)$u->avg_grade : null;
}

$avgCompleted = (count($users) > 0) ? round($sumCompleted / count($users)) : 0;

$avgPercent = ($totalh5p > 0 && count($users) > 0)
    ? round(($sumCompleted / ($totalh5p * count($users))) * 100)
    : 0;

$avgGradeGlobal = ($gradeCount > 0)
    ? round($sumGrades / $gradeCount, 2)
    : 0;

/* ======================================================
   RESPUESTA FINAL
====================================================== */

echo json_encode([
    "data" => $data,
    "kpis" => [
        "totalh5p"   => $totalh5p,
        "completed"  => $avgCompleted,
        "pending"    => max(0, $totalh5p - $avgCompleted),
        "percentage" => $avgPercent,
        "avggrade"   => $avgGradeGlobal
    ],
    "chart" => [
        "labels" => $chartLabels,
        "compliance" => $chartCompliance,
        "grades" => $chartGrades
    ],
    "sectionchart" => [
        "labels" => $sectionLabels,
        "compliance" => $sectionCompliance
    ]

]);

exit;
