<?php

/*==========================================================================*\
#
#  Copyright © 2010-2017 Dipl.-Ing. (BA) Steffen Liersch
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

require_once dirname(__FILE__).'/index.php';

final class SLRelatedPostsWidget extends WP_Widget
{
  // Widget construction
  public function __construct()
  {
    $options=array(
      'classname'=>'widget_liersch_related_posts',
      'description'=>'A list of related posts.');

    parent::__construct('liersch_related_posts', 'Liersch Related Posts', $options);
  }

  // Widget rendering
  function widget($args, $instance)
  {
    $limit=isset($instance['limit']) ? $instance['limit'] : 0;
    $items=SLRelatedPosts::getRelatedPosts($limit);
    if(strlen($items)<=0)
      return;

    echo "\n\n<!-- SLRelatedPostsWidget by Steffen Liersch -->\n";
    echo $args['before_widget'];
    echo '<div>';
    
    if(isset($instance['title']))
    {
      $title=apply_filters('widget_title', $instance['title']);
      if(strlen($title)>0)
         echo $args['before_title'].$title.$args['after_title'];
    }

    echo "<ul>\n".$items."</ul>\n";

    echo '</div>';
    echo $args['after_widget'];
    echo "\n<!-- SLRelatedPostsWidget by Steffen Liersch -->\n\n";
  }

  // Widget settings update
  function update($new_instance, $old_instance)
  {
    $instance=$old_instance;
    $instance['title']=trim(strip_tags(stripslashes($new_instance['title'])));
    $instance['limit']=trim(strip_tags(stripslashes($new_instance['limit'])));
    return $instance;
  }

  // Widget setup form
  function form($instance)
  {
    // Set default values
    $instance=wp_parse_args((array)$instance, array('title'=>'Related Posts', 'limit'=>'5'));

    // Render controls
    $option='title';
    $value=htmlspecialchars($instance[$option]);
    $id=$this->get_field_id($option);
    echo '<p><label for="'.$id.'">Title: ';
    echo '<br/><input id="'.$id.'" name="'.$this->get_field_name($option);
    echo '" type="text" size="20" value="'.$value.'" /></label></p>';

    $option='limit';
    $value=htmlspecialchars($instance[$option]);
    $id=$this->get_field_id($option);
    echo '<p><label for="'.$id.'">Limit: ';
    echo '<br/><input id="'.$id.'" name="'.$this->get_field_name($option);
    echo '" type="number" min="3" max="50" size="3" value="'.$value.'" /></label></p>';

    // Render remainder    
    require dirname(__FILE__).'/support.php';
  }
}

if(function_exists('add_action'))
  add_action('widgets_init', function() { register_widget('SLRelatedPostsWidget'); } );
