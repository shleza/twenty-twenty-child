<?php
//child theme
add_action( 'wp_enqueue_scripts', 'enqueue_parent_styles' );

function enqueue_parent_styles() {
   wp_enqueue_style( 'parent-style', get_template_directory_uri().'/style.css' );
}

//disable wp admin bar for specific user
add_action('after_setup_theme', 'remove_admin_bar');
function remove_admin_bar() {
	$user = wp_get_current_user();

	if ( $user->user_login == 'wp-test' ) {
        show_admin_bar(false);
    }
}


//Products custom post type 
function create_post_type() {
    register_post_type( 'products',
        array(
            'labels' => array(
                'name' => __( 'Products' ),
                'singular_name' => __( 'Product' )
            ),
            'public' => true,
            'has_archive' => true,
            'supports' => array( 'title', 'editor', 'custom-fields' )
        )
    );
}
add_action( 'init', 'create_post_type' );
flush_rewrite_rules( false );


//json api
add_action('rest_api_init', function () {
  register_rest_route( 'twentytwentychild/v1', 'p/(?P<category_id>\d+)',array(
                'methods'  => 'GET',
                'callback' => 'get_products_by_category'
      ));
});

function get_products_by_category($request) {

    $posts = array();
	$products = get_posts([
		'post_type' => 'products',
		'post_status' => 'publish',
		'numberposts' => -1,
		'order'    => 'ASC'
	]);
	
	foreach($products as $product)
	{
		$cats = get_field('category', $product->ID);
		if( in_array($request['category_id'], $cats) )
		{
			$image = get_field('main_image', $product->ID);
			$posts[] = array(
				'title' 		=> 	$product->post_title,
				'description'	=>	get_field('description', $product->ID),
				'image'			=>	esc_url($image['url']),
				'price'			=>	get_field('price', $product->ID),
				'is_on_sale'	=>	get_field('is_on_sale', $product->ID),
				'sale_price'	=>	get_field('sale_price', $product->ID)
			);			
		}
	}

    if (empty($posts)) {
		return new WP_Error( 'empty_category', 'there is no products in this category', array('status' => 404) );
    }

    $response = new WP_REST_Response($posts);
    $response->set_status(200);

    return json_encode($arr);
}

//related products
function related_products($pid){
	
	$related = array();
	$categories = get_field('category', $pid);
	$products = get_posts([
	  'post_type' => 'products',
	  'post_status' => 'publish',
	  'numberposts' => -1,
	   'order'    => 'ASC'
	]);
	
	foreach($products as $product)
	{
		if( $product->ID == $pid )
			continue;
		
		$cats = get_field('category', $product->ID);
		foreach($categories as $category)
		{
			if( in_array($category, $cats) )
			{
				$image = get_field('main_image', $product->ID);
				$related[$product->ID] = array(
					'title'		=>	$product->post_title,
					'thumbnail'	=>	$image['url'],	
					'permalink'	=>	get_permalink($product->ID)
				);
				break;
			}
		}
	}
	
	return $related;
}

//product shortcode
function product_shortcode($atts) {
	$output = '';
   $a = shortcode_atts( array(
		'product_id'	=>	get_the_ID(),
		'bg_color' 		=> 'yellow'
   ), $atts );
   
   if( get_post_type($a['product_id']) != 'products' )
	   return $output;
   
   $image = get_field('main_image', $a['product_id']);
   $output = "<div class='row' style='background-color: ".$a['bg_color']."'><div class='column'><div class='main_image'>";
   $output .= "<a target='_blank' href='".get_permalink($a['product_id'])."'>";
   $output .= "<img src='".esc_url($image['url'])."' ></a></div>";
   $output .= "<h4>".get_the_title($a['product_id'])."</h4><span>price: ".get_field('price', $a['product_id'])."<span></div></div>";
   
   return $output;
}

add_shortcode( 'productshortcode', 'product_shortcode' );
?>