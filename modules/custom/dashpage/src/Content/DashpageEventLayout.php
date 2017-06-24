<?php

/**
 * @file
 */

namespace Drupal\dashpage\Content;

use Drupal\Core\Controller\ControllerBase;


/**
 * An example controller.
 $DashpageEventLayout = new DashpageEventLayout();
 $DashpageEventLayout->blockEventsSnapshot();
 */
class DashpageEventLayout extends ControllerBase {

  /**
   *
   */
  public function blockEventsSnapshot($meeting_nodes = array(), $evaluationform_tid = NULL, $page_view = NULL) {
    $output = array();

    if ($evaluationform_tid) {
      $evaluationform_term = \Drupal::entityTypeManager()
        ->getStorage('taxonomy_term')
        ->load($evaluationform_tid);
    }

    if (empty($evaluationform_term)) {
      return $output;
    }
    if ($evaluationform_term->getVocabularyId() != 'evaluationform') {
      return $output;
    }

    $layout_tids = \Drupal::getContainer()
      ->get('flexinfo.field.service')
      ->getFieldAllTargetIds($evaluationform_term, 'field_evaluationform_layout');

    if (is_array($layout_tids) && count($layout_tids) > 0) {
      $output = $this->blockEventsSnapshotCustomDefined($meeting_nodes, $layout_tids);
    }
    else {
      $output = $this->blockEventsSnapshotAuto($meeting_nodes, $evaluationform_term, $page_view);
    }

    $output = array_merge($output, $this->blockEventsSnapshotLearningObjective($meeting_nodes, $evaluationform_term));

    if ($evaluationform_tid == 5313) {
      $output = array_merge($output, $this->blockEventsSnapshotPrePostQuestions($meeting_nodes, $evaluationform_term));
    }

    $output = array_merge($output, $this->blockEventsSnapshotSelectQuestions($meeting_nodes, $evaluationform_term));
    $output = array_merge($output, $this->blockEventsSnapshotComments($meeting_nodes, $evaluationform_term));
    return $output;
  }

