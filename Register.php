<?php
require_once 'Form.php';

/**
 * Class Register
 */
class Register extends Form
{
  public function __construct()
  {
    /**
     * assign form elements, validations, error messages and associated HTML elements
     */
    $this->form = array(
      'username' => array(
        'elementType'       => 'input',
        'type'              => 'text',
        'name'              => 'username',
        'value'             => '',
        'placeholder'       => 'JoeBloggs',
        'label'             => 'Username',
        'validation'        => 'data-unique=username data-len-gt=5 data-len-lt=60 data-allow=an',
        'unique'            => '_access.username',
        'showError'         => false,
        'errorMsg'          => 'Your username must be at least 5 characters and contain only alpha numeric characters',
        'showUniqueError'   => false,
        'uniquerErrorMsg'   => 'This username has already been taken',
        'tooltip'           => 'Enter an alpha numeric username greater than 5 characters in length'
      ),
      'email' => array(
        'elementType'       => 'input',
        'type'              => 'email',
        'name'              => 'email',
        'value'             => '',
        'placeholder'       => 'joe@bloggs.com',
        'label'             => 'Email',
        'validation'        => 'data-unique=email data-len-gt=6 data-len-lt=256 data-email',
        'unique'            => '_access.email',
        'showError'         => false,
        'errorMsg'          => 'Please enter a valid email address',
        'showUniqueError'   => false,
        'uniqueErrorMsg'    => 'Your email address has already been registered',
        'tooltip'           => 'Please enter a valid email address'
      ),
      'repeat_email' => array(
        'elementType'       => 'input',
        'type'              => 'email',
        'name'              => 'repeat_email',
        'value'             => '',
        'placeholder'       => 'joe@bloggs.com',
        'label'             => 'Repeat email',
        'validation'        => 'data-repeat=email',
        'showError'         => false,
        'errorMsg'          => 'Your email addresses do not match.',
        'tooltip'           => 'Repeat your valid email address'
      ),
      'password' => array(
        'elementType'       => 'input',
        'type'              => 'password',
        'name'              => 'password',
        'value'             => '',
        'placeholder'       => 'Jimmy23Eats',
        'label'             => 'Password',
        'validation'        => 'data-len-gt=5 data-len-lt=61 data-inc=1xn',
        'showError'         => false,
        'errorMsg'          => 'Enter a valid password between 6 and 60 characters long. It must include at least 1 number',
        'tooltip'           => 'Your password should be between 6 and 60 characters long and include at least 1 number'
      ),
      'repeat_password' => array(
        'elementType'       => 'input',
        'type'              => 'password',
        'name'              => 'repeat_password',
        'value'             => '',
        'placeholder'       => 'Jimmy23Eats',
        'label'             => 'Repeat password',
        'validation'        => 'data-repeat=password',
        'showError'         => false,
        'errorMsg'          => 'The repeat password does not match the password entered above',
        'tooltip'           => 'Repeat your valid password'
      ),
      'submit' => array(
        'elementType'       => 'submit',
        'text'              => 'Join',
        'validation'        => true
      )
    );
  }

}