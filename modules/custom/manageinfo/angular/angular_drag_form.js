/**
 *@file
 * drag and drop Evaluation form to edit
 */
var pathArg = drupalSettings.path.currentPath.split('/');
var jsonFileUrl = drupalSettings.path.baseUrl + 'manageinfo/termdrag/json/' + pathArg[3];

var pageInfoBaseControllers = angular.module('pageInfoBase', ['ngResource', 'ngMaterial', 'dragularModule']);
pageInfoBaseControllers.controller('MildderDragController', ['$scope',  '$http', '$element', '$timeout','$mdDialog', '$mdMedia', '$filter', '$sce', 'dragularService',
  function($scope, $http, $element, $timeout, $mdDialog, $mdMedia, $filter, $sce, dragularService) {

    var spinner = new Spinner(spinOptions).spin(document.getElementById('center'));
    angular.element(document).ready(function() {
      $http({
        method: 'GET',
        url: jsonFileUrl
      }).then(function(response) {
        $scope.formDragJson = response.data;
        dragularService($element.children().eq(0).children(), {containersModel: $scope.formDragJson.selectedQuestions});
        jQuery('#form-name').val($scope.formDragJson.formName);
        spinner.stop();
      },function(error) {
        // if error occurs
        spinner.stop();
      });
    });

    $scope.addQuestion = function(index) {
      $scope.formDragJson.selectedQuestions.push({});
    }

    $scope.removeQuestion = function(index) {
      if (index > -1) {
        $scope.formDragJson.selectedQuestions.splice(index, 1);
      }
    }

    // get rest Token
    jQuery.get(drupalSettings.path.baseUrl + 'rest/session/token').done(function (data) {
      $scope.csrfToken = data;
    });



    $scope.submit = function() {
      var postUrl = drupalSettings.path.baseUrl + 'taxonomy/term/' + pathArg[3];

      var redirectUrl = drupalSettings.path.baseUrl + 'manageinfo/evaluationform/list/all';

      $scope.formDragJson.formName = jQuery('#form-name').val();
      $scope.submitQuestions = [];

      angular.forEach($scope.formDragJson.selectedQuestions, function(value) {
        $scope.submitQuestions.push({'target_id': value.defaultValue});
      });

      var formDragJson = {
        "vid": [{ "target_id": 'evaluationform' }],
        "name": [{ "value": $scope.formDragJson.formName }],
        "field_evaluationform_questionset": $scope.submitQuestions,
      };

      $http({
        method  : 'PATCH',   // GET, POST, PATCH, DELETE
        url     : postUrl,
        data    : formDragJson,
        headers : {'Content-Type': 'application/json', 'X-CSRF-Token': $scope.csrfToken},
      }).then(function(response) {
        console.log('Success');

        window.location.replace(redirectUrl);
      },function (error) {

      });
    }

    $scope.deleteAlertBox = function($event) {
      var parentEl = angular.element(document.body);
      $mdDialog.show({
        clickOutsideToClose: true,
        parent: parentEl,
        targetEvent: $event,
        scope: $scope,
        preserveScope: true,
        template:
        '<md-dialog flex="35">' +
          '  <md-dialog-content class="padding-24">Are you sure you want to delete this Evaluation Form?</md-dialog-content>' +
          '  <md-dialog-actions>' +
          '    <md-button ng-click="delete()" class="md-primary pageinfo-btn-saved">' +
          '      Yes' +
          '    </md-button>' +
          '    <md-button ng-click="closeDialog()" class="md-primary">' +
          '      No' +
          '    </md-button>' +
          '  </md-dialog-actions>' +
        '</md-dialog>',
        controller: function DialogController($scope, $mdDialog) {
          $scope.closeDialog = function() {
            $mdDialog.hide();
          }
        }
      });
    }

    /*
     * delete form function
     */
    $scope.delete = function() {
      console.log("deleted");
      var postUrl = drupalSettings.path.baseUrl + "taxonomy/term/" + pathArg[3];
      var redirectUrl = drupalSettings.path.baseUrl + 'manageinfo/evaluationform/list/all';

      $http({
        method  : 'DELETE',   // GET, POST, PATCH, DELETE
        url     : postUrl,
        headers : {'Content-Type': 'application/json', 'X-CSRF-Token': $scope.csrfToken},
      }).then(function(response) {
        $scope.isLoading = false;
        console.log('Success');

        window.location.replace(redirectUrl);
      },function (error) {
        $scope.isLoading = false;
        // $scope.submitUnsuccessfulAlert();

        console.log('Form not submit successfully');
      });
    }
  }
]);
