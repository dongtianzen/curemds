<?php

/**
 * @file
 */

namespace Drupal\manageinfo\Content;

use Drupal\Core\Controller\ControllerBase;

use Drupal\dashpage\Content\DashpageObjectContent;

/**
 *
 */
class JsonFormWidget {
  private $post_url = NULL;
  private $redirect_url = NULL;
  private $delete_redirect_url = NULL;

  public function getPostUrl() {
    return $this->post_url;
  }
  public function getRedirectUrl() {
    return $this->redirect_url;
  }
  public function getDeleteRedirectUrl() {
    if (empty($this->delete_redirect_url)) {
      $this->delete_redirect_url = $this->getRedirectUrl();
    }

    return $this->delete_redirect_url;
  }

  public function setPostUrl($value = NULL) {
    $this->post_url = $value;
  }
  public function setRedirectUrl($value = NULL) {
    $this->redirect_url = $value;
  }
  public function setDeleteRedirectUrl($value = NULL) {
    $this->delete_redirect_url = $value;
  }

  /** - - - - - field- - - - - - - - - - - - - - - */

  public function getCheckbox($fieldName = NULL, $fieldTitle = NULL, $options = array()) {
    $output = array(
      'fieldType' => "checkbox",
      'fieldName' => $fieldName,
      'fieldTitle' => $fieldTitle,
      'fieldClass' => array(),
      'fieldRequired' => NULL,
      'defaultValue' => "",
      'question_tid' => "",
      'refer_tid' => "",
      'refer_uid' => "",
      'refer_other' => "",
      'returnType' => "value",
      'updateStatus' => 0
    );
    $output = $this->setFieldProperty($output, $options);
    return $output;
  }

  public function getDateTime($fieldName = NULL, $fieldTitle = NULL, $options = array()) {
    $startTime = strtotime('00:00:00');
    $timeInterval = array();

    for($i = 0; $i <= 95; ++$i) {
      $timeInterval[] = $startTime + ($i * 15 * 60);
    }

    foreach ($timeInterval as $key => $value) {
      $fieldLabel[] = array(
        "termTid" => (date('H:i', $value)),
        "termName" => (date('h:i A', $value)),
      );
    }

    $output = array(
      'fieldType' => "dateTime",
      'fieldStyle' => "dateTime",
      'fieldName' => $fieldName,
      'fieldTitle' => $fieldTitle,
      'fieldLabel' => $fieldLabel,
      'fieldClass' => array(),
      'fieldRequired' => FALSE,
      'fieldDate' => NULL,
      'fieldTime' => NULL,
      'defaultValue' => "",
      'refer_tid' => "",
      'refer_uid' => "",
      'refer_other' => "",
      'returnType' => "value",
      'updateStatus' => 0
    );

    $output = $this->setFieldProperty($output, $options);
    return $output;
  }

  /**
   * @param $fieldCategory can be hierarchyFather, specificAnswer, filterFather or filterChildren
   * @param $parentTid is needed for child to use filter
   */
  public function getSelect($fieldName = NULL, $fieldTitle = NULL, $options = array(), $fieldType = NULL) {
    if(!$fieldType) {
      $fieldType = 'select';
    }
    $output = array(
      'parentFieldName' => NULL,
      'parentTermTid' => NULL,
      'fieldType' => $fieldType,
      'fieldCategory' => 'filterFather',
      'fieldStyle' => "dropDown",
      'fieldName' => $fieldName,   // result
      'fieldClass' => array(),
      'fieldShow' => TRUE,
      'fieldTitle' => $fieldTitle,
      'fieldLabel' => array(),
      'fieldLabelOptions' => NULL,
      'filteredLabel' => array(),    // select options
      'fieldRequired' => FALSE,
      'defaultValue' => "",       // result
      'question_tid' => "",
      'refer_tid' => "",
      'refer_uid' => "",
      'refer_other' => "",
      'returnType' => "target_id",
      'updateStatus' => 0,
    );

    $output = $this->setFieldProperty($output, $options);
    $output = $this->overrideParentTid($output);

    return $output;
  }

  public function getMultiSelect($fieldName = NULL, $fieldTitle = NULL, $options = array(), $fieldType = 'multiSelect') {
    $output = $this->getSelect($fieldName, $fieldTitle, $options, $fieldType);
    return $output;
  }

  public function getSlider($fieldName = NULL, $fieldTitle = NULL, $options = array()) {
    $output = array(
      'fieldType' => "slider",
      'fieldClass' => array(),
      'fieldName' => $fieldName,
      'fieldTitle' => $fieldTitle,
      'minimumStep' => NULL,
      'minimumValue' => NULL,
      'maximumValue' => NULL,
      'fieldRequired' => FALSE,
      'defaultValue' => "",
      'question_tid' => "",
      'refer_tid' => "",
      'refer_uid' => "",
      'refer_other' => "",
      'returnType' => "value",
      'updateStatus' => 0
    );
    $output = $this->setFieldProperty($output, $options);
    return $output;
  }

