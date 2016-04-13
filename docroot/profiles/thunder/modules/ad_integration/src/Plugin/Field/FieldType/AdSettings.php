<?php

/**
 * @file
 * Contains Drupal\ad_integration\Plugin\Field\FieldType\AdSettings.
 */

namespace Drupal\ad_integration\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'ad_integration_settings' field type.
 *
 * @FieldType(
 *   id = "ad_integration_settings",
 *   label = @Translation("Advertising settings"),
 *   description = @Translation("Define content specific Advertising settings. These settings override the default settings."),
 *   default_widget = "ad_integration_widget",
 *   default_formatter = "ad_empty_formatter"
 * )
 */
class AdSettings extends FieldItemBase {
  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return array(
      'columns' => array(
        'adsc_unit1' => array(
          'type' => 'varchar',
          'length' => 256,
          'not null' => TRUE,
        ),
        'adsc_unit2' => array(
          'type' => 'varchar',
          'length' => 256,
          'not null' => TRUE,
        ),
        'adsc_unit3' => array(
          'type' => 'varchar',
          'length' => 256,
          'not null' => TRUE,
        ),
        'adsc_keyword' => array(
          'type' => 'varchar',
          'length' => 256,
          'not null' => TRUE,
        ),
        'adsc_mode' => array(
          'type' => 'varchar',
          'length' => 256,
          'not null' => TRUE,
        ),
      ),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['adsc_unit1'] = DataDefinition::create('string')->setLabel(t('Ad unit 1'));
    $properties['adsc_unit2'] = DataDefinition::create('string')->setLabel(t('Ad unit 2'));
    $properties['adsc_unit3'] = DataDefinition::create('string')->setLabel(t('Ad unit 3'));
    $properties['adsc_keyword'] = DataDefinition::create('string')->setLabel(t('Ad keyword'));
    $properties['adsc_mode'] = DataDefinition::create('string')->setLabel(t('Ad mode'));

    return $properties;
  }
}
