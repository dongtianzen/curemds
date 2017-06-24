<?php

/**
 * @file
 */

namespace Drupal\manageinfo\Content;

use Drupal\Core\Controller\ControllerBase;

/**
 * An example controller.
 $ManageinfoContentGenerator = new ManageinfoContentGenerator();
 $ManageinfoContentGenerator->angularForm();
 */
class ManageinfoContentGenerator extends ControllerBase {

  /**
   *
   */
  public function angularForm() {
    $output = '';
    $output .= '<div id="pageInfoBase" data-ng-app="pageInfoBase" class="pageinfo-subpage-common margin-left-16">';
      $output .= '<div data-ng-controller="MildderPreFormController" layout="column" ng-cloak>';

        /* Heading */
        $output .= '<div class="row">';
          $output .= '<div class="md-headline">';
            $output .= '<span class="padding-top-12 margin-left-40 standard-form-title">';
              $output .= $this->standardFormTitle();
            $output .= '</span>';
          $output .= '</div>';
          $output .= '<div class="col-md-12 height-2 bg-00a9e0 margin-top-20"></div>';
        $output .= '</div>';

        $output .= '<div class="padding-top-30 padding-bottom-48" ng-if="formJson.fixedSection.value.length">';
          $output .= '<div ng-bind-html="$sce.trustAsHtml(formJson.fixedSection.value)">';
            $output .= "{{formJson.fixedSection.value}}";
          $output .= '</div>';
        $output .= '</div>';

        /* Form */
        $output .= '<form novalidate name="preForm" class="preform-wrapper">';
          $output .= '<md-content class="autoScroll padding-24">';
          $output .= '<div id="center" class="fixed-center"></div>';

            /* looping */
            $output .= '<div data-ng-repeat="field in formJson.formElementsSection">';
              $output .= '<div ng-switch="field.fieldType">';

                /* Checkbox */
                $output .= '<div data-ng-switch-when="checkbox">';
                  $output .= '<md-input-container md-block class="width-pt-60 {{field.fieldClass}}">';
                    $output .= '<md-checkbox type="checkbox" data-ng-model="field.defaultValue" ng-change="checkValue(field.defaultValue);
                      field.updateStatus=\'1\'">';
                      $output .= '{{field.fieldTitle}}';
                    $output .= '</md-checkbox>';
                  $output .= '</md-input-container>';
                $output .= '</div>';

                /* Date and Time */
                $output .= '<div data-ng-switch-when="dateTime">';
                  $output .= '<md-input-container class="width-pt-25 {{field.fieldClass}}">';
                    $output .= '<label>Time</label>';
                    $output .= '<md-select data-ng-model="field.fieldTime" data-ng-change="field.updateStatus=\'1\'; convertDate(field.fieldTime, field.fieldDate, field.fieldName)" data-ng-required="field.fieldRequired">';
                      $output .= '<md-option data-ng-repeat="options in field.fieldLabel" data-ng-value="options.termTid">';
                        $output .= '{{options.termName}}';
                      $output .= '</md-option>';
                    $output .= '</md-select>';
                  $output .= '</md-input-container>';
                  $output .= '<md-datepicker data-ng-model="field.fieldDate" data-ng-change="convertDate(field.fieldTime, field.fieldDate, field.fieldName)" md-placeholder="Enter Date"></md-datepicker>';
                  $output .= '<md-button class="md-primary" data-ng-click="resetDateTime(field.fieldName);">';
                    $output .= 'Clear';
                  $output .= '</md-button>';
                $output .= '</div>';

                /* Textfield */
                $output .= '<div data-ng-switch-when="textfield">';
                  $output .= '<md-input-container md-block class="width-pt-60 {{field.fieldClass}}" >';
                    $output .= '<label translate>{{field.fieldTitle}}</label>';
                    $output .= '<input name="textfield" data-ng-model="field.defaultValue" aria-label="..." data-ng-change="field.updateStatus=\'1\';" data-ng-required="field.fieldRequired">';
                  $output .= '</md-input-container>';

                  // check co-ordinates button
                  $output .= '<section data-ng-if="field.fieldName == \'field_meeting_postalcode\'" layout="row" layout-sm="column" layout-align="left" layout-wrap class="margin-bottom-30">';
                    $output .= '<md-button data-ng-click="getLatLon()" class="md-raised pageinfo-btn-saved">';
                      $output .= 'Check Coordinates';
                    $output .= '</md-button>';
                  $output .= '</section>';
                $output .= '</div>';

                /* Select */
                $output .= '<div data-ng-switch-when="select">';
                  $output .= '</br><md-input-container ng-show="field.fieldShow" md-block class="width-pt-60 {{field.fieldClass}}">';
                    $output .= '<label>{{field.fieldTitle}}</label>';
                    $output .= '<md-select aria-label="select" data-ng-model="field.defaultValue" data-ng-change="field.updateStatus=\'1\'; masterUpdate(field.defaultValue, field.fieldName, field.fieldCategory)" data-ng-required="field.fieldRequired" md-on-close="searchOption=\'\'">';
                      $output .= '<md-select-header class="demo-select-header">';
                        $output .= '<input data-ng-model="searchOption" ng-keydown="$event.stopPropagation()" type="search"
                        placeholder="Search your Option . ." class="demo-header-searchbox md-text">';
                      $output .= '</md-select-header>';
                      $output .= '<md-option value="">-None</md-option>';

                      // when labels are filtered
                      $output .= '<md-option ng-if="field.fieldLabelOptions == \'filteredChildren\'" data-ng-value="options.termTid" data-ng-repeat="options in field.filteredLabel | filter:searchOption">';
                        $output .= '{{options.termName}}';
                      $output .= '</md-option>';

                      // when labels aren't filtered
                      $output .= '<md-option ng-if="field.fieldLabelOptions != \'filteredChildren\'" data-ng-value="options.termTid" data-ng-repeat="options in field.fieldLabel | filter:searchOption">';
                        $output .= '{{options.termName}}';
                      $output .= '</md-option>';
                    $output .= '</md-select>';
                  $output .= '</md-input-container><br />';
                $output .= '</div>';

                /* MultiSelect */
                $output .= '<div data-ng-switch-when="multiSelect">';
                  $output .= '<md-input-container ng-show="field.fieldShow" md-block class="width-pt-60 {{field.fieldClass}}">';
                    $output .= '<label>{{field.fieldTitle}}</label>';
                    $output .= '<md-select multiple aria-label="select" data-ng-model="field.defaultValue" data-ng-change="field.updateStatus=\'1\'; masterUpdate(field.defaultValue, field.fieldName, field.fieldCategory)" data-ng-required="field.fieldRequired" md-on-close="searchMultiOption=\'\'">';
                      $output .= '<md-select-header class="demo-select-header">';
                        $output .= '<input data-ng-model="searchMultiOption" ng-keydown="$event.stopPropagation()" type="search"
                        placeholder="Search your Option . ." class="demo-header-searchbox md-text">';
                      $output .= '</md-select-header>';

                      $output .= '<md-option ng-if="field.fieldCategory == \'filteredChildren\'" data-ng-value="options.termTid" data-ng-repeat="options in field.filteredLabel | filter:searchMultiOption">';
                        $output .= '{{options.termName}}';
                      $output .= '</md-option>';
                      $output .= '<md-option ng-if="field.fieldCategory != \'filteredChildren\'" data-ng-value="options.termTid" data-ng-repeat="options in field.fieldLabel | filter:searchMultiOption">';
                        $output .= '{{options.termName}}';
                      $output .= '</md-option>';
                    $output .= '</md-select>';
                  $output .= '</md-input-container>';
                $output .= '</div>';

                /* Slider */
                $output .= '<div data-ng-switch-when="slider">';
                  $output .= '<md-input-container md-block class="width-pt-60 {{field.fieldClass}}">';
                    $output .= '<span class="slider-label">{{field.fieldTitle}}</span>';
                    $output .= '<md-slider flex="" class="md-primary" md-discrete="" data-ng-model="field.defaultValue" data-ng-change="field.updateStatus=\'1\';" step="{{field.minimumStep}}" min="{{field.minimumValue}}" max="{{field.maximumValue}}" aria-label="rating" data-ng-required="field.fieldRequired">';
                    $output .= '</md-slider>';
                  $output .= '</md-input-container>';
                $output .= '</div>';

              $output .= '</div>';
            $output .= '</div>';

            /* Action Buttons */
            $output .= '<div class="col-md-8 margin-top-48">';
              $output .= '<div class="col-md-2">';
                $output .= '<md-progress-circular data-ng-show="isLoading" md-mode="indeterminate" class="md-accent md-hue-1"></md-progress-circular>';
              $output .= '</div>';
              $output .= '<div class="col-md-3">';
                $output .= '<md-button  data-ng-disabled="preForm.$invalid" data-ng-init="buttonId=\'submit\';" data-ng-click="submit(buttonId)" class="md-raised pageinfo-btn-saved">';
                  $output .= 'Submit';
                $output .= '</md-button>';
              $output .= '</div>';
              $output .= '<div data-ng-show="saveAndEvaluate" class="col-md-4">';
                $output .= '<md-button   data-ng-disabled="preForm.$invalid" data-ng-init="btnId=\'evaluate\';" data-ng-click="submit(btnId)" class="md-raised pageinfo-btn-add">';
                  $output .= 'Save & Evaluate';
                $output .= '</md-button>';
              $output .= '</div>';
              $output .= '<div class="col-md-3">';
                $output .= '<md-button data-ng-disabled="preForm.$invalid" data-ng-show="deleteButton" class="md-raised pageinfo-btn-cancel margin-right-20" data-ng-click="deleteAlertBox()">';
                  $output .= 'Delete';
                $output .= '</md-button>';
              $output .= '</div>';
            $output .= '</div>';
          $output .= '</md-content>';
        $output .= '</form>';

      $output .= '</div>';
    $output .= '</div>';

    return $output;
  }

