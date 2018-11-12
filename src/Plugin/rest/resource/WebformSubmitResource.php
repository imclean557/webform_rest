<?php

namespace Drupal\webform_rest\Plugin\rest\resource;

use Drupal\webform\Entity\Webform;
use Drupal\webform\WebformSubmissionForm;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ModifiedResourceResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Creates a resource for submitting a webform.
 *
 * @RestResource(
 *   id = "webform_rest_submit",
 *   label = @Translation("Webform Submit"),
 *   uri_paths = {
 *     "canonical" = "/webform_rest/submit",
 *     "https://www.drupal.org/link-relations/create" = "/webform_rest/submit"
 *   }
 * )
 */
class WebformSubmitResource extends ResourceBase {

  /**
   * Responds to entity POST requests and saves the new entity.
   *
   * @param array $webform_data
   *   Webform field data and webform ID.
   *
   * @return \Drupal\rest\ResourceResponse
   *   The HTTP response object.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\HttpException
   *   Throws HttpException in case of error.
   */
  public function post(array $webform_data) {

    // Basic check for webform ID.
    if (empty($webform_data['webform_id'])) {
      $errors = [
        'error' => [
          'code' => '500'
        ]
      ];
      return new JsonResponse($errors, 500);
    }

    // Convert to webform values format.
    $values = [
      'webform_id' => $webform_data['webform_id'],
      'entity_type' => NULL,
      'entity_id' => NULL,
      'in_draft' => FALSE,
      'uri' => '/webform/' . $webform_data['webform_id'] . '/api',
    ];

    $values['data'] = $webform_data;

    // Don't submit webform ID.
    unset($values['data']['webform_id']);

    // Check for a valid webform.
    $webform = Webform::load($values['webform_id']);
    if (!$webform) {
      $errors = [
        'error' => [
          'message' => 'Invalid webform_id value.'
        ]
      ];
      return new ModifiedResourceResponse($errors);
    }

    // Check webform is open.
    $is_open = WebformSubmissionForm::isOpen($webform);

    if ($is_open === TRUE) {
      // Validate submission.
      $errors = WebformSubmissionForm::validateFormValues($values);

      // Check there are no validation errors.
      if (!empty($errors)) {
        $errors = ['error' => $errors];
        return new ModifiedResourceResponse($errors);
      }
      else {
        // Return submission ID.
        $webform_submission = WebformSubmissionForm::submitFormValues($values);
        return new ModifiedResourceResponse(['sid' => $webform_submission->id()]);
      }
    }
    else {
      $errors = [
        'error' => [
          'message' => 'This webform is closed, or too many submissions have been made.'
        ]
      ];
      return new ModifiedResourceResponse($errors);
    }
  }

}
