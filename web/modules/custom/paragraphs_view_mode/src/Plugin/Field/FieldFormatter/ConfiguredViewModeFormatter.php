<?php

namespace Drupal\paragraphs_view_mode\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;

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
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    // The ProcessedText element already handles cache context & tag bubbling.
    // @see \Drupal\filter\Element\ProcessedText::preRenderText()
    foreach ($items as $delta => $item) {
      $field_config = $item->getDataDefinition()->getFieldDefinition();
      $field_view_modes = \Drupal::service('entity_display.repository')->getViewModes($field_config->get('entity_type'));
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
