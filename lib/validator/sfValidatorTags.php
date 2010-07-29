<?php

/*
 * This file is part of the sfPropelTaggableWidgetPlugin package.
 * (c) 2009 Matteo Giachino <matteog@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfValidatorTags is the class that validate the tag widget and attaches tags to taggable object
 *
 * @package    sfPropelTaggableWidgetPlugin
 * @author     Matteo Giachino <matteog@gmail.com>
 */

class sfValidatorTags extends sfValidatorBase
{
  protected function configure($options = array(), $messages = array())
  {
    // the taggable propel object
    $this->addRequiredOption('taggable_object');

    $this->addOption('required', false);

    parent::configure($options, $messages);
  }

  /**
   * validates tags and attaches tags to taggable object
   */
  protected function doClean($tags)
  {
    if(!is_array($tags) && !is_null($tags))
    {
      //can only handle null or array
      throw new sfValidatorError($this, 'invalid', array('value' => $tags));
    }

    $this -> getOption('taggable_object') -> setTags($tags); //will be saved when object gets saved
    return $tags;
  }

  /**
   * override parent to trigger on empty value (remove tags)
   *
   * @return bool
   */
  public function isEmpty($tags)
  {
    return false;
  }
}