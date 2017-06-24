var imagePath = drupalSettings.path.baseUrl + 'modules/custom/dashpage/images';

var pageInfoBaseControllers = angular.module('MildderInfoRegister', ['ngResource', 'ngMaterial', 'ngMessages']);
pageInfoBaseControllers.controller('MildderInfoRegisterController', function($scope, $http, $element, $sce, $mdDialog) {
  $scope.user = {
    name: '',
    pass: '',
    mail: '',
    'field_user_city': '',
    'field_user_speciality': ''
  };

  $scope.specialityOptions = [
    {
      termTid: 1,
      termName: "Speciality 1"
    },
    {
      termTid: 1,
      termName: "Speciality "
    }
  ];

  var postUrl = drupalSettings.path.baseUrl + 'user/register';

  $scope.submit = function() {
    var postTermJsonArray = [];
    var postTermJsonObject = {};

    angular.forEach($scope.user, function(value, key) {
      if(value) {

        if (key =='speciality') {
          postTermJsonArray.push({
            [key]:[{"target_id": value }]
          });
        }
        else if (key == 'confirmPassword') {}
        else {
          postTermJsonArray.push({
            [key]:[{"value": value }]
          });
        }
      }
    });

    Array.prototype.forEach.call(postTermJsonArray, function(element) {
      var keys = Object.keys(element);
      postTermJsonObject[keys[0]] = element[keys[0]];
    });

    $http({
        method  : 'POST',   // GET, POST, PATCH, DELETE
        url     : postUrl,
        data    : postTermJsonObject,
        // headers : {'Content-Type': 'application/json', 'X-CSRF-Token': $scope.csrfToken},
        headers : {'Content-Type': 'application/json'},
    }).then(function(response) {
      $scope.isLoading = false;
      $scope.registerSuccessfullAlert();

    },function (error) {
      $scope.isLoading = false;
      console.log('Form not submit successfully');
    });
  }

  $scope.registerSuccessfullAlert = function($event) {
    var parentEl = angular.element(document.body);
    $mdDialog.show({
      clickOutsideToClose: true,
      parent: parentEl,
      targetEvent: $event,
      scope: $scope,
      preserveScope: true,
      template: `
        <md-dialog flex="45" class="register-dialogbox">
            <md-dialog-content class="overflow-hidden">
            <div class="row padding-20">
              <div class="col-xs-offset-2 col-xs-8 col-sm-offset-0 col-sm-4">
                <img src="` +imagePath+ `/login-page-logo.png" width="100%" alt="">
              </div>
              <div class="col-xs-12 col-sm-8 text-align-center">
                <h4 class="color-009ddf">Registration Successful!</h4>
                <p class="margin-top-28">We will send you an email regarding further information once Mildder is live.</p>
              </div>
            </div>
            <div class="row text-align-center">
              <a href="` +drupalSettings.path.baseUrl+ `/home/guide/page" class="btn btn-submit" ng-click="closeDialog()">Great!</a>
            </div>
            </md-dialog-content>
        </md-dialog>`,
      controller: function DialogController($scope, $mdDialog) {
        $scope.closeDialog = function() {
          $mdDialog.hide();
        }
      }
    });
  }
});
