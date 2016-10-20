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

$qgid = required_param('qgid', PARAM_INT);
$courseid = required_param('courseid', PARAM_INT);

// Formulary leads
$form = required_param('form', PARAM_TEXT);

$id = required_param('id', PARAM_INT);
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

$res['default'] = 'default';

function updateOrInsertStudentScore($userid, $quantity, $matchid, $position, $changeState)
{
    global $DB;

    $record = $DB->get_record('quizgame_scores', array(
        'userid' => $userid,
        'match' => $matchid
    ), '*', IGNORE_MISSING);

    if ($changeState == 1) {

        if ($record) {

            $record1 = new stdClass();

            $record1->id = $record->id;

            if ($position >= 1 && $position <= 3) {
                $tmp = $record->bonus + (1 - ($position / 4));
                $record1->bonus = $tmp;
            }

            $tmp2 = $record->rights + 1;
            $record1->rights = $tmp2;
            $record1->timemodified = time();
            $response = $DB->update_record('quizgame_scores', $record1);
        } else {

            $record2 = new stdClass();
            $record2->userid = $userid;
            $record2->rights = 1;
            $record2->bonus = 0;
            $record2->quantity = $quantity;
            $record2->match = $matchid;
            $record2->timecreated = time();
            $record2->timemodified = time();

            if ($position >= 1 && $position <= 3) {
                $record2->bonus = 1 - ($position / 4);
            }

            $response = $DB->insert_record('quizgame_scores', $record2);
        }
    }
}

