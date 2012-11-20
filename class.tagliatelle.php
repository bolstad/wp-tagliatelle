<?php
/**
 * @package Tagliatelle
 * @version 1.0.3
 * @author Christian Bolstad
 */
/*
Plugin Name: Tagliatelle
Plugin URI: http://kracked.com/wordpress-plugins/tagliatelle/
Description: Tagliatelle is a a PHP-class for easy tagging of wordpress objects.
Version: 1.0.3
Author URI: http://christianbolstad.com/
Author: Christian Bolstad
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
  
  function tag_connected($tag, $postid,$taxonomy)
  {
      if ($check = is_term($tag, $taxonomy)) {
          $termid = $check['term_id'];
          $rs = wp_get_object_terms($postid, $taxonomy);
          foreach ($rs as $item) {
              if ($termid == $item->term_id) {
                  return true;
              }
          }
      } else {
          return false;
      }
  }

function write_tag($id, $solotag, $taxonomy = 'post_tag')
{
  $solotag = trim($solotag);
          if (!$this->tag_connected($solotag, $id,$taxonomy)) 
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
  
function write_tags($id, $tags, $taxonomy = 'post_tag')
  {
      $tags = explode(',', $tags);
      foreach ($tags as $solotag) 
      {
          write_tag($id, $solotag, $taxonomy);
      }  
}
?>