  /**
   *
   */
  public function drapDropForm() {
    $output = '';
    $output .= '<div id="pageInfoBase" data-ng-app="pageInfoBase" class="pageinfo-subpage-common margin-left-16">';

      /* Heading */
      $output .= '<div class="row">';
        $output .= '<div class="md-headline">';
          $output .= '<span class="padding-top-12 margin-left-40">';
            $output .= 'Manage Form';
          $output .= '</span>';
        $output .= '</div>';
        $output .= '<div class="col-md-12 height-2 bg-00a9e0 margin-top-20"></div>';
      $output .= '</div>';
      $output .= '<div id="center" class="fixed-center"></div>';

      /* Textfield */
      $output .= '<md-content class="autoScroll padding-24">';
        $output .= 'Form Name: <input id="form-name" type="text" name="Form Name"><br>';
      $output .= '</md-content>';

      $output .= '<div class="wrapper" data-ng-controller="MildderDragController" ng-cloak>';
        $output .= '<md-content class="autoScroll padding-24">';
          $output .= '<div class="containerVertical">';
            $output .= '<div layout="row" data-ng-repeat="field in formDragJson.selectedQuestions track by $index">';

              /* Select */
              $output .= '<div class="col-xs-12 question-select">';
                $output .= '<div class="col-md-1 col-xs-2 padding-0 margin-top-20 float-left padding-top-3">';
                  $output .= '<i style="font-size: 14px;" class="fa fa-arrows stepNum inset"></i>{{$index + 1}}.';
                $output .= '</div>';

                $output .= '<div class="col-md-10 col-xs-9">';
                  $output .= '<md-input-container md-block class="width-pt-100 margin-top-20 margin-bottom-40">';
                    $output .= '<md-select placeholder="Choose Question" aria-label="select" data-ng-model="field.defaultValue">';
                      $output .= '<md-select-header class="demo-select-header">';
                        $output .= '<input data-ng-model="searchQuestion" aria-label=".." ng-keydown="$event.stopPropagation()" type="search"
                          class="demo-header-searchbox md-text">';
                      $output .= '</md-select-header>';
                      $output .= '<md-option data-ng-value="options.termTid" data-ng-repeat="options in formDragJson.fieldLabel | filter:searchQuestion">';
                        $output .= '{{options.termName}} - ({{options.field_queslibr_fieldtype}})';
                      $output .= '</md-option>';
                    $output .= '</md-select>';
                  $output .= '</md-input-container>';
                $output .= '</div>';

                // remove button
                $output .= '<div class="col-xs-1 padding-0 margin-top-28">';
                  $output .= '<span class="table-top-remove-button">';
                    $output .= '<a aria-label="menu" class="font-size-16 md-fab md-button">';
                      $output .= '<md-icon class="fa fa-minus" data-ng-click="removeQuestion($index)"></md-icon>';
                    $output .= '</a>';
                  $output .= '</span>';
                $output .= '</div>';
              $output .= '</div>';

            $output .= '</div>';
          $output .= '</div>';
        $output .= '</md-content>';

        /* Action Buttons */
        $output .= '<section layout="row" layout-sm="column" layout-align="left" layout-wrap class="margin-left-112">';
          $output .= '<div class="col-md-4">';
            $output .= '<md-button data-ng-click="addQuestion()" class="md-raised pageinfo-btn-add">';
              $output .= 'Add New Question';
            $output .= '</md-button>';
            $output .= '</div>';
          $output .= '<div class="col-md-3">';
            $output .= '<md-button data-ng-click="submit()" class="md-raised pageinfo-btn-saved">';
              $output .= 'Submit';
            $output .= '</md-button>';
          $output .= '</div>';
          $output .= '<div class="col-md-3">';
            $output .= '<md-button data-ng-click="deleteAlertBox()" class="md-raised pageinfo-btn-cancel">';
              $output .= 'Delete';
            $output .= '</md-button>';
          $output .= '</div>';
        $output .= '</section>';
      $output .= '</div>';
    $output .= '</div>';

    return $output;
  }

