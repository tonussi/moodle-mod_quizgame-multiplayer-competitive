/**
 * Quiz Game Matches Competition Application
 */
(function() {
    /**
     * Application and Imports
     */
    var app = angular.module('quizgame', ['highcharts-ng',
        'angularScreenfull', 'ngRoute', 'ngMaterial', 'ngMessages',
        'ngMdIcons', 'ngSanitize'
    ]);

    var BASEURL = window.location.origin;
    var HOST = window.location.host;
    var PATHARRAY = window.location.pathname.split('/');

    /**
     * Global Functions
     */
    function getUrlParameter(sParam) {
        var sPageURL = decodeURIComponent(window.location.search.substring(1)),
            sURLVariables = sPageURL.split('&'),
            sParameterName,
            i;

        for (i = 0; i < sURLVariables.length; i++) {
            sParameterName = sURLVariables[i].split('=');

            if (sParameterName[0] === sParam) {
                return sParameterName[1] === undefined ? true :
                    sParameterName[1];
            }
        }
    };

    function checkWH() {
        if ((window.outerWidth-screen.width) ==0 && (window.outerHeight-screen.height) == 0) {
            return true;
        } else {
            return false;
        }
    }

    String.prototype.replaceAll = function(str1, str2, ignore) {
        return this.replace(new RegExp(str1.replace(/([\/\,\!\\\^\$\{\}\[\]\(\)\.\*\+\?\|\<\>\-\&])/g,"\\$&"),(ignore?"gi":"g")),(typeof(str2)=="string")?str2.replace(/\$/g,"$$$$"):str2);
    }

    $('#togglefullscreen').on('click', function() {
        // if already full screen; exit
        // else go fullscreen
        if (document.fullscreenElement ||
            document.webkitFullscreenElement ||
            document.mozFullScreenElement ||
            document.msFullscreenElement) {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.mozCancelFullScreen) {
                document.mozCancelFullScreen();
            } else if (document.webkitExitFullscreen) {
                document.webkitExitFullscreen();
            } else if (document.msExitFullscreen) {
                document.msExitFullscreen();
            }
        } else {
            element = $('#fullthis').get(0);
            if (element.requestFullscreen) {
                element.requestFullscreen();
            } else if (element.mozRequestFullScreen) {
                element.mozRequestFullScreen();
            } else if (element.webkitRequestFullscreen) {
                element.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
            } else if (element.msRequestFullscreen) {
                element.msRequestFullscreen();
            }
        }
    });

    function gcd(a, b) {
        return (b == 0) ? a : gcd(b, a % b);
    }

    /**
     * Filters
     */
    app.filter('num', function() {
        return function(input) {
            return parseInt(input, 10);
        };
    });

    /**
     * Services
     */
    app.factory('Service', function($http, $timeout) {
        var Service = {
            bridgePerformances: {
                data: []
            }
        };
        return Service;
    });

    app.factory('Colors', function() {
        var Colors = {
            LETTERS: ['A', 'B', 'C', 'D'],
            COLORSBLIND: ['#FDD3C5', '#019DD0', '#C7E4D3', '#9E8DC4'],
            EFFECTIVE: ['#231F20', '#A9306F', '#3D6AB3', '#5F4139'],
            ICONS: ['fiber_manual_record',
                    'fiber_manual_record',
                    'fiber_manual_record',
                    'fiber_manual_record']
        }
        return Colors;
    });

    /**
     * Controllers
     */
    app.controller('TempoController', function($scope, $http) {
        this.questionTime = 5000;

        this.getActualTiming = function(t) {
            this.questionTime = t;
        };

        var c = new Date().getTime() + this.questionTime;

        $('#clock').countdown(c, {
            elapse: true
        }).on('update.countdown', function(event) {
            var $this = $(this);
            if (event.elapsed) {
                // $this.html(event.strftime('%M:%S'));
                $scope.$emit('someEvent', 1);
            } else {
                $this.html(event.strftime('%M:%S'));
            }
        });
    });

    app.controller('StudentPanelController', function($scope, $http, Colors) {

        $scope.answersItems = [];

        var url = {
            id: getUrlParameter('id'),
            qids: getUrlParameter('qids'),
            key: getUrlParameter('key'),
            cfgid: getUrlParameter('cfgid'),
            qgid: getUrlParameter('qgid'),
            courseid: getUrlParameter('courseid'),
            time: getUrlParameter('time'),
            uid: getUrlParameter('uid'),
            dtmatch: getUrlParameter('dtmatch')
        };

        this.getAllAnswers = function() {
            var geturl = BASEURL +
                "/moodle/mod/quizgame/views/answers.php?" +
                "id=" + url.id +
                "&qids=" + url.qids +
                "&cfgid=" + url.cfgid;

            $http({
                method: 'GET',
                url: geturl
            }).then(function successCallback(response) {

                angular.forEach(response.data, function(value, key) {
                    var answers = [];
                    angular.forEach(value, function(v, k) {
                        answers.push(v);
                    });

                    $scope.answersItems.push(answers);
                });

            }.bind(this), function errorCallback(
                response) {
                console.log(response);
            });
        };

        this.getAllAnswers();

        this.tab = 0;
        this.changetab = 0;
        this.offset = 1;
        this.finalstate = false;
        this.hisAnswers = [];

        this.panelitems = $scope.answersItems;

        this.send = function(tileid) {

            $scope.master = {
                curra: tileid,
                key: url.key,
                form: 'setanswer'
            }

            console.log($scope.master);
            this.submitFormSetAnswer();
            $scope.master = {};

        };

        this.submitFormSetAnswer = function() {
            var posturl = BASEURL +
                "/moodle/mod/quizgame/sync.php?" +
                "id=" + url.id +
                "&qgid=" + url.qgid +
                "&courseid=" + url.courseid +
                "&uid=" + url.uid +
                "&key=" + url.key +
                "&qids=" + url.qids +
                "&curra=" + $scope.master.curra +
                "&form=" + $scope.master.form;

            console.log(posturl);

            $http({
                method: 'POST',
                url: posturl,
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).then(function successCallback(callbackInformation) {
                $scope.messages = {
                    response: callbackInformation.data.message,
                    warning: callbackInformation.data.warning
                }
            }, function errorCallback(response) {
                console.log(response)
            })
        };

        this.quitgame = function() {
            var sequence = '';
            for (var where = 0; where < $scope.answersItems.length; where++) {
           	sequence += '&s' + where + $scope.answersItems[where][0].id + '|' +
           	                           $scope.answersItems[where][1].id + '|' +
           	                           $scope.answersItems[where][2].id + '|' +
           	                           $scope.answersItems[where][3].id;
            }

            $scope.master = {
                key: url.key,
                form: 'quit',
                sequence
            };

            console.log($scope.master);
            this.submitFormState();
            $scope.master = {};

        };

        this.submitFormQuitGame = function() {

            var posturl = BASEURL +
                "/moodle/mod/quizgame/restforms.php?" +
                "id=" + url.id +
                "&qgid=" + url.qgid +
                "&courseid=" + url.courseid +
                "&qids=" + url.qids +
                "&form=" + $scope.master.form +
                "&key=" + $scope.master.key +
                $scope.master.sequence;

            console.log(posturl);

            $http({
                method: 'POST',
                url: posturl,
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).then(function successCallback(callbackInformation) {
                $scope.messages = {
                    response: callbackInformation.data.message,
                };

                setTimeout(function() {
                    $scope.$apply(function() {
                        counterSynchro = {
                            countdown: callbackInformation.data.countdown
                        };
                    });
                }, 500);

                console.log(counterSynchro.countdown);
            }, function errorCallback(response) {
                console.log(response)
            })
        };

        var w = screen.width;
        var h = screen.height;
        var r = gcd(w, h);

        $scope.changeRowHeightGT = function() {
            if (h < w) {
                return '2:1';
            } else {
                return '1:2';
            }
        }

        $scope.changeRowHeight = function() {
            if (h < w) {
                return '4:3';
            } else {
                return '3:4';
            }
        }

        this.colorTiles = (function() {
            var tiles = [];
            for (var i = 0; i < 4; i++) {
                tiles.push({
                    id: i,
                    size: 100,
                    letter: Colors.LETTERS[i],
                    icon: Colors.ICONS[i],
                    colorblind: Colors.COLORSBLIND[
                        i],
                    effective: Colors.EFFECTIVE[i],
                    span: {
                        md: {
                            col: (w / r) / (h / r),
                            row: (w / r) / (h / r)
                        },
                        sm: {
                            col: (w / r) / (h / r),
                            row: (w / r) / (h / r)
                        },
                        xs: {
                            col: (w / r) / (h / r),
                            row: (w / r) / (h / r)
                        }
                    }
                });
            }
            return tiles;
        })();

    });

    app.controller('AnswersPerformancesController', function($scope, Service) {
	this.bridgePerformances = Service.bridgePerformances;

        this.refreshChart = function() {
            this.PERFORMANCES.series = [];
            this.PERFORMANCES.series.push({
                data: Service.bridgePerformances.data
            })
        };

        this.options = {
            type: 'bar'
        };

        this.swapChartType = function() {
            if (this.PERFORMANCES.options.chart.type === 'line') {
                this.PERFORMANCES.options.chart.type = 'bar'
            } else {
                this.PERFORMANCES.options.chart.type = 'line'
            }
        };

        this.PERFORMANCES = {
            options: {
                chart: {
                    type: 'bar',
                    style: {
                	color: '#000000',
                	fontSize:'18px'
                    }
                }
            },
            xAxis: {
                categories: ['A', 'B', 'C', 'D'],
                labels: {
                    style: {
                	color: '#000000',
                        fontSize:'24px'
                    }
                }
            },
            yAxis: {
                labels: {
                    style: {
                	color: '#000000',
                        fontSize:'24px'
                    }
                }
            },
            plotOptions: {
                bar: {
                    dataLabels: {
                        enabled: true,
                	color: '#000000',
                        fontSize:'24px'
                    }
                }
            },
            series: Service.bridgePerformances.data,
            title: {
                text: '<h1>Avaliação</h1>',
                style: {
            	    color: '#000000',
                    fontSize:'18px'
                }
            },
            subtitle: {
        	text: '<h3>Questões e Respostas / Posições Atuais</h3>',
                style: {
            	    fontSize:'16px'
                }
            }
        };

        $scope.$watch(function() {
            return Service.bridgePerformances;
        }, function(newVal, oldVal) {
            this.bridgePerformances = newVal;
        });

    });

    /**
     * Teacher Panel Controller //
     */
    app.controller('TeacherPanelController', function($scope, $http, $rootScope,
        $timeout, Service, Colors) {

        this.gametitle = decodeURIComponent(getUrlParameter('title')).replaceAll("+", " ");
        this.gamekey = getUrlParameter('key');

        $scope.questionsItems = [];
        $scope.answersItems = [];

        var counterSynchro = {
            countdown: 'NACK'
        };

        var url = {
            id: getUrlParameter('id'),
            uids: getUrlParameter('uids'),
            qids: getUrlParameter('qids'),
            key: getUrlParameter('key'),
            cfgid: getUrlParameter('cfgid'),
            qgid: getUrlParameter('qgid'),
            courseid: getUrlParameter('courseid'),
            time: getUrlParameter('time'),
            tid: getUrlParameter('tid'),
            dtmatch: getUrlParameter('dtmatch')
        };

        this.getAllQuestions = function() {

            var geturl = BASEURL +
                "/moodle/mod/quizgame/views/questions.php?" +
                "id=" + url.id +
                "&qids=" + url.qids +
                "&cfgid=" + url.cfgid;

            $http({
                method: 'GET',
                url: geturl
            }).then(function successCallback(response) {
                angular.forEach(response.data, function(
                    value, key) {
                    $scope.questionsItems.push({
                        id: value.id,
                        qtext: value.questiontext,
                        timer: url.time
                    });
                });
            }.bind(this), function errorCallback(
                response) {
                console.log(response);
            });
        };

        this.getAllAnswers = function() {

            var geturl = BASEURL +
                "/moodle/mod/quizgame/views/answers.php?" +
                "id=" + url.id +
                "&qids=" + url.qids +
                "&cfgid=" + url.cfgid;

            $http({
                method: 'GET',
                url: geturl
            }).then(function successCallback(response) {
                angular.forEach(response.data, function(
                    value, key) {
                    var answers = [];
                    angular.forEach(value,
                        function(v, k) {
                            answers.push(
                                v
                            );
                        });
                    $scope.answersItems.push(answers);
                });
            }.bind(this), function errorCallback(
                response) {
                console.log(response);
            });
        };

        this.getAllQuestions();
        this.getAllAnswers();

        this.panelitems = $scope.questionsItems;

        this.tab = 0;
        this.changetab = 0;

        this.offset = 1;

        $scope.canAdvance = {
            nextState: 'NACK',
        };

        $scope.cansubmitFormFeedPerfs = {
 	       check: true
        };

        this.atLeastOneReady = 0;
        this.finalState = false;
        $scope.canSavePerformances = true;

        $scope.counter = 0;
        var stopped;

        this.blocks = 4;

        this.getActualTiming = function(t) {
            this.questionTime = t;
        };

        $scope.countdown = function() {
            $scope.stop();

            stopped = $timeout(function() {
                if ($scope.counter > 0) {
                    $scope.counter--;
                    $scope.countdown();
                } else {
                    $scope.stop();
                    $scope.timesup();
                }
            }, 1000);

        };

        $scope.stop = function() {
            $timeout.cancel(stopped);
        };

        this.setNewCountDown = function(t) {
            $scope.counter = t;
            $scope.stop();
        };

        $scope.configuration = {};
        $scope.messages = {};
        $scope.errors = {};

        $scope.engageSharedDevice = function() {
            Service.bridgePerformances = $scope.bridgePerformances;
        }

        $scope.feedPerformances = function(where) {

            $scope.master = {
                key: url.key,
                form: 'perfs',
                qaids: $scope.answersItems[where][0].id + '|' +
                       $scope.answersItems[where][1].id + '|' +
                       $scope.answersItems[where][2].id + '|' +
                       $scope.answersItems[where][3].id,
                qid: $scope.answersItems[where][0].question,
                where: where,
                qnt: $scope.questionsItems.length
            };

            $scope.submitFormFeedPerformances();
            $scope.engageSharedDevice();

        };

        $scope.submitFormFeedPerformances = function() {
            var posturl = BASEURL +
                "/moodle/mod/quizgame/restforms.php?" +
                "id=" + url.id +
                "&qgid=" + url.qgid +
                "&courseid=" + url.courseid +
                "&form=" + $scope.master.form +
                "&qaids=" + $scope.master.qaids +
                "&qid=" + $scope.master.qid +
                "&key=" + $scope.master.key +
                "&qnt=" + $scope.master.qnt;

            console.log(posturl);

            $http({
                method: 'POST',
                url: posturl,
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).then(function successCallback(callbackInformation) {
                   var data = callbackInformation.data;
                   console.log(data);

                   var rank = [];
                   angular.forEach(data.rank, function(v, k) {
                       if (v.position >= 1 && v.position <= 3) {
                           rank.push({
                               name: {
                                   firstname: v.name.firstname,
                                   lastname: v.name.lastname
                               },
                               position: v.position,
                               bonus: 1 - (v.position / 4)
                           });
                       }
                   });

                   $scope.bridgePerformances = {
                       data: [
		           {name: '<label>A</label>', y: data.count[$scope.answersItems[$scope.master.where][0].id], color: Colors.COLORSBLIND[0]},
		           {name: '<label>B</label>', y: data.count[$scope.answersItems[$scope.master.where][1].id], color: Colors.COLORSBLIND[1]},
		           {name: '<label>C</label>', y: data.count[$scope.answersItems[$scope.master.where][2].id], color: Colors.COLORSBLIND[2]},
		           {name: '<label>D</label>', y: data.count[$scope.answersItems[$scope.master.where][3].id], color: Colors.COLORSBLIND[3]}
		       ],
		       rank : rank,
		       correct: Colors.LETTERS[data.rightindex]
		   };

		   // console.log(data);
		   console.log($scope.bridgePerformances);
		   Service.bridgePerformances = $scope.bridgePerformances;
	           $rootScope.$emit("CallParentMethod", {});

            }, function errorCallback(response) {
                console.log(response)
            }).bind(this)
        };

        $scope.timesup = function() {
            $scope.master = {
                key: url.key,
                form: 'timesup'
            };
            $scope.submitFormTimesup();
        };

        $scope.submitFormTimesup = function() {
            var posturl = BASEURL +
                "/moodle/mod/quizgame/sync.php?" +
                "id=" + url.id +
                "&qgid=" + url.qgid +
                "&courseid=" + url.courseid +
                "&form=" + $scope.master.form +
                "&key=" + $scope.master.key;

            console.log(posturl);

            $http({
                method: 'POST',
                url: posturl,
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).then(function successCallback(callbackInformation) {
                $scope.messages = {
                    response: callbackInformation.data.message,
                }
            }, function errorCallback(response) {
                console.log(response)
            })
        };

        this.setstate = function() {
            $scope.master = {
                tab: this.tab,
                state: this.panelitems[this.tab].id,
                key: url.key,
                form: 'setstate'
            };
            console.log($scope.master);
            this.submitFormState();
            $scope.master = {};
            this.atLeastOneReady = 1;
        };

        this.submitFormState = function() {
            var posturl = BASEURL +
                "/moodle/mod/quizgame/sync.php?" +
                "id=" + url.id +
                "&qgid=" + url.qgid +
                "&courseid=" + url.courseid +
                "&form=" + $scope.master.form +
                "&state=" + $scope.master.state +
                "&key=" + $scope.master.key +
                "&tab=" + $scope.master.tab;

            console.log(posturl);

            $http({
                method: 'POST',
                url: posturl,
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).then(function successCallback(callbackInformation) {
                var data = callbackInformation.data;

                if (data.success) {
                    $scope.messages = {
                        response: data.message,
                    };

                    $scope.canAdvance = {
                        nextState: data.countdown,
                    };

                } else {
                    $scope.messages = {
                        error: data.error
                    }
                    console.log(data);
                }
            }, function errorCallback(response) {
                console.log(response)
            })
        };

        this.adjustTimer = function() {
            this.setNewCountDown(this.panelitems[this.tab].timer);
        };

        this.selectTab = function(tabId) {
            this.adjustTimer();
            this.tab = parseInt(tabId, 10);
        };

        this.isSelected = function(tabId) {
            return this.tab === tabId;
        };

        this.goNext = function(tabId) {

            if (this.tab + 1 == Object.keys(this.panelitems).length) {
        	this.finalState = true;
            }

            console.log($scope.canSavePerformances, $scope.canAdvance.warning);

            if ($scope.canAdvance.nextState == 'ACK') {

                if (this.atLeastOneReady == 1 && !this.finalState) {

                    // Adjust the timer to prepare to a new counting down
                    this.adjustTimer();

                    // Adjust tab to go to a next question
                    this.tab = (this.tab + 1) % Object.keys(this.panelitems).length;
                    this.selectTab(this.tab);

                    // Lock state will be unlocked by another component
                    this.atLeastOneReady = 0;
                    $scope.canAdvance.nextState = 'NACK';

                }

            }

            return this.tab;
        };

        /*this.answer = function(answer) {
            return answer;
        };*/

        var w = screen.width;
        var h = screen.height;
        var r = gcd(w, h);

        $scope.changeRowHeightGT = function() {
            if (h < w) {
                return '3:1';
            } else {
                return '1:3';
            }
        }

        $scope.changeRowHeight = function() {
            if (h < w) {
                return '5:3';
            } else {
                return '3:5';
            }
        }

        this.colorTiles = (function() {
            var tiles = [];
            for (var i = 0; i < 4; i++) {
                tiles.push({
                    size: 100,
                    letter: Colors.LETTERS[i],
                    icon: Colors.ICONS[i],
                    colorblind: Colors.COLORSBLIND[
                        i],
                    effective: Colors.EFFECTIVE[i],
                    span: {
                        md: {
                            col: (w / r) / (h / r),
                            row: (w / r) / (h / r)
                        },
                        sm: {
                            col: (h / r) / (w / r),
                            row: (h / r) / (w / r)
                        },
                        xs: {
                            col: (w / r) / (h / r),
                            row: (w / r) / (h / r)
                        }
                    }
                });
            }
            return tiles;
        })();
    });

    app.directive('myCurrentTime', ['$interval', 'dateFilter',
        function($interval, dateFilter) {
            // return the directive link function. (compile function not needed)
            return function(scope, element, attrs) {
                var format, // date format
                    stopTime; // so that we can cancel the time updates

                // used to update the UI
                function updateTime() {
                    element.text(dateFilter(new Date(), format));
                }

                // watch the expression, and update the UI on change.
                scope.$watch(attrs.myCurrentTime, function(
                    value) {
                    format = value;
                    updateTime();
                });

                stopTime = $interval(updateTime, 1000);

                // listen on DOM destroy (removal) event, and cancel the next UI update
                // to prevent updating time after the DOM element was removed.
                element.on('$destroy', function() {
                    $interval.cancel(stopTime);
                });
            }
        }
    ]);

    app.controller('TemplateController', function($scope, $http) {});

    app.controller('AppCtrl', ['$scope', '$rootScope', '$mdBottomSheet', '$mdSidenav', '$mdDialog',
        function($scope, $rootScope, $mdBottomSheet, $mdSidenav, $mdDialog) {

            var url = {
                id: getUrlParameter('id'),
                qgid: getUrlParameter('qgid'),
                courseid: getUrlParameter('courseid')
            };

	    $scope.gobacktostudentarea = BASEURL +
                   "/moodle/mod/quizgame/views/student.php?" +
                   "id=" + url.id +
                   "&qgid=" + url.qgid +
                   "&courseid=" + url.courseid;

	    $scope.fs = {
                status: false,
                buttonclicked: false
	    };

	    $(window).keypress(function(event) {
	        var code = event.keyCode || event.which;
	        if (code == 122) {
	            setTimeout(function() {
	    	        $scope.fs.status = checkWH()
	            }, 1000);
	            if ($scope.fs.buttonclicked == true) {
		        $scope.fs.buttonclicked = false;
	    	        $scope.fs.status = false;
	            } else {
		        $scope.fs.buttonclicked = true;
	            }
	        }
	    });

            $scope.toggleSidenav = function(menuId) {
                $mdSidenav(menuId).toggle();
            };

            $scope.alert = '';

            $scope.waitingPage = function(ev) {
                $mdDialog.show({
                        controller: StartQuizGameController,
                        template: '<md-dialog class="startquizgame" layout="row" layout-align="center center" ng-cloak>'
                                  + '<div layout="column">'
                                  + '<div flex><md-button style="margin:25px;" ng-click="startgame()" class="md-raised md-accent">Tornar Jogo Visível</md-button></div>'
                                  + '<div flex><md-button style="margin:25px;" ng-click="hide()" class="md-raised md-accent">Encerrar Esse Diálogo</md-button></div>'
                                  + '<div flex style="margin:25px; text-align:justify;">'
                                  + '<h1>Olá jogadores, preparem-se para praticar questões com seus colegas. Esperem os comandos do professor, ele vai habilitar o jogo para que vocês possam entrar. Memorizem o controle que deverá estár aparecendo no dispositível móvel de vocês. Tenham um bom jogo e aproveite para estudar.</h1>'
                                  + '</md-dialog>'
                                  + '</div>'
                                  + '</div>',
                        targetEvent: ev
                }).then(function(answer) {
                        $scope.alert = 'You said the information.';
                }, function() {
                        $scope.alert = 'You cancelled the dialog.';
                });
            };

            $scope.showFullScreenOption = function(ev) {
                $mdDialog.show({
                    controller: DialogController,
                    template: '<md-dialog>Text</md-dialog>',
                    targetEvent: ev,
                }).then(function(answer) {
                    $scope.alert = 'You said the information was "' + answer + '".';
                }, function() {
                    $scope.alert = 'You cancelled the dialog.';
                });
            };

            $rootScope.$on("CallParentMethod", function(){
                $scope.showPerformances();
            });

            $scope.showPerformances = function(ev) {
                $mdDialog.show({
                    controller: DialogController,
                    template: '<md-card-content layout="row" layout-align="center center" ng-cloak style="width:1250px; height:1250px;">'
                              +    '<md-card ng-controller="AnswersPerformancesController as apc">'
                              +        '<div flex ng-init="apc.refreshChart()"><h2>Resposta Certa: {{apc.bridgePerformances.correct}}</h3></div>'
                              +        '<highchart config="apc.PERFORMANCES"></highchart>'
                              +        '<div flex>'
                              +            '<div layout="column" layout-align="left left">'
                              +                '<div ng-repeat="podium in apc.bridgePerformances.rank">'
                              +                    '<div flex><h3>{{ podium.position }}º {{podium.name.firstname}} {{podium.name.lastname}} - {{ podium.bonus }} extra points.</h3></div>'
                              +                '</div>'
                              +            '</div>'
                              +        '</div>'
                              +        '<div flex>'
                              +            '<div layout="row" layout-align="center center" class="md-dialog-actions" layout="column">'
                              +                '<span flex></span>'
                              +                '<md-button ng-click="answer(\'not useful\')">Fechar</md-button>'
                              +            '</div>'
                              +        '</div>'
                              +    '</md-card>'
                              + '</md-card-content>',
                    targetEvent: ev
                })
                .then(function(answer) {
                    $scope.alert = 'You said the information.';
                }, function() {
                    $scope.alert = 'You cancelled the dialog.';
                });
            };

            $scope.showListBottomSheet = function($event) {
                $scope.alert = '';
                $mdBottomSheet.show({
                    template: '<md-dialog>Text</md-dialog>',
                    controller: 'ListBottomSheetCtrl',
                    targetEvent: $event
                }).then(function(clickedItem) {
                    $scope.alert = clickedItem.name +
                        ' clicked!';
                });
            };
        }
    ]);

    app.controller('ListBottomSheetCtrl', function($scope, $mdBottomSheet) {
        $scope.items = [{
            name: 'Back to Moodle',
            icon: 'arrow_back'
        }];

        $scope.listItemClick = function($index) {
            var clickedItem = $scope.items[$index];
            $mdBottomSheet.hide(clickedItem);
        };

    });

    function DialogController($scope, $mdDialog) {
        $scope.hide = function() {
            $mdDialog.hide();
        };
        $scope.cancel = function() {
            $mdDialog.cancel();
        };
        $scope.answer = function(answer) {
            $mdDialog.hide(answer);
        };
    };

    
    function StartQuizGameController ($scope, $http, $mdDialog) {

        var url = {
            id: getUrlParameter('id'),
            qids: getUrlParameter('qids'),
            key: getUrlParameter('key'),
            cfgid: getUrlParameter('cfgid'),
            courseid: getUrlParameter('courseid'),
            uid: getUrlParameter('uid')
        };

        $scope.hide = function() {
            $mdDialog.hide();
        };

	$scope.startgame = function() {
            var posturl = BASEURL +
                "/moodle/mod/quizgame/sync.php?" +
                "id=" + url.id +
                "&qgid=" + url.qgid +
                "&courseid=" + url.courseid +
                "&key=" + url.key +
                "&form=" + 'startgame';

            console.log(posturl);

            $http({
                method: 'POST',
                url: posturl,
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).then(function successCallback(callbackInformation) {
                $scope.messages = {
                    response: data.message,
                    warning: data.warning
                }
            }, function errorCallback(response) {
                console.log(response)
            })
	};
    };

    app.config(function($mdThemingProvider) {
        var customBlueMap = $mdThemingProvider.extendPalette('light-blue', {
                'contrastDefaultColor': 'light',
                'contrastDarkColors': ['50'],
                '50': 'ffffff'
            });
        $mdThemingProvider.definePalette('customBlue', customBlueMap);
        $mdThemingProvider.theme('default')
            .primaryPalette('customBlue', {
                'default': '500',
                'hue-1': '50'
            }).accentPalette('pink');
        $mdThemingProvider.theme('input', 'default').primaryPalette('grey')
    });

})();