<?php

namespace Drupal\ivw_integration\Plugin\Block;

use Drupal\ivw_integration\IvwTracker;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Url;
use Drupal\Core\Cache\Cache;
use Drupal\taxonomy\TermInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a IVW call block.
 *
 * @Block(
 *   id = "ivw_integration_call_block",
 *   admin_label = @Translation("IVW call"),
 * )
 */
class IvwCallBlock extends BlockBase implements ContainerFactoryPluginInterface {
  /**
   * The main menu object.
   *
   * @var \Drupal\ivw_integration\IvwTracker
   */
  protected $ivwTracker;

  /**
   * Constructs an Related Content object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   *
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\ivw_integration\IvwTracker $ivw_tracker
   *   The ivw tracker object.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    IvwTracker $ivw_tracker
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->ivwTracker = $ivw_tracker;
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
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('ivw_integration.tracker')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $tracker = $this->ivwTracker->getTrackingInformation();

    // site is missing, do not render tag
    if (empty($tracker['st'])){
      return [];
    }

    return array(
      'ivw_call' => array(
        '#theme' => 'ivw_call'
      ),
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    return AccessResult::allowedIfHasPermission($account, 'access content');
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    $parent_tags = parent::getCacheTags();
    return Cache::mergeTags($parent_tags, $this->ivwTracker->getCacheTags());
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    $parent_context = parent::getCacheContexts();
    return Cache::mergeContexts($parent_context, $this->ivwTracker->getCacheContexts());
  }

}
