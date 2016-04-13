<?php
/**
 * @file
 * Contains \Drupal\riddle_marketplace\Controller\RiddleController.
 */

namespace Drupal\riddle_marketplace\Controller;

use Drupal\Core\Controller\ControllerBase;

class RiddleController extends ControllerBase {


  public function riddleIframe() {

    $config = \Drupal::service('config.factory')->getEditable(
      'riddle_marketplace.settings'
    );
    $token = $config->get('riddle_marketplace.token');

   return [
      '#theme' => 'riddle_backend',
      '#token' => $token,
    ];
  }
}
