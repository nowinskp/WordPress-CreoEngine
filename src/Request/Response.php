<?php

namespace Wpce\Request;

/**
 * A request response class to standardize response and error handling.
 */
class Response {

  /**
   * Default HTTP error response code when form contains any errors
   * and the response code has not been explicitly set.
   *
   * Can be overwritten by setting the constant:
   * WPCE_FORM_DEFAULT_ERROR_RESPONSE_CODE
   */
  const DEFAULT_ERROR_RESPONSE_CODE = 400;

  /**
   * Default form error added when form contain errors in its fields.
   *
   * Can be overwritten by setting the constant:
   * WPCE_FORM_ERROR_WHEN_THERE_ARE_ERRORS_IN_FIELDS
   */
  const DEFAULT_FORM_ERROR_WHEN_THERE_ARE_ERRORS_IN_FIELDS = 'Please check your form fields for errors.';

  protected $fieldErrors = [];
  protected $formErrors = [];
  protected $responseCode = 200;
  protected $responseData = [];
  protected $successMessage = 'Request was made successfully.';

  /**
   * Parses response data and generates response
   *
   * @return array response
   */
  public function getResponse() {
    if ($this->hasErrors()) {
      if ($this->hasFieldErrors()) {
        $this->addFormError(
          defined('WPCE_FORM_ERROR_WHEN_THERE_ARE_ERRORS_IN_FIELDS') ? constant('WPCE_FORM_ERROR_WHEN_THERE_ARE_ERRORS_IN_FIELDS') : self::DEFAULT_FORM_ERROR_WHEN_THERE_ARE_ERRORS_IN_FIELDS
        );
      }

      /**
       * If no error response code is set but response has errors
       * use a default one, ie. 400.
       */
      if ($this->responseCode < 400) {
        http_response_code(
          defined('WPCE_FORM_DEFAULT_ERROR_RESPONSE_CODE') ? constant('WPCE_FORM_DEFAULT_ERROR_RESPONSE_CODE') : self::DEFAULT_ERROR_RESPONSE_CODE
        );
      }

      return [
        'fieldErrors' => $this->fieldErrors,
        'formErrors' => $this->formErrors,
        'isSuccess' => false,
      ];
    }

    return [
      'data' => $this->responseData,
      'isSuccess' => true,
      'successMessage' => $this->successMessage,
    ];
  }

  /**
   * Returns JSON of generated response
   *
   * @return string JSON-encoded response
   */
  public function getJsonResponse() {
    return json_encode($this->getResponse());
  }

  /**
   * Sets data to be returned with the response on success
   *
   * @param mixed $responseData
   * @return void
   */
  public function setResponseData($responseData) {
    $this->responseData = $responseData;
  }

  /**
   * Adds field-related error
   *
   * @param string $fieldName name of the form field
   * @param string $errorMessage error message
   * @return void
   */
  public function addFieldError(string $fieldName, string $errorMessage) {
    $this->fieldErrors[$fieldName][] = $errorMessage;
  }


  /**
   * Adds form-related error
   *
   * @param string $errorMessage error message
   * @return void
   */
  public function addFormError(string $errorMessage) {
    $this->formErrors[] = $errorMessage;
  }


  /**
   * Checks if response has any field errors set.
   *
   * @return boolean
   */
  public function hasFieldErrors() {
    return count($this->fieldErrors) > 0;
  }

  /**
   * Checks if response has any errors set.
   *
   * @return boolean
   */
  public function hasErrors() {
    return count($this->formErrors) + count($this->fieldErrors) > 0;
  }

  /**
   * Sets HTTP response code
   *
   * @param int $responseCode HTTP response code
   *
   * @return void
   */
  public function setResponseCode(int $responseCode) {
    $this->responseCode = $responseCode;
  }

  /**
   * Sets success message added to successful response
   *
   * @param string $message success message to be set
   *
   * @return void
   */
  public function setSuccessMessage(string $message) {
    $this->successMessage = $message;
  }

}
