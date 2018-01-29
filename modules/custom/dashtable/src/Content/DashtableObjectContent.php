<?php

/**
 * @file
 * Contains \Drupal\dashtable\Content\DashtableObjectContent.
 */

namespace Drupal\dashtable\Content;

use Drupal\Core\Url;
use Drupal\Component\Utility\Unicode;

use Drupal\flexpage\Content\FlexpageBaseJson;
use Drupal\flexpage\Content\FlexpageJsonGenerator;
use Drupal\flexpage\Content\FlexpageSampleDataGenerator;

use Drupal\dashpage\Content\DashpageObjectContent;
use Drupal\dashpage\Content\DashTabSpeaker;


/**
 *
 */
class DashtableGridData {

  public $FlexpageBaseJson;
  public $FlexpageJsonGenerator;
  public $FlexpageSampleDataGenerator;

  /**
   *
   */
  public function __construct() {
    $this->FlexpageBaseJson = new FlexpageBaseJson();
    $this->FlexpageJsonGenerator = new FlexpageJsonGenerator();
    $this->FlexpageSampleDataGenerator = new FlexpageSampleDataGenerator();
  }

  /**
   *
   */
  public function basicTermData($vid = NULL) {
    $terms = \Drupal::getContainer()
      ->get('flexinfo.term.service')
      ->getFullTermsFromVidName($vid);

    foreach ($terms as $term) {
      $tbody[] = [
        $term->getName(),
        $term->getDescription(),
        '<a href=' . base_path() . 'taxonomy/term/' . $term->id() . '/edit' . '>Edit</a>',
      ];
    }

    $output = array(
      "thead" => [
        [
          "NAME",
          "DESCRIPTION",
          "EDIT"
        ]
      ],
      "tbody" => $tbody
    );

    return $output;
  }

  /**
   *
   */
  public function dataMeeting() {
    $nodes = \Drupal::getContainer()->get('flexinfo.querynode.service')->nodesByBundle('meeting');

    foreach ($nodes as $node) {
      $program_entity = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetIdTermEntity($node, 'field_meeting_program');
      // \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetIdTermName($meeting_node, 'field_meeting_program');

      $tbody[] = [
        $program_entity->getName(),
        rand(3, 20),
        \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValueDateFormat($node, 'field_meeting_date'),
        \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetIdTermName($node, 'field_meeting_province'),
        \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetIdUserName($node, 'field_meeting_speaker'),
        rand(3, 20),
        0,
        // \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue($node, 'field_meeting_evaluationnum'),
        \Drupal::l('Add', Url::fromUserInput('/manageinfo/node/evaluation/add/form/' . $node->id())),
        \Drupal::getContainer()->get('flexinfo.node.service')->getNodeEditLink($node->id()),
      ];
    }

    $output = array(
      "thead" => [
        [
          "Program",
          "BU",
          "Date",
          "province",
          "Speaker",
          "Class",
          "Num",
          "Add",
          "Edit",
        ]
      ],
      "tbody" => $tbody
    );

    return $output;
  }

  /**
   * @return array
   */
  public function tableViewRecord($meeting_nodes = array(), $field = 'field_record_date') {
    $output = array();

    if (is_array($meeting_nodes)) {
      foreach ($meeting_nodes as $node) {

        $output[] = array(
          \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue($node, 'field_record_date'),
          \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue($node, $field),
        );
      }
    }

    return $output;
  }

  /**
   * @return array
   */
  public function jsonSingleRecordAsNumber($meeting_nodes = array(), $field = 'field_record_neut') {
    $output = array();

    if (is_array($meeting_nodes)) {
      foreach ($meeting_nodes as $node) {
        $output[] = array(
          (float)\Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue($node, $field),
        );
      }
    }

    return $output;
  }

  /**
   * @return array
   */
  public function jsonSingleRecord($meeting_nodes = array(), $field = 'field_record_date') {
    $output = array();

    if (is_array($meeting_nodes)) {
      foreach ($meeting_nodes as $node) {
        $output[] = array(
          \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue($node, $field),
        );
      }
    }

    return $output;
  }

