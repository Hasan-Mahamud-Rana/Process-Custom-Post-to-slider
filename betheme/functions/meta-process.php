<?php
if( ! function_exists( 'mfn_process_post_type' ) )
{
	function mfn_process_post_type()
	{
		$process_item_slug = mfn_opts_get( 'process-slug', 'process-item' );
		$labels = array(
			'name' => __('Process','mfn-opts'),
			'singular_name' => __('process','mfn-opts'),
			'add_new' => __('Add New','mfn-opts'),
			'add_new_item' => __('Add New process','mfn-opts'),
			'edit_item' => __('Edit process','mfn-opts'),
			'new_item' => __('New process','mfn-opts'),
			'view_item' => __('View processs','mfn-opts'),
			'search_items' => __('Search processs','mfn-opts'),
			'not_found' => __('No processs found','mfn-opts'),
			'not_found_in_trash' => __('No processs found in Trash','mfn-opts'),
			'parent_item_colon' => ''
		);
		$args = array(
			'labels' => $labels,
			'menu_icon'	=> 'dashicons-businessman',
			'query_var' => true,
			'capability_type' => 'post',
			'hierarchical' => false,
			//'menu_position' => null,
			'public' => false,
			'publicly_queryable' => true,
			'show_ui' => true,
			'exclude_from_search' => true,
			'show_in_nav_menus' => false,
			'has_archive' => false,
			'rewrite' => false,
			'taxonomies'         => array('category'),
			//'rewrite' => array( 'slug' => $process_item_slug, 'with_front'=>true ),
			'supports' => array( 'title', 'editor', 'author', 'thumbnail','revisions' ),
		);
		register_post_type( 'process', $args );
	}
}
add_action( 'init', 'mfn_process_post_type' );

add_shortcode('query', 'shortcode_query');

function shortcode_query($atts, $content){
  extract(shortcode_atts(array( // a few default values
   'posts_per_page' => '10',
   'caller_get_posts' => 1,
   'category_name'=> 'test',
   'post__not_in' => get_option('sticky_posts'),
  ), $atts));

  global $post;

  $posts = new WP_Query($atts);
  $output = '';
  if ($posts->have_posts())
    while ($posts->have_posts()):
      $posts->the_post();

      // these arguments will be available from inside $content
      $parameters = array(
        'PERMALINK' => get_permalink(),
        'TITLE' => get_the_title(),
				'THUMB' => wp_get_attachment_url( get_post_thumbnail_id($post->ID) ),
        'CONTENT' => get_the_content(),
        'COMMENT_COUNT' => $post->comment_count,
        'CATEGORIES' => get_the_category_list(', '),
        // add here more...
      );

      $finds = $replaces = array();
      foreach($parameters as $find => $replace):
        $finds[] = '{'.$find.'}';
        $replaces[] = $replace;
      endforeach;
      $output .= str_replace($finds, $replaces, $content);

    endwhile;
  else
    return; // no posts found

  wp_reset_query();
  return html_entity_decode($output);
}

function slick_scripts() {
  wp_enqueue_script( 'slick-js', get_template_directory_uri() . '/assets/slick/slick.min.js', array( 'jquery' ), '', true );
  wp_enqueue_style( 'slick', get_template_directory_uri() . '/assets/slick/slick.css');
  wp_enqueue_style( 'slick-theme', get_template_directory_uri() . '/assets/slick/slick-theme.css');
}
add_action('wp_enqueue_scripts', 'slick_scripts', 999);