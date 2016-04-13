<?php

/**
 * @file
 * Contains Drupal\ivw_integration\IvwTrackerInterface
 */

namespace Drupal\ivw_integration;

use Symfony\Component\DependencyInjection\ContainerInterface;

interface IvwTrackerInterface {
  /**
   * Gets all tracking parameters
   *
   * @return string[]
   *   Array containing 'st', 'cp', 'cpm' and 'sv' parameters.
   */
  function getTrackingInformation();
}

