<?php

/**
 *
  require_once(DRUPAL_ROOT . '/modules/custom/phpdebug/debug_field_create.php');
  _run_batch_entity_create_fields();
 */


  function _run_batch_entity_create_fields() {
    $entity_info = array(
      'entity_type' => 'node',  // 'node', 'taxonomy_term', 'user'
      'bundle' => 'record',
    );

    $fields = _entity_fields_info();
    foreach ($fields as $field) {
      _entity_create_fields_save($entity_info, $field);
    }
  }

  /**
   *
    field type list:
    boolean
    datetime
    decimal
    email
    entity_reference
    file
    float
    image
    integer
    link
    list_integer
    list_string
    telephone
    string         // Text (plain)
    string_long    // Text (plain, long)
    text_long      // Text (formatted, long)
    text_with_summary
   */
  function _entity_fields_info() {
    $fields[] = array(
      'field_name' => 'field_record_baso_pct',
      'type'       => 'string',
      'label'      => t('嗜碱性粒细胞百分数'),
    );
    return $fields;
  }

  function _entity_fields_names() {
    $output = array(
      array("嗜酸性粒细胞总数","EO"),
      array("嗜酸性粒细胞百分数","EO%"),
      array("红细胞压积","HCT"),
      array("血红蛋白","HGB"),
      array("淋巴细胞百分数","LYM%"),
      array("淋巴细胞总数","LYMPH"),
      array("红细胞平均血红蛋白量","MCH"),
      array("红细胞平均血红蛋白浓度","MCHC"),
      array("红细胞平均体积","MCV"),
      array("单核细胞总数","MONO"),
      array("平均血小板体积","MPV"),
      array("中性粒细胞总数","NEUT"),
      array("大型血小板比率","P-LCR"),
      array("血小板分布宽度","PDW"),
      array("血小板计数","PLT"),
      array("红细胞","RBC"),
      array("网织红细胞计数","RC"),
      array("红细胞变异系数","RDW-CV"),
      array("红细胞分布宽度","RDW-SD"),
      array("白细胞总数","WBC"),
    );
    return $output;
  }

  use Drupal\field\Entity\FieldConfig;
  use Drupal\field\Entity\FieldStorageConfig;
  function _entity_create_fields_save($entity_info, $field) {
    $field_storage = FieldStorageConfig::create(array(
      'field_name'  => $field['field_name'],
      'entity_type' => $entity_info['entity_type'],
      'type'  => $field['type'],
      'settings' => array(
        'target_type' => 'node',
      ),
    ));
    $field_storage->save();

    $field_config = FieldConfig::create([
      'field_name'  => $field['field_name'],
      'label'       => $field['label'],
      'entity_type' => $entity_info['entity_type'],
      'bundle'      => $entity_info['bundle'],
    ]);
    $field_config->save();

    entity_get_form_display($entity_info['entity_type'], $entity_info['bundle'], 'default')
      ->setComponent($field['field_name'], [
        'settings' => [
          'display' => TRUE,
        ],
      ])
      ->save();

    entity_get_display($entity_info['entity_type'], $entity_info['bundle'], 'default')
      ->setComponent($field['field_name'], [
        'settings' => [
          'display_summary' => TRUE,
        ],
      ])
      ->save();
  }
