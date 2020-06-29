<?php

namespace Drupal\paragraphs_view_mode\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'configured_view_mode_widget' widget.
 *
 * @FieldWidget(
 *   id = "configured_view_mode_widget",
 *   label = @Translation("Configured view mode widget"),
 *   field_types = {
 *     "configured_view_mode"
 *   }
 * )
 */
class ConfiguredViewModeWidget extends WidgetBase {

  /**
   * Configured view modes.
   *
   * @var array
   */
  protected $viewModes = [];

  /**
   * {@inheritdoc}
   */
  public function __construct($plugin_id, array $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, array $third_party_settings) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings);
    $entity_bundle = $field_definition->getTargetBundle();
    // Get all view modes for the current bundle.
    $view_modes = get_configured_paragraph_view_modes($entity_bundle);
    $this->viewModes = $view_modes;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['third_party_settings']);
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return ['limit_view_modes' => FALSE, 'available_view_modes' => []];
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {

    $elements = [];

    $elements['limit_view_modes'] = [
      '#type' => 'checkbox',
      '#title' => $this
        ->t('Limit View Modes'),
      '#default_value' => $this->getSetting('limit_view_modes'),
    ];
    $elements['available_view_modes'] = [
      '#type' => 'checkboxes',
      '#options' => ['default' => 'Default'] + $this->viewModes,
      '#title' => 'Available View Modes',
      '#default_value' => $this->getSetting('available_view_modes'),
      '#states' => [
        'visible' => [
          ':input[name="fields[vmode_selector][settings_edit_form][settings][limit_view_modes]"]' => [
            'checked' => TRUE,
          ],
        ],
      ],
    ];
    return $elements;
  }

  /**
   * Returns filtered checkbox options.
   *
   * @return array
   *   Returns the filtered checkbox options.
   */
  public function getFilteredCheckBoxOptions() {
    return array_filter($this->getSetting('available_view_modes'), function ($var) {
      return $var !== 0;
    });
  }

  /**
   * {@inheritDoc}
   */
  public function settingsSummary() {
    $limit_view_mode_selection = $this->getSetting('limit_view_modes');
    if ($limit_view_mode_selection == TRUE) {
      $filtered_options = $this->getFilteredCheckBoxOptions();
      $options = array_intersect_key($this->viewModes, $filtered_options);
      if (isset($filtered_options['default'])) {
        $options['default'] = 'Default';
      }
      return [
        'limit_view_modes' => ['#markup' => 'Limit View Modes: TRUE'],
        'available_view_modes' => ['#markup' => 'Available View Modes: ' . implode(', ', $options)],
      ];
    }
    else {
      return [
        'limit_view_modes' => ['#markup' => 'Limit View Modes: FALSE'],
      ];
    }

  }

  /**
   * {@inheritDoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    if (empty($this->viewModes)) {
      return $values = ['value' => $element];
    }
    else {
      $limit_view_mode_selection = $this->getSetting('limit_view_modes');
      if ($limit_view_mode_selection == TRUE) {
        if (empty($this->getSetting('available_view_modes'))) {
          return $values = ['value' => $element];
        }
        else {
          $filtered_options = $this->getFilteredCheckBoxOptions();
          if (isset($filtered_options['default'])) {
            $options = array_intersect_key($this->viewModes, $filtered_options);
            $options['default'] = 'Default';
          }
          else {
            $options = array_intersect_key($this->viewModes, $filtered_options);
          }

          $select_value = $items[$delta]->value ?: reset($options);
          $element += [
            '#type' => 'select',
            '#options' => $options,
            '#default_value' => $select_value,
            '#element_validate' => [
              [
                $this,
                'validate',
              ],
            ],
            '#delta' => $delta,
          ];
          $values = ['value' => $element];
          return $values;
        }
      }
      else {
        $select_value = $items[$delta]->value ?: 'default';
        $element += [
          '#type' => 'select',
          '#options' => ['default' => 'Default'] + $this->viewModes,
          '#default_value' => $select_value,
          '#element_validate' => [
            [
              $this,
              'validate',
            ],
          ],
          '#delta' => $delta,
        ];
        $values = ['value' => $element];
        return $values;
      }
    }
  }

  /**
   * Gets the available view mode options.
   *
   * @return array
   *   Returns the available view mode options.
   */
  public function getAvailableViewModeOptions() {
    if ($this->isLimited()) {
      if (empty($this->getSetting('available_view_modes'))) {
        return [];
      }
      else {
        $filtered_options = $this->getFilteredCheckBoxOptions();
        if (isset($filtered_options['default'])) {
          $options = array_intersect_key($this->viewModes, $filtered_options);
          $options['default'] = 'Default';
        }
        else {
          $options = array_intersect_key($this->viewModes, $filtered_options);
        }
      }
    }
    else {
      $options = $this->viewModes;
    }
    return $options;
  }

  /**
   * Gets if the view modes are limited.
   *
   * @return bool
   *   Returns true if the view modes are limited, false otherwise
   */
  public function isLimited() {
    return $this->getSetting('limit_view_modes') == 1;
  }

  /**
   * Validates an element.
   *
   * @param mixed $element
   *   The renderable element.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state object.
   */
  public function validate($element, FormStateInterface $form_state) {
    $value = $element['#value'];
    if (strlen($value) === 0) {
      $form_state->setError($element, "Element value does not exist.");
      $form_state->setValueForElement($element, '');
    }
  }

}
