<?php

/**
 * Interface FormInterface
 *
 * Interface to ensure that all new forms developed have all the necessary methods.
 */
interface FormInterface
{
  public function form();
  public function validate(array $post);
}