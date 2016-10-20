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

require_capability('mod/quizgame:watchgame', $context);
?>

<html>

<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
  <link rel="stylesheet" href="../components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../components/angular-material/angular-material.css">
  <link rel="stylesheet" href="../css/dialogs.css">
  <meta name="viewport" content="initial-scale=1" />
  <title>Moodle / Quiz Game</title>
  <link rel="shortcut icon" href="/moodle/theme/image.php/clean/theme/1473873112/favicon" />
</head>

<body layout="row" ng-app="quizgame">

  <div ng-controller="AppCtrl as app" layout="column" class="absolute" layout-fill role="main" ng-init="waitingPage()">

    <md-toolbar ng-show="!fs.status">
      <div class="md-toolbar-tools">
         <h3>
           <img alt="GQS" src="../pix/icon.png">
           <a href="/moodle">Moodle / Quiz Game</a>
         </h3>
         <span flex></span>
         <img alt="GQS" width="50px" height="50px" src="../pix/moodle.png">
         <span flex></span>
         <img alt="GQS" width="250px" height="50px" src="../pix/brasao.ufsc.png">
         <span flex></span>
         <img alt="GQS" width="100px" height="50px" src="../pix/incod_header_watermark.png">
         <span flex></span>
         <img alt="GQS" width="150px" height="50px" src="../pix/logo_gqs.png">
         <span flex></span>
      </div>
      <md-tabs md-stretch-tabs class="md-primary" md-selected="data.selectedIndex">
        <md-tab id="tab1" aria-controls="tab1-content">
          Quiz Game
        </md-tab>
      </md-tabs>
    </md-toolbar>

    <md-content flex md-scroll-y ngsf-fullscreen>
      <ui-view layout="column" layout-fill layout-padding>
        <div class="inset" hide-sm></div>
        <ng-switch on="data.selectedIndex" class="tabpanel-container">

          <div role="tabpanel"
               id="tab1-content"
               aria-labelledby="tab1"
               md-swipe-left="next()"
               ng-switch-when="0"
               md-swipe-right="previous()"
               layout="row"
               layout-align="center center">

            <md-card layout-fill>

              <md-card-content>

                <div ng-controller="TeacherPanelController as tab">

                  <div layout="row">
                    <div flex>
                      <md-button class="md-fab md-fab-bottom-right" aria-label="Add" ng-click="tab.goNext(1); tab.setstate(); countdown()" style="position:fixed !important;">
                        <ng-md-icon icon="navigate_next"></ng-md-icon>
                      </md-button>
                    </div>

                    <!--<div flex>
                      <a ngsf-toggle-fullscreen show-if-fullscreen-enabled>
                        <md-button class="md-fab md-fab-bottom-left" aria-label="Fullscreen" style="position:fixed !important;">
                          <ng-md-icon icon="aspect_ratio"></ng-md-icon>
                        </md-button>
                      </a>
                    </div>-->

                    <div flex style="position:fixed;">
                      <ul class="nav nav-pills">
                        <li ng-repeat="pill in tab.panelitems track by $index" ng-class="{ active: tab.isSelected($index) }">
                          <!-- <a href ng-click="tab.selectTab($index)">{{ pill.id }}</a> -->
                          <a href ng-click="tab.selectTab($index)" style="pointer-events: none;">{{ pill.id }}</a>
                        </li>
                      </ul>
                    </div>
                  </div>

                  <div ng-repeat="q in questionsItems track by $index" class="panel" ng-show="tab.isSelected({{ $index }})">
                    <section layout="row" layout-sm="row" layout-align="center center" layout-wrap>
                        <p class="md-display-4" ng-bind="counter" ng-init="tab.setNewCountDown(q.timer)"></p>
                        <md-button class="md-raised md-accent" ng-click="stop()">Stop</md-button>
                        <md-button class="md-raised md-warn" ng-click="tab.setstate(); countdown()">Começar</md-button>
                        <label style="font-size: 18px;">Nome: {{tab.gametitle}},<br>Chave: {{tab.gamekey}}.</label>
                        <md-button class="md-raised md-primary" ng-click="feedPerformances($index)">Avaliação</md-button>
                    </section>

                    <section layout="row" layout-sm="row" layout-align="center center" layout-wrap>
                        <h1 class="md-display-3" ng-bind-html="q.qtext"></h1>
                    </section>

                    <md-grid-list md-cols-gt-md="2"
                                  md-cols="2"
                                  md-cols-md="2"
                                  md-row-height-gt-md="{{ changeRowHeightGT() }}"
                                  md-row-height="{{ changeRowHeight() }}"
                                  md-gutter-gt-md="3px"
                                  md-gutter-md="3px"
                                  md-gutter="3px">
                        <md-grid-tile ng-repeat="a in answersItems[$index] track by $index"
                                      ng-style="{'background': tab.colorTiles[$index].colorblind}"
                                      md-rowspan="{{ tab.colorTiles[$index].span.md.row }}"
                                      md-colspan="{{ tab.colorTiles[$index].span.md.col }}"
                                      md-rowspan-sm="{{ tab.colorTiles[$index].span.sm.row }}"
                                      md-colspan-sm="{{ tab.colorTiles[$index].span.sm.col }}"
                                      md-rowspan-xs="{{ tab.colorTiles[$index].span.xs.row }}"
                                      md-colspan-xs="{{ tab.colorTiles[$index].span.xs.col }}">

                            <!-- <ng-md-icon style="fill:{{ tab.colorTiles[$index].effective }}"
                                             size="{{ tab.colorTiles[$index].size }}"
                                             icon="{{ tab.colorTiles[$index].icon }}">
                                 </ng-md-icon> -->

                            <md-label style="font-size:{{ tab.colorTiles[$index].size }};
                                      color:{{ tab.colorTiles[$index].effective }}">
                                      {{ tab.colorTiles[$index].letter }})&nbsp;
                            </md-label>

                            <div ng-if="a.answer.length >= 15">
                                <h2 class="text-uppercase" ng-bind-html="a.answer"></h2>
                            </div>

                            <div ng-if="!(a.answer.length >= 15)">
                                <h1 class="text-uppercase" ng-bind-html="a.answer"></h1>
                            </div>

                        </md-grid-tile>
                    </md-grid-list>
                  </div>
                </div>
              </md-card-content>
            </md-card>
          </div>

          <!-- <div role="tabpanel" id="tab2-content" aria-labelledby="tab2" ng-switch-when="1" md-swipe-left="next()" md-swipe-right="previous()" layout="row" layout-align="center center">
            <md-card flex-gt-sm="90" flex-gt-md="80">

              <md-card-content>
                <div ng-controller="AnswersPerformancesController as apc">
                  <div>Controller 2: {{foo}}</div>
                  <md-button ng-click="tab.addSeries()">Add Series</md-button>
                  <md-button ng-click="tab.addPoints()">Add Points to Random Series</md-button>
                  <md-button ng-click="tab.removeRandomSeries()">Remove Random Series</md-button>
                  <md-button ng-click="tab.swapChartType()">Line/Bar</md-button>
                  <highchart config="apc.PERFORMANCES"></highchart>
                </div>

              </md-card-content>

            </md-card>
          </div> -->

        </ng-switch>
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
