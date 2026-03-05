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

if ($courseid <= 0) {
    echo json_encode([
        "data"=>[],
        "kpis"=>["totalquiz"=>0,"completed"=>0,"pending"=>0,"percentage"=>0,"avggrade"=>0],
        "chart"=>["labels"=>[],"compliance"=>[],"grades"=>[]],
        "sectionchart"=>["labels"=>[],"compliance"=>[]]
    ]);
    exit;
}

/* ======================================================
   TOTAL QUIZ (SIN JOIN A modules)
====================================================== */

$paramsTotal = ['courseid_total'=>$courseid];

$sqlTotalQuiz = "
SELECT COUNT(1)
FROM {course_modules}
WHERE course = :courseid_total
AND deletioninprogress = 0
AND module = (
    SELECT id FROM {modules} WHERE name = 'quiz'
)
";

if ($sectionid > 0) {
    $sqlTotalQuiz .= " AND section = :sectionid_total";
    $paramsTotal['sectionid_total'] = $sectionid;
}

$totalquiz = (int)$DB->get_field_sql($sqlTotalQuiz, $paramsTotal);

/* ======================================================
   SUBQUERY COMPLETADOS (PRE AGRUPADA)
====================================================== */

$paramsCompleted = ['courseid_completed'=>$courseid];

$sqlCompleted = "
SELECT
    qa.userid,
    COUNT(DISTINCT qa.quiz) AS completed_quiz
FROM {quiz_attempts} qa
JOIN {quiz} q ON q.id = qa.quiz
JOIN {course_modules} cm ON cm.instance = q.id
WHERE qa.state = 'finished'
AND q.course = :courseid_completed
";

if ($sectionid > 0) {
    $sqlCompleted .= " AND cm.section = :sectionid_completed";
    $paramsCompleted['sectionid_completed'] = $sectionid;
}

$sqlCompleted .= " GROUP BY qa.userid";

/* ======================================================
   SUBQUERY NOTAS (PRE AGRUPADA)
====================================================== */

$paramsGrades = ['courseid_grades'=>$courseid];

$sqlGrades = "
SELECT
    gg.userid,
    AVG((gg.finalgrade / gi.grademax) * 100) AS avg_grade
FROM {grade_grades} gg
JOIN {grade_items} gi ON gi.id = gg.itemid
JOIN {quiz} q ON q.id = gi.iteminstance
JOIN {course_modules} cm ON cm.instance = q.id
WHERE gi.courseid = :courseid_grades
AND gi.itemmodule = 'quiz'
AND gi.grademax > 0
";

if ($sectionid > 0) {
    $sqlGrades .= " AND cm.section = :sectionid_grades";
    $paramsGrades['sectionid_grades'] = $sectionid;
}

$sqlGrades .= " GROUP BY gg.userid";

/* ======================================================
   4️⃣ CONSULTA PRINCIPAL LIMPIA
====================================================== */

$paramsUsers = ['courseid_users'=>$courseid];

$userFilter = '';
if ($userid > 0) {
    $userFilter = " AND u.id = :userid_filter";
    $paramsUsers['userid_filter'] = $userid;
}

$sqlUsers = "
SELECT
    u.id,
    u.username,
    CONCAT(u.firstname,' ',u.lastname) AS fullname,
    u.city,
    ue.status AS enrolstatus,
    u.lastlogin,
    COALESCE(c.completed_quiz,0) AS completed_quiz,
    ROUND(g.avg_grade,2) AS avg_grade
FROM {user} u
JOIN {user_enrolments} ue
    ON ue.userid = u.id AND ue.status = 0
JOIN {enrol} e
    ON e.id = ue.enrolid AND e.courseid = :courseid_users
LEFT JOIN ($sqlCompleted) c
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
   TOTAL USUARIOS ACTIVOS
====================================================== */

$totalusers = (int)$DB->get_field_sql("
SELECT COUNT(1)
FROM {user_enrolments} ue
JOIN {enrol} e ON e.id = ue.enrolid
JOIN {user} u ON u.id = ue.userid
WHERE e.courseid = :courseid_totalusers
AND ue.status = 0
AND u.deleted = 0
", ['courseid_totalusers'=>$courseid]);

/* ======================================================
   TABLA + KPI
====================================================== */

$data = [];
$sumCompleted = 0;
$sumGrades = 0;
$gradeCount = 0;

$chartLabels = [];
$chartCompliance = [];
$chartGrades = [];

foreach ($users as $u) {

    $completed = min((int)$u->completed_quiz, $totalquiz);

    $percent = ($totalquiz > 0)
        ? round(($completed / $totalquiz) * 100)
        : 0;

    $pending = max(0, $totalquiz - $completed);

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
        $totalquiz,
        $completed,
        $pending,
        $percent . '%',
        $u->avg_grade !== null ? number_format($u->avg_grade,2) : '—'
    ];

    $chartLabels[] = $u->fullname;
    $chartCompliance[] = $percent;
    $chartGrades[] = $u->avg_grade !== null ? (float)$u->avg_grade : null;
}

/* ======================================================
   KPI GLOBALES
====================================================== */

$usercount = count($users);

$avgCompleted = ($usercount > 0)
    ? round($sumCompleted / $usercount)
    : 0;

$avgPercent = ($totalquiz > 0 && $usercount > 0)
    ? round(($sumCompleted / ($totalquiz * $usercount)) * 100)
    : 0;

$avgGradeGlobal = ($gradeCount > 0)
    ? round($sumGrades / $gradeCount, 2)
    : 0;

/* ======================================================
   GRAFICA POR QUIZ (OPTIMIZADA)
====================================================== */

$paramsChart = ['courseid_chart'=>$courseid];

$sqlQuizChart = "
SELECT
    q.name,
    COUNT(DISTINCT qa.userid) AS completed_users
FROM {quiz} q
JOIN {course_modules} cm ON cm.instance = q.id
LEFT JOIN {quiz_attempts} qa
    ON qa.quiz = q.id
    AND qa.state = 'finished'
WHERE q.course = :courseid_chart
";

if ($sectionid > 0) {
    $sqlQuizChart .= " AND cm.section = :sectionid_chart";
    $paramsChart['sectionid_chart'] = $sectionid;
}

$sqlQuizChart .= " GROUP BY q.id, q.name ORDER BY q.name";

$records = $DB->get_records_sql($sqlQuizChart, $paramsChart);

$sectionLabels = [];
$sectionCompliance = [];

foreach ($records as $r) {

    $percent = ($totalusers > 0)
        ? round(($r->completed_users / $totalusers) * 100)
        : 0;

    $sectionLabels[] = $r->name ?: 'Quiz';
    $sectionCompliance[] = $percent;
}

/* ======================================================
   RESPUESTA FINAL (FORMATO EXACTO)
====================================================== */

echo json_encode([
    "data"=>$data,
    "kpis"=>[
        "totalquiz"=>$totalquiz,
        "completed"=>$avgCompleted,
        "pending"=>max(0,$totalquiz-$avgCompleted),
        "percentage"=>$avgPercent,
        "avggrade"=>$avgGradeGlobal
    ],
    "chart"=>[
        "labels"=>$chartLabels,
        "compliance"=>$chartCompliance,
        "grades"=>$chartGrades
    ],
    "sectionchart"=>[
        "labels"=>$sectionLabels,
        "compliance"=>$sectionCompliance
    ]
]);

exit;
