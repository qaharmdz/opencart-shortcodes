<?php
class ShortcodesDefault extends Controller {

   /**
    * Generate product link with it variant of category and manufacture link.
    *
    * @since 1.0
    * 
    * @param array $atts Shortcode attributes
    * @param string $content Shortcode content
    * @return string Link to product page
    * 
    * @example [link_product id="x" path="x_x" brand="x" ssl="0" title="xyz" /]
    * @example [link_product id="x" path="x_x" brand="x" ssl="0" title="xyz"]custom text[/link_product]
    */
   function link_product($atts, $content = '') {
      extract($this->shortcodes->shortcode_atts(array(
         'id'     => 0,
         'path'   => 0,
         'brand'  => 0,
         'ssl'    => 0,
         'title'  => ''
      ), $atts));
      
      if ($id) {
         $ssl     = ($ssl) ? "'SSL'" : "";
         $title   = ($title) ? 'Title="' . $title . '"' : "";

         $this->load->model('catalog/product');
         $product = $this->model_catalog_product->getProduct($id);
         
         if ($product) {
            if (!$content) {
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
         } elseif (!$product && $content) {
            return $content;
         }
      }
   }
   
   /**
    * Generate category link.
    *
    * @since 1.0
    * 
    * @param array $atts Shortcode attributes
    * @param string $content Shortcode content
    * @return string Link to category page
    * 
    * @example [link_category path="x_y" ssl="0" title="xyz" /]
    * @example [link_category path="x_y" ssl="1" title="xyz"]custom text[/link_category]
    */
   function link_category($atts, $content = '') {
      extract($this->shortcodes->shortcode_atts(array(
         'path'   => 0,
         'ssl'    => 0,
         'title'  => ''
      ), $atts));
      
      if ($path) {
         $ssl     = ($ssl) ? "'SSL'" : "";
         $title   = ($title) ? 'Title="' . $title . '"' : "";
         
         $this->load->model('catalog/category');
         $category = $this->model_catalog_category->getCategory($path);
         
         if ($category) {
            if (!$content) {
               return '<a href="' . $this->url->link('product/category', 'path=' . $path, $ssl) . '" ' . $title . '>' . $category['name'] . '</a>';
            } elseif ($content) {
               return '<a href="' . $this->url->link('product/category', 'path=' . $path, $ssl) . '" ' . $title . '>' . $content . '</a>';
            }
         } elseif (!$category && $content) {
            return $content;
         }
      }
   }
   
   /**
    * Generate brand/ manufacturer link.
    * 
    * @since 1.0
    * 
    * @param array $atts Shortcode attributes
    * @param string $content Shortcode content
    * @return string Link to manufacturer list or manufacture page
    * 
    * @example [link_brand ssl="0" title="xyz" /]
    * @example [link_brand ssl="1" title="xyz"]custom text[/link_brand]
    * @example [link_brand brand="x" ssl="0" title="xyz" /]
    * @example [link_brand brand="x" ssl="1" title="xyz"]custom text[/link_brand]
    */
   function link_brand($atts, $content = '') {
      extract($this->shortcodes->shortcode_atts(array(
         'brand'  => 0,
         'ssl'    => 0,
         'title'  => ''
      ), $atts));
      
      $ssl     = ($ssl) ? "'SSL'" : "";
      $title   = ($title) ? 'Title="' . $title . '"' : "";

      if ($brand) {
         $this->load->model('catalog/manufacturer');
         $manufacturer = $this->model_catalog_manufacturer->getManufacturer($brand);
         
         if (version_compare(VERSION, '1.5.3.1', '<=') == true) {
            $brand_route   = 'product/manufacturer/product';
         } else {
            $brand_route   = 'product/manufacturer/info';
         }
         
         if ($manufacturer) {
            if (!$content) {
               return '<a href="' . $this->url->link($brand_route, 'manufacturer_id=' . $brand, $ssl) . '" ' . $title . '>' . $manufacturer['name'] . '</a>';
            } else {
               return '<a href="' . $this->url->link($brand_route, 'manufacturer_id=' . $brand, $ssl) . '" ' . $title . '>' . $content . '</a>';
            }
         } elseif (!$manufacturer && $content) {
            return $content;
         }
      } else {
         if (!$content) {
            $this->load->language('product/manufacturer');

            return '<a href="' . $this->url->link('product/manufacturer', '', $ssl) . '" ' . $title . '>' . $this->language->get('text_brand') . '</a>';
         } else {
            return '<a href="' . $this->url->link('product/manufacturer', '', $ssl) . '" ' . $title . '>' . $content . '</a>';
         }
      }
   }
   
   /**
    * Generate information link.
    * 
    * @since 1.0
    * 
    * @param array $atts Shortcode attributes
    * @param string $content Shortcode content
    * @return string Link to information page
    * 
    * @example [link_info id="x" ssl="0" title="xyz" /]
    * @example [link_info id="x" ssl="0" title="xyz"]custom text[/link_info]
    */
   function link_info($atts, $content = '') {
      extract($this->shortcodes->shortcode_atts(array(
         'id'     => 0,
         'ssl'    => 0,
         'title'  => ''
      ), $atts));
      
      if ($id) {
         $ssl     = ($ssl) ? "'SSL'" : "";
         $title   = ($title) ? 'Title="' . $title . '"' : "";
         
         $this->load->model('catalog/category');
         $information = $this->model_catalog_information->getInformation($id);
         
         if ($information) {
            if (!$content) {
               return '<a href="' . $this->url->link('information/information', 'information_id=' . $id, $ssl) . '" ' . $title . '>' . $information['title'] . '</a>';
            } elseif ($content) {
               return '<a href="' . $this->url->link('information/information', 'information_id=' . $id, $ssl) . '" ' . $title . '>' . $content . '</a>';
            }
         } elseif (!$information && $content) {
            return $content;
         }
      }
   }
   
   /**
    * Generate custom link based on OpenCart API Url format
    * 
    * @since 1.0
    * 
    * @param array $atts Shortcode attributes
    * @param string $content Shortcode content
    * @return string Return link based on user input
    * 
    * @example [link_custom route="foo" args="bar" ssl="0" title="xyz"]custom text[/link_custom]
    */
   function link_custom($atts, $content = '') {
      extract($this->shortcodes->shortcode_atts(array(
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
    * Generate custom link for multi-store site
    * 
    * @since 1.0
    * 
    * @param array $atts Shortcode attributes
    * @param string $content Shortcode content
    * @return string Link to manufacturer list or manufacture page
    * 
    * @example [link_store store="x" route="foo" args="bar" ssl="0" title="xyz"]custom text[/link_custom]
    */
   function link_store($atts, $content = '') {
      extract($this->shortcodes->shortcode_atts(array(
         'store'  => 0,
         'route'  => '',
         'args'   => '',
         'ssl'    => 0,
         'title'  => ''
      ), $atts));
      
      if ($route && $content) {
         $current_store    = $this->config->get('config_url');
         
         if ($store) {
            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE store_id = '" . (int)$store . "'" );
            
            if ($query->num_rows) {
               foreach ($query->rows as $setting) {
                  if ($setting['key'] == 'config_url') {
                     $store_url  = $setting['value'];
                  }
               }

               $url = str_replace($current_store, $store_url, $this->url->link($route, $args, $ssl));
               
               return '<a href="' . $url . '" ' . $title . '>' . $content . '</a>';
            } else {
               return $content;
            }
         } else {
            $store_url  = HTTP_SERVER;
            
            $url = str_replace($current_store, $store_url, $this->url->link($route, $args, $ssl));
               
            return '<a href="' . $url . '" ' . $title . '>' . $content . '</a>';
         }
      }
   }
   
   /**
    * Load module type product (featured, latest, bestseller, special) anywhere!
    * 
    * @since 1.0
    * 
    * @param array $atts Shortcode attributes
    * @return string Module based on user choose
    * 
    * @example [module_product type="featured" limit="5" img_w="100" img_h="100" /]
    */
   function module_product($atts) {
      extract($this->shortcodes->shortcode_atts(array(
         'type'   => '',
         'limit'  => 5,
         'img_w'  => 80,
         'img_h'  => 80
      ), $atts));

      if ($type) {
         $module = $this->getChild('module/' . $type, array(
                     'limit'        => $limit,
                     'image_width'  => $img_w,
                     'image_height' => $img_h
                  ));
         
         $html = '<div class="shortcode-module sc-' . $type . '">' . $module . '</div>';
         
         return $html;
      }
   }
   
   /**
    * Load module slideshow
    * 
    * @since 1.0
    * 
    * @param array $atts Shortcode attributes
    * @return string Show module slideshow
    * 
    * @example [module_slideshow id="x" limit="5" img_w="100" img_h="100" /]
    */
   function module_slideshow($atts) {
      extract($this->shortcodes->shortcode_atts(array(
         'id'     => 0,
         'img_w'  => 80,
         'img_h'  => 80
      ), $atts));

      if ($id) {
         $script  = '<script type="text/javascript" src="catalog/view/javascript/jquery/nivo-slider/jquery.nivo.slider.pack.js"></script>';
         $style   = '<link href="catalog/view/theme/default/stylesheet/slideshow.css" type="text/css" rel="stylesheet" />';

         $module = $this->getChild('module/slideshow', array(
                     'banner_id' => $id,
                     'width'     => $img_w,
                     'height'    => $img_h
                  ));
         
         $html = '<div class="shortcode-module sc-slideshow">' . $script . $style . $module . '</div>';
         
         return $html;
      }
   }
   
   /**
    * User required to login to read the rest of the content.
    * Able to restrict user to read based on their group.
    * 
    * @since 1.0
    * 
    * @param array $atts Shortcode attributes
    * @param string $content Shortcode content
    * @return string Message if not permitted to read, show rest of article if permitted.
    * 
    * @example [login message='Silahkan <a href="%s">login</a> untuk melihat halaman ini.']content[/login]
    */
   function login($atts, $content = '') {
      $this->language->load('common/shortcodes_default');
      
      extract($this->shortcodes->shortcode_atts(array(
         'msg_login'    => $this->language->get('login_message'),
         'msg_group'    => $this->language->get('login_group'),
         'suffix'       => 'attention',
         'group'        => ''
      ), $atts));
      
      
      
      if ($content && $this->customer->isLogged()) {
         if($group) {
            if ($group == $this->customer->getCustomerGroupId()) {
               return $this->shortcodes->do_shortcode($content);
            } else {
               return '<div class="' . $suffix . '">' . sprintf($msg_group, $this->url->link('information/contact')) . '</div>';
            }
         } else {
            return $this->shortcodes->do_shortcode($content);
         }
      } else {
         return '<div class="' . $suffix . '">' . sprintf($msg_login, $this->url->link('account/login')) . '</div>';
      }
   }
   
   /**
    * Embed video: youtube and vimeo
    * 
    * @since 1.0
    * 
    * @param array $atts Shortcode attributes
    * @return string Video embed code
    * 
    * @example [video type="vimeo" id="xxx" vid_w="450" vid_h="280" /]
    */
   function video($atts) {
      extract($this->shortcodes->shortcode_atts(array(
         'type'      => 'youtube',
         'id'        => 0,
         'vid_w'     => 450,
         'vid_h'     => 280,
         'autoplay'  => 0
      ), $atts));

      if ($id) {
         if ($type == 'youtube') {
            $video   = '<iframe width="' . $vid_w . '" height="' . $vid_h . '" src="http://youtube.com/embed/' . $id . '?rel=0&autoplay=' . $autoplay . '" frameborder="0" allowfullscreen></iframe>';
            
            $html    = '<div class="shortcode-video sc-' . $type . '">' . $video . '</div>';
            
            return $html;
            
         } elseif ($type == 'vimeo') {
            $video   = '<iframe src="//player.vimeo.com/video/' . $id . '?autoplay=' . $autoplay . '" width="' . $vid_w . '" height="' . $vid_h . '" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
            
            $html    = '<div class="shortcode-video sc-' . $type . '">' . $video . '</div>';
            
            return $html;
         }
      }
   }
   
   /**
    * Embed image directly or with cache
    * 
    * @since 1.0
    * 
    * @param array $atts Shortcode attributes
    * @param string $content Shortcode content
    * @return string Image html code
    * 
    * @example [image src="" img_w="450" img_h="280" title="" alt="" align="" /]
    */
   function image($atts, $content = '') {
      extract($this->shortcodes->shortcode_atts(array(
         'src'       => '',
         'img_w'     => 200,
         'img_h'     => 200,
         'title'     => '',
         'alt'       => '',
         'align'     => '',    // left, right, center
         'cache'     => 1
      ), $atts));
      
      if (!$src && $content) { $src = $content; }
      if (!$alt & $title) { $alt = $title; }
      if ($align == 'right') {
         $align_style = 'float:right;margin:0 0 10px 10px;';
      } elseif ($align == 'center') {
         $align_style = 'display:block;margin:0 auto 15px;';
      } else {
         $align_style = 'float:left;margin:0 10px 0 10px;';
      }

      $src_resize = str_replace('image/', '', $src);
      
      if (is_file(DIR_IMAGE . $src_resize)) {
         if ($cache) {
            $this->load->model('tool/image');
            $src = $this->model_tool_image->resize($src_resize, $img_w, $img_h);
         }
         
         return '<img class="shortcode-image" src="' . $src . '" width="' . $img_w.'px' . '" height="' . $img_h.'px' . '" alt="' . $alt . '" title="' . $title . '" style="' . $align_style . '">';
      }
   }
   
   /**
    * Embed image with modalbox feature
    * 
    * @since 1.1
    * 
    * @param array $atts Shortcode attributes
    * @return string Thumbnail with link to open modal box
    * 
    * @example [image_modal src="image/data/your_image.jpg" /]
    * @example [image_modal src="image/data/your_image.jpg" img_w="450" img_h="280" title="" alt="" align="" caption="" load_script="1"/]
    */
   function image_modal($atts, $content = '') {
      $this->language->load('common/shortcodes_default');
      
      extract($this->shortcodes->shortcode_atts(array(
         'src'       => '',
         'img_w'     => 200,
         'img_h'     => 200,
         'title'     => '',
         'alt'       => '',
         'align'     => 'left', // left, right, center
         'caption'   => $this->language->get('imgModal_caption'),
         'load_script'  => 0,
         'cache'     => 1
      ), $atts));
      
      if (!$src && $content) { $src = $content; }
      if (!$alt & $title) { $alt = $title; }
      if ($align == 'right') {
         $align_style = 'float:right;margin:0 0 10px 10px;';
      } elseif ($align == 'center') {
         $align_style = 'display:block;margin:0 auto 15px;';
      } else {
         $align_style = 'float:left;margin:0 10px 10px 0;';
      }
      
      $src_resize    = str_replace('image/', '', $src);
      $script_load   = '';
      
      if ($load_script) {
         $script_load = '<script type="text/javascript"><!--
               $(document).ready(function() {
                  $(".modalbox").colorbox({
                     overlayClose: true,
                     opacity: 0.5
                  });
               });
               //--></script> ';
         $script_load .= '<link rel="stylesheet" type="text/css" href="catalog/view/javascript/jquery/colorbox/colorbox.css" media="screen" />';
         $script_load .= '<script type="text/javascript" src="catalog/view/javascript/jquery/colorbox/jquery.colorbox-min.js"></script>';
      }
      
      if (is_file(DIR_IMAGE . $src_resize)) {
         if ($cache) {
            $this->load->model('tool/image');
            $src_thumb  = $this->model_tool_image->resize($src_resize, $img_w, $img_h);
         } else {
            $src_thumb  = $src;
         }

         $html  = '<div style="' . $align_style . '">';
         $html .= '<a href="' . $src . '" title="' . $title . '" class="colorbox modalbox" style="text-decoration:none; outline:0;">';
         $html .= '<img class="shortcode-image-modal" src="' . $src_thumb . '" width="' . $img_w.'px' . '" height="' . $img_h.'px' . '" alt="' . $alt . '" title="' . $title . '">';
         $html .= '<div class="help" style="font-style:italic;">' . $caption . '</div>';
         $html .= '</a>';
         $html .= $script_load;
         $html .= '</div>';
         
         return $html;
      }
   }
   
   /**
    * Show lite System Information 
    * (full: http://www.echothemes.com/extensions/system-information.html)
    * 
    * @since 1.0
    * 
    * @return string List of system information
    * 
    * @example [debug /]
    */
   function debug() {
      $this->language->load('common/shortcodes_default');
      
      $data    = '<h3>' . $this->language->get('debug_title') . ' - ' . date('d M, Y') . '</h3>';
      $data   .= '<table class="sc-debug">';
      $data   .= '<tr><td>' . $this->language->get('debug_opencart') . '</td><td>: v' . VERSION . '</td></tr>';
      if (isset(VQMod::$_vqversion)) {
         $data   .=  '<tr><td>' . $this->language->get('debug_vqmod') . '</td><td>: v' . VQMod::$_vqversion . '</td></tr>';
      }
      $data   .= '<tr><td>' . $this->language->get('debug_shortcodes') . '</td><td>: v' . SHORTCODES_VERSION . '</td></tr>';
      $data   .= '</table>';
      $data   .= '<table class="sc-debug">';
      $data   .= '<tr><td>' . $this->language->get('debug_php') . '</td><td>: v.' . phpversion() . '</td></tr>';
      $data   .= '<tr><td>' . $this->language->get('debug_safemode') . '</td><td>: ' . ((ini_get('safe_mode')) ? $this->language->get('text_on') . ' <span class="sc-alert">- ' . $this->language->get('text_req_off') . '</span>' :  $this->language->get('text_off') . ' <span class="sc-good">- ' . $this->language->get('text_good') . '</span>') . '</td></tr>';
      $data   .= '<tr><td>' . $this->language->get('debug_reg_global') . '</td><td>: ' . ((ini_get('register_globals')) ? $this->language->get('text_on') . ' <span class="sc-alert">- ' . $this->language->get('text_req_off') . '</span>' :  $this->language->get('text_off') . ' <span class="sc-good">- ' . $this->language->get('text_good') . '</span>') . '</td></tr>';
      $data   .= '<tr><td>' . $this->language->get('debug_gpc') . '</td><td>: ' . ((ini_get('magic_quotes_gpc') || get_magic_quotes_gpc()) ? $this->language->get('text_on') . ' <span class="sc-alert">- ' . $this->language->get('text_req_off') . '</span>' :  $this->language->get('text_off') . ' <span class="sc-good">- ' . $this->language->get('text_good') . '</span>') . '</td></tr>';
      $data   .= '<tr><td>' . $this->language->get('debug_session') . '</td><td>: ' . ((ini_get('session_auto_start')) ? $this->language->get('text_on') . ' <span class="sc-alert">- ' . $this->language->get('text_req_off') . '</span>' :  $this->language->get('text_off') . ' <span class="sc-good">- ' . $this->language->get('text_good') . '</span>') . '</td></tr>';
      $data   .= '<tr><td>' . $this->language->get('debug_fopen') . '</td><td>: ' . ((ini_get('allow_url_fopen')) ? $this->language->get('text_on') . ' <span class="sc-good">- ' . $this->language->get('text_good') . '</span>' :  $this->language->get('text_off') . ' <span class="sc-alert">- ' . $this->language->get('text_req_on') . '</span>') . '</td></tr>';
      if(VERSION >= '1.5.4') {
         $data   .= '<tr><td>' . $this->language->get('debug_mcrypt') . '</td><td>: ' . ((extension_loaded('mcrypt')) ? $this->language->get('text_on') . ' <span class="sc-good">- ' . $this->language->get('text_good') . '</span>' :  $this->language->get('text_off') . ' <span class="sc-alert">- ' . $this->language->get('text_req_on') . '</span>') . '</td></tr>';
      }
      $data   .= '<tr><td>' . $this->language->get('debug_upload') . '</td><td>: ' . ((ini_get('file_uploads')) ? $this->language->get('text_on') . ' <span class="sc-good">- ' . $this->language->get('text_good') . '</span>' :  $this->language->get('text_off') . ' <span class="sc-alert">- ' . $this->language->get('text_req_on') . '</span>') . '</td></tr>';
      $data   .= '<tr><td>' . $this->language->get('debug_cookies') . '</td><td>: ' . ((ini_get('session.use_cookies')) ? $this->language->get('text_on') . ' <span class="sc-good">- ' . $this->language->get('text_good') . '</span>' :  $this->language->get('text_off') . ' <span class="sc-alert">- ' . $this->language->get('text_req_on') . '</span>') . '</td></tr>';
      $data   .= '<tr><td>' . $this->language->get('debug_gd') . '</td><td>: ' . ((extension_loaded('gd')) ? $this->language->get('text_on') . ' <span class="sc-good">- ' . $this->language->get('text_good') . '</span>' :  $this->language->get('text_off') . ' <span class="sc-alert">- ' . $this->language->get('text_req_on') . '</span>') . '</td></tr>';
      $data   .= '<tr><td>' . $this->language->get('debug_curl') . '</td><td>: ' . ((extension_loaded('curl')) ? $this->language->get('text_on') . ' <span class="sc-good">- ' . $this->language->get('text_good') . '</span>' :  $this->language->get('text_off') . ' <span class="sc-alert">- ' . $this->language->get('text_req_on') . '</span>') . '</td></tr>';
      $data   .= '<tr><td>' . $this->language->get('debug_fsock') . '</td><td>: ' . ((extension_loaded('sockets')) ? $this->language->get('text_on') . ' <span class="sc-good">- ' . $this->language->get('text_good') . '</span>' :  $this->language->get('text_off') . ' <span class="sc-alert">- ' . $this->language->get('text_req_on') . '</span>') . '</td></tr>';
      $data   .= '<tr><td>' . $this->language->get('debug_zip') . '</td><td>: ' . ((extension_loaded('zlib')) ? $this->language->get('text_on') . ' <span class="sc-good">- ' . $this->language->get('text_good') . '</span>' :  $this->language->get('text_off') . ' <span class="sc-alert">- ' . $this->language->get('text_req_on') . '</span>') . '</td></tr>';
      $data   .= '<tr><td>' . $this->language->get('debug_xml') . '</td><td>: ' . ((extension_loaded('xml')) ? $this->language->get('text_on') . ' <span class="sc-good">- ' . $this->language->get('text_good') . '</span>' :  $this->language->get('text_off') . ' <span class="sc-alert">- ' . $this->language->get('text_req_on') . '</span>') . '</td></tr>';
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
         $html = '<div class="shortcode-debug">' . $style . $data . '</div>';
         
         return $html;
      }
   }
}