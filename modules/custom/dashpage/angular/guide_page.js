var imagePath = drupalSettings.path.baseUrl + 'modules/custom/dashpage/images';

var pageInfoBaseControllers = angular.module('MildderInfoHome', ['ngResource', 'ngMaterial', 'ngMessages']);
pageInfoBaseControllers.controller('MildderInfoHomeController', function($scope, $http, $element, $sce, $mdDialog) {
  $scope.$sce = $sce;

  $scope.teamBio = [
    {
      termName: "Dr. Shane Shapera",
      termData: "<b>Dr. Shapera</b> obtained his Doctor of Medicine in 2003 from the University of Toronto and went on to continue in Toronto during his Internal Medicine and Respirology residency training. With the help of the Dr. Cameron C. Gray Award, he completed an additional year of clinical fellowship with an emphasis in Interstitial Lung Diseases. He was appointed to the Faculty of Medicine as an Assistant Professor and Clinician Educator in December 2011. He has been an innovator in his role as an educator and has received numerous teaching awards since his appointment. He is currently the Director of the Interstitial Lung Diseases Program at the Toronto General Hospital, UHN. He also works with the Toronto Lung Transplant Program providing pre-lung transplant assessments for patients with ILDs.",
      imageName: "p-1"
    },
    {
      termName: "Dr. Jolene Fisher",
      termData: "<b>Dr. Fisher</b> obtained her Doctor of Medicine in 2008 from the University of Manitoba. She went on to complete an Internal Medicine residency at the University of Manitoba and a Respirology residency at the University of Toronto. She received formal training in Interstitial Lung Diseases at the Toronto General Hospital, UHN, completing a fellowship in 2014 and is currently enrolled in a Clinical Epidemiology Master of Science program at the University of Toronto. Dr. Fisher works as a clinical associate in UHN’s ILD Clinic.",
      imageName: "p-2"

    },
    {
      termName: "Dr. Shikha Mittoo",
      termData: "<b>Dr. Mittoo</b> obtained her Doctor of Medicine in 2001 from McMaster University and then went on to do her internal medicine, rheumatology residency training and Master’s in Clinical Investigation/Epidemiology at Johns Hopkins Hospital where she did an additional year of training in fibrosing diseases (ILDs and scleroderma) and received the Daniel Baker Award for Outstanding Patient Care. She subsequently came on faculty at the University of Manitoba from 2007-2010 before joining the Faculty of Medicine at University of Toronto as Assistant Professor and Staff Rheumatologist at UHN and Mount Sinai Hospital. She had successful peer-reviewed funding in lupus lung disease, scleroderma ILD, and rheumatoid arthritis ILD and has authored several peer-reviewed articles and book chapters on connective tissue disease-related interstitial lung disease.  She has served as the Co-Director of Research for the UHN ILD Program from 2011-2016 and has started a new model of care in the community for chronic complex rheumatic disease and ILD.",
      imageName: "p-3"
    },
    {
      termName: "Dr. Heidi Roberts",
      termData: "<b>Dr. Roberts</b> is a radiologist in the Joint Department of Medical Imaging (JDMI) at UHN, Mount Sinai Hospital and Women’s College Hospital. She trained in Germany, where she graduated from Medical School in 1988, and finished her residency as a diagnostic radiologist in 1993. Following a research fellowship at the University of California, San Francisco (UCSF) in 1994, Dr. Roberts went back to Germany, and returned to North America in 1997, where she remained on the faculty of the Department of Radiology at UCSF until she moved to Toronto in 2002.</br></br> Currently, Dr. Roberts is a staff radiologist in the cardiothoracic division in the JDMI. In 2003, she started the Lung Cancer Screening Study at Princess Margaret Hospital as part of the International Early Lung Cancer Action Program (I-ELCAP), and she is the local Principal Investigator of the Pan-Canadian Early Lung Cancer Detection Study. More than 5,000 individuals have been screened under her supervision in those research studies. Since 2007, Dr. Roberts is the Site Director for Medical Imaging at Women’s College Hospital; since 2010 head and director of operations of the chest section in JDMI; and as of July 2010, Professor of Radiology at the University of Toronto.",
      imageName: "p-4"

    },
    {
      termName: "Dr. Taebong Chung",
      termData: "<b>Dr. Chung</b> obtained his Doctor of Medicine for the University of Western Ontario in 1995. He finished residency in radiology at the University of Manitoba in 2000. This was followed by subspecialty fellowship training in chest imaging at the University of Toronto and UHN, where he has continued as a staff radiologist. He is appointed at the University of Toronto as an Assistant Professor in the Department of Medical Imaging. He has a strong interest in teaching and has received numerous teaching awards. He is Director of Education in Chest Imaging at UHN and is the supervisor for elective medical students and radiology residents in chest imaging. He is also the supervisor of the chest imaging fellowship at UHN.",
      imageName: "p-5"

    },
    {
      termName: "Dr. Lee Fidler",
      termData: "<b>Dr. Fidler</b> obtained his Doctor of Medicine in 2010 at the University of Toronto and completed both his Internal Medicine and Respirology training in Toronto. He completed a fellowship in Interstitial Lung Disease at the University Health Network in 2017. He is currently completing a Master's of Clinical Epidemiology at the University of Toronto and is interested in performing research focused on ILD.",
      imageName: "p-8"
    }
  ]

  $scope.showBio = function($event, cardData) {
    var parentEl = angular.element(document.body);
    $mdDialog.show({
      clickOutsideToClose: true,
      parent: parentEl,
      targetEvent: $event,
      scope: $scope,
      preserveScope: true,
      disableParentScroll: true,
      controller: function DialogController($scope, $mdDialog) {
        $scope.closeDialog = function() {
          $mdDialog.hide();
        }
        var vm = this;
        vm.cardData = {};
        vm.cardData = cardData;
      },
      controllerAs: 'teamCard',
      template:
        '<md-dialog flex="40" class="register-dialogbox">' +
          '<md-dialog-content class="overflow-x-hidden">' +
            '<div class="row">' +
              '<div class="col-xs-12 team-card-header">{{teamCard.cardData.termName}}</div>' +
              '<div class="col-xs-12 team-card-content" ng-bind-html="$sce.trustAsHtml(teamCard.cardData.termData)">{{teamCard.cardData.termData}}</div>' +
            '</div>' +
          '</md-dialog-content>' +
        '</md-dialog>',
    });
  }
});

(function($) {

  function main() {

  (function () {
     'use strict';

     // Page scroll
      $('a.page-scroll').click(function() {
          if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
            var target = $(this.hash);
            target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
            if (target.length) {
              $('html,body').animate({
                scrollTop: target.offset().top - 70
              }, 300);
              return false;
            }
          }
        });

      // Show Menu on Book
      $(window).bind('scroll', function() {
          var navHeight = $(window).height() - 70;
          if ($(window).scrollTop() > navHeight) {
              $('.navbar-default').addClass('on');
          } else {
              $('.navbar-default').removeClass('on');
          }
      });

      $('body').scrollspy({
          target: '.navbar-default',
          offset: 80
      });

      // hide menu on clicking on link
      $('.navbar-toggle-link').on('click', function() {
        $('.navbar-main-collapse').collapse('hide');
      });

      $(document).on("scroll", onScroll);

  }());

  }
  main();
})(jQuery);

function onScroll(event) {
  var scrollPos =jQuery(document).scrollTop() + 70;
  jQuery('#menu-center a').each(function () {
    var currLink =jQuery(this);
    var refElement =jQuery(currLink.attr("href"));

    if (refElement.position().top <= scrollPos && refElement.position().top + refElement.height() > scrollPos) {
      jQuery('#menu-center ul li a').removeClass("nav-active");
      currLink.addClass("nav-active");
    }
    else {
      currLink.removeClass(" nav-active");
    }
  });
}