  /**
   *
   */
  public function selectQuestionList() {
    $output = '';
    $output .= '<div id="pageInfoBase" data-ng-app="pageInfoBase" class="manageinfo-question-library margin-left-16">';
      $output .= '<div class="wrapper" data-ng-controller="QuestionLibraryController" ng-cloak>';

        $output .= '<div id="center" class="fixed-center"></div>';
        $output .= '<div class="row padding-bottom-24">';
          $output .= '<div class="md-headline display-inline-block">';
            $output .= '<span class="padding-top-12 margin-left-40">';
              $output .= 'Question Library';
            $output .= '</span>';
          $output .= '</div>';
          $output .= '<span class="table-top-add-button padding-left-4">';
            $output .= '<a aria-label="menu" class="font-size-16 md-fab md-warn md-button" href="' . base_path() . 'manageinfo/taxonomy_term/bundle/add/form/questionlibrary' . '">';
              $output .= '<md-icon class="fa fa-plus" aria-hidden="true"></md-icon>';
            $output .= '</a>';
          $output .= '</span>';

          $output .= '<div class="col-md-12 height-2 bg-00a9e0 margin-top-20"></div>';
        $output .= '</div>';

        /* Create New Evaluation Form */
        $output .= '<div class="row padding-30">';
          $output .= '<md-input-container md-block class="width-pt-100">';
            $output .= '<label>' . t('Evaluation Title') . '</label>';
            $output .= '<input data-ng-model="evalutionFormTitle">';
          $output .= '</md-input-container>';

          /* Question List */
          $output .= '<div ng-repeat="question in selectedQuestions track by $index">';
            $output .= '<div layout="row" class="padding-bottom-10">';
              $output .= '<span class="padding-right-10">{{$index + 1}}.</span>';
              $output .= '<span>{{question[0]}}</span>';

              $output .= '<span class="table-top-remove-button">';
                $output .= '<a ng-model="question.Tid" aria-label="menu" class="font-size-16 md-fab md-warn md-button" ng-click="removeQuestion(question)">';
                  $output .= '<md-icon class="fa fa-minus" aria-hidden="true"></md-icon>';
                $output .= '</a>';
              $output .= '</span>';
            $output .= '</div>';
          $output .= '</div>';

          /* Action Form Submit */
          $output .= '<section layout="row" layout-sm="column" layout-align="left center" layout-wrap>';
            $output .= '<md-button data-ng-click="submit()" class="md-raised pageinfo-btn-saved">';
              $output .= 'Submit';
            $output .= '</md-button>';
          $output .= '</section>';
        $output .= '</div>';

        /*Question Library Table*/
        $output .= '<div class="col-md-12 row margin-top-24">';
            $output .= '<table datatable="ng" dt-options="dtOptions" dt-columns="dtColumns" class="stripe responsive no-wrap">';
              $output .= '<thead>';
              $output .= '<tr>';
                $output .= '<th>Name</th>';
                $output .= '<th>Type</th>';
                $output .= '<th>Scale</th>';
                $output .= '<th>Question Type</th>';
                $output .= '<th>Reference</th>';
                $output .= '<th>Add</th>';
              $output .= '</tr>';
              $output .= '</thead>';
              $output .= '<tbody>';
                $output .= '<tr data-ng-repeat="tableRow in questionLibraryData.contentSection[0].middle.middleMiddle.middleMiddleMiddle.value.tbody">';
                  $output .= '<td>{{tableRow[0]}}</td>';
                  $output .= '<td>{{tableRow[1]}}</td>';
                  $output .= '<td class="padding-left-40">{{tableRow[2]}}</td>';
                  $output .= '<td>{{tableRow[3]}}</td>';
                  $output .= '<td class="padding-left-40" ng-bind-html="tableRow[4]">{{tableRow[4]}}</td>';
                  $output .= '<td class="table-top-add-button padding-0">';
                    $output .= '<a aria-label="menu" class="font-size-16 md-fab md-button">';
                      $output .= '<md-icon class="fa fa-plus" data-ng-click="getQuestionTid(tableRow)"></md-icon>';
                    $output .= '</a>';
                  $output .= '</td>';
                $output .= '</tr>';
              $output .= '</tbody>';
            $output .= '</table>';
        $output .= '</div>';

      $output .= '</div>';
    $output .= '</div>';

    return $output;
  }

