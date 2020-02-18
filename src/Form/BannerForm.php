<?php

namespace Drupal\os2web_banner\Form;

use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form controller for OS2Web Banner edit forms.
 *
 * @ingroup os2web_banner
 */
class BannerForm extends ContentEntityForm {

  /**
   * The current user account.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $account;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    $instance = parent::create($container);
    $instance->account = $container->get('current_user');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var \Drupal\os2web_banner\Entity\Banner $entity */
    $form = parent::buildForm($form, $form_state);
    $build_info = $form_state->getBuildInfo();
    $form['#id'] = Html::getUniqueId($build_info['form_id']);
    self::adjustForm($form, $form_state);

    if (!$this->entity->isNew()) {
      $form['new_revision'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Create new revision'),
        '#default_value' => FALSE,
        '#weight' => 10,
      ];
    }
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;

    // Save as a new revision if requested to do so.
    if (!$form_state->isValueEmpty('new_revision') && $form_state->getValue('new_revision') != FALSE) {
      $entity->setNewRevision();

      // If a new revision is created, save the current user as revision author.
      $entity->setRevisionCreationTime($this->time->getRequestTime());
      $entity->setRevisionUserId($this->account->id());
    }
    else {
      $entity->setNewRevision(FALSE);
    }

    $status = parent::save($form, $form_state);

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label OS2Web Banner.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label OS2Web Banner.', [
          '%label' => $entity->label(),
        ]));
    }
    $form_state->setRedirect('entity.os2web_banner.canonical', ['os2web_banner' => $entity->id()]);
  }
  /**
   * Function that do adjust form for custom view.
   *
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   */
  public static function adjustForm(array &$form, FormStateInterface $form_state) {
    $wrapper_id = $form['#id'];
    $form['type']['widget']['#ajax'] = [
      'callback' => [static::class, 'ajaxCallback'],
      'wrapper' => $wrapper_id,
    ];

    $form['field_os2web_banner_butt_link']['widget']['#ajax'] = [
      'callback' => [static::class, 'ajaxCallback'],
      'wrapper' => $wrapper_id,
    ];

    $entity = $form_state->getFormObject()->getEntity();
    $field_os2web_banner_butt_link = $entity->field_os2web_banner_butt_link;
    $link_type = NestedArray::getValue($form_state->getUserInput(), $form['field_os2web_banner_butt_link']['widget']['#parents']);
    if (empty($link_type)) {
      if (!$field_os2web_banner_butt_link->isEmpty()) {
        $link_type = $field_os2web_banner_butt_link->first()->value;
      }
      else {
        $link_type = '_none';
      }
    }
    switch ($link_type) {
      case '_none':
        $form['field_os2web_banner_butt_link_i']['#access'] = FALSE;
        $form['field_os2web_banner_butt_link_e']['#access'] = FALSE;
        break;

      case 'internal':
        $form['field_os2web_banner_butt_link_e']['#access'] = FALSE;
        break;

      case 'external':
        $form['field_os2web_banner_butt_link_i']['#access'] = FALSE;
        break;
    }
  }

  public static function ajaxCallback(array $form, FormStateInterface $form_state) {
    $triggering_element = $form_state->getTriggeringElement();
    $banner_form_parents = [];
    foreach ($triggering_element['#field_parents'] as $key) {
      $banner_form_parents[] = $key;
      if (strpos($key, 'field_') === 0) {
        $banner_form_parents[] = 'widget';
      }
    }
    // @TODO Integration with Inline entity forms still works not well
    // on edit mode.
    $banner_form = NestedArray::getValue($form, $banner_form_parents);
    return $banner_form;
  }
}
