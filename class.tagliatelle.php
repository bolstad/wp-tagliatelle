<?php
/**
 * @package Tagliatelle
 * @version 1
 */
/*
Plugin Name: Tagliatelle
Plugin URI: http://kracked.com/wordpress-plugins/tagliatelle/
Description: A class for easy tagging of wordpress objects 
Version: 1.0
Author URI: http://christianbolstad.se/
*/

class Tagliatelle
{  
  function find_tagged_post($tag, $value,$post_type = 'post')
  {
      $superid = null;
      query_posts(array('posts_per_page' => '1', 'post_type' => $post_type, 'meta_key' => $tag, 'meta_value' => $value, 'orderby' => 'meta_value_num', 'order' => 'DESC'));
      if (have_posts())
          : while (have_posts())
          : the_post();
      $superid = get_the_ID();
      endwhile;
      endif;
      wp_reset_query();
      return $superid;
  }
  
  function tagConnected($tag, $postid)
  {
      if ($check = is_term($tag, 'post_tag')) {
          $termid = $check['term_id'];
          $rs = wp_get_object_terms($postid, 'post_tag');
          foreach ($rs as $item) {
              if ($termid == $item->term_id) {
                  return true;
              }
          }
      } else {
          return false;
      }
  }
  
function write_tags($id, $tags, $taxonomy = 'post_tag')
  {
      $tags = explode(',', $tags);
      foreach ($tags as $solotag) {
          $solotag = trim($solotag);
          if (!$this->tagConnected($solotag, $id)) 
          {   // $id is not tagged with $solotag
              $check = is_term($solotag, $taxonomy);
              if (is_null($check)) {
                  $tag = wp_insert_term($solotag, $taxonomy);
                  if (!is_wp_error($tag)) {  $tagid = $tag['term_id'];          } 
              } else { $tagid = $check['term_id'];   }              
              $blah = array($solotag);              
              $ret = wp_set_object_terms($id, $blah, $taxonomy, true);
          }
      }
  }  
}
?>