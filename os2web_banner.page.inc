<?php

/**
 * @file
 * Contains os2web_banner.page.inc.
 *
 * Page callback for OS2Web Banner entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for OS2Web Banner templates.
 *
 * Default template: os2web_banner.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_os2web_banner(array &$variables) {
  // Fetch Banner Entity Object.
  $os2web_banner = $variables['elements']['#os2web_banner'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
