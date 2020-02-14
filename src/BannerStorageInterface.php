<?php

namespace Drupal\os2web_banner;

use Drupal\Core\Entity\ContentEntityStorageInterface;
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
interface BannerStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of OS2Web Banner revision IDs for a specific OS2Web Banner.
   *
   * @param \Drupal\os2web_banner\Entity\BannerInterface $entity
   *   The OS2Web Banner entity.
   *
   * @return int[]
   *   OS2Web Banner revision IDs (in ascending order).
   */
  public function revisionIds(BannerInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as OS2Web Banner author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   OS2Web Banner revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\os2web_banner\Entity\BannerInterface $entity
   *   The OS2Web Banner entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(BannerInterface $entity);

  /**
   * Unsets the language for all OS2Web Banner with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
