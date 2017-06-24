<?php

/**
 * @file
 */

namespace Drupal\manageinfo\Content;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;

use Drupal\dashpage\Content\DashpageJsonGenerator;
use Drupal\terminfo\Controller\TerminfoJsonController;

/**
 * An example controller.
 */
class ManageinfoTableContent extends ControllerBase {

  /**
   * {@inheritdoc}
   * @return php object, not JSON
   */
  public function tableContentToCommonTable($section, $entity_id) {
    $table_content = $this->tableContentData($section, $entity_id);

    $table_value = \Drupal::getContainer()->get('flexinfo.chart.service')->convertContentToTableArray($table_content);

    $DashpageJsonGenerator = new DashpageJsonGenerator();
    $output['fixedSection'][] = $this->tableHeaderToCommonTable($section, $entity_id);
    $output['contentSection'] = array(
      $DashpageJsonGenerator->getBlockOne(
        array('class' => "col-md-12", 'type' => "commonTable", 'blockClasses' => "height-400 overflow-visible", 'top' => array('enable' => FALSE)),
        $DashpageJsonGenerator->getCommonTable($option = array(), $table_value)
      ),
    );

    return $output;
  }

  /**
   * {@inheritdoc}
   * @return php object, not JSON
   */
  public function tableContentToCommonPhpTable($section, $entity_id) {
    $DashpageJsonGenerator = new DashpageJsonGenerator();
    $output['fixedSection'][] = $this->tableHeaderToCommonTable($section, $entity_id);
    $output['contentSection'] = array(
      $DashpageJsonGenerator->getBlockOne(
        array(
          'class' => "col-md-12",
          'blockClasses' => "height-400 overflow-visible",
          'type' => "commonPhpTable",     // going to contentRenderPhpTable()
          'top' => array(
            'enable' => FALSE,
          )
        ),
        $DashpageJsonGenerator->getCommonTable(NUll, NULL)
      ),
    );

    return $output;
  }

  /**
   * {@inheritdoc}
   * @return php object, not JSON
   */
  public function tableContentData($section, $entity_id) {
    $section = strtolower($section);

    $flexinfoEntityService = \Drupal::getContainer()->get('flexinfo.entity.service');
    $TerminfoJsonController = new TerminfoJsonController($flexinfoEntityService);
    $output = $TerminfoJsonController->basicCollectionContent($section, $entity_id);

    return $output;
  }

  /**
   * {@inheritdoc}
   * @return php object, not JSON
   */
  public function tableHeaderToCommonTable($section, $entity_id) {
    $title = $this->tableHeaderTitle($section, $entity_id);

    $vocabularies_list = taxonomy_vocabulary_get_names();

    $internal_url = '';
    switch ($section) {
      case 'meeting':
      case 'meetingbyprogram':
        $internal_url = base_path() . 'manageinfo/node/meeting/add/form/new';
        break;

      case 'user':
        $internal_url = base_path() . 'manageinfo/user/bundle/add/form/user';
        break;

      default:
        if (in_array($section, $vocabularies_list)) {
          $internal_url = base_path() . 'manageinfo/taxonomy_term/bundle/add/form/' . $section;
        }
        break;
    }

    $output = '';
    $output .= '<div class="margin-left-12 float-left">';
      $output .= '<div class="md-headline display-inline-block">';
        $output .= '<span class="padding-top-12 margin-left-40 font-size-20">';
          $output .= $title;
        $output .= '</span>';
      $output .= '</div>';

      if ($internal_url) {
        $output .= '<span class="table-top-add-button padding-left-4">';
          $output .= '<a class="font-size-16 md-fab md-warn md-button md-ink-ripple" href="' . $internal_url . '">';
            $output .= '<md-icon class="fa fa-plus" aria-hidden="true">';
            $output .= '</md-icon>';
          $output .= '</a>';
        $output .= '</span>';
      }

    $output .= '</div>';
    $output .= '<div class="col-md-12 height-2 bg-00a9e0 margin-top-20"></div>';

    $DashpageJsonGenerator = new DashpageJsonGenerator();
    $output = $DashpageJsonGenerator->getHtmlSnippet(NULL, $output);

    return $output;
  }

  /**
   *
   */
  public function tableHeaderTitle($section, $entity_id) {
    $title = ucwords($section);

    $entity = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($entity_id);
    $entity_name = $entity_id;
    if (method_exists($entity, 'getName')) {
      $entity_name = $entity->getName();
    }

    switch ($section) {
      case 'evaluationformbyquestion':
        $title = "Evaluation Form By " . $entity_name;
        break;

      case 'questionlibrarybyevaluationform':
        $title = "Question By " . $entity_name;
        break;

      case 'Meetingbyprogram':
        $title = 'Meeting By Program - ' . $entity_name;
        break;

      default:
        break;
    }

    return $title;
  }

}
