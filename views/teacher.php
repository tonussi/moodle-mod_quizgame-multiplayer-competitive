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

$PAGE->set_url('/mod/quizgame/views/teacher.php', array(
    'id' => $cm->id,
    'qgid' => $quizgame->id,
    'courseid' => $course->id,
    'form' => 'teacher'
));

$title = $course->shortname . ': ' . format_string($quizgame->name);
$PAGE->set_title($title);
$PAGE->set_heading($course->fullname);
$PAGE->set_context($context);

$PAGE->requires->css_theme(new moodle_url($CFG->wwwroot . '/mod/quizgame/components/font-awesome/css/font-awesome.min.css'));
$PAGE->requires->css_theme(new moodle_url($CFG->wwwroot . '/mod/quizgame/css/dialogs.css'));
$PAGE->requires->js(new moodle_url($CFG->wwwroot . '/mod/quizgame/components/angular/angular.min.js'));
$PAGE->requires->js(new moodle_url($CFG->wwwroot . '/mod/quizgame/components/angular-route/angular-route.min.js'));
$PAGE->requires->js(new moodle_url($CFG->wwwroot . '/mod/quizgame/ng/teacherforms.js'));

echo $OUTPUT->header();

if ($quizgame->intro) {
    echo $OUTPUT->box(format_module_intro('quizgame', $quizgame, $cm->id), 'generalbox mod_introbox', 'quizgameintro');
}

echo $OUTPUT->heading(get_string('editquestions', 'mod_quizgame'));
$url = new moodle_url('/question/edit.php?courseid=2', array(
    'courseid' => $course->id
));

echo html_writer::link($url, get_string('editquestions', 'mod_quizgame'), array(
    'class' => 'btn btn-default'
));

echo $OUTPUT->heading(get_string('creatematch', 'mod_quizgame'));
$url = new moodle_url($CFG->wwwroot . '/mod/quizgame/forms/key.php', array(
    'qgid' => $quizgame->id,
    'id' => $cm->id,
    'courseid' => $course->id,
    'form' => 'key'
));
echo html_writer::link($url, get_string('creatematch', 'mod_quizgame'), array(
    'class' => 'btn btn-default'
));

echo $OUTPUT->heading(get_string('createconfig', 'mod_quizgame'));
$url = new moodle_url($CFG->wwwroot . '/mod/quizgame/forms/config.php', array(
    'qgid' => $quizgame->id,
    'id' => $cm->id,
    'courseid' => $course->id,
    'form' => 'config'
));
echo html_writer::link($url, get_string('createconfig', 'mod_quizgame'), array(
    'class' => 'btn btn-default'
));

$categories = $DB->get_records('question_categories');
$tmpcats = array();
foreach ($categories as $c) {
    $tmpcats[] = array(
        'id' => $c->id,
        'name' => $c->name
    );
}

loadtemplate('categories', array(
    'categories' => $tmpcats,
    'createpackage' => get_string('createpackage', 'mod_quizgame'),
    'wwwroot' => $CFG->wwwroot . '/mod/quizgame/forms/package.php',
    'qgid' => $quizgame->id,
    'id' => $cm->id,
    'courseid' => $course->id,
    'selectcategory' => get_string('selectcategory', 'mod_quizgame'),
    'form' => 'package',
    'ngcatid' => '{{catid}}'
));

/*$url = new moodle_url($CFG->wwwroot . '/mod/quizgame/forms/package.php', array(
    'qgid' => $quizgame->id,
    'id' => $cm->id,
    'courseid' => $course->id,
    'form' => 'package'
));
echo html_writer::link($url, get_string('createpackage', 'mod_quizgame'), array(
    'class' => 'btn btn-default'
));*/

echo $OUTPUT->heading(get_string('createquestion', 'mod_quizgame'));
$url = new moodle_url($CFG->wwwroot . '/mod/quizgame/forms/question.php', array(
    'qgid' => $quizgame->id,
    'id' => $cm->id,
    'courseid' => $course->id,
    'form' => 'question'
));
echo html_writer::link($url, get_string('createquestion', 'mod_quizgame'), array(
    'class' => 'btn btn-default'
));

$uids = array();
foreach (get_enrolled_users($context) as $value) {
    $uids[] = $value->id;
}

$test = new DateTime(date('Y-m-d'));
$gamestoday = $DB->get_records('quizgame_record_matches');
// print_r($gamestoday);

$configurationsToday = array();
$questionsGamesToday = array();

foreach ($gamestoday as $key => $value) {
    $currentdate = new DateTime($value->date_match);
    if ($currentdate >= $test) {
        $tmp = $DB->get_record('quizgame_game_configuration', array(
            'id' => $value->configuration,
            'quizgame' => $quizgame->id
        ));

        if ($tmp != NULL) {
            $groupquestions = $DB->get_records('quizgame_question_pack', array(
                'configuration' => $tmp->id
            ), 'id', 'id, question');

            if (count($groupquestions) > 0) {

                foreach ($groupquestions as $qid => $question) {
                    $questionsGamesToday[$tmp->id][$question->question] = $question->question;
                }

                $configurationsToday[] = array(
                    'time' => $tmp->time,
                    'dtmatch' => $value->date_match,
                    'key' => $value->key,
                    'title' => urlencode($tmp->name),
                    'name' => $tmp->name,
                    'descr' => $tmp->description,
                    'cfgid' => $tmp->id,
                    'qids' => implode('|', $questionsGamesToday[$tmp->id])
                );
            } else {

                $configurationsToday[] = array(
                    'time' => $tmp->time,
                    'dtmatch' => $value->date_match,
                    'key' => $value->key,
                    'title' => urlencode($tmp->name),
                    'name' => $tmp->name,
                    'descr' => $tmp->description,
                    'cfgid' => $tmp->id,
                    'qids' => implode('|', [])
                );
            }
        }
    }
    $currentdate = null;
}

loadtemplate('teacher', array(
    'wwwroot' => $CFG->wwwroot . '/mod/quizgame/',
    'quizgame' => $cm->id,
    'qgid' => $quizgame->id,
    'courseid' => $course->id,
    'form' => 'teacher',
    'cfgstoday' => $configurationsToday,
    'tid' => $USER->id,
    'uids' => 'all',
    'gamescreated' => get_string('gamescreated', 'mod_quizgame'),
    'infogamesteacher' => get_string('infogamesteacher', 'mod_quizgame'),
    'viewquizgame' => get_string('viewquizgame', 'mod_quizgame'),
    'quizgametitle' => get_string('quizgame', 'mod_quizgame'),
    'choosequestions' => get_string('warn:empty', 'mod_quizgame')
));

echo $OUTPUT->footer();

?>