  /**
   *
   */
  public function blockEventsSnapshotAuto($meeting_nodes = array(), $evaluationform_term = NULL, $page_view = NULL) {
    $output = array();
    $DashpageJsonGenerator = new DashpageJsonGenerator();

    $question_tids = \Drupal::getContainer()
      ->get('flexinfo.field.service')
      ->getFieldAllTargetIds($evaluationform_term, 'field_evaluationform_questionset');

    $question_tids = \Drupal::getContainer()->get('flexinfo.queryterm.service')->wrapperQuestionTidsByRadiosByLearningObjective($question_tids, FALSE);

    if (is_array($question_tids) && count($question_tids) > 0) {

      $question_terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadMultiple($question_tids);
      foreach ($question_terms as $question_term) {

        // radios is 2493
        $question_tid  = $question_term->id();
        $question_scale = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue($question_term, 'field_queslibr_scale');

        // $chart_type_method like "getChartDoughnut"
        $chart_type_method = \Drupal::getContainer()->get('flexinfo.chart.service')->getChartTypeFunctionNameByQuestion($question_term);

        // $chart_render_method like "renderChartPieDataSet"
        $chart_render_method = \Drupal::getContainer()->get('flexinfo.chart.service')->getChartTypeRenderFunctionByQuestion($question_term);

        // chart Data
        $pool_data = \Drupal::getContainer()->get('flexinfo.querynode.service')->wrapperPoolAnswerIntDataByQuestionTid($meeting_nodes, $question_tid);
        $pool_label = \Drupal::getContainer()->get('flexinfo.chart.service')->generateChartLabelForRadioQuestion($pool_data);

        $chart_data = \Drupal::getContainer()->get('flexinfo.chart.service')->{$chart_render_method}($pool_data, $pool_label);

        // block_option
        $chart_block_title = \Drupal::getContainer()->get('flexinfo.chart.service')->getChartTitleByQuestion($question_term);

        $block_option = $this->getBlockOption($pool_data, $question_term, $chart_type_method);
        $chart_options = $this->getChartOption($pool_data, $question_term, $chart_type_method);

        // check if has more than one tab
        $multiple_tab = FALSE;
        if ($page_view == 'meeting_view') {
          if ($question_term->id() == 3006) {    // 3006 is How effective was the speaker?
            if ($question_term->id() == 3006) {

              $meeting_node = reset($meeting_nodes);
              $speaker_uids = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldAllTargetIds($meeting_node, 'field_meeting_speaker');
              if (count($speaker_uids) > 1) {
                $multiple_tab = TRUE;
              }
            }
          }
        }

        // output standard block or tab block
        if (!$multiple_tab) {
          $output[] = $DashpageJsonGenerator->getBlockOne(
            $block_option,
            $DashpageJsonGenerator->{$chart_type_method}($chart_options, $chart_data)
          );
        }
        else {
          $block_option_3006 = array(
            'class' => "col-md-6",
            'top'  => array(
              'value' => $chart_block_title = \Drupal::getContainer()->get('flexinfo.chart.service')->getChartTitleByQuestion($question_term),
            ),
          );

          $speaker_users = \Drupal::entityManager()->getStorage('user')->loadMultiple($speaker_uids);
          foreach ($speaker_users as $speaker_user) {

            $pool_data = \Drupal::getContainer()->get('flexinfo.querynode.service')->wrapperPoolAnswerIntDataByQuestionTidByReferUid($meeting_nodes, $question_tid, $speaker_user->id());

            // $chart_render_method is renderChartPieDataSet
            $chart_data = \Drupal::getContainer()->get('flexinfo.chart.service')->{$chart_render_method}($pool_data, $pool_label);
            $chart_options = $this->getChartOption($pool_data, $question_term, $chart_type_method);
            $chart_options['chartOptions']["responsiveMaxHeight"] = 280;

            $legends = \Drupal::getContainer()->get('flexinfo.chart.service')->renderChartPieLegend($pool_data, $question_term);
            $middle_bottom = \Drupal::getContainer()->get('flexinfo.chart.service')->renderChartBottomFooter($pool_data, $question_term, FALSE, TRUE);

            $block_content[] = $DashpageJsonGenerator->getBlockTabContainer(
              array(
                'title' => $speaker_user->getUsername(),
                'middle' => array(
                  'middleMiddle' => array(
                    'middleMiddleMiddleClass' => "col-md-8",
                    'middleMiddleRightClass' => "col-md-4",
                    'middleMiddleRight' => $legends,
                  ),
                  "middleBottom" => $middle_bottom,
                ),
              ),
              $DashpageJsonGenerator->{$chart_type_method}($chart_options, $chart_data)    // $chart_type_method is getChartPie
            );
          }

          $output[] = $DashpageJsonGenerator->getBlockMultiTabs(
            $block_option_3006,
            $block_content
          );
        }
      }

      $num_question_tids = count($question_tids);
      $remainder = fmod($num_question_tids, 2);
      if ($remainder == 1) {
        // change last three as "col-md-4"
        for ($i = 0; $i < 3; $i++) {
          $output[$num_question_tids - $i - 1]['class'] = "col-md-4";
        }
      }
      if ($num_question_tids == 1) {
        $output[0]['class'] = "col-md-12";
      }

    }

    return $output;
  }

