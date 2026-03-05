<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Plugin strings are defined here.
 *
 * @package     local_sub_dashboard
 * @category    string
 * @copyright   2021 Subitus <contacto@subitus.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

use core_completion\progress;
require_once $CFG->libdir . '/gradelib.php';
require_once $CFG->dirroot . '/grade/querylib.php';

require_login();

// Verifica si el usuario está logueado y tiene el rol de administrador del sitio, manager o es manager de curso
$context_system = context_system::instance();

if (!isloggedin() || isguestuser() || 
    (!has_capability('moodle/site:config', $context_system) && 
    !has_capability('moodle/role:manager', $context_system) && 
    !has_capability('moodle/course:manageactivities', context_course::instance(SITEID)))) {
    
    redirect('/', 'No tienes permiso para ver esta sección', 'error', 0);
}

function local_sub_dashboard_average_course($gradesuser) {
    $countusers = 0;
    $grades = 0;

    foreach ($gradesuser as $user) {
        if (!is_null($user->finalgrade) && $user->grademax > 0) {
            $gradef = intval($user->finalgrade / $user->grademax * 10);
            $grades += $gradef;
            $countusers++; // Solo contar usuarios con calificación válida
        }
    }

    // Evitar división por cero
    return ($countusers > 0) ? number_format($grades / $countusers, 1) : 0;
}


// Función para obtener todos los usuarios
function obtener_todos_los_usuarios() {
    global $DB;
    return $DB->get_records('user');
}

