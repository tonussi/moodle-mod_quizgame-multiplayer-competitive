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
 * This is an empty module, that is required before all other modules.
 * Because every module is returned from a request for any other module, this
 * forces the loading of all modules with a single request.
 *
 * @module     mod_quizgame/refresh
 * @package    mod_quizgame
 * @copyright  2015 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      2.9
 */
define(['jquery', 'core/ajax', 'core/templates', 'core/notification'], function($, ajax, templates, notification) { 
    return /** @alias module:mod_quizgame/refresh */ {
        
        /**
         * Refresh the middle of the page!
         *
         * @method refresh
         */
        refresh: function() {
            // Add a click handler to the button.
            $('[data-region="index-page"] #refresh').on('click', function() {

                // First - reload the data for the page.
                var promises = ajax.call([{
                    methodname: 'mod_quizgame_get_site_info',
                    args:{ }
                }]);
                promises[0].done(function(data) {

                    // We have the data - lets re-render the template with it.
                    templates.render('mod_quizgame/index_page', data).done(function(html, js) {
                        $('[data-region="index-page"]').replaceWith(html);
                        // And execute any JS that was in the template.
                        templates.runTemplateJS(js);
                    }).fail(notification.exception);
                }).fail(notification.exception);
            });
        }
    };
});
