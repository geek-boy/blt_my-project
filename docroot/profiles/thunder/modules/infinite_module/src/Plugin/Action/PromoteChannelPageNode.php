<?php

/**
 * @file
 * Contains \Drupal\infinite_article\Plugin\Action\PromoteHomePresenterNode.
 */

namespace Drupal\infinite_base\Plugin\Action;

use Drupal\Core\Action\ActionBase;
use Drupal\Core\Session\AccountInterface;

/**
 * Promotes a node to channel page.
 *
 * @Action(
 *   id = "node_promote_channel_page_action",
 *   label = @Translation("Promote selected content to channel page"),
 *   type = "node"
 * )
 */
class PromoteChannelPageNode extends ActionBase {

  /**
   * {@inheritdoc}
   */
  public function execute($entity = NULL) {
    if ($entity->hasField('promote_channel')) {
      $entity->set('promote_channel', 1);
      $entity->save();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function access($object, AccountInterface $account = NULL, $return_as_object = FALSE) {
    /** @var \Drupal\node\NodeInterface $object */
    $access = $object->access('update', $account, TRUE)
      ->andif($object->promote->access('edit', $account, TRUE));
    return $return_as_object ? $access : $access->isAllowed();
  }

}
