<?php

/**
 * @file
 */

namespace Drupal\dashpage\Content;

use Drupal\Core\Controller\ControllerBase;

use Drupal\dashpage\Content\DashpageBlockGenerator;

/**
 * An example controller.
 $DashpageContentGenerator = new DashpageContentGenerator();
 $DashpageContentGenerator->angularPage();
 */
class DashpageContentGenerator extends ControllerBase {

  /**
   *
   */
  public function angularSnapshot() {
    $DashpageBlockGenerator = new DashpageBlockGenerator();

    $output = '';
    $output .= '<div id="pageInfoBase" data-ng-app="pageInfoBase" class="pageinfo-subpage-common margin-left-60 margin-right-44">';
      $output .= '<div data-ng-controller="PageInfoBaseController" class="row margin-0 margin-top-16" ng-cloak>';
        $output .= '<div data-ng-controller="SaveAsPng">';


          $output .= '<div class="block-one bg-ffffff padding-bottom-20">';
            $output .= '<div class="row">';
              $output .= $DashpageBlockGenerator->topWidgetsFixed();
            $output .= '</div>';
          $output .= '</div>';




          $output .= '<div id="center" class="fixed-center"></div>';
          $output .= '<div id="charts-section" class="block-three row tab-content-block-wrapper">';
            $output .= '<div data-ng-repeat="block in pageData.contentSection" >';
              $output .= '<div class="{{block.class}}">';
                $output .= $DashpageBlockGenerator->contentBlockMaster();
              $output .= '</div>';
            $output .= '</div>';
          $output .= '</div>';

        $output .= '</div>';
      $output .= '</div>';
    $output .= '</div>';

    return $output;
  }

  /**
   *
   */
  public function guidePage() {
    $images_path = base_path() . drupal_get_path('module', 'dashpage') . '/images/';

    $output = '';

    $output .= '<div class="home-page bg-ffffff padding-bottom-20" data-spy="scroll" data-target=".navbar-fixed-top">';

      $output .= '<div data-ng-app="MildderInfoHome" class="height-pt-100">';
        $output .= '<div data-ng-controller="MildderInfoHomeController" layout="column" ng-cloak class="height-pt-100">';

          // navigation
          $output .= '<nav class="navbar navbar-custom navbar-fixed-top" role="navigation">';
            $output .= '<div class="navbar-header">';
              $output .= '<button type="button" class="navbar-toggle navbar-toggle-button" data-toggle="collapse" data-target=".navbar-main-collapse"> ';
                $output .= '<i class="fa fa-bars"></i>';
              $output .= '</button>';
              $output .= '<a class="page-scroll navbar-logo" href="#page-top">';
                $output .= '<img src="' . $images_path . 'navbar_logo.png">';
              $output .= '</a>';
            $output .= '</div>';

            $output .= '<div class="collapse navbar-collapse navbar-main-collapse" id="menu-center">';
              $output .= '<ul class="nav navbar-nav">';
                $output .= '<li> <a class="page-scroll navbar-toggle-link" href="#intro">HOME</a></li>';
                $output .= '<li> <a class="page-scroll navbar-toggle-link" href="#about">ABOUT US</a></li>';
                $output .= '<li> <a class="page-scroll navbar-toggle-link" href="#team">OUR TEAM</a></li>';
                $output .= '<li> <a class="page-scroll navbar-toggle-link" href="#contact">Contact</a></li>';
              $output .= '</ul>';
            $output .= '</div>';
          $output .= '</nav>';

          // introduction page
          $output .= '<div id="intro">';
            $output .= '<div class="intro-body">';
              $output .= '<div class="container">';
                $output .= '<div class="col-lg-offset-3 col-lg-6 col-md-offset-2 col-md-8 col-xs-offset-1 col-xs-10 intro-text-wrapper">';
                  $output .= '<p class="intro-text">';
                    $output .= '<span class="intro-heading">M</span><span>ulti-disciplinary</span><br />';
                    $output .= '<span class="intro-heading">I</span><span>nterstitial&nbsp;</span>';
                    $output .= '<span class="intro-heading">L</span><span>ung&nbsp;</span>';
                    $output .= '<span class="intro-heading">D</span><span>isease</span><br />';
                    $output .= '<span class="intro-heading">D</span><span>iscussion&nbsp;</span>';
                    $output .= '<span class="intro-heading-italic">with&nbsp;</span>';
                    $output .= '<span class="intro-heading">E</span><span>xperts&nbsp;</span>';
                    $output .= '<span class="intro-heading">R</span><span>emotely</span>';
                  $output .= '</p>';
                  $output .= '<div class="row">';
                    $output .= '<div class="col-sm-offset-4 col-sm-4 margin-top-20">';
                      $output .= '<a href="' . base_path() . '/home/user/register" class="btn btn-default page-scroll">';
                        $output .= 'SUBSCRIBE NOW';
                      $output .= '</a>';
                    $output .= '</div>';
                  $output .= '</div>';
                $output .= '</div>';
              $output .= '</div>';
            $output .= '</div>';
          $output .= '</div>';

          // about us
          $output .='<div id="about">';
            $output .='<div class="container">';
              $output .='<div class="col-lg-offset-3 col-lg-6 col-md-offset-2 col-md-8 col-xs-offset-1 col-xs-10">';
                $output .='<div class="section-title text-center center">';
                  $output .='<h2 class="color-009ddf">About Us</h2>';
                $output .='</div>';
                $output .='<h5>';
                  $output .='MILDDER is a virtual network linking Interstitial Lung Disease (ILD) specialists at the University Health Network (UHN)
                    with community-based healthcare providers across the province of Ontario. MILDDER sessions willprovide a platform for continuing medical education (CME) while also providing broad access to multi-disciplinary discussions (MDDs) with clinicians at an ILD center of excellence.</br></br>
                    The MILDDER core team at UHN is comprised of two Respirologists, two Radiologists, a Rheumatologist, and a Pathologist. Every two weeks, the core team gathers for an hour-long web-conference which will include a comprehensive review and MDD of two patient cases presented by referring respirologists.';
                $output .='</h5>';
              $output .='</div>';

              $output .='<div class="col-lg-offset-3 col-lg-6 col-md-offset-2 col-md-8 col-xs-offset-1 col-xs-10">';
                $output .='<div class="section-title text-center center">';
                  $output .='<h2 class="color-009ddf">Our Mission</h2>';
                $output .='</div>';
                $output .='<h5>';
                  $output .='Our purpose is to support community-based health care providers in effectively diagnosing and caring for patients  who have limited access to ILD centres of excellence by providing a cooperative learning and decision-making  environment. </br> </br>The American Thoracic Society diagnostic guidelines for the assessment of patients with ILD recommend that a final ILD diagnosis involve a multidisciplinary discussion (MDD) with experts from the field of Respirology,Radiology, Rheumatology and Pathology. </br> MILDDER provides the virtual space for MDDs to occur while allowing learners to participate in the discussions as part of an extended circle of care.  </br>  </br>By supporting local physicians and increasing their knowledge and confidence in diagnosing and managing patients with ILD, MILDDER ultimately hopes to reduce barriers to care, improve physician confidence, increase access to evidence-based care plans and ultimately elevate the care of patients living with ILD in Ontario.';
                $output .='</h5>';
              $output .='</div>';

              $output .='<div class="col-lg-offset-3 col-lg-6 col-md-offset-2 col-md-8 col-xs-offset-1 col-xs-10">';
                $output .='<div class="section-title text-center center">';
                  $output .='<h2 class="color-009ddf">Our Goals</h2>';
                $output .='</div>';
                $output .='<ul class="about-goals-list">';
                  $output .='<li>';
                    $output .='<span class="col-xs-2 col-sm-1 custom-list-style-wrapper">';
                      $output .='<span class="custom-list-style">1</span>';
                    $output .='</span>';
                    $output .='<span class="col-xs-10 col-sm-11 margin-bottom-30">Improve patient outcomes by ensuring accurate diagnosis and evidence-based care plans.</span>';
                  $output .='</li></br>';
                  $output .='<li>';
                    $output .='<span class="col-xs-2 col-sm-1 custom-list-style-wrapper">';
                      $output .='<span class="custom-list-style">2</span>';
                    $output .='</span>';
                    $output .='<span class="col-xs-10 col-sm-11 margin-bottom-30">Improve access to care by shortening wait times for ILD consultations and allowing patients who are not geographically accessible to an ILD centre of excellence to receive input from an ILD regional referral centre.</span>';
                  $output .='</li></br>';
                  $output .='<li>';
                    $output .='<span class="col-xs-2 col-sm-1 custom-list-style-wrapper">';
                      $output .='<span class="custom-list-style">3</span>';
                    $output .='</span>';
                    $output .='<span class="col-xs-10 col-sm-11 margin-bottom-30">Improve physician knowledge through formal teaching topics and informal discussions around real patient cases.</span>';
                  $output .='</li></br>';

                $output .='</p>';
              $output .='</div>';
            $output .='</div>';
          $output .='</div>';

          // Team
          $output .='<div id="team" class="text-center">';
            $output .='<div class="container">';
              $output .='<div class="col-lg-offset-3 col-lg-6 col-md-offset-2 col-md-8 col-xs-offset-1 col-xs-10">';
                $output .='<div class="section-title text-center center">';
                  $output .='<h2 class="color-009ddf">MILDDER TEAM</h2>';
                $output .='</div>';
                $output .='<div id="row">';
                  $output .='<div data-ng-repeat="teamCard in teamBio">';
                    $output .='<div class="col-xs-offset-1 col-xs-10 col-sm-offset-0 col-sm-4">';
                      $output .='<div class="thumbnail text-center">';
                        $output .='<img src="' . $images_path . 'team/{{teamCard.imageName}}.jpg" alt="..." class="text-center">';
                        $output .='</hr>';
                        $output .='<h5 class="font-size-16">{{teamCard.termName}}</h5>';
                        $output .='<a data-ng-click="showBio($event, teamCard)">View Bio</a>';
                      $output .='</div>';
                    $output .='</div>';
                  $output .='</div>';

                $output .='</div>';
              $output .='</div>';
            $output .='</div>';
          $output .='</div>';

          // Contact
          $output .='<div id="contact">';
            $output .='<div class="container">';
                $output .='<div class="col-lg-offset-3 col-lg-6 col-md-offset-2 col-md-8 col-xs-offset-1 col-xs-10 contact-container">';

                  $output .='<div class="col-sm-7 col-xs-12">';
                    $output .='<h3 class="padding-bottom-10 margin-top-50">CONTACT INFO</h3>';
                    $output .='<h4>Feel free to contact us, if you have any questions</h4>';
                    $output .='<ul class="list-style-none margin-top-40 padding-left-0">';
                      $output .='<li>';
                        $output .='<i class="fa fa-envelope-o fa-lg" aria-hidden="true"></i>';
                        $output .='<a href="mailto:admin@mildder.ca?Subject=Query" target="_top">admin@mildder.ca</a>';
                      $output .='</li>';
                      $output .='<li>';
                        $output .='<i class="fa fa-phone fa-lg" aria-hidden="true"></i>';
                        $output .='<a href="tel:+416)885-9696">(416) 885-9595</a>';
                      $output .='</li>';
                      $output .='<li>';
                        $output .='<i class="fa fa-fax fa-lg" aria-hidden="true"></i>';
                        $output .='(416) 885-9696';
                      $output .='</li>';
                    $output .='</ul>';
                  $output .='</div>';

                  $output .='<div class="col-sm-5 col-xs-12">';
                    $output .='<h3 class="padding-bottom-10 margin-top-50">GET IN TOUCH</h3>';
                    $output .='<h4>Send a Message</h4>';
                    $output .='<form>';
                      $output .='<md-content>';

                        // User Name
                        $output .= '<md-input-container md-block class="width-pt-100 user-name-wrapper">';
                          $output .= '<label translate>Name</label>';
                          $output .= '<input required name="userName" data-ng-model="user.name">';
                          $output .= '<div ng-messages="contactForm.userName.$error">';
                            $output .= '<div ng-message="required">This is required.</div>';
                          $output .= '</div>';
                        $output .= '</md-input-container>';

                        // User Email
                        $output .= '<md-input-container md-block class="width-pt-100">';
                          $output .= '<label translate>Email</label>';
                          $output .= '<input required type="email" name="userEmail" data-ng-model="contactForm.email" minlength="10" maxlength="100" ng-pattern="/^.+@.+\..+$/" />';
                          $output .= '<div ng-messages="contactForm.userEmail.$error" role="alert">';
                            $output .= '<div ng-message-exp="[\'required\', \'minlength\', \'maxlength\', \'pattern\']">';
                              $output .= 'Your email must be between 10 and 100 characters and must be valid.';
                            $output .= '</div>';
                          $output .= '</div>';
                        $output .= '</md-input-container>';

                        $output .= '<md-input-container md-block class="width-pt-100">';
                          $output .= '<label translate>Message</label>';
                          $output .= '<input required name="userMessage" data-ng-model="contactForm.message">';
                          $output .= '<div ng-messages="contactForm.userMessage.$error">';
                            $output .= '<div ng-message="required">This is required.</div>';
                          $output .= '</div>';
                        $output .= '</md-input-container>';

                        $output .='<md-button type="submit" class="btn btn-default">SUBMIT</md-button>';

                      $output .='</md-content>';
                    $output .='</form>';
                  $output .='</div>';
                $output .='</div>';
            $output .='</div>';
          $output .='</div>';



              // // contact from
              // $output .='<div id="contact-form" class="col-sm-offset-3 col-sm-6 col-xs-offset-1 col-xs-10 margin-top-50">';
              //   $output .='<form name="contactForm" class="col-xs-offset-1 col-xs-10 bg-ffffff margin-top-16 margin-bottom-16">';

              //     $output .='<h1>Send a Message</h1>';
              //     $output .='<md-content class="margin-top-40 margin-bottom-48">';

              //       // User Name
              //       $output .= '<md-input-container  md-block class="width-pt-80">';
              //         $output .= '<label translate>Name</label>';
              //         $output .= '<input required name="userName" data-ng-model="user.name">';
              //         $output .= '<div ng-messages="contactForm.userName.$error">';
              //           $output .= '<div ng-message="required">This is required.</div>';
              //         $output .= '</div>';
              //       $output .= '</md-input-container>';

              //       // User Email
              //       $output .= '<md-input-container md-block class="width-pt-80">';
              //         $output .= '<label translate>Email</label>';
              //         $output .= '<input required type="email" name="userEmail" data-ng-model="contactForm.email" minlength="10" maxlength="100" ng-pattern="/^.+@.+\..+$/" />';
              //         $output .= '<div ng-messages="contactForm.userEmail.$error" role="alert">';
              //           $output .= '<div ng-message-exp="[\'required\', \'minlength\', \'maxlength\', \'pattern\']">';
              //             $output .= 'Your email must be between 10 and 100 characters and must be valid.';
              //           $output .= '</div>';
              //         $output .= '</div>';
              //       $output .= '</md-input-container>';

              //       $output .= '<md-input-container md-block class="width-pt-80">';
              //         $output .= '<label translate>Message</label>';
              //         $output .= '<input required name="userMessage" data-ng-model="contactForm.message">';
              //         $output .= '<div ng-messages="contactForm.userMessage.$error">';
              //           $output .= '<div ng-message="required">This is required.</div>';
              //         $output .= '</div>';
              //       $output .= '</md-input-container>';

              //       $output .='<md-button type="submit" class="btn btn-default">Send Message</md-button>';
              //     $output .='</md-content>';
              //   $output .='</form>';
              // $output .='</div>';


          // footer
          $output .='<div id="footer">';
            $output .='<div class="container">';
              $output .='<div class="col-sm-offset-1 col-sm-2 col-xs-offset-3 col-xs-8 footer-logo-wrapper">';
                $output .='<img src="' . $images_path . 'footer_logo.png" width="120px" alt="">';
              $output .='</div>';
              $output .='<div class="col-sm-8 footer-text-wrapper">';
                $output .='<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit.Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur.</p>';
              $output .='</div>';
            $output .='</div>';
          $output .='</div>';

          // lower footer
          $output .='<div class="col-md-12 lower-footer-wrapper">';
            $output .='<div class="col-sm-offset-1 col-sm-3 col-xs-offset-3 col-xs-8 ">';
              $output .='<p style="text-align: center;"> &copy; All Right Reserved</p>';
            $output .='</div>';
            $output .='<div class="col-sm-8 text-align-center">';
              $output .='<span class="margin-right-10">Help</span>';
              $output .='<span class="margin-right-10">Contact</span>';
              $output .='<span class="margin-right-10">Terms</span>';
              $output .='<span class="margin-right-10">Privacy</span>';
            $output .='</div>';
          $output .='</div>';
        $output .='</div>';
      $output .='</div>';

    $output .= '</div>';

    return $output;
  }

  /**
   *
   */
  public function userRegister() {

    $images_path = base_path() . drupal_get_path('module', 'dashpage') . '/images/';

    $output = '';
    $output .= '<div data-ng-app="MildderInfoRegister" id="MildderInfoRegister">';
      $output .= '<div data-ng-controller="MildderInfoRegisterController" layout="column" class="register-form-outer-wrapper" ng-cloak>';
        $output .='<div class="register-form-inner-wrapper">';

          $output .='<div class="register-logo-wrapper margin-top-16">';
            $output .='<img src="' . $images_path . 'register_logo.png" width="20%" alt="">';
          $output .='</div>';

          $output .='<div class="col-xs-12 bg-ffffff">';
            $output .='<h3 class="color-009ddf">Create an Account</h3>';
          $output .='</div>';

          $output .='<div class="col-xs-12 bg-ffffff">';
            $output .='<form name="registerForm" novalidate>';
              $output .='<md-content>';

                // User Name
                $output .= '<md-input-container  md-block class="width-pt-80 username-container">';
                  $output .= '<label translate>Name</label>';
                  $output .= '<input required name="userName" data-ng-model="user.name">';
                  $output .= '<div ng-messages="registerForm.userName.$error">';
                    $output .= '<div ng-message="required">This is required.</div>';
                  $output .= '</div>';
                $output .= '</md-input-container>';

                // User Password
                $output .= '<md-input-container  md-block class="width-pt-80">';
                  $output .= '<label translate>Password</label>';
                  $output .= '<input required type="password" name="password1" data-ng-model="user.pass">';
                  $output .= '<div ng-messages="registerForm.password1.$error">';
                    $output .= '<div ng-message="required">This is required.</div>';
                  $output .= '</div>';
                $output .= '</md-input-container>';

                // User Email
                $output .= '<md-input-container md-block class="width-pt-80">';
                  $output .= '<label translate>Email</label>';
                  $output .= '<input required type="email" name="userEmail" data-ng-model="user.mail" minlength="10" maxlength="100" ng-pattern="/^.+@.+\..+$/" />';
                  $output .= '<div ng-messages="registerForm.userEmail.$error" role="alert">';
                    $output .= '<div ng-message-exp="[\'required\', \'minlength\', \'maxlength\', \'pattern\']">';
                      $output .= 'Your email must be between 10 and 100 characters and must be valid.';
                    $output .= '</div>';
                  $output .= '</div>';
                $output .= '</md-input-container>';

                // City
                $output .= '<md-input-container md-block class="width-pt-80">';
                  $output .= '<label translate>City(Ontario Only)</label>';
                  $output .= '<input required name="userCity" data-ng-model="user.field_user_city">';
                  $output .= '<div ng-messages="registerForm.userCity.$error">';
                    $output .= '<div ng-message="required">This is required.</div>';
                  $output .= '</div>';
                $output .= '</md-input-container>';

                // Speciality
                $output .= '<md-input-container class="width-pt-80">';
                  $output .= '<label>Speciality</label>';
                  $output .= '<md-select data-ng-model="user.field_user_speciality">';
                    $output .= '<md-option data-ng-value="speciality.termTid" data-ng-repeat="speciality in specialityOptions">{{speciality.termName}}</md-option>';
                  $output .= '</md-select>';
                $output .= '</md-input-container>';
              $output .='</md-content>';

              // recaptcha
              $output .= '<div class="recaptcha-wrapper margin-bottom-20">';
                $output .= '<p>Please show that you\'re not a robot.</p>';
                $output .= '<div class="g-recaptcha" data-sitekey="6LcM9iMUAAAAAN63zIMqYSCnylqB0BYwb8YvDzsI"></div>';
              $output .= '</div>';

              $output .='<md-button type="submit" class="btn btn-submit" data-ng-click="registerForm.$valid && submit()">Create Account</md-button>';
            $output .='</form>';
          $output .='</div>';


        $output .='</div>';
      $output .='</div>';
    $output .='</div>';

    return $output;
  }

  /**
   *
   */
  public function userSnapshot() {

    $DashpageBlockGenerator = new DashpageBlockGenerator();

    $images_path = base_path() . drupal_get_path('module', 'dashpage') . '/images/';

    $output = '';
    $output .='<div> Welcome</div>';

    return $output;
  }

}
