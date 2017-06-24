<?php

namespace Drupal\dashpage\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Listens to the dynamic route events.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  public function alterRoutes(RouteCollection $collection) {
    if ($route = $collection->get('manageinfo.demo.page')) {
      $route->setDefault('_title', 'Demo page title by alterRoutes');
    }
  }

}
