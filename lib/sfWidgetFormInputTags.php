<?php

/*
 * This file is part of the sfPropelTaggableWidgetPlugin package.
 * (c) 2009 Matteo Giachino <matteog@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfWidgetFormInputTags is the class that define the tag widget.
 *
 * @package    sfPropelTaggableWidgetPlugin
 * @author     Matteo Giachino <matteog@gmail.com>
 */

class sfWidgetFormInputTags extends sfWidgetForm
{
  public function configure($options = array(), $attributes = array())
  {
    // the taggable propel object
    $this->addRequiredOption('taggable_object');

    // enable the autocomplete feature
    $this->addOption('enable_autocomplete', true);

    // common actions labels
    $this->addOption('check_all_label', 'Check all');
    $this->addOption('uncheck_all_label', 'Uncheck all');
    $this->addOption('invert_selection_label', 'Invert selection');
  }

  public function getStylesheets()
  {
    return array(
      // Layout of the widget
      '/sfPropelTaggableWidgetPlugin/css/sfWidgetFormInputTags' => 'screen',
      // Layout of autocomplete jQuery plugin
      '/sfPropelTaggableWidgetPlugin/css/jquery.autocomplete.css' => 'screen'
    );
  }

  public function getJavascripts() {
    return $this->getOption('enable_autocomplete') ? array('/sfPropelTaggableWidgetPlugin/js/jquery.autocomplete.pack.js') : array();
  }

  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    // retrieve the current tags for this taggable Propel object
    $prev_tags = $this->getOption('taggable_object')->getTags();

    // $html holds the html output of the widget
    $html = '<div>';
    $html .= '<ul class="previous-tags">';
    foreach ($prev_tags as $tag) {
      $html .= '<li>'.$this->renderContentTag('input', '<em class="mantain">'.$tag.'</em>', array('type' => 'checkbox', 'name' => $name.'[]', 'checked' => 'checked', 'value' => $tag)).'</li>';
    }
    $html .= '</ul>';
    $html .= '</div>';
    $html .= $this->renderContentTag('input', '', array('id' => 'input_add_tags', 'name' => 'add_tags'));
    $html .= $this->renderContentTag('input', '', array('type' => 'button', 'id' => 'btn-add-tag', 'value' => 'Add tag'));
    $html .= '<div class="tag_common_actions">';
    $html .= $this->renderContentTag('a', $this->getOption('check_all_label'), array('href' => 'javascript:onclick:checkAll()', 'class' => 'tag-action check'));
    $html .= $this->renderContentTag('a', $this->getOption('uncheck_all_label'), array('href' => 'javascript:onclick:uncheckAll()', 'class' => 'tag-action uncheck'));
    $html .= $this->renderContentTag('a', $this->getOption('invert_selection_label'), array('href' => 'javascript:onclick:invertSelection()', 'class' => 'tag-action invert'));
    $html .= '</div>';

    // Generate the autocomplete code
    $autocomplete_js = "var data = \"".implode('|', TagPeer::getAll())."\".split('|');
            $('input#input_add_tags').autocomplete(data, { onItemSelect:selectItem });";

    // Generate the js code
    $js_code = "
        <script type=\"text/javascript\">
          var tagExists = ".(empty($prev_tags) ? "false" : "true").";
          var isShowing = tagExists;
          $(document).ready(function() {
            ";
          // if 'enable_autocomplete' is true (default) inserts the autocomplete code
          $js_code .= ($this->getOption('enable_autocomplete')) ? $autocomplete_js : '';
          $js_code .= "
            if (!tagExists) {
              $('ul.previous-tags').hide();
              $('div.tag_common_actions').hide();
            }

            $('ul.previous-tags li input').each( function() {
              $(this).siblings('em').removeClass('mantain');
            });
            $('ul.previous-tags li input:checked').each( function() {
              $(this).siblings('em').addClass('mantain');
            });
            addCallbacks();

            $('input#input_add_tags').keydown(function(key) {
              if (key.keyCode == 13) {
                addTag();
                return false;
              }
            });
            $('input#btn-add-tag').click( function() {
              addTag();
              return false;
            });
          });

          function selectItem(li) {
            alert(li);
          }

          function addTag() {
            tagName = $('input#input_add_tags').val();
            if (checkTagName(tagName)) {
              if (!isShowing) {
                isShowing = true;
                $('ul.previous-tags').fadeIn('slow');
                $('div.tag_common_actions').fadeIn('slow');
              }
              $('ul.previous-tags').html($('ul.previous-tags').html() + '<li><input type=\"checkbox\" checked=\"checked\" name=\"".$name."[]\" size=\"20\" value=\"' + tagName + '\" /><em class=\"mantain\">' + tagName + '</em></li>');
              $('input#input_add_tags').val('');
              addCallbacks();
            }
          }

          function checkTagName(tag) {
            valid = true;
            $('ul.previous-tags li input').each( function() {
              if ($(this).val() === tag) {
                valid = false;
                alert('The Object is already tagged with ' + tag);
              }
            });
            if (tag == '') {
              valid = false;
              alert('Please insert a tag!');
            }
            return valid;
          }
          
          function addCallbacks() {
            normalizeStatus();
            $('ul.previous-tags li').each( function() {
              $(this).children('input').change(function() {
                if ($(this).siblings('em').hasClass('mantain')) {
                  $(this).siblings('em').removeClass('mantain');
                } else {
                  $(this).siblings('em').addClass('mantain');
                }
              });
            });
          }

          function normalizeStatus() {
            $('ul.previous-tags li').each( function() {
              if ($(this).children('em').hasClass('mantain')) {
                $(this).children('input').attr('checked', 'checked');
              } else {
                $(this).children('input').attr('checked', '');
              }
            });
          }

          function checkAll() {
            $('ul.previous-tags li input').attr('checked','checked');
            $('ul.previous-tags li em').addClass('mantain');
          }
          function uncheckAll() {
            $('ul.previous-tags li input').attr('checked','');
            $('ul.previous-tags li em').removeClass('mantain');
          }
          function invertSelection() {
            $('ul.previous-tags li').each( function() {
              if ($(this).children('input').attr('checked')) {
                $(this).children('input').attr('checked','');
                $(this).children('em').removeClass('mantain');
              } else {
                $(this).children('input').attr('checked','checked');
                $(this).children('em').addClass('mantain');
              }
            });
          }
        </script>
    ";

    return $html.$js_code;
  }

}