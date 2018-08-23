<?php

namespace Drupal\active_theme_behaviors\Controller;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\Entity\ConfigEntityTypeInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\Controller\EntityController;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Render\ElementInfoManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Route;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Access\AccessResultAllowed;
use Drupal\Core\Entity\EntityType;
use Drupal\contact\Entity\ContactForm;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Url;
use Drupal\Core\Entity\BundleEntityFormBase;

/**
 * Class EntityFormController.
 */
class FormController extends ControllerBase implements ContainerInjectionInterface {

   
    public function __construct(ConfigFactoryInterface $config_factory, EntityTypeManagerInterface $entity_type_manager, ModuleHandlerInterface $module_handler) {
        $this->configFactory = $config_factory;
        $this->entityTypeManager = $entity_type_manager;
        $this->moduleHandler = $module_handler;
    }
    
    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container) {
        return new static(
            $container->get('config.factory'),
            $container->get('entity_type.manager'),
            $container->get('module_handler')
            );
    }
    
    /**
     * Handle Contact Forms.
     */
    
    public function contactDefault() {
        $config = \Drupal::config('system.site');
        $front_uri = $config->get('page.front');
        $alias = \Drupal::service('path.alias_manager')->getAliasByPath($front_uri);
        $build = [
            '#markup' => $this->t('Contact Me'),
        ];
        return $build;
    }
    
    /**
     * Handle Contact Form Titles.
     */
    public function contactDefaultTitle() {

        $build = [
            '#markup' => 'Contact',
        ];
        return $build;;
    }
    /**
     * Handle Entity Contact Forms.
     */
    
    public function contact(string $entity_type, string $form_name) {
        // Create get contact forms from entity types.
        $definitions = array_keys($this->entityTypeManager->getDefinitions());
        if(!in_array($entity_type, $definitions)) {
            return False;
        }
        
        $build = [
            '#markup' => $this->t('Hello World!'),
        ];
        return $build;
    }
    
    /**
     * Handle Entity Contact Form Titles.
     */
    public function contactTitle(string $entity_type, string $form_name) {
        // Needs Local Tasks
        $build = [
            '#markup' => $this->t('Hello World!'),
        ];
        return $build;
        
    }
    public function checkContactFormAccess(string $entity_type, string $form_name) {
        if ($entity_type->getBundleEntityType() === 'node') {
            return AccessResultAllowed::allowedIf($entity_type->getBundleOf() . ': Create new content');
        } elseif($entity_type instanceof ContactForm) {
            
        }
        return AccessResultAllowed::allowedIf('access ');
    }
    
    public function checkContactAccess() {
        return AccessResultAllowed::allowedIf(TRUE);
    }
    

  
    protected function getAddPageRoute(EntityTypeInterface $entity_type) {
    if ($entity_type->hasLinkTemplate('add-page') && $entity_type->getKey('bundle')) {
      $route = new Route($entity_type->getLinkTemplate('add-page'));
      $route->setDefault('_controller', EntityController::class . '::addPage');
      $route->setDefault('_title_callback', EntityController::class . '::addTitle');
      $route->setDefault('entity_type_id', $entity_type->id());
      $route->setRequirement('_entity_create_any_access', $entity_type->id());

      return $route;
    }
  }
  
    protected function getAddFormRoute(EntityTypeInterface $entity_type) {
    if ($entity_type->hasLinkTemplate('add-form')) {
      $entity_type_id = $entity_type->id();
      $route = new Route($entity_type->getLinkTemplate('add-form'));
      // Use the add form handler, if available, otherwise default.
      $operation = 'default';
      if ($entity_type->getFormClass('add')) {
        $operation = 'add';
      }
      $route->setDefaults([
        '_entity_form' => "{$entity_type_id}.{$operation}",
        'entity_type_id' => $entity_type_id,
      ]);

      // If the entity has bundles, we can provide a bundle-specific title
      // and access requirements.
      $expected_parameter = $entity_type->getBundleEntityType() ?: $entity_type->getKey('bundle');
      // @todo: We have to check if a route contains a bundle in its path as
      //   test entities have inconsistent usage of "add-form" link templates.
      //   Fix it in https://www.drupal.org/node/2699959.
      if (($bundle_key = $entity_type->getKey('bundle')) && strpos($route->getPath(), '{' . $expected_parameter . '}') !== FALSE) {
        $route->setDefault('_title_callback', EntityController::class . '::addBundleTitle');
        // If the bundles are entities themselves, we can add parameter
        // information to the route options.
        if ($bundle_entity_type_id = $entity_type->getBundleEntityType()) {
          $bundle_entity_type = $this->entityTypeManager->getDefinition($bundle_entity_type_id);

          $route
            // The title callback uses the value of the bundle parameter to
            // fetch the respective bundle at runtime.
            ->setDefault('bundle_parameter', $bundle_entity_type_id)
            ->setRequirement('_entity_create_access', $entity_type_id . ':{' . $bundle_entity_type_id . '}');

          // Entity types with serial IDs can specify this in their route
          // requirements, improving the matching process.
          if ($this->getEntityTypeIdKeyType($bundle_entity_type) === 'integer') {
            $route->setRequirement($entity_type_id, '\d+');
          }

          $bundle_entity_parameter = ['type' => 'entity:' . $bundle_entity_type_id];
          if ($bundle_entity_type instanceof ConfigEntityTypeInterface) {
            // The add page might be displayed on an admin path. Even then, we
            // need to load configuration overrides so that, for example, the
            // bundle label gets translated correctly.
            // @see \Drupal\Core\ParamConverter\AdminPathConfigEntityConverter
            $bundle_entity_parameter['with_config_overrides'] = TRUE;
          }
          $route->setOption('parameters', [$bundle_entity_type_id => $bundle_entity_parameter]);
        }
        else {
          // If the bundles are not entities, the bundle key is used as the
          // route parameter name directly.
          $route
            ->setDefault('bundle_parameter', $bundle_key)
            ->setRequirement('_entity_create_access', $entity_type_id . ':{' . $bundle_key . '}');
        }
      }
      else {
        $route
          ->setDefault('_title_callback', EntityController::class . '::addTitle')
          ->setRequirement('_entity_create_access', $entity_type_id);
      }

      return $route;
    }
  }



}
