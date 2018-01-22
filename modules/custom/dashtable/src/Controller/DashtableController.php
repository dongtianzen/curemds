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
    $FlextableController = new FlextableController();
    return $FlextableController->standardJson($section, $entity_id, $start, $end);
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
  public function standardTable($section, $entity_id, $start, $end) {
    $DashtableObjectContent = new DashtableObjectContent();
    $object_content_data = $DashtableObjectContent->standardSnapshotObjectContent(strtolower($section), $entity_id);

    $FlexpageController = new FlexpageController();
    $build = $FlexpageController->angularSnapshotTemplate($object_content_data);

    return $build;
  }

}
