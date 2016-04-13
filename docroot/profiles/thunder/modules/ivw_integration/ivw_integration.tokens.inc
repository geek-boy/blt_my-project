<?php

/**
 * @file
 * Builds placeholder replacement tokens for ivw-related data.
 */

use Drupal\Component\Utility\Xss;
use Drupal\taxonomy\Entity\Term;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Routing\CurrentRouteMatch;

/**
 * Implements hook_token_info().
 */
function ivw_integration_token_info() {
  $type = array(
    'name' => t('IVW'),
    'description' => t('Tokens for IVW data.'),
    'needs-data' => 'ivw',
  );

  // Core comment tokens
  $ivw['site'] = array(
    'name' => t("IVW site name"),
    'description' => t("Site name as provided by IVW."),
  );
  $ivw['responsive'] = array(
    'name' => t("Responsive flag"),
    'description' => t("Has the value 1 if the site is cionfigured to be responsive, else it is set to 0."),
  );
  $ivw['mobile_width'] = array(
    'name' => t("Mobile width"),
    'description' => t("The maximum width in pixels until a devive is considered to be mobile."),
  );
  $ivw['offering'] = array(
    'name' => t("Offering"),
    'description' => t("An identifier for the offering on this site."),
  );
  $ivw['language'] = array(
    'name' => t("Content language"),
    'description' => t("Content language ID."),
  );
  $ivw['format'] = array(
    'name' => t("Format"),
    'description' => t("The content format ID."),
  );
  $ivw['creator'] = array(
    'name' => t("Creator"),
    'description' => t("The content creator ID."),
  );
  $ivw['homepage'] = array(
    'name' => t('Hompage'),
    'description' => t('Denotes, if the content is the homepage of the offering.'),
  );
  $ivw['delivery'] = array(
    'name' => t("Deliver"),
    'description' => t("Delivery type ID."),
  );
  $ivw['app'] = array(
    'name' => t("App flag"),
    'description' => t("Flags if the offering is an mobile app."),
  );
  $ivw['paid'] = array(
    'name' => t("paid flag"),
    'description' => t("Flags paid content."),
  );
  $ivw['content'] = array(
    'name' => t("Content"),
    'description' => t("Content category identifier."),
  );

  return array(
    'types' => array('ivw' => $type),
    'tokens' => array(
      'ivw' => $ivw,
    ),
  );
}

/**
 * Implements hook_tokens().
 */
function ivw_integration_tokens($type, $tokens, array $data, array $options, BubbleableMetadata $bubbleable_metadata) {
  $sanitize = !empty($options['sanitize']);
  $replacements = array();

  if ($type == 'ivw') {
    foreach ($tokens as $name => $original) {
      $replacement = ivw_integration_get_setting($name);
      $replacements[$original] = $sanitize ? Xss::filter($replacement) : $replacement;
    }
  }

  return $replacements;
}

function ivw_integration_get_setting($name) {
  /**
   * @var CurrentRouteMatch $currentRoutematch
   */
  $currentRoutematch = \Drupal::service('current_route_match');


  $parameters = ['node', 'media', 'taxonomy_term'];

  $entity = NULL;
  foreach($parameters as $parameter){
    /**
     * @var ContentEntityInterface $entity
     */
    if($entity = $currentRoutematch->getParameter($parameter)){
      if (is_numeric($entity)) {
        $entity = \Drupal\node\Entity\Node::load($entity);
      }
      /**
       * Search for ivw_integration_settings field
       */
      foreach ($entity->getFieldDefinitions() as $fieldDefinition) {
        $fieldType =  $fieldDefinition->getType();
        /**
         * If settings are found, check if an overridden value for the
         * given setting is found and return that
         */
        $overiddenSetting = get_overridden_ivw_setting($name, $fieldDefinition, $entity);
        if (isset($overiddenSetting)){
          return $overiddenSetting;
        }

        /**
         * Check for fallback categories if no ivw_integration_setting is found
         */
        if(!isset($termOverride) && $fieldType === 'entity_reference' && $fieldDefinition->getSetting('target_type') === 'taxonomy_term') {
          $fieldName = $fieldDefinition->getName();
          if($tid = $entity->$fieldName->target_id) {
            $term = Term::load($tid);
            if($term) {
              $termOverride = get_overridden_ivw_setting_from_term($name, $term);
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
  return \Drupal::config('ivw_integration.settings')->get($default_setting_key);
}
