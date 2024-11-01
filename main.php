<?php
/*
Plugin Name: Liersch Related Posts
Plugin URI: http://www.steffen-liersch.de/wordpress/
Description: This plug-in provides a widget and an optional content filter to list related posts.
Version: 1.2
Author: Steffen Liersch
Author URI: http://www.steffen-liersch.de/
*/

require_once dirname(__FILE__).'/index.php';

if(is_admin())
{
  require_once dirname(__FILE__).'/class-settings.php';
  SLRelatedPostsSettings::initialize(__FILE__);
}
else require_once dirname(__FILE__).'/class-main.php';

require_once dirname(__FILE__).'/class-widget.php';