  public function getTextfield($fieldName = NULL, $fieldTitle = NULL, $options = array()) {
    $output = array(
      'fieldType' => "textfield",
      'fieldClass' => array(),
      'fieldName' => $fieldName,
      'fieldTitle' => $fieldTitle,
      'fieldLabel' => NULL,
      'fieldRequired' => FALSE,
      'defaultValue' => "",
      'question_tid' => "",
      'refer_tid' => "",
      'refer_uid' => "",
      'refer_other' => "",
      'returnType' => "value",
      'updateStatus' => 0
    );
    $output = $this->setFieldProperty($output, $options);

    return $output;
  }

  /** - - - - - field- - - - - - - - - - - - - - - */
  /**
   *
   */
  public function setFieldProperty($output = array(), $options = array(), $child = FALSE) {
    if (is_array($options)) {
      foreach ($options as $key => $value) {

        if (array_key_exists($key, $output)) {
          if (is_array($value)) {
            $output[$key] = $this->setFieldProperty($output[$key], $value, TRUE);
          }
          else {
            $output[$key] = $value;
          }
        }
        else {
          if ($child) {
            $output[$key] = $value;
          }
        }
      }
    }

    return $output;
  }

  /** - - - - - Options- - - - - - - - - - - - - - - */
  /**
   *
   */
  public function getSelectOptions($question_term = NULL) {
    $scale = 5;
    if ($question_term->id()) {
      $question_scale = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue($question_term, 'field_queslibr_scale');

      if ($question_scale > 0 && ctype_digit($question_scale)) {
        $scale = $question_scale;
      }
    }

    $tree = array(
      1,
      2,
      3,
      4,
      5,
    );

    $question_label = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetId($question_term, 'field_queslibr_label');

    // 2453 is "ABCDEF", 2458 is "Yes No"
    if ($question_label == 2453 || $question_label == 2458) {
      $label_term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($question_label);
      $label_titles = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldAllValues($label_term, 'field_queslabel_title');
      for ($i = 0; $i < $scale; $i++) {
        $output[] = array(
          "termTid" => $tree[$i],
          "termName" => $label_titles[$i],
        );
      }
    }
    else {
      for ($i = 0; $i < $scale; $i++) {
        $output[] = array(
          "termTid" => $tree[$i],
          "termName" => $tree[$i],
        );
      }
    }

    return $output;
  }

  /**
   *
   */
  public function getSelectOptionsForEntityReferenceField($field_definition = array(), $field_name = NULL) {
    if ($field_definition->getSetting('target_type') == 'taxonomy_term') {
      $handler_settings = $field_definition->getSetting('handler_settings');

      if (isset($handler_settings['target_bundles'])) {
        // only get first one
        $target_bundles = reset($handler_settings['target_bundles']);

        $tree = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($target_bundles, 0);
        $term_options = $this->getSelectOptionsFromTreeForBasicTerm($tree, $field_name);
      }
    }
    elseif ($field_definition->getSetting('target_type') == 'user') {
      $users = array();

      if ($field_name == 'field_meeting_speaker') {
        $users = \Drupal::getContainer()->get('flexinfo.queryuser.service')->wrapperUsersByRoleName('speaker');
      }
      elseif ($field_name == 'field_meeting_representative') {
        $users = \Drupal::getContainer()->get('flexinfo.queryuser.service')->wrapperUsersByRoleName('representative');
      }

      if ($users) {
        $term_options = $this->getSelectOptionsFromFullUser($users);
      }
    }

    return $term_options;
  }

  /**
   * @param $fieldCategory can be hierarchyFather, specificAnswer, filterFather or filterChildren
   * @param $parentTid is needed for child to use filter
   */
  public function getSelectOptionsFromTreeForBasicTerm($tree = array(), $field_name = NULL) {
    $term_options = array();

    if (is_array($tree)) {
      foreach ($tree as $tree_term) {
        $child_options = array(
          "termTid" => $tree_term->tid,
          "termName" => $tree_term->name,
        );

        if ($field_name) {
          $child_options = $this->addParentTid($child_options, $field_name, $tree_term->tid);
        }

        $term_options[] = $child_options;
      }
    }

    return $term_options;
  }

  /**
   *
   */
  public function getSelectOptionsFromVocabularyTree($vid = NULL, $field_name = NULL) {
    $tree = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vid, 0);
    $term_options = $this->getSelectOptionsFromTreeForBasicTerm($tree, $field_name);

