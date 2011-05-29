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


  function find_tagged_post($tag, $value)
  {
      $superid = null;
      query_posts(array('posts_per_page' => '1', 'post_type' => 'product', 'meta_key' => $tag, 'meta_value' => $value, 'orderby' => 'meta_value_num', 'order' => 'DESC'));
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
                  print "Tag '$tag' ($termid) is already connected to Page $postid<br>\n";
                  return true;
              }
          }
      } else {
          print "taggen '$tag' finns inte\n";
          return false;
      }
  }
  
  function write_tags($id, $tags, $taxonomy)
  {
      $tags = explode(',', $tags);
      foreach ($tags as $solotag) {
          $solotag = trim($solotag);
          print "Solotag $solotag <br>\n";
          if (tagConnected($solotag, $id)) {
              print "- PASSIVE: '$solotag' is already connected with '$id'<br>\n";
          } else {
              print "- ACTIVE: '$solotag' is NOT already connected with '$id'<br>\n";
              print "  Testar $solotag<br>\n";
              $check = is_term($solotag, $taxonomy);
              if (is_null($check)) {
                  print "  '$solotag' finns inte som solotag'\n<br>";
                  $tag = wp_insert_term($solotag, $taxonomy);
                  if (!is_wp_error($tag)) {
                      $tagid = $tag['term_id'];
                      print "  Lyckades stoppa in '$solotag' som en tag med id  '$tagid'\n<br>";
                  } else {
                      //$tagid = $check['term_id'];
                      print "error BEEP BEEP - could not create the tag '$tag' \n";
                      print_r($check);
                  }
                  print "$tagid\n";
              } else {
                  $tagid = $check['term_id'];
                  print "  '$solotag' finns redan som tag med id   '$tagid'\n<br>";
              }
              
              print "Tagid: '$tagid' '$taxonomy'<br>\n";
              print "Taggar ihop objekt '$id' med tag '$tagid' med taxonomy '$taxonomy'<br>\n";
              $blah = array($solotag);
              
              print_r($blah);
              $ret = wp_set_object_terms($id, $blah, $taxonomy, true);
              if (is_wp_error($ret)) {
                  echo "ERRROR: " . $return ->get_error_message() . "<br>\n";
                  print_r($ret);
              } else {
                  echo "OK? \n<br>";
                  print_r($ret);
              }
          }
      }
  }
?>