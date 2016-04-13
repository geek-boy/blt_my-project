<?php

/**
 * @file
 * Contains Drupal\ad_integration\Plugin\Field\FieldFormatter\AdEmptyFormatter.
 */

namespace Drupal\ad_integration\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;

/**
 * Plugin implementation of the 'ad_empty_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "ad_empty_formatter",
 *   module = "ad_integration",
 *   label = @Translation("Empty formatter"),
 *   field_types = {
 *     "ad_integration_settings"
 *   }
 * )
 */
class AdEmptyFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode = NULL) {
    // Does not actually output anything.
    return array();
  }

}
