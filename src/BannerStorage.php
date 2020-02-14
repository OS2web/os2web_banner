<?php

namespace Drupal\os2web_banner;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\os2web_banner\Entity\BannerInterface;

/**
 * Defines the storage handler class for OS2Web Banner entities.
 *
 * This extends the base storage class, adding required special handling for
 * OS2Web Banner entities.
 *
 * @ingroup os2web_banner
 */
class BannerStorage extends SqlContentEntityStorage implements BannerStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(BannerInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {os2web_banner_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {os2web_banner_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(BannerInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {os2web_banner_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('os2web_banner_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