    return $term_options;
  }

  /**
   * @param $fieldCategory can be hierarchyFather, specificAnswer, filterFather or filterChildren
   * @param $parentTid is needed for child to use filter
   */
  public function getSelectOptionsFromTreeForFullTerm($terms = array(), $field_name = NULL) {
    $term_options = array();

    if (is_array($terms)) {
      foreach ($terms as $term) {
        $child_options = array(
          "termTid" => $term->id(),
          "termName" => $term->getName(),
        );

        if ($field_name) {
          $child_options = $this->addParentTid($child_options, $field_name, $term->id());
        }

        $term_options[] = $child_options;
      }
    }

    return $term_options;
  }

  /**
   * @param $fieldCategory can be hierarchyFather, specificAnswer, filterFather or filterChildren
   * @param $parentTid is needed for child to use filter
   */
  public function getSelectOptionsFromTreeForFullTermWithFields($terms = array(), $field_name = NULL, $other_fields = array()) {
    $term_options = array();

    if (is_array($terms)) {
      foreach ($terms as $term) {
        $child_options = array(
          "termTid" => $term->id(),
          "termName" => $term->getName(),
        );

        if ($field_name) {
          $child_options = $this->addParentTid($child_options, $field_name, $term->id());
        }

        if ($other_fields) {
          foreach ($other_fields as $field_name => $method_name) {
            $child_options[$field_name] = \Drupal::getContainer()->get('flexinfo.field.service')->{$method_name}($term, $field_name);
          }
        }

        $term_options[] = $child_options;
      }
    }

    return $term_options;
  }

  /**
   *
   */
  public function getSelectOptionsFromFullUser($terms = array()) {
    $term_options = array();

    if (is_array($terms)) {
      foreach ($terms as $term) {
        $term_options[] = array(
          "termTid" => $term->id(),
          "termName" => $term->getUsername(),
        );
      }
    }

    return $term_options;
  }
}

/**
 *
 */
class JsonFormSortOrder extends JsonFormWidget {

  /**
   *@param $entity = NULL is add, $entity not empty is edit, $type is edit or add
   */
  public function formCustomFieldElements($custom_fields = NULL, $entity = NULL, $type = NULL) {
    $form_elements = array();
    if (is_array($custom_fields) && !empty($custom_fields)) {

      // loop
      foreach ($custom_fields as $field_name => $field_definition) {

        $term_options = array();
        $field_definition_get_type = $field_definition->getType();

        $field_standard_type = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldStandardType();

        // check field type and get option
        if ($field_definition_get_type == 'boolean') {
          $form_elements[] = $this->getCheckbox($field_name, $field_definition->getLabel());
        }
        elseif ($field_definition_get_type == 'list_string') {
          $list_options[] = array(
            "termTid" => 'Yes',
            "termName" => 'Yes'
          );
          $list_options[] = array(
            "termTid" => 'No',
            "termName" => 'No'
          );

          $form_elements[] = $this->getSelect($field_name, $field_definition->getLabel(), array('fieldLabel' => $list_options));
        }
        elseif ($field_definition_get_type == 'datetime') {
          $form_elements[] = $this->getDateTime($field_name, $field_definition->getLabel());
        }
        elseif (in_array($field_definition_get_type, $field_standard_type)) {
          $form_elements[] = $this->getTextfield($field_name, $field_definition->getLabel());
        }
        elseif ($field_definition_get_type == 'entity_reference') {
          $term_options = $this->getSelectOptionsForEntityReferenceField($field_definition, $field_name);

          if ($field_definition->getItemDefinition()->getFieldDefinition()->getFieldStorageDefinition()->getCardinality() == -1) {
            $form_elements[] = $this->getMultiSelect($field_name, $field_definition->getLabel(), array('fieldLabel' => $term_options));
            // $form_elements[] = $this->getSelect($field_name, $field_definition->getLabel(), array('fieldLabel' => $term_options));
          }
          else {
            $form_elements[] = $this->getSelect($field_name, $field_definition->getLabel(), array('fieldLabel' => $term_options));
          }
        }

        // set default value
        if ($entity) {
          $last_element_key =  count($form_elements) - 1;

          if ($field_definition_get_type == 'boolean') {
            $form_elements[$last_element_key]['defaultValue'] = \Drupal::getContainer()
              ->get('flexinfo.field.service')
              ->getFieldFirstBooleanValue($entity, $field_name);
          }
          elseif ($field_definition_get_type == 'datetime') {
            $form_elements[$last_element_key]['defaultValue'] = \Drupal::getContainer()
              ->get('flexinfo.field.service')
              ->getFieldFirstValue($entity, $field_name);
          }
          elseif (in_array($field_definition_get_type, $field_standard_type)) {
            $form_elements[$last_element_key]['defaultValue'] = \Drupal::getContainer()
              ->get('flexinfo.field.service')
              ->getFieldFirstValue($entity, $field_name);
          }
          elseif ($field_definition_get_type == 'entity_reference') {
            if ($field_definition->getItemDefinition()->getFieldDefinition()->getFieldStorageDefinition()->getCardinality() == -1) {
              $form_elements[$last_element_key]['defaultValue'] = \Drupal::getContainer()
                ->get('flexinfo.field.service')
                ->getFieldAllTargetIds($entity, $field_name);
            }
            else {
              $form_elements[$last_element_key]['defaultValue'] = \Drupal::getContainer()
                ->get('flexinfo.field.service')
                ->getFieldFirstTargetId($entity, $field_name);
            }
          }
        }

      }
    }

    return $form_elements;
  }

