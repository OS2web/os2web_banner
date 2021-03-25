<?php

namespace Drupal\os2web_banner\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\os2web_banner\Entity\BannerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class BannerController.
 *
 *  Returns responses for OS2Web Banner routes.
 */
class BannerController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * The date formatter.
   *
   * @var \Drupal\Core\Datetime\DateFormatter
   */
  protected $dateFormatter;

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\Renderer
   */
  protected $renderer;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->dateFormatter = $container->get('date.formatter');
    $instance->renderer = $container->get('renderer');
    return $instance;
  }

  /**
   * Displays a OS2Web Banner revision.
   *
   * @param int $os2web_banner_revision
   *   The OS2Web Banner revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($os2web_banner_revision) {
    $os2web_banner = $this->entityTypeManager()->getStorage('os2web_banner')
      ->loadRevision($os2web_banner_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('os2web_banner');

    return $view_builder->view($os2web_banner);
  }

  /**
   * Page title callback for a OS2Web Banner revision.
   *
   * @param int $os2web_banner_revision
   *   The OS2Web Banner revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($os2web_banner_revision) {
    $os2web_banner = $this->entityTypeManager()->getStorage('os2web_banner')
      ->loadRevision($os2web_banner_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $os2web_banner->label(),
      '%date' => $this->dateFormatter->format($os2web_banner->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of a OS2Web Banner.
   *
   * @param \Drupal\os2web_banner\Entity\BannerInterface $os2web_banner
   *   A OS2Web Banner object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(BannerInterface $os2web_banner) {
    $account = $this->currentUser();
    $os2web_banner_storage = $this->entityTypeManager()->getStorage('os2web_banner');

    $langcode = $os2web_banner->language()->getId();
    $langname = $os2web_banner->language()->getName();
    $languages = $os2web_banner->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $os2web_banner->label()]) : $this->t('Revisions for %title', ['%title' => $os2web_banner->label()]);

    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("revert all os2web banner revisions") || $account->hasPermission('administer os2web banner entities')));
    $delete_permission = (($account->hasPermission("delete all os2web banner revisions") || $account->hasPermission('administer os2web banner entities')));

    $rows = [];

    $vids = $os2web_banner_storage->revisionIds($os2web_banner);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\os2web_banner\BannerInterface $revision */
      $revision = $os2web_banner_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $os2web_banner->getRevisionId()) {
          $link = Link::fromTextAndUr($date, new Url('entity.os2web_banner.revision', [
            'os2web_banner' => $os2web_banner->id(),
            'os2web_banner_revision' => $vid,
          ]));
        }
        else {
          $link = $os2web_banner->toLink($date)->toString();
        }

        $row = [];
        $column = [
          'data' => [
            '#type' => 'inline_template',
            '#template' => '{% trans %}{{ date }} by {{ username }}{% endtrans %}{% if message %}<p class="revision-log">{{ message }}</p>{% endif %}',
            '#context' => [
              'date' => $link,
              'username' => $this->renderer->renderPlain($username),
              'message' => [
                '#markup' => $revision->getRevisionLogMessage(),
                '#allowed_tags' => Xss::getHtmlTagList(),
              ],
            ],
          ],
        ];
        $row[] = $column;

        if ($latest_revision) {
          $row[] = [
            'data' => [
              '#prefix' => '<em>',
              '#markup' => $this->t('Current revision'),
              '#suffix' => '</em>',
            ],
          ];
          foreach ($row as &$current) {
            $current['class'] = ['revision-current'];
          }
          $latest_revision = FALSE;
        }
        else {
          $links = [];
          if ($revert_permission) {
            $links['revert'] = [
              'title' => $this->t('Revert'),
              'url' => $has_translations ?
              Url::fromRoute('entity.os2web_banner.translation_revert', [
                'os2web_banner' => $os2web_banner->id(),
                'os2web_banner_revision' => $vid,
                'langcode' => $langcode,
              ]) :
              Url::fromRoute('entity.os2web_banner.revision_revert', [
                'os2web_banner' => $os2web_banner->id(),
                'os2web_banner_revision' => $vid,
              ]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.os2web_banner.revision_delete', [
                'os2web_banner' => $os2web_banner->id(),
                'os2web_banner_revision' => $vid,
              ]),
            ];
          }

          $row[] = [
            'data' => [
              '#type' => 'operations',
              '#links' => $links,
            ],
          ];
        }

        $rows[] = $row;
      }
    }

    $build['os2web_banner_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
