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
      case 'record':
        $output = $this->basicCollectionNodeContent($section, $entity_id, $start, $end);
        break;

      case 'meeting':
        $output = $this->basicCollectionNodeMeetingContent($section, $entity_id);
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
  public function basicCollectionNodeContent($entity_bundle, $entity_id = NULL, $start = NULL, $end = NULL) {
    $output = array();

    $nids = $this->basicCollectionNids($entity_bundle, $start, $end);

    if (is_array($nids) && $nids) {
      foreach ($nids as $nid) {
        $row = array();

        $edit_path = '/node/' . $nid . '/edit';
        $edit_url = Url::fromUserInput($edit_path);
        $edit_link = \Drupal::l(t('Edit'), $edit_url);

        $collectionContentFields = $this->collectionContentFields($entity_bundle, $nid, $entity_type = 'node');
        if (is_array($collectionContentFields)) {
          $row = array_merge($row, $collectionContentFields);
        }

        // last
        // $row["Edit"] = $edit_link;

        $output[] = $row;
      }
    }

    return $output;
  }

  /**
   * @return php array
   */
  public function basicCollectionNids($entity_bundle = NULL, $start = NULL, $end = NULL) {
    $nids = \Drupal::getContainer()->get('flexinfo.querynode.service')->nidsByBundle($entity_bundle);

    $start_boolean = \Drupal::getContainer()->get('flexinfo.setting.service')->isTimestamp($start);
    $end_boolean = \Drupal::getContainer()->get('flexinfo.setting.service')->isTimestamp($end);
    if ($start_boolean && $end_boolean) {
      $start_query_date = \Drupal::getContainer()
        ->get('flexinfo.setting.service')->convertTimeStampToQueryDate($start);

      $end_query_date = \Drupal::getContainer()
        ->get('flexinfo.setting.service')->convertTimeStampToQueryDate($end);

      if ($entity_bundle == 'record666666') {
        $nids = \Drupal::getContainer()
          ->get('flexinfo.querynode.service')
          ->wrapperNidesByStandardStartEndQueryQate('record', 'field_record_date', $start_query_date, $end_query_date);
      }
    }

    // $nids = array_slice($nodes, 0, 10);

    return $nids;
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

        $edit_path = '/taxonomy/term/' . $tid . '/edit';
        $edit_url = Url::fromUserInput($edit_path);
        $edit_link_ob = \Drupal::l(t('Edit'), $edit_url);

        // first
        $row["Name"] = $term->getName();

        $collectionContentFields = $this->collectionContentFields($vid, $tid, $entity_type = 'taxonomy_term');
        if (is_array($collectionContentFields)) {
          $row = array_merge($row, $collectionContentFields);
        }

        // last
        // if ($edit_link_column) {
        //   $row["Edit"] = $edit_link_ob;
        // }

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
          $output[$field_row['field_label']] = $this->{$field_row['formula_function']}($entity_id, $field_row['field_label']);
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
      case 'record':
        $output = array(
          array(
            'field_label' => '日期',
            'field_name'  => 'field_record_date',
          ),
          array(
            'field_label' => '血红蛋白',
            'field_name'  => 'custom_formula_function',
            'formula_function' => 'colorFieldValueByRange',
          ),
          array(
            'field_label' => '血小板计数',
            'field_name'  => 'custom_formula_function',
            'formula_function' => 'colorFieldValueByRange',
          ),
          array(
            'field_label' => '白细胞总数',
            'field_name'  => 'custom_formula_function',
            'formula_function' => 'colorFieldValueByRange',
          ),
          array(
            'field_label' => '中性粒细胞总数',
            'field_name'  => 'custom_formula_function',
            'formula_function' => 'colorFieldValueByRange',
          ),
          array(
            'field_label' => '查看',
            'field_name'  => 'custom_formula_function',
            'formula_function' => 'linkToViewNode',
          ),
        );
        break;


      // term
      case 'item':
        $output = array(
          array(
            'field_label' => 'ABB',
            'field_name'  => 'field_item_abbrevname',
          ),
          array(
            'field_label' => 'Min',
            'field_name'  => 'field_item_minimun',
          ),
          array(
            'field_label' => 'Max',
            'field_name'  => 'field_item_maximun',
          ),
          array(
            'field_label' => 'Unit',
            'field_name'  => 'field_item_unit',
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

  public function convertTermAbbNameToNodeRecordFieldName($abb_name) {
    $row_name = strtolower($abb_name);

    if (strpos($row_name, '%') !== false) {
      $row_name = str_replace('%', '_pct', $row_name);
    }
    if (strpos($row_name, '-') !== false) {
      $row_name = str_replace('-', '_', $row_name);
    }

    $field_name = 'field_record_' . $row_name;

    return $field_name;
  }

  /**
   * @return
   */
  public function colorHslValue($result_value, $term) {
    // default color #002840
    $output = 'hsl(203, 100%, 12.5%)';

    $min = \Drupal::getContainer()->get('flexinfo.field.service')
      ->getFieldFirstValue($term, 'field_item_minimun');

    if ($result_value < $min) {
      $diff = $min - $result_value;

      $max = \Drupal::getContainer()->get('flexinfo.field.service')
        ->getFieldFirstValue($term, 'field_item_maximun');

      $range = $max - $min;
      $average = ($max + $min) / 2;
      $step = 0.3;

      if ($range > 0) {
        $percentage = 1 - ($diff / $range * $step);
        if ($percentage > 1) {
          $percentage = 1;
        }

        $hsl_color_end = 30;
        $hsl_color_start = 0;

        $hsl_color_angle = ($hsl_color_end - $hsl_color_start) * $percentage;
        $hsl_value = $hsl_color_start + $hsl_color_angle;
        $hsl_value = number_format($hsl_value, 2);

        $lightness = number_format(((0.5 - ($percentage / 2)) * 100), 2);

        // $saturation = number_format(((0.5 - ($percentage / 2)) * 100), 2);

        // $output = 'hsl(' . $hsl_value . ', 100%, 50%)';
        // $output = 'hsl(0, 100%, ' . $lightness . '%)';
        $output = 'hsl(' . $hsl_value . ', 100%, ' . $lightness . '%)';
      }
    }

    return $output;
  }

  /**
   * @return
   */
  public function colorFieldValueByRange($nid, $item_name) {
    $terms = \Drupal::entityTypeManager()
          ->getStorage('taxonomy_term')
          ->loadByProperties(['name' => $item_name]);
    $term = reset($terms);

    $field_name = $this->convertTermAbbNameToNodeRecordFieldName($this
      ->flexinfoEntityService
      ->getEntity('field')
      ->getFieldSingleValue('taxonomy_term', $term, 'field_item_abbrevname')
    );

    $entity  = \Drupal::entityTypeManager()->getStorage('node')->load($nid);
    $result_value = \Drupal::getContainer()->get('flexinfo.field.service')
      ->getFieldFirstValue($entity, $field_name);

    $output = $result_value;
    if ($term) {
      $output = '<div class="" style="color:' . $this->colorHslValue($result_value, $term) . '">';
        $output .= $result_value;
      $output .= '</div>';
    }

    return $output;
  }

  /**
   * @return
   */
  public function linkToViewNode($nid = NULL) {
    $path = '/node/' . $nid;
    $url = Url::fromUserInput($path);
    $link = \Drupal::l('查看', $url);

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