  /**
   *
   */
  public function formNodeMeetingAddDefaultValues($program_term = NULL) {
    $meeting_default_values = array();

    if ($program_term) {
      $meeting_default_values = array(
        'field_meeting_program' => $program_term->id(),
        'field_meeting_eventregion' => \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetId($program_term, 'field_program_region'),
        'field_meeting_evaluationform' => \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetId($program_term, 'field_program_evaluationform'),
      );
    }

    return $meeting_default_values;
  }

  /**
   *
   */
  public function formNodeMeetingSortOrder() {
    $fields_order = array(
      1 => 'field_meeting_eventregion',
      'field_meeting_eventhub',
      'field_meeting_program',
      'field_meeting_module',
      'field_meeting_programclass',
      'field_meeting_evaluationform',
      'field_meeting_meetingformat',    // event type
      'field_meeting_date',             // ?
      'field_meeting_representative',
      'field_meeting_speaker',
      'field_meeting_multitherapeutic',             // ?
      'field_meeting_location',
      'field_meeting_venuename',
      'field_meeting_address',          // venue location
      'field_meeting_globalcity',
      'field_meeting_province',
      'field_meeting_city',
      'field_meeting_postalcode',
      'field_meeting_latitude',
      'field_meeting_longitude',
      'field_meeting_eventme',
      'field_meeting_honorarium',
      'field_meeting_foodcost',
      'field_meeting_signature',
      'field_meeting_received',
      'field_meeting_usergroup',
      'field_meeting_catering',
      'field_meeting_summaryevaluation',             // ?
      'field_meeting_evaluationnum',
    );

    return $fields_order;
  }

  /**
   * order array need start from 1 not 0
   */
  public function formTermProgramSortOrder() {
    $fields_order = array(
      1 => 'field_program_region',
      'field_program_businessunit',
      'field_program_theraparea',
      'field_program_diseasestate',
    );

    return $fields_order;
  }

  /**
   * order array need start from 1 not 0
   */
  public function formTermQuestionlibrarySortOrder() {
    $fields_order = array(
      1 => 'field_queslibr_fieldtype',
      'field_queslibr_label',
      'field_queslibr_scale',
      'field_queslibr_questiontype',
      'field_queslibr_subtitle',
      'field_queslibr_module',
    );

    return $fields_order;
  }

  /**
   *
   */
  public function sortCustomFields($custom_fields = NULL, $fields_order = array()) {
    // sort fields order
    if ($fields_order) {
      uksort($custom_fields, function ($a, $b) use ($fields_order) {
        $pos_a = array_search($a, $fields_order);
        $pos_b = array_search($b, $fields_order);

        $result = 0;
        // order array need start from 1 not 0, because need check $pos_a or $pos_b
        if ($pos_a && $pos_b) {
          $result = $pos_a - $pos_b;
        }
        elseif ($pos_b) {
          $result = 100;
        }

        return $result;
      });
    }

    return $custom_fields;
  }

}

/**
 *
 */
class JsonFormBase extends JsonFormSortOrder {

  /**
   *
   */
  public function addParentTid($child_options = NULL, $field_name = NULL, $tid = NULL) {
    $parent_list = $this->storeParentList();
    $parent_list_key_array = array_keys($parent_list);

    if (in_array($field_name, $parent_list_key_array)) {
      if ($tid) {
        $term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($tid);
        $child_options['termParentTid'] = array(\Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetId($term, $parent_list[$field_name]['option_field']));
      }
    }

    return $child_options;
  }

  /**
   *
   */
  public function setFormDefaultValues($form_elements = [], $default_values = []) {
    if (is_array($default_values) && $default_values) {
      foreach ($default_values as $key => $row) {
        $result_key = array_search($key, array_column($form_elements, 'fieldName'));

        if ($result_key) {
          if (isset($form_elements[$result_key]['defaultValue'])) {
            $form_elements[$result_key]['defaultValue'] = $row;
          }
        }
      }
    }

    return $form_elements;
  }

