<?php

namespace Drupal\webform_rest\Plugin\rest\resource;

use Drupal\webform\Entity\WebformSubmission;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ModifiedResourceResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Creates a resource for retrieving webform submission data.
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
   * Retrieve submission data.
   *
   * @param string $webform_id
   *   Webform ID.
   *
   * @param int $sid
   *   Submission ID.
   *
   * @return \Drupal\rest\ModifiedResourceResponse
   *   HTTP response object containing webform submission.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\HttpException
   *   Throws HttpException in case of error.
   */
  public function get($webform_id, $sid) {

    if (empty($webform_id) || empty($sid)) {
      $errors = [
        'error' => [
          'message' => 'Both webform ID and submission ID are required.'
        ]
      ];
      return new ModifiedResourceResponse($errors);
    }

    // Load the webform submission.
    $webform_submission = WebformSubmission::load($sid);

    // Basic check to see if something's returned.
    if ($webform_submission) {

      // Grab submission data.
      $data = $webform_submission->getData();

      $response = [
        'entity' => $webform_submission,
        'data' => $data
      ];

      // Return the submission.
      return new ModifiedResourceResponse($response);
    }

    throw new HttpException(t("Can't load webform submission."));

  }

}
