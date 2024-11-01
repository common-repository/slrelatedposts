<?php

/*==========================================================================*\
#
#  Copyright © 2016-2017 Dipl.-Ing. (BA) Steffen Liersch
#  All rights reserved.
#
#  Steffen Liersch
#  Robert-Schumann-Straße 1
#  08289 Schneeberg
#  Germany
#
#  Phone: +49-3772-38 28 08
#  E-Mail: S.Liersch@gmx.de
#
\*===========================================================================*/

if(!class_exists('SLOptionRenderer')) :

final class SLOptionRenderer
{
  public static function addActionsForColorAndUpload()
  {
    add_action('admin_enqueue_scripts', 'SLOptionRenderer::handleAdminEnqueueScripts');
  }

  public static function handleAdminEnqueueScripts($hook)
  {
    // jQuery is required for both, media upload and color picker.
    wp_enqueue_script('jquery');

    // Enable media upload
    wp_enqueue_media();

    // Enable color picker
    wp_enqueue_style('wp-color-picker');

    // Add custom script
    wp_enqueue_script('SLOptionRenderer', plugins_url('class-option-renderer.js', __FILE__), array('wp-color-picker'), false, true);
  }

  public function __construct($optionName)
  {
    $this->m_OptionName=$optionName;
    $this->m_OptionValues=get_option($optionName);
  }

  public function begin($title=null, $class=null)
  {
    echo "\n\n<!-- SLOptionRenderer::begin -->";
    echo "\n".'<div class="liersch-option-renderer sl-group '.$class.'">';
    if(strlen($title)>0)
      echo "\n".'<h2>'.$title.'</h2>';
    echo $this->retrieveDescription();
    echo "\n".'<table class="form-table">';
  }

  public function end()
  {
    echo "\n".'</table></div>';
    echo "\n<!-- SLOptionRenderer::end -->\n\n";
  }

  public function desc($description)
  {
    $this->m_Description=$description;
  }

  public function checkbox($title1, $title2, $name, $default)
  {
    echo
      self::rowBegin('sl-checkbox').$title1.'</th><td><label><input type="checkbox" '.$this->name($name);

    if($this->m_OptionValues!==false ? isset($this->m_OptionValues[$name]) && $this->m_OptionValues[$name] : $default)
      echo ' checked="checked"';

    echo ' value="1" />'.$title2.$this->retrieveDescription().'</label>';
    echo self::rowEnd();
  }

  public function textbox($title, $name, $default)
  {
    $val=isset($this->m_OptionValues[$name]) ? $this->m_OptionValues[$name] : $default;
    echo
      self::rowBegin('sl-textbox sl-'.$name).
      $this->label($title, $name).'</th>'."\n  ".'<td><input type="text" size="50" '.
      $this->name($name).' value="'.$val.'" />'.
      $this->retrieveDescription().
      self::rowEnd();
  }

  public function textarea($title, $name, $default)
  {
    $val=isset($this->m_OptionValues[$name]) ? $this->m_OptionValues[$name] : $default;
    echo
      self::rowBegin('sl-textarea sl-'.$name).
      $this->label($title, $name).'</th>'."\n  ".'<td><textarea '.
      $this->name($name).' cols="50" rows="5">'.$val.'</textarea>'.
      $this->retrieveDescription().
      self::rowEnd();
  }

  public function number($title, $name, $default, $args=null)
  {
    $val=isset($this->m_OptionValues[$name]) ? $this->m_OptionValues[$name] : $default;
    $min=isset($args['min']) ? 'min="'.$args['min'].'"' : '';
    $max=isset($args['max']) ? 'max="'.$args['max'].'"' : '';
    $hint=isset($args['hint']) ? ' '.$args['hint'] : '';
    echo
      self::rowBegin('sl-number sl-'.$name).
      $this->label($title, $name).'</th>'."\n  ".'<td><input type="number" '.
      $this->name($name).' '.$min.' '.$max.' value="'.$val.'" />'.$hint.
      $this->retrieveDescription().
      self::rowEnd();
  }

  public function color($title, $name, $default)
  {
    $val=isset($this->m_OptionValues[$name]) ? $this->m_OptionValues[$name] : $default;
    echo
      self::rowBegin('sl-color sl-'.$name).
      $this->label($title, $name).'</th>'."\n  ".'<td><input type="text" value="'.$val.'" class="sl-color-field" '.
      $this->name($name).' data-default-color="'.$default.'" />'.
      $this->retrieveDescription().
      self::rowEnd();
  }

  public function upload($title, $name, $default, $args=null)
  {
    // Inspired by https://codex.wordpress.org/Javascript_Reference/wp.media

    $val=isset($this->m_OptionValues[$name]) ? $this->m_OptionValues[$name] : $default;

    $img_avail=strlen($val)>0;
    if($img_avail && $val!=-1)
    {
      $img_src=wp_get_attachment_image_src($val, 'full');
      $img_avail=is_array($img_src);
    }

    echo
      self::rowBegin('sl-upload sl-'.$name). // No label here!
      $title.'</th>'."\n  ".'<td><input type="hidden" size="50" value="'.$val.'" name="'.
      $this->m_OptionName.'['.$name.']." />'. // No ID for hidden field here!
      '<span class="sl-preview">';

    if($img_avail)
    {
      if($val!=-1)
        echo '<img src="'.$img_src[0].'" alt="" style="max-width: 100%;" />';
      else echo 'Demo';
    }

    echo
      '</span>'.
      '<a class="button sl-add'.($img_avail ? ' hidden' : '').'">Select</a>'.
      '<a class="button sl-del'.(!$img_avail ? ' hidden' : '').'">Clear</a>'.
      $this->retrieveDescription().
      self::rowEnd();
  }

  private function retrieveDescription()
  {
    $res=$this->m_Description;
    $this->m_Description=null;
    return strlen($res)>0 ? "\n  ".'<p class="description">'.$res.'</p>' : '';
  }

  private function label($title, $name)
  {
    return '<label for="'.$this->m_OptionName.'_'.$name.'">'.$title.'</label';
  }

  private function name($name)
  {
    return 'name="'.$this->m_OptionName.'['.$name.']" id="'.$this->m_OptionName.'_'.$name.'"';
  }

  private static function rowBegin($class)
  {
    return "\n".'<tr class="sl-editor '.$class.'">'."\n  <th>";
  }

  private static function rowEnd()
  {
    return "</td>\n</tr>";
  }

  private $m_OptionName;
  private $m_OptionValues;
  private $m_Description;
}

endif;