function grades_user($userid, $courseid) {
    global $DB;

    $sql = "SELECT 
                gi.itemname AS activity_name,
                ROUND(g.finalgrade, 2) AS final_grade,
                FROM_UNIXTIME(g.timemodified) AS date_graded
            FROM 
                {user} u
            JOIN 
                {grade_grades} g ON g.userid = u.id
            JOIN 
                {grade_items} gi ON gi.id = g.itemid
            JOIN 
                {course} c ON gi.courseid = c.id
            WHERE 
                u.id = :userid
                AND c.id = :courseid and   gi.itemname!=''
            ORDER BY 
                gi.itemname";

    $params = array('userid' => $userid, 'courseid' => $courseid);
    return $DB->get_records_sql($sql, $params);
}
function obtener_cursos_del_usuario($userid) {
    global $DB;

    $courses = enrol_get_users_courses($userid);

    $cursos = array();

    foreach ($courses as $course) {
        $curso = new stdClass();
        $curso->course_name = $course->fullname;
        $curso->category = $course->category;
        $curso->courseid = $course->id;
        $curso->userid = $userid;

        // Obtener el nombre de la categoría y la subcategoría del curso
        $category = $DB->get_record('course_categories', array('id' => $course->category));
        if ($category) {
            $curso->category_name = $category->name;
        } else {
            $curso->category_name = 'Sin categoría';
            $curso->subcategory_name = 'Sin subcategoría';
        }

        // Obtener las calificaciones del curso
        $grade = grade_get_course_grade($userid, $course->id);

        if ($grade !== false && !empty($grade->grade)) {
            $curso->grade = substr($grade->grade, 0, -6);
            $curso->aprobado = ($curso->grade >= 80) ? 'Aprobado' : 'Desaprobado';
        } else {
            $curso->grade = "Sin promedio";
            $curso->aprobado = 'Sin información';
        }

        // Calcular el progreso del usuario en el curso
        $courseObj = $DB->get_record("course", array('id' => $course->id));
        $percentageuser = progress::get_course_progress_percentage($courseObj, $userid);
        if (!is_null($percentageuser)) {
            $curso->percentage = floor($percentageuser) . "%";
        } else {
            $curso->percentage = "No hay avance";
        }

        // Obtener el primer y último ingreso a Moodle del usuario
        $user = $DB->get_record('user', array('id' => $userid), 'firstaccess, lastaccess');
        $curso->primer_ingreso = (!empty($user->firstaccess)) ? date('Y-m-d H:i:s', $user->firstaccess) : 'Sin información';
        $curso->ultimo_ingreso = (!empty($user->lastaccess)) ? date('Y-m-d H:i:s', $user->lastaccess) : 'Sin información';

       

        $cursos[] = $curso;
    }

    return $cursos;
}
function obtener_cursos_del_usuario2($userid) {
    global $DB;

    $courses = enrol_get_users_courses($userid);

    $cursos = array();

    foreach ($courses as $course) {
        $curso = new stdClass();
        $curso->course_name = $course->fullname;
        $curso->category = $course->category;
        $curso->courseid = $course->id;
        $curso->userid = $userid;

        // Obtener el nombre de la categoría y la subcategoría del curso
        $category = $DB->get_record('course_categories', array('id' => $course->category));
        if ($category) {
            $curso->category_name = $category->name;
        } else {
            $curso->category_name = 'Sin categoría';
            $curso->subcategory_name = 'Sin subcategoría';
        }

        // Obtener las calificaciones del curso
        $grade = grade_get_course_grade($userid, $course->id);

        if ($grade !== false && !empty($grade->grade)) {
            $curso->grade = substr($grade->grade, 0, -6);
            $curso->aprobado = ($curso->grade >= 80) ? 'Aprobado' : 'Desaprobado';
        } else {
            $curso->grade = "Sin promedio";
            $curso->aprobado = 'Sin información';
        }

        // Obtener las actividades del curso con sus calificaciones
        $activities = $DB->get_records_sql("SELECT
                gi.id AS item_id,
                gi.itemname AS activity_name,
                ROUND(gg.finalgrade, 2) AS final_grade,
                FROM_UNIXTIME(gg.timemodified) AS date_graded
            FROM
                {grade_items} gi
            LEFT JOIN
                {grade_grades} gg ON gg.itemid = gi.id AND gg.userid = :userid
            WHERE
                gi.courseid = :courseid and   gi.itemname!=''
            ORDER BY
                gi.itemname
        ", array('userid' => $userid, 'courseid' => $course->id));

        // Agregar las actividades al curso
        $curso->activities = array();
        foreach ($activities as $activity) {
            $act = new stdClass();
            $act->activity_name = $activity->activity_name;
            if($activity->final_grade!=''){
                $act->final_grade = $activity->final_grade;
            }else{
                $act->final_grade = 'No hay calificación';
            }
            if($activity->date_graded!=''){
                $act->date_graded = $activity->date_graded;
            }else{
                $act->date_graded = 'Sin información disponible';
            }
            $curso->activities[] = $act;
        }

        $cursos[] = $curso;
    }

    return $cursos;
}
function local_sub_dashboard_get_grades_users_courses($idcourse, $default = -1,  $scale = 10){
    global $DB;
    $grade = $default;
    $query = "SELECT grades.userid as userid, grades.finalgrade as finalgrade, items.grademax as grademax FROM {grade_grades} grades JOIN {grade_items} items
        ON grades.itemid = items.id where items.itemtype = 'course' AND items.courseid = {$idcourse}";
    $gradescourse = $DB->get_records_sql($query);
    // if($data = $DB->get_records_sql($query)){
    //     if($data->grademax > 0){
    //         $grade = $data->finalgrade / $data->grademax * $scale;
    //     }
    // }
    // return $grade;
    return $gradescourse;
}

function curses_category($categoryId) {
    global $DB;

    // Consulta para obtener los cursos dentro de una categoría específica
    $query = "SELECT c.id, c.fullname, c.shortname, c.summary, c.startdate, c.enddate
              FROM {course} c
              JOIN {course_categories} cc ON c.category = cc.id
              WHERE cc.id = :categoryid";

    // Ejecutar la consulta
    $results = $DB->get_records_sql($query, array('categoryid' => $categoryId));

    // Convertir los timestamps en fechas legibles
    $courses = [];
    foreach ($results as $course) {
        $courses[] = [
            'id' => $course->id,
            'fullname' => $course->fullname,
            'shortname' => $course->shortname,
            'summary' => $course->summary,
            'startdate' => ($course->startdate > 0) ? date('Y-m-d', $course->startdate) : 'No definido',
            'enddate' => ($course->enddate > 0) ? date('Y-m-d', $course->enddate) : 'No definido'
        ];
    }

    return $courses;
}

function local_dashboard_extend_settings_navigation(settings_navigation $settingsnav, context $context) {
    global $PAGE;

    // Solo mostrar si tiene permiso
    if (!has_capability('local/dashboard:access', context_system::instance())) {
        return;
    }

    // Buscar el nodo "Administración del sitio"
    $rootnode = $settingsnav->find('root', navigation_node::TYPE_SITE_ADMIN);

    if (!$rootnode) {
        return;
    }

    // Recorrer sus hijos para encontrar el nodo de "Reportes"
    foreach ($rootnode->children as $child) {
        if ($child->key === 'reports') {
            $url = new moodle_url('/local/dashboard/index.php');
            $child->add(
                get_string('pluginname', 'local_dashboard'),
                $url,
                navigation_node::TYPE_SETTING,
                null,
                'local_dashboard'
            );
            break;
        }
    }
}
