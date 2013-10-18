<?php 
if (!defined('DIR_APPLICATION')) exit('No direct script access allowed');

class ShortcodesTheme extends Controller {
   /* Quick Docs:
    *  - Function name would be the shortcode tag.
    *  - Same function name with available shortcode will overwrite the previous shortcode.
    */

   /**
    * Get theme info
    *
    * [themeinfo /]
    */
   function themeinfo() {
      $data = 'This store use <b>' . $this->config->get('config_template') . '</b> theme.';
      
      return $data;
   }
}