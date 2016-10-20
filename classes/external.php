<?php
namespace mod_quizgame;

require_once ("$CFG->libdir/externallib.php");
require_once ("$CFG->dirroot/webservice/externallib.php");

use external_api;
use external_function_parameters;
use external_value;
use external_format_value;
use external_single_structure;
use external_multiple_structure;
use invalid_parameter_exception;

/**
 * This is the external API for this plugin.
 *
 * @copyright 2016 Lucas Tonussi <lptonussi@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class external extends external_api
{

    /**
     * Wrap the core function get_site_info.
     *
     * @return external_function_parameters
     */
    public static function get_site_info_parameters()
    {
        return \core_webservice_external::get_site_info_parameters();
    }

    /**
     * Expose to AJAX
     *
     * @return boolean
     */
    public static function get_site_info_is_allowed_from_ajax()
    {
        return true;
    }

    /**
     * Wrap the core function get_site_info.
     */
    public static function get_site_info($serviceshortnames = array())
    {
        global $PAGE;
        $renderer = $PAGE->get_renderer('mod_quizgame');
        $page = new \mod_quizgame\output\index_page();
        return $page->export_for_template($renderer);
    }

    /**
     * Wrap the core function get_site_info.
     *
     * @return external_description
     */
    public static function get_site_info_returns()
    {
        $result = \core_webservice_external::get_site_info_returns();
        $result->keys['currenttime'] = new external_value(PARAM_RAW, 'the current time');
        return $result;
    }
}

?>