<?php 
/*
Plugin Name: ALT Lab FAQ
Plugin URI:  https://github.com/
Description: For stuff that's magical
Version:     1.0
Author:      ALT Lab
Author URI:  http://altlab.vcu.edu
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Domain Path: /languages
Text Domain: my-toolset

*/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );


add_action('wp_enqueue_scripts', 'alt_faq_load_scripts');

function alt_faq_load_scripts() {                           
    $deps = array('jquery');
    $version= '1.0'; 
    $in_footer = true;    
    wp_enqueue_script('alt-lab-faq-main-js', plugin_dir_url( __FILE__) . 'js/alt-lab-faq-main.js', $deps, $version, $in_footer); 
    wp_enqueue_style( 'alt-lab-faq-main-css', plugin_dir_url( __FILE__) . 'css/alt-lab-faq-main.css');
}


add_filter( 'the_content', 'alt_faq_make_faq', 1 );


function alt_faq_make_faq($content){
    global $post;
    $html = '';
     // Check if we're inside the main loop in a single Post.
    if ( is_singular() && in_the_loop() && is_main_query() && get_field( 'faq', $post->ID )) {          
        remove_filter( 'the_content', 'wpautop' );

        $html .= '<div class="accordion" id="faq_block">';
         while( the_repeater_field('faq', $post->ID) ){
            $question = get_sub_field('question', $post->ID);
            $answer = get_sub_field('answer', $post->ID);
            $id = get_row_index($post->ID);

            $html .= "<div class='card'>
                        <div class='card-header' id='heading_{$id}'>
                          <h2 class='mb-0'>
                            <button class='btn btn-link' type='button' data-toggle='collapse' data-target='#faq_{$id}' aria-expanded='false' aria-controls='faq_{$id}'>
                              {$question}
                             <!--QUESTION-->
                            </button>
                          </h2>
                        </div>

                        <div id='faq_{$id}' class='collapse' aria-labelledby='heading_{$id}' data-parent='#faq_block'>
                          <div class='card-body'>
                            {$answer}
                          <!--ANSWER-->
                         </div>
                      </div>
                  </div>";      

         }
            
                 
        $html .= '</div>';
        return $content . $html;
    }
 
    return $content;
}



//save acf json
add_filter('acf/settings/save_json', 'alt_faq_json_save_point');
 
function alt_faq_json_save_point( $path ) {
    
    // update path
    $path = plugin_dir_path(__FILE__) . '/acf-json';
    var_dump($path);
    
    // return
    return $path;
    
}


// load acf json
add_filter('acf/settings/load_json', 'alt_faq_json_load_point');

function alt_faq_json_load_point( $paths ) {
    
    // remove original path (optional)
    unset($paths[0]);
    
    
    // append path
    $paths[] = plugin_dir_path(__FILE__)  . '/acf-json';
    
    
    // return
    return $paths;
    
}


//LOGGER -- like frogger but more useful

if ( ! function_exists('write_log')) {
   function write_log ( $log )  {
      if ( is_array( $log ) || is_object( $log ) ) {
         error_log( print_r( $log, true ) );
      } else {
         error_log( $log );
      }
   }
}

  //print("<pre>".print_r($a,true)."</pre>");


//FAQ custom post type

// Register Custom Post Type FAQ
// Post Type Key: FAQ

function create_FAQ_cpt() {

  $labels = array(
    'name' => __( 'FAQs', 'Post Type General Name', 'textdomain' ),
    'singular_name' => __( 'FAQ', 'Post Type Singular Name', 'textdomain' ),
    'menu_name' => __( 'FAQ', 'textdomain' ),
    'name_admin_bar' => __( 'FAQ', 'textdomain' ),
    'archives' => __( 'FAQ Archives', 'textdomain' ),
    'attributes' => __( 'FAQ Attributes', 'textdomain' ),
    'parent_item_colon' => __( 'FAQ:', 'textdomain' ),
    'all_items' => __( 'All FAQs', 'textdomain' ),
    'add_new_item' => __( 'Add New FAQ', 'textdomain' ),
    'add_new' => __( 'Add New', 'textdomain' ),
    'new_item' => __( 'New FAQ', 'textdomain' ),
    'edit_item' => __( 'Edit FAQ', 'textdomain' ),
    'update_item' => __( 'Update FAQ', 'textdomain' ),
    'view_item' => __( 'View FAQ', 'textdomain' ),
    'view_items' => __( 'View FAQs', 'textdomain' ),
    'search_items' => __( 'Search FAQs', 'textdomain' ),
    'not_found' => __( 'Not found', 'textdomain' ),
    'not_found_in_trash' => __( 'Not found in Trash', 'textdomain' ),
    'featured_image' => __( 'Featured Image', 'textdomain' ),
    'set_featured_image' => __( 'Set featured image', 'textdomain' ),
    'remove_featured_image' => __( 'Remove featured image', 'textdomain' ),
    'use_featured_image' => __( 'Use as featured image', 'textdomain' ),
    'insert_into_item' => __( 'Insert into FAQ', 'textdomain' ),
    'uploaded_to_this_item' => __( 'Uploaded to this FAQ', 'textdomain' ),
    'items_list' => __( 'FAQ list', 'textdomain' ),
    'items_list_navigation' => __( 'FAQ list navigation', 'textdomain' ),
    'filter_items_list' => __( 'Filter FAQ list', 'textdomain' ),
  );
  $args = array(
    'label' => __( 'FAQ', 'textdomain' ),
    'description' => __( '', 'textdomain' ),
    'labels' => $labels,
    'menu_icon' => '',
    'supports' => array('title', 'editor', 'revisions', 'author', 'trackbacks', 'custom-fields', 'thumbnail',),
    'taxonomies' => array('category', 'post_tag'),
    'public' => true,
    'show_ui' => true,
    'show_in_menu' => true,
    'menu_position' => 5,
    'show_in_admin_bar' => true,
    'show_in_nav_menus' => true,
    'can_export' => true,
    'has_archive' => true,
    'hierarchical' => false,
    'exclude_from_search' => false,
    'show_in_rest' => true,
    'publicly_queryable' => true,
    'capability_type' => 'post',
    'menu_icon' => 'dashicons-universal-access-alt',
  );
  register_post_type( 'FAQ', $args );
  
  // flush rewrite rules because we changed the permalink structure
  global $wp_rewrite;
  $wp_rewrite->flush_rules();
}
add_action( 'init', 'create_FAQ_cpt', 0 );
