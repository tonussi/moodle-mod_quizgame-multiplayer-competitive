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
require_once (dirname(dirname(dirname(dirname(__FILE__)))) . '/config.php');

require_once (dirname(dirname(__FILE__)) . '/lib.php');

require_once (dirname(dirname(dirname(dirname(__FILE__)))) . '/mod/quizgame/locallib.php');

require_once ($CFG->libdir . '/pagelib.php');

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

if (! has_capability('mod/quizgame:watchgame', $context)) {
    redirect(new moodle_url('/course/view.php', array(
        'id' => $course->id
    )));
}

$PAGE->set_url('/mod/quizgame/view.php', array(
    'id' => $cm->id
));

$title = $course->shortname . ': ' . format_string($quizgame->name);
$PAGE->set_title($title);
$PAGE->set_heading($course->fullname);
$PAGE->set_context($context);

$PAGE->requires->css_theme(new moodle_url($CFG->wwwroot . '/mod/quizgame/components/font-awesome/css/font-awesome.min.css'));
$PAGE->requires->js(new moodle_url($CFG->wwwroot . '/mod/quizgame/components/angular/angular.min.js'));
$PAGE->requires->js(new moodle_url($CFG->wwwroot . '/mod/quizgame/components/angular-route/angular-route.min.js'));
$PAGE->requires->js(new moodle_url($CFG->wwwroot . '/mod/quizgame/ng/teacherforms.js'));

$OUTPUT = $PAGE->get_renderer('mod_quizgame');

echo $OUTPUT->header();

$categories = $DB->get_records('question_categories');
$tmpcats = array();
foreach ($categories as $c) {
    $tmpcats[] = array(
        'id' => $c->id,
        'name' => $c->name
    );
}

loadtemplate('question', array(
    'wwwroot' => $CFG->wwwroot . '/mod/quizgame/',
    'quizgame' => $cm->id,
    'categories' => $tmpcats,
    'questionbuilder' => get_string('questionbuilder', 'mod_quizgame'),
    'questionname' => get_string('questionname', 'mod_quizgame'),
    'qnametext' => get_string('name', 'mod_quizgame'),
    'addquestiontext' => get_string('addquestiontext', 'mod_quizgame'),
    'qtextdescr' => get_string('descr', 'mod_quizgame'),
    'qanswer' => get_string('qanswer', 'mod_quizgame'),
    'savequestion' => get_string('savequestion', 'mod_quizgame'),
    'options' => get_string('options', 'mod_quizgame'),
    'selectcategory' => get_string('selectcategory', 'mod_quizgame')
));

echo $OUTPUT->footer();

?>
