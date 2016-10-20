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

require_capability('mod/quizgame:playgame', $context);

$PAGE->set_url('/mod/quizgame/views/student.php', array(
    'id' => $cm->id,
    'qgid' => $quizgame->id,
    'courseid' => $course->id,
    'form' => 'student'
));

$title = $course->shortname . ': ' . format_string($quizgame->name);
$PAGE->set_title($title);
$PAGE->set_heading($course->fullname);
$PAGE->set_context($context);

$PAGE->requires->css_theme(new moodle_url($CFG->wwwroot . '/mod/quizgame/components/font-awesome/css/font-awesome.min.css'));

$OUTPUT = $PAGE->get_renderer('mod_quizgame');

echo $OUTPUT->header();

if ($quizgame->intro) {
    echo $OUTPUT->box(format_module_intro('quizgame', $quizgame, $cm->id), 'generalbox mod_introbox', 'quizgameintro');
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

            if ($groupquestions != NULL) {
                foreach ($groupquestions as $qid => $question) {
                    $questionsGamesToday[$tmp->id][$question->question] = $question->question;
                }

                $configurationsToday[] = array(
                    'time' => $tmp->time,
                    'dtmatch' => $value->date_match,
                    'key' => $value->key,
                    'hidden' => ($value->hidden == 0) ? TRUE : FALSE,
                    'name' => $tmp->name,
                    'descr' => $tmp->description,
                    'cfgid' => $tmp->id,
                    'qids' => implode('|', $questionsGamesToday[$tmp->id])
                );
            }
        }
    }
    $currentdate = null;
}

$grades = $DB->get_records('quizgame_scores', array(
    'userid' => $USER->id
));
$matchgrades = array();

if (count($grades) > 0) {
    foreach ($grades as $v) {

        $tmp = $DB->get_records('quizgame_record_matches', array(
            'id' => $v->match
        ));

        $poptmp = array_pop($tmp);

        if ($quizgame->id == $poptmp->quizgame) {
            $matchgrades[] = array(
                'matchid' => $v->match,
                'rights' => $v->rights,
                'bonus' => $v->bonus,
                'quantity' => $v->quantity,
                'pontuacao' => (($v->rights * 10) / $v->quantity) + $v->bonus
            );
        }
    }
}


loadtemplate('student', array(
    'wwwroot' => $CFG->wwwroot . '/mod/quizgame/',
    'quizgame' => $cm->id,
    'qgid' => $quizgame->id,
    'courseid' => $course->id,
    'form' => 'student',
    'cfgstoday' => $configurationsToday,
    'uid' => $USER->id,
    'gamescreated' => get_string('gamescreated', 'mod_quizgame'),
    'infogamesstudent' => get_string('infogamesstudent', 'mod_quizgame'),
    'quizgametitle' => get_string('quizgame', 'mod_quizgame'),
    'studentscores' => get_string('studentscores', 'mod_quizgame'),
    'matchgrades' => $matchgrades
));

echo $OUTPUT->footer();

?>