  /**
   *
   */
  public function blockEventsSnapshotCustomDefined($meeting_nodes = array(), $layout_tids = array()) {
    $output = array();

    $DashpageJsonGenerator = new DashpageJsonGenerator();
    if (is_array($layout_tids)) {

      $layout_terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadMultiple($layout_tids);
      foreach ($layout_terms as $layout_term) {

        // css class
        $grid_class  = "col-md-6";
        $grid_number = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue($layout_term, 'field_layout_gridcolumn');
        if ($grid_number > 0) {
          $grid_class = "col-md-" . $grid_number;
        }

        // chart_type
        $chart_type_method = 'getChartDoughnut';
        $chart_render_method = 'renderChartDoughnutDataSet';

        $chart_type_tid  = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetId($layout_term, 'field_layout_charttype');
        $chart_type_term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($chart_type_tid);
        if ($chart_type_term) {
          $chart_type_method = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue($chart_type_term, 'field_charttype_functionname');
          $chart_render_method = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue($chart_type_term, 'field_charttype_renderfunction');
        }

        $question_tid = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetId($layout_term, 'field_layout_question');

        // chart Data
        $pool_data  = \Drupal::getContainer()->get('flexinfo.querynode.service')->wrapperPoolAnswerIntDataByQuestionTid($meeting_nodes, $question_tid);
        $pool_label = \Drupal::getContainer()->get('flexinfo.chart.service')->generateChartLabelForRadioQuestion($pool_data);

        $chart_data = \Drupal::getContainer()->get('flexinfo.chart.service')->{$chart_render_method}($pool_data, $pool_label);

        $chart_block_title = \Drupal::getContainer()->get('flexinfo.chart.service')->getChartTitleByLayoutTerm($layout_term);

        $block_option = array(
          'class' => $grid_class,
          'top'  => array(
            'value' => $chart_block_title,
          ),
        );

        $output[] = $DashpageJsonGenerator->getBlockOne(
          $block_option,
          $DashpageJsonGenerator->{$chart_type_method}(NUll, $chart_data)
        );
      }
    }

    return $output;
  }

  /**
   *
   */
  public function blockEventsSnapshotSelectQuestions($meeting_nodes = array(), $evaluationform_term = NULL) {
    $output = array();
    $DashpageJsonGenerator = new DashpageJsonGenerator();

    $question_tids = \Drupal::getContainer()
      ->get('flexinfo.field.service')
      ->getFieldAllTargetIds($evaluationform_term, 'field_evaluationform_questionset');

    // selectkey tid is 2494
    $selectkey_question_tids = \Drupal::getContainer()
      ->get('flexinfo.queryterm.service')->wrapperStandardTidsByTidsByField($question_tids, 'questionlibrary', 'field_queslibr_fieldtype', 2494);

    if ($selectkey_question_tids) {
      $selectkey_question_terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadMultiple($selectkey_question_tids);

      $allow_select_key_question_array = array(
        2594,
        2627,
        2628,
        2629,
      );

      foreach ($selectkey_question_terms as $selectkey_question_term) {

        if (!in_array($selectkey_question_term->id(), $allow_select_key_question_array)) {
          continue;
        }

        $table_data = array();

        $pool_data = \Drupal::getContainer()->get('flexinfo.querynode.service')->wrapperPoolAnswerTermDataByQuestionTid($meeting_nodes, $selectkey_question_term->id());
        $pool_data_count = array_count_values($pool_data);

        if ($pool_data_count) {
          foreach ($pool_data_count as $key => $row) {

            $table_data[] = array(
              'Name' => $key,
              'Number' => $row,
              'Percentage' => \Drupal::getContainer()->get('flexinfo.calc.service')->getPercentageDecimal($row, array_sum($pool_data_count), 0) . '%',
            );
          }

          $table_content = \Drupal::getContainer()
            ->get('flexinfo.chart.service')->convertContentToTableArray($table_data);

          $block_option = array(
            'class' => "col-md-12",
            'blockClasses' => "height-400 overflow-visible",
            'type' => "commonTable",
            'top'  => array(
              'enable' => TRUE,
              'value' => $selectkey_question_term->getName(),
            ),
          );

          $DashpageJsonGenerator = new DashpageJsonGenerator();
          $output[] = $DashpageJsonGenerator->getBlockOne(
            $block_option,
            $DashpageJsonGenerator->getCommonTable(NULL, $table_content)
          );
        }
      }
    }

    return $output;
  }

