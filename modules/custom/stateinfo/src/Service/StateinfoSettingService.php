<?php

/**
 * @file
 * Contains Drupal\stateinfo\Service\StateinfoSettingService.php.
 */
namespace Drupal\stateinfo\Service;

/**
 *
 */
class StateinfoSettingService {

  /**
   *
   \Drupal::getContainer()->get('stateinfo.setting.service')->colorHslValue($result_value, $term);
   */
  public function colorHslValue($result_value, $term) {
    // default color #002840
    $output = 'hsl(203, 100%, 12.5%)';

    $min = \Drupal::getContainer()->get('flexinfo.field.service')
      ->getFieldFirstValue($term, 'field_item_minimun');

    if ($result_value < $min) {
      $diff = $min - $result_value;

      $max = \Drupal::getContainer()->get('flexinfo.field.service')
        ->getFieldFirstValue($term, 'field_item_maximun');

      $range = $max - $min;
      $average = ($max + $min) / 2;
      $step = 0.3;

      if ($range > 0) {
        $percentage = 1 - ($diff / $range * $step);
        if ($percentage > 1) {
          $percentage = 1;
        }

        $hsl_color_end = 30;
        $hsl_color_start = 0;

        $hsl_color_angle = ($hsl_color_end - $hsl_color_start) * $percentage;
        $hsl_value = $hsl_color_start + $hsl_color_angle;
        $hsl_value = number_format($hsl_value, 2);

        $lightness = number_format(((0.5 - ($percentage / 10)) * 100), 2);

        $saturation = number_format($percentage, 2);

        // $output = 'hsl(' . $hsl_value . ', 100%, 50%)';
        $output = 'hsl(' . $hsl_value . ', 100%, ' . $lightness . '%)';
        // $output = 'hsl(' . $hsl_value . ', '. $saturation . '%, ' . $lightness . '%)';
      }
    }

    return $output;
  }

  /**
   *
   \Drupal::getContainer()->get('stateinfo.setting.service')->convertTermAbbNameToNodeRecordFieldName($abb_name);
   */
  public function convertTermAbbNameToNodeRecordFieldName($abb_name) {
    $row_name = strtolower($abb_name);

    if (strpos($row_name, '%') !== false) {
      $row_name = str_replace('%', '_pct', $row_name);
    }
    if (strpos($row_name, '-') !== false) {
      $row_name = str_replace('-', '_', $row_name);
    }

    $field_name = 'field_record_' . $row_name;

    return $field_name;
  }

}
