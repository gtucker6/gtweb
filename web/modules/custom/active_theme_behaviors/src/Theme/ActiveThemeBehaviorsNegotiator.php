<?php

namespace Drupal\active_theme_behaviors\Theme;

use \Drupal\Core\Routing\RouteMatchInterface;
use \Drupal\Core\Theme\ThemeNegotiator;
/**
 * Class ActiveThemeBehaviorsNegotiator.
 */
class ActiveThemeBehaviorsNegotiator extends ThemeNegotiator implements ActiveThemeBehaviorsNegotiatorInterface, \Drupal\Core\DependencyInjection\ContainerInjectionInterface {

  /**
   * Constructs a new ActiveThemeBehaviorsNegotiator object.
   */
  
  private $entity_types = [];
  
  private $entity_type_manager;
  
  public function __construct(\Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager) {
    $this->entity_type_manager = $entity_type_manager;
  }
  public static function create(\Symfony\Component\DependencyInjection\ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
      );
  }
  
  public function applies(RouteMatchInterface $route_match) {
    if (isset($route_match)) {
      $active_theme = parent::determineActiveTheme($route_match);
      $entity_types = $this->entity_type_manager->getDefinitions();
      $parameters = $route_match->getParameters()->keys();
      $request_path = \Drupal::request()->getRequestUri();
      $route_path = $route_match->getRouteObject();
      
     foreach ($parameters as $parameter_key) {
       if ($route_match->getParameters()->get($parameter_key) instanceof \Drupal\Core\Entity\EntityInterface) {
       }
      }
      
      return TRUE;
    }
    return FALSE;
;
  }
  public function determineActiveTheme(RouteMatchInterface $route_match) {
    $active_theme = parent::determineActiveTheme($route_match);
  }



}