  /**
   * only for program 5145 - Simplify Diabetes Management: Addressing Top Clinical Questions Overview
   */
  public function blockEventsSnapshotPrePostQuestions($meeting_nodes = array(), $evaluationform_term = NULL) {
    $output = array();
    $DashpageJsonGenerator = new DashpageJsonGenerator();

    $question_array = NULL;
    $question_array[] = array(
      'module_name' => 'GENERAL TYPE 2 DIABETES MANAGEMENT/ORAL PHARMACOLOGICAL AGENTS',
      'questions' => array(
        array(
          'pre'  => 5161,
          'post' => 5189,
          'answer' => 'c. Antihyperglycemic agent with demonstrated CV outcome benefit (empagliflozin, liraglutide)',
        ),
        array(
          'pre'  => 5162,
          'post' => 5190,
          'answer' => 'e. All of the above',
        ),
        array(
          'pre'  => 5163,
          'post' => 5191,
          'answer' => 'b. <7.0%',
        ),
        array(
          'pre'  => 5164,
          'post' => 5192,
          'answer' => 'e. All of the above',
        ),
        array(
          'pre'  => 5165,
          'post' => 5193,
          'answer' => 'e. All of the above',
        ),
      ),
    );
    $question_array[] = array(
      'module_name' => 'GLP-1 RECEPTOR AGONISTS',
      'questions' => array(
        array(
          'pre'  => 5217,
          'post' => 5221,
          'answer' => 'a. Exenatide twice daily (BID)',
        ),
        array(
          'pre'  => 5218,
          'post' => 5222,
          'answer' => 'd. All of the above',
        ),
        array(
          'pre'  => 5219,
          'post' => 5223,
          'answer' => 'e. All of the above',
        ),
        array(
          'pre'  => 5220,
          'post' => 5224,
          'answer' => 'e. A and B',
        ),
      ),
    );
    $question_array[] = array(
      'module_name' => 'INSULIN',
      'questions' => array(
        array(
          'pre'  => 5263,
          'post' => 5264,
          'answer' => 'c. Insulin should be initiated immediately',
        ),
        array(
          'pre'  => 5265,
          'post' => 5266,
          'answer' => 'd. Thiazolidinediones',
        ),
        array(
          'pre'  => 5267,
          'post' => 5268,
          'answer' => 'a. Glargine U100',
        ),
        array(
          'pre'  => 5269,
          'post' => 5270,
          'answer' => 'c. Insulin',
        ),
        array(
          'pre'  => 5271,
          'post' => 5272,
          'answer' => 'c. Negatively impact CV outcomes',
        ),
      ),
    );

    foreach ($question_array as $question_subset) {

      $table_data = array();
      $num_key = 1;
      foreach ($question_subset['questions'] as $question_row) {

        $pool_data_pre = \Drupal::getContainer()
          ->get('flexinfo.querynode.service')
          ->wrapperPoolAnswerTermDataByQuestionTid($meeting_nodes, $question_row['pre']);

        $count_pre = array_count_values($pool_data_pre);
        $correct_answer_pre = 0;
        if (isset($count_pre[$question_row['answer']])) {
          $correct_answer_pre = $count_pre[$question_row['answer']];
        }

        $pool_data_post = \Drupal::getContainer()
          ->get('flexinfo.querynode.service')
          ->wrapperPoolAnswerTermDataByQuestionTid($meeting_nodes, $question_row['post']);

        $count_post = array_count_values($pool_data_post);
        $correct_answer_post = 0;
        if (isset($count_post[$question_row['answer']])) {
          $correct_answer_post = $count_post[$question_row['answer']];
        }

        $question_term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($question_row['pre']);

        $table_data[] = array(
          'Name' => $num_key . '. ' . str_replace('PRE-TEST', '', $question_term->getName()),
          'Pre'  => $correct_answer_pre. ' of ' . count($pool_data_pre),
          'Post' => $correct_answer_post . ' of ' . count($pool_data_post),
        );

        $num_key++;
      }

      $table_content = \Drupal::getContainer()
        ->get('flexinfo.chart.service')->convertContentToTableArray($table_data);

      $block_option = array(
        'class' => "col-md-12",
        'blockClasses' => "height-400 overflow-visible",
        'type' => "commonTable",
        'top'  => array(
          'enable' => TRUE,
          'value' => $question_subset['module_name'],
        ),
      );

      $DashpageJsonGenerator = new DashpageJsonGenerator();
      $output[] = $DashpageJsonGenerator->getBlockOne(
        $block_option,
        $DashpageJsonGenerator->getCommonTable(NULL, $table_content)
      );
    }

    return $output;
  }

