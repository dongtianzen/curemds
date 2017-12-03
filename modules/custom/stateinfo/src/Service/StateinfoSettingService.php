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
    $max = \Drupal::getContainer()->get('flexinfo.field.service')
      ->getFieldFirstValue($term, 'field_item_maximun');

    if ($result_value < $min) {
      $diff = $min - $result_value;

      $range = $max - $min;
      $average = ($max + $min) / 2;
      $step = 0.3;

      if ($range > 0) {
        $percentage = 1 - ($diff / $range * $step);
        if ($percentage > 1) {
          $percentage = 1;
        }

        $output = $this->getColorHslValueByPercentageStep($percentage);
      }
    }

    return $output;
  }

  /**
   *
   */
  public function colorRgbValue($result_value, $term) {
    // default color #002840
    $output = '002840';

    $min = \Drupal::getContainer()->get('flexinfo.field.service')
      ->getFieldFirstValue($term, 'field_item_minimun');
    $max = \Drupal::getContainer()->get('flexinfo.field.service')
      ->getFieldFirstValue($term, 'field_item_maximun');

    if ($result_value < $min) {
      $diff = $min - $result_value;

      $range = $max - $min;
      $average = ($max + $min) / 2;

      if ($range > 0) {
        $percentage = ($result_value / $min) + (($average - $min) / $average);

        dpm($result_value . ' - - - ' . $min . ' - - - ' . $percentage);
        $output = $this->getColorRgbValueByPercentageStep($percentage);
      }
    }

    return $output;
  }

  /**
   *
   */
  public function getColorRgbValueByPercentageStep($percentage = 1) {
    // default color #002840
    $output = '002840';

    if ($percentage < 1) {
      $color_array = $this->getColorPlateRgb();
      $num_of_color = count($color_array);

      $color_percentage_step = \Drupal::getContainer()
        ->get('flexinfo.calc.service')
        ->getPercentage(1, $num_of_color) / 100;

      $color_percentage = $color_percentage_step;
      for ($i = 0; $i < $num_of_color; $i++) {
        dpm($color_percentage);
        if ($percentage < $color_percentage) {
          $output = $color_array[$i];

          break;
        }
        $color_percentage += $color_percentage_step;
      }
    }

    $output = '#' . $output;

    return $output;
  }

  /**
   * @see color plate http://www.rapidtables.com/web/color/RGB_Color.htm
   */
  public function getColorPlateRgb() {
    $output = array(
      'ff3333',      // red
      'ff9933',
      'ffff33',   // yellow
      // '99ff33',      // green
      // '66ff33',
      // '33ff33',
      '33ffff',      // light blue
      '3399ff',      // blue
      '9933ff',      // purple
    );

    return $output;
  }

  /**
   *
   */
  public function getColorRgbArray() {
    $output = array();

    $colors = array(50, 70, 90, 110, 120);
    $low = 0;
    $high = 255;
    $grey = 240;
    for ($i = 0; $i < 128; $i++) {
      /** Red */
      if ($i < $colors[0]) {
        $R = $grey + ($low - $grey) * $i / $colors[0];
      }
      elseif ($i < $colors[2]) {
        $R = $low;
      }
      elseif ($i < $colors[3]) {
        $R = $low + ($high - $low) * ($i - $colors[2]) / ($colors[3] - $colors[2]);
      }
      else {
        $R = $high;
      }
      /** Green */
      if ($i < $colors[0]) {
        $G = $grey + ($low - $grey) * $i / $colors[0];
      }
      elseif ($i < $colors[1]) {
        $G = $low + ($high - $low) * ($i - $colors[0]) / ($colors[1] - $colors[0]);
      }
      elseif ($i < $colors[3]) {
        $G = $high;
      }
      else {
        $G = $high - ($high - $low) * ($i - $colors[3]) / (127 - $colors[3]);
      }
      /** Blue */
      if ($i < $colors[0]) {
        $B = $grey + ($high - $grey) * $i / $colors[0];
      }
      elseif ($i < $colors[1]) {
        $B = $high;
      }
      elseif ($i < $colors[2]) {
        $B = $high - ($high - $low) * ($i - $colors[1]) / ($colors[2] - $colors[1]);
      }
      else {
        $B = $low;
      }
      echo "R:".(int)$R.";&nbsp;&nbsp;&nbsp;&nbsp;G:".(int)$G.";&nbsp;&nbsp;&nbsp;&nbsp;B:".(int)$B;
      echo "<br />";
    }

    return $output;
  }

  /**
   *
   */
  public function getColorHslValueByPercentage($percentage = 1) {
    // default color #002840
    $output = 'hsl(203, 100%, 12.5%)';

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

    return $output;
  }

  /**
   *
   \Drupal::getContainer()->get('stateinfo.setting.service')->convertTermAbbNameToStandardName($abb_name);
   */
  public function convertTermAbbNameToStandardName($abb_name) {
    $row_name = strtolower($abb_name);

    if (strpos($row_name, '%') !== false) {
      $row_name = str_replace('%', '_pct', $row_name);
    }
    if (strpos($row_name, '-') !== false) {
      $row_name = str_replace('-', '_', $row_name);
    }

    return $row_name;
  }

  /**
   *
   \Drupal::getContainer()->get('stateinfo.setting.service')->convertTermAbbNameToNodeRecordFieldName($abb_name);
   */
  public function convertTermAbbNameToNodeRecordFieldName($abb_name) {
    $row_name = $this->convertTermAbbNameToStandardName($abb_name);

    $field_name = 'field_record_' . $row_name;

    return $field_name;
  }

}