switch ($form) {

    case 'hiddengame':

        $key = required_param('key', PARAM_TEXT);

        $verify = $DB->get_records_sql('SELECT id FROM {quizgame_record_matches} WHERE key = :key AND quizgame = :quizgame', array(
            'key' => $key,
            'quizgame' => $qgid
        ), '', MUST_EXIST);

        if (count($verify) > 0) {

            $record = new stdClass();
            $record->id = array_pop($verify)->id;
            $record->hidden = 1;

            $checkupdate = $DB->update_record('quizgame_record_matches', $record);

            $res['hiddenmsg'] = "You hide this game, key: $key";
            $res['checkupdate'] = $checkupdate;

        } else {

            $res['hiddenmsg'] = "You did not hide this game, key: $key";

        }

        redirect($CFG->wwwroot . "/mod/quizgame/views/teacher.php?id=$id&courseid=$courseid&qgid=$qgid&form=teacher");

        break;

    case 'deletegame':

        $key = required_param('key', PARAM_TEXT);

        $verify = $DB->get_records_sql('SELECT id FROM {quizgame_record_matches} WHERE key = :key AND quizgame = :quizgame', array(
            'key' => $key,
            'quizgame' => $qgid
        ), '', MUST_EXIST);

        break;

    case 'perfs':

        $key = required_param('key', PARAM_TEXT);
        $qid = required_param('qid', PARAM_INT);
        $qaids = required_param('qaids', PARAM_TEXT);
        $qnt = required_param('qnt', PARAM_INT);

        $sum = $DB->get_records_sql('SELECT * FROM {quizgame_answers_game} WHERE key = :key and current_question = :qid ORDER BY timemodified ASC', array(
            'key' => $key,
            'qid' => $qid
        ));

        $check = $DB->get_records_sql('SELECT id FROM {quizgame_state_game} WHERE key = :key AND quizgame = :quizgame', array(
            'key' => $key,
            'quizgame' => $qgid
        ));

        $answers = $DB->get_records('question_answers', array(
            'question' => $qid
        ));

        $currentstate = $DB->get_records_sql('SELECT id, state, oldstate FROM {quizgame_state_game} WHERE key = :key AND state = :state OR state = -9999', array(
            'key' => $key,
            'state' => $qid
        ), '', IGNORE_MISSING);

        $ready = count($currentstate);
        $tmp = array_pop($currentstate);

        $changeState = 0;

        if ($ready > 0) {

            // my condition to save new scores or update scores sync. and correcly per state
            if ($qid == $tmp->oldstate && $tmp->state == - 9999) {

                $changeState = 1;

                $changeOldState = new stdClass();
                $changeOldState->id = $tmp->id;
                $changeOldState->oldstate = - 9999;
                $DB->update_record('quizgame_state_game', $changeOldState);
            }
        }

        $right = - 9999;
        foreach ($answers as $k => $a) {
            if ($a->fraction == 1) {
                $right = $a->id;
            }
        }

        $res = array(
            'rank' => array(),
            'count' => array()
        );
        $order = explode('|', $qaids);
        $order2 = array();
        $i = 0;
        $res['rank'] = array();
        foreach ($order as $v) {
            $aux = intval($v, 10);
            $order2[] = $aux;
            $res['count'][$order2[$i]] = 0;
            if ($aux == $right) {
                $res['rightindex'] = $i;
            }
            $i = $i + 1;
        }

        if (count($check) > 0) {
            $matchid = array_pop($check)->id;

            $position = 0;
            foreach ($sum as $k => $v) {

                if ($v->current_answer == 0) {
                    $tmp0 = $order2[0];
                    $res['count'][$tmp0] += 1;
                    if ($tmp0 == $right) {
                        $res['rank'][] = array(
                            'position' => $position += 1,
                            'userid' => $v->userid,
                            'name' => $DB->get_record('user', array(
                                'id' => $v->userid
                            ), 'firstname, lastname')
                        );
                        updateOrInsertStudentScore($v->userid, $qnt, $matchid, $position, $changeState);
                    }
                }
                if ($v->current_answer == 1) {
                    $tmp1 = $order2[1];
                    $res['count'][$tmp1] += 1;
                    if ($tmp1 == $right) {
                        $res['rank'][] = array(
                            'position' => $position += 1,
                            'userid' => $v->userid,
                            'name' => $DB->get_record('user', array(
                                'id' => $v->userid
                            ), 'firstname, lastname')
                        );
                        updateOrInsertStudentScore($v->userid, $qnt, $matchid, $position, $changeState);
                    }
                }
                if ($v->current_answer == 2) {
                    $tmp2 = $order2[2];
                    $res['count'][$tmp2] += 1;
                    if ($tmp2 == $right) {
                        $res['rank'][] = array(
                            'position' => $position += 1,
                            'userid' => $v->userid,
                            'name' => $DB->get_record('user', array(
                                'id' => $v->userid
                            ), 'firstname, lastname')
                        );
                        updateOrInsertStudentScore($v->userid, $qnt, $matchid, $position, $changeState);
                    }
                }
                if ($v->current_answer == 3) {
                    $tmp3 = $order2[3];
                    $res['count'][$tmp3] += 1;
                    if ($tmp3 == $right) {
                        $res['rank'][] = array(
                            'position' => $position += 1,
                            'userid' => $v->userid,
                            'name' => $DB->get_record('user', array(
                                'id' => $v->userid
                            ), 'firstname, lastname')
                        );
                        updateOrInsertStudentScore($v->userid, $qnt, $matchid, $position, $changeState);
                    }
                }
            }
        }

        break;

    case 'teacher':

        $sendkey = required_param('sendkey', PARAM_TEXT);
        $sendqids = required_param('sendqids', PARAM_TEXT);

        $verify = $DB->get_records_sql('SELECT * FROM {quizgame_record_matches} WHERE key = :key', array(
            'key' => $sendkey
        ), '', MUST_EXIST);

        $checkCountGamesByKey = $DB->get_records_sql('SELECT count(id) FROM {quizgame_state_game} WHERE key = :key', array(
            'key' => $sendkey,
            'userid' => $USER->id
        ));

        if ($checkCountGamesByKey > 0) {
            $res['message'] = "Game already started.";
        } else {
            $sequence = explode('|', $sendqids);

            $record = new stdClass();
            $record->userid = $USER->id;
            $record->quizgame = $qgid;
            $record->key = $sendkey;
            $record->state = intval($sequence[0], 10);
            $record->timecreated = time();
            $record->timemodified = time();

            $response = $DB->insert_record('quizgame_state_game', $record, false);
            $res['message'] = "You started a game nro: $sendkey.";
        }
        break;

    case 'key':

        $size = required_param('size', PARAM_INT);
        $cfgid = required_param('cfgid', PARAM_INT);

        $str = random_string($size);
        $date = date('Y-m-d');
        $key = substr(sha1($str), 0, 10);

        $record = new stdClass();
        $record->quizgame = $quizgame->id;
        $record->hidden = 1; // 1 means true (initially hidden match)
        $record->key = $key;
        $record->configuration = $cfgid;
        $record->date_match = $date;
        $record->timecreated = time();
        $record->timemodified = time();

        $lastinsertid = $DB->insert_record('quizgame_record_matches', $record, false);
        $res['message'] = "You have added a key: $key associated with a configuration nro: $cfgid, at $date.";
        break;

    case 'package':

        $cfgid = required_param('cfgid', PARAM_INT);
        $qids = required_param('qids', PARAM_TEXT);

        $tmp = explode('|', $qids);
        $dataobjects = array();

        foreach ($tmp as $qid) {
            $record = new stdClass();
            $record->quizgame = $quizgame->id;
            $record->question = $qid;
            $record->configuration = $cfgid;
            $record->timecreated = time();
            $record->timemodified = time();
            $dataobjects[] = $record;
        }

        $lastinsertid = $DB->insert_records('quizgame_question_pack', $dataobjects);
        $res['message'] = "You have added [$qids] associated with configuration nro. $cfgid.";
        break;

    case 'config':

        $time = required_param('time', PARAM_INT);
        $name = required_param('name', PARAM_TEXT);
        $descr = required_param('descr', PARAM_TEXT);

        $record = new stdClass();
        $record->quizgame = $quizgame->id;
        $record->time = $time;
        $record->name = $name;
        $record->description = $descr;
        $record->timecreated = time();
        $record->timemodified = time();

        $lastinsertid = $DB->insert_record('quizgame_game_configuration', $record, false);
        $res['message'] = "A new configuration [$descr] has been added.";
        break;

    case 'question':

        $qname = required_param('qname', PARAM_TEXT);
        $qtext = required_param('qtext', PARAM_TEXT);
        $answers = required_param('answers', PARAM_TEXT);
        $fractions = required_param('fractions', PARAM_TEXT);
        $catid = required_param('catid', PARAM_INT);

        $record = new stdClass();
        $record->category = $catid;
        $record->name = $qname;
        $record->questiontext = $qtext;
        $record->qtype = 'multichoice';
        $record->generalfeedback = 'QUIZGAME for Moodle (TM) using Angular (TM) hacks!';
        $record->questiontextformat = 1;
        $record->generalfeedbackformat = 1;

        $record->timecreated = time();
        $record->timemodified = time();
        $record->createdby = $USER->id;
        $record->modifiedby = $USER->id;

        $qid = $DB->insert_record('question', $record, true);

        $dataobjects = array();
        $gatherAnswers = explode('|', $answers);
        $gatherFractions = explode('|', $fractions);

        $index = 0;
        foreach ($gatherAnswers as $ans) {

            $record = new stdClass();
            $record->question = $qid;
            $record->answer = $ans;
            $record->fraction = floatval($gatherFractions[$index]);
            $record->feedback = 'QUIZGAME for Moodle (TM) using Angular (TM) hacks!';
            $record->answerformat = 1;
            $record->feedbackformat = 1;
            $dataobjects[] = $record;

            $index = $index + 1;
        }

        $lastinsertid2 = $DB->insert_records('question_answers', $dataobjects);

        $res['message'] = "A new question [$qtext] has been added (quizgame multichoice, 4 answers only).";
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
