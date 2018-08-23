<?php

namespace Drupal\vmode_selector\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Plugin extends the 'options_key' formatter.
 *
 * @FieldFormatter(
 *   id = "vmode_selector_format",
 *   label = @Translation("Selected view mode name as text"),
 *   field_types = {
 *     "vmode_selector_list"
 *   }
 * )
 */
class VModeSelectorFormatter extends FormatterBase {

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
