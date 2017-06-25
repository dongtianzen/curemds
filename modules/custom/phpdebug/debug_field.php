<?php

/**
 *
  require_once(DRUPAL_ROOT . '/modules/custom/phpdebug/debug_field.php');
  _get_all_fields_in_a_bundle();
 */

use Drupal\field\Entity\FieldConfig;

function _get_all_fields_in_a_bundle() {
  // $bundle_fields = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldsCollectionByEntityBundle('taxonomy_term', 'province');
  // foreach ($bundle_fields as $key => $value) {
  //   dpm($value->getLabel());
  //   dpm($value->getName());
  //   dpm($value->getType());
  //   dpm($value->getSettings());
  //   dpm($value->getSetting('handler_settings'));
  //   dpm($value->getSetting('target_type'));
  // }

// ksm($bundle_fields);
  $bundle_fields = \Drupal::service('entity_field.manager')->getFieldDefinitions('node', 'meeting');
  // $bundle_fields = \Drupal::service('entity_field.manager')->getFieldDefinitions('node', 'article');
 // ksm($bundle_fields['field_meeting_module']->getSettings());
 // ksm($bundle_fields['field_article_text_plain_1024']->getItemDefinition()->getFieldDefinition());

// cardinality
 ksm($bundle_fields['field_meeting_module']->getItemDefinition());
 ksm($bundle_fields['field_meeting_module']->getItemDefinition()->getFieldDefinition()->getFieldStorageDefinition()->getCardinality());
 // ksm($bundle_fields['field_meeting_module']);
  // dpm($bundle_fields['field_province_region']->getName());
  // ksm($bundle_fields);

  // ksm($bundle_fields['field_article_text_plain_1024']->getLabel());
  // ksm($bundle_fields['field_article_text_plain_1024']->getName());
  // ksm($bundle_fields['field_article_text_plain_1024']);
}

function _get_all_fields_in_a_bundle() {
  // $bundle_fields = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldsCollectionByEntityBundle('taxonomy_term', 'province');
  // foreach ($bundle_fields as $key => $value) {
  //   dpm($value->getLabel());
  //   dpm($value->getName());
  //   dpm($value->getType());
  //   dpm($value->getSettings());
  //   dpm($value->getSetting('handler_settings'));
  //   dpm($value->getSetting('target_type'));
  // }

// ksm($bundle_fields);
  $bundle_fields = \Drupal::service('entity_field.manager')->getFieldDefinitions('node', 'meeting');
  // $bundle_fields = \Drupal::service('entity_field.manager')->getFieldDefinitions('node', 'article');
 // ksm($bundle_fields['field_meeting_module']->getSettings());
 // ksm($bundle_fields['field_article_text_plain_1024']->getItemDefinition()->getFieldDefinition());

// cardinality
 ksm($bundle_fields['field_meeting_module']->getItemDefinition());
 ksm($bundle_fields['field_meeting_module']->getItemDefinition()->getFieldDefinition()->getFieldStorageDefinition()->getCardinality());
 // ksm($bundle_fields['field_meeting_module']);
  // dpm($bundle_fields['field_province_region']->getName());
  // ksm($bundle_fields);

  // ksm($bundle_fields['field_article_text_plain_1024']->getLabel());
  // ksm($bundle_fields['field_article_text_plain_1024']->getName());
  // ksm($bundle_fields['field_article_text_plain_1024']);
}

/**
 *
 */
function _get_field($entity_id = NULL) {
  $entity_storage = \Drupal::entityTypeManager()->getStorage('node');
  $entity = $entity_storage->load($entity_id);

  $field_name = 'field_page_city';
  $field_name = 'body';
  $field = $entity->get($field_name);
// dpm($field);
dpm($field->value);

}

/**
 * Obtaining information about the field
  require_once(DRUPAL_ROOT . '/modules/custom/phpdebug/debug.php');
  _get_field_information(3);
 */
function _get_field_information($entity_id = NULL) {
  // $entity = \Drupal::entityTypeManager()->getStorage('node')->load($entity_id);
  $entity = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($entity_id);
  $field = $entity->get('field_page_city');

  $definition = $field->getFieldDefinition();
  $field_name = $definition->get('field_name');
  $field_type = $definition->get('field_type');

  dpm($field_name);
  dpm($field_type);
}

/**
 *
  require_once(DRUPAL_ROOT . '/modules/custom/phpdebug/debug.php');
  _set_field_value(3);
 */
function _set_field_value($entity_id = NULL) {
  $entity = \Drupal::entityTypeManager()->getStorage('node')->load($entity_id);

  $field_name = 'field_page_city';
  $field = $entity->get($field_name);
  dpm($entity->get($field_name)->value);

  $field_values = $field->getValue();
  $field_values[0]['value'] = 'London';
  $field->setValue($field_values);
  $entity->save();

  $entity = \Drupal::entityTypeManager()->getStorage('node')->load($entity_id);
  dpm($entity->get($field_name)->value);
}

function _print_field() {
  // dpm(\Drupal::currentUser());

  $entity_type = 'taxonomy_term';
  $field_name  = 'field_theraparea_eventregion';
  $bundle      = 'therapeuticarea';

  $entity_type = 'user';
  $field_name  = 'field_user_region';
  $bundle      = 'user';

  $entity_type = 'node';
  $field_name  = 'field_meeting_eventregion';
  $bundle      = 'meeting';

  $entityManager = Drupal::service('entity.manager');
  $FieldDefinition = $entityManager->getFieldDefinitions($entity_type, $field_name);

  $FieldConfig = FieldConfig::loadByName($entity_type, $bundle, $field_name);
  $target_bundles = $FieldConfig->getSettings()['handler_settings']['target_bundles'];
  dpm($target_bundles);

  $field = \Drupal\field\Entity\FieldStorageConfig::loadByName($entity_type, $field_name);
  dpm($field->getType());
}
