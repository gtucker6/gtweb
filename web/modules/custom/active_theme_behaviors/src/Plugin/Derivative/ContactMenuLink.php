<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Drupal\active_theme_behaviors\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Description of ContactMenuLinks
 *
 * @author gloria
 */
class ContactMenuLink extends DeriverBase implements ContainerDeriverInterface {
  //put your code here
  public function __construct($base_plugin_id) {
   
  }
  public static function create(ContainerInterface $container, $base_plugin_id) {
    return new static(
      $base_plugin_id
      );
  }
  public function getDerivativeDefinitions($base_plugin_definition) {
    parent::getDerivativeDefinitions($base_plugin_definition);
  }

  }
