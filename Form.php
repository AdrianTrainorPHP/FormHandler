<?php
require_once 'FormInterface.php';
require_once 'NoCSRF.php';
require_once 'Validator.php';

/**
 * Class Form
 */
class Form implements FormInterface
{
  /**
   * @var array $form
   */
  public $form;

  /**
   * @return array
   */
  public function form()
  {
    return $this->form;
  }

  /**
   * Check for valid post and pass each input to the Validator class
   *
   * @param array $post
   * @return bool
   */
  public function validate(array $post)
  {
    $valid = array();
    if (!empty($post)) {
      foreach ($post as $postKey => $postValue) {
        if (!isset($this->form[$postKey])) {
          // invalid post key - stop executing immediately
          die();
        }
        $valid[$postKey]                = false;
        $this->form[$postKey]['value']  = $postValue;
        $valid[$postKey]                = Validator::validate($this->form, $postKey);
      }
    }
    $allValid = true;
    foreach ($valid as $validKey => $isValid) {
      if (!$isValid) {
        $allValid = false;
      }
    }
    return $allValid;
  }

  /**
   * assign a CSRF token to be added to the form.
   * Will be checked by the corresponding checkCSRF method on form submission
   */
  public function setCSRF()
  {
    $this->form['csrf_token'] = array(
      'elementType' => 'input',
      'type'        => 'hidden',
      'name'        => 'csrf_token',
      'value'       => NoCSRF::generate('csrf_token')
    );
  }

  /**
   * Check the CSRF token on form submission
   * Assigned by setCSRF method.
   *
   * @return bool
   * @throws Exception
   */
  public function checkCSRF()
  {
    if ($_POST && isset($_POST['csrf_token'])) {

      $validCSRF = NoCSRF::check('csrf_token', $_POST);
      unset($_POST['csrf_token']);
      return $validCSRF;

    }
    return false;
  }
}