  /**
   *
   */
  public function storeParentList() {
    $parent_list = array(
      // node meeting
      // 'field_meeting_module' => array(
      //   'option_field' => 'field_module_program',
      //   'filter_field' => 'field_meeting_program',
      // ),
      // term program
      'field_program_diseasestate' => array(
        'option_field' => 'field_disease_theraparea',
        'filter_field' => 'field_program_theraparea',
      ),
      'field_program_theraparea' => array(
        'option_field' => 'field_theraparea_businessunit',
        'filter_field' => 'field_program_businessunit',
      ),
    );

    return $parent_list;
  }

  /**
   *
   */
  public function overrideParentTid($output = NULL) {
    $parent_list = $this->storeParentList();
    $parent_list_key_array = array_keys($parent_list);

    if (in_array($output['fieldName'], $parent_list_key_array)) {
      $output['parentFieldName'] = $parent_list[$output['fieldName']]['filter_field'];
      $output['fieldLabelOptions'] = 'filteredChildren';
    }

    return $output;
  }

  /**
   *
   */
  public function evaluationReferUserQuestions() {
    $refer_user_questions = array(
      3006,
    );
    return $refer_user_questions;
  }

  /**
   *
   */
  public function formNodeInfo($bundle = NULL, $form_type = 'add', $meeting_nid = NULL) {
    $formInfo = array(
      'postUrl' =>  $this->getPostUrl(),
      'redirectUrl' => $this->getRedirectUrl(),
      'deleteRedirectUrl' => $this->getDeleteRedirectUrl(),
      'formType' => $form_type,       // add or edit
      'resultSubmit' => array(
        'type' => array(
          'target_id' => $bundle,
        ),
      ),
    );
    if ($form_type == 'edit') {
      $formInfo['redirectUrl'] = 'manageinfo/node/evaluation/add/form/' . $meeting_nid;
    }
    return $formInfo;
  }

  /**
   *
   */
  public function formNodeEvaluationInfo($bundle = NULL, $form_type = 'add', $meeting_nid = NULL) {
    $formInfo = array(
      'postUrl' =>  $this->getPostUrl(),
      'redirectUrl' => $this->getRedirectUrl(),
      'deleteRedirectUrl' => $this->getDeleteRedirectUrl(),
      'formType' => $form_type,       // add or edit
      'resultSubmit' => array(
        'type' => array(
          'target_id' => $bundle,
        ),
        'field_evaluation_meetingnid' => array(
          'target_id' => $meeting_nid,
          "target_type" => "node",
        ),
      ),
    );

    return $formInfo;
  }

  /**
   *
   */
  public function formNodeEvaluationAdd($meeting_nid = NULL) {
    $form_elements = array();

    $meeting_node = \Drupal::entityTypeManager()->getStorage('node')->load($meeting_nid);
    if ($meeting_node) {
      $evaluationform_term = \Drupal::getContainer()->get('flexinfo.node.service')->getMeetingEvaluationformTerm($meeting_node);

      $question_terms = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldAllTargetIdsEntitys($evaluationform_term, 'field_evaluationform_questionset');

      // add node name
      $title_placeholder = 'Evaluation for meeting ' . $meeting_nid;
      $form_elements[] = $this->getTextfield('title', 'Title', array('defaultValue' => $title_placeholder));

      if (is_array($question_terms) && !empty($question_terms)) {
        foreach ($question_terms as $question_term) {
          $question_title = $question_term->getName();

          // repeat question by user
          if ($question_term->id() == 3006 || $question_term->id() == 2843) {    // How effective was the speaker?

            $speaker_users = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldAllTargetIdsEntitys($meeting_node, 'field_meeting_speaker', 'user');
            if ($speaker_users) {
              foreach ($speaker_users as $speaker_user) {
                $question_title = $question_term->getName() . ' - ' . $speaker_user->getUsername();

                $options = array(
                  'refer_uid' => $speaker_user->id(),
                );

                $form_elements[] = $this->formNodeEvaluationQuestionElements($question_term, $meeting_node, $question_title, $options);
              }
            }
          }
          else {
            $form_elements[] = $this->formNodeEvaluationQuestionElements($question_term, $meeting_node, $question_title);
          }

        }
      }
    }

    return $form_elements;
  }

