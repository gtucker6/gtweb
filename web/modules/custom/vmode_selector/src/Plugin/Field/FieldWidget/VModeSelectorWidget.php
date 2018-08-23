<?php

namespace Drupal\vmode_selector\Plugin\Field\FieldWidget;

use Drupal\Core\Entity\EntityDisplayRepositoryInterface;
use Drupal\Core\Field\Plugin\Field\FieldWidget\OptionsWidgetBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin extends the 'text_default' formatter.
 *
 * @FieldWidget(
 *   id = "vmode_selector_widget",
 *   label = @Translation("Selected view mode name as text"),
 *   field_types = {
 *     "vmode_selector_list"
 *   }
 * )
 */
class VModeSelectorWidget extends OptionsWidgetBase implements ContainerFactoryPluginInterface {

  protected $viewModes = [];

  protected $fieldDefinition;

  /**
   * {@inheritdoc}
   */
  public function __construct($plugin_id, array $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, array $third_party_settings, EntityDisplayRepositoryInterface $entityDisplayRepository) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings);

    $this->fieldDefinition = $field_definition;
    $entity_type = $this->fieldDefinition->getTargetEntityTypeId();

    // Get all view modes for the current bundle.
    $view_modes = $entityDisplayRepository->getViewModes($entity_type);

    if (!in_array('default', $view_modes)) {
      $view_modes['default']['label'] = 'Default';
    }

    foreach ($view_modes as $key => $view_mode) {
      $this->viewModes[$key] = $view_mode['label'];
    }

  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $entity_display_repo = $container->get('entity_display.repository');
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['third_party_settings'],
      $entity_display_repo);
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {

    $vmode_keys = array_keys($this->viewModes);
    $select_value = $items[$delta]->value ?: reset($vmode_keys);

    $element += [
      '#type' => 'select',
      '#options' => $this->viewModes,
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

  /**
   * {@inheritdoc}
   */
  public function validate($element, FormStateInterface $form_state) {
    $value = $element['#value'];
    if (strlen($value) === 0) {
      $form_state->setError($element, "Element value does not exist.");
      $form_state->setValueForElement($element, '');
    }
  }

}
