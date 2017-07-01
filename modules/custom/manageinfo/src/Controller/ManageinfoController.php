<?php

/**
 * @file
 * Contains \Drupal\dashpage\Controller\ManageinfoController.
 */

namespace Drupal\manageinfo\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Url;

use Drupal\Component\Utility\Timer;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Drupal\dashpage\Content\DashpageContentGenerator;

use Drupal\manageinfo\Content\ManageinfoContentGenerator;
use Drupal\manageinfo\Content\ManageinfoJsonGenerator;
use Drupal\manageinfo\Content\ManageinfoSampleGenerator;
use Drupal\manageinfo\Content\ManageinfoTableContent;

/**
 * An example controller.
 */
class ManageinfoController extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  public function angularJson($tid) {
    $ManageinfoSampleGenerator = new ManageinfoSampleGenerator();
    $output = $ManageinfoSampleGenerator->angularJson();

    return new JsonResponse($output);

    $build = array(
      '#type' => 'markup',
      '#markup' => json_encode($output),
    );

    return $build;
  }

  /**
   *
   */
  public function angularFormTemplate($entity, $bundle, $nid) {
    $ManageinfoContentGenerator = new ManageinfoContentGenerator();
    $output = $ManageinfoContentGenerator->angularForm();

    $build = array(
      '#type' => 'markup',
      '#header' => 'header',
      '#markup' => $output,
      '#allowed_tags' => \Drupal::getContainer()->get('flexinfo.setting.service')->adminTag(),
      '#attached' => array(
        'library' => array(
          'manageinfo/angular_form',
        ),
      ),
    );

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function entityEditForm($entity, $nid) {
    if ($entity == 'user') {
      $user_roles = \Drupal::currentUser()->getRoles();
      if (\Drupal::currentUser()->id() == 1 || \Drupal::currentUser()->id() == $nid || in_array("siteadmin", $user_roles)) {
      }
      else {
        \Drupal::getContainer()->get('flexinfo.setting.service')->throwExceptionPage(404);
      }
    }

    $build = $this->angularFormTemplate($entity, $bundle = NULL, $nid);
    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function entityEditJson($entity, $nid) {
    $ManageinfoJsonGenerator = new ManageinfoJsonGenerator();

    if ($entity == 'node') {
      $output = $ManageinfoJsonGenerator->nodeEditJson($entity, $nid);
    }
    elseif ($entity == 'taxonomy_term') {
      $output = $ManageinfoJsonGenerator->termEditJson($nid);
    }
    elseif ($entity == 'user') {
      $output = $ManageinfoJsonGenerator->userEditJson($nid);
    }

    return new JsonResponse($output);

    $build = array(
      '#type' => 'markup',
      '#markup' => json_encode($output),
    );

    return $build;
  }

  /**
   * {@inheritdoc}
   * use Drupal\dashpage\Content\DashpageContentGenerator;
   * @return render table
   */
  public function standardJson($section, $entity_id, $start, $end) {
    $json_content_data = $this->standardJsonData($section, $entity_id, $start, $end);

    return new JsonResponse($json_content_data);

    $build = array(
      '#type' => 'markup',
      '#markup' => json_encode($json_content_data),
    );

    return $build;
  }

  /**
   * {@inheritdoc}
   * use Drupal\dashpage\Content\DashpageContentGenerator;
   * @return render table
   */
  public function standardJsonData($section, $entity_id, $start, $end) {
    $section = strtolower($section);

    $ManageinfoTableContent = new ManageinfoTableContent();

    $path_args = \Drupal::getContainer()->get('flexinfo.setting.service')->getCurrentPathArgs();

    $json_content_data = $ManageinfoTableContent->tableContentToCommonTable($section, $entity_id, $start, $end);

    return $json_content_data;
  }

  /**
   * {@inheritdoc}
   * use Drupal\dashpage\Content\DashpageContentGenerator;
   * @return render table
   */
  public function standardList($section, $entity_id, $start, $end) {
    // load and use DashpageContent templage
    $DashpageContentGenerator = new DashpageContentGenerator();
    $output = $DashpageContentGenerator->angularSnapshot();

    // $name = 'time_two';
    // Timer::start($name);

    $json_content_data = $this->standardJsonData($section, $entity_id, $start, $end);

    $build = array(
      '#type' => 'markup',
      '#header' => 'header',
      '#markup' => $output,
      '#allowed_tags' => \Drupal::getContainer()->get('flexinfo.setting.service')->adminTag(),
      '#attached' => array(
        'library' => array(
          'dashpage/angular_snapshot',
        ),
        'drupalSettings' => array(
          'manageinfo' => array(
            'manageinfoTable' => array(
              'jsonContentData' => $json_content_data,
            ),
          ),
        ),
      ),
    );

    // if (\Drupal::currentUser()->id() == 1) {
    //   Timer::stop($name);
    //   dpm('time_two ' . Timer::read($name) . 'ms');
    // }

    return $build;
  }

  /**
   * {@inheritdoc}
   * use Drupal\dashpage\Content\DashpageContentGenerator;
   * @return render table
   */
  public function standardTable($section, $entity_id, $start, $end) {
    // load and use DashpageContent templage
    $DashpageContentGenerator = new DashpageContentGenerator();
    $output = $DashpageContentGenerator->angularSnapshot();

    $build = array(
      '#type' => 'markup',
      '#header' => 'header',
      '#markup' => $output,
      '#allowed_tags' => \Drupal::getContainer()->get('flexinfo.setting.service')->adminTag(),
      '#attached' => array(
        'library' => array(
          'dashpage/angular_snapshot',
        ),
      ),
    );

    return $build;
  }

  /**
   * {@inheritdoc}
   * use Symfony\Component\HttpFoundation\RedirectResponse;
   */
  public function standardMenuItem($section, $entity_id) {
    $start = \Drupal::getContainer()->get('flexinfo.setting.service')->userStartTime();
    $end = \Drupal::getContainer()->get('flexinfo.setting.service')->userEndTime();

    $uri = '/manageinfo/' . $section . '/list/' . $entity_id . '/' . $start . '/' . $end;
    $url = Url::fromUserInput($uri)->toString();

    return new RedirectResponse($url);
  }

}
