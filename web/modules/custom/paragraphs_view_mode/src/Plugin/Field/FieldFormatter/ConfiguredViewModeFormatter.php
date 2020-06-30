<?php

namespace Drupal\paragraphs_view_mode\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'configured_view_mode_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "configured_view_mode_formatter",
 *   label = @Translation("Configured view mode formatter"),
 *   field_types = {
 *     "configured_view_mode"
 *   }
 * )
 */
class ConfiguredViewModeFormatter extends FormatterBase {

  /**
   * The entity repository service.
   *
   * @var \Drupal\Core\Entity\EntityDisplayRepositoryInterface
   */
  protected $entityDisplayRepository;

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = parent::create(
      $container,
      $configuration,
      $plugin_id,
      $plugin_definition
    );
    $instance->entityDisplayRepository = $container->get('entity_display.repository');
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    // The ProcessedText element already handles cache context & tag bubbling.
    // @see \Drupal\filter\Element\ProcessedText::preRenderText()
    foreach ($items as $delta => $item) {
      $field_config = $item->getDataDefinition()->getFieldDefinition();
      $field_view_modes = $this->entityDisplayRepository->getViewModes($field_config->get('entity_type'));
      $elements[$delta] = [
        '#type' => 'processed_text',
        '#text' => $item->value === 'default' ? 'Default' : $field_view_modes[$item->value]['label'],
        '#format' => $item->format,
        '#langcode' => $item->getLangcode(),
      ];
    }
    return $elements;
  }

}
