<?php

namespace Drupal\active_theme_behaviors\Theme;

use Drupal\Core\Theme\ThemeNegotiatorInterface;
use Drupal\Core\Routing\RouteMatchInterface;
/**
 * Interface ActiveThemeBehaviorsNegotiatorInterface.
 */
interface ActiveThemeBehaviorsNegotiatorInterface extends ThemeNegotiatorInterface {
    /**
   * Whether this theme negotiator should be used to set the theme.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The current route match object.
   *
   * @return bool
   *   TRUE if this negotiator should be used or FALSE to let other negotiators
   *   decide.
   */
  public function applies(RouteMatchInterface $route_match);

  /**
   * Determine the active theme for the request.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The current route match object.
   *
   * @return string|null
   *   The name of the theme, or NULL if other negotiators, like the configured
   *   default one, should be used instead.
   */
  public function determineActiveTheme(RouteMatchInterface $route_match);
}
