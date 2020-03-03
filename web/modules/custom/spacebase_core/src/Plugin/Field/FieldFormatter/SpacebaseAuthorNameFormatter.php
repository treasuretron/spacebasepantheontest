<?php

namespace Drupal\spacebase_core\Plugin\Field\FieldFormatter;

use Drupal\user\Entity\User;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;

/**
 * Plugin implementation of the 'Author name' formatter.
 *
 * @FieldFormatter(
 *   id = "sb_author_names",
 *   label = @Translation("Author name(s)"),
 *   field_types = {
 *     "entity_reference"
 *   }
 * )
 */
class SpacebaseAuthorNameFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    $summary[] = $this->t('Displays the author\'s name(s).');
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];

    foreach ($items as $delta => $item) {
      // Load the user entity for each item value
      /** @var \Drupal\user\Entity\User $user */
      $user = \Drupal::entityTypeManager()->getStorage('user')->load($item->target_id);
      $name = $user->get('field_first_name_user')->value . ' ' . $user->get('field_last_name_user')->value;
      // Render each element as markup.
      $element[$delta] = ['#markup' => $name];
    }

    return $element;
  }

}
