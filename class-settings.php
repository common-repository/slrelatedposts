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

final class SLRelatedPostsSettings
{
  /*private*/ const OPTION_GROUP='liersch_related_posts';
  /*private*/ const OPTION_NAME='liersch_related_posts';
  /*private*/ const SLUG_NAME='liersch_related_posts';

  public static function initialize($file)
  {
    add_action('admin_init', 'SLRelatedPostsSettings::handleAdminInit');
    add_action('admin_menu', 'SLRelatedPostsSettings::handleAdminMenu', 1000+ord('R')*10);
    add_action('admin_enqueue_scripts', 'SLRelatedPostsSettings::handleAdminEnqueueScripts');
    add_filter('plugin_action_links_'.plugin_basename($file), 'SLRelatedPostsSettings::renderPlugInActionLink'); // Must be called from the primary file due to using plugin_basename!
  }

  public static function handleAdminInit()
  {
    register_setting(self::OPTION_GROUP, self::OPTION_NAME);
  }

  public static function handleAdminMenu()
  {
    add_options_page(
      /* page_title */ 'Liersch Related Posts - Settings',
      /* menu_title */ 'Related Posts',
      /* capability */ 'administrator',
      /* menu_slug  */ self::SLUG_NAME,
      /* function   */ 'SLRelatedPostsSettings::showSettings');
  }

  public static function handleAdminEnqueueScripts($hook)
  {
    //wp_enqueue_style(self::SLUG_NAME, plugins_url('class-settings.css', __FILE__));
  }
  
  public static function renderPlugInActionLink($links)
  {
    array_push($links, '<a href="options-general.php?page='.self::SLUG_NAME.'">'.__('Settings').'</a>');
    return $links;
  }

  public static function showSettings()
  {
    require_once dirname(__FILE__).'/class-option-renderer.php';
?>
<div class="wrap liersch-related-posts-settings">
<h2>Liersch Related Posts</h2>        
<form method="post" action="options.php">
<div class="sl-group">
<?php
settings_fields(self::OPTION_GROUP);
do_settings_sections(self::OPTION_GROUP);
$renderer=new SLOptionRenderer(self::OPTION_NAME);

$renderer->desc('This plug-in provides a widget to list related posts if applicable. The widget works independent from the activation here.');
$renderer->begin('Widget');
$renderer->end();

$renderer->begin('Content');
$renderer->desc('If this option is enabled, a content filter adds a list of related posts at the end of each post.');
$renderer->checkbox('Activation', 'Enable Related Posts', 'is_enabled', false);
$renderer->desc('This text is used as headline for the list of related posts.');
$renderer->textbox('Headline', 'headline', 'Related Posts');
$renderer->desc('This value specifies the maximum number of related posts.');
$renderer->number('Limit', 'limit', 5, array('min'=>3, 'max'=>50, 'hint'=>null));
$renderer->end();

$renderer->begin();
echo '<tr><th></th><td><div style="max-width: 300px;">';
require dirname(__FILE__).'/support.php';
echo '</div></td></tr>';
$renderer->end();

submit_button();
?>
</form>
</div>
<?php
  }
}