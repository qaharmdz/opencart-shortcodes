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
   
   function foobar() {
      return "foo and bar";
   }

   function category_name($atts) {
      extract( shortcode_atts(
         array(
            'id' => 0,
         ), $atts)
      );
      
      $this->load->model('catalog/category');
      $category = $this->model_catalog_category->getCategory($id);
      
      return $category['name'];
   }
   
   function product_link($atts, $content = null) {
      $data = shortcode_atts(array(
         'id'     => 0,
         'path'   => 0
      ), $atts);

      if ($content) {
         if(!$data['path'] && $data['id']) {
            return '<a href="' . $this->url->link('product/product', 'product_id=' . $data['id']) . '">' . $content . '</a>';
         } elseif ($data['path'] && $data['id']) {
            return '<a href="' . $this->url->link('product/product', 'path=' . $data['path'] . '&product_id=' . $data['id']) . '">' . $content . '</a>';
         } else {
            return $content;
         }
      }
   }
   
   function module($atts, $content = NULL) {
      // [module]featured[/module]
      // [module limit="5" img_w="100" img_h="100"]latest[/module]
      // [module limit="3" img_w="120" img_h="120"]special[/module]
      
      $data = shortcode_atts(array(
         'limit'  => 5,
         'img_w'  => 80,
         'img_h'  => 80
      ), $atts);
      
      if ($content) {
         $action = new sController($this->registry); 

         $module = $action->get_child('module/'.$content, array(
                     'limit' => $data['limit'],
                     'image_width' => $data['img_w'],
                     'image_height' => $data['img_h']
                  ));
         
         return $module;
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