<?php
/**
 * OpenCart Shortcodes
 * Copyright (c) 2013 qahar
 * http://www.echothemes.com
 * Licensed under the GPLv3 (or later)
 * All rights reserved
 *
 * Based on Shortcode for OpenCart
 * Copyright (c) 2013 RempongSoft
 * http://www.rempongsoft.com
 * All rights reserved
 *
 * Based on WordPress Shortcode
 * Copyright (c) 2003-2013 Wordpress
 * Licensed under the GPLv2 (or later)
 * http://www.wordpress.org/
 * All rights reserved
 *
 *
 * Shortcodes format example:
 * @example [shortcode /]
 * @example [shortcode foo="bar" baz="bing" /]
 * @example [shortcode foo="bar"]content[/shortcode]
 */

class Shortcodes {

   /**
    * Container for storing shortcode tags and their hook to call for the shortcode.
    *
    * @since 1.0
    * @name shortcode_tags
    * @access private
    *
    * @var array
    */
   private $shortcode_tags = array();
   
   /**
    * Class refference of shortcode tags method.
    *
    * @since 1.0
    * @name $shortcode_class
    * @access private
    *
    * @var array
    */
   private $shortcode_class = array();

   /**
    * Add hook for shortcode tag.
    *
    * There can only be one hook for each shortcode. Which means that if another
    * plugin has a similar shortcode, it will override yours or yours will override
    * theirs depending on which order the plugins are included and/or ran.
    *
    * @since 1.0
    * @uses shortcode_tags
    * @uses $shortcode_class
    *
    * @param string $tag Shortcode tag to be searched in post content
    * @param callable $func Hook to run when shortcode is found
    * @param callable $class Class where the shortcode is stored
    */
   public function add_shortcode($tag, $func, $class) {
      if (is_callable(get_class($class).'::'.$func)) {
         $this->shortcode_tags[$tag] = $func;
         $this->shortcode_class[$tag] = $class;
      }
   }

   /**
    * Removes hook for shortcode.
    *
    * @since 1.0
    * @uses shortcode_tags
    * @uses $shortcode_class
    *
    * @param string $tag shortcode tag to remove hook for
    */
   public function remove_shortcode($tag) {
      unset($this->shortcode_tags[$tag]);
      unset($this->shortcode_class[$tag]);
   }

   /**
    * Clear all shortcodes.
    *
    * This function is simple, it clears all of the shortcode tags by replacing the
    * shortcodes global by a empty array. This is actually a very efficient method
    * for removing all shortcodes.
    *
    * @since 1.0
    * @uses shortcode_tags
    * @uses $shortcode_class
    */
   public function remove_all_shortcodes() {
      $this->shortcode_tags = array();
      $this->shortcode_class = array();
   }

   /**
    * Whether a registered shortcode exists named $tag
    *
    * @since 1.0
    *
    * @param string $tag
    * @return boolean
    */
   public function shortcode_exists($tag) {
      return array_key_exists($tag, $this->shortcode_tags);
   }

   /**
    * Whether the passed content contains the specified shortcode.
    *
    * @since 1.0
    *
    * @param string $content
    * @param string $tag
    * @return boolean
    */
   public function has_shortcode($content, $tag) {
      if ( shortcode_exists( $tag ) ) {
         preg_match_all( '/' . $this->get_shortcode_regex() . '/s', $content, $matches, PREG_SET_ORDER );
         if ( empty( $matches ) ) {
            return false;
         }

         foreach ( $matches as $shortcode ) {
            if ( $tag === $shortcode[2] ) {
               return true;
            }
         }
      }
      
      return false;
   }

   /**
    * Search content for shortcodes and filter shortcodes through their hooks.
    *
    * If there are no shortcode tags defined, then the content will be returned
    * without any filtering. This might cause issues when extensions are disabled but
    * the shortcode will still show up in the post or content.
    *
    * @since 1.0
    * @uses shortcode_tags
    * @uses get_shortcode_regex() Gets the search pattern for searching shortcodes.
    *
    * @param string $content Content to search for shortcodes
    * @return string Content with shortcodes filtered out
    */
   public function do_shortcode($content) {
      if (empty($this->shortcode_tags) || !is_array($this->shortcode_tags)) {
         return $content;
      }

      $pattern = $this->get_shortcode_regex();
      
      return preg_replace_callback('/' . $pattern . '/s', 'Shortcodes::do_shortcode_tag', $content);
   }

   /**
    * Retrieve the shortcode regular expression for searching.
    *
    * The regular expression combines the shortcode tags in the regular expression
    * in a regex class.
    *
    * The regular expression contains 6 different sub matches to help with parsing.
    *
    * 1 - An extra [ to allow for escaping shortcodes with double [[]]
    * 2 - The shortcode name
    * 3 - The shortcode argument list
    * 4 - The self closing /
    * 5 - The content of a shortcode when it wraps some content.
    * 6 - An extra ] to allow for escaping shortcodes with double [[]]
    *
    * @since 1.0
    * @uses shortcode_tags
    *
    * @return string The shortcode search regular expression
    */
   public function get_shortcode_regex() {
      $tagnames = array_keys($this->shortcode_tags);
      $tagregexp = join( '|', array_map('preg_quote', $tagnames) );

      // WARNING! Do not change this regex!
      return
           '\\['                              // Opening bracket
         . '(\\[?)'                           // 1: Optional second opening bracket for escaping shortcodes: [[tag]]
         . "($tagregexp)"                     // 2: Shortcode name
         . '(?![\\w-])'                       // Not followed by word character or hyphen
         . '('                                // 3: Unroll the loop: Inside the opening shortcode tag
         .     '[^\\]\\/]*'                   // Not a closing bracket or forward slash
         .     '(?:'
         .         '\\/(?!\\])'               // A forward slash not followed by a closing bracket
         .         '[^\\]\\/]*'               // Not a closing bracket or forward slash
         .     ')*?'
         . ')'
         . '(?:'
         .     '(\\/)'                        // 4: Self closing tag ...
         .     '\\]'                          // ... and closing bracket
         . '|'
         .     '\\]'                          // Closing bracket
         .     '(?:'
         .         '('                        // 5: Unroll the loop: Optionally, anything between the opening and closing shortcode tags
         .             '[^\\[]*+'             // Not an opening bracket
         .             '(?:'
         .                 '\\[(?!\\/\\2\\])' // An opening bracket not followed by the closing shortcode tag
         .                 '[^\\[]*+'         // Not an opening bracket
         .             ')*+'
         .         ')'
         .         '\\[\\/\\2\\]'             // Closing shortcode tag
         .     ')?'
         . ')'
         . '(\\]?)';                          // 6: Optional second closing brocket for escaping shortcodes: [[tag]]
   }

