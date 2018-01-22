<?php

/**
 * @file
 * Contains \Drupal\dashtable\Controller\DashtableController.
 */

namespace Drupal\dashtable\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Drupal\flextable\Controller\FlextableController;
use Drupal\flexpage\Controller\FlexpageController;

use Drupal\dashtable\Content\DashtableObjectContent;


/**
 * An example controller.
 */
class DashtableController extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  public function standardJson($section, $entity_id, $start, $end) {
    $object_content_data = $this->standardTableContent($section, $entity_id, $start, $end);

    return new JsonResponse($object_content_data);

    // debug output as JSON format
    $build = array(
      '#type' => 'markup',
      '#markup' => json_encode($object_content_data),
    );

    return $build;
  }

  /**
   * {@inheritdoc}
   * use Symfony\Component\HttpFoundation\RedirectResponse;
   */
  public function standardMenuItem($section, $entity_id) {
    $start = \Drupal::getContainer()->get('flexinfo.setting.service')->userStartTime();
    $end   = \Drupal::getContainer()->get('flexinfo.setting.service')->userEndTime();

    $uri = '/dashtable/' . $section . '/table/' . $entity_id . '/' . $start . '/' . $end;
    $url = Url::fromUserInput($uri)->toString();

    return new RedirectResponse($url);
  }

  /**
   * {@inheritdoc}
   */
  public function standardTableContent($section, $entity_id, $start, $end) {
    $DashtableObjectContent = new DashtableObjectContent();
    $output = $DashtableObjectContent->standardSnapshotObjectContent(strtolower($section), $entity_id);

    return $output;
  }

  /**
   * {@inheritdoc}
   */
  public function standardTable($section, $entity_id, $start, $end) {
    $object_content_data = $this->standardTableContent(strtolower($section), $entity_id);

    $FlexpageController = new FlexpageController();
    $build = $FlexpageController->angularSnapshotTemplate($object_content_data);

    return $build;
  }

}
