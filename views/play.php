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

$id = required_param('id', PARAM_INT);

if ($id) {
    $cm = get_coursemodule_from_id('quizgame', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array(
        'id' => $cm->course
    ), '*', MUST_EXIST);
    $quizgame = $DB->get_record('quizgame', array(
        'id' => $cm->instance
    ), '*', MUST_EXIST);
}

// print_r ($id);
// print_r ($cm);
// print_r ($course);
// print_r ($quizgame);

require_login($course, true, $cm);
$context = context_module::instance($cm->id);

// print_r($context);

if (!has_capability('mod/quizgame:playgame', $context)) {
    // print_r('true');
    redirect(new moodle_url('/course/view.php', array('id' => $course->id)));
}
?>

<html id="fullthis">

<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
<!--   <link rel="stylesheet" href="../components/bootstrap/dist/css/bootstrap.min.css"> -->
  <link rel="stylesheet" href="../components/angular-material/angular-material.css">
  <link rel="stylesheet" href="../css/dialogs.css">
  <title>Moodle / Quiz Game</title>
  <link rel="shortcut icon" href="/moodle/theme/image.php/clean/theme/1473873112/favicon" />
</head>

<body layout="row" ng-app="quizgame" class="noselect">

  <div ng-controller="AppCtrl as app" layout="column" class="absolute" layout-fill role="main">

    <md-toolbar>
      <div class="md-toolbar-tools">
         <span flex></span>
         <a href="{{gobacktostudentarea}}">
             <img alt="QuizGame" src="../pix/icon.png"/>
         </a>
         <span flex></span>
         <img alt="Moodle" width="50px" height="50px" src="../pix/moodle.png"/>
         <span flex></span>
         <img alt="UFSC" width="50px" height="50px" src="../pix/brasao.ufsc2.png"/>
         <span flex></span>
      </div>
    </md-toolbar>

    <md-content flex md-scroll-y>
      <ui-view layout="column" layout-fill layout-padding>
          <div class="inset" hide-sm></div>

          <div ng-controller="StudentPanelController as tab">

            <div layout="row">

              <div flex>
                  <!--<a ngsf-toggle-fullscreen show-if-fullscreen-enabled></a>-->
                  <a id="togglefullscreen">
                      <md-button class="md-fab md-fab-bottom-right" aria-label="Fullscreen" style="position:fixed !important;">
                        <ng-md-icon icon="fullscreen"></ng-md-icon>
                      </md-button>
                  </a>
              </div>

            </div>

            <md-grid-list md-cols-gt-md="2"
                          md-cols="2"
                          md-cols-md="2"
                          md-row-height-gt-md="{{ changeRowHeightGT() }}"
                          md-row-height="{{ changeRowHeight() }}"
                          md-gutter-gt-md="1px"
                          md-gutter-md="1px"
                          md-gutter="1px">

                <md-grid-tile ng-ref ng-repeat="tile in tab.colorTiles"
                              ng-style="{'background': tile.colorblind}"
                              md-rowspan="{{ tab.colorTiles[$index].span.md.row }}"
                              md-colspan="{{ tab.colorTiles[$index].span.md.col }}"
                              md-rowspan-sm="{{ tab.colorTiles[$index].span.sm.row }}"
                              md-colspan-sm="{{ tab.colorTiles[$index].span.sm.col }}"
                              md-rowspan-xs="{{ tab.colorTiles[$index].span.xs.row }}"
                              md-colspan-xs="{{ tab.colorTiles[$index].span.xs.col }}"
                              ng-click="tab.send(tile.id);"
                              md-ink-ripple="{{tile.effective}}">

                     <!--<ng-md-icon style="fill:{{ tile.effective }};
                                     font-size:{{ tile.size }};
                                     color:{{ tile.effective }}"
                                     size="{{ tile.size }}"
                                     icon="{{ tile.icon }}">
                     </ng-md-icon>-->

                     <md-label style="font-size:{{ tile.size }};
                               color:{{ tile.effective }}">{{ tile.letter }}</md-label>

                </md-grid-tile>

            </md-grid-list>

          </div>

      </ui-view>
    </md-content>

  </div>

  <script type="text/javascript" src="../components/angular/angular.min.js"></script>
  <script type="text/javascript" src="../components/angular-material/angular-material.min.js"></script>
  <script type="text/javascript" src="../components/angular-animate/angular-animate.min.js"></script>
  <script type="text/javascript" src="../components/angular-aria/angular-aria.min.js"></script>
  <script type="text/javascript" src="../components/angular-material-icons/angular-material-icons.min.js"></script>
  <script type="text/javascript" src="../components/angular-messages/angular-messages.min.js"></script>
  <script type="text/javascript" src="../components/angular-route/angular-route.min.js"></script>
  <script type="text/javascript" src="../components/angular-sanitize/angular-sanitize.min.js"></script>
  <!-- <script type="text/javascript" src="../components/tv4/tv4.js"></script> -->
  <!-- <script type="text/javascript" src="../components/objectpath/lib/ObjectPath.js"></script> -->
  <!-- <script type="text/javascript" src="../components/angular-schema-form/dist/schema-form.min.js"></script> -->
  <!-- <script type="text/javascript" src="../components/angular-schema-form/dist/bootstrap-decorator.min.js"></script> -->
  <script type="text/javascript" src="../components/screenfull/dist/screenfull.min.js"></script>
  <script type="text/javascript" src="../components/angular-screenfull/dist/angular-screenfull.min.js"></script>
  <!-- <script type="text/javascript" src="../components/angular-dynforms/dynamic-forms.js"></script> -->
  <script type="text/javascript" src="../components/jquery/dist/jquery.min.js"></script>
  <script type="text/javascript" src="../components/highcharts/highcharts.js"></script>
  <script type="text/javascript" src="../components/highcharts-ng/dist/highcharts-ng.min.js"></script>
  <script type="text/javascript" src="../ng/app.js" ng-module="quizgame"></script>
  <!-- <script src="ng/routes.js"></script> -->
  <!-- <script src="ng/controller/quizgame-tab.js"></script> -->
  <!-- <script src="ng/controller/read-questions.js"></script> -->
  <!-- <script src="ng/directives/widget.js"></script> -->
  <!-- <script src="ng/services/quizgame.js"></script> -->
</body>

</html>