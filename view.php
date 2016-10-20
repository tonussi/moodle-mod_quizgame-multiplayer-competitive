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
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.

/**
 * Prints a particular instance of quizgame
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package mod_quizgame
 * @copyright 2016 Lucas Tonussi <lptonussi@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once (dirname(dirname(dirname(__FILE__))) . '/config.php');

require_once (dirname(__FILE__) . '/lib.php');

require_once (dirname(dirname(dirname(__FILE__))) . '/mod/quizgame/locallib.php');

require_once ($CFG->libdir . '/pagelib.php');

require_once ($CFG->libdir . '/gradelib.php');

$id = optional_param('id', 0, PARAM_INT);

$n = optional_param('n', 0, PARAM_INT);

$lg = optional_param('lg', 1, PARAM_INT);

if ($id) {
    $cm = get_coursemodule_from_id('quizgame', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array(
        'id' => $cm->course
    ), '*', MUST_EXIST);
    $quizgame = $DB->get_record('quizgame', array(
        'id' => $cm->instance
    ), '*', MUST_EXIST);
} else {
    if ($n) {
        $quizgame = $DB->get_record('quizgame', array(
            'id' => $n
        ), '*', MUST_EXIST);
        $course = $DB->get_record('course', array(
            'id' => $quizgame->course
        ), '*', MUST_EXIST);
        $cm = get_coursemodule_from_instance('quizgame', $quizgame->id, $course->id, false, MUST_EXIST);
    } else {
        error('You must specify a course_module ID or an instance ID');
    }
}

require_login($course, true, $cm);
$context = context_module::instance($cm->id);

// Initialize $PAGE, compute blocks.
$PAGE->set_url('/mod/quizgame/view.php', array(
    'id' => $cm->id
));

$title = $course->shortname . ': ' . format_string($quizgame->name);
$PAGE->set_title($title);
$PAGE->set_heading($course->fullname);
$PAGE->set_context($context);

echo $OUTPUT->header();

if (has_capability('mod/quizgame:watchgame', $context)) {
    echo $OUTPUT->heading(get_string('teacherpage', 'mod_quizgame'));
    $url = new moodle_url('/mod/quizgame/views/teacher.php', array(
        'id' => $cm->id,
        'courseid' => $course->id,
        'qgid' => $quizgame->id,
        'form' => 'teacher'
    ));
    echo html_writer::link($url, get_string('teacherpage', 'mod_quizgame'), array(
        'class' => 'btn btn-default'
    ));
}

if (has_capability('mod/quizgame:playgame', $context)) {
    echo $OUTPUT->heading(get_string('studentpage', 'mod_quizgame'));
    $url = new moodle_url('/mod/quizgame/views/student.php', array(
        'id' => $cm->id,
        'courseid' => $course->id,
        'qgid' => $quizgame->id,
        'form' => 'student'
    ));
    echo html_writer::link($url, get_string('studentpage', 'mod_quizgame'), array(
        'class' => 'btn btn-default'
    ));
}

// Work out the final grade, checking whether it was overridden in the gradebook.
if (!has_capability('mod/quizgame:playgame', $context)) {
    $mygrade = quizgame_get_best_grade($quizgame, $USER->id);
} else {
    $mygrade = null;
}

$mygradeoverridden = false;
$gradebookfeedback = '';

$grading_info = grade_get_grades($course->id, 'mod', 'quizgame', $quizgame->id, $USER->id);

if (!empty($grading_info->items)) {

    $item = $grading_info->items[0];

    if (isset($item->grades[$USER->id])) {

        $grade = $item->grades[$USER->id];

        if ($grade->overridden) {
            $mygrade = $grade->grade + 0; // Convert to number.
            $mygradeoverridden = true;
        }

        if (!empty($grade->str_feedback)) {
            $gradebookfeedback = $grade->str_feedback;
        }

    }
}

echo $OUTPUT->footer();

?>
