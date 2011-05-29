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
  
  public $debug = 0;
  
  function debugmsg($str)
  {
    if ($this->debug) echo "$str<br>\n"; 
  }
  
  
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
                  $this->debugmsg("Tag '$tag' ($termid) is already connected to Page $postid");
                  return true;
              }
          }
      } else {
          $this->debugmsg("taggen '$tag' finns inte");
          return false;
      }
  }
  
  function write_tags($id, $tags, $taxonomy = 'post_tag')
  {
      $tags = explode(',', $tags);
      foreach ($tags as $solotag) {
          $solotag = trim($solotag);
          if ($this->debug) $this->debugmsg("Solotag $solotag <br>\n");
          if ($this->tagConnected($solotag, $id)) {
              $this->debugmsg("- PASSIVE: '$solotag' is already connected with '$id'");
          } else {
              $this->debugmsg("- ACTIVE: '$solotag' is NOT already connected with '$id'");
              $this->debugmsg("  Testar $solotag<br>");
              $check = is_term($solotag, $taxonomy);
              if (is_null($check)) {
                  $this->debugmsg("  '$solotag' finns inte som solotag'");;
                  $tag = wp_insert_term($solotag, $taxonomy);
                  if (!is_wp_error($tag)) {
                      $tagid = $tag['term_id'];
                      $this->debugmsg("  Lyckades stoppa in '$solotag' som en tag med id  '$tagid'");
                  } else {
                      //$tagid = $check['term_id'];
                      $this->debugmsg("error BEEP BEEP - could not create the tag '$tag'");
                      $this->debugmsg(print_r($check),1);
                  }
                  $this->debugmsg("$tagid");
              } else {
                  $tagid = $check['term_id'];
                  $this->debugmsg("  '$solotag' finns redan som tag med id   '$tagid'");
              }
              
              $this->debugmsg("Tagid: '$tagid' '$taxonomy'");
              $this->debugmsg("Taggar ihop objekt '$id' med tag '$tagid' med taxonomy '$taxonomy'");
              $blah = array($solotag);
              
              $this->debugmsg(print_r($blah,1));
              $ret = wp_set_object_terms($id, $blah, $taxonomy, true);
              if (is_wp_error($ret)) {
                  $this->debugmsg("ERRROR: " . $return ->get_error_message());;
                  $this->debugmsg(print_r($ret),1);
              } else {
                  $this->debugmsg("OK?");
                  $this->debugmsg(print_r($ret),1);
              }
          }
      }
  }
  
}
?>