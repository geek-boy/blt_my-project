<?php

/**
 * @file
 * Contains Drupal\ivw_integration\Plugin\Field\FieldType\IvwSettings.
 */

namespace Drupal\ivw_integration\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'ivw_integration_settings' field type.
 *
 * @FieldType(
 *   id = "ivw_integration_settings",
 *   label = @Translation("IVW settings"),
 *   description = @Translation("Define content specific IVW settings. These settings override the default settings."),
 *   default_widget = "ivw_integration_widget",
 *   default_formatter = "ivw_empty_formatter"
 * )
 */
class IvwSettings extends FieldItemBase {
  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return array(
      'columns' => array(
        'offering' => array(
          'type' => 'varchar',
          'length' => 256,
          'not null' => TRUE,
        ),
        'language' => array(
          'type' => 'varchar',
          'length' => 256,
          'not null' => TRUE,
        ),
        'format' => array(
          'type' => 'varchar',
          'length' => 256,
          'not null' => TRUE,
        ),
        'creator' => array(
          'type' => 'varchar',
          'length' => 256,
          'not null' => TRUE,
        ),
        'homepage' => array(
          'type' => 'varchar',
          'length' => 256,
          'not null' => TRUE,
        ),
        'delivery' => array(
          'type' => 'varchar',
          'length' => 256,
          'not null' => TRUE,
        ),
        'app' => array(
          'type' => 'varchar',
          'length' => 256,
          'not null' => TRUE,
        ),
        'paid' => array(
          'type' => 'varchar',
          'length' => 256,
          'not null' => TRUE,
        ),
        'content' => array(
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
    $properties['offering'] = DataDefinition::create('string')->setLabel(t('Offering'));
    $properties['language'] = DataDefinition::create('string')->setLabel(t('Language'));
    $properties['format'] = DataDefinition::create('string')->setLabel(t('Format'));
    $properties['creator'] = DataDefinition::create('string')->setLabel(t('Creator'));
    $properties['homepage'] = DataDefinition::create('string')->setLabel(t('Homepage'));
    $properties['delivery'] = DataDefinition::create('string')->setLabel(t('Delivery'));
    $properties['app'] = DataDefinition::create('string')->setLabel(t('App'));
    $properties['paid'] = DataDefinition::create('string')->setLabel(t('Paid'));
    $properties['content'] = DataDefinition::create('string')->setLabel(t('Content'));

    return $properties;
  }
}
