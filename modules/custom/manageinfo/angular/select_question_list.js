
var jsonFileUrl = drupalSettings.path.baseUrl + 'manageinfo/selectquestion/json/all/22/33';
// var jsonFileUrl = drupalSettings.path.baseUrl + 'modules/custom/manageinfo/angular/question_library.json';

var pageInfoBaseControllers = angular.module('pageInfoBase', ['ngMaterial', 'datatables', 'ngResource', 'datatables.buttons', 'ngSanitize']);
pageInfoBaseControllers.controller('QuestionLibraryController', ['$scope', '$http', '$timeout', '$q', '$log', '$filter','$mdDialog', '$element',
  function($scope, $http, $timeout, $resource, DTOptionsBuilder, DTColumnDefBuilder, DTDefaultOptions) {
    $scope.evalutionFormTitle = "";
    $scope.selectedQuestions = [];

    var spinner = new Spinner(spinOptions).spin(document.getElementById('center'));
    angular.element(document).ready(function() {
      $http({
        method: 'GET',
        url: jsonFileUrl
      }).then(function(response) {
        $scope.questionLibraryData = response.data;
        spinner.stop();

        $timeout(function() {
          var blockHeight = jQuery('.main-container').find('.row').height();
          var footerHeight = jQuery('footer').height();
          jQuery('.sidebar-nav').css("height", 1180);
        });
      },function(error) {
        // if error occurs
      });
    });

    $scope.dtOptions = {
      paginationType: 'full_numbers',
      bInfo: false,
      bSort: false,
      iDisplayLength: 10,
      aoColumnDefs: [
        { sWidth:'35%', bSortable: true, aTargets: [ 0 ] },
        { sWidth:'10%', bSortable: false, aTargets: [ '_all' ] }
      ],
      info: true,
      paging: true,
      language: {
        "searchPlaceholder": "SEARCH",
        "sSearch": "",
        "oPaginate": {
          "sFirst": "",
          "sLast": "",
          "sNext": "<span class='fa fa-caret-right color-00a9e0'></span>",
          "sPrevious": "<span class='fa fa-caret-left color-00a9e0'></span>",
        },
        "sLengthMenu": ' <select>' + '<option value="10">SHOW 10</option>' + '<option value="20">SHOW 20</option>' + '<option value="30">SHOW 30</option>' + '<option value="40">SHOW 40</option>' + '<option value="50">SHOW 50</option>' + '<option value="-1">SHOW All</option>' + '</select> '
      }
    };

    $scope.getQuestionTid = function(question) {
      $scope.selectedQuestions.push(question);
    }

    $scope.removeQuestion = function(question) {
      var index = $scope.selectedQuestions.indexOf(question);
      if (index > -1) {
        $scope.selectedQuestions.splice(index, 1);
      }
    }

    jQuery.get(drupalSettings.path.baseUrl + 'rest/session/token').done(function (data) {
      $scope.csrfToken = data;
    });

    $scope.submit = function() {
      $scope.submitQuestions = [];
      angular.forEach($scope.selectedQuestions, function(value, key) {
        $scope.submitQuestions.push({'target_id':value[5]});
      });

      var postUrl = drupalSettings.path.baseUrl + 'entity/taxonomy_term';
      var redirectUrl = drupalSettings.path.baseUrl + 'manageinfo/evaluationform/list/all';

      var postTermJson = {
        "vid": [{ "target_id": "evaluationform" }],
        "name": [{ "value": $scope.evalutionFormTitle }],
        "field_evaluationform_questionset": $scope.submitQuestions,
        // "field_evaluationform_questionset2": [
        //   { "target_id": 2633 },
        //   { "target_id": 2634 }
        // ],
      };

      console.log(postTermJson);

      $http({
        method  : 'POST',   // GET, POST, PATCH, DELETE
        url     : postUrl,
        data    : postTermJson,
        headers : {'Content-Type': 'application/json', 'X-CSRF-Token': $scope.csrfToken},
      }).then(function(response) {
        console.log('Success');

        window.location.replace(redirectUrl);
      },function (error) {

      });
    }
  }
]);