  /**
   *
   */
  public function formNodeEvaluationQuestionElements($question_term = NULL, $meeting_node, $question_title = NULL, $options = array()) {
    $question_elements = array();

    $field_name = 'field_evaluation_reactset';

    $fieldtype = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetId($question_term, 'field_queslibr_fieldtype');

    // check field type
    switch ($fieldtype) {
      case '2490':  // 2490 is checkbox
        $question_elements = $this->formNodeEvaluationQuestionElementsCheckbox($field_name, $question_title, $question_term, $options);
        break;

      case '2493':  // 2493 is radios
        $question_elements = $this->formNodeEvaluationQuestionElementsRadios($field_name, $question_title, $question_term, $options);

        break;

      case '2494':  // selectkey tid is 2494
        $question_elements = $this->formNodeEvaluationQuestionElementsSelectkey($field_name, $question_title, $question_term, $options);
        break;

      case '2496':  // 2496 is textfield
        $question_elements = $this->formNodeEvaluationQuestionElementsTextfield($field_name, $question_title, $question_term, $options);

        break;

      default:
        break;
    }

    return $question_elements;
  }

  /**
   *
   */
  public function formNodeEvaluationQuestionElementsCheckbox($field_name, $question_title, $question_term, $options) {
    $question_elements = $this->getCheckbox(
      $field_name,
      $question_title,
      array(
        'question_tid' => $question_term->id(),
      )
    );

    return $question_elements;
  }

  /**
   *
   */
  public function formNodeEvaluationQuestionElementsRadios($field_name, $question_title, $question_term, $options) {
    $question_options = array(
      'fieldLabel' => $this->getSelectOptions($question_term),
      'question_tid' => $question_term->id(),
    );

    if ($options) {
      foreach ($options as $key => $value) {
        // 'refer_uid' => 366,
        $question_options[$key] = $value;
      }
    }

    $question_elements = $this->getSelect(
      $field_name,
      $question_title,
      $question_options
    );

    return $question_elements;
  }

  /**
   *
   */
  public function formNodeEvaluationQuestionElementsSelectkey($field_name, $question_title, $question_term, $options) {
    $queslibr_label_term = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetIdTermEntity($question_term, 'field_queslibr_label');

    $refer_vid = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetId($queslibr_label_term, 'field_queslibr_refervocabulary');

    $question_elements = $this->getMultiSelect(
      $field_name,
      $question_title,
      array(
        'fieldLabel' => $this->getSelectOptionsFromVocabularyTree($refer_vid),
        'question_tid' => $question_term->id(),
      )
    );

    return $question_elements;
  }

  /**
   *
   */
  public function formNodeEvaluationQuestionElementsTextfield($field_name, $question_title, $question_term, $options) {
    $question_options = array(
      'question_tid' => $question_term->id(),
    );
    if ($options) {
      foreach ($options as $key => $value) {
        // 'refer_uid' => 366,
        $question_options[$key] = $value;
      }
    }

    $question_elements = $this->getTextfield(
      $field_name,
      $question_title,
      $question_options
    );

    return $question_elements;
  }

  /**
   *
   */
  public function formNodeMeetingAdd($program_tid = NULL) {
    if ($program_tid) {
      $program_term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($program_tid);
    }

    $program_name = NULL;
    $meeting_default_values = NULL;
    if ($program_term) {
      $vid = $program_term->getVocabularyId();
      if ($vid == 'program') {
        $program_name = $program_term->getName();

        $meeting_default_values = $this->formNodeMeetingAddDefaultValues($program_term);
      }
    }

    // add node name
    $title_name = t('Meeting') . ' - ' . $program_name;
    $form_elements[] = $this->getTextfield(
      'title',
      t('Meeting Name'),
      array(
        'defaultValue' => $title_name,
      )
    );

    $custom_fields = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldsCollectionByEntityBundle('node', 'meeting');
    $custom_fields = $this->sortCustomFields($custom_fields, $this->formNodeMeetingSortOrder());

    $form_elements = array_merge($form_elements, $this->formCustomFieldElements($custom_fields, NULL, 'add'));

    if ($meeting_default_values) {
      $form_elements = $this->setFormDefaultValues($form_elements, $meeting_default_values);
    }

    return $form_elements;
  }

  /**
   *
   */
  public function formNodeMeetingEdit($meeting_node = NULL) {
    $meeting_default_values = NULL;

    // add node name
    $title_name = t('Meeting');
    $form_elements[] = $this->getTextfield(
      'title',
      t('Meeting Name'),
      array(
        'defaultValue' => $meeting_node->getTitle(),
      )
    );

    $custom_fields = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldsCollectionByEntityBundle('node', 'meeting');
    $custom_fields = $this->sortCustomFields($custom_fields, $this->formNodeMeetingSortOrder());

    $form_custom_elements = $this->formCustomFieldElements($custom_fields, $meeting_node, 'edit');
    $form_elements = array_merge($form_elements, $form_custom_elements);

    return $form_elements;
  }

