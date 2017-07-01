<?php

/**
 * @file
 */

namespace Drupal\dashpage\Content;

use Drupal\Core\Controller\ControllerBase;

use Drupal\dashpage\Content\DashpageBlockGenerator;

/**
 * An example controller.
 $DashpageContentGenerator = new DashpageContentGenerator();
 $DashpageContentGenerator->angularPage();
 */
class DashpageContentGenerator extends ControllerBase {

  /**
   *
   */
  public function angularSnapshot() {
    $DashpageBlockGenerator = new DashpageBlockGenerator();

    $output = '';
    $output .= '<div id="pageInfoBase" data-ng-app="pageInfoBase" class="pageinfo-subpage-common margin-left-12 margin-right-12">';
      $output .= '<div data-ng-controller="PageInfoBaseController" class="row margin-0" ng-cloak>';
        $output .= '<div data-ng-controller="SaveAsPng">';

          $output .= '<div class="block-one bg-ffffff padding-bottom-20">';
            $output .= '<div class="row">';
              $output .= $DashpageBlockGenerator->topWidgetsFixed();
            $output .= '</div>';
          $output .= '</div>';

          $output .= '<div id="center" class="fixed-center"></div>';
          $output .= '<div id="charts-section" class="block-three row tab-content-block-wrapper">';
            $output .= '<div data-ng-repeat="block in pageData.contentSection" >';
              $output .= '<div class="{{block.class}}">';
                $output .= $DashpageBlockGenerator->contentBlockMaster();
              $output .= '</div>';
            $output .= '</div>';
          $output .= '</div>';

        $output .= '</div>';
      $output .= '</div>';
    $output .= '</div>';

    return $output;
  }

}
