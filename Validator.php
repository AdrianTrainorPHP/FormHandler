<?php
require_once 'CustomPDO.php';

/**
 * Class Validator
 */
class Validator
{
  /**
   * Iterates through the form inputs passing them to their respective validation methods for checking
   *
   * @param array $form
   * @param string $postKey
   * @return bool
   */
  public static function validate(&$form = array(), $postKey = '')
  {
    $allValid = true;
    if (!isset($form[$postKey]['validation'])) {

      $allValid = true;

    } else {

      $validations = explode(' ', $form[$postKey]['validation']);

      foreach ($validations as &$validation) {

        $validation = explode('=', $validation);

        if (isset($validation[1])) {
          $validation[1] = str_replace('"', '', $validation[1]);
        }

        $method = str_replace('-', '_', $validation[0]);

        switch ($validation[0]) {
          case 'data-unique':
          case 'data-email':
            $valid = self::$method($form[$postKey]);
            if (!$valid) {
              $allValid = false;
            }
            break;
          case 'data-len-gt':
          case 'data-len-lt':
          case 'data-allow':
          case 'data-inc':
            $valid = self::$method($validation[1], $form[$postKey]);
            if (!$valid) {
              $allValid = false;
            }
            break;
          case 'data-repeat':
            $valid = self::$method($postKey, $validation[1], $form);
            if (!$valid) {
              $allValid = false;
            }
            break;
        }

      }
    }
    return $allValid;
  }

  /**
   * Check the input value is unique i.e. has not already been stored in the database under a different row
   * Useful for email and username validation
   *
   * @param array $postedKeyArray
   * @return bool
   */
  public static function data_unique(&$postedKeyArray = array())
  {
    $valid = true;
    if (!isset($postedKeyArray['unique']) || false === strpos($postedKeyArray['unique'], '.')) {
      return false;
    }

    $pdo = new CustomPDO('sampleDBDSN', 'sampleDBUser', 'sampleDBPass');

    $query = 'SELECT * FROM `%s` WHERE `%s`="%s"';
    $tableAndColumnParameters = explode('.', $postedKeyArray['unique']);
    $query = sprintf($query, $tableAndColumnParameters[0], $tableAndColumnParameters[1], $postedKeyArray['value']);

    $result = $pdo->Query($query);
    if (count($result) > 0) {
      $postedKeyArray['showUniqueError'] = true;
      $valid = false;
    }
    return $valid;
  }

  /**
   * Check the passed input value's length is greater than the validation value
   *
   * @param string $validationValue
   * @param array $postedFormKey
   * @return bool
   */
  public static function data_len_gt($validationValue = '', &$postedFormKey = array())
  {
    if (is_numeric($validationValue)) {
      $validationValue = (int) $validationValue;
      if ($validationValue > 0) {
        if (strlen($postedFormKey['value']) <= $validationValue) {
          $postedFormKey['showError'] = true;
          return false;
        }
      }
    }
    return true;
  }

  /**
   * Check the passed input value's length is less than the validation value
   *
   * @param int $validationValue
   * @param array $postedFormKey
   * @return bool
   */
  public static function data_len_lt($validationValue = 0, &$postedFormKey = array())
  {
    if (is_numeric($validationValue)) {
      $validationValue = (int) $validationValue;
      if ($validationValue > 0) {
        if (strlen($postedFormKey['value']) >= $validationValue) {
          $postedFormKey['showError'] = true;
          return false;
        }
      }
    }
    return true;
  }

  /**
   * Check for incorrect character types in a string
   * n = numeric only - any other characters will fail the test
   * an = alpha numeric only - any special chars will fail the test
   * a = alpha only - any other characters will fail the test
   *
   * @param string $validationValue
   * @param array $postedFormKey
   * @return bool
   */
  public static function data_allow($validationValue = '', &$postedFormKey = array())
  {
    $valid = true;
    switch ($validationValue) {
      case 'n':
        if (!is_numeric($postedFormKey['value'])) {
          $postedFormKey['showError'] = true;
        }
        break;
      case 'an':
        if (!ctype_alnum(str_replace(' ', '', $postedFormKey['value']))) {
          $postedFormKey['showError'] = true;
        }
        break;
      case 'a':
        if (!ctype_alpha(str_replace(' ', '', $postedFormKey['value']))) {
          $postedFormKey['showError'] = true;
        }
        break;
      default:
        $valid = false;
    }
    /**
     * return false only if switch does not match
     * $postedFormKey[showError] determines the validity of the value passed
     */
    return $valid;
  }

  /**
   * Check for the existence of types of characters in the specified input value.
   *
   * Character keys:
   * n|d = integer
   * a   = alpha
   * sp  = special character
   *
   * Specifying a minimum number of matches to the type can be done by specifying the minimum number of occurrences
   * followed by an "x" and the character type. i.e.
   * 2xd  = minimum 2 integers required
   * 3xsp = minimum 3 special characters required
   *
   * Multiple types can be specified by separating with an comma i.e.
   * 2xn,5xa  = minimum 2 integers, minimum 5 alpha chars - minimum string length of 7
   *
   * @param string $validationValue
   * @param array $postedFormKey
   * @return bool
   */
  public static function data_inc($validationValue = '', &$postedFormKey = array())
  {
    $valid = true;
    $validationValue = explode(',', $validationValue);

    foreach ($validationValue as $validation) {

      $validation = explode('x', $validation);

      if (is_numeric($validation[0]) && strlen($validation[1]) > 0) {

        $validation[0] = (int) $validation[0];

        if ($validation[0] > 0) {

          switch($validation[1]) {

            case 'n':
            case 'd':

              $countMatches = preg_match_all('/[0-9]/', $postedFormKey['value']);
              if ($countMatches < $validation[0]) {
                $postedFormKey['showError'] = true;
                $valid = false;
              }
              break;

            case 'a':

              $countMatches = preg_match_all('/[ a-zA-Z]/', $postedFormKey['value']);
              if ($countMatches < $validation[0]) {
                $postedFormKey['showError'] = true;
                $valid = false;
              }
              break;

            case 'sp':

              $countMatches = preg_match_all('/[^ a-zA-Z0-9]/', $postedFormKey['value']);
              if ($countMatches < $validation[0]) {
                $postedFormKey['showError'] = true;
                $valid = false;
              }
              break;

            default:

              $valid = false;

          }
        }
      }
    }
    /**
     * return false if switch does not match or if any failed match counts
     * $postedFormKey[showError] determines the validity of the value passed
     */
    return $valid;
  }

  /**
   * return true if valid email is passed
   *
   * @param array $postedFormKey
   * @return bool
   */
  public static function data_email(&$postedFormKey = array())
  {
    if (!filter_var($postedFormKey['value'], FILTER_VALIDATE_EMAIL)) {
      $postedFormKey['showError'] = true;
      return false;
    }
    return true;
  }

  /**
   * check that the input values of the target and the repeat fields are the same.
   *
   * @param string $checkKey
   * @param string $againstKey
   * @param array $form
   * @return bool
   */
  public static function data_repeat($checkKey = '', $againstKey = '', &$form = array())
  {
    if (isset($form[$checkKey]) && isset($form[$againstKey])) {
      if ($form[$checkKey]['value'] !== $form[$againstKey]['value']) {
        $form[$checkKey]['showError'] = true;
        return false;
      }
    }
    return true;
  }
}