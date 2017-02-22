<?php

/**
 * @file
 * Contains \Drupal\webform_rest\Plugin\rest\resource\WebformSubmitResource.
 */

namespace Drupal\webform_rest\Plugin\rest\resource;

use Drupal\webform\Entity\WebformSubmission;
use Drupal\Core\Entity\EntityStorageException;
use Drupal\Core\Form\FormState;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\rest\Plugin\ResourceBase;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Creates a resource for submitting a webform.
 *
 * @RestResource(
 *   id = "webform_submit",
 *   label = @Translation("Webform Submit"),
 *   uri_paths = {
 *     "canonical" = "/webform_submit"
 *   }
 * )
 */
class WebformSubmitResource extends ResourceBase {

   /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->getParameter('serializer.formats'),
      $container->get('logger.factory')->get('rest'),
      $container->get('current_user')
    );
  }

  /**
   * Constructs a Drupal\rest\Plugin\ResourceBase object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param array $serializer_formats
   *   The available serialization formats.
   * @param \Psr\Log\LoggerInterface $logger
   *   A logger instance.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The configuration factory.
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   The current user.
   */
  public function __construct(array $configuration,
    $plugin_id,
    $plugin_definition,
    array $serializer_formats,
    LoggerInterface $logger,
    AccountProxyInterface $current_user) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);
    $this->currentUser = $current_user;
  }

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
    if (empty($errors)) {
      // Save webform submission
      try {
        $webform_submission->save();
        print $webform_submission->id();
      }
      catch (EntityStorageException $e) {
        throw new HttpException(500, 'Internal Server Error', $e);
      }

    }
    return new Response('', 200);

    // @TODO Handle errors.
  }



}
