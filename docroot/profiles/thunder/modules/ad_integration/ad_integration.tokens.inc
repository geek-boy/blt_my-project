<?php

/**
 * @file
 * Builds placeholder replacement tokens for ad-related data.
 */

use Drupal\Component\Utility\Xss;
use Drupal\taxonomy\Entity\Term;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Routing\CurrentRouteMatch;

/**
 * Implements hook_token_info().
 */
function ad_integration_token_info() {
  $type = array(
    'name' => t('Advertising'),
    'description' => t('Tokens for Advertising data.'),
    'needs-data' => 'advertising',
  );

  // Core comment tokens
  $ad['adsc_unit1'] = array(
    'name' => t("Ad unit 1"),
    'description' => t("Ad unit 1."),
  );
  $ad['adsc_unit2'] = array(
    'name' => t("Ad unit 2"),
    'description' => t("Ad unit 2."),
  );
  $ad['adsc_unit3'] = array(
    'name' => t("Ad unit 3"),
    'description' => t("Ad unit 3."),
  );
  $ad['adsc_keyword'] = array(
    'name' => t("Ad keyword"),
    'description' => t("Komma seperated list of ad keywords."),
  );
  $ad['adsc_mode'] = array(
    'name' => t("Ad mode"),
    'description' => t("Ad mode."),
  );
  return array(
    'types' => array('advertising' => $type),
    'tokens' => array(
      'advertising' => $ad,
    ),
  );
}

/**
 * Implements hook_tokens().
 */
function ad_integration_tokens($type, $tokens, array $data, array $options, BubbleableMetadata $bubbleable_metadata) {
  $sanitize = !empty($options['sanitize']);
  $replacements = array();

  if ($type == 'advertising') {
    foreach ($tokens as $name => $original) {
      $replacement = ad_integration_get_setting($name);
      $replacements[$original] = $sanitize ? Xss::filter($replacement) : $replacement;
    }
  }

  return $replacements;
}

function ad_integration_get_setting($name) {
  /**
   * @var CurrentRouteMatch $currentRoutematch
   */
  $currentRoutematch = \Drupal::service('current_route_match');

  $parameters = ['node', 'taxonomy_term'];

  $entity = NULL;
  foreach($parameters as $parameter){
    /**
     * @var ContentEntityInterface $entity
     */
    if($entity = $currentRoutematch->getParameter($parameter)){
      /**
       * Search for ad_integration_settings field
       */
      foreach ($entity->getFieldDefinitions() as $fieldDefinition) {
        $fieldType =  $fieldDefinition->getType();
        /**
         * If settings are found, check if an overridden value for the
         * given setting is found and return that
         */
        $overiddenSetting = get_overridden_ad_setting($name, $fieldDefinition, $entity);
        if (isset($overiddenSetting)){
          return $overiddenSetting;
        }

        /**
         * Check for fallback categories if no ad_integration_setting is found
         */
        if(!isset($termOverride) && $fieldType === 'entity_reference' && $fieldDefinition->getSetting('target_type') === 'taxonomy_term') {
          $fieldName = $fieldDefinition->getName();
          if($tid = $entity->$fieldName->target_id) {
            if ($term = Term::load($tid)) {
              $termOverride = get_overridden_ad_setting_from_term($name, $term);
            }
          }
        }
      }
      /**
       * If we not returned before, it is possible, that we found a termOverride
       */
      if(isset($termOverride)) {
        return $termOverride;
      }
    }
  }

  $default_setting_key = $name . '_default';
  return \Drupal::config('ad_integration.settings')->get($default_setting_key);
}

/**
 * @param $name
 * @param $fieldDefinition
 * @param $entity
 */
function get_overridden_ad_setting($name, $fieldDefinition, $entity) {
  if ($fieldDefinition->getType() === 'ad_integration_settings') {
    $fieldName = $fieldDefinition->getName();
    if (!empty($entity->$fieldName->get(0)->$name)) {
      return $entity->$fieldName->get(0)->$name;
    }
  }
}

function get_overridden_ad_setting_from_term($name, Term $term) {
  foreach ($term->getFieldDefinitions() as $fieldDefinition) {
    $override = get_overridden_ad_setting($name, $fieldDefinition, $term);
    if (isset($override)) {
      return $override;
    }
  }

  foreach (\Drupal::entityManager()->getStorage('taxonomy_term')->loadParents($term->id()) as $parent) {
    $override =  get_overridden_ad_setting_from_term($name, $parent);
    if (isset($override)) {
      return $override;
    }
  }
}
