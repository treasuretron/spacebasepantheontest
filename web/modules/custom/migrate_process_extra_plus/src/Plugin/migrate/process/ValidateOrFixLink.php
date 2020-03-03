<?php

namespace Drupal\migrate_process_extra_plus\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateException;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;

/**
 * Checks if a link is not broken - tries to fix, accepts blanks
 *
 * Mostly copied from migrate_process_extra/src/Plugin/migrate/process
 *
 * @MigrateProcessPlugin(
 *   id = "validate_or_fix_link"
 * )
 */
class ValidateOrFixLink extends ProcessPluginBase {

  /**
   * Checks if a link does not return 404.
   *
   * @param string $url
   *   The url to be checked.
   *
   * @return bool
   *   URL is not 404.
   */
  private function checkLink($url) {
    $exists = TRUE;
    $file_headers = @get_headers($url);
    if (!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found') {
      $exists = FALSE;
    }
    return $exists;
  }

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (is_string($value)) {
      $value = trim($value);
      if ($this->checkLink($value)) {
        return $value;
      }
      // We're ok with ''
      elseif ( empty($value) ) {
        return '';
      } 
      else {
        // Try again as http
        if ($this->checkLink('http://' . $value)) {
          return 'http://' . $value;
        }
        else {
          throw new MigrateException(sprintf('%s not found (404).', var_export($value, TRUE)));
        }
      }
    }
    else {
      throw new MigrateException(sprintf('%s is not a string.', var_export($value, TRUE)));
    }
  }

}
