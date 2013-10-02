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
 * [shortcode /]
 * [shortcode foo="bar" baz="bing" /]
 * [shortcode foo="bar"]content[/shortcode]
 */

$shortcode_tags = array();
$shortcode_class = array();

// Add shortcode tag
function add_shortcode($tag, $func, $class) {
   global $shortcode_tags;
   global $shortcode_class;

   if (is_callable(get_class($class).'::'.$func)) {
      $shortcode_tags[$tag] = $func;        
      $shortcode_class[$tag] = $class;
   }
}

// Removes shortcode tag
function remove_shortcode($tag) {
   global $shortcode_tags;
   unset($shortcode_tags[$tag]);
}

// Clear all shortcodes
function remove_all_shortcodes() {
   global $shortcode_tags;
   $shortcode_tags = array();
}

// Check shortcode exists named $tag
function shortcode_exists($tag) {
   global $shortcode_tags;
   return array_key_exists($tag, $shortcode_tags);
}

// Whether the passed content contains the specified shortcode
function has_shortcode($content, $tag) {
   if ( shortcode_exists( $tag ) ) {
      preg_match_all( '/' . get_shortcode_regex() . '/s', $content, $matches, PREG_SET_ORDER );
      if ( empty( $matches ) )
         return false;

      foreach ( $matches as $shortcode ) {
         if ( $tag === $shortcode[2] )
            return true;
      }
   }
   return false;
}

// Search content for shortcodes and apply them
function do_shortcode($content) {
   global $shortcode_tags;

   if (empty($shortcode_tags) || !is_array($shortcode_tags))
      return $content;

   $pattern = get_shortcode_regex();
   return preg_replace_callback( "/$pattern/s", 'do_shortcode_tag', $content );
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
 */
function get_shortcode_regex() {
   global $shortcode_tags;
   
   $tagnames = array_keys($shortcode_tags);
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

// Regular Expression callable for do_shortcode()
function do_shortcode_tag($m) {
   global $shortcode_tags;    
   global $shortcode_class; 

   if ( $m[1] == '[' && $m[6] == ']' ) {
      return substr($m[0], 1, -1);
   }

   $tag = $m[2];
   $attr = shortcode_parse_atts( $m[3] );
    
   if ( isset( $m[5] ) ) {
      // enclosing tag - extra parameter
      return $m[1] . call_user_func( array( $shortcode_class[$tag] ,$shortcode_tags[$tag]), $attr, $m[5], $tag ) . $m[6];
   } else {
      // self-closing tag
      return $m[1] . call_user_func( array( $shortcode_class[$tag] ,$shortcode_tags[$tag]), $attr, null,  $tag ) . $m[6];
   }
}

// Retrieve all attributes from the shortcodes tag
function shortcode_parse_atts($text) {
   $atts = array();
   $pattern = '/(\w+)\s*=\s*"([^"]*)"(?:\s|$)|(\w+)\s*=\s*\'([^\']*)\'(?:\s|$)|(\w+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|(\S+)(?:\s|$)/';
   $text = preg_replace("/[\x{00a0}\x{200b}]+/u", " ", $text);
   if ( preg_match_all($pattern, $text, $match, PREG_SET_ORDER) ) {
      foreach ($match as $m) {
         if (!empty($m[1]))
            $atts[strtolower($m[1])] = stripcslashes($m[2]);
         elseif (!empty($m[3]))
            $atts[strtolower($m[3])] = stripcslashes($m[4]);
         elseif (!empty($m[5]))
            $atts[strtolower($m[5])] = stripcslashes($m[6]);
         elseif (isset($m[7]) and strlen($m[7]))
            $atts[] = stripcslashes($m[7]);
         elseif (isset($m[8]))
            $atts[] = stripcslashes($m[8]);
      }
   } else {
      $atts = ltrim($text);
   }
   
   return $atts;
}

// Combine user attributes with known attributes and fill in defaults when needed
function shortcode_atts($pairs, $atts, $shortcode = '') {
   $atts = (array)$atts;
   $out = array();
   foreach($pairs as $name => $default) {
      if ( array_key_exists($name, $atts) )
         $out[$name] = $atts[$name];
      else
         $out[$name] = $default;
   }

   return $out;
}

// Remove all shortcode tags from the given content
function strip_shortcodes($content) {
   global $shortcode_tags;

   if (empty($shortcode_tags) || !is_array($shortcode_tags))
      return $content;

   $pattern = get_shortcode_regex();

   return preg_replace_callback( "/$pattern/s", 'strip_shortcode_tag', $content );
}

function strip_shortcode_tag($m) {
   // allow [[foo]] syntax for escaping a tag
   if ( $m[1] == '[' && $m[6] == ']' ) {
      return substr($m[0], 1, -1);
   }

   return $m[1] . $m[6];
}