  /**
   *
   */
  public function blockEventsSnapshotComments($meeting_nodes = array(), $evaluationform_term = NULL) {
    $output = array();
    $DashpageJsonGenerator = new DashpageJsonGenerator();

    $question_tids = \Drupal::getContainer()
      ->get('flexinfo.field.service')
      ->getFieldAllTargetIds($evaluationform_term, 'field_evaluationform_questionset');

    // textfield tid is 2496
    $textfield_question_tids = \Drupal::getContainer()
      ->get('flexinfo.queryterm.service')->wrapperStandardTidsByTidsByField($question_tids, 'questionlibrary', 'field_queslibr_fieldtype', 2496);

    $comments = array();
    if (is_array($question_tids)) {
      $textfield_question_terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadMultiple($textfield_question_tids);

      foreach ($textfield_question_terms as $textfield_question_term) {
        $pool_data = \Drupal::getContainer()->get('flexinfo.querynode.service')->wrapperPoolAnswerTextDataByQuestionTid($meeting_nodes, $textfield_question_term->id());

        $question_comments = NULL;
        if (isset($pool_data) && count($pool_data) > 0) {
          $question_comments .= '<div class="panel-body bg-ffffff font-size-12 margin-left-12">';
            foreach ($pool_data as $key => $row) {
              $question_comments .= '<li>' . $row . '</li>';
            }
          $question_comments .= '</div">';

          $block_option = array(
            'class' => "col-md-12",
            'top'  => array(
              'value' => $textfield_question_term->getName(),
            ),
          );

          $output[] = $DashpageJsonGenerator->getBlockHtmlSnippet($block_option, $question_comments);
        }
      }
    }

    return $output;
  }

  /**
   *
   */
  public function blockEventsSnapshotLearningObjective($meeting_nodes = array(), $evaluationform_term = NULL) {
    $question_col_class_array = array();

    if ($evaluationform_term->id() == 2885) {
      $question_col_class_array = array(
        "col-md-6",
        "col-md-6",
        "col-md-6",
        "col-md-6",
        "col-md-6",
        "col-md-6",
        "col-md-6",
        "col-md-6",
      );
    }

    $output = $this->blockEventsSnapshotLearningObjectiveStandard($meeting_nodes, $evaluationform_term, $question_col_class_array);

    return $output;
  }

  /**
   *
   */
  public function blockEventsSnapshotLearningObjectiveStandard($meeting_nodes = array(), $evaluationform_term = NULL, $question_col_class_array = array()) {
    $output = array();
    $DashpageJsonGenerator = new DashpageJsonGenerator();

    $question_tids = \Drupal::getContainer()
      ->get('flexinfo.field.service')
      ->getFieldAllTargetIds($evaluationform_term, 'field_evaluationform_questionset');

    $question_tids = \Drupal::getContainer()->get('flexinfo.queryterm.service')->wrapperQuestionTidsByRadiosByLearningObjective($question_tids, TRUE);

    if (is_array($question_tids) && $question_tids) {

      // chart_type
      $chart_type_method = 'getChartBar';
      $chart_render_method = 'renderChartBarOneGroupDataSet';

      $question_terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadMultiple($question_tids);

      $question_num = 0;
      foreach ($question_terms as $question_term) {
        $question_tid  = $question_term->id();

        // chart Data
        $pool_data = \Drupal::getContainer()->get('flexinfo.querynode.service')->wrapperPoolAnswerIntDataByQuestionTid($meeting_nodes, $question_tid);
        $pool_label = \Drupal::getContainer()->get('flexinfo.chart.service')->generateChartLabelForRadioQuestion($pool_data);

        $chart_data = \Drupal::getContainer()->get('flexinfo.chart.service')->{$chart_render_method}($pool_data, $pool_label);

        $chart_block_title = '<span class="legend-text text-center float-left height-32 padding-left-24 padding-right-12 font-size-12">';
          $chart_block_title .= $question_term->getName();
        $chart_block_title .= '</span>';

        $question_col_class = "col-md-4";
        if (isset($question_col_class_array[$question_num])) {
          $question_col_class = $question_col_class_array[$question_num];
        }

        $container_content[] = $DashpageJsonGenerator->getBlockOne(
          array(
            'class' => $question_col_class,
            'type' => "chart",
            'middle' => array(
              'middleBottom' => $chart_block_title,
            ),
          ),
          $DashpageJsonGenerator->getChartBar(
            array(
              "chartType" => "Bar",
              "chartOptions" => array('barValueSpacing' => "10"),
            ),
            $chart_data
          )
        );

        $pool_data_total[] = $pool_data;

        $question_num++;
      }

      if (!isset($question_col_class_array[0])) {

        // override $container_content for md class
        $num_question_tids = count($question_tids);
        if ($num_question_tids == 1) {
          $container_content[0]['class'] = "col-md-12";
        }
        else {
          // change last as "col-md-6"
          $remainder = fmod($num_question_tids, 3);

          if ($remainder == 1) {
            for ($i = 0; $i < 4; $i++) {
              $container_content[$num_question_tids - $i - 1]['class'] = "col-md-6";
            }
          }
          elseif ($remainder == 2) {
            for ($i = 0; $i < 2; $i++) {
              $container_content[$num_question_tids - $i - 1]['class'] = "col-md-6";
            }
          }
        }
      }

      // convert $pool_data_total
      for ($i = 0; $i < 5; $i++) {
        $pool_data_sum[$i] = array_sum(array_column($pool_data_total, $i));
      }

      // block_option
      $block_option = array(
        'class' => "col-md-12",
        'top'  => array(
          'value' => t('Learning Objective'),
        ),
        'middle' => array(
          'middleBottom' => 6666,   // it does not work
        ),
        'bottom' => array(
          'value' => \Drupal::getContainer()->get('flexinfo.chart.service')->renderChartBottomLegend($pool_data_sum, $pool_label),
        )
      );

      $output[] = $DashpageJsonGenerator->getBlockMultiContainer(
        $block_option,
        $container_content
      );
    }

    return $output;
  }

