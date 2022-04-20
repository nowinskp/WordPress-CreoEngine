<?php

namespace Wpce\Request;

/**
 * A request response class to standardize response and error handling.
 */
class Response {

  const DEFAULT_ERROR_RESPONSE_CODE = 400;

  protected $fieldErrors = [];
  protected $formErrors = [];
  protected $responseCode = 200;
  protected $responseData = [];

  /**
   * Parses response data and generates response
   *
   * @return array response
   */
  public function getResponse() {
    if ($this->hasErrors()) {
      /**
       * If no error response code is set but response has errors
       * use a default one, ie. 400.
       */
      if ($this->responseCode < 400) {
        http_response_code(self::DEFAULT_ERROR_RESPONSE_CODE);
      }

      return [
        'isSuccess' => false,
        'fieldErrors' => $this->fieldErrors,
        'formErrors' => $this->formErrors,
      ];
    }

    return [
      'isSuccess' => true,
      'data' => $this->responseData,
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
  public function setFormError(string $errorMessage) {
    $this->formErrors[] = $errorMessage;
  }


  /**
   * Checks if response has eny errors set.
   *
   * @return boolean
   */
  public function hasErrors() {
    return count($this->formErrors + $this->fieldErrors) > 0;
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

}
