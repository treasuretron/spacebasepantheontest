<?php

namespace Drupal\spacebase_core\Plugin\facets\processor;

use Drupal\facets\FacetInterface;
use Drupal\facets\Processor\BuildProcessorInterface;
use Drupal\facets\Processor\ProcessorPluginBase;

/**
* Provides a processor for CamelCaseLabel.
*
* @FacetsProcessor(
*   id = "camel_case_label",
*   label = @Translation("CamelCaseLabel"),
*   description = @Translation("Transform labels to camel case"),
*   stages = {
*     "build" = 35
*   }
* )
*/
class CamelCaseLabel extends ProcessorPluginBase implements BuildProcessorInterface {

/**
* {@inheritdoc}
*/
public function build(FacetInterface $facet, array $results) {
  $config = $this->getConfiguration();

  /** @var \Drupal\facets\Result\Result $result */
  foreach ($results as $result) {
    $result->setDisplayValue( ucwords($result->getDisplayValue()) );
  }

  return $results;
  }
}