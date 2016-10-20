<?php
header("Content-Type: application/json;charset=utf-8");

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

$cmid = required_param('id', PARAM_INT);
$qids = required_param('qids', PARAM_TEXT);
$cfgid = required_param('cfgid', PARAM_INT);

if ($cmid) {
    $cm = get_coursemodule_from_id('quizgame', $cmid, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array(
        'id' => $cm->course
    ), '*', MUST_EXIST);
    $quizgame = $DB->get_record('quizgame', array(
        'id' => $cm->instance
    ), '*', MUST_EXIST);
}

if ($qids != NULL) {
    $qids_aux = explode('|', $qids);
} else {
    $qids_aux = [];
}

$quizgame_pack = $DB->get_records('quizgame_question_pack', array('configuration' => $cfgid), '', '*');

// $time = $DB->get_record('quizgame_game_configuraton', array('id' => $cfgid), '', 'time', '*', MUST_EXIST);

$dataArrAnswers = array();

foreach ($qids_aux as $qid) {
    array_push($dataArrAnswers, $DB->get_records('question', array('id' => $qid), '', 'id, questiontext', IGNORE_MISSING)[$qid]);
}

$json = json_encode( $dataArrAnswers,
                     JSON_PRETTY_PRINT
                   | JSON_NUMERIC_CHECK
                   | JSON_UNESCAPED_SLASHES
                   | JSON_FORCE_OBJECT);

if ($json === false) {
    // Avoid echo of empty string (which is invalid JSON), and
    // JSONify the error message instead:
    $json = json_encode(array("jsonError", json_last_error_msg()));
    if ($json === false) {
        // This should not happen, but we go all the way now:
        $json = '{"jsonError": "unknown"}';
    }
    // Set HTTP response status code to: 500 - Internal Server Error
    http_response_code(500);
}

// echo preg_replace('/&\w*;/', '', strip_tags($json));
echo $json;
flush();
?>