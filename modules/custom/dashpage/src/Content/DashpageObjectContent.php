<?php

/**
 * @file
 * Contains \Drupal\dashpage\Content\DashpageObjectContent.
 */

namespace Drupal\dashpage\Content;

use Drupal\Core\Url;
use Drupal\Component\Utility\Unicode;

use Drupal\terminfo\Controller\TerminfoJsonController;

/**
 *
 */
class DashpageGridContent {

  /**
   * @return array
   */
  public function getCustomitemNodes($section, $entity_id) {
    $query_container = \Drupal::getContainer()->get('flexinfo.querynode.service');
    $query = $query_container->queryNidsByBundle('record');
    $query = $query->sort('field_record_date', 'DESC');

    $nids = $query_container->runQueryWithGroup($query);
    $nodes = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($nids);

    return $nodes;
  }

  /**
   * @return array
   */
  public function getCustomitemTerm($section, $entity_id) {
    $terms = \Drupal::entityTypeManager()
          ->getStorage('taxonomy_term')
          ->loadByProperties(['name' => $entity_id]);
    $term = reset($terms);

    return $term;
  }

  /**
   * @return array
   */
  public function tableCustomitem($section, $entity_id) {
    $nodes = $this->getCustomitemNodes($section, $entity_id);
    $term = $this->getCustomitemTerm($section, $entity_id);

    if ($term) {
      $field_name = \Drupal::getContainer()
        ->get('stateinfo.setting.service')
        ->convertTermAbbNameToNodeRecordFieldName(
          \Drupal::getContainer()
            ->get('flexinfo.field.service')
            ->getFieldFirstValue($term, 'field_item_abbrevname')
      );

      if (is_array($nodes)) {
        foreach ($nodes as $node) {
          $result_value = \Drupal::getContainer()->get('flexinfo.field.service')
            ->getFieldFirstValue($node, $field_name);

          $colorHslValue = \Drupal::getContainer()
            ->get('stateinfo.setting.service')
            ->colorRgbValue($result_value, $term);

          $result_color_value = '<div class="" style="color:' . $colorHslValue . '">';
            $result_color_value .= $result_value;
          $result_color_value .= '</div>';

          $output[] = array(
            '日期' => \Drupal::getContainer()->get('flexinfo.field.service')
              ->getFieldFirstValue($node, 'field_record_date'),
            $term->getName() => $result_color_value,
          );
        }
      }
    }

    return $output;
  }

}

/**
 *
 */
class DashpageBlockContent extends DashpageGridContent{

  /**
   *
   */
  public function blockChartLineForOneCustomItem($section, $entity_id) {
    $nodes = $this->getCustomitemNodes($section, $entity_id);
    $term = $this->getCustomitemTerm($section, $entity_id);

    $minimun = \Drupal::getContainer()
      ->get('flexinfo.field.service')
      ->getFieldFirstValue($term, 'field_item_minimun');
    $maximun = \Drupal::getContainer()
      ->get('flexinfo.field.service')
      ->getFieldFirstValue($term, 'field_item_maximun');

    if ($term) {
      $field_name = \Drupal::getContainer()
        ->get('stateinfo.setting.service')
        ->convertTermAbbNameToNodeRecordFieldName(
          \Drupal::getContainer()
            ->get('flexinfo.field.service')
            ->getFieldFirstValue($term, 'field_item_abbrevname')
      );

      if (is_array($nodes)) {
        foreach ($nodes as $node) {
          $result_value[] = \Drupal::getContainer()
            ->get('flexinfo.field.service')
            ->getFieldFirstValue($node, $field_name);

          $result_label[] = \Drupal::getContainer()
            ->get('flexinfo.field.service')
            ->getFieldFirstValue($node, 'field_record_date');

          $min_value_array[] = $minimun;
          $max_value_array[] = $maximun;
        }
      }

      $result_value_array = array(
        $min_value_array,
        $max_value_array,
        $result_value
      );
    }

    $max_value = max($result_value);
    $max_value = max(array($max_value, $maximun));

    $min_value = min($result_value);
    $min_value = min(array($min_value, $minimun));

    $range = $max_value - $min_value;
    $range_rate = 0.2;

    $DashpageJsonGenerator = new DashpageJsonGenerator();

    // $chart_data = \Drupal::getContainer()->get('flexinfo.chart.service')->renderChartLineDataSet($result_value, $result_label);
    $chart_data = \Drupal::getContainer()->get('flexinfo.chart.service')->renderChartMultiLineDataSet($result_value_array, $result_label);

    $output = $DashpageJsonGenerator->getBlockOne(
      array(
        'top'  => array(
          'value' => ucwords($entity_id),          // block top title value
        ),
        'class' => "col-md-12",
      ),
      $DashpageJsonGenerator->getChartLine(
        array(
          "chartOptions" => array(
            'graphMax' => $max_value + ($range * $range_rate),
            'graphMin' => $min_value - ($range * $range_rate),
            'yAxisLabel' => ''
          ),
        ),
        $chart_data
      )
    );

    return $output;
  }

