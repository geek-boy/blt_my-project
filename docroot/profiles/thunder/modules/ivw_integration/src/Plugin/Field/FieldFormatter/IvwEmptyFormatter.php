<?php

/**
 * @file
 * Contains Drupal\ivw_integration\Plugin\Field\FieldFormatter\IvwEmptyFormatter.
 */

namespace Drupal\ivw_integration\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;

/**
 * Plugin implementation of the 'ivw_empty_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "ivw_empty_formatter",
 *   module = "ivw_integration",
 *   label = @Translation("Empty formatter"),
 *   field_types = {
 *     "ivw_integration_settings"
 *   }
 * )
 */
class IvwEmptyFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode = NULL) {
    // Does not actually output anything.
    return array();
  }

}
