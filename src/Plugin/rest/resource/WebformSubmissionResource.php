<?php

namespace Drupal\webform_rest\Plugin\rest\resource;

use Drupal\webform\Entity\Webform;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ModifiedResourceResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Creates a resource for retrieving webform elements.
 *
 * @RestResource(
 *   id = "webform_rest_submission",
 *   label = @Translation("Webform Submission"),
 *   uri_paths = {
 *     "canonical" = "/webform_rest/{webform_id}/submission/{sid}"
 *   }
 * )
 */
class WebformSubmissionResource extends ResourceBase {

  /**
   *  Retrieve submission data.
   *
   * @param string $webform_id
   *   The ID of the webform.
   *
   * @param integer $sid
   *   Webform submission ID.
   */
  public function get($webform_id, $sid) {
    
  }
}