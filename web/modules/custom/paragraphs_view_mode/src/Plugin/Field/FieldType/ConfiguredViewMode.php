<?php

namespace Drupal\paragraphs_view_mode\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'configured_view_mode' field type.
 *
 * @FieldType(
 *   id = "configured_view_mode",
 *   label = @Translation("Configured view mode"),
 *   description = @Translation("My Field Type"),
 *   default_widget = "configured_view_mode_widget",
 *   default_formatter = "configured_view_mode_formatter",
 *   no_ui = "TRUE",
 * )
 */
class ConfiguredViewMode extends FieldItemBase {

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
