(function() {

    var app = angular.module('tforms', [ 'ngRoute' ]);

    var BASEURL = window.location.origin;
    var HOST = window.location.host;
    var PATHARRAY = window.location.pathname.split( '/' );

    function getUrlParameter(sParam) {
	var sPageURL = decodeURIComponent(window.location.search.substring(1)), sURLVariables = sPageURL.split('&'), sParameterName, i;

	for (i = 0; i < sURLVariables.length; i++) {
	    sParameterName = sURLVariables[i].split('=');

	    if (sParameterName[0] === sParam) {
		return sParameterName[1] === undefined ? true
			: sParameterName[1];
	    }
	}
    };

    app.controller('TeacherFormController', function($scope, $http) {
    });

    app.controller('CategoriesFormController', function($scope, $http) {
	$scope.master = {};
	$scope.value = 0;

	var url = {
            id: getUrlParameter('id'),
            qgid: getUrlParameter('qgid'),
            courseid: getUrlParameter('courseid'),
            form: getUrlParameter('form')
	};
	console.log(url);

	$scope.configuration = {};
	$scope.messages = {};
	$scope.errors = {};

	this.send = function(sendkey, sendqids) {
	    $scope.master = {
                sendkey: sendkey,
                sendqids: sendqids
	    }

	    console.log($scope.master);
	    this.rest = this.submitForm();

	    $scope.master = {};
	    console.log($scope.master);

	};

	this.submitForm = function() {
	    var posturl = BASEURL + "/moodle/mod/quizgame/restforms.php?"
		        + "id="        + url.id
		        + "&qgid="     + url.qgid
		        + "&courseid=" + url.courseid
		        + "&form="     + url.form
		        + '&sendkey='  + $scope.master.sendkey
		        + '&sendqids=' + $scope.master.sendqids;

	    console.log(posturl);

	    $http({
		method : 'POST',
		url : posturl,
		headers : { 'Content-Type' : 'application/x-www-form-urlencoded' }
	    }).success(function(data) {
		if (!data.success) {
		    $scope.messages = {
			response : data.message,
		    }
		    console.log(data);
		} else {
		    $scope.messages = {
			error : data.error
		    }
		    console.log(data);
		}
	    }).error(function(errors) {
		console.log(errors);
	    });
	};

    });

    app.controller('StudentFormController', function($scope, $http) {
	$scope.master = {};

	var url = {
            id: getUrlParameter('id'),
            qgid: getUrlParameter('qgid'),
            courseid: getUrlParameter('courseid'),
            form: getUrlParameter('form')
	};
	console.log(url);

	$scope.configuration = {};
	$scope.messages = {};
	$scope.errors = {};

	this.send = function(name, descr, time, cfgid, dtmatch, key, plid, qids) {
	    $scope.master = {
                name    : name    ,
                descr   : descr   ,
                time    : time    ,
                cfgid   : cfgid   ,
                dtmatch : dtmatch ,
                key     : key     ,
                plid    : plid    ,
                qids    : qids
	    };

	    console.log($scope.master);

	    $scope.master = {};
	    console.log($scope.master);

	};

	this.submitForm = function() {
	    var posturl = BASEURL + "/moodle/mod/quizgame/play.php?"
		        + "id="        + url.id
		        + "&qgid="     + url.qgid
		        + "&courseid=" + url.courseid
		        + "&form="     + url.form
		        + '&size='     + $scope.master.size
		        + '&cfgid='    + $scope.master.cfgid;

	    $http({
		method : 'POST',
		url : posturl,
		headers : { 'Content-Type' : 'application/x-www-form-urlencoded' }
	    }).success(function(data) {
		if (!data.success) {
		    $scope.messages = {
			response : data.message,
		    }
		    console.log(data);
		} else {
		    $scope.messages = {
			error : data.error
		    }
		    console.log(data);
		}
	    }).error(function(errors) {
		console.log(errors);
	    });
	};

    });

    app.controller('FormQuestionController', function($scope, $http) {
	$scope.master = {};

	var url = {
            id: getUrlParameter('id'),
            qgid: getUrlParameter('qgid'),
            courseid: getUrlParameter('courseid'),
            form: getUrlParameter('form')
	};

	console.log(BASEURL, HOST, PATHARRAY);

	$scope.configuration = {};
	$scope.messages = {};
	$scope.errors = {};

	this.send = function(catid, qname, qtext, answer1, answer2, answer3, answer4, fraction1, fraction2, fraction3, fraction4) {
	    $scope.master = {
                qname     : encodeURIComponent(qname),
                qtext     : encodeURIComponent(qtext),
                answers   : encodeURIComponent(answer1) + '|'
                          + encodeURIComponent(answer2) + '|'
                          + encodeURIComponent(answer3) + '|'
                          + encodeURIComponent(answer4),
                fractions : (fraction1 / 100) + '|'
                          + (fraction2 / 100) + '|'
                          + (fraction3 / 100) + '|'
                          + (fraction4 / 100),
                catid     : catid
            };

	    console.log($scope.master);
	    this.submitForm();

	    $scope.master = {};
	    console.log($scope.master);

	};

	this.submitForm = function() {
	    var posturl = BASEURL + "/moodle/mod/quizgame/restforms.php?"
		        + "id="         + url.id
		        + "&qgid="      + url.qgid
		        + "&courseid="  + url.courseid
		        + "&form="      + url.form
		        + '&qname='     + $scope.master.qname
		        + '&qtext='     + $scope.master.qtext
	                + '&answers='   + $scope.master.answers
	                + '&fractions=' + $scope.master.fractions
	                + '&catid='     + $scope.master.catid;

	    console.log(posturl);

	    $http({
		method : 'POST',
		url : posturl,
		headers : { 'Content-Type' : 'application/x-www-form-urlencoded' }
	    }).success(function(data) {
		if (!data.success) {
		    $scope.messages = {
			response : data.message,
		    }
		    console.log(data);
		} else {
		    $scope.messages = {
			error : data.error
		    }
		    console.log(data);
		}
	    }).error(function(errors) {
		console.log(errors);
	    });
	};

    });

    app.controller('FormKeyController', function($scope, $http) {
	$scope.master = {};

	var url = {
            id: getUrlParameter('id'),
            qgid: getUrlParameter('qgid'),
            courseid: getUrlParameter('courseid'),
            form: getUrlParameter('form')
	};
	console.log(url);

	$scope.configuration = {};
	$scope.messages = {};
	$scope.errors = {};

	this.send = function(size, cfgid) {
	    $scope.master = {
                size: size,
                cfgid: cfgid
	    }

	    console.log($scope.master);
	    this.submitForm();

	    $scope.master = {};
	    console.log($scope.master);

	};

	this.submitForm = function() {
	    var posturl = BASEURL + "/moodle/mod/quizgame/restforms.php?"
		        + "id="        + url.id
		        + "&qgid="     + url.qgid
		        + "&courseid=" + url.courseid
		        + "&form="     + url.form
		        + '&size='     + $scope.master.size
		        + '&cfgid='    + $scope.master.cfgid;

	    console.log(posturl);

	    $http({
		method : 'POST',
		url : posturl,
		headers : { 'Content-Type' : 'application/x-www-form-urlencoded' }
	    }).success(function(data) {
		if (!data.success) {
		    $scope.messages = {
			response : data.message,
		    }
		    console.log(data);
		} else {
		    $scope.messages = {
			error : data.error
		    }
		    console.log(data);
		}
	    }).error(function(errors) {
		console.log(errors);
	    });
	};

    });

    app.controller('FormConfigurationController', function($scope, $http) {
	$scope.master = {};

	var url = {
            id: getUrlParameter('id'),
            qgid: getUrlParameter('qgid'),
            courseid: getUrlParameter('courseid'),
            form: getUrlParameter('form')
	};
	console.log(url);

	this.send = function(name, description, time) {

	    $scope.master = {
		name: name,
		description: description,
		time: time
	    }

	    console.log($scope.master);
	    this.submitForm();

	    $scope.master = {};
	    console.log($scope.master);
	};

	$scope.configuration = {};
	$scope.messages = {};
	$scope.errors = {};

	this.submitForm = function() {
	    var posturl = BASEURL + "/moodle/mod/quizgame/restforms.php?"
		        + "id="        + url.id
		        + "&qgid="     + url.qgid
		        + "&courseid=" + url.courseid
		        + "&form="     + url.form
		        + '&name='     + $scope.master.name
		        + '&descr='    + $scope.master.description
		        + '&time='     + $scope.master.time;

	    console.log(posturl);

	    $http({
		method : 'POST',
		url : posturl,
		headers : { 'Content-Type' : 'application/x-www-form-urlencoded' }
	    }).success(function(data) {
		if (!data.success) {
		    $scope.messages = {
			response : data.message,
		    }
		    console.log(data);
		} else {
		    $scope.messages = {
			error : data.error
		    }
		    console.log(data);
		}
	    }).error(function(errors) {
		console.log(errors);
	    });
	};

    });

    app.controller('FormPackageController', function($scope, $http) {
	$scope.master = {};

	var url = {
            id: getUrlParameter('id'),
            qgid: getUrlParameter('qgid'),
            courseid: getUrlParameter('courseid'),
            form: getUrlParameter('form')
	};

	console.log(url);

	this.send = function(cfgid, qids) {

	    $scope.master = {
                cfgid: cfgid,
                qids: qids.join('|')
	    }

	    console.log($scope.master);
	    this.submitForm();

	    $scope.master = {};
	    console.log($scope.master);

	};

	$scope.configuration = {};
	$scope.messages = {};
	$scope.errors = {};

	this.submitForm = function() {
	    var posturl = BASEURL + "/moodle/mod/quizgame/restforms.php?"
		        + "id="        + url.id
		        + "&qgid="     + url.qgid
		        + "&courseid=" + url.courseid
		        + "&form="     + url.form
		        + '&cfgid='    + $scope.master.cfgid
		        + '&qids='     + $scope.master.qids;

	    console.log(posturl);

	    $http({
		method : 'POST',
		url : posturl,
		headers : { 'Content-Type' : 'application/x-www-form-urlencoded' }
	    }).success(function(data) {
		if (!data.success) {
		    $scope.messages = {
			response : data.message,
		    }
		    console.log(data);
		} else {
		    $scope.messages = {
			error : data.error
		    }
		    console.log(data);
		}
	    }).error(function(errors) {
		console.log(errors);
	    });
	};

    });

    app.controller('TeacherViewController', function($scope, $http) {
	$scope.master = {};

	var url = {
            id: getUrlParameter('id'),
            qgid: getUrlParameter('qgid'),
            courseid: getUrlParameter('courseid'),
            form: getUrlParameter('form')
	};
	console.log(url);

	this.send = function(cfgid, qids) {

	    $scope.master = {
	    }

	    console.log($scope.master);

	    $scope.master = {};
	    console.log($scope.master);

	};

	$scope.configuration = {};
	$scope.messages = {};
	$scope.errors = {};

	this.submitForm = function() {
	    var posturl = BASEURL + "/moodle/mod/quizgame/restforms.php?"
		        + "id="        + url.id
		        + "&qgid="     + url.qgid
		        + "&courseid=" + url.courseid
		        + "&form="     + url.form
		        + '&cfgid='    + $scope.master.cfgid
		        + '&qids='     + $scope.master.qids;

	    $http({
		method : 'POST',
		url : posturl,
		headers : { 'Content-Type' : 'application/x-www-form-urlencoded' }
	    }).success(function(data) {
		if (!data.success) {
		    $scope.messages = {
			response : data.message,
		    }
		    console.log(data);
		} else {
		    $scope.messages = {
			error : data.error
		    }
		    console.log(data);
		}
	    }).error(function(errors) {
		console.log(errors);
	    });
	};

    });

})();
