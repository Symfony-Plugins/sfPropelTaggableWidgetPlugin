<?php

/*
 * This file is part of the sfPropelTaggableWidgetPlugin package.
 * (c) 2009 Matteo Giachino <matteog@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfValidatorTags is the class that validate the tag widget.
 *
 * @package    sfPropelTaggableWidgetPlugin
 * @author     Matteo Giachino <matteog@gmail.com>
 */

class sfValidatorTags extends sfValidatorBase {
    //put your code here
  protected function configure($options = array(), $messages = array())
  {
    $this->addOption('required', false);
  }

  protected function doClean($value)
  {
    if (is_array($value)) {
      return true;
    }

    throw new sfValidatorError($this, 'invalid', array('value' => $value));
  }

  public function isEmpty($value)
  {
    return true;
  }

}
?>