  /**
   *
   */
  public function formTermAdd($vid = NULL) {
    $title_name = ucwords($vid) . ' ' . t('Name');
    $form_elements[] = $this->getTextfield('name', $title_name);

    $custom_fields = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldsCollectionByEntityBundle('taxonomy_term', $vid);

    $method_name = 'formTerm' . ucwords($vid) . 'SortOrder';
    if (method_exists($this, $method_name)) {
      $fields_order = $this->{$method_name}();
    }

    $form_elements = array_merge($form_elements, $this->formCustomFieldElements($custom_fields, NULL, 'add'));

    return $form_elements;
  }

  /**
   *
   */
  public function formTermEdit($term = NULL, $vid = NULL) {
    // add term name
    $title_name = ucwords($vid) . ' ' . t('Name');
    $form_elements[] = $this->getTextfield(
      'name',
      $title_name,
      array(
        'defaultValue' => $term->getName(),
      )
    );

    // custom fields
    $custom_fields = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldsCollectionByEntityBundle('taxonomy_term', $vid);

    $method_name = 'formTerm' . ucwords($vid) . 'SortOrder';
    if (method_exists($this, $method_name)) {
      $fields_order = $this->{$method_name}();
      $custom_fields = $this->sortCustomFields($custom_fields, $fields_order);
    }

    $form_custom_elements = $this->formCustomFieldElements($custom_fields, $term, 'edit');

    $form_elements = array_merge($form_elements, $form_custom_elements);

    return $form_elements;
  }

  /**
   *
   */
  public function formTermInfo($vid = NULL, $form_type = 'add') {
    $formInfo = array(
      'postUrl' =>  $this->getPostUrl(),
      'redirectUrl' => $this->getRedirectUrl(),
      'deleteRedirectUrl' => $this->getDeleteRedirectUrl(),
      'formType' => $form_type,       // add or edit
      'resultSubmit' => array(
        'vid' => array(
          'target_id' => $vid,
        ),
      ),
    );

    return $formInfo;
  }

  /**
   *
   */
  public function formUserAdd($user = NULL) {
    $form_elements[] = $this->getTextfield(
      'name',
      t('Name')
    );

    $form_elements[] = $this->getTextfield(
      'mail',
      'Email'
    );

    $form_elements[] = $this->getTextfield(
      'pass',
      'Password'
    );

    $roles_options[] = array(
      "termTid" => 'client',
      "termName" => 'Client'
    );
    $roles_options[] = array(
      "termTid" => 'representative',
      "termName" => 'Representative'
    );
    $roles_options[] = array(
      "termTid" => 'speaker',
      "termName" => 'Speaker'
    );

    $form_elements[] = $this->getSelect(
      'roles',
      'Roles',
      array('fieldLabel' => $roles_options)
    );

    return $form_elements;
  }

  /**
   *
   */
  public function formUserEdit($user = NULL) {
    $form_elements[] = $this->getTextfield(
      'name',
      t('Name'),
      array(
        'defaultValue' => $user->getUsername(),
      )
    );

    $form_elements[] = $this->getTextfield(
      'mail',
      'Email',
      array(
        'defaultValue' => $user->get('mail')->value,
      )
    );

    $current_user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
    if ($current_user->hasRole('siteadmin') || $current_user->hasRole('administrator')) {
      $form_elements[] = $this->getTextfield(
        'password',
        'Password'
      );

      $roles_options[] = array(
        "termTid" => 'client',
        "termName" => 'Client'
      );
      $roles_options[] = array(
        "termTid" => 'representative',
        "termName" => 'Representative'
      );
      $roles_options[] = array(
        "termTid" => 'speaker',
        "termName" => 'Speaker'
      );

      $form_elements[] = $this->getSelect(
        'roles',
        'Roles',
        array(
          'fieldLabel' => $roles_options,
          'defaultValue' => \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetId($user, 'roles')
        )
      );

    }

    return $form_elements;
  }

  /**
   *
   */
  public function formUserInfo($uid = NULL, $form_type = 'add') {
    $formInfo = array(
      'postUrl' =>  $this->getPostUrl(),
      'redirectUrl' => $this->getRedirectUrl(),
      'deleteRedirectUrl' => $this->getDeleteRedirectUrl(),
      'formType' => $form_type,       // add or edit
      'resultSubmit' => array(
        'status' => array(
          'value' => true,
        ),
      ),
    );

    return $formInfo;
  }

}

/**
 *
 */
class ManageinfoJsonGenerator extends JsonFormBase {

