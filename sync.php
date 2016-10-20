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
require_once (dirname(dirname(dirname(__FILE__))) . '/config.php');

require_once (dirname(__FILE__) . '/lib.php');

require_once (dirname(dirname(dirname(__FILE__))) . '/mod/quizgame/locallib.php');

$id = required_param('id', PARAM_INT);
$qgid = required_param('qgid', PARAM_INT);
$courseid = required_param('courseid', PARAM_INT);

// Formulary leads
$form = required_param('form', PARAM_TEXT);

if ($id) {
    $cm = get_coursemodule_from_id('quizgame', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array(
        'id' => $cm->course
    ), '*', MUST_EXIST);
    $quizgame = $DB->get_record('quizgame', array(
        'id' => $cm->instance
    ), '*', MUST_EXIST);
}

require_login($course, true, $cm);
$context = context_module::instance($cm->id);

$res['default'] = 'default';

switch ($form) {

    case 'timesup':

        $key = required_param('key', PARAM_TEXT);

        $gameid = $DB->get_records_sql('SELECT id, state FROM {quizgame_state_game} WHERE key = :key', array(
            'key' => $key
        ));

        $tmpCount = count($gameid);
        $tmpGameStatus = array_pop($gameid);

        if ($tmpCount == 1) {

            $record = new stdClass();

            $record->id = $tmpGameStatus->id;
            $record->quizgame = $qgid;

            // only moment when I need to save the
            // /looping state/ for synching porpuses
            $record->oldstate = $tmpGameStatus->state;

            $record->state = - 9999;
            $record->timemodified = time();

            $response = $DB->update_record('quizgame_state_game', $record);

            $res['message'] = "Times Up, No more answers for a while.";
            $res['key'] = $key;
            $res['countdown'] = 'ACK';

        } else {

            $res['key'] = $key;
            $res['countdown'] = 'NACK';
            $res['gameid'] = 'NOTFOUND';

        }

        break;

    case 'startgame':

        $key = required_param('key', PARAM_TEXT);

        $verify = $DB->get_records_sql('SELECT id FROM {quizgame_record_matches} WHERE key = :key', array(
            'key' => $key
        ));

        if (count($verify) == 1) {
            $record = new stdClass();
            $record->id = array_pop($verify)->id;
            $record->hidden = 0; // 0 means false (not hidden)
            $record->timemodified = time();

            $success = $DB->update_record('quizgame_record_matches', $record);

            if ($success) {
                $res['message'] = "Game nro: [$key], engadge.";
                $res['countdown'] = 'ACK';
                $res['success'] = $success;
            } else {
                $res['message'] = "Game nro: [$key], not engadge.";
                $res['countdown'] = 'NACK';
                $res['success'] = $success;
            }
        } else {
            $res['message'] = "Game nro: [$key], do not exist.";
            $res['countdown'] = 'NACK';
        }
        break;

    case 'setstate':

        $key = required_param('key', PARAM_TEXT);
        $state = required_param('state', PARAM_INT);

        $verify = $DB->get_records_sql('SELECT id FROM {quizgame_record_matches} WHERE key = :key', array(
            'key' => $key
        ));

        $gameid = $DB->get_records_sql('SELECT id FROM {quizgame_state_game} WHERE key = :key', array(
            'key' => $key
        ));

        if (count($verify) == 1 && count($gameid) == 0) {

            $record = new stdClass();
            $record->userid = $USER->id;
            $record->quizgame = $qgid;
            $record->key = $key;
            $record->state = $state;
            $record->timecreated = time();
            $record->timemodified = time();

            $success = $DB->insert_record('quizgame_state_game', $record, false);

            if ($success) {
                $res['message'] = "State is now holding on question nro: [$state].";
                $res['countdown'] = 'ACK';
                $res['success'] = $success;
            } else {
                $res['message'] = "State is not holding on question nro: [$state].";
                $res['countdown'] = 'NACK';
                $res['success'] = $success;
            }
        }

        if (count($verify) == 1 && count($gameid) == 1) {

            $record = new stdClass();
            $record->id = array_pop($gameid)->id;
            $record->userid = $USER->id;
            $record->quizgame = $qgid;
            $record->state = $state;
            $record->timemodified = time();

            $success = $DB->update_record('quizgame_state_game', $record);

            if ($success) {
                $res['message'] = "State is now holding on question nro: [$state].";
                $res['countdown'] = 'ACK';
                $res['success'] = $success;
            } else {
                $res['message'] = "State is not holding on question nro: [$state].";
                $res['countdown'] = 'NACK';
                $res['success'] = $success;
            }
        }

        break;

    case 'cfg':

        $uid = required_param('uid', PARAM_INT);
        $qids = required_param('qids', PARAM_TEXT);
        $key = required_param('key', PARAM_TEXT);

        $tab = 0;

        $states = explode('|', $qids);
        $states2 = array();
        for ($i = 0; $i < count($states); $i ++) {
            $states2[$states[$i]] = $i;
        }
        $states = null;

        $currentstate = $DB->get_records_sql('SELECT state FROM {quizgame_state_game} WHERE key = :key', array(
            'key' => $key
        ));

        $ready = count($currentstate);

        $tmp = array_pop($currentstate);
        $where = $states2[$tmp->state];

        if ($tmp->state != - 9999) {
            if ($tab != $where) {
                $where = $states2[$tmp->state];
                $res['message'] = "game configuration released.";
                $res['warning'] = 'NACK';
                $res['currstate'] = $tmp->state;
                $res['where'] = $where;
            } else {
                $where = $states2[$tmp->state];
                $res['message'] = "game configuration released.";
                $res['warning'] = 'ACK';
                $res['currstate'] = $tmp->state;
                $res['where'] = $where;
            }
        } else {
            $res['message'] = "game configuration released.";
            $res['warning'] = 'NACK';
            $res['currstate'] = - 9999;
            $res['where'] = $where;
        }

        break;

    case 'setanswer':

        $uid = required_param('uid', PARAM_INT);
        // $currq = required_param('currq', PARAM_INT);
        $curra = required_param('curra', PARAM_INT);
        $qids = required_param('qids', PARAM_TEXT);
        $key = required_param('key', PARAM_TEXT);

        $states = explode('|', $qids);
        $states2 = array();
        for ($i = 0; $i < count($states); $i ++) {
            $states2[$states[$i]] = $i;
        }
        $states = null;

        $currentstate = $DB->get_records_sql('SELECT state FROM {quizgame_state_game} WHERE key = :key', array(
            'key' => $key
        ));

        $ready = count($currentstate);
        $tmp = array_pop($currentstate);

        /*
         * $where = 0;
         * if ($tmp->state != - 9999) {
         * $where = $states2[$tmp->state];
         * }
         */

        $studentAnswer = $DB->get_records_sql('SELECT id FROM {quizgame_answers_game} WHERE key = :key and userid = :userid', array(
            'key' => $key,
            'userid' => $USER->id
        ));

        $countanswers = count($studentAnswer);
        $tmp2 = array_pop($studentAnswer);

        $res['currstate'] = $tmp->state;
        // $res['currq'] = $currq;
        $res['message'] = "answer not updated.";
        $res['warning'] = 'NACK';
        $res['state'] = - 9999;

        if ($ready > 0 && $tmp->state != - 9999) {
            if ($countanswers == 0) {

                $record = new stdClass();
                $record->userid = $USER->id;
                $record->current_answer = $curra;
                $record->current_question = $tmp->state;
                $record->key = $key;
                $record->timecreated = time();
                $record->timemodified = time();

                $success = $DB->insert_record('quizgame_answers_game', $record);

                if ($success) {
                    $res['message'] = "new answer created. count: ";
                    $res['warning'] = 'ACK';
                } else {
                    $res['message'] = "answer creation failed.";
                    $res['warning'] = 'NACK';
                }
            } else {

                // this line synchronize students with a teacher
                $record = new stdClass();
                $record->id = $tmp2->id;
                $record->userid = $USER->id;
                $record->current_answer = $curra;
                $record->current_question = $tmp->state;
                $record->key = $key;
                $record->timemodified = time();

                $success = $DB->update_record('quizgame_answers_game', $record);

                if ($success) {
                    $res['message'] = "you are in the right state. answer updated.";
                    $res['warning'] = 'ACK';
                } else {
                    $res['message'] = "answer not updated.";
                    $res['warning'] = 'NACK';
                }
            }
        }
        break;

    default:
        break;
}

$json = json_encode($res, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES | JSON_FORCE_OBJECT);

if ($json === false) {
    // Avoid echo of empty string (which is invalid JSON), and
    // JSONify the error message instead:
    $json = json_encode(array(
        "jsonError",
        json_last_error_msg()
    ));
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
