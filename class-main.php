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

final class SLRelatedPosts
{
  public static function initialize()
  {
    self::$m_Options=get_option('liersch_related_posts');
    if(isset(self::$m_Options['is_enabled']) && self::$m_Options['is_enabled']==='1')
      add_filter('the_content' , 'SLRelatedPosts::the_content' );
  }

  public static function the_content($content)
  {
    if(is_single())
    {
      $limit=isset(self::$m_Options['limit']) ? self::$m_Options['limit'] : 5;
      $items=self::getRelatedPosts($limit);
      if(strlen($items)>0)
      {
        $s="\n\n<!-- SLRelatedPosts by Steffen Liersch -->\n";
        $headline=isset(self::$m_Options['headline']) ? self::$m_Options['headline'] : 'Related Posts';
        if(strlen($headline)>0)
          $s.="<h2>".$headline."</h2>\n";
        $s.="<ul>\n";
        $s.=$items;
        $s.="</ul>\n";
        $s.="<!-- SLRelatedPosts by Steffen Liersch -->\n\n";
        $content.=$s;
      }
    }
    return $content;
  }

  public static function getRelatedPosts($limit)
  {
    if($limit<=0 || !is_single())
      return '';

    $params='category=';
    foreach(get_the_category() as $c)
      $params.=','.$c->cat_ID;

    global $post;
    $params.='&exclude='.$post->ID;
    $params.='&numberposts='.$limit;
    $params.='&orderby=date';

    $items='';
    foreach(get_posts($params) as $p)
    {
      $href=get_permalink($p->ID);
      $title=get_the_title($p->ID);
      $items.='<li><a href="'.$href.'" title="'.$title.'">'.$title."</a></li>\n";
    }
    return $items;
  }

  private static $m_Options;
}

SLRelatedPosts::initialize();