  /**
   *
   */
  public function blockPhpTableWebinarSchedule($table_content = array(), $block_title = 'Event Table') {
    $DashpageJsonGenerator = new DashpageJsonGenerator();
    $output = $DashpageJsonGenerator->getBlockOne(
      array(
        'class' => "col-md-12",
        'blockClasses' => "height-400 overflow-visible",
        'type' => "commonPhpTable",
        'top' => array(
          'value' => 'commonPhpTable'
        )
      ),
      $DashpageJsonGenerator->getCommonTable(NUll, NULL)
    );

    return $output;
  }

  /**
   *
   */
  public function blockTableGenerate($table_content = array(), $block_title = 'Event Table') {
    $block_option = array(
      'class' => "col-md-12",
      'blockClasses' => "height-400 overflow-visible",
      'type' => "mildderTable",
      'top'  => array(
        'enable' => TRUE,
        'value' => $block_title,
      ),
    );

    $DashpageJsonGenerator = new DashpageJsonGenerator();
    $output = $DashpageJsonGenerator->getBlockOne(
      $block_option,
      $DashpageJsonGenerator->getCommonTable(
        array(
          "tableSettings" => array(
            "pagination" => FALSE,
          ),
        ),
        $table_content
      )
    );

    return $output;
  }

}

/**
 * @return php object, not JSON
 */
class DashpageObjectContent extends DashpageBlockContent {

  /**
   * @return php object, not JSON
   */
  public function homeSnapshotObjectContent($meeting_nodes = array(), $entity_id = NULL) {
    $output = $this->homeSnapshotCanadaObjectContent($meeting_nodes, $entity_id);

    return $output;
  }

  /** - - - - - - datatable - - - - - - - - - - - - - - - - - - - - - - - - - */

  /**
   * @return php object, not JSON
   */
  public function datatableSnapshotObjectContent($meeting_nodes = array(), $entity_id = NULL, $method_name = NULL) {
    if ($entity_id != 'all') {
      $program_entity = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($entity_id);

      if ($program_entity) {
        if ($program_entity->getVocabularyId() == 'program') {
          $meeting_nodes = \Drupal::getContainer()
            ->get('flexinfo.querynode.service')
            ->wrapperMeetingNodesByFieldValue($meeting_nodes, 'field_meeting_program', array($entity_id), 'IN');
        }
        else {
          \Drupal::getContainer()->get('flexinfo.setting.service')->throwExceptionPage(404);
        }
      }
      else {
        \Drupal::getContainer()->get('flexinfo.setting.service')->throwExceptionPage(404);
      }
    }

    $output['contentSection'][] = $this->{$method_name}($meeting_nodes);
    return $output;
  }

  /**
   * @return php object, not JSON
   */
  public function customitemSnapshotObjectContent($section, $entity_id) {
    $output['contentSection'][] = $this->blockChartLineForOneCustomItem($section, $entity_id);
    $output['contentSection'][] = $this->blockTableCustomitem($section, $entity_id);
    return $output;
  }

}
