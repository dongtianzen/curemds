<?php

/**
 * @file
 * Contains \Drupal\terminfo\Controller\TerminfoJsonController.
 */

namespace Drupal\terminfo\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

use Drupal\flexinfo\Service\FlexinfoEntityService;

/**
 *
 */
class TerminfoJsonController extends ControllerBase {

  protected $flexinfoEntityService;

  /**
   * {@inheritdoc}
   */
  public function __construct(FlexinfoEntityService $flexinfoEntityService) {
    $this->flexinfoEntityService = $flexinfoEntityService;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('flexinfo.entity.service')
    );
  }

  /**
   * {@inheritdoc}
   * use Symfony\Component\HttpFoundation\JsonResponse;
   * @param $vid is vid
   * @return php array
   */
  public function basicCollectionContent($section, $entity_id) {
    $section = strtolower($section);

    switch ($section) {
      case 'evaluationbymeeting':
        $output = $this->listEvaluationByMeeting($section, $entity_id);
        break;

      case 'evaluationformbyquestion':
        $output = $this->listEvaluationFormByQuestion($entity_id);
        break;

      case 'meeting':
      case 'meetingsummary':
        $output = $this->basicCollectionNodeMeetingContent($section, $entity_id);
        break;

      case 'meetingbyprogram':
        $output = $this->listMeetingByProgram($section, $entity_id);
        break;

      case 'programbyevaluationform':
        $output = $this->listProgramByEvaluationform($entity_id);
        break;

      case 'questionlibrarybyevaluationform':
        $output = $this->listQuestionlibraryByEvaluationform($entity_id);
        break;

      case 'user':
        $output = $this->basicCollectionUserContent($section);
        break;

      default:
        $output = $this->basicCollectionTermContent($section);
        break;
    }

    return $output;
  }

  /**
   * @return php array
   */
  public function basicCollectionNodeMeetingContent($entity_bundle, $entity_id = 'all') {
    $output = array();

    if ($entity_bundle == 'meeting' || $entity_bundle == 'meetingsummary') {
      $output = $this->listMeeting($entity_bundle, $entity_id);
    }

    return $output;
  }

  /**
   * @return php array
   */
  public function basicCollectionNodeTableArray($entity_bundle, $meeting_nids = array(), $edit_link_column = TRUE) {
    $output = array();

    if (is_array($meeting_nids)) {
      foreach ($meeting_nids as $nid) {
        $row = array();

        $edit_path = '/manageinfo/node/' . $nid . '/edit/form';
        $edit_url = Url::fromUserInput($edit_path);
        $edit_link_ob = \Drupal::l(t('Edit'), $edit_url);

        $collectionContentFields = $this->collectionContentFields($entity_bundle, $nid, $entity_type = 'node');
        if (is_array($collectionContentFields)) {
          $row = array_merge($row, $collectionContentFields);
        }

        if ($entity_bundle == 'evaluation') {
          $edit_link_column = FALSE;
        }

        // last
        if ($edit_link_column) {
          $row["Edit"] = $edit_link_ob;
        }

        $output[] = $row;
      }
    }

    return $output;
  }

  /**
   * @return php array
   */
  public function basicCollectionTermContent($vid) {
    $terms = \Drupal::getContainer()->get('flexinfo.term.service')->getFullTermsFromVidName($vid);

    $output = $this->basicCollectionTermTableArray($vid, $terms);

    return $output;
  }

  /**
   * @return php array
   */
  public function basicCollectionTermTableArray($vid, $terms, $edit_link_column = TRUE) {
    $output = array();

    // check if custom fields already have 'Edit' link
    $custom_manage_fields = $this->customManageFields($vid);
    if ($custom_manage_fields) {
      $field_label_column = array_column($custom_manage_fields, 'field_label');
      $edit_exist = in_array('Edit', $field_label_column);
      if ($edit_exist) {
        $edit_link_column = FALSE;
      }
    }

    if (is_array($terms)) {
      foreach ($terms as $tid => $term) {
        $row = array();

        // $edit_path = '/taxonomy/term/' . $tid . '/edit';
        $edit_path = '/manageinfo/taxonomy_term/' . $tid . '/edit/form';
        $edit_url = Url::fromUserInput($edit_path);
        $edit_link_ob = \Drupal::l(t('Edit'), $edit_url);

        // first
        $row["Name"] = $term->getName();

        $collectionContentFields = $this->collectionContentFields($vid, $tid, $entity_type = 'taxonomy_term');
        if (is_array($collectionContentFields)) {
          $row = array_merge($row, $collectionContentFields);
        }

        // last
        if ($edit_link_column) {
          $row["Edit"] = $edit_link_ob;
        }

        $output[] = $row;
      }
    }

    return $output;
  }

  /**
   * @return php array
   */
  public function basicCollectionUserContent($vid) {
    $output = array();

    $users = \Drupal::entityManager()->getStorage('user')->loadMultiple(NULL);
    // $users = entity_load_multiple('user', NULL);

    if (is_array($users)) {
      foreach ($users as $uid => $user) {
        if ($uid > 1) {
          $row = array();

          $edit_path = '/manageinfo/user/' . $uid . '/edit/form';
          $edit_url = Url::fromUserInput($edit_path);
          $edit_link_ob = \Drupal::l(t('Edit'), $edit_url);

          // first
          $row["Name"] = $user->get('name')->value;

          $collectionContentFields = $this->collectionContentFields($vid, $uid, $entity_type = 'user');
          if (is_array($collectionContentFields)) {
            $row = array_merge($row, $collectionContentFields);
          }

          // last
          $row["Edit"] = $edit_link_ob;

          $output[] = $row;
        }
      }
    }

    return $output;
  }

  /**
   * @return php array
   */
  public function collectionContentFields($vid = NULL, $entity_id = NULL, $entity_type = 'taxonomy_term') {
    $output = NULL;

    $custom_manage_fields = $this->customManageFields($vid);
    if (is_array($custom_manage_fields)) {
      $entity  = \Drupal::entityTypeManager()->getStorage($entity_type)->load($entity_id);

      foreach ($custom_manage_fields as $field_row) {
        if ($field_row['field_name'] == 'custom_formula_function') {
          $output[$field_row['field_label']] = $this->{$field_row['formula_function']}($entity_id);
        }
        else {    // noraml custom field
          $output[$field_row['field_label']] = $this->flexinfoEntityService->getEntity('field')
            ->getFieldSingleValue($entity_type, $entity, $field_row['field_name']);
        }
      }
    }

    return $output;
  }

  /**
   * @return php array
   */
  public function customManageFields($vid = NULL) {
    $output = NULL;

    switch ($vid) {
      // node
      case 'evaluation':
        $output = array(
          array(
            'field_label' => 'View',
            'field_name'  => 'custom_formula_function',
            'formula_function' => 'linkForViewEvaluatioNode',
          ),
          array(
            'field_label' => 'Edit',
            'field_name'  => 'custom_formula_function',
            'formula_function' => 'linkForEditEvaluation',
          ),
        );
        break;

      case 'meeting':
        $output = array(
          array(
            'field_label' => 'Program',
            'field_name'  => 'field_meeting_program',
          ),
          array(
            'field_label' => 'BU',
            'field_name'  => 'custom_formula_function',
            'formula_function' => 'getBusinessUnitByMeetingNid',
          ),
          array(
            'field_label' => 'Date',
            'field_name'  => 'field_meeting_date',
          ),
          array(
            'field_label' => 'Province',
            'field_name'  => 'field_meeting_province',
          ),
          array(
            'field_label' => 'Speaker',
            'field_name'  => 'field_meeting_speaker',
          ),
          array(
            'field_label' => 'Class',
            'field_name'  => 'field_meeting_programclass',
          ),
          // array(
          //   'field_label' => 'City',
          //   'field_name'  => 'field_meeting_city',
          // ),
          array(
            'field_label' => 'Num',
            'field_name'  => 'custom_formula_function',
            'formula_function' => 'linkForEvaluatioNumByMeeting',
          ),
          array(
            'field_label' => 'Add',
            'field_name'  => 'custom_formula_function',
            'formula_function' => 'linkForAddEvaluatioByMeeting',
          ),
        );
        break;

      case 'meetingsummary':
        $output = array(
          array(
            'field_label' => 'Program',
            'field_name'  => 'field_meeting_program',
          ),
          array(
            'field_label' => 'Date',
            'field_name'  => 'field_meeting_date',
          ),
          array(
            'field_label' => 'Speaker',
            'field_name'  => 'field_meeting_speaker',
          ),
          array(
            'field_label' => 'Class',
            'field_name'  => 'field_meeting_programclass',
          ),
          array(
            'field_label' => 'City',
            'field_name'  => 'field_meeting_city',
          ),
          array(
            'field_label' => 'Province',
            'field_name'  => 'field_meeting_province',
          ),
          array(
            'field_label' => 'Num',
            'field_name'  => 'custom_formula_function',
            'formula_function' => 'linkForEvaluatioNumByMeeting',
          ),
          array(
            'field_label' => 'Add',
            'field_name'  => 'custom_formula_function',
            'formula_function' => 'linkForAddEvaluatioByMeeting',
          ),
        );
        break;

      // term
      case 'city':
        $output = array(
          array(
            'field_label' => 'Province',
            'field_name'  => 'field_city_province',
          ),
        );
        break;

      case 'evaluationform':
        $output = array(
          array(
            'field_label' => 'Questions',
            'field_name'  => 'custom_formula_function',
            'formula_function' => 'linkForQuestionlibraryByEvaluationform',
          ),
          array(
            'field_label' => 'Style',
            'field_name'  => 'custom_formula_function',
            'formula_function' => 'linkForEvaluationformSnapshot',
          ),
          array(
            'field_label' => 'Programs',
            'field_name'  => 'custom_formula_function',
            'formula_function' => 'linkForCountEvaluationformByProgram',
          ),
          array(
            'field_label' => 'Edit',
            'field_name'  => 'custom_formula_function',
            'formula_function' => 'linkForEditEvaluationform',
          ),
        );
        break;

      case 'item':
        $output = array(
          array(
            'field_label' => 'ABB',
            'field_name'  => 'field_item_abbrevname',
          ),
          array(
            'field_label' => 'Mini',
            'field_name'  => 'field_item_minimun',
          ),
          array(
            'field_label' => 'Maxi',
            'field_name'  => 'field_item_maximun',
          ),
        );
        break;

      case 'selectquestion':
        $output = array(
          array(
            'field_label' => 'Type',
            'field_name'  => 'field_queslibr_fieldtype',
          ),
          array(
            'field_label' => 'Scale',
            'field_name'  => 'field_queslibr_scale',
          ),
          array(
            'field_label' => 'Question Type',
            'field_name'  => 'field_queslibr_questiontype',
          ),
          array(
            'field_label' => 'Reference',
            'field_name'  => 'custom_formula_function',
            'formula_function' => 'countEvaluationFormByQuestion',
          ),
          array(
            'field_label' => 'Tid',
            'field_name'  => 'custom_formula_function',
            'formula_function' => 'entityIdForTerm',
          ),
        );
        break;

      case 'selectkeyanswer':
        $output = array(
          array(
            'field_label' => 'Question',
            'field_name'  => 'field_keyanswer_question',
          ),
        );
        break;

      // user
      case 'user':
        $output = array(
          array(
            'field_label' => 'Role',
            'field_name'  => 'custom_formula_function',
            'formula_function' => 'userRoleNames',
          ),
        );
        break;

      default:
        break;
    }

    return $output;
  }

  /** - - - - - - table - - - - - - - - - - - - - - - - - - - - - - - - -  */

  /**
   * @param $section = evaluationByMeeting
   */
  public function listEvaluationByMeeting($section, $meeting_nid = NULL) {
    $evaluation_nids = array();
    if ($meeting_nid != 'all') {
      $evaluation_nids = \Drupal::getContainer()
        ->get('flexinfo.querynode.service')
        ->nodeNidsByStandardByFieldValue('evaluation', 'field_evaluation_meetingnid', $meeting_nid);
    }

    $output = $this->basicCollectionNodeTableArray('evaluation', $evaluation_nids);
    return $output;
  }

  /**
   * @return all evaluationform with using specify Question Tid
   */
  public function listEvaluationFormByQuestion($question_tid = NULL) {
    $result = NULL;

    if ($question_tid) {
      $tids = $this->evaluationFormTidsByQuestion($question_tid);

      $terms = taxonomy_term_load_multiple($tids);
      $result = $this->basicCollectionTermTableArray('evaluationform', $terms);
    }

    return $result;
  }

  /**
   *
   */
  public function listMeetingByProgram($section, $program_tid = NULL) {
    $result = NULL;

    if ($program_tid) {
      $nids = \Drupal::getContainer()
        ->get('flexinfo.querynode.service')
        ->nodeNidsByStandardByFieldValue('meeting', 'field_meeting_program', array($program_tid), 'IN');

      $result = $this->basicCollectionNodeTableArray('meeting', $nids);
    }

    return $result;
  }

  /**
   *
   */
  public function listMeeting($entity_bundle, $entity_id = NULL) {
    $output = array();

    $query_container = \Drupal::getContainer()->get('flexinfo.querynode.service');
    $query = $query_container->queryNidsByBundle('meeting');
    $query->sort('field_meeting_date', 'DESC');
    $meeting_nids = $query_container->runQueryWithGroup($query);

    $output = $this->basicCollectionNodeTableArray($entity_bundle, $meeting_nids);
    return $output;
  }

  /**
   * @return all Question set with specify evaluationform
   */
  public function listProgramByEvaluationform($evaluationform_tid = NULL) {
    $output = NULL;

    if ($evaluationform_tid) {
      $term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($evaluationform_tid);
      if ($term) {
        $term_vid = $term->getVocabularyId();
        if ($term_vid == 'evaluationform') {
          $program_terms = \Drupal::getContainer()
            ->get('flexinfo.queryterm.service')
            ->wrapperTermEntitysByField('program', 'field_program_evaluationform', $evaluationform_tid);

          $output = $this->basicCollectionTermTableArray('program', $program_terms);
        }
      }
    }

    return $output;
  }

  /**
   * @return all Question set with specify evaluationform
   */
  public function listQuestionlibraryByEvaluationform($evaluationform_tid = NULL) {
    $output = NULL;

    if ($evaluationform_tid) {
      $term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($evaluationform_tid);
      if ($term) {
        $term_vid = $term->getVocabularyId();
        if ($term_vid == 'evaluationform') {
          $question_terms = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldAllTargetIdsEntitys($term, 'field_evaluationform_questionset');

          $output = $this->basicCollectionTermTableArray('questionlibrary', $question_terms);
        }
      }
    }

    return $output;
  }

  /** - - - - - - render - - - - - - - - - - - - - - - - - - - - - - - - -  */

  /**
   *
   */
  public function countEvaluationFormByQuestion($question_tid = NULL) {
    $num = count($this->evaluationFormTidsByQuestion($question_tid));
    return $num;
  }

  /**
   * @return all evaluationform with using specify Question Tid
   */
  public function evaluationFormTidsByQuestion($question_tid = NULL) {
    $result = NULL;

    $tids = array();
    if ($question_tid) {
      $vid = 'evaluationform';

      $query_container = \Drupal::getContainer()->get('flexinfo.queryterm.service');
      $query = $query_container->queryTidsByBundle($vid);
      $group = $query_container->groupStandardByFieldValue($query, 'field_evaluationform_questionset', $question_tid);
      $query->condition($group);
      $tids = $query_container->runQueryWithGroup($query);
    }

    return $tids;
  }

  /**
   * @return
   */
  public function getBusinessUnitByMeetingNid($meeting_nid = NULL) {
    $meeting_node = \Drupal::entityTypeManager()->getStorage('node')->load($meeting_nid);
    $program_term = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetIdTermEntity($meeting_node, 'field_meeting_program');

    $output = NULL;
    if ($program_term) {
      $output = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetIdTermName($program_term, 'field_program_businessunit');
    }

    return $output;
  }

  /**
   * @return
   */
  public function programNamesByModule($module_tid = NULL) {
    $module_term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($module_tid);

    $output = NULL;
    if ($module_term) {
      $output = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetIdTermName($module_term, 'field_module_program');
    }

    return $output;
  }

  /**
   * @return
   */
  public function userRoleNames($uid = NULL) {
    $role_names = \Drupal::getContainer()->get('flexinfo.user.service')->getUserRolesFromUid($uid);

    $output = NULL;
    if ($role_names) {
      $output = implode(", ", $role_names);
    }

    return $output;
  }

  /** - - - - - - link - - - - - - - - - - - - - - - - - - - - - - - - -  */

  /**
   * @return
   */
  public function linkForAddEvaluatioByMeeting($meeting_nid = NULL) {
    $path = '/manageinfo/node/evaluation/add/form/' . $meeting_nid;
    $url = Url::fromUserInput($path);
    $link = \Drupal::l('Add', $url);

    return $link;
  }

  /**
   * @return
   */
  public function linkForEvaluatioNumByMeeting($meeting_nid = NULL) {
    $link = NULL;

    $meeting_node = \Drupal::entityTypeManager()->getStorage('node')->load($meeting_nid);
    $num = \Drupal::getContainer()->get('flexinfo.field.service')
            ->getFieldFirstValue($meeting_node, 'field_meeting_evaluationnum');

    $link = $num;
    if ($num) {
      $path = '/manageinfo/evaluationbymeeting/list/' . $meeting_nid;
      $url = Url::fromUserInput($path);
      $link = \Drupal::l($num, $url);
    }


    return $link;
  }

  /**
   * @return
   */
  public function linkForAddMeetingByProgram($program_tid = NULL) {
    $path = '/manageinfo/node/meeting/add/form/' . $program_tid;
    $url = Url::fromUserInput($path);
    $link = \Drupal::l('Add', $url);

    return $link;
  }

  /**
   * @return all evaluationform with using specify Question Tid
   */
  public function linkForCountEvaluationFormByQuestion($question_tid = NULL) {
    $num = count($this->evaluationFormTidsByQuestion($question_tid));

    $link = NULL;
    if ($question_tid) {
      $path = '/manageinfo/evaluationformbyquestion/list/' . $question_tid;
      $url = Url::fromUserInput($path);
      $link = \Drupal::l($num, $url);
    }

    return $link;
  }

  /**
   * @return all evaluationform with using specify Question Tid
   */
  public function linkForCountEvaluationformByProgram($evaluationform_tid = NULL) {
    $program_tids = \Drupal::getContainer()
      ->get('flexinfo.queryterm.service')
      ->wrapperTermTidsByField('program', 'field_program_evaluationform', $evaluationform_tid);
    $num = count($program_tids);

    $link = NULL;
    if ($num > 0) {
      $path = '/manageinfo/programbyevaluationform/list/' . $evaluationform_tid;
      $url = Url::fromUserInput($path);
      $link = \Drupal::l($num, $url);
    }

    return $link;
  }

  /**
   * @return
   */
  public function linkForCountMeetingByProgram($program_tid = NULL) {
    $nids = \Drupal::getContainer()
      ->get('flexinfo.querynode.service')
      ->nodeNidsByStandardByFieldValue('meeting', 'field_meeting_program', array($program_tid), 'IN');
    $num = count($nids);

    $path = '/manageinfo/meetingbyprogram/list/' . $program_tid;
    $url = Url::fromUserInput($path);
    $link = \Drupal::l($num, $url);

    return $link;
  }

  /**
   *
   */
  public function linkForEditEvaluation($evaluation_nid = NULL) {
    $link = NULL;
    if ($evaluation_nid) {
      $path = '/node/' . $evaluation_nid . '/edit';
      $url = Url::fromUserInput($path);
      $link = \Drupal::l('Edit', $url);
    }

    return $link;
  }

  /**
   * @return all evaluationform with using specify Question Tid
   */
  public function linkForEditEvaluationform($evaluationform_tid = NULL) {
    $link = NULL;
    if ($evaluationform_tid) {
      $path = '/manageinfo/termdrag/form/' . $evaluationform_tid;
      $url = Url::fromUserInput($path);
      $link = \Drupal::l('Edit', $url);
    }

    return $link;
  }

  /**
   * @return Evaluationform Snapshot page Style Demo
   */
  public function linkForEvaluationformSnapshot($evaluationform_tid = NULL) {
    $term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($evaluationform_tid);

    $link = NULL;
    if ($term) {
      $path = '/dashpage/evaluationform/snapshot/' . $evaluationform_tid;
      $url = Url::fromUserInput($path);
      $link = \Drupal::l(t('Style'), $url);
    }

    return $link;
  }

  /**
   * @return
   */
  public function linkForProgram($program_tid = NULL) {
    $term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($program_tid);

    $link = NULL;
    if ($term) {
      $path = '/dashpage/program/snapshot/' . $program_tid;
      $url = Url::fromUserInput($path);
      $link = \Drupal::l($term->getName(), $url);
    }

    return $link;
  }

  /**
   * @return
   */
  public function linkForViewEvaluatioNode($evaluation_nid = NULL) {
    $link = NULL;
    if ($evaluation_nid) {
      $path = '/node/' . $evaluation_nid;
      $url = Url::fromUserInput($path);
      $link = \Drupal::l('View', $url);
    }

    return $link;
  }

  /**
   * @return
   */
  public function linkForQuestionlibraryByEvaluationform($tid = NULL) {
    $term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($tid);

    $link = NULL;
    if ($term) {
      $path = '/manageinfo/questionlibraryByEvaluationform/list/' . $tid;
      $url = Url::fromUserInput($path);
      $link = \Drupal::l('View', $url);
    }

    return $link;
  }

  /**
   * @return
   */
  public function entityIdForTerm($tid = NULL) {
    return $tid;
  }

}
