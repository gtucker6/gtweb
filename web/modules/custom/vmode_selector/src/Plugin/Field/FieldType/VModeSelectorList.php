<?php

/**
 * Created by PhpStorm.
 * User: gloria
 * Date: 3/9/18
 * Time: 7:47 AM
 */
namespace Drupal\vmode_selector\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;



/**
 * Plugin implementation of the 'vmode_selector_list' field type.
 *
 * @FieldType(
 *   id = "vmode_selector_list",
 *   label = @Translation("View Mode Select List"),
 *   description = @Translation("This field stores the entity view mode."),
 *   default_widget = "vmode_selector_widget",
 *   default_formatter = "vmode_selector_format",
 * )
 */
class VModeSelectorList extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return [
      'columns' => [
        'value' => [
          'type' => 'varchar',
          'length' => 255,
        ],
      ],
      'indexes' => [
        'value' => ['value'],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {

    $properties['value'] = DataDefinition::create('string')
      ->setLabel(t('View mode'))
      ->addConstraint('Length', ['max' => 255])
      ->setRequired(TRUE);

    return $properties;
  }

  /**
   * {@inheritdoc}
   *
   * @throws \Drupal\Core\TypedData\Exception\MissingDataException
   */
  public function isEmpty() {
    $value = $this->get('value')->getValue();
    return $value === NULL || $value === '';
  }

}
