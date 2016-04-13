<?php

/**
 * @file
 * Contains Drupal\ad_integration\AdTracker
 */

namespace Drupal\ad_integration;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Core\Path\PathMatcher;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\Core\Utility\Token;

class AdIntegration implements AdIntegrationInterface {
  /**
   * The entity storage object for taxonomy terms.
   *
   * @var TermStorageInterface
   */
  protected $termStorage;

  /**
   * The entity query object for nodes.
   *
   * @var \Drupal\Core\Entity\Query\Sql\Query
   */
  protected $nodeQuery;

  /**
   * The config factory.
   *
   * @var ImmutableConfig
   */
  protected $settings;

  /**
   * The current path match.
   *
   * @var PathMatcher
   */
  protected $pathMatch;

  /**
   * The current route match.
   *
   * @var CurrentRouteMatch
   */
  protected $currentRouteMatch;

  /**
   * The token object.
   *
   * @var Token
   */
  protected $token;

  /**
   * Generates Advertising information.
   *
   * @param EntityManagerInterface $entity_manager
   *   The entity query object for taxonomy terms.
   * @param QueryFactory $query
   *   The entity query object for taxonomy terms.
   * @param ConfigFactoryInterface $config_factory
   *   The config factory service.
   * @param PathMatcher $path_match
   *   The current path match.
   * @param CurrentRouteMatch $current_route_match
   *   The current route match.
   * @param Token $token
   *   Token service.
   */
  public function __construct(
    EntityManagerInterface $entity_manager,
    QueryFactory $query,
    ConfigFactoryInterface $config_factory,
    PathMatcher $path_match,
    CurrentRouteMatch $current_route_match,
    Token $token
  ) {
    $this->termStorage = $entity_manager->getStorage('taxonomy_term');
    $this->nodeQuery = $query->get('node');
    $this->settings = $config_factory->get('ad_integration.settings');
    $this->pathMatch = $path_match;
    $this->currentRouteMatch = $current_route_match;
    $this->token = $token;
  }


  /**
   * @inherit
   */
  public function getAdUnit1() {
    return $this->token->replace('[advertising:adsc_unit1]', array(), array('sanitize' => FALSE));
  }

  /**
   * @inherit
   */
  public function getAdUnit2() {
    return $this->token->replace('[advertising:adsc_unit2]', array(), array('sanitize' => FALSE));
  }

  /**
   * @inherit
   */
  public function getAdUnit3() {
    return $this->token->replace('[advertising:adsc_unit3]', array(), array('sanitize' => FALSE));
  }

  /**
   * @inherit
   */
  public function getKeyword() {
    return $this->token->replace('[advertising:adsc_keyword]', array(), array('sanitize' => FALSE));
  }

  /**
   * @inherit
   */
  public function getAdMode() {
    return $this->token->replace('[advertising:adsc_mode]', array(), array('sanitize' => FALSE));
  }

  /**
   * @inherit
   */
  public function getAdProvider() {
    return $this->settings->get('ad_provider');
  }

  /**
   * @inherit
   */
  public function getAdEngine() {
    return $this->settings->get('adsc_ad_engine');
  }

  /**
   * @inherit
   */
  public function getAdContainerTag() {
    return $this->settings->get('adsc_container_tag');
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    return ['url.path'];
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    return $this->settings->getCacheTags();
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    return 0;
  }
}

