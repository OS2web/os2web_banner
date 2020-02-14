<?php

namespace Drupal\os2web_banner\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining OS2Web Banner entities.
 *
 * @ingroup os2web_banner
 */
interface BannerInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the OS2Web Banner name.
   *
   * @return string
   *   Name of the OS2Web Banner.
   */
  public function getName();

  /**
   * Sets the OS2Web Banner name.
   *
   * @param string $name
   *   The OS2Web Banner name.
   *
   * @return \Drupal\os2web_banner\Entity\BannerInterface
   *   The called OS2Web Banner entity.
   */
  public function setName($name);

  /**
   * Gets the OS2Web Banner creation timestamp.
   *
   * @return int
   *   Creation timestamp of the OS2Web Banner.
   */
  public function getCreatedTime();

  /**
   * Sets the OS2Web Banner creation timestamp.
   *
   * @param int $timestamp
   *   The OS2Web Banner creation timestamp.
   *
   * @return \Drupal\os2web_banner\Entity\BannerInterface
   *   The called OS2Web Banner entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the OS2Web Banner revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the OS2Web Banner revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\os2web_banner\Entity\BannerInterface
   *   The called OS2Web Banner entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the OS2Web Banner revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the OS2Web Banner revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\os2web_banner\Entity\BannerInterface
   *   The called OS2Web Banner entity.
   */
  public function setRevisionUserId($uid);

}