  /**
   *
   */
  public function dataViewRecord($meeting_nodes = array()) {

    $output = array(
      "thead" => [
        [
          "Date",
          "中性粒细胞总数",
        ]
      ],
    );
    $output["tbody"] = $this->tableViewRecord($meeting_nodes, 'field_record_neut');

    return $output;
  }

  /**
   *
   */
  public function dataRecordJson($meeting_nodes = array()) {
    $terms = \Drupal::getContainer()->get('flexinfo.term.service')->getFullTermsFromVidName('item');;

    foreach ($terms as $term) {

      $abbrevname = strtolower(\Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue($term, 'field_item_abbrevname'));
      $abbrevname = \Drupal::getContainer()->get('stateinfo.setting.service')->convertTermAbbNameToStandardName($abbrevname);

      $output['term'][] = array(
        $abbrevname => $term->getName(),
      );

      $field_item_abbrevname = 'field_record_' . $abbrevname;

      $output[$abbrevname] = $this->jsonSingleRecordAsNumber($meeting_nodes, $field_item_abbrevname);
    }

    $output['date'] = $this->jsonSingleRecord($meeting_nodes);

    return $output;
  }

  /**
   *
   */
  public function htmlSnippetSpeakerlist($tableData = NULL, $topValue = NULL, $col_class = 'col-md-6') {
    $speaker_users = \Drupal::getContainer()->get('flexinfo.queryuser.service')->wrapperUsersByRoleName('speaker');

    $speaker_icon = "";
    $speaker_icon .= '<div class="bg-f0f3f4">';
      $speaker_icon .= '<div class="col-xs-2 font-size-40 bg-f0f3f4">';
        $speaker_icon .= '<i class="fa fa-user-circle fa-lg font-size-40 color-d6006e margin-left-20" aria-hidden="true"></i>';
      $speaker_icon .= '</div>';
      $speaker_icon .= '<div class="col-xs-4 color-009ddf padding-top-12 bg-f0f3f4">';
        $speaker_icon .= '<div class="font-size-18">';
          $speaker_icon .= count($speaker_users);
        $speaker_icon .= '</div>';
        $speaker_icon .= '<div class="">Active Speakers</div>';
      $speaker_icon .= '</div>';
      $speaker_icon .= '<div class="col-xs-2 font-size-40 bg-f0f3f4">';
        $speaker_icon .= '<i class="fa fa-bar-chart fa-lg font-size-40 color-7dba00 margin-left-20" aria-hidden="true"></i>';
      $speaker_icon .= '</div>';
      $speaker_icon .= '<div class="col-xs-4 color-009ddf padding-top-12 bg-f0f3f4">';
        $speaker_icon .= '<div class="font-size-18">';
          $speaker_icon .= 'N/A';
        $speaker_icon .= '</div>';
        $speaker_icon .= '<div class="">Average Rating</div>';
      $speaker_icon .= '</div>';
    $speaker_icon .= '</div>';

    // block option
    $option['top'] = array(
      'enable' => FALSE,
    );
    $option['class'] = 'speakerlist-table-header-block-wrapper col-md-6 col-sm-10';

    $output = $this->FlexpageBaseJson->getBlockHtmlSnippet(
      $option,
      $speaker_icon,
      'col-md-6'
    );
    return $output;
  }

}

/**
 *
 */
class DashtableFeatureContent extends DashtableGridData {

  /**
   * @return Array data
   */
  public function generateCommonTable($table_data = array()) {
    $output = $this->FlexpageBaseJson->getBlockOne(
      array(
        'class' => "col-md-12",
        'type' => "commonTable",
        'blockClasses' => "height-400 overflow-visible"
      ),
      $this->FlexpageBaseJson->getCommonTable(NUll, $table_data)
    );

    return $output;
  }

  /**
   *
   */
  public function generateTermAddLinkHtmlSnippet($vid = NULL) {
    // default one
    // $url = Url::fromUserInput('/admin/structure/taxonomy/manage/' . $vid . '/add');
    $url = Url::fromUserInput('/flexform/entityadd/taxonomy_term/' . $vid);

    $output = $this->generateEntityAddLinkHtmlSnippet($url);
    return $output;
  }

