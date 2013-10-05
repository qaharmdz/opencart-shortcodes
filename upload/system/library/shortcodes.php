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