  /**
   *
   */
  public function standardFormTitle() {
    $form_title = 'Manage Form';
    $path_args = \Drupal::getContainer()->get('flexinfo.setting.service')->getCurrentPathArgs();

    if (isset($path_args[4])) {
      if ($path_args[2] == 'node') {
        $entity = \Drupal::entityTypeManager()->getStorage('node')->load($path_args[3]);
        if (method_exists($entity, 'getType')) {
          $form_title = ucwords($entity->getType()) . ' Form';
        }
      }
      elseif ($path_args[2] == 'taxonomy_term') {
        $entity = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($path_args[3]);
        if (method_exists($entity, 'getName')) {
          $form_title = $entity->getName() . ' Form';
        }
      }
      elseif ($path_args[2] == 'user') {
        $form_title = 'User Settings';
        if ($path_args[4] == 'edit') {
          $entity = \Drupal::entityTypeManager()->getStorage('user')->load($path_args[3]);
          $form_title = $entity->getUserName() . ' Settings';
        }
      }
    }

    return $form_title;
  }

  /**
   *
   */
  public function summaryEvaluationQuestionPage($entity_id) {
    $meeting_entity = \Drupal::entityTypeManager()->getStorage('node')->load($entity_id);
    $evaluationform_term = \Drupal::getContainer()->get('flexinfo.node.service')->getMeetingEvaluationformTerm($meeting_entity);

    $question_terms = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldAllTargetIdsEntitys($evaluationform_term, 'field_evaluationform_questionset');

    $output = '';
    $output .= '<div class="row">';
      $output .= '<div class="col-md-12">';
        $output .= '<hr />';
      $output .= '</div>';
      $output .= '<div class="col-md-12">';

      if (is_array($question_terms) && !empty($question_terms)) {
        foreach ($question_terms as $question_term) {
          $output .= '<div class="col-md-12 line-height-32">';
            $output .= '<span class="col-md-2 color-00a9e0">';
              $output .= 'add';
            $output .= '</span>';
            $output .= '<span class="col-md-10">';
              $output .= $question_term->getName();
            $output .= '</span>';
          $output .= '</div>';
        }
      }

      $output .= '</div>';
    $output .= '</div>';

    return $output;
  }

}