   /**
    * Regular Expression callable for do_shortcode() for calling shortcode hook.
    * @see get_shortcode_regex for details of the match array contents.
    *
    * @since 1.0
    * @access private
    * @uses shortcode_tags
    *
    * @param array $m Regular expression match array
    * @return mixed False on failure
    */
   private function do_shortcode_tag($m) {
      if ( $m[1] == '[' && $m[6] == ']' ) {
         return substr($m[0], 1, -1);
      }

      $tag = $m[2];
      $attr = $this->shortcode_parse_atts( $m[3] );
       
      if ( isset( $m[5] ) ) {
         // enclosing tag - extra parameter
         return $m[1] . call_user_func( array( $this->shortcode_class[$tag] ,$this->shortcode_tags[$tag]), $attr, $m[5], $tag ) . $m[6];
      } else {
         // self-closing tag
         return $m[1] . call_user_func( array( $this->shortcode_class[$tag] ,$this->shortcode_tags[$tag]), $attr, null,  $tag ) . $m[6];
      }
   }

   /**
    * Retrieve all attributes from the shortcodes tag.
    *
    * The attributes list has the attribute name as the key and the value of the
    * attribute as the value in the key/value pair. This allows for easier
    * retrieval of the attributes, since all attributes have to be known.
    *
    * @since 1.0
    *
    * @param string $text
    * @return array List of attributes and their value
    */
   public function shortcode_parse_atts($text) {
      $atts = array();
      $pattern = '/(\w+)\s*=\s*"([^"]*)"(?:\s|$)|(\w+)\s*=\s*\'([^\']*)\'(?:\s|$)|(\w+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|(\S+)(?:\s|$)/';
      $text = preg_replace("/[\x{00a0}\x{200b}]+/u", " ", $text);
      if ( preg_match_all($pattern, $text, $match, PREG_SET_ORDER) ) {
         foreach ($match as $m) {
            if (!empty($m[1])) {
               $atts[strtolower($m[1])] = stripcslashes($m[2]);
            } elseif (!empty($m[3])) {
               $atts[strtolower($m[3])] = stripcslashes($m[4]);
            } elseif (!empty($m[5])) {
               $atts[strtolower($m[5])] = stripcslashes($m[6]);
            } elseif (isset($m[7]) and strlen($m[7])) {
               $atts[] = stripcslashes($m[7]);
            } elseif (isset($m[8])) {
               $atts[] = stripcslashes($m[8]);
            }
         }
      } else {
         $atts = ltrim($text);
      }
      
      return $atts;
   }

   /**
    * Combine user attributes with known attributes and fill in defaults when needed.
    *
    * The pairs should be considered to be all of the attributes which are
    * supported by the caller and given as a list. The returned attributes will
    * only contain the attributes in the $pairs list.
    *
    * If the $atts list has unsupported attributes, then they will be ignored and
    * removed from the final returned list.
    *
    * @since 1.0
    *
    * @param array $pairs Entire list of supported attributes and their defaults
    * @param array $atts User defined attributes in shortcode tag
    * @param string $shortcode Optional. The name of the shortcode, provided for context to enable filtering
    * @return array Combined and filtered attribute list
    */
   public function shortcode_atts($pairs, $atts, $shortcode = '') {
      $atts = (array)$atts;
      $out = array();
      foreach($pairs as $name => $default) {
         if ( array_key_exists($name, $atts) ) {
            $out[$name] = $atts[$name];
         } else {
            $out[$name] = $default;
         }
      }

      return $out;
   }

   /**
    * Remove all shortcode tags from the given content.
    *
    * @since 1.0
    * @uses shortcode_tags
    *
    * @param string $content Content to remove shortcode tags
    * @return string Content without shortcode tags
    */
   public function strip_shortcodes($content) {
      if (empty($this->shortcode_tags) || !is_array($this->shortcode_tags)) {
         return $content;
      }
      
      if (is_array($content)) {
         foreach ($content as $key => $value) {
            $content[$this->strip_shortcodes($key)] = $this->strip_shortcodes($value);
         }
      }

      $pattern = $this->get_shortcode_regex();

      return preg_replace_callback('/' . $pattern . '/s', 'Shortcodes::strip_shortcode_tag', $content);
   }

   /**
    * Allo [[foo]] syntax for escaping a tag.
    *
    * @since 1.0
    * @uses shortcode_tags
    *
    * @param array $m Regular expression match array
    * @return string Content without shortcode tags
    */
   public function strip_shortcode_tag($m) {
      if ( $m[1] == '[' && $m[6] == ']' ) {
         return substr($m[0], 1, -1);
      }

      return $m[1] . $m[6];
   }
}