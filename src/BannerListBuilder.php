<?php

namespace Drupal\os2web_banner;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of OS2Web Banner entities.
 *
 * @ingroup os2web_banner
 */
class BannerListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('OS2Web Banner ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var \Drupal\os2web_banner\Entity\Banner $entity */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.os2web_banner.edit_form',
      ['os2web_banner' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
