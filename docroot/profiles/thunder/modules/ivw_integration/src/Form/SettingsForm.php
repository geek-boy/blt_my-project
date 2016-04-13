<?php

/**
 * @file
 * Contains \Drupal\ivw_integration\Form\SettingsForm.
 */

namespace Drupal\ivw_integration\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Utility\Token;

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
   * Constructs a \Drupal\ivw_integration\SettingsForm object.
   *
   * @param ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
    * @param Token $token
    *   The token object.
   */
  public function __construct(ConfigFactoryInterface $config_factory, Token $token){
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
    return 'ivw_integration_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $ivw_integration_settings = $this->config('ivw_integration.settings');
    $form['current_code'] = array(
      '#markup' => 'Current Default Code: ' . $this->token->replace($ivw_integration_settings->get('code_template'), array(), array('sanitize' => FALSE)),
    );

    $form['ivw_settings'] = array(
      '#type' => 'vertical_tabs',
      '#default_tab' => 'site_settings',
    );

    $form['site_settings'] = array(
      '#type' => 'details',
      '#title' => t('Site settings'),
      '#open' => TRUE,
      '#group' => 'ivw_settings',
    );

    $form['default_values'] = array(
      '#type' => 'details',
      '#title' => t('Default values'),
      '#open' => FALSE,
      '#group' => 'ivw_settings',
    );

    $form['site_settings']['site'] = array(
      '#type' => 'textfield',
      '#title' => t('IVW Site name'),
      '#required' => TRUE,
      '#default_value' => $ivw_integration_settings->get('site'),
      '#description' => t('Site name as given by IVW, this is used as default for the "st" parameter in the iam_data object')
    );
    $form['site_settings']['mobile_site'] = array(
      '#type' => 'textfield',
      '#title' => t('IVW Mobile Site name'),
      '#required' => TRUE,
      '#default_value' => $ivw_integration_settings->get('mobile_site'),
      '#description' => t('Mobile site name as given by IVW, this is used as default for the "st" parameter in the iam_data object')
    );
    $form['site_settings']['code_template'] = array(
      '#type' => 'textfield',
      '#title' => t('Code template'),
      '#required' => TRUE,
      '#maxlength' => 256,
      '#size' => 128,
      '#default_value' => $ivw_integration_settings->get('code_template'),
      '#description' => t('Code template, for creating the actual ivw code.'),
    );

    $form['site_settings']['code_template_token_tree'] = array(
      '#theme' => 'token_tree',
      '#token_types' => ['ivw'],
    );

    $form['site_settings']['responsive'] = array(
      '#type' => 'checkbox',
      '#title' => t('Site is responsive'),
      '#required' => TRUE,
      '#default_value' => $ivw_integration_settings->get('responsive'),
      '#description' => t('Responsive sites must handle mobile code in javascript, this is activated here.')
    );

    $form['site_settings']['mobile_width'] = array(
      '#type' => 'textfield',
      '#states' => array(
        // Hide the setting when site is not flagged as responsive.
        'invisible' => array(
          ':input[name="ivw_responsive"]' => array('checked' => FALSE),
        ),
      ),
      '#title' => t('Mobile width'),
      '#required' => TRUE,
      '#default_value' => $ivw_integration_settings->get('mobile_width'),
      '#description' => t('On a responsive site, this value tells the javascript up to which screen width, the device should be treated as mobile.')
    );

    $form['default_values']['offering_default'] = array(
      '#type' => 'textfield',
      '#title' => t('Fallback offering code'),
      '#required' => TRUE,
      '#default_value' => $ivw_integration_settings->get('offering_default'),
      '#description' => t('A single ivw site can have multiple offerings, they can be differentiated by different numbers.'),
      '#min' => 1,
    );
    $form['default_values']['offering_overridable'] = array(
      '#type' => 'checkbox',
      '#title' => t('Offering code is overrideable'),
      '#default_value' => $ivw_integration_settings->get('offering_overridable'),
    );
    $form['default_values']['language_default'] = array(
      '#type' => 'select',
      '#options' => array(
        1 => 'Deutsch',
        2 => 'Andere Sprache, Inhalt prüfbar',
        3 => 'Andere Sprache, Inhalt nicht prüfbar'
      ),
      '#title' => t('Fallback language'),
      '#required' => TRUE,
      '#default_value' => $ivw_integration_settings->get('language_default'),
    );
    $form['default_values']['language_overridable'] = array(
      '#type' => 'checkbox',
      '#title' => t('Language code is overrideable'),
      '#default_value' => $ivw_integration_settings->get('language_overridable'),
    );
    $form['default_values']['format_default'] = array(
      '#type' => 'select',
      '#options' => array(
        1 => 'Bild/Text',
        2 => 'Audio',
        3 => 'Video',
        4 => 'Andere dynamische Formate'
      ),
      '#title' => t('Fallback format'),
      '#required' => TRUE,
      '#default_value' => $ivw_integration_settings->get('format_default'),
    );
    $form['default_values']['format_overridable'] = array(
      '#type' => 'checkbox',
      '#title' => t('Format code is overrideable'),
      '#default_value' => $ivw_integration_settings->get('format_overridable'),
    );
    $form['default_values']['creator_default'] = array(
      '#type' => 'select',
      '#options' => array(
        1 => 'Redaktion',
        2 => 'User',
        3 => 'Unbekannt'
      ),
      '#title' => t('Fallback creator'),
      '#required' => TRUE,
      '#default_value' => $ivw_integration_settings->get('creator_default'),
    );
    $form['default_values']['creator_overridable'] = array(
      '#type' => 'checkbox',
      '#title' => t('Creator code is overrideable'),
      '#default_value' => $ivw_integration_settings->get('creator_overridable'),
    );
    $form['default_values']['homepage_default'] = array(
      '#type' => 'select',
      '#options' => array(
        1 => 'Homepage des Angebots',
        2 => 'Keine Homepage',
        3 => 'Hompage der Fremddomains bei Multi-Angeboten'
      ),
      '#title' => t('Fallback homepage flag'),
      '#required' => TRUE,
      '#default_value' => $ivw_integration_settings->get('homepage_default'),
    );
    $form['default_values']['homepage_overridable'] = array(
      '#type' => 'checkbox',
      '#title' => t('Homepage flag is overridable'),
      '#default_value' => $ivw_integration_settings->get('homepage_overridable'),
    );
    $form['default_values']['delivery_default'] = array(
      '#type' => 'select',
      '#options' => array(
        1 => 'Online',
        2 => 'Mobile',
        3 => 'Connected TV'
      ),
      '#title' => t('Fallback delivery'),
      '#required' => TRUE,
      '#default_value' => $ivw_integration_settings->get('delivery_default'),
    );
    $form['default_values']['delivery_overridable'] = array(
      '#type' => 'checkbox',
      '#title' => t('Delivery flag is overridable'),
      '#default_value' => $ivw_integration_settings->get('delivery_overridable'),
    );
    $form['default_values']['app_default'] = array(
      '#type' => 'select',
      '#options' => array(
        1 => 'App',
        2 => 'Keine App'
      ),
      '#title' => t('Fallback app flag'),
      '#required' => TRUE,
      '#default_value' => $ivw_integration_settings->get('app_default'),
    );
    $form['default_values']['app_overridable'] = array(
      '#type' => 'checkbox',
      '#title' => t('App flag is overridable'),
      '#default_value' => $ivw_integration_settings->get('app_overridable'),
    );
    $form['default_values']['paid_default'] = array(
      '#type' => 'select',
      '#options' => array(
        1 => 'Paid',
        2 => 'Nicht zugeordnet'
      ),
      '#title' => t('Fallback paid flag'),
      '#required' => TRUE,
      '#default_value' => $ivw_integration_settings->get('paid_default'),
    );
    $form['default_values']['paid_overridable'] = array(
      '#type' => 'checkbox',
      '#title' => t('Paid flag is overridable'),
      '#default_value' => $ivw_integration_settings->get('paid_overridable'),
    );
    $form['default_values']['content_default'] = array(
      '#type' => 'select',
      '#options' => array(
        '01' => 'Nachrichten',
        '02' => 'Sport',
        '03' => 'Entertainment/Boulevard/Stars/Film/Musik',
        '04' => 'Fashion/Beauty',
        '05' => 'Familie/Kinder/Lebenshilfe',
        '06' => 'Liebe/Psychologie/Beziehungen',
        '07' => 'Fahrzeuge/Verkehr/Mobilität',
        '08' => 'Reise/Touristik',
        '09' => 'Computer',
        '10' => 'Consumer Electronics',
        '11' => 'Telekommunikation/Internetdienste',
        '12' => 'Spiele',
        '13' => 'Wohnen/Immobilien/Garten/Haushalt',
        '14' => 'Wirtschaft/Finanzen/Job/Karriere',
        '15' => 'Gesundheit',
        '16' => 'Essen/Trinken',
        '17' => 'Kunst/Kultur/Literatur',
        '18' => 'Erotik',
        '19' => 'Wissenschaft/Bildung/Natur/Umwelt',
        '20' => 'Angebotsinformation',
        '21' => 'Vermischtes (multithematisch)',
        '22' => 'Sonstiges (monothematisch)',
        '23' => 'Übersichtsseite zu Spiele',
        '24' => 'Casual Games',
        '25' => 'Core Games',
        '26' => 'Sonstiges (Bereich Spiele)',
        '27' => 'Social Networking - Privat',
        '28' => 'Social Networking - Business',
        '29' => 'Partnersuche/Dating',
        '30' => 'Newsletter',
        '31' => 'E-Mail/SMS/E-Cards',
        '32' => 'Messenger/Chat',
        '33' => 'Sonstiges (Bereich Networking/Kommunikation',
        '34' => 'Suchmaschinen',
        '35' => 'Verzeichnisse/Auskunftsdienste',
        '36' => 'Sonstiges (Bereich Suchmaschinen/Verzeichnisse)',
        '37' => 'Onlineshops/Shopping Mall/Auktionen/B2bMarktplätze',
        '38' => 'Immobilien Rubrikenmärkte/Kleinanzeigen',
        '39' => 'Jobs Rubrikenmärkte/Kleinanzeigen',
        '40' => 'Fahrzeuge Rubrikenmärkte/Kleinanzeigen',
        '41' => 'Sonstiges Rubrikenmärkte/Kleinanzeigen',
        '42' => 'Sonstiges (Bereich E-Commerce)'
      ),
      '#title' => t('Fallback content category'),
      '#required' => TRUE,
      '#default_value' => $ivw_integration_settings->get('content_default'),
    );
    $form['default_values']['content_overridable'] = array(
      '#type' => 'checkbox',
      '#title' => t('Content category is overridable'),
      '#default_value' => $ivw_integration_settings->get('content_overridable'),
    );


    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $this->config('ivw_integration.settings')
      ->set('site', $values['site'])
      ->set('mobile_site', $values['mobile_site'])
      ->set('code_template', $values['code_template'])
      ->set('responsive', $values['responsive'])
      ->set('mobile_width', $values['mobile_width'])
      ->set('offering_default', $values['offering_default'])
      ->set('offering_overridable', $values['offering_overridable'])
      ->set('language_default', $values['language_default'])
      ->set('language_overridable', $values['language_overridable'])
      ->set('format_default', $values['format_default'])
      ->set('format_overridable', $values['format_overridable'])
      ->set('creator_default', $values['creator_default'])
      ->set('creator_overridable', $values['creator_overridable'])
      ->set('homepage_default', $values['homepage_default'])
      ->set('homepage_overridable', $values['homepage_overridable'])
      ->set('delivery_default', $values['delivery_default'])
      ->set('delivery_overridable', $values['delivery_overridable'])
      ->set('app_default', $values['app_default'])
      ->set('app_overridable', $values['app_overridable'])
      ->set('paid_default', $values['paid_default'])
      ->set('paid_overridable', $values['paid_overridable'])
      ->set('content_default', $values['content_default'])
      ->set('content_overridable', $values['content_overridable'])
      ->save();
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'ivw_integration.settings',
    ];
  }
}