  /**
   * @param $section = 'evaluation' or 'meeting'
   * @param use Drupal\dashpage\Content\DashpageObjectContent;
   */
  public function nodeAddJson($entity = NULL, $bundle = NULL, $nid = NULL) {
    $this->setPostUrl('entity/node');
    $this->setRedirectUrl('nodeinfo/redirect/meetinginsert');

    $output['formInfo'] = $this->formNodeInfo($bundle, 'add');

    switch ($bundle) {
      case 'evaluation':
        $this->setRedirectUrl('manageinfo/node/evaluation/add/form/' . $nid);
        $meeting_entity = \Drupal::entityTypeManager()->getStorage('node')->load($nid);

        $DashpageObjectContent = new DashpageObjectContent();

        $output['formInfo'] = $this->formNodeEvaluationInfo($bundle, 'add', $nid);
        $output['fixedSection'] = $DashpageObjectContent->blockTileMeeting($meeting_entity, $meeting_snapshot_link = TRUE, $meeting_share_link = FALSE);
        $output['formElementsSection'] = $this->formNodeEvaluationAdd($nid);
        break;

      case 'meeting':
        $output['formElementsSection'] = $this->formNodeMeetingAdd($nid);
        break;

      default:
        break;
    }

    return $output;
  }

  /**
   * @param $section = 'evaluation' or 'meeting'
   */
  public function nodeEditJson($entity = NULL, $nid = NULL) {
    $node = \Drupal::entityTypeManager()->getStorage('node')->load($nid);

    if ($node) {
      $bundle = $node->getType();
    }
    $this->setPostUrl('node/' . $nid);
    $this->setRedirectUrl('manageinfo/node/evaluation/add/form/' . $nid);
    $this->setDeleteRedirectUrl('manageinfo/program/list/all');

    /**
     * switch not work for $this->setRedirectUrl()
     */
    if ($bundle == 'evaluation') {
      $meeting_entity = \Drupal::entityTypeManager()->getStorage('node')->load($nid);

      $DashpageObjectContent = new DashpageObjectContent();

      $output['fixedSection'] = $DashpageObjectContent->blockTileMeeting($meeting_entity);
      $output['formElementsSection'] = $this->formNodeEvaluationEdit($nid);
    }
    elseif ($bundle == 'meeting') {
      $output['formElementsSection'] = $this->formNodeMeetingEdit($node);
    }

    $output['formInfo'] = $this->formNodeInfo($bundle, 'edit', $nid);

    return $output;
  }

  /**
   *
   */
  public function termAddJson($vid = NULL) {
    $this->setPostUrl('entity/taxonomy_term');
    $this->setRedirectUrl('manageinfo/' . $vid . '/list/all');

    $output['formInfo'] = $this->formTermInfo($vid, 'add');
    $output['formElementsSection'] = $this->formTermAdd($vid);

    return $output;
  }

  /**
   *
   */
  public function termEditJson($tid = NULL) {
    $term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($tid);
    $vid = $term->getVocabularyId();

    $this->setPostUrl('taxonomy/term/' . $tid);
    $this->setRedirectUrl('manageinfo/' . $vid . '/list/all');

    $output['formInfo'] = $this->formTermInfo($vid, 'edit');
    $output['formElementsSection'] = $this->formTermEdit($term, $vid);

    return $output;
  }

  /**
   *
   */
  public function termDragEditJson($tid = NULL) {
    $term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($tid);
    $questionset_tids = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldAllTargetIds($term, 'field_evaluationform_questionset');

    $questionlibrary_terms = \Drupal::getContainer()->get('flexinfo.term.service')->getFullTermsFromVidName('questionlibrary');

    $output['fieldLabel'] = $this->getSelectOptionsFromTreeForFullTermWithFields($questionlibrary_terms, NULL, array('field_queslibr_fieldtype' => 'getFieldFirstTargetIdTermName'));
    $output['formName'] = $term->getName();

    $output['selectedQuestions'] = array();
    if ($questionset_tids) {
      foreach ($questionset_tids as $key => $value) {
        $output['selectedQuestions'][]['defaultValue'] = $value;
      }
    }

    return $output;
  }

  /**
   *
   */
  public function userAddJson($uid = NULL) {
    $this->setPostUrl('entity/user');
    $this->setRedirectUrl('manageinfo/user/list/all');

    $output['formInfo'] = $this->formUserInfo(NULL, 'add');
    $output['formElementsSection'] = $this->formUserAdd();

    return $output;
  }

  /**
   *
   */
  public function userEditJson($uid = NULL) {
    $user = \Drupal::entityTypeManager()->getStorage('user')->load($uid);

    $this->setPostUrl('user/' . $uid);
    $this->setRedirectUrl('dashpage/home/snapshot/2064');

    $output['formInfo'] = $this->formUserInfo($uid, 'edit');
    $output['formElementsSection'] = $this->formUserEdit($user);

    return $output;
  }

}