  /**
   *
   */
  public function generateNodeAddLinkHtmlSnippet($bundle = NULL) {
    // default one
    // $url = Url::fromUserInput('/node/add/' . $bundle);
    $url = Url::fromUserInput('/flexform/entityadd/node/' . $bundle);

    $output = $this->generateEntityAddLinkHtmlSnippet($url);
    return $output;
  }

  /**
   *
   */
  public function generateEntityAddLinkHtmlSnippet($url = NULL) {
    $output = '';
    $output .= '<div class="entity-add-link-wrapper">';
      $output .= '<div class="btn-group">';
        $output .= '<a href="' . $url->toString() . '" class="btn btn-block btn-info active">';
          $output .= '<i class="glyphicon glyphicon-plus-sign" aria-hidden="true"></i>';
          $output .= '<span class="margin-left-6">';
            $output .= 'Create';
          $output .= '</span>';
        $output .= '</a>';
      $output .= '</div>';
    $output .= '</div>';

    return $output;
  }

}

/**
 * @return php object, not JSON
 */
class DashtableObjectContent extends DashtableFeatureContent {

  /**
   * {@inheritdoc}
   */
  public function standardSnapshotObjectContent($section, $entity_id = NULL) {
    $content_method = $section . 'SnapshotObjectContent';
    if (method_exists($this, $content_method)) {
      $output = $this->{$content_method}($section, $entity_id);

      // insert create link
      // array_unshift($output['contentSection'], $this->FlexpageBaseJson->getBlockHtmlSnippetWithoutTop(
      //   array(),
      //   $this->generateTermAddLinkHtmlSnippet($section)
      // ));
    }
    else {
      $output = $this->standardTermSnapshotObjectContent($section, $entity_id);
    }

    return $output;
  }

  /**
   * {@inheritdoc}
   */
  public function standardTermSnapshotObjectContent($vid = NULL, $entity_id = NULL) {
    $table_data = $this->basicTermData($vid);

    $output['contentSection'][] = $this->FlexpageBaseJson->getBlockHtmlSnippetWithoutTop(
      array(),
      $this->generateTermAddLinkHtmlSnippet($vid)
    );
    $output['contentSection'][] = $this->generateCommonTable($table_data);
    return $output;
  }

  /**
   * {@inheritdoc}
   */
  public function meetingSnapshotObjectContent() {
    $output['contentSection'][] = $this->FlexpageBaseJson->getBlockHtmlSnippetWithoutTop(
      array(),
      $this->generateNodeAddLinkHtmlSnippet('meeting')
    );

    $table_data = $this->dataMeeting();
    $output['contentSection'][] = $this->generateCommonTable($table_data);
    return $output;
  }

  /** - - - - - - term - - - - - - - - - - - - - - - - - - - - - - - - - -  */

  /**
   * {@inheritdoc}
   */
  public function commonVidSnapshotObjectContent($section, $entity_id, $table_data) {
    $output['contentSection'][] = $this->FlexpageBaseJson->getBlockHtmlSnippetWithoutTop(
      array(),
      $this->generateTermAddLinkHtmlSnippet($section)
    );

    $output['contentSection'][] = $this->generateCommonTable($table_data);
    return $output;
  }

  /** - - - - - - table - - - - - - - - - - - - - - - - - - - - - - - - - -  */

  /**
   * {@inheritdoc}
   */
  public function viewrecordSnapshotObjectContent($section = NULL, $entity_id = NULL, $start = NULL, $end = NULL) {
    $meeting_nodes = \Drupal::getContainer()->get('flexinfo.querynode.service')->nodesByBundle('record');

    $table_data = $this->dataViewRecord($meeting_nodes);
    $output['contentSection'][] = $this->generateCommonTable($table_data);
    return $output;
  }

  /**
   * {@inheritdoc}
   */
  public function recordjsonSnapshotObjectContent($section = NULL, $entity_id = NULL, $start = NULL, $end = NULL) {
    $meeting_nodes = \Drupal::getContainer()->get('flexinfo.querynode.service')->nodesByBundle('record');
    $output = $this->dataRecordJson($meeting_nodes);

    return $output;
  }

}
