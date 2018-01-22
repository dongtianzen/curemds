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
  public function termProvince() {
    $terms = \Drupal::getContainer()
      ->get('flexinfo.term.service')
      ->getFullTermsFromVidName('province');

    foreach ($terms as $term) {
      $tbody[] = [
        $term->getName(),
        $term->getDescription(),
        \Drupal::getContainer()->get('flexinfo.term.service')->getTermEditLink($term->id()),
      ];
    }

    $output = array(
      "thead" => [
        [
          "NAME",
          "ABB",
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
  public function termQuestionlibrary() {
    $terms = \Drupal::getContainer()
      ->get('flexinfo.term.service')
      ->getFullTermsFromVidName('questionlibrary');

    foreach ($terms as $term) {
      $tbody[] = [
        $term->getName(),
        \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetIdTermName($term, 'field_queslibr_fieldtype'),
        \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetIdTermName($term, 'field_queslibr_questiontype'),
        \Drupal::getContainer()->get('flexinfo.term.service')->getTermEditLink($term->id()),
      ];
    }

    $output = array(
      "thead" => [
        [
          "NAME",
          "FieldType",
          "QuestionType",
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
  public function termBusinessunit() {
    $terms = \Drupal::getContainer()
      ->get('flexinfo.term.service')
      ->getFullTermsFromVidName('businessunit');

    foreach ($terms as $term) {
      $tbody[] = [
        $term->getName(),
        $term->getDescription(),
        \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetIdTermName($term, 'field_businessunit_division'),
        \Drupal::getContainer()->get('flexinfo.term.service')->getTermEditLink($term->id()),
      ];
    }

    $output = array(
      "thead" => [
        [
          "NAME",
          "DESCRIPTION",
          "DV",
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
  public function termDiseasestate() {
    $terms = \Drupal::getContainer()
      ->get('flexinfo.term.service')
      ->getFullTermsFromVidName('diseasestate');

    foreach ($terms as $term) {
      $tbody[] = [
        $term->getName(),
        $term->getDescription(),
        \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetIdTermName($term, 'field_disease_theraparea'),
        \Drupal::getContainer()->get('flexinfo.term.service')->getTermEditLink($term->id()),
      ];
    }

    $output = array(
      "thead" => [
        [
          "NAME",
          "DESCRIPTION",
          "TA",
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
  public function termProgram() {
    $terms = \Drupal::getContainer()
      ->get('flexinfo.term.service')
      ->getFullTermsFromVidName('program');

    foreach ($terms as $term) {
      $theraparea_term = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetIdTermEntity($term, 'field_program_theraparea');

      $tbody[] = [
        $term->getName(),
        \Drupal::getContainer()->get('flexinfo.field.service')
          ->getFieldFirstTargetIdTermName($theraparea_term, 'field_theraparea_businessunit'),
        \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue($theraparea_term, 'name'),
        \Drupal::getContainer()->get('flexinfo.term.service')->getTermEditLink($term->id()),
      ];
    }

    $output = array(
      "thead" => [
        [
          "NAME",
          "BU",
          "TA",
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
  public function termTherapeuticarea() {
    $terms = \Drupal::getContainer()
      ->get('flexinfo.term.service')
      ->getFullTermsFromVidName('therapeuticarea');

    foreach ($terms as $term) {
      $tbody[] = [
        $term->getName(),
        $term->getDescription(),
        \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetIdTermName($term, 'field_theraparea_businessunit'),
        \Drupal::getContainer()->get('flexinfo.term.service')->getTermEditLink($term->id()),
      ];
    }

    $output = array(
      "thead" => [
        [
          "NAME",
          "DESCRIPTION",
          "BU",
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
   *
   */
  public function dataUser() {
    $users = \Drupal::getContainer()
      ->get('flexinfo.queryuser.service')
      ->wrapperUsersByRoleNames(array('client', 'speaker', 'siteadmin'));

    foreach ($users as $user) {
      $tbody[] = [
        $user->getUsername(),
        implode(", ", $user->getRoles($exclude_locked_roles = TRUE)),
        "Edit",
      ];

    }

    $output = array(
      "thead" => [
        [
          "Name",
          "Role",
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
  public function tableSpeakerList($meeting_nodes = array(), $limit_row = NULL, $sort_boolean = FALSE) {
    $output = array();

    $speaker_users = \Drupal::getContainer()->get('flexinfo.queryuser.service')->wrapperUsersByRoleName('speaker');

    if (is_array($speaker_users)) {

      // first loop get top 10 user
      $top_speaker_users = array();
      foreach ($speaker_users as $key => $user) {
        $meeting_nodes_by_current_user = \Drupal::getContainer()
          ->get('flexinfo.querynode.service')
          ->meetingNodesBySpeakerUids($meeting_nodes, array($user->id()));

        $num_meeting_nodes = count($meeting_nodes_by_current_user);

        if ($num_meeting_nodes > 0) {
          $top_speaker_users[] = array(
            'num_meeting_nodes' => $num_meeting_nodes,
            'user' => $user,
            'nodes_by_current_user' => $meeting_nodes_by_current_user,
          );

          $sort_value[] = $num_meeting_nodes;
        }
      }

      // sort by set condition
      if (count($top_speaker_users) > 0 && $sort_boolean) {
        array_multisort($sort_value, SORT_DESC, $top_speaker_users);
      }

      // // cut table off to specify number
      // if ($limit_row) {
      //   if (count($top_speaker_users) > $limit_row) {
      //     $top_speaker_users = array_slice($top_speaker_users, 0, $limit_row);
      //   }
      // }

      /** - - - - - - second loop for top 10 - - - - - - - -  - - - - - - - - - - - - - - -  */
      $DashTabSpeaker = new DashTabSpeaker();

      foreach ($top_speaker_users as $user_array) {
        $user = $user_array['user'];

        $meeting_nodes_by_current_user = $user_array['nodes_by_current_user'];

        $num_meeting_nodes = $user_array['num_meeting_nodes'];

        if ($num_meeting_nodes > 0) {
          $signature_total = array_sum(
            \Drupal::getContainer()->get('flexinfo.field.service')
            ->getFieldFirstValueCollection($meeting_nodes_by_current_user, 'field_meeting_signature')
          );
          $evaluation_nums = array_sum(
            \Drupal::getContainer()->get('flexinfo.field.service')
            ->getFieldFirstValueCollection($meeting_nodes_by_current_user, 'field_meeting_evaluationnum')
          );

          $speaker_name_link = '<a data-ng-click="speakerPopUp(' . $user->id() . ')" class="md-raised pageinfo-btn-saved">';
            $speaker_name_link .= $user->getDisplayName();
          $speaker_name_link .= '</a>';

          $speaker_name_link =  $DashTabSpeaker->getHtmlModalLink($user->id(), $user->getDisplayName());
          $speaker_name_link .= $DashTabSpeaker->getModalAndTab($user->id(), $user->getDisplayName());
          // $speaker_name_link = '<p><a class="use-ajax" data-dialog-type="modal" href="' . base_path() . 'node/1">Search</a></p>';

          $output[] = array(
            'Name' => $speaker_name_link,
            'Events' => $num_meeting_nodes,
            'Reach' => $signature_total,
            'Responses' => $evaluation_nums,
            'Rating' => NULL,
            // 'Rating' => \Drupal::getContainer()->get('flexinfo.calc.service')->arrayAverageByMeetingNodes($meeting_nodes_by_current_user, 3006),
          );
        }
      }
    }

    return $output;
  }

  /**
   *
   */
  public function dataSpeakerList($meeting_nodes = array()) {

    // for ($i = 0; $i < 20; $i++) {
    //   $tbody[] = [
    //     "Speaker",
    //     rand(3, 20),
    //     rand(3, 20),
    //     rand(3, 20),
    //     rand(3, 20),
    //   ];
    // }

    $tbody = $this->tableSpeakerList($meeting_nodes);

    $output = array(
      "thead" => [
        [
          "Speaker",
          "# Events",
          "HCP Reach",
          "Responses",
          "Rating",
        ]
      ],
      "tbody" => $tbody
    );

    return $output;
  }

  /**
   * @return array
   */
  public function tableViewEvents($meeting_nodes = array()) {
    $output = array();

    if (is_array($meeting_nodes)) {
      foreach ($meeting_nodes as $node) {
        $program_entity = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetIdTermEntity($node, 'field_meeting_program');

        if ($program_entity) {
          $internal_url = Url::fromUserInput('/dashpage/meeting/snapshot/' . $node->id());
          $view_button = \Drupal::l(t('View'), $internal_url);

          $date_text  = '<span class="width-90 float-left">';
            $date_text .= \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValueDateFormat($node, 'field_meeting_date');
          $date_text .= '</span>';

          $program_text = $program_entity->getName();

          if(strlen($program_text) > 25) {
            $program_text  = '<span class="table-tooltip width-140">';
              $program_text .= Unicode::substr($program_entity->getName(), 0, 25) . '...';
              $program_text .= '<span class="table-tooltip-text">';
                $program_text .= $program_entity->getName();
              $program_text .= '</span>';
            $program_text .= '</span>';
          }

          $status_button = '<span class="color-fff">';
            $status_button .= '<i class="fa ' . \Drupal::getContainer()->get('flexinfo.node.service')->getMeetingStatusIcon($node) . ' fa-lg color-';
              $status_button .= \Drupal::getContainer()->get('flexinfo.node.service')->getMeetingStatusColorCode($node);
              $status_button .= '" aria-hidden="true">';
            $status_button .= '</i>';
          $status_button .= '</span>';

          $output[] = array(
            'DATE' => $date_text,
            'PROGRAM' => $program_text,
            'REP' => \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetIdUserName($node, 'field_meeting_rep'),
            'SPEAKER' => \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetIdUserName($node, 'field_meeting_speaker'),
            'HCP Reach' => \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue($node, 'field_meeting_signature'),
            'Responses' => \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue($node, 'field_meeting_evaluationnum'),
            'STATUS' => $status_button,
            'VIEW' => $view_button,
          );
        }
      }
    }

    return $output;
  }

  /**
   *
   */
  public function dataViewEvents($meeting_nodes = array()) {
    // for ($i = 0; $i < 20; $i++) {
    //   $tbody[] = [
    //     '10/31/2017',
    //     'Program Name',
    //     "Rep Name",
    //     "Speaker",
    //     rand(3, 20),
    //     rand(3, 20),
    //     'Status',
    //     'View'
    //   ];
    // }
    $tbody = $this->tableViewEvents($meeting_nodes);

    $output = array(
      "thead" => [
        [
          "Date",
          "Program Name",
          "Rep Name",
          "Speaker",
          "HCP Reach",
          "Responses",
          "Status",
          "View",
        ]
      ],
      "tbody" => $tbody
    );

    return $output;
  }

  /**
   * @return array
   */
  public function tableViewPrograms($meeting_nodes = array(), $limit_row = NULL, $sort_boolean = FALSE) {
    $output = array();

    $program_trees = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree('program', 0);
    if (is_array($program_trees)) {

      // first loop get top 10 Program
      $top_program_trees = array();
      foreach ($program_trees as $key => $term) {
        $meetings_nodes_by_current_term = \Drupal::getContainer()
          ->get('flexinfo.querynode.service')
          ->wrapperMeetingNodesByFieldValue($meeting_nodes, 'field_meeting_program', array($term->tid), 'IN');

        if (count($meetings_nodes_by_current_term) > 0) {
          $num_meetings_nodes = count($meetings_nodes_by_current_term);

          $top_program_trees[] = array(
            'term' => $term,
            'num_meetings_nodes' => $num_meetings_nodes,
          );

          // for sort order condition criteria
          $sort_value[] = $num_meetings_nodes;
        }
      }

      // sort by set condition
      if (count($top_program_trees) > 0 && $sort_boolean) {
        array_multisort($sort_value, SORT_DESC, $top_program_trees);
      }

      // cut table off to specify number
      if ($limit_row) {
        if (count($top_program_trees) > $limit_row) {
          $top_program_trees = array_slice($top_program_trees, 0, $limit_row);
        }
      }

      /** - - - - - - second loop for top 10 - - - - - - - -  - - - - - - - - - - - - - - -  */
      foreach ($top_program_trees as $key => $top_program) {
        $term = $top_program['term'];

        $program_entity = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($term->tid);

        $meetings_nodes_by_current_term = \Drupal::getContainer()
          ->get('flexinfo.querynode.service')
          ->wrapperMeetingNodesByFieldValue($meeting_nodes, 'field_meeting_program', array($term->tid), 'IN');

        if (count($meetings_nodes_by_current_term) > 0) {

          $signature_total = array_sum(
            \Drupal::getContainer()->get('flexinfo.field.service')
            ->getFieldFirstValueCollection($meetings_nodes_by_current_term, 'field_meeting_signature')
          );
          $evaluation_nums = array_sum(
            \Drupal::getContainer()->get('flexinfo.field.service')
            ->getFieldFirstValueCollection($meetings_nodes_by_current_term, 'field_meeting_evaluationnum')
          );

          $internal_url = Url::fromUserInput('/dashpage/program/snapshot/' . $term->tid);

          $output[] = array(
            'PROGRAM' => \Drupal::l($term->name, $internal_url),
            'THERAPEUTIC AREA' => \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetIdTermName($program_entity, 'field_program_theraparea'),
            'EVENTS' => count($meetings_nodes_by_current_term),
            'EVALUATIONS' => $evaluation_nums,
            'REACH' => $signature_total,
          );
        }
      }
    }

    return $output;
  }

  /**
   *
   */
  public function dataViewPrograms($meeting_nodes = array()) {
    // foreach ($terms as $term) {
    //   $tbody[] = [
    //     'Program Name',
    //     'BU',
    //     rand(3, 20),
    //     rand(3, 20),
    //     rand(3, 20),
    //     'View'
    //   ];
    // }
    $tbody = $this->tableViewPrograms($meeting_nodes);

    $output = array(
      "thead" => [
        [
          "Program Name",
          "BU",
          "# Events",
          "HCP Reach",
          "Responses",
        ]
      ],
      "tbody" => $tbody
    );

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
  public function generateUserAddLinkHtmlSnippet($bundle = NULL) {
    $url = Url::fromUserInput('/admin/people/create');
    $url = Url::fromUserInput('/flexform/entityadd/user/user');

    $output = $this->generateEntityAddLinkHtmlSnippet($url);
    return $output;
  }

  /**
   *
   */
  public function generateUserEditLinkHtmlSnippet($bundle = NULL) {
    $url = Url::fromUserInput('/flexform/entityadd/user/user');

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

  /**
   * {@inheritdoc}
   */
  public function userSnapshotObjectContent() {
    $output['contentSection'][] = $this->FlexpageBaseJson->getBlockHtmlSnippetWithoutTop(
      array(),
      $this->generateUserAddLinkHtmlSnippet()
    );

    $table_data = $this->dataUser();
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

  /**
   * {@inheritdoc}
   */
  public function programSnapshotObjectContent($section, $entity_id) {
    $table_data = $this->termProgram();
    $output = $this->commonVidSnapshotObjectContent($section, $entity_id, $table_data);

    return $output;
  }

  /** - - - - - - table - - - - - - - - - - - - - - - - - - - - - - - - - -  */

  /**
   * {@inheritdoc}
   */
  public function viewprogramsSnapshotObjectContent($section = NULL, $entity_id = NULL, $start = NULL, $end = NULL) {
    $DashpageObjectContent = new DashpageObjectContent();
    $meeting_nodes = $DashpageObjectContent->querySnapshotMeetingsNodes($section, $entity_id, $start, $end);

    $output['fixedSection'] = $this->FlexpageBaseJson->generateTileStyleOne($DashpageObjectContent->pageTopFixedSectionData($meeting_nodes));

    $table_data = $this->dataViewPrograms($meeting_nodes);
    $output['contentSection'][] = $this->generateCommonTable($table_data);
    return $output;
  }

  /**
   * {@inheritdoc}
   */
  public function vieweventsSnapshotObjectContent($section = NULL, $entity_id = NULL, $start = NULL, $end = NULL) {
    $DashpageObjectContent = new DashpageObjectContent();
    $meeting_nodes = $DashpageObjectContent->querySnapshotMeetingsNodes($section, $entity_id, $start, $end);

    $output['fixedSection'] = $this->FlexpageBaseJson->generateTileStyleOne($DashpageObjectContent->pageTopFixedSectionData($meeting_nodes));

    $table_data = $this->dataViewEvents($meeting_nodes);
    $output['contentSection'][] = $this->generateCommonTable($table_data);
    return $output;
  }

  /**
   * {@inheritdoc}
   */
  public function speakerlistSnapshotObjectContent($section = NULL, $entity_id = NULL, $start = NULL, $end = NULL) {
    $DashpageObjectContent = new DashpageObjectContent();
    $meeting_nodes = $DashpageObjectContent->querySnapshotMeetingsNodes($section, $entity_id, $start, $end);

    $output['contentSection'][] = $this->htmlSnippetSpeakerlist();

    $table_data = $this->dataSpeakerList($meeting_nodes);
    $output['contentSection'][] = $this->generateCommonTable($table_data);
    return $output;
  }

}
