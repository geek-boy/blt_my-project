<?php

/**
 * @file
 * Contains \Drupal\ad_integration\Plugin\Field\Widget\AdSettingsWidget.
 */

namespace Drupal\ad_integration\Plugin\Field\FieldWidget;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\Element;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'ad_integration_settings' widget.
 *
 * @FieldWidget(
 *   id = "ad_integration_widget",
 *   module = "ad_integration",
 *   label = @Translation("Advertising Settings"),
 *   field_types = {
 *     "ad_integration_settings"
 *   }
 * )
 */
class AdSettingsWidget extends WidgetBase implements ContainerFactoryPluginInterface {
  /**
   * The config factory.
   *
   * @var ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The form builder.
   *
   * @var \Drupal\Core\Form\FormBuilderInterface
   */
  protected $formBuilder;

  /**
   * {@inheritdoc}
   */
  public function __construct(
    $plugin_id,
    $plugin_definition,
    FieldDefinitionInterface $field_definition,
    array $settings,
    array $third_party_settings,
    ConfigFactoryInterface $config_factory,
    FormBuilderInterface $form_builder
  ) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings);
    $this->configFactory = $config_factory;
    $this->formBuilder = $form_builder;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(
    ContainerInterface $container,
    array $configuration,
    $plugin_id,
    $plugin_definition
  ) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['third_party_settings'],
      $container->get('config.factory'),
      $container->get('form_builder'));
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(
    FieldItemListInterface $items,
    $delta,
    array $element,
    array &$form,
    FormStateInterface $form_state
  ) {
    $settings = $this->configFactory->get('ad_integration.settings');
    $settingsForm = $this->formBuilder->getForm('\Drupal\ad_integration\Form\SettingsForm');
    // add configurable settings
    foreach(Element::children($settingsForm['default_values']) as $child_element){
      if(strrpos($child_element, '_overridable') !== FALSE && ($settings->get($child_element) == 1)) {
        $value_element_name = str_replace('_overridable', '', $child_element);
        $default_value_element_name = $value_element_name . '_default';
        $default_value_element = $settingsForm['default_values'][$default_value_element_name];

        $element[$value_element_name]['#type'] = $default_value_element['#type'];
        $element[$value_element_name]['#title'] = $default_value_element['#title'];
        $element[$value_element_name]['#default_value'] = isset($items[$delta]->$value_element_name) ? $items[$delta]->$value_element_name : NULL;
        $element[$value_element_name]['#required'] = FALSE;

        if(!empty($default_value_element['#options'])) {
          $element[$value_element_name]['#options'] = $default_value_element['#options'];
          $element[$value_element_name]['#empty_option'] = t('Site default value');
        }
      }
    }
    // add keywords
    $value_element_name = 'adsc_keyword';
    $element[$value_element_name]['#type'] = 'textfield';
    $element[$value_element_name]['#title'] = t('Keywords');
    $element[$value_element_name]['#description'] = t('Comma separated ad keywords');
    $element[$value_element_name]['#default_value'] = isset($items[$delta]->$value_element_name) ? $items[$delta]->$value_element_name : NULL;
    $element[$value_element_name]['#required'] = FALSE;

    return $element;
  }
}
