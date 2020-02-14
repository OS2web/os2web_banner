<?php

namespace Drupal\os2web_banner\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for OS2Web Banner entities.
 */
class BannerViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.
    return $data;
  }

}
