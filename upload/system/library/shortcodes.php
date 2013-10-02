<?php
class Shortcodes {
   protected $registry;	
   
   public function __construct($registry) {
		$this->registry = $registry;
	}
	
	public function __get($key) {
		return $this->registry->get($key);
	}
	
	public function __set($key, $value) {
		$this->registry->set($key, $value);
	}
   
   /**
    * Generate product link with it variant of category and manufacture link.
    *
    * [link_product id="x" path="x_x" brand="x" ssl="0" title="xyz" /]
    * [link_product id="x" path="x_x" brand="x" ssl="0" title="xyz"]custom text[/link_product]
    */
   function link_product($atts, $content = '') {
      extract(shortcode_atts(array(
         'id'     => 0,
         'path'   => 0,
         'brand'  => 0,
         'ssl'    => 0,
         'title'  => ''
      ), $atts));
      
      if ($id) {
         $ssl     = ($ssl) ? "'SSL'" : "";
         $title   = ($title) ? 'Title="' . $title . '"' : "";

         if (!$content) {
            $this->load->model('catalog/product');
            $product = $this->model_catalog_product->getProduct($id);
         
            if(!$path && !$brand) {
               return '<a href="' . $this->url->link('product/product', 'product_id=' . $id, $ssl) . '" ' . $title . '>' . $product['name'] . '</a>';
            } elseif ($path && (!$brand || $brand)) {
               return '<a href="' . $this->url->link('product/product', 'path=' . $path . '&product_id=' . $id, $ssl) . '" ' . $title . '>' . $product['name'] . '</a>';
            } elseif (!$path && $brand) {
               return '<a href="' . $this->url->link('product/product', 'manufacturer_id=' . $brand . '&product_id=' . $id, $ssl) . '" ' . $title . '>' . $product['name'] . '</a>';
            }
         } elseif ($content) {
            if(!$path && !$brand) {
               return '<a href="' . $this->url->link('product/product', 'product_id=' . $id, $ssl) . '" ' . $title . '>' . $content . '</a>';
            } elseif ($path && (!$brand || $brand)) {
               return '<a href="' . $this->url->link('product/product', 'path=' . $path . '&product_id=' . $id, $ssl) . '" ' . $title . '>' . $content . '</a>';
            } elseif (!$path && $brand) {
               return '<a href="' . $this->url->link('product/product', 'manufacturer_id=' . $brand . '&product_id=' . $id, $ssl) . '" ' . $title . '>' . $content . '</a>';
            }
         }
      }
   }
   
   /**
    * Generate category link.
    *
    * [link_category path="x_y" ssl="0" title="xyz" /]
    * [link_category path="x_y" ssl="1" title="xyz"]custom text[/link_category]
    */
   function link_category($atts, $content = '') {
      extract(shortcode_atts(array(
         'path'   => 0,
         'ssl'    => 0,
         'title'  => ''
      ), $atts));
      
      if ($path) {
         $ssl     = ($ssl) ? "'SSL'" : "";
         $title   = ($title) ? 'Title="' . $title . '"' : "";
         
         if (!$content) {
            $this->load->model('catalog/category');
            $category = $this->model_catalog_category->getCategory($path);
            
            return '<a href="' . $this->url->link('product/category', 'path=' . $path, $ssl) . '" ' . $title . '>' . $category['name'] . '</a>';
         } elseif ($content) {
            return '<a href="' . $this->url->link('product/category', 'path=' . $path, $ssl) . '" ' . $title . '>' . $content . '</a>';
         }
      }
   }
   
