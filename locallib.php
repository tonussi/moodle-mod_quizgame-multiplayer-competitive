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
 * Internal library of functions for module quizgame
 *
 * All the quizgame specific functions, needed to implement the module
 * logic, should go here. Never include this file from your lib.php!
 *
 * @package mod_quizgame
 * @copyright 2016 Lucas Tonussi <lptonussi@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

// require_once (dirname(dirname(dirname(__FILE__))) . '/lib/questionlib.php');
// require_once (dirname(dirname(dirname(__FILE__))) . '/mod/quizgame/mobiledetect.php');
// require_once (dirname(dirname(dirname(__FILE__))) . '/mod/quizgame/quizgame.php');
// require_once (dirname(dirname(dirname(__FILE__))) . '/mod/quizgame/vendor/autoload.php');

function htmlspecial_array(&$variable)
{
    foreach ($variable as &$value) {
        if (!is_array($value)) {
            $value = htmlspecialchars($value, ENT_COMPAT, 'UTF-8');
        } else {
            htmlspecial_array($value);
        }
    }
}

function getMustacheConfiguration()
{
    return array(
        'template_class_prefix' => '__MyTemplates_',
        'cache' => dirname(dirname(dirname(__FILE__))) . '/mod/quizgame/tmp/cache/mustache',
        'cache_file_mode' => 0666, // Please, configure your umask instead of doing this :)
        'cache_lambda_templates' => true,
        'loader' => new Mustache_Loader_FilesystemLoader(dirname(dirname(dirname(__FILE__))) . '/mod/quizgame/templates'),
        'helpers' => array(
            'i18n' => function ($text) {}
        ),
        'escape' => function ($value) {
            if (!is_array($value)) {
                return htmlspecialchars($value, ENT_COMPAT, 'UTF-8');
            } else {
                return htmlspecial_array($value);
            }
        },
        'charset' => 'ISO-8859-1',
        'logger' => new Mustache_Logger_StreamLogger('php://stderr'),
        'strict_callables' => true,
        'pragmas' => [
            Mustache_Engine::PRAGMA_FILTERS
        ]
    );
}

function student_join_matches($userid, $context, $cm)
{
    global $PAGE, $DB, $OUTPUT;

    $tablequestionpackage = new html_table();

    $questionids = array_keys($DB->get_records('quizgame_question_pack'));

    $gamestoday = $DB->get_records('quizgame_record_matches');

    print_r($gamestoday);
}

function teacher_watch_matches($players, $configs, $packets, $context, $cm)
{
    global $PAGE, $DB, $OUTPUT;

    $tablequestionpackage = new html_table();

    $tablequestionpackage->head = array(
        get_string('quizgameid', 'mod_quizgame'),
        get_string('quizgamedesc', 'mod_quizgame'),
        get_string('quizgameplayer', 'mod_quizgame'),
        get_string('quizgametools', 'mod_quizgame')
    );

    $tablequestionpackage->attributes['class'] = 'generaltable mod_index';

    $groupquestions = array();
    foreach ($packets as $key => $packet) {
        $groupquestions["$packet->configuration"][] = $packet->question;
    }

    foreach ($groupquestions as $key => $value) {

        $groupquestions[$key] = implode('|', $groupquestions[$key]);

        $str = random_string();

        $linkWatch = html_writer::link(new moodle_url('watch.php', array(
            'id' => $cm->id,
            'uids' => implode('|', $players),
            'qids' => $groupquestions[$key],
            'times' => $configs[$packets[$key]->configuration]->time,
            'qskey' => sha1($str),
            'cfgid' => $packets[$key]->configuration
        )), get_string('viewquizgame', 'mod_quizgame'));

        $tablequestionpackage->data[$key] = array(
            get_string('quizgame', 'mod_quizgame') . ' #' . $key,
            '',
            '',
            html_writer::span(null, 'fa fa-eye', array(
                'aria-hidden' => 'true'
            )) . ' ' . $linkWatch . html_writer::end_span()
        );
    }

    echo html_writer::table($tablequestionpackage);
}

function loadtemplate($template, $data)
{
    // global $PAGE, $DB, $OUTPUT;
    Mustache_Autoloader::register();
    $mustache = new Mustache_Engine(getMustacheConfiguration());
    $tpl = $mustache->loadTemplate($template);
    echo $tpl->render($data);
}

?>
