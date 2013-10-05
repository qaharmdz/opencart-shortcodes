<?php
class ShortcodesThemes extends Controller {
   /* Quick Docs:
    *  - Function name would be the shortcode tag.
    *  - Use the same function name as in default shortcode to overwrite it.
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