<?php

/**
 * @file
 * Contains \Drupal\webform_rest\Plugin\rest\resource\WebformElementsResource.
 */

namespace Drupal\webform_rest\Plugin\rest\resource;

use Drupal\webform\Entity\Webform;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Creates a resource for retrieving webform elements.
 *
 * @RestResource(
 *   id = "webform_elements",
 *   label = @Translation("Webform Elements"),
 *   uri_paths = {
 *     "canonical" = "/webform_elements/{webform_id}"
 *   }
 * )
 */
class WebformElementsResource extends ResourceBase {
  /**
   * Responds to entity GET requests for webform elements.
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
  public function get($webform_id) {
    if (empty($webform_id)) {
      throw new HttpException(t('Webform ID wasn\'t provided'));
    }

    // Load the webform.
    $webform = Webform::load($webform_id);

    // Basic check to see if something's returned.
    if ($webform) {

      // Grab the form in its entirety.
      $form = $webform->getSubmissionForm();

      // Return only the form elements.
      return new ResourceResponse($form['elements']);
    }

    throw new HttpException(t('Can\'t load webform.'));
   
  }

}