   /**
    * Generate brand/ manufacturer link.
    *
    * [link_brand ssl="0" title="xyz" /]
    * [link_brand ssl="1" title="xyz"]custom text[/link_brand]
    *
    * [link_brand brand="x" ssl="0" title="xyz" /]
    * [link_brand brand="x" ssl="1" title="xyz"]custom text[/link_brand]
    */
   function link_brand($atts, $content = '') {
      extract(shortcode_atts(array(
         'brand'  => 0,
         'ssl'    => 0,
         'title'  => ''
      ), $atts));
      
      $ssl     = ($ssl) ? "'SSL'" : "";
      $title   = ($title) ? 'Title="' . $title . '"' : "";
      
      if (!$content) {
         $this->load->language('product/manufacturer');
         $this->load->model('catalog/manufacturer');
         $manufacturer = $this->model_catalog_manufacturer->getManufacturer($brand);
         
         if (!$brand) {
            return '<a href="' . $this->url->link('product/manufacturer', '', $ssl) . '" ' . $title . '>' . $this->language->get('text_brand') . '</a>';
         } else {
            return '<a href="' . $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $brand, $ssl) . '" ' . $title . '>' . $manufacturer['name'] . '</a>';
         }
      } elseif ($content) {
         if (!$brand) {
            return '<a href="' . $this->url->link('product/manufacturer', 'manufacturer_id=' . $brand, $ssl) . '" ' . $title . '>' . $content . '</a>';
         } else {
            return '<a href="' . $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $brand, $ssl) . '" ' . $title . '>' . $content . '</a>';
         }
      }
   }
   
   /**
    * Generate information link.
    *
    * [link_info id="x" ssl="0" title="xyz" /]
    * [link_info id="x" ssl="0" title="xyz"]custom text[/link_info]
    */
   function link_info($atts, $content = '') {
      extract(shortcode_atts(array(
         'id'     => 0,
         'ssl'    => 0,
         'title'  => ''
      ), $atts));
      
      if ($id) {
         $ssl     = ($ssl) ? "'SSL'" : "";
         $title   = ($title) ? 'Title="' . $title . '"' : "";
         
         if (!$content) {
            $this->load->model('catalog/category');
            $information = $this->model_catalog_information->getInformation($id);
            
            return '<a href="' . $this->url->link('information/information', 'information_id=' . $id, $ssl) . '" ' . $title . '>' . $information['title'] . '</a>';
         } elseif ($content) {
            return '<a href="' . $this->url->link('information/information', 'information_id=' . $id, $ssl) . '" ' . $title . '>' . $content . '</a>';
         }
      }
   }
   
   /**
    * Generate custom link based on OpenCart API Url format
    *
    * [link_custom route="foo" args="bar" ssl="0" title="xyz"]custom text[/link_custom]
    */
   function link_custom($atts, $content = '') {
      extract(shortcode_atts(array(
         'route'  => '',
         'args'   => '',
         'ssl'    => 0,
         'title'  => ''
      ), $atts));
      
      if($route && $content) {
         return '<a href="' . $this->url->link($route, $args, $ssl) . '" ' . $title . '>' . $content . '</a>';
      }
   }
   
   /**
    * Load module type product (featured, latest, bestseller, special) everywhere!
    *
    * [module_product type="featured" limit="5" img_w="100" img_h="100" /]
    */
   function module_product($atts) {
      extract(shortcode_atts(array(
         'type'   => '',
         'limit'  => 5,
         'img_w'  => 80,
         'img_h'  => 80
      ), $atts));

      if ($type) {
         $action = new sController($this->registry); 

         $module = $action->get_child('module/' . $type, array(
                     'limit' => $limit,
                     'image_width' => $img_w,
                     'image_height' => $img_h
                  ));
         
         return $module;
      }
   }
   
   /**
    * Show lite System Information 
    * (full: http://www.echothemes.com/extensions/system-information.html)
    *
    * [debug]
    */
   function debug() {
      $data    = '<h3>OpenCart Debug Info - ' . date('d M, Y') . '</h3>';
      $data   .= '<table class="sc-debug">';
      $data   .= '<tr><td>OpenCart</td><td>: v' . VERSION . '</td></tr>';
      if (isset(VQMod::$_vqversion)) {
         $data   .=  '<tr><td>vQmod</td><td>: v' . VQMod::$_vqversion . '</td></tr>';
      }
      $data   .= '<tr><td>Shortcodes</td><td>: v' . SHORTCODES_VERSION . '</td></tr>';
      $data   .= '</table>';
      $data   .= '<table class="sc-debug">';
      $data   .= '<tr><td>PHP version</td><td>: ' . phpversion() . '</td></tr>';
      $data   .= '<tr><td>Safe Mode</td><td>: ' . ((ini_get('safe_mode')) ? 'ON <span class="sc-alert">- Required to turn off!</span>' : 'OFF <span class="sc-good">- Good</span>') . '</td></tr>';
      $data   .= '<tr><td>Register Globals</td><td>: ' . ((ini_get('register_globals')) ? 'ON <span class="sc-alert">- Required to turn off!</span>' : 'OFF <span class="sc-good">- Good</span>') . '</td></tr>';
      $data   .= '<tr><td>Magic Quotes GPC</td><td>: ' . ((ini_get('magic_quotes_gpc') || get_magic_quotes_gpc()) ? 'ON <span class="sc-alert">- Required to turn off!</span>' : 'OFF <span class="sc-good">- Good</span>') . '</td></tr>';
      $data   .= '<tr><td>Session Auto Start</td><td>: ' . ((ini_get('session_auto_start')) ? 'ON <span class="sc-alert">- Required to turn off!</span>' : 'OFF <span class="sc-good">- Good</span>') . '</td></tr>';
      $data   .= '<tr><td>Allow Url Fopen</td><td>: ' . ((ini_get('allow_url_fopen')) ? 'ON <span class="sc-good">- Good</span>' : 'OFF <span class="sc-alert">- Required to turn ON!</span>') . '</td></tr>';
      if(VERSION >= '1.5.4') {
         $data   .= '<tr><td>mCrypt</td><td>: ' . ((extension_loaded('mcrypt')) ? 'ON <span class="sc-good">- Good</span>' : 'OFF <span class="sc-alert">- Required to turn ON!</span>') . '</td></tr>';
      }
      $data   .= '<tr><td>File Uploads</td><td>: ' . ((ini_get('file_uploads')) ? 'ON <span class="sc-good">- Good</span>' : 'OFF <span class="sc-alert">- Required to turn ON!</span>') . '</td></tr>';
      $data   .= '<tr><td>Cookies</td><td>: ' . ((ini_get('session.use_cookies')) ? 'ON <span class="sc-good">- Good</span>' : 'OFF <span class="sc-alert">- Required to turn ON!</span>') . '</td></tr>';
      $data   .= '<tr><td>GD</td><td>: ' . ((extension_loaded('gd')) ? 'ON <span class="sc-good">- Good</span>' : 'OFF <span class="sc-alert">- Required to turn ON!</span>') . '</td></tr>';
      $data   .= '<tr><td>Curl</td><td>: ' . ((extension_loaded('curl')) ? 'ON <span class="sc-good">- Good</span>' : 'OFF <span class="sc-alert">- Required to turn ON!</span>') . '</td></tr>';
      $data   .= '<tr><td>Fsock</td><td>: ' . ((extension_loaded('sockets')) ? 'ON <span class="sc-good">- Good</span>' : 'OFF <span class="sc-alert">- Required to turn ON!</span>') . '</td></tr>';
      $data   .= '<tr><td>Zip</td><td>: ' . ((extension_loaded('zlib')) ? 'ON <span class="sc-good">- Good</span>' : 'OFF <span class="sc-alert">- Required to turn ON!</span>') . '</td></tr>';
      $data   .= '<tr><td>Xml</td><td>: ' . ((extension_loaded('xml')) ? 'ON <span class="sc-good">- Good</span>' : 'OFF <span class="sc-alert">- Required to turn ON!</span>') . '</td></tr>';
      $data   .= '</table>';

      $style   = '<style>';
      $style  .= '.sc-debug { width:400px; border-collapse:separate; border-spacing:0; margin-bottom:20px; line-height:16px; }';
      $style  .= '.sc-debug > tbody > tr:nth-child(odd) > td { background-color:#f2f2f2; }';
      $style  .= '.sc-debug td { padding:6px 10px; vertical-align:top; }';
      $style  .= '.sc-debug td:first-child { width:175px; }';
      $style  .= '.sc-alert { color:#d00; }';
      $style  .= '.sc-good { color:#1da00c; font-weight:bold; }';
      $style  .= '</style>';
      
      // Show debug only for admin user
      $this->load->library('user');
      $this->user = new User($this->registry);

      if ($this->user->isLogged()) {
         return $data . $style;
      }
   }
}

class sController extends Controller {
   function __construct($registry) {
      $this->registry = $registry;
   }

   function get_child($child, $args = array()) {
      return $this->getChild($child, $args);
   }

   function forward_to($route, $args = array()) {
      $this->forward($child, $args);
   }
   
   function redirect_to($url) {
      $this->redirect($url);
   }
} 