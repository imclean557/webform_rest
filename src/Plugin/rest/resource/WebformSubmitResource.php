<?php

/**
 * @file
 * Contains \Drupal\webform_rest\Plugin\rest\resource\WebformSubmitResource.
 */

namespace Drupal\webform_rest\Plugin\rest\resource;

use Drupal\webform\Entity\WebformSubmission;
use Drupal\Core\Entity\EntityStorageException;
use Drupal\Core\Form\FormState;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

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
   * @param $webform_data
   *   Webform field data and webform ID.
   *
   * @return \Drupal\rest\ResourceResponse
   *   The HTTP response object.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\HttpException
   *   Throws HttpException in case of error.
   */
  public function post($webform_data) {

    // Basic check for webform ID.
    if (empty($webform_data['webform_id'])) {
      return new Response('', 500);
    }

    // Create webform submission object.
    $webform_submission = WebformSubmission::create(['webform_id' => $webform_data['webform_id']]);

    // Don't submit webform ID
    unset($webform_data['webform_id']);

    // Get the form object.
    $entity_form_object = \Drupal::entityTypeManager()
      ->getFormObject('webform_submission', 'default');
    $entity_form_object->setEntity($webform_submission);

    // Initialize the form state.
    $form_state = (new FormState())->setValues($webform_data);

    // Submit form.
    \Drupal::formBuilder()->submitForm($entity_form_object, $form_state);

    $errors = $form_state->getErrors();

    // Check there are no validation errors.
    if (!empty($errors)) {
      return new ResourceResponse($errors);
    }

    // Save webform submission
    try {
      $webform_submission->save();
      return new ResourceResponse(['sid' => $webform_submission->id()]);
    }
    catch (EntityStorageException $e) {
      throw new HttpException(500, 'Internal Server Error', $e);
    }
    return new Response('', 200);
  }

}