  /**
   *
   */
  public function getBlockOption($pool_data, $question_term, $chart_type_method) {
    $grid_class = "col-md-6";

    $chart_block_title = \Drupal::getContainer()->get('flexinfo.chart.service')->getChartTitleByQuestion($question_term);
    $middle_bottom = \Drupal::getContainer()->get('flexinfo.chart.service')->renderChartBottomFooter($pool_data, $question_term, TRUE, TRUE);

    $block_option = array(
      'class' => $grid_class,
      'top'  => array(
        'value' => $chart_block_title,
      ),
    );

    // pie chart
    if ($chart_type_method == 'getChartPie') {
      $legends = \Drupal::getContainer()->get('flexinfo.chart.service')->renderChartPieLegend($pool_data, $question_term);

      $block_option = array(
        'class' => $grid_class,
        'middle' => array(
          'middleMiddle' => array(
            'middleMiddleMiddleClass' => "col-md-8",
            'middleMiddleRightClass' => "col-md-4",
            'middleMiddleRight' => $legends,
          ),
          "middleBottom" => $middle_bottom,
        ),
        'top'  => array(
          'value' => $chart_block_title,
        ),
      );
    }
    elseif ($chart_type_method == 'getChartDoughnut') {
      $legends = \Drupal::getContainer()
        ->get('flexinfo.chart.service')
        ->renderChartDoughnutLegend($pool_data, $question_term);

      $block_option = array(
        'class' => $grid_class,
        'middle' => array(
          'middleMiddle' => array(
            'middleMiddleMiddleClass' => "col-md-8",
            'middleMiddleRightClass' => "col-md-4",
            'middleMiddleRight' => $legends,
          ),
          "middleBottom" => $middle_bottom,
        ),
        'top'  => array(
          'value' => $chart_block_title,
        ),
      );
    }

    return $block_option;
  }

  /**
   *
   */
  public function getChartOption($pool_data, $question_term, $chart_type_method) {
    $chart_option = array();

    if ($chart_type_method == 'getChartDoughnut') {
      $legends = array();
      $middle_text = NULL;

      if (isset($pool_data[1])) {
        $legends = \Drupal::getContainer()->get('flexinfo.calc.service')->getPercentageDecimal($pool_data[1], array_sum($pool_data), 0) . '%';
        $middle_text = "No Bias";
      }

      $chart_option = array(
        'chartOptions' => array(
          'crossText' => array(
            "",
            $middle_text,
            $legends,
          ),
        ),
      );

    }

    return $chart_option;
  }

}
