<?php

namespace Drupal\toggle_editable_fields\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Build a form to switch state of targeted FieldItem.
 */
class AjaxToggleForm extends FormBase {

  /**
   * The FieldItem being targeted by this form.
   *
   * @var \Drupal\Core\Field\FieldItemInterface
   */
  protected $fieldItem;

  /**
   * The entity being used by this form.
   *
   * @var \Drupal\Core\Entity\EntityInterface
   */
  protected $entity;

  /**
   * The current FieldItem name.
   *
   * @var string
   */
  protected $fieldName;

  /**
   * The current FieldItem delta.
   *
   * @var int
   */
  protected $delta;

  /**
   * Default value of current FieldItem.
   *
   * @var bool
   */
  protected $defaultValue;

  /**
   * The field item plugin settings.
   *
   * @var array
   */
  protected $fieldSettings;

  /**
   * Initialize this Form Builder with FieldItem definition.
   *
   * Drupal only supports one form with a given ID per page,
   * so we generate a fieldItem specific ID at getFormId().
   *
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *   FieldItem to be displayed.
   * @param array $settings
   *   The formatter settings.
   */
  public function setFieldItem(FieldItemInterface $item, array $settings = []) {
    $this->fieldItem = $item;
    $this->entity = $this->fieldItem->getEntity();
    $this->fieldName = $this->fieldItem->getFieldDefinition()->getName();
    $this->delta = $this->fieldItem->getName();
    $this->defaultValue = $this->fieldItem->value;
    $this->fieldSettings = $settings;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    $parts = [
      'editable_ajax_toggle',
      $this->entity->getEntityTypeId(),
      $this->entity->id(),
      $this->fieldName,
      $this->delta,
      'form',
    ];
    return implode('_', $parts);
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['#tree'] = TRUE;
    $form['checkbox'] = [
      '#type' => 'checkbox',
      '#default_value' => $this->defaultValue,
      '#attributes' => [
        'class' => ['checkbox-toggle'],
      ],
      '#ajax' => [
        'callback' => [$this, 'formListAjax'],
        'event' => 'change',
        'progress' => [
          'type' => 'none',
        ],
      ],
      '#disabled' => !( $this->entity->access('update') && $this->fieldItem->getEntity()->get($this->fieldName)->access('update') ),
    ];

    // Add toggle library and set attribute, unless set to just show a checkbox
    if ( $this->fieldSettings['toggle'] ) {
      $form['checkbox']['#attributes']['data-toggle'] = 'toggle';
      $form['#attached']['library'][] = 'toggle_editable_fields/bootstrap.toogle';
      $this->setBooststrapDataAttributes($form['checkbox']);
    }
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {}

  /**
   * Update the clicked field with given value.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   *   The current AjaxResponse.
   *
   * @throws \Exception
   *   Thrown when the entity can't found the clicked field name.
   */
  public function formListAjax(array &$form, FormStateInterface $form_state) {
    $element = $form_state->getTriggeringElement();
    if (!empty($element)) {
      $this->updateFieldValue($form_state->getValue($element['#parents']));
    }

    return new AjaxResponse();
  }

  /**
   * Update the clicked field with given value.
   *
   * @param bool $value
   *   Value given by user.
   *
   * @throws \Exception
   *   Thrown when the entity can't found the clicked field name.
   */
  public function updateFieldValue($value) {
    if (!$this->entity->hasField($this->fieldName)) {
      throw new \Exception("No field $this->fieldName found in {$this->entity->id()} entity.");
    }

    // Checks update access for the node and the field
    $fieldItemList = $this->fieldItem->getEntity()->get($this->fieldName);
    if ($this->entity->access('update') && $fieldItemList->access('update')) {
      $this->entity->get($this->fieldName)->set($this->delta, $value);
      $this->entity->save();
    }
  }

  /**
   * Set booststrap data attributes for given element.
   *
   * @param array $element
   *   An associative array containing the part of the form structure,
   *   representing checkbox element.
   */
  public function setBooststrapDataAttributes(array &$element) {
    foreach ($this->fieldSettings as $data_id => $data_value) {
      if ($data_value != NULL && !isset($element['#attributes']["data-$data_id"])) {
        $element['#attributes']["data-$data_id"] = $data_value;
      }
    }
  }

}
