<?php

namespace Wpce\Request;

use Wpce\Utils\Get;

/**
 * A request processor that unifies the way data-related scripts are
 * being processed by enforcing strinct flow of actions.
 *
 * Classes extending RequestProcessor, apart from implementing abstract
 * methods may have the following methods implemented:
 * - `protected function validatePrerequisites()` when any prerequisites
 * not related to request's data need to be validated, eg. user session,
 * - `protected function validateField($fieldName, $fieldValue)` that should
 * implement logic to validate any request's data values.
 *
 * Additionally, classes should use the $response property's Response class
 * instance to set request errors or response data.
 */
abstract class RequestProcessor {

  protected $requestData = [];
  protected Response $response;

  /**
   * Returns handle which is, by default, called class name without
   * namespace. Can be used eg. in case 3rd party scripts like WordPress'
   * ajax-related actions need a string handle to register and later execute
   * a given ajax script.
   *
   * @return string
   */
  public static function getHandle(): string {
    return (new \ReflectionClass(get_called_class()))->getShortName();
  }

  /**
   * Sets $requestData by iterating over its raw source using keys
   * from getAllowedRequestDataKeys method and setting any missingvalues
   * to null. While doing so, sanitizes every data key's value by calling
   * the sanitizeField method on it.

   * @param array $requestData array of request data
   *
   * @return void
   */
  final public function __construct(array $requestData) {
    $this->response = new Response();
    foreach ($this->getAllowedRequestDataKeys() as $requestDataKey) {
      $requestDataValue = Get::in($requestData, $requestDataKey);
      $this->requestData[$requestDataKey] = $this->sanitizeField($requestDataKey, $requestDataValue);
    }
  }

  /**
   * Returns an array of strings representing data keys that this request
   * can use.
   *
   * @return array
   */
  abstract protected function getAllowedRequestDataKeys(): array;

  /**
   * Processes success action when all requirements (prerequisites,
   * request data validation) are met.
   *
   * @return void
   */
  abstract protected function processSuccessAction(): void;

  /**
   * Sanitizes given request data field
   *
   * @param string $fieldName
   * @param mixed $fieldValue
   *
   * @return mixed sanitized $fieldValue
   */
  abstract protected function sanitizeField(string $fieldName, $fieldValue);

  /**
   * Process the request by going through the following flow:
   * - validate prerequisites requirements
   * - validate request data
   * - if data is valid, process success action
   * - echo the JSON-encoded response
   *
   * @return void
   */
  final public function processRequest() {
    if ($this->validatePrerequisites()) {
      $this->validateRequestData();
    }

    if (!$this->response->hasErrors()) {
      $this->processSuccessAction();
    }

    echo $this->response->getJsonResponse();
    die();
  }

  /**
   * Validate request-data-agnostic requirements that must be met in order
   * for this request process to even start, eg. if user session is valid.
   *
   * @return boolean false if rest of the flow steps should be cancelled
   */
  protected function validatePrerequisites() {
    return true;
  }

  /**
   * Validates request data by calling validateField method
   * on every of its fields.
   *
   * @return void
   */
  final protected function validateRequestData() {
    foreach ($this->requestData as $fieldName => $fieldValue) {
      $this->validateField($fieldName, $fieldValue);
    }
  }

  /**
   * Validates given request data value.
   * Should add errors using $this->response->addFieldError or
   * $this->response->addFormError methods to prevent the process from
   * succeeding if any field values are invalid.
   *
   * @param string $fieldName data field name
   * @param mixed $fieldValue data field's value being validated
   *
   * @return void
   */
  protected function validateField(string $fieldName, $fieldValue): void {}

}
