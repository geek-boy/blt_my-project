<?php

/**
 * @file
 * Contains \Drupal\ad_integration\Form\SettingsForm.
 */

namespace Drupal\ad_integration\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Utility\Token;
use Drupal\Component\Utility\Tags;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines a form that configures ivw settings.
 */
class SettingsForm extends ConfigFormBase {
  /**
   * The token object.
   *
   * @var Token
   */
  protected $token = array();

  /**
   * Constructs a \Drupal\ad_integration\SettingsForm object.
   *
   * @param ConfigFactoryInterface $config_factory
   *  The factory for configuration objects.
   * @param Token $token
   *  The token object.
   */
  public function __construct(ConfigFactoryInterface $config_factory, Token $token) {
    parent::__construct($config_factory);
    $this->token = $token;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('token')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ad_integration_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $settings = $this->config('ad_integration.settings');

    $form['ad_settings'] = array(
      '#type' => 'vertical_tabs',
      '#default_tab' => 'site_settings',
    );

    $form['site_settings'] = array(
      '#type' => 'details',
      '#title' => t('Site settings'),
      '#open' => TRUE,
      '#group' => 'ad_settings',
    );

    $form['default_values'] = [
      '#type' => 'details',
      '#title' => t('Default values'),
      '#open' => FALSE,
      '#group' => 'ad_settings',
    ];

    $provider_options = ['fag' => 'fag', 'orbyd' => 'orbyd'];
    $form['site_settings']['ad_provider'] = array(
      '#type' => 'select',
      '#options' => $provider_options,
      '#title' => t('Ad provider'),
      '#default_value' => $settings->get('ad_provider'),
    );

    $form['site_settings']['adsc_container_tag'] = array(
      '#type' => 'textfield',
      '#title' => t('Container tag url'),
      '#default_value' => $settings->get('adsc_container_tag'),
      '#states' => array(
        'visible' => array(
          ':input[name=ad_provider]' => array('value' => 'fag'),
        ),
      ),
    );

    $form['site_settings']['adsc_ad_engine'] = array(
      '#type' => 'textfield',
      '#title' => t('Ad engine'),
      '#default_value' => $settings->get('adsc_ad_engine'),
    );
    
    $adsc_unit2_values = $settings->get('adsc_unit2_values');
    $form['site_settings']['adsc_unit2_values'] = array(
      '#type' => 'textarea',
      '#title' => t('First hierarchy level values'),
      '#default_value' => !empty($adsc_unit2_values) ? Tags::implode($adsc_unit2_values) : '',
      '#description' => t('Comma separated list of possible values for first hierarchy level')
    );

    $adsc_unit3_values = $settings->get('adsc_unit3_values');
    $form['site_settings']['adsc_unit3_values'] = array(
      '#type' => 'textarea',
      '#title' => t('Second hierarchy level values'),
      '#default_value' => !empty($adsc_unit3_values) ? Tags::implode($adsc_unit3_values) : '',
      '#description' => t('Comma separated list of possible values for second hierarchy level')
    );

    $form['default_values']['adsc_unit1_default'] = [
      '#title' => t('Ad level 1'),
      '#type' => 'textfield',
      '#default_value' => $settings->get('adsc_unit1_default'),
      '#description' => t('First hierarchical level. This is the name of the Website')
    ];

    $form['default_values']['adsc_unit1_overridable'] = [
      '#type' => 'checkbox',
      '#title' => t('Adsc Unit 1 is overrideable'),
      '#default_value' => $settings->get('adsc_unit1_overridable'),
    ];


    $form['default_values']['adsc_unit2_default'] = [
      '#title' => t('Ad level 2'),
      '#default_value' => $settings->get('adsc_unit2_default'),
      '#description' => t('Second hierarchical level')
    ];

    if(empty($adsc_unit2_values)) {
      $form['default_values']['adsc_unit2_default']['#type'] = 'textfield';
    } else {
      $form['default_values']['adsc_unit2_default']['#type'] = 'select';
      $form['default_values']['adsc_unit2_default']['#options'] =  array_combine($adsc_unit2_values, $adsc_unit2_values);
    }

    $form['default_values']['adsc_unit2_overridable'] = [
      '#type' => 'checkbox',
      '#title' => t('Ad level 2 is overrideable'),
      '#default_value' => $settings->get('adsc_unit2_overridable'),
    ];

    $form['default_values']['adsc_unit3_default'] = [
      '#title' => t('Ad level 3'),
      '#default_value' => $settings->get('adsc_unit3_default'),
      '#description' => t('Third hierarchical level')
    ];

    if(empty($adsc_unit3_values)) {
      $form['default_values']['adsc_unit3_default']['#type'] = 'textfield';
    } else {
      $form['default_values']['adsc_unit3_default']['#type'] = 'select';
      $form['default_values']['adsc_unit3_default']['#options'] =  array_combine($adsc_unit3_values, $adsc_unit3_values);
    }

    $form['default_values']['adsc_unit3_overridable'] = [
      '#type' => 'checkbox',
      '#title' => t('Ad level 3 is overrideable'),
      '#default_value' => $settings->get('adsc_unit3_overridable'),
    ];

    $modes = ['full' => 'full', 'infinite' => 'infinite'];
    $form['default_values']['adsc_mode_default'] = [
      '#title' => t('Adsc mode'),
      '#type' => 'select',
      '#options' => $modes,
      '#default_value' => $settings->get('adsc_mode_default'),
      '#description' => t('Adsc mode, fag provides a special mode for infinite scrolling.'),
      '#states' => array(
        'visible' => array(
          ':input[name=ad_provider]' => array('value' => 'fag'),
        ),
      ),
    ];

    $form['default_values']['adsc_mode_overridable'] = [
      '#type' => 'checkbox',
      '#title' => t('Adsc mode is overrideable'),
      '#default_value' => $settings->get('adsc_mode_overridable'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    $values = $form_state->getValues();
    $config =$this->configFactory()->getEditable('ad_integration.settings');
    $config->set('adsc_container_tag', $values['adsc_container_tag'])
      ->set('adsc_ad_engine', $values['adsc_ad_engine'])
      ->set('ad_provider', $values['ad_provider'])
      ->set('adsc_unit1_default', $values['adsc_unit1_default'])
      ->set('adsc_unit1_overridable', $values['adsc_unit1_overridable'])
      ->set('adsc_unit2_default', $values['adsc_unit2_default'])
      ->set('adsc_unit2_values', Tags::Explode($values['adsc_unit2_values']))
      ->set('adsc_unit2_overridable', $values['adsc_unit2_overridable'])
      ->set('adsc_unit3_default', $values['adsc_unit3_default'])
      ->set('adsc_unit3_values', Tags::Explode($values['adsc_unit3_values']))
      ->set('adsc_unit3_overridable', $values['adsc_unit3_overridable'])
      ->set('adsc_mode_default', $values['adsc_mode_default'])
      ->set('adsc_mode_overridable', $values['adsc_mode_overridable'])
      ->save();
  }


  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'ad_integration.settings',
    ];
  }
}
