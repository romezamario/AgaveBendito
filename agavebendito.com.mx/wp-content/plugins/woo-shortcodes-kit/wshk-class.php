<?php

/*
 * Woo Shortcodes Kit
 * @get_cf7as_sidebar_options()
 * @get_cf7as_sidebar_content()
 * */
 if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
 
global $pluginOptionsVal;
$pluginOptionsVal=get_wshk_sidebar_options();


/* WOO GLOBAL ORDERS/ DOWNLOADS COUNTER */
// If you want to show the global orders/downloads count on any page or post, use this Shortcode: [woo_global_sales]

if(isset($pluginOptionsVal['wshk_enablethetotsalessht']) && $pluginOptionsVal['wshk_enablethetotsalessht']==2008)
{

function wshk_my_global_sales() {

global $wpdb;

$order_totals = apply_filters( 'woocommerce_reports_sales_overview_order_totals', $wpdb->get_row( "

SELECT SUM(meta.meta_value) AS total_sales, COUNT(posts.ID) AS total_orders FROM {$wpdb->posts} AS posts

LEFT JOIN {$wpdb->postmeta} AS meta ON posts.ID = meta.post_id

WHERE meta.meta_key = '_order_total'

AND posts.post_type = 'shop_order'

AND posts.post_status IN ( '" . implode( "','", array( 'wc-completed', 'wc-processing', 'wc-on-hold' ) ) . "' )

" ) );

return absint( $order_totals->total_orders);

}
add_shortcode('woo_global_sales', 'wshk_my_global_sales');
}


/* INDIVIDUAL PRODUCT SALES/DOWNLOADS COUNT FUNCTION*/ 
// If you want to show the invididual product sales/downloads with a  automatic counter just need a clic

	function get_wshk_sidebar_options() {
		global $wpdb;
		$ctOptions = $wpdb->get_results("SELECT option_name, option_value FROM $wpdb->options WHERE option_name LIKE 'wshk_%'");				
		foreach ($ctOptions as $option) {
			$ctOptions[$option->option_name] =  $option->option_value;
		}
		return $ctOptions;	
	}
	// Get plugin options
    
global $pluginOptionsVal;
$pluginOptionsVal=get_wshk_sidebar_options();

/** Check if is active */

if(isset($pluginOptionsVal['wshk_enable']) && $pluginOptionsVal['wshk_enable']==1)
{
	/* Start Sales Count Code */

  if(!function_exists('wshk_product_sold_count')):
	add_action( 'woocommerce_single_product_summary', 'wshk_product_sold_count', 11 );
	add_action( 'woocommerce_after_shop_loop_item', 'wshk_product_sold_count', 11 );
function wshk_product_sold_count() {
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $post, $woocommerce, $product;

if ($product->is_downloadable('yes')) {
    
    // It will happen if the product is downloable.

		global $product;
		$pluginOptionsVal=get_wshk_sidebar_options();
		if(isset($pluginOptionsVal['wshk_text']) && $pluginOptionsVal['wshk_text']!='')
		{
			$salesTxt=$pluginOptionsVal['wshk_text'];
			}else {
				$salesTxt="Downloads";
				}
		$units_sold = get_post_meta( $product->id, 'total_sales', true );

    //Since v.1.4
		
		if($units_sold >= $pluginOptionsVal['wshk_min']){
		echo '<p class="wshk">' . sprintf( __( '<span class="wshk-count">%s</span> <span class="wshk-text">%s</span>', 'woocommerce' ), $units_sold,$salesTxt ) . '</p>';}
		
	} else {
	    
	    // It will happen if the product is not downloable
	    
	global $product;
		$pluginOptionsVal=get_wshk_sidebar_options();
	if(isset($pluginOptionsVal['wshk_textsales']) && $pluginOptionsVal['wshk_textsales']!='')
		{
			$saleTxt=$pluginOptionsVal['wshk_textsales'];
			}else {
				$saleTxt="Sales";
				}
				$units_sold = get_post_meta( $product->id, 'total_sales', true );

    //Since v.1.4
		
		if($units_sold >= $pluginOptionsVal['wshk_minsales']){
		echo '<p class="wshk">' . sprintf( __( '<span class="wshk-count">%s</span> <span class="wshk-text">%s</span>', 'woocommerce' ), $units_sold,$saleTxt ) . '</p>';}
	}
	} 
  endif;
  
  add_action('wp_head','add_wshk_inline_style');

	/** Default Counter CSS */
	if(!function_exists('add_wshk_inline_style')):
	function add_wshk_inline_style()
	{
		$pluginOptionsVal=get_wshk_sidebar_options();
		$wshk_style='<style>'.$pluginOptionsVal['wshk-inlinecss'].'</style>';
		print $wshk_style;
		}
	endif;
}

//Since v.1.3

/*ADD PRODUCT IMAGE IN ORDER EMAIL*/
//If you want show the product image in the Order email, just enable this function.

global $pluginOptionsVal;
$pluginOptionsVal=get_wshk_sidebar_options();

if(isset($pluginOptionsVal['wshk_test']) && $pluginOptionsVal['wshk_test']==2)
{
add_filter( 'woocommerce_email_order_items_args', 'wshk_woocommerce_email_order_items_args', 10, 1 );
 
function wshk_woocommerce_email_order_items_args( $args ) {
$emailordersizes = get_option('wshk_emailordersizes');
 
    $args['show_image'] = true;
    $args['image_size'] = array( $emailordersizes, $emailordersizes );
 
    return $args;
 
}
}


/* WOO TOTAL PRODUCT COUNTER */

//Updated v.1.7.9

//If you want to show total products on any page or post, use this Shortcode: [woo_total_product_count] and if you want exclude any category from the total count just add [woo_total_product_count cat_id="Here write the category ID number"]

if(isset($pluginOptionsVal['wshk_enablethetotprosht']) && $pluginOptionsVal['wshk_enablethetotprosht']==2009)
{

function wshk_product_count_shortcode( $atts ) {
    ob_start();
extract( shortcode_atts( array(
        'product_count' => 0
    ), $atts ) );

    $data = shortcode_atts( array(
        'cat_id'    => '',
        'taxonomy'  => 'product_cat'
    ), $atts );
    
    // loop through all categories to collect the count.
   foreach (get_terms('product_cat') as $term)
      $product_count += $term->count;

   //Since v.1.3

    $category = get_term( $data['cat_id'], $data['taxonomy'] );
    $count = $category->count;
    $count_posts = wp_count_posts( 'product' );
    return (int)$count_posts->publish - (int)$count;
    return ob_get_clean();
}
add_shortcode( 'woo_total_product_count', 'wshk_product_count_shortcode' );
}



//Since v.1.4

/*PRODUCT PER PAGE*/
//if you want manage the product per page to display in shop page, just enable the function and write the number of products to display (Write -1 to show all product in the same page)

global $pluginOptionsVal;
$pluginOptionsVal=get_wshk_sidebar_options();


if(isset($pluginOptionsVal['wshk_perpage']) && $pluginOptionsVal['wshk_perpage']==3)
{


//Updated v.1.7.8

$theme = wp_get_theme(); // gets the current theme
if ( 'Divi' == $theme->name || 'Divi' == $theme->parent_theme ) {
    // if you're here Divi is the active theme or is
    // the current theme's parent theme
    
    add_filter( 'option_et_divi', function( $option ){
	$option['divi_woocommerce_archive_num_posts'] = get_option("wshk_nperpage");
	return $option;
} );
    
} else {

function wshk_loop_shop_per_page( $cols ) {
  // $cols contains the current number of products per page based on the value stored on Options -> Reading
  // Return the number of products you wanna show per page.
  $cols = get_option("wshk_nperpage");
  return $cols;
}

add_filter( 'loop_shop_per_page', 'wshk_loop_shop_per_page', 20 );
//add_filter( 'loop_shop_per_page', create_function( '$cols', 'return get_option("wshk_nperpage");' ), 20 );
}
}

//Since v.1.5

/*SHOW SPECIFIC CATEGORIES IN SHOP PAGE*/
//if you want display only specifics categories in the shop page, just write the slug of each category
global $pluginOptionsVal;
$pluginOptionsVal=get_wshk_sidebar_options();


if(isset($pluginOptionsVal['wshk_enablecat']) && $pluginOptionsVal['wshk_enablecat']==4)
{


function wshk_specifics_categories( $q ) {

//Since 1.6.2 - To fix the problem was hide the products in categories pages.

 if ( ! is_admin() && is_shop() ){

$cat1 = get_option('wshk_firstcat');
$cat2 = get_option('wshk_secondcat');
$cat3 = get_option('wshk_thirdcat');
    $tax_query = (array) $q->get( 'tax_query' );

    $tax_query[] = array(
           'taxonomy' => 'product_cat',
           'field' => 'slug',
           'terms' => array( $cat1, $cat2, $cat3 ), // Display only products of these categories on the shop page.
           'operator' => 'IN'
    );


    $q->set( 'tax_query', $tax_query );
}
}
add_action( 'woocommerce_product_query', 'wshk_specifics_categories' );
}



//Since v.1.6.4

/*EXCLUDE PRODUCTS OF SPECIFIC CATEGORIES IN THE SHOP PAGE*/
//If you want exclude products of some categories, just need enable this function and write the category slug to exlude in each field. You can exclude 3 categories how much by now.

global $pluginOptionsVal;
$pluginOptionsVal=get_wshk_sidebar_options();


if(isset($pluginOptionsVal['wshk_excludecat']) && $pluginOptionsVal['wshk_excludecat']==16)
{
    
function wshk_exclude_categories( $q ) {
    if ( ! is_admin() && is_shop() ){ 
$excat1 = get_option('wshk_exfirstcat');
$excat2 = get_option('wshk_exsecondcat');
$excat3 = get_option('wshk_exthirdcat');

    $tax_query = (array) $q->get( 'tax_query' );

    $tax_query[] = array(
           'taxonomy' => 'product_cat',
           'field' => 'slug',
           'terms' => array( $excat1, $excat2, $excat3 ),
           'operator' => 'NOT IN'
    );


    $q->set( 'tax_query', $tax_query );

}
}
add_action( 'woocommerce_product_query', 'wshk_exclude_categories' );
}



//Since v.1.5 - Updated in v.1.7.9

/*PRODUCTS BOUGHT BY A USER*/
//if you want display which products has bought a user, use this Shortcode: [woo_bought_products]

if(isset($pluginOptionsVal['wshk_enabletheboughtsht']) && $pluginOptionsVal['wshk_enabletheboughtsht']==2010)
{

add_shortcode( 'woo_bought_products', 'wshk_user_products_bought' );
 
function wshk_user_products_bought() {
global $product, $woocommerce, $woocommerce_loop;
    $columns = 3;
 
    // GET USER
    $current_user = wp_get_current_user();
 
    // GET USER ORDERS (COMPLETED + PROCESSING)
    $customer_orders = get_posts( array(
        'numberposts' => -1,
        'meta_key'    => '_customer_user',
        'meta_value'  => $current_user->ID,
        'post_type'   => wc_get_order_types(),
        'post_status' => array_keys( wc_get_order_statuses() ),
    ) );
 
    // LOOP THROUGH ORDERS AND GET PRODUCT IDS
    if ( ! $customer_orders ) return;
    $product_ids = array();
    foreach ( $customer_orders as $customer_order ) {
        $order = new WC_Order( $customer_order->ID );
        $items = $order->get_items();
        foreach ( $items as $item ) {
            $product_id = $item->get_product_id();
            $product_ids[] = $product_id;
        }
    }
    $product_ids = array_unique( $product_ids );
 
    // QUERY PRODUCTS
    $args = array(
       'post_type' => 'product',
       'post__in' => $product_ids,
    );
    $loop = new WP_Query( $args );
 
    // GENERATE WC LOOP
    ob_start();
    woocommerce_product_loop_start();
    while ( $loop->have_posts() ) : $loop->the_post();
    wc_get_template_part( 'content', 'product' ); 
    endwhile; 
    woocommerce_product_loop_end();
    woocommerce_reset_loop();
    wp_reset_postdata();

    //DISPLAY CONTENT
       
      return '<div class="woocommerce columns-' . $columns . '">' . ob_get_clean() . '</div>';
   
}

}




//Since v.1.5 -Updated v.1.8.0

/*SHOW GRAVATAR USER IMAGE*/
//Display the user's Gravatar image, if you want show the Gravata'r image in any page or post, use this shortcode [woo_gravatar_image]

global $pluginOptionsVal;
$pluginOptionsVal=get_wshk_sidebar_options();


if(isset($pluginOptionsVal['wshk_enablegravatar']) && $pluginOptionsVal['wshk_enablegravatar']==15)
{

function wshk_gravatar_image(){
$textgravasize = get_option('wshk_textgravasize');
$textgravashd = get_option('wshk_textgravashd');
$textgravabdsz = get_option('wshk_textgravabdsz');
$textgravabdtp = get_option('wshk_textgravabdtp');
$textgravabdcl = get_option('wshk_textgravabdcl');
$textgravabdrd = get_option('wshk_textgravabdrd');

$user_id = get_current_user_id();

ob_start();
$id_or_email = wp_get_current_user();

//Since v.1.7.9
//Updated styles compatibility with builders

echo '<style> img.avatar.avatar-'.$textgravasize.'.photo { height: '.$textgravasize.'px;
  width: '.$textgravasize.'px;
  border: '.$textgravabdsz.'px '.$textgravabdtp.' '.$textgravabdcl.' !important;  
  border-radius: '.$textgravabdrd.'% !important;
  box-shadow: '.$textgravashd.';
  overflow: hidden;
  margin: auto;}</style>';
echo get_avatar( $id_or_email, $textgravasize, '', '', '' );
return ob_get_clean();
}
}
add_shortcode( 'woo_gravatar_image', 'wshk_gravatar_image' );





//Since v.1.5

/* CHANGE ADD TO CART TEXT BUTTON*/
// The button's text will change in the single product shop page loop & single product summary, when the user have purchase the product. Just need Enable the function and write the text to show.

global $pluginOptionsVal;
$pluginOptionsVal=get_wshk_sidebar_options();


if(isset($pluginOptionsVal['wshk_enablebought']) && $pluginOptionsVal['wshk_enablebought']==5)
{


add_filter('woocommerce_loop_add_to_cart_link','wshk_add_to_cart_link_customer_has_bought');
//add_filter( 'woocommerce_product_single_add_to_cart_text', 'wshk_add_to_cart_link_customer_has_bought' );

    function wshk_add_to_cart_link_customer_has_bought() {

        global $product;

        if( empty( $product->id ) ){

            $wc_pf = new WC_Product_Factory();
            $product = $wc_pf->get_product( $id );

        }

        $current_user = wp_get_current_user();

        if( wc_customer_bought_product( $current_user->user_email, $current_user->ID, $product->id ) ){

            $product_url = get_permalink();
            $textbutton = get_option('wshk_buttontext');
            $button_label =  $textbutton;  

        } else {

            $product_url =  $product->add_to_cart_url();  
            $button_label = $product->add_to_cart_text();

        };
/*OLD class = single_%s button product_type_simple add_to_cart_button ajax_add_to_cart single_add_to_cart_button button alt*/
        echo sprintf( '<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" data-quantity="%s" class=" single_%s button product_type_simple ajax_add_to_cart" style="text-decoration:none;">%s</a>',       
            esc_url( $product_url ),
            esc_attr( $product->id ),
            esc_attr( $product->get_sku() ),
            esc_attr( isset( $quantity ) ? $quantity : 1 ),
            $product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
            //esc_attr( $product->product_type ),
            esc_html( $button_label )
        );

    }
    
    
    
/*Compatible with change ADD TO CART text function*/
//Since v.1.7.9
function wshk_compatibles(){
    
    //Check if change add to cart text function is enabled
global $pluginOptionsVal;
$pluginOptionsVal=get_wshk_sidebar_options();
if(isset($pluginOptionsVal['wshk_enableaddtocarttxt']) && $pluginOptionsVal['wshk_enableaddtocarttxt']==14)
{
    //Do nothing
   
} else {
       global $product;
       global $post;

        if( empty( $product->id ) ){

            $wc_pf = new WC_Product_Factory();
            $product = $wc_pf->get_product( $id );

        }

        $current_user = wp_get_current_user();

        if( wc_customer_bought_product( $current_user->user_email, $current_user->ID, $product->id ) ){

            $product_url = get_permalink();
            $textbutton = get_option('wshk_buttontext');
            $button_label =  $textbutton;  

        } else {

            $product_url =  $product->add_to_cart_url();  
            $button_label = $product->add_to_cart_text();

        };

/*Check if is external product*/

 if( $product->is_type('external') ){
    
  return $button_label = $product->add_to_cart_text();
  
 } else {

        echo sprintf( '<span style="text-decoration:none;">' .$button_label. '</span>'
        );
 };  // FIN external product check
}; // FIN compatibilidad

}// Fin funcion principal

add_filter( 'woocommerce_product_single_add_to_cart_text', 'wshk_compatibles' );
    
    } // FIN condicional
    

//Since v.1.5    

/*HOW MUCH PRODUCTS BOUGHT A USER (NUMBER ONLY)*/
//With a shortcode you can show the number of products that a user bought. If you want show in any page or post, use this shortcode : [woo_total_bought_products]



global $pluginOptionsVal;
$pluginOptionsVal=get_wshk_sidebar_options();


if(isset($pluginOptionsVal['wshk_enablectbp']) && $pluginOptionsVal['wshk_enablectbp']==6)
{

   add_shortcode( 'woo_total_bought_products', 'wshk_current_customer_month_count' );
function wshk_current_customer_month_count( $user_id=null ) {
    if ( empty($user_id) ){
        $user_id = get_current_user_id();
    }
    // Date calculations to limit the query
    $today_year = date( 'Y' );
    $today_month = date( 'm' );
    $day = date( 'd' );
    if ($today_month == '01') {
        $month = '12';
        $year = $today_year - 1;
    } else{
        $month = $today_month - 1;
        $month = sprintf("%02d", $month);
        $year = $today_year - 1;
    }

    // ORDERS FOR LAST 30 DAYS (Time calculations)
    $now = strtotime('now');
    // Set the gap time (here 30 days)
    $gap_days = 30;
    $gap_days_in_seconds = 60*60*24*$gap_days;
    $gap_time = $now - $gap_days_in_seconds;

    // The query arguments
    $args = array(
        // WC orders post type
        'post_type'   => 'shop_order',        
        // Only orders with status "completed" (others common status: 'wc-on-hold' or 'wc-processing')
        'post_status' => 'wc-completed', 
        // all posts
        'numberposts' => -1,
        // for current user id
        'meta_key'    => '_customer_user',
        'meta_value'  => $user_id,
        'date_query' => array(
            //orders published on last 30 days
            'relation' => 'OR',
            array(
                'year' => $today_year,
                'month' => $today_month,
            ),
            array(
                'year' => $year,
                'month' => $month,
            ),
        ),
    );

    // Get all customer products
    $customer_orders = get_posts( $args );
    $textprefix = get_option('wshk_textprefix');
    $textsuffix = get_option('wshk_textsuffix');
    $textpsuffix = get_option('wshk_textpsuffix');
    $textnobp = get_option('wshk_textnobp');
    $aligntheproducts = get_option('wshk_aligntheproducts');
    $caunt = 1;
    $count = 0;
    
     global $pluginOptionsVal;
$pluginOptionsVal=get_wshk_sidebar_options();

if(isset($pluginOptionsVal['wshk_yesenabletwo']) && $pluginOptionsVal['wshk_yesenabletwo']=='wshk_yesenabletwo')
{
  ob_start();
  }
  else if(isset($pluginOptionsVal['wshk_nnoenabletwo']) && $pluginOptionsVal['wshk_nnoenabletwo']==wshk_nnoenabletwo) {
      //ob_start();
      
  }
    
    if (!empty($customer_orders)) {
        $customer_orders_date = array();
        // Going through each current customer orders
        foreach ( $customer_orders as $customer_order ){
            // Conveting order dates in seconds
            $customer_order_date = strtotime($customer_order->post_date);
            // Only past 30 days orders
            if ( $customer_order_date > $gap_time ) {
                $customer_order_date;
                $order = new WC_Order( $customer_order->ID );
                $order_items = $order->get_items();
                // Going through each current customer items in the order
                foreach ( $order_items as $order_item ){
                    $count++;
                }                
            } 
        }
        if ($count > $caunt){
        return '<p style="text-align:' . $aligntheproducts .';">' . $textprefix . ' ' . $count . ' ' . $textpsuffix . '</p>';
        }
    }
    if ($count == $caunt){
        echo '<p style="text-align:' . $aligntheproducts .';">' . $textprefix . ' ' . $count . ' ' . $textsuffix . '</p>';
        
        } else{
            echo '<p style="text-align:' . $aligntheproducts .';">' . $textnobp . '</p>' ;
            }
           
                global $pluginOptionsVal;
$pluginOptionsVal=get_wshk_sidebar_options();

if(isset($pluginOptionsVal['wshk_yesenabletwo']) && $pluginOptionsVal['wshk_yesenabletwo']==wshk_yesenabletwo)
{
  return ob_get_clean(); 
  }
  else if(isset($pluginOptionsVal['wshk_nnoenabletwo']) && $pluginOptionsVal['wshk_nnoenabletwo']==wshk_nnoenabletwo) {
      //return ob_get_clean(); 
      
  } 
            
           
}
}





//Since v.1.5

/*GET ALL ORDERS FOR A USER*/
//Show the total orders that a user have made, if you want display in any page or post, use this shortcode: [woo_customer_total_orders]


global $pluginOptionsVal;
$pluginOptionsVal=get_wshk_sidebar_options();

if(isset($pluginOptionsVal['wshk_enablectbo']) && $pluginOptionsVal['wshk_enablectbo']==7)
{

add_shortcode( 'woo_customer_total_orders', 'wshk_get_customer_total_orders' );
function wshk_get_customer_total_orders( $user_id=null ) {
        

    if ( empty($user_id) ){
        $user_id = get_current_user_id();
    }
    // Date calculations to limit the query
    $today_year = date( 'Y' );
    $today_month = date( 'm' );
    $day = date( 'd' );
    if ($today_month == '01') {
        $month = '12';
        $year = $today_year - 1;
    } else{
        $month = $today_month - 1;
        $month = sprintf("%02d", $month);
        $year = $today_year - 1;
    }

    // ORDERS FOR LAST 30 DAYS (Time calculations)
    $now = strtotime('now');
    // Set the gap time (here 30 days)
    $gap_days = 30;
    $gap_days_in_seconds = 60*60*24*$gap_days;
    $gap_time = $now - $gap_days_in_seconds;

    // The query arguments
    $args = array(
        // WC orders post type
        'post_type'   => 'shop_order',        
        // Only orders with status "completed" (others common status: 'wc-on-hold' or 'wc-processing')
        'post_status' => 'wc-completed', 
        // all posts
        'numberposts' => -1,
        // for current user id
        'meta_key'    => '_customer_user',
        'meta_value'  => $user_id,
        'date_query' => array(
            //orders published on last 30 days
            'relation' => 'OR',
            array(
                'year' => $today_year,
                'month' => $today_month,
            ),
            array(
                'year' => $year,
                'month' => $month,
            ),
        ),
    );
    
    
    global $pluginOptionsVal;
$pluginOptionsVal=get_wshk_sidebar_options();

if(isset($pluginOptionsVal['wshk_yesenable']) && $pluginOptionsVal['wshk_yesenable']=='wshk_yesenable')
{
  ob_start();
  }
  else if(isset($pluginOptionsVal['wshk_nnoenable']) && $pluginOptionsVal['wshk_nnoenable']=='wshk_nnoenable') {
      //ob_start();
      
  }
    // Get all customer orders
    $customer_orders = get_posts( $args );
    $tordersprefix = get_option('wshk_tordersprefix');
    $torderssuffix = get_option('wshk_torderssuffix');
    $torderspsuffix = get_option('wshk_torderspsuffix');
    $textnobo = get_option('wshk_textnobo');
    $aligntheorders = get_option('wshk_aligntheorders');
    $caunt = 1;
    $count = 0;
    
    

    if (!empty($customer_orders)) {
        $customer_orders_date = array();
        // Going through each current customer orders
        foreach ( $customer_orders as $customer_order ){
            // Conveting order dates in seconds
            $customer_order_date = strtotime($customer_order->post_date);
            // Only past 30 days orders
            if ( $customer_order_date > $gap_time ) {
                $customer_order_date;
                $order = new WC_Order( $customer_order->ID );
                
                    $count++;
                                
            } 
        }
        if ($count > $caunt){
        return '<p style="text-align:' . $aligntheorders .';">' .$tordersprefix . ' ' . $count . ' ' . $torderspsuffix . '</p>' ;
        }
    }
    if ($count == $caunt){
        echo '<p style="text-align:' . $aligntheorders .';">' . $tordersprefix . ' ' . $count . ' ' . $torderssuffix . '</p>' ;
        
        } else{
            echo '<p style="text-align:' . $aligntheorders .';">' . $textnobo . '</p>';
            }
           
           
            global $pluginOptionsVal;
$pluginOptionsVal=get_wshk_sidebar_options();

if(isset($pluginOptionsVal['wshk_yesenable']) && $pluginOptionsVal['wshk_yesenable']=='wshk_yesenable')
{
  return ob_get_clean();
    
} else if(isset($pluginOptionsVal['wshk_nnoenable']) && $pluginOptionsVal['wshk_nnoenable']=='wshk_nnoenable') {
    //return ob_get_clean();
}

    

}
}



//Since v.1.5

/*DISPLAY A MESSAGE IF HAVE MADE A NUMBER OF ORDERS*/
//Show a custom message if the customer has a number of orders made, if you want display in any page or post, use this shortcode: [woo_message]



function wshk_detect_customer_total_orders() {
    
    // Get all customer orders
    $customer_orders = get_posts( array(
        'numberposts' => -1,
        'meta_key'    => '_customer_user',
        'meta_value'  => get_current_user_id(),
        'post_type'   => wc_get_order_types(),
        'post_status' => array_keys( wc_get_order_statuses() ),
    ) );
    
    $customer = wp_get_current_user();
  
    // Order count for a "loyal" customer
    $setnumber =  get_option('wshk_wmorders');
    $textwmssg =  get_option('wshk_textwmssg');
    $textnonotice = get_option('wshk_nonotice');
    $textmorenotice = get_option('wshk_morenotice');
    $orders_count =  $setnumber;
    $descuento = 20;
    
    global $pluginOptionsVal;
$pluginOptionsVal=get_wshk_sidebar_options();
      if(isset($pluginOptionsVal['wshk_enablewmessage']) && $pluginOptionsVal['wshk_enablewmessage']==8)
{
    ob_start();
    // Check if the message is empty for don't display nothing
    $notice_text = sprintf( $textwmssg, $customer->display_name, $orders_count );
    if (empty($textwmssg)) {
    return false;
    }
     // Display this notice if the customer has less orders than the orders number selected in the plugin settings.
    if ( count( $customer_orders ) < $orders_count ) {
        echo $textnonotice;
    }
     // Display this notice if the customer has more orders than the orders number selected in the plugin settings.
    if ( count( $customer_orders ) > $orders_count ) {
        echo $textmorenotice;
    }
    // Display the message if the customer has the same number of orders than the orders number selected in the plugin settings.
    if ( count( $customer_orders ) == $orders_count ) {
        //wc_print_notice( $notice_text, 'notice' );
        echo $notice_text;
    }
    return ob_get_clean();
}
}
add_shortcode( 'woo_message', 'wshk_detect_customer_total_orders' );


//Since v.1.5 - Updated in v.1.8.0

/*SHOW COMMENTS BY A USER (Only products)*/
//Display all the products reviews made by a user with just a shortcode, If you want display in any page or post, use this shortcode [woo_review_products]

global $pluginOptionsVal;
$pluginOptionsVal=get_wshk_sidebar_options();
      if(isset($pluginOptionsVal['wshk_enablereviews']) && $pluginOptionsVal['wshk_enablereviews']==9)
{

function wshk_show_reviews_by_user(){
    
     
    ob_start();
    
    
$user_id = get_current_user_id();




$acreviews =  get_option('wshk_enablereviews');
$textavsize =  get_option('wshk_textavsize');
$textavbdsize = get_option('wshk_textavbdsize');
$textavbdradius = get_option('wshk_textavbdradius');
$textavbdtype = get_option('wshk_textavbdtype');
$textavbdcolor = get_option('wshk_textavbdcolor');
$texttbwsize = get_option('wshk_texttbwsize');
$textbxfsize =  get_option('wshk_textbxfsize');
$textbxbdsize = get_option('wshk_textbxbdsize');
$textbxbdradius = get_option('wshk_textbxbdradius');
$textbxbdtype = get_option('wshk_textbxbdtype');
$textbxbdcolor = get_option('wshk_textbxbdcolor');
$textbxbgcolor = get_option('wshk_textbxbgcolor');
$textbtnbdsize = get_option('wshk_textbtnbdsize');
$textbtnbdradius = get_option('wshk_textbtnbdradius');
$textbtnbdtype = get_option('wshk_textbtnbdtype');
$textbtnbdcolor = get_option('wshk_textbtnbdcolor');
$textbtntarget = get_option('wshk_textbtntarget');
$textbtntxd = get_option('wshk_textbtntxd');
$textbxpadding = get_option('wshk_textbxpadding');
$textbtntxt = get_option('wshk_textbtntxt');
$avshadow = get_option('wshk_avshadow');
    
    
    $id_or_email = get_current_user_id();
$numbrevdis = get_option('wshk_numbrevdis');
$count = 0;
$html_r = "";
$title="";
$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
$number = $numbrevdis ; 
$tesunoo = 1; //updated v.1.7.3
// all for show all the comments, for other quantity just write the number 1,2,3,4...
$offset = ( $paged - $tesunoo ) * $number; //updated v.1.7.3

global $product;
$args = array(
	'user_id' => $user_id, // get the user by ID
	'post_type' => 'product',
	'number' => $number,
    'offset' => $offset,
    'paged' => $paged,
	'post_ID' =>$product,  // Product Id  
	'meta_key' => '',
	'meta_value' => '',
	'status' => "approve", // Status you can also use 'hold', 'spam', 'trash'
);

$acreviews =  get_option('wshk_enablereviews');
$textavsize =  get_option('wshk_textavsize');
$textavbdsize = get_option('wshk_textavbdsize');
$textavbdradius = get_option('wshk_textavbdradius');
$textavbdtype = get_option('wshk_textavbdtype');
$textavbdcolor = get_option('wshk_textavbdcolor');
$texttbwsize = get_option('wshk_texttbwsize');
$textbxfsize =  get_option('wshk_textbxfsize');
$textbxbdsize = get_option('wshk_textbxbdsize');
$textbxbdradius = get_option('wshk_textbxbdradius');
$textbxbdtype = get_option('wshk_textbxbdtype');
$textbxbdcolor = get_option('wshk_textbxbdcolor');
$textbxbgcolor = get_option('wshk_textbxbgcolor');
$textbtnbdsize = get_option('wshk_textbtnbdsize');
$textbtnbdradius = get_option('wshk_textbtnbdradius');
$textbtnbdtype = get_option('wshk_textbtnbdtype');
$textbtnbdcolor = get_option('wshk_textbtnbdcolor');
$textbtntarget = get_option('wshk_textbtntarget');
$textbtntxd = get_option('wshk_textbtntxd');
$textbxpadding = get_option('wshk_textbxpadding');
$textbtntxt = get_option('wshk_textbtntxt');
$avshadow = get_option('wshk_avshadow');
    
$gravatar = get_avatar( $id_or_email, $textavsize) . ' ';
$url = home_url();
$comments = get_comments($args);
if (!empty ($comments)){
foreach($comments as $comment) :
?>
<style>
.mcon-image-container {
  height: <?php echo $textavsize ?>px !important;
  width: <?php echo $textavsize ?>px !important;
  border: <?php echo $textavbdsize ?>px <?php echo $textavbdtype ?> <?php echo $textavbdcolor ?> !important;  
  border-radius: <?php echo $textavbdradius ?>% !important;
  box-shadow: <?php echo $avshadow ?>;
    overflow: hidden;  
}

.wshk.star-rating {
    
    float:right;
}

ul.userreviewswshk {
    
    margin: 0px !important;
}

div.wshkreviewbox {
    
    padding-left:25px;
}

th.wshktableth {
    
    background-color:transparent;
}
</style>
<?php
$product = wc_get_product( $comment->comment_post_ID );
$teprodu = $product->get_name();
//Updated - v.1.7.9
echo('<div class="wshkreviewcontainer" style="background:' .$textbxbgcolor . '; font-size:' . $textbxfsize . 'px; border:' . $textbxbdsize . 'px' . ' ' . $textbxbdtype . ' ' . $textbxbdcolor . '; border-radius:' . $textbxbdradius . 'px; padding:' . $textbxpadding . 'px;">' . '<ul class="userreviewswshk"><table><tr><th class="wshktableth" style="width:' . $texttbwsize . 'px;"><div class="mcon-image-container">' . $gravatar . '</div></th><th class="wshktableth">' . '<div class="wshk star-rating" itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating"><span style="width:' . ( get_comment_meta( $comment->comment_ID, 'rating', true ) / 5 ) * 100 . '%"><strong itemprop="ratingValue">' . get_comment_meta( $comment->comment_ID, 'rating', true ) . '</strong></span></div><a href="' . $url . '/?p=' . $comment->comment_post_ID . '/#comments' . '">' . $teprodu . '</a><br /><strong>' . $comment->comment_author . '</strong><br /><small>' . get_comment_date( '', $comment) . '</small></th></tr></table><div class="wshkreviewbox">' . $comment->comment_content . '</div><br /><br />' . '<div class="wshkproductbuttonlink"><a class="woocommerce-Button button wshkcomment" target="' .$textbtntarget . '" style="border:' . $textbtnbdsize . 'px' . ' ' . $textbtnbdtype . ' ' . $textbtnbdcolor . '; border-radius:' . $textbtnbdradius . 'px; text-decoration:' . $textbtntxd . ';" href="' . $url . '/?p=' . $comment->comment_post_ID . '/#comments' . '">' . $textbtntxt . '</a></div>' . '</ul>' . '</div>' . '<br />');


endforeach;
} else {
   
    
    
    
    
    global $pluginOptionsVal;
$pluginOptionsVal=get_wshk_sidebar_options();
    if( isset($pluginOptionsVal['wshk_enableacustomshopage']) && $pluginOptionsVal['wshk_enableacustomshopage']==85)
{
    $tesprue = sprintf( __( 'No reviews has been made yet.', 'woo-shortcodes-kit' ) );
    
    $tesbuton = sprintf( __( 'Make your first review', 'woo-shortcodes-kit' ) );
    $mycustomshopurl = get_option('wshk_shopageslug');
    $miurl = get_option( 'siteurl' );
	//return $miurl. '/' .$mycustomshopurl;
        echo '
    <div class="woocommerce-Message woocommerce-Message--info woocommerce-info test">
    '. $tesprue . '
		<a class="woocommerce-Button button" href="' . $miurl. '/' .$mycustomshopurl . '">' . $tesbuton . '</a><br />
		
	</div>
    
    ';
    
    
   } else {
       
        $mbaselink = wc_get_page_permalink( 'shop' );
    //$linksh = wc_get_page_permalink( 'shop' );
    $tesprue = sprintf( __( 'No reviews has been made yet.', 'woo-shortcodes-kit' ) );
    
    $tesbuton = sprintf( __( 'Make your first review', 'woo-shortcodes-kit' ) );
       
         echo '
    <div class="woocommerce-Message woocommerce-Message--info woocommerce-info test">
    '. $tesprue . '
		<a class="woocommerce-Button button" href="' . $mbaselink . '">' . $tesbuton . '</a><br />
		
	</div>
    
    ';
        
    }
    
    
    
    
    
    
}
return ob_get_clean();
}
}
add_shortcode( 'woo_review_products', 'wshk_show_reviews_by_user' );





//Since v.1.6.6 - Updated v.1.8.0
/*SHOW THE WOOCOMMERCE REVIEWS EVERYWHERE YOU WANT*/
//Display the product valorations made by all the users, If you want display in any page or post, use this shortcode [woo_display_reviews]




global $pluginOptionsVal;
$pluginOptionsVal=get_wshk_sidebar_options();
      if(isset($pluginOptionsVal['wshk_enabledisplayreviews']) && $pluginOptionsVal['wshk_enabledisplayreviews']==40)
{




function wshk_get_woo_reviews()
{
    ob_start();
$disreacreviews =  get_option('wshk_enabledisplayreviews');
$disretextavsize =  get_option('wshk_disretextavsize');
$disretextavbdsize = get_option('wshk_disretextavbdsize');
$disretextavbdradius = get_option('wshk_disretextavbdradius');
$disretextavbdtype = get_option('wshk_disretextavbdtype');
$disretextavbdcolor = get_option('wshk_disretextavbdcolor');
$disretexttbwsize = get_option('wshk_disretexttbwsize');

$disretextbxfsize =  get_option('wshk_disretextbxfsize');

$disretextbxbdsize = get_option('wshk_disretextbxbdsize');
$disretextbxbdradius = get_option('wshk_disretextbxbdradius');
$disretextbxbdtype = get_option('wshk_disretextbxbdtype');
$disretextbxbdcolor = get_option('wshk_disretextbxbdcolor');
$disretextbxbgcolor = get_option('wshk_disretextbxbgcolor');
$disretextbxpadding = get_option('wshk_disretextbxpadding');
$disretextbxminheight = get_option('wshk_disretextbxminheight');

$disretextlinktarget = get_option('wshk_disretextlinktarget');
$disretextlinktxd = get_option('wshk_disretextlinktxd');
$disretextlinktxtsize = get_option('wshk_disretextlinktxtsize');
$disretextlinktxtcolor = get_option('wshk_disretextlinktxtcolor');

$disredisplaynumber = get_option('wshk_disredisplaynumber');
$disrecolumnsnumber = get_option('wshk_disrecolumnsnumber');
$disretextmargintop = get_option('wshk_disretextmargintop');
$disretextcolor = get_option('wshk_disretextcolor');
$limitationtype = get_option('wshk_showpoints');
$llimitlinktext = get_option('wshk_readmoretextlim');

if(!isset($llimitlinktext) || trim($llimitlinktext) == ''){
    
   $limitlinktext = __('Read more', 'woo-shortcodes-kit'); 
    
} else {
    
   $limitlinktext = $llimitlinktext; 
}


$limitcomm = get_option('wshk_limitcomm');
 if(!isset($limitcomm) || trim($limitcomm) == ''){
    
    $limitationquantity = '300'; 
} else {
     $limitationquantity = get_option('wshk_limitcomm');  
}

//Detect storefront

$wshk_storefront_my_theme = wp_get_theme( 'storefront' );
if ( $wshk_storefront_my_theme->exists() ) {
    
    $commentstablink = 'tab-reviews';
    
} else {
    
    $commentstablink = 'comments';
}

    
$count = 0;
$html_r = "";
$title="";
$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
$number = $disredisplaynumber; // all for show all the comments, for other quantity just write the number 1,2,3,4...
$offset = ( $paged - 1 ) * $number;
$args = array(
  'number' => $number,
    'offset' => $offset,
    'paged' => $paged,
'post_type' => 'product',

);
?> 
  <style>
  .wshk-grava {
border: <?php echo $disretextavbdsize ?>px <?php echo $disretextavbdtype ?> <?php echo $disretextavbdcolor ?>;
border-radius: <?php echo $disretextavbdradius ?>%;
margin-top: <?php echo $disretextmargintop ?>px;

}


@media screen and (max-width: 659px) and (min-width: 320px) { 
   .wshk-reviews{ 
    display: initial;
   }
}
  </style>

  <?php
$comments_query = new WP_Comment_Query;
$comments = $comments_query->query( $args );
$id_or_email = get_current_user_id();


$ccomments = get_comments($args);
$publishedby = __( 'Published by', 'woo-shortcodes-kit' );  
$publishedon = __( 'on', 'woo-shortcodes-kit' );

foreach($comments as $comment) :
    
    //Since v.1.8.1
    // strip tags to avoid breaking any html
$comment->comment_content = strip_tags($comment->comment_content);
if (strlen($comment->comment_content) > $limitationquantity) {
$url = home_url();
    // truncate string
    $stringCut = substr($comment->comment_content, 0, $limitationquantity);
    $endPoint = strrpos($stringCut, ' ');

    //if the string doesn't contain any space then it will cut without word basis.
    $comment->comment_content = $endPoint? substr($stringCut, 0, $endPoint) : substr($stringCut, 0);
    
    if ($limitationtype == 'showlin') {
        
    $comment->comment_content .= '... <a href="' . $url . '/?p=' . $comment->comment_post_ID . '/#'.$commentstablink. '" target="'.$disretextlinktarget . '">'.$limitlinktext.'</a>'; 
    } else {
    $comment->comment_content .= '...';
        
    }
}
    
    
    
    $ccomment = get_comments($args);
 /*New from v.1.6.9.a - Updated v.1.8.0*/
    
  $coauema = $comment->comment_author_email;
  
  $ggravatar = get_avatar( $coauema, $disretextavsize, null, null, array('class' => array('wshk-grava') ) ) . ' ';
  
$title = '<div style="border:'. $disretextbxbdsize. 'px ' . $disretextbxbdtype . ' ' .  $disretextbxbdcolor .'; border-radius:' . $disretextbxbdradius .'px;background-color:' .$disretextbxbgcolor .';color: '.$disretextcolor .' ;padding:' . $disretextbxpadding . 'px;max-width:100%;min-height:' . $disretextbxminheight .'px;max-height:' . $disretextbxminheight .'px;height:100%;margin-bottom: 20px;font-size:' .$disretextbxfsize . 'px;">'.get_the_title( $comment->comment_post_ID ).'';
  
$html_r = $html_r. '<a class="wshkreviprolink" style="font-size:' . $disretextlinktxtsize . 'px; color:' . $disretextlinktxtcolor . ';text-decoration:' . $disretextlinktxd . ';" href="/?p=' . $comment->comment_post_ID . '/#'.$commentstablink. '" target="' .$disretextlinktarget . '">' . $title . '</a><span style="float:right;" class="star-rating" itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating"><span style="width:' . ( get_comment_meta( $comment->comment_ID, 'rating', true ) / 5 ) * 100 . '%"><strong itemprop="ratingValue">' . get_comment_meta( $comment->comment_ID, 'rating', true ) . '</strong></span></span><br />';
  
$html_r = $html_r. '
<table class="wshktabborder" style="border: 0px solid transparent;"><tr><td class="wshkreviewsgrav" width="' .$disretexttbwsize . 'px"><span>' .$ggravatar.'</span></td>';


$html_r = $html_r.'<td class="wshkreviewsgravdata"><span class="wshkpublishedtext"><small>'. $publishedby .'</small></span><span class="wshkcomauthor"><small><strong>'.' '.$comment->comment_author. '</strong></small></span><span class="wshkontext"><small>'. ' ' .$publishedon.'</small></span><span class="wshkcomdate"><small> '. get_comment_date( "", $comment) . '</small></span></td></tr></table>';

$html_r = $html_r. '<div class="wshkcommenttext" style="padding-left: 0px;">' .$comment->comment_content.'<br /></div></div>';


/*$html_r = $html_r."<small>Publicado por ".$comment->comment_author." el ".$comment->comment_date. "</small></div>";*/
  
  
endforeach;
  
return '
<style>
  span.allwshk.star-rating {
      float:right;
  }
  .wshk-grava {
      border: '.$disretextavbdsize.'px '.$disretextavbdtype.' '.$disretextavbdcolor.';
border-radius: '.$disretextavbdradius.'%;
     
  }
  
 

td.wshkreviewsgrav {
    padding: 0em 0em !important;
    background:none !important;
}

td.wshkreviewsgravdata {
    padding: 0em 0em !important;
    background:none !important;
}
</style>

<div class="wshk-reviews" style="display:block;column-count:' .$disrecolumnsnumber . '; width: 100%;">'.$html_r.'</div>';
  

ob_get_clean();
}

add_shortcode('woo_display_reviews', 'wshk_get_woo_reviews');

}







//Since v.1.5

/*SHOW TOTAL OF COMMENTS BY USER (Only products)*/
//Display a product reviews counter made by a user, If you want display in any page or post, use this shortcode [woo_total_count_reviews] 

function wshk_count_reviews_by_user(){
$user_id = get_current_user_id();
$args = array(
	'user_id' => $user_id, // get the user by ID
	'post_type' => 'product',	
        'count' => true //return only the count
);
  global $pluginOptionsVal;
$pluginOptionsVal=get_wshk_sidebar_options();
      if(isset($pluginOptionsVal['wshk_enablerwcounter']) && $pluginOptionsVal['wshk_enablerwcounter']==10)
{
        global $pluginOptionsVal;
$pluginOptionsVal=get_wshk_sidebar_options();

if(isset($pluginOptionsVal['wshk_yesenablethree']) && $pluginOptionsVal['wshk_yesenablethree']=='wshk_yesenablethree')
{
  ob_start();
  }
  else if(isset($pluginOptionsVal['wshk_nnoenablethree']) && $pluginOptionsVal['wshk_nnoenablethree']==wshk_nnoenablethree) {
      //ob_start();
      
  }

$treviewprefix = get_option('wshk_treviewprefix');
$treviewsuffix = get_option('wshk_treviewsuffix');
$treviewpsuffix = get_option('wshk_treviewpsuffix');
$textnoreview = get_option('wshk_textnoreview');
$alignthereviews = get_option('wshk_alignthereviews');

$comments = get_comments($args);


 // Display the message if the customer has 1 review.
    if ( $comments == 1 ) {
        echo '<p style="text-align:' . $alignthereviews .';">' . $treviewprefix . ' '  . $comments . ' ' . $treviewsuffix. '</p>';
 // Display this notice if the customer hasn't reviews yet.       
    } elseif( $comments == 0 ) {
        echo '<p style="text-align:' . $alignthereviews .';">' . $textnoreview. '</p>';
    }
     // Display this notice if the customer has more than 1 review.
    else if( $comments >= 2 ) {
        echo '<p style="text-align:' . $alignthereviews .';">' . $treviewprefix . ' ' . $comments . ' ' . $treviewpsuffix. '</p>';
    } 
    
            global $pluginOptionsVal;
$pluginOptionsVal=get_wshk_sidebar_options();

if(isset($pluginOptionsVal['wshk_yesenablethree']) && $pluginOptionsVal['wshk_yesenablethree']=='wshk_yesenablethree')
{
  return ob_get_clean();
  }
  else if(isset($pluginOptionsVal['wshk_nnoenablethree']) && $pluginOptionsVal['wshk_nnoenablethree']==wshk_nnoenablethree) {
      //return ob_get_clean();
      
  }
    
}
} 
add_shortcode( 'woo_total_count_reviews', 'wshk_count_reviews_by_user' );








//SINCE v.1.6.6 
//CHECK IF EASY MY ACCOUNT BUILDER IS ACTIVE

if ( in_array( 'easy-myaccount-builder/easy-myaccount-builder-for-wshk.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    
       include( ABSPATH . '/wp-content/plugins/easy-myaccount-builder/emab-functions.php' );
    

        }



  
//Since v.1.6.6

/*SHOW THE DASHBOARD*/
//Display the account edit form and let customize the data, If you want display in any page or post, use this shortcode [woo_mydashboard]

//Since v.1.7.8
if(isset($pluginOptionsVal['wshk_enabledashbsht']) && $pluginOptionsVal['wshk_enabledashbsht']==2004)
{



function wshk_newstyle_mydashboard() {
    /*wc_get_template( 'myaccount/form-edit-account.php', array( 'user' => get_user_by( 'id', get_current_user_id() ) ) ); */
    if (  is_user_logged_in() && ( is_account_page() ) ) {
        ob_start();
    require dirname( __FILE__ ) . '/mytemplates/dashboard.php';
    return ob_get_clean();
    }
}
add_shortcode ('woo_mydashboard', 'wshk_newstyle_mydashboard');
}

//Since v.1.6.6

/*SHOW THE ORDERS*/
//Display the account edit form and let customize the data, If you want display in any page or post, use this shortcode [woo_myorders]

//Since v.1.7.8
if(isset($pluginOptionsVal['wshk_enableorderscontrol']) && $pluginOptionsVal['wshk_enableorderscontrol']==140)
{


function wshk_newstyle_myorders() {
    /*wc_get_template( 'myaccount/form-edit-account.php', array( 'user' => get_user_by( 'id', get_current_user_id() ) ) ); */
    
   
    if (  is_user_logged_in() && ( is_account_page() ) ) {
ob_start();
    require dirname( __FILE__ ) . '/mytemplates/my-orders.php';
    
    global $wp;

    if ( ! empty( $wp->query_vars ) ) {
      foreach ( $wp->query_vars as $key => $value ) {
        // Ignore pagename param.
        if ( 'edit-address' === $key ) {
          continue;
        }
        
        if ( 'add-payment-method' === $key ) {
          continue;
        }
        
        //Since v.1.8.2
if(isset($pluginOptionsVal['wshk_enablesubscriptionshortcode']) && $pluginOptionsVal['wshk_enablesubscriptionshortcode']==3003)
{
        
        if ( 'view-subscription' === $key ) {
          continue;

        } //Activar solo si se usa el shortcode
}
        
        if ( 'payment-methods' === $key ) {
          continue;
        }


        if ( has_action( 'woocommerce_account_' . $key . '_endpoint' ) ) {
          do_action( 'woocommerce_account_' . $key . '_endpoint', $value );
          return ob_get_clean();
          
        }
      }
    }

    // No endpoint found? Default to dashboard.
    /*wc_get_template( 'myaccount/', array(
      'current_user' => get_user_by( 'id', get_current_user_id() ),
    ) );*/
    return ob_get_clean();
} 
}
add_shortcode ('woo_myorders', 'wshk_newstyle_myorders');

//Sustituir plantilla del tema por la del plugin
add_filter( 'wc_get_template', 'wshk_cma_get_templatee', 10, 5 );
function wshk_cma_get_templatee( $located, $template_name, $args, $template_path, $default_path ) {    
    if ( 'myaccount/view-order.php' == $template_name ) {
        $located = plugin_dir_path( __FILE__ ) . '/mytemplates/view-order.php';
    }
    
    return $located;
}

}





//Since v.1.6.6

/*SHOW THE DOWNLOADS*/
//Display the account edit form and let customize the data, If you want display in any page or post, use this shortcode [woo_mydownloads]

//Since v.1.7.8
if(isset($pluginOptionsVal['wshk_enablemydownloadsht']) && $pluginOptionsVal['wshk_enablemydownloadsht']==2000)
{

function wshk_newstyle_mydownloads() {
    /*wc_get_template( 'myaccount/form-edit-account.php', array( 'user' => get_user_by( 'id', get_current_user_id() ) ) ); */
    if (  is_user_logged_in() && ( is_account_page() ) ) {
        ob_start();

    require dirname( __FILE__ ) . '/mytemplates/downloads.php';
    return ob_get_clean();
}
}
add_shortcode ('woo_mydownloads', 'wshk_newstyle_mydownloads');
}





//Since v.1.6.6

/*SHOW THE ADDRESSES*/
//Display the account edit form and let customize the data, If you want display in any page or post, use this shortcode [woo_myaddress]

//Since v.1.7.8
if(isset($pluginOptionsVal['wshk_enablemyaddressessht']) && $pluginOptionsVal['wshk_enablemyaddressessht']==2001)
{

function wshk_newstyle_myaddress() {
    /*wc_get_template( 'myaccount/form-edit-account.php', array( 'user' => get_user_by( 'id', get_current_user_id() ) ) ); */
    
    if (  is_user_logged_in() && ( is_account_page() ) ) {
    ob_start();    
        
    require dirname( __FILE__ ) . '/mytemplates/my-address.php';
    
    ?> <?php
    
    global $wp;

    if ( ! empty( $wp->query_vars ) ) {
      foreach ( $wp->query_vars as $key => $value ) {
        // Ignore pagename param.
        if ( 'view-order' === $key ) {
          continue;
        }
        
        if ( 'add-payment-method' === $key ) {
          continue;
        }
        
        if ( 'view-subscription' === $key ) {
          continue;

        }
        
        if ( 'payment-methods' === $key ) {
          continue;
        }


        if ( has_action( 'woocommerce_account_' . $key . '_endpoint' ) ) {
          
          //ob_start();
          do_action( 'woocommerce_account_' . $key . '_endpoint', $value );
          return ob_get_clean();
          
        }
      }
    }

    // No endpoint found? Default to dashboard.
   /* wc_get_template( 'myaccount/', array(
      'current_user' => get_user_by( 'id', get_current_user_id() ),
    ) );*/
return ob_get_clean();    
}

}

add_shortcode ('woo_myaddress', 'wshk_newstyle_myaddress');

//Sustituir plantilla del tema por la del plugin

function wshk_cma_get_template( $located, $template_name, $args, $template_path, $default_path ) {   
        
    if ( 'myaccount/form-edit-address.php' == $template_name ) {
        $located = plugin_dir_path( __FILE__ ) . '/mytemplates/form-edit-address.php';
    }
    
    return $located;
    
}
add_filter( 'wc_get_template', 'wshk_cma_get_template', 10, 5 );

}




//Since v.1.6.6

/*SHOW THE PAYMENTS METHODS*/
//Display the account edit form and let customize the data, If you want display in any page or post, use this shortcode [woo_mypayments]


//Since v.1.7.8
if(isset($pluginOptionsVal['wshk_enablemypaymentsht']) && $pluginOptionsVal['wshk_enablemypaymentsht']==2002)
{


function wshk_newstyle_mypayment() {
    //wc_get_template( 'myaccount/form-edit-account.php', array( 'user' => get_user_by( 'id', get_current_user_id() ) ) ); 
    if (  is_user_logged_in() && ( is_account_page() ) ) {
        ob_start();
    require dirname( __FILE__ ) . '/mytemplates/payment-methods.php';
    
    //require dirname( __FILE__ ) . '/mytemplates/form-add-payment-method.php';
    ?>
   <br /><br /><br /><br /><?php
    global $wp;

    if ( ! empty( $wp->query_vars ) ) {
        
      foreach ( $wp->query_vars as $key => $value ) {
        // Ignore pagename param.

        if ( 'edit-address' === $key ) {
          continue;

        }
        
        if ( 'view-order' === $key ) {
          continue;

        }
        
        if ( 'view-subscription' === $key ) {
          continue;

        }
        
        if ( 'payment-methods' === $key ) {
          continue;

        }


        if ( has_action( 'woocommerce_account_' . $key . '_endpoint' ) ) {
            //ob_start();
          do_action( 'woocommerce_account_' . $key . '_endpoint', $value );
          return ob_get_clean();
          
          
        }
      }
      
    }

    // No endpoint found? Default to dashboard.
   /* wc_get_template( 'myaccount/', array(
      'current_user' => get_user_by( 'id', get_current_user_id() ),
    ) );*/
    return ob_get_clean();
    }
}
add_shortcode ('woo_mypayment', 'wshk_newstyle_mypayment');

//Sustituir plantilla del tema por la del plugin

function wshk_pcma_get_templatee( $located, $template_name, $args, $template_path, $default_path ) {    
    if ( 'myaccount/payment-methods.php' == $template_name ) {
         
        $located = plugin_dir_path( __FILE__ ) . '/mytemplates/payment-methods.php';
        
    }
    
    return $located;
    
}
add_filter( 'wc_get_template', 'wshk_pcma_get_templatee', 10, 5 );
}





//Since v.1.6.8 
//CHECK IF CUSTOM REDIRECTIONS IS ACTIVE

if ( in_array( 'custom-redirections-for-wshk/custom-redirections-for-whsk.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    
      


//Since v.1.6.8
//Sustituir plantilla del tema por la del plugin

function wshk_pcma_get_templateeh( $located, $template_name, $args, $template_path, $default_path ) {    
    if ( 'myaccount/form-add-payment-method.php' == $template_name ) {
         
        $located = plugin_dir_path( __FILE__ ) . '/mytemplates/form-add-payment-method.php';
        
    }
    
    return $located;
    
}
add_filter( 'wc_get_template', 'wshk_pcma_get_templateeh', 10, 5 );
}

//Since v.1.6.6

/*SHOW THE EDIT ACCOUNT*/
//Display the account edit form and let customize the data, If you want display in any page or post, use this shortcode [woo_edit_myaccount]

//Since v.1.7.8
if(isset($pluginOptionsVal['wshk_enablemyeditaccsht']) && $pluginOptionsVal['wshk_enablemyeditaccsht']==2003)
{

function wshk_newstyle_myeditaccount() {
    /*wc_get_template( 'myaccount/form-edit-account.php', array( 'user' => get_user_by( 'id', get_current_user_id() ) ) ); */
    if (  is_user_logged_in() && ( is_account_page() ) ) {
    ob_start();
    require dirname( __FILE__ ) . '/mytemplates/form-edit-account.php';
    return ob_get_clean();
}
}
add_shortcode ('woo_myedit_account', 'wshk_newstyle_myeditaccount');
}



function mytheme_add_woocommerce_support() {
    add_theme_support( 'woocommerce' );
}
add_action( 'after_setup_theme', 'mytheme_add_woocommerce_support' );


//Since v.1.5 - Updated v.1.8.0

/*SHOW THE LOGIN & REGISTER FORM*/
//If you are building your own myaccount page, you need use this function to display the login/register form. Just need use this shortcode [woo_login_form]
/* global $pluginOptionsVal;
$pluginOptionsVal=get_wshk_sidebar_options();*/
      if(isset($pluginOptionsVal['wshk_enableloginform']) && $pluginOptionsVal['wshk_enableloginform']==13)
{
add_shortcode ('woo_login_form', 'wshk_login_form');
function wshk_login_form() {


if ( ! is_user_logged_in() ) {
        //ob_start();
         return do_shortcode( '[woocommerce_my_account]' );
    /*wc_get_template( 'myaccount/form-edit-account.php', array( 'user' => get_user_by( 'id', get_current_user_id() ) ) ); */
   //return get_ob_clean();
     
        
     /*OLD method*/
//wc_get_template( 'myaccount/form-lost-password.php' ); /*OLD method*/
//wc_get_template( 'myaccount/form-reset-password.php' ); /*OLD method*/
//require dirname( __FILE__ ) . '/mytemplates/login.php';
//echo wp_login_form();


} 
}
}

 /*global $pluginOptionsVal;
$pluginOptionsVal=get_wshk_sidebar_options();*/
      if(isset($pluginOptionsVal['wshk_enableloginform']) && $pluginOptionsVal['wshk_enableloginform']==13)
{
//Sustituir plantilla del tema por la del plugin
add_filter( 'wc_get_template', 'wshk_logma_get_templatee', 10, 5 );
function wshk_logma_get_templatee( $located, $template_name, $args, $template_path, $default_path ) {    
    $theme = get_current_theme(); // gets the current theme

    if ( 'myaccount/form-login.php' == $template_name ) {
        $located = plugin_dir_path( __FILE__ ) . '/mytemplates/form-login.php';
        
    } elseif($theme = get_option( 'Storefront' ) ) {
        
        $located = do_shortcode('[woocommerce_my_account]');
    }
    
    return $located;

}
}





//Since v.1.5 - Updated v.1.7.7
/*REDIRECT USERS TO CUSTOM URL AFTER LOGIN (BASE ON THEIR ROLE)*/
//If you are building your own myaccount page, you need use it to redirect the user after the login to a page. Just need activate and write the page slug. For exmaple "myownaccount".

/**
 * Redirect users to custom URL based on their role after login
 *
 * @param string $redirect
 * @param object $user
 * @return string
 */
 global $pluginOptionsVal;
$pluginOptionsVal=get_wshk_sidebar_options();
      if(isset($pluginOptionsVal['wshk_enableloginform']) && $pluginOptionsVal['wshk_enableloginform']==13)
{


    
function wshk_custom_user_redirect( $redirect, $user ) {


	// Get the first of all the roles assigned to the user
	$loginredi = get_option('wshk_loginredi');
	$role = $user->roles[0];
	$dashboard = admin_url();
	$myaccount = home_url( '/' . $loginredi );
	$wshk_checkouturl = get_permalink( get_option( 'woocommerce_checkout_page_id' ) );
	
	
	if( $role == 'administrator' ) {
		//Redirect administrators to the dashboard
		$redirect = $dashboard;
	} elseif ( $role == 'shop-manager' ) {
		//Redirect shop managers to the dashboard
		$redirect = $dashboard;
	} elseif ( $role == 'editor' ) {
		//Redirect editors to the dashboard
		$redirect = $dashboard;
	} elseif ( $role == 'author' ) {
		//Redirect authors to the dashboard
		$redirect = $dashboard;
	} elseif ( WC()->cart->is_empty() or is_account_page() && $role == 'customer' || $role == 'subscriber' ) {
	    //$redirect = $mytestreditest;
	    $redirect = $myaccount;
	    //$redirect = $wshkurl;
	} else {
	    
		//Redirect any other role to the previous visited page or, if not available, to the home
		//$redirect = wp_get_referer() ? wp_get_referer() : home_url();
		//$redirect = $myaccount;
		$redirect = $wshk_checkouturl;
	}
	return $redirect;

}

add_filter( 'woocommerce_login_redirect', 'wshk_custom_user_redirect', 10, 2 );



}
//Since v.1.5

/*SHOW LOGOUT BUTTON*/
//If you are building your own myaccount page, you will need this function to let the user make a logout. Just need activate and use this shortcode: [woo_logout_button]
add_shortcode ('woo_logout_button', 'wshk_logout_button');
function wshk_logout_button() {

 global $pluginOptionsVal;
$pluginOptionsVal=get_wshk_sidebar_options();
      if(isset($pluginOptionsVal['wshk_enablelogoutbtn']) && $pluginOptionsVal['wshk_enablelogoutbtn']==12)
{
    
    
if ( is_user_logged_in() && ( is_account_page() ) ) {
$logbtnbdsize = get_option('wshk_logbtnbdsize');
$logbtnbdradius = get_option('wshk_logbtnbdradius');
$logbtnbdtype = get_option('wshk_logbtnbdtype');
$logbtnbdcolor = get_option('wshk_logbtnbdcolor');
$logbtntext = get_option('wshk_logbtntext');
$logbtntd = get_option('wshk_logbtntd');
$logbtnta = get_option('wshk_logbtnta');
$logbtnwd = get_option('wshk_logbtnwd');



//the get page id myaccount can be changed for shop to redirect after logout to the shop page
ob_start();
print '<a class="woocommerce-Button button wshkclose" style="border:' . ' ' . $logbtnbdsize . 'px' . ' ' . $logbtnbdtype . ' ' . $logbtnbdcolor . '; border-radius:' . ' ' . $logbtnbdradius . 'px; text-decoration:' . ' ' . $logbtntd . '; margin: 0 auto;  text-align:' . ' ' . $logbtnta . '; display:block; width:' . ' ' . $logbtnwd . 'px;" href="' . wp_logout_url( get_permalink( wc_get_page_id( "myaccount" ) ) ) . '">' . ' ' . $logbtntext . ' ' . '</a>';

return ob_get_clean();
}

}
}

//Since 1.6.6
/*Redirect after logout to a custom page*/

function wshk_custom_logout_redirect() {
    
    global $pluginOptionsVal;
$pluginOptionsVal=get_wshk_sidebar_options();
      if(isset($pluginOptionsVal['wshk_enablelogoutbtn']) && $pluginOptionsVal['wshk_enablelogoutbtn']==12)
{
    
    $clogpage = get_option( 'wshk_btnlogoutredi' );
    $baselink = home_url( '/' . $clogpage );
    if (!empty ($clogpage)) {
        wp_redirect($baselink);
        exit();
    }
    
}
}
add_action('wp_logout', 'wshk_custom_logout_redirect', PHP_INT_MAX);

//Since v.1.5

/*SHOW THE USERNAME*/
//If you are building your own myaccount page, maybe need this function to get the username. Just need activate and use this shortcode: [woo_user_name]
add_shortcode('woo_user_name', 'wshk_get_user');
function wshk_get_user() {

  global $pluginOptionsVal;
$pluginOptionsVal=get_wshk_sidebar_options();
      if(isset($pluginOptionsVal['wshk_enableusername']) && $pluginOptionsVal['wshk_enableusername']==11)
{
ob_start();
	if ( is_user_logged_in()) {
$usernmtc = get_option('wshk_usernmtc');
$usernmts = get_option('wshk_usernmts');
$usernmta = get_option('wshk_usernmta');
$textusernmpf = get_option('wshk_textusernmpf');
$textusernmsf = get_option('wshk_textusernmsf');
		$user = wp_get_current_user();
		
		//CONDITION TO CHANGE THE SHORTCODE DISPLAY FUNCTION

global $pluginOptionsVal;
$pluginOptionsVal=get_wshk_sidebar_options();

if(isset($pluginOptionsVal['wshk_showusername']) && $pluginOptionsVal['wshk_showusername']==showus)
{
    echo '<p style="color:' . ' ' . $usernmtc . '; text-align:' . ' ' . $usernmta . '; font-size:' . ' ' . $usernmts . 'px;">' . $textusernmpf . ' ' . $user->user_login . ' ' . $textusernmsf . '</p>';
    
} else if (isset($pluginOptionsVal['wshk_showusername']) && $pluginOptionsVal['wshk_showusername']==showonly) 

{
    
    echo '<p style="color:' . ' ' . $usernmtc . '; text-align:' . ' ' . $usernmta . '; font-size:' . ' ' . $usernmts . 'px;">' . $textusernmpf . ' ' . $user->user_firstname . ' ' . $textusernmsf . '</p>';
    
} else {

   echo '<p style="color:' . ' ' . $usernmtc . '; text-align:' . ' ' . $usernmta . '; font-size:' . ' ' . $usernmts . 'px;">' . $textusernmpf . ' ' . $user->display_name . ' ' . $textusernmsf . '</p>';
    
}
		
		
		/*echo '<p style="color:' . ' ' . $usernmtc . '; text-align:' . ' ' . $usernmta . '; font-size:' . ' ' . $usernmts . 'px;">' . $textusernmpf . ' ' . $user->display_name . ' ' . $textusernmsf . '</p>';*/
		
		return ob_get_clean();
	}
}
}

//Since v1.5

/*CHANGE THE ADD TO CART BUTTON TEXT*/
//If you want change the add to cart button text for: external, grouped, simple, and variable products, just activate this function and change the texts. If the function is active, you need complet all the fields.  


global $pluginOptionsVal;
$pluginOptionsVal=get_wshk_sidebar_options();
      if(isset($pluginOptionsVal['wshk_enableaddtocarttxt']) && $pluginOptionsVal['wshk_enableaddtocarttxt']==14)
{
function wshk_custom_woocommerce_product_add_to_cart_text() {


	global $product;
	$atctxtexternal = get_option('wshk_atctxtexternal');
	$atctxtgrouped = get_option('wshk_atctxtgrouped');
	$atctxtsimple = get_option('wshk_atctxtsimple');
	$atctxtvariable = get_option('wshk_atctxtvariable');
	/*$atctxtntin = get_option('wshk_atctxtntin');	*/
	
	$product_type = $product->product_type;
	
	switch ( $product_type ) {
		case 'external':
			return __( $atctxtexternal, 'woocommerce' );
		break;
		case 'grouped':
			return __( $atctxtgrouped, 'woocommerce' );
		break;
		case 'simple':
			return __( $atctxtsimple, 'woocommerce' );
		break;
		case 'variable':
			return __( $atctxtvariable, 'woocommerce' );
	/*	break;
		default:
			return __( $atctxtntin, 'woocommerce' );*/
	}
	
}
add_filter( 'woocommerce_product_add_to_cart_text' , 'wshk_custom_woocommerce_product_add_to_cart_text' ); 

add_filter( 'woocommerce_product_single_add_to_cart_text', 'wshk_custom_woocommerce_product_add_to_cart_text' );
}

//Since 1.6.4
/*CUSTOM MENU FOR LOGGED IN AND NON LOGGED IN USERS*/
//If you want display a different menu for logged in & non logged in users, just need activate this function and write the menu name in each field.
global $pluginOptionsVal;
$pluginOptionsVal=get_wshk_sidebar_options();
      if(isset($pluginOptionsVal['wshk_enablecustomenu']) && $pluginOptionsVal['wshk_enablecustomenu']==17)
{
    
    
function wshk_custom_menus( $args ) {
    //can be change top for primary or secondary
   $menloca = get_option('wshk_menulocation');
   
      if ( $args['theme_location'] ==  $menloca) {
         
    $loggedinmenu = get_option('wshk_logmenu');
    $nonloggedinmenu = get_option('wshk_nonlogmenu');
    
 if( is_user_logged_in() ) {
      
   


 $args['menu'] = $loggedinmenu;
 } else {
     
 $args['menu'] = $nonloggedinmenu;
 }
 }
 return $args;
    
}
 add_filter( 'wp_nav_menu_args', 'wshk_custom_menus' );
}


//Since 1.6.4
/*ENABLE ADD SHORTCODES IN MENU ITEM TITLES*/
//If you want insert shortcodes in your menu item titles, just need activate this function and nothing more!

global $pluginOptionsVal;
$pluginOptionsVal=get_wshk_sidebar_options();
      if(isset($pluginOptionsVal['wshk_enableshtmenu']) && $pluginOptionsVal['wshk_enableshtmenu']==18)
{

function wshk_shortcodes_in_menu( $menu ){ 
        return do_shortcode( $menu ); 
} 
add_filter('wp_nav_menu', 'wshk_shortcodes_in_menu'); 
}

//Since 1.6.4
/*ENABLE DISPLAY USERNAME IN MENU*/
//If you want show the username in the menu, just need activate this function.

global $pluginOptionsVal;
$pluginOptionsVal=get_wshk_sidebar_options();
      if(isset($pluginOptionsVal['wshk_enableuserinmenu'])  && $pluginOptionsVal['wshk_enableuserinmenu']==19)
{
   

function displayname_on_menu(){
    ob_start();
    $user = wp_get_current_user();
    return $user->first_name;
    return ob_get_clean();
}



add_shortcode( 'wshk_user_in_menu' , 'displayname_on_menu' );
}


//Since 1.6.6
/*ENABLE BLOCK WP-ADMIN & WP-LOGIN-PHP + REDIRECT TO SHOP PAGE*/
//If you want block the access to this urls and redirect to the custom login form, just need activate this function. Can be used with the WooCommerce's custom myaccount shortcode and with your custom myaccount page.

global $pluginOptionsVal;
$pluginOptionsVal=get_wshk_sidebar_options();
      if(isset($pluginOptionsVal['wshk_enablesloginsec'])  && $pluginOptionsVal['wshk_enablesloginsec']==20)
{
   

function wshk_redirect_custom_account_page(){

    // Get the user current page
    
    $page_viewed = basename( $_SERVER['REQUEST_URI'] );

    // Get permalink to the account page
    
    $caccount_page  = get_permalink( get_option('woocommerce_myaccount_page_id') );
      
	/* Check if a non logged in user is trying to view wp-login.php */
	
	global $pagenow;
    if ($pagenow == 'wp-login.php' && !is_user_logged_in())
        {
            // Redirect
            wp_redirect( $caccount_page );
            exit();
        }

    // Block wp-login for logged in users
    if( $page_viewed == "wp-login.php" && is_user_logged_in()) {
        wp_redirect( $caccount_page );
        exit();
    }
    
     
}

add_action( 'init','wshk_redirect_custom_account_page' );
}



//Since 1.6.6 - Updated 1.7.5
/*ENABLE BLOCK WP-ADMIN BAR FOR NON ADMINS*/
//If you want block the access via admin top bar to the non admins users, just need activate this function.

global $pluginOptionsVal;
$pluginOptionsVal=get_wshk_sidebar_options();
      if(isset($pluginOptionsVal['wshk_enablesadminbar'])  && $pluginOptionsVal['wshk_enablesadminbar']==21)
{
   

function wshk_hide_admin_bar_if_non_admin( $show ) {
    $user = wp_get_current_user();
    $role = $user->roles[0];
    if ( ! current_user_can( 'administrator' ) ) $show = false;
    elseif ( $role == 'shop-manager' ) $show = true;
    elseif ( $role == 'editor' ) $show = true;
    elseif ( $role == 'author' ) $show = true;
    return $show;
} 


 
 
add_filter( 'show_admin_bar', 'wshk_hide_admin_bar_if_non_admin', 20, 1 );
}



//Since v.1.6.6

/*RESTRICT CONTENT TO NON LOGGED IN USERS*/
//Hide the content that you want for non logged in users everywhere! If you want restrict some content in any page or post, use this shortcode [wshk] my contente [/wshk]

global $pluginOptionsVal;
$pluginOptionsVal=get_wshk_sidebar_options();
      if(isset($pluginOptionsVal['wshk_enablerestrictctnt']) && $pluginOptionsVal['wshk_enablerestrictctnt']==22)
{
function wshk_hide_content_shortcode($atts = [], $content = null)
{
    // do something to $content
    
    if ( is_user_logged_in() ) {
 ob_start();
    // run shortcode parser recursively
    $content = do_shortcode($content);
 
    // always return
    return $content;
    return ob_get_clean();
}
}
add_shortcode('wshk', 'wshk_hide_content_shortcode');
}

//Since v.1.6.6

/*RESTRICT CONTENT TO LOGGED IN USERS*/
//Hide the content that you want for logged in users everywhere! If you want restrict some content in any page or post, use this shortcode [off] my contente [/off]

global $pluginOptionsVal;
$pluginOptionsVal=get_wshk_sidebar_options();
      if(isset($pluginOptionsVal['wshk_enableoffctnt']) && $pluginOptionsVal['wshk_enableoffctnt']==23)
{
function wshk_off_content_shortcode($atts = [], $content = null)
{
    // do something to $content
    
    if ( ! is_user_logged_in() ) {
 ob_start();
    // run shortcode parser recursively
    $content = do_shortcode($content);
 
    // always return
    return $content;
    return ob_get_clean();
}
}
add_shortcode('off', 'wshk_off_content_shortcode');
}


//Sustituir plantilla del tema por la del plugin

function wshk_order_downloads_get_template( $located, $template_name, $args, $template_path, $default_path ) {   
        
    if ( 'order/order-downloads.php' == $template_name ) {
        $located = plugin_dir_path( __FILE__ ) . '/mytemplates/order-downloads.php';
    }
    
    return $located;
    
}
add_filter( 'wc_get_template', 'wshk_order_downloads_get_template', 10, 5 );



//Sustituir plantilla del tema por la del plugin


if ( in_array( 'custom-redirections-for-wshk/custom-redirections-for-whsk.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

function wshk_order_again_get_template( $located, $template_name, $args, $template_path, $default_path ) {   
        
    if ( 'order/order-again.php' == $template_name ) {
        //$located = plugin_dir_path( __FILE__ ) . '/mytemplates/order-again.php';
        
        $located = ABSPATH . '/wp-content/plugins/custom-redirections-for-wshk/mytemplates/order-again.php';
    }
    
    return $located;
    
}
add_filter( 'wc_get_template', 'wshk_order_again_get_template', 10, 5 );

}


//Since v.1.6.8 - Updated v.1.8.0

/*AUTOCOMPLETE THE ORDERS*/
//With this function your orders will be completed automaticlly, just active it and forget the processing status.

global $pluginOptionsVal;
$pluginOptionsVal=get_wshk_sidebar_options();
      if(isset($pluginOptionsVal['wshk_enableautocom']) && $pluginOptionsVal['wshk_enableautocom']==84)
{
add_action( 'woocommerce_thankyou', 'wshk_auto_complete_paid_order', 20, 1 );
function wshk_auto_complete_paid_order( $order_id ) {
    if ( ! $order_id )
        return;

    // Get an instance of the WC_Product object
    $order = wc_get_order( $order_id );

    // No updated status for orders delivered with Bank wire, Cash on delivery and Cheque payment methods.
    if ( in_array( $order->get_payment_method(), array( 'bacs', 'cod', 'cheque', '' ) ) ) {
        return;
    } 
    // For paid Orders with all others payment methods (paid order status "processing")
    elseif( $order->has_status('processing') ) {
        $order->update_status( 'completed' );
    }
}
}


//Since v.1.6.8

/*CUSTOM SHOP PAGE*/
//If you want make your custom shop page, just need use this function.

global $pluginOptionsVal;
$pluginOptionsVal=get_wshk_sidebar_options();
      if(isset($pluginOptionsVal['wshk_enableacustomshopage']) && $pluginOptionsVal['wshk_enableacustomshopage']==85)
{

function skyverge_empty_cart_notice() {
    
	if ( WC()->cart->get_cart_contents_count() == 0 ) {
        	/*wc_print_notice( __( 'Get free shipping if your order is over &#36;60!', 'woocommerce' ), 'notice' );*/
        	// Change notice text as desired
	  
	 ?><p class="return-to-shop">
		<a class="button wc-backward" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>">
			<?php _e( 'Return to shop', 'woocommerce' ) ?>
		</a>
	</p><?php
	}

}
// Add to cart page
/*add_action( 'woocommerce_check_cart_items', 'skyverge_empty_cart_notice' );*/
add_action( 'woocommerce_cart_is_empty', 'skyverge_empty_cart_notice', 99 );



function wc_empty_cart_redirect_url() {
    
    $mycustomshopurl = get_option('wshk_shopageslug');
    $miurl = get_option( 'siteurl' );
	return $miurl. '/' .$mycustomshopurl;
    
}
add_filter( 'woocommerce_return_to_shop_redirect', 'wc_empty_cart_redirect_url' );


}

//Since v.1.6.8

/*HIDE LOGIN ERRORS*/
//If you wont display the login errors while the user is loging, just need enable this function.


global $pluginOptionsVal;
$pluginOptionsVal=get_wshk_sidebar_options();
      if(isset($pluginOptionsVal['wshk_enablehidelogerror']) && $pluginOptionsVal['wshk_enablehidelogerror']==86)
{

function wshk_no_wordpress_errors(){
    $mycustomessageforlogin = get_option('wshk_hidelogerrorcustomessage');
    
  return $mycustomessageforlogin;
}
add_filter( 'login_errors', 'wshk_no_wordpress_errors' );


}




//Since v.1.7.1 - Updated v.1.7.9

/*ADD SECURITY HEADERS*/
//If you wont let use your website in another website how a iframe and prevent the clickjacking attacks, just need enable this function.


global $pluginOptionsVal;
$pluginOptionsVal=get_wshk_sidebar_options();
      if(isset($pluginOptionsVal['wshk_enablesecheaders']) && $pluginOptionsVal['wshk_enablesecheaders']==95)
{
    
    
    function wshk_add_security_headers() {
        
        
        
    // Enforce the use of HTTPS
	header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
	// Prevent Clickjacking
	header("X-Frame-Options: SAMEORIGIN");
	// Prevent XSS Attack
	header ("Content-Security-Policy: object-src 'none'; base-uri 'none';"); // FF 23+ Chrome 25+ Safari 7+ Opera 19+
	header("X-Content-Security-Policy: default-src 'self';"); // IE 10+
	// Block Access If XSS Attack Is Suspected
	header("X-XSS-Protection: 1; mode=block");
	// Prevent MIME-Type Sniffing
	header("X-Content-Type-Options: nosniff");
	// Referrer Policy
	header("Referrer-Policy: no-referrer-when-downgrade");
	// Feature Policy
    header("Feature-Policy: vibrate 'self'");
}
add_action( 'send_headers', 'wshk_add_security_headers' );

}





//Since v.1.7.1

/*SKIP CART AND JUMP TO CHECKOUT*/
//If you want send the users directly to the checkout page after press the add to cart button, just need enable this function.


global $pluginOptionsVal;
$pluginOptionsVal=get_wshk_sidebar_options();
      if(isset($pluginOptionsVal['wshk_enableskipcart']) && $pluginOptionsVal['wshk_enableskipcart']==96)
{

add_filter('woocommerce_add_to_cart_redirect', 'wshk_add_to_cart_redirect');
function wshk_add_to_cart_redirect() {
 global $woocommerce;
 $checkout_url = wc_get_checkout_url();
 return $checkout_url;
}


}


//Since v.1.6.8
    
/*CUSTOM THANK YOU PAGE REDIRECTIONS*/
//If you want redirect the users to a custom thank you page if buy some product (max 3 differents products) or redirect to a general custom thank you page, just need use this function.
    
    
global $pluginOptionsVal;
$pluginOptionsVal=get_wshk_sidebar_options();
      if(isset($pluginOptionsVal['wshk_enableacustomthankyoupage']) && $pluginOptionsVal['wshk_enableacustomthankyoupage']==87)
{ 


    
    function wcs_redirect_product_based_men ( $order_id ){
	$order = wc_get_order( $order_id );
	
	
	
	$myproductone = get_option('wshk_customthankyouone');
	$myproductoneid = get_option('wshk_customthankyouoneid');
	
	$myproducttwo = get_option('wshk_customthankyoutwo');
	$myproducttwoid = get_option('wshk_customthankyoutwoid');
	
	$myproductthree = get_option('wshk_customthankyouthree');
	$myproductthreeid = get_option('wshk_customthankyouthreeid');
	
	$myproducsgeneral = get_option('wshk_customthankyougeneral');
	
	$miurlm = get_option( 'siteurl' );
	
	
 
	foreach( $order->get_items() as $item ) {
		$_product = wc_get_product( $item['product_id'] );
		
	  
	  // PRODUCT ONE
		if ( $item['product_id'] == $myproductoneid ) {
			// change to the URL that you want to send your customer to  
                	wp_redirect($miurlm . '/' . $myproductone);
		}
	  
	  // PRODUCT TWO
	  else if ( $item['product_id'] == $myproducttwoid ) {
			// change to the URL that you want to send your customer to  
                	wp_redirect($miurlm . '/' . $myproducttwo);
		}
	  
	  //PRODUCT THREE
	  else if ( $item['product_id'] == $myproductthreeid ) {
			// change to the URL that you want to send your customer to  
                	wp_redirect($miurlm . '/' . $myproductthree);
		}
		
		
	  
	  //GENERAL OR OTHER PRODUCTS
	  else {
			// change to the URL that you want to send your customer to  
                	wp_redirect($miurlm . '/' . $myproducsgeneral);
		}
	  
	}
}
add_action( 'woocommerce_thankyou', 'wcs_redirect_product_based_men' );	

}

//Since v.1.6.8

/*DISPLAY IP ADDRESS*/
// If you want to show the user IP address in any page, just use this shortcode [woo_display_ip]

//Since v.1.7.8
if(isset($pluginOptionsVal['wshk_enabletheipsht']) && $pluginOptionsVal['wshk_enabletheipsht']==2005)
{

function wshk_display_user_ip() {
        $ip = $_SERVER['REMOTE_ADDR'];
        return $ip;
}
add_shortcode('woo_display_ip', 'wshk_display_user_ip');
}



//Since v.1.6.8

/*DISPLAY USER NAME AND SURNAME*/
// If you want to show the user name and surname, just use this shortcode [woo_display_nsurname]

//Since v.1.7.8
if(isset($pluginOptionsVal['wshk_enablethenamesurnsht']) && $pluginOptionsVal['wshk_enablethenamesurnsht']==2006)
{


function wshk_displayuserapell_short(){
    
    $theuserapell = wp_get_current_user();
  
  
    return $theuserapell->first_name . " " . $theuserapell->last_name . "\n";
    
}

add_shortcode( 'woo_display_nsurname' , 'wshk_displayuserapell_short' );

}


//Since v.1.6.8

/*DISPLAY USER EMAIL*/
// If you want to show the user email, just use this shortcode [woo_display_email]

//Since v.1.7.8
if(isset($pluginOptionsVal['wshk_enabletheuseremailsht']) && $pluginOptionsVal['wshk_enabletheuseremailsht']==2007)
{

function wshk_displayemail_on_menu(){
    
    $theeuser = wp_get_current_user();
    return $theeuser->user_email;
    
}

add_shortcode( 'woo_display_email' , 'wshk_displayemail_on_menu' );
}


//Since v.1.6.8

/*ENABLE GPRD SETTINGS*/
//If you want adjust the function settings you need enable it.

global $pluginOptionsVal;
$pluginOptionsVal=get_wshk_sidebar_options();
      if(isset($pluginOptionsVal['wshk_gprdsettings']) && $pluginOptionsVal['wshk_gprdsettings']==88)
{ 

$gprdurlslug = get_option('wshk_gprdurlslug');
$gprdiread = get_option('wshk_gprdiread');
$gprdpolit = get_option('wshk_gprdpolit');
$gprderror = get_option('wshk_gprderror');
$gprduserlegalinfo = get_option('wshk_gprduserlegalinfo');
$gprdcomveri = get_option('wshk_gprdcomveri');

}

//Since v.1.6.8

/*DISPLAY CHECKBOX IN WP COMMENTS*/
//If you want to show the GPRD checkbox in the comments form, just need activate this function.


global $pluginOptionsVal;
$pluginOptionsVal=get_wshk_sidebar_options();
      if(isset($pluginOptionsVal['wshk_gprdcomments']) && $pluginOptionsVal['wshk_gprdcomments']==89)
{ 
//Since v.1.6.8
/*CHECKBOX*/

function wshk_custom_fields($fields) {


	// Multilingual strings
    $req = get_option( 'require_name_email' );
    $aria_req = ( $req ? " aria-required='true'" : '' );
	$url = '/' . get_option('wshk_gprdurlslug'); /*get_permalink ( get_option( 'wpcpc_policy_page_id' ) );*/
	$read_and_accept =  get_option('wshk_gprdiread');//__( 'He leido y ACEPTO la ', 'wp-comment-policy-checkbox' );

    $fields[ 'policy' ] =
        '<p class="comment-form-policy">'.
            '<label for="policy">
                <input name="policy" value="policy-key" class="comment-form-policy__input" type="checkbox" style="width:auto"' . $aria_req . ' aria-req="true" />
                ' . $read_and_accept . '
                <a href="' . esc_url( $url ) . '" target="_blank" class="comment-form-policy__see-more-link">' . __('Policy Privacy', 'woo-shortcodes-kit') . '</a>
                <span class="comment-form-policy__required required">*</span>
            </label>
        </p>';

    return $fields;
}

add_filter('comment_form_default_fields', 'wshk_custom_fields');



//Since v.1.6.8
/*CHECKBOX VERIFICATOR*/

//javascript validation
add_action('wp_footer','wshk_validate_privacy_comment_javascript');
function wshk_validate_privacy_comment_javascript(){
    if (! is_user_logged_in() && is_single() && comments_open()){
        wp_enqueue_script('jquery');
        ?>
        <script type="text/javascript">
        jQuery(document).ready(function($){
            $("#submit").click(function(e){
                if (!$('.comment-form-policy__input').prop('checked')){
                    e.preventDefault();
                    alert('You must agree to our privacy term by checking the box', 'woo-shortcodes-kit');
                    return false;
                }
            })
        });
        </script>
        <?php
    }
}

//Since v.1.6.8
/*LEGAL TEXT*/

add_action('comment_form_after','wshk_my_comment_form_before');
function wshk_my_comment_form_before() {
    
    
  if (! is_product() ) {
      
      $gprduserlegalinfoo = get_option('wshk_gprduserlegalinfo');
    $gprdcomveri = get_option('wshk_gprdcomveri');
    
    $gprdcommentsbdsize = get_option('wshk_gprdcommentsbdsize');
    $gprdcommentsbdtype = get_option('wshk_gprdcommentsbdtype');
    $gprdcommentsbdcolor = get_option('wshk_gprdcommentsbdcolor');
    $gprdcommentsbdradius = get_option('wshk_gprdcommentsbdradius');
    $gprdcommentspadding = get_option('wshk_gprdcommentspadding');
    $gprdcommentsbgcolor = get_option('wshk_gprdcommentsbgcolor');
    
    
?>
  
  <div style="border: <?php echo $gprdcommentsbdsize ;?>px <?php echo $gprdcommentsbdtype ;?> <?php echo $gprdcommentsbdcolor ;?>;border-radius: <?php echo $gprdcommentsbdradius ;?>px;padding:<?php echo $gprdcommentspadding ;?>px;background-color:<?php echo $gprdcommentsbgcolor ;?>;margin-top: 20px;"><?php echo $gprdcomveri; ?>
  
<!--<h4 style="letter-spacing: 1px;"><span class="fa fa-info-circle"></span> Información relativa a los datos que proporcionas al dejar tu comentario</h4>

<p><strong>Responsable:</strong> Alberto Gómez Orta</p>

<p><strong>Finalidad:</strong> moderación de comentarios</p>

<p><strong>Legitimación:</strong> tu consentimiento, mediante marcación de botón.</p>

<p><strong>Destinatarios:</strong> servidores de Webempresa (actual hosting de esta web).</p>

<p><strong>Derechos:</strong> acceso, rectificación, limitación y/o supresión de tus datos.</p>--></div><br /><?php
}   
}



//Since v.1.6.8
/* COMMENTS VERIFICATION */
add_filter('comment_notification_text', 'wshk_my_comment_notification_text');
add_filter('comment_moderation_text', 'wshk_my_comment_notification_text');


function wshk_my_comment_notification_text($notify_message)
{
     $gprduserlegalinfoo = get_option('wshk_gprduserlegalinfo');
    
    
return $notify_message . $gprduserlegalinfoo;
}


}




//Since v.1.6.8
/*DISPLAY CHECKBOX IN CHECKOUT PAGE */



global $pluginOptionsVal;
$pluginOptionsVal=get_wshk_sidebar_options();
      if(isset($pluginOptionsVal['wshk_gprdorders']) && $pluginOptionsVal['wshk_gprdorders']==90)
{ 




add_action( 'woocommerce_review_order_before_submit', 'wshk_add_checkout_tickbox', 9 );
  
function wshk_add_checkout_tickbox() {
 $readandaccept = get_option('wshk_gprdiread');
 $urlpol = '/' . get_option('wshk_gprdurlslug');
 $pollink = __('Policy Privacy', 'woo-shortcodes-kit');
  
  if(! is_user_logged_in() ) {

?> 
<p class="form-row terms" style="font-size: 16px;">
  
  <input name="deliverycheck" id="deliverycheck" class="comment-form-policy__input" type="checkbox" required style="width:auto">
                <?php echo $readandaccept ; ?>
                <a href="<?php echo $urlpol;  ?>" target="_blank" class="comment-form-policy__see-more-link"><?php echo $pollink ;?></a>
  

</p>
<?php
  } else { 
	?>
  
	<p class="form-row terms" style="display:none;">
  
  <input name="deliverycheck" id="deliverycheck" class="comment-form-policy__input" type="checkbox" required style="width:auto"checked>
                <?php echo $readandaccept ; ?>
                <a href="<?php echo $urlpol; ?>" target="_blank" class="comment-form-policy__see-more-link"><?php echo $pollink ;?></a>
  

</p>
  <?php
  }

 
}
 
// Show notice if customer does not tick
  
add_action( 'woocommerce_checkout_process', 'wshk_not_approved_delivery' );
 
function wshk_not_approved_delivery() {
    if ( ! (int) isset( $_POST['deliverycheck'] ) ) {
        wc_add_notice( __( 'You must agree to our privacy term by checking the box', 'woo-shortcodes-kit'), 'error' );
    }
}







//Since v.1.6.8

/*ADD LEGAL TEXT IN CHECKOUT PAGE*/



add_action( 'woocommerce_review_order_after_submit', 'wshk_gprd_law_info_text', 9 );

function wshk_gprd_law_info_text()  {
$gprduserlegalinfoo = get_option('wshk_gprduserlegalinfo');
$gprdordveri = get_option('wshk_gprdordveri');

$gprdcheckoutbdsize = get_option('wshk_gprdcheckoutbdsize');
$gprdcheckoutbdtype = get_option('wshk_gprdcheckoutbdtype');
$gprdcheckoutbdcolor = get_option('wshk_gprdcheckoutbdcolor');
$gprdcheckoutbdradius = get_option('wshk_gprdcheckoutbdradius');
$gprdcheckoutpadding = get_option('wshk_gprdcheckoutpadding');
$gprdcheckoutbgcolor = get_option('wshk_gprdcheckoutbgcolor');
?>
  <br />
  <br />
  <div style="border: <?php echo $gprdcheckoutbdsize; ?>px <?php echo $gprdcheckoutbdtype; ?> <?php echo $gprdcheckoutbdcolor; ?>;border-radius: <?php echo $gprdcheckoutbdradius; ?>px; padding: <?php echo $gprdcheckoutpadding; ?>px; background-color: <?php echo $gprdcheckoutbgcolor; ?>;">
      <?php echo $gprdordveri; ?>
      
<!--<h4 style="letter-spacing: 1px;font-size: 14px !important"><span class="fa fa-info-circle"></span> Información relativa a los datos que proporcionas al realizar tu pedido</h4>

<p><strong>Responsable:</strong> Alberto Gómez Orta</p>

<p><strong>Finalidad:</strong> realizar compra en tienda online</p>

<p><strong>Legitimación:</strong> tu consentimiento, mediante marcación de botón.</p>

<p><strong>Destinatarios:</strong> servidores de Webempresa (actual hosting de esta web).</p>

<p><strong>Derechos:</strong> acceso, rectificación, limitación y/o supresión de tus datos.</p>--></div><br /><?php
}





//Since v.1.6.8

/*ADD VERIFICATION IN EMAIL ORDER*/




add_action( 'woocommerce_email_customer_details', 'wshk_add_content_', 50, 4 ); 
function wshk_add_content_( $order, $sent_to_admin, $plain_text, $email ) {
    
    $gprduserlegalinfoo = get_option('wshk_gprduserlegalinfo');
    
    
    if ( $sent_to_admin ) {
        echo $gprduserlegalinfoo;
    }
}


}





//Since v.1.6.8 - Updated v.1.7.9

/*DISPLAY THE CHECKBOX IN WOOCOMMERCE REVIEWS*/



global $pluginOptionsVal;
$pluginOptionsVal=get_wshk_sidebar_options();
      if(isset($pluginOptionsVal['wshk_gprdreviews']) && $pluginOptionsVal['wshk_gprdreviews']==91)
{ 
    $urlpoli = '/' . get_option('wshk_gprdurlslug');
    
//Since v.1.6.8

/*WC CHECKBOX*/

add_action('comment_form_after_fields','my_revie_form_after_fields');
function my_revie_form_after_fields() {
  $readandaccepto = get_option('wshk_gprdiread');
 $urlpoli = '/' . get_option('wshk_gprdurlslug');
 $polilink = __('Policy Privacy', 'woo-shortcodes-kit');
  if ( is_product() ) {
?>
  <p class="form-row terms" style="font-size: 12px;">
  
  <input name="deliverycheck" id="deliverycheck" class="comment-form-policy__input" type="checkbox" required style="width:auto">
                <?php echo $readandaccepto ; ?>
                <a href="<?php echo $urlpoli;  ?>" target="_blank" class="comment-form-policy__see-more-link"><?php echo $polilink ;?></a>
  

</p>
  <?php
}   
}

//Since v.1.6.8

/*WC REVIEWS LEGAL TEXT*/

// define the woocommerce_review_after_comment_text callback 
function action_woocommerce_review_after_comment_text( $comment ) { 
    // make action magic happen here... 
  $gprduserlegalinfoo = get_option('wshk_gprduserlegalinfo');
  $gprdrewveri = get_option('wshk_gprdrewveri');
  
  
$gprdreviewsbdsize = get_option('wshk_gprdreviewsbdsize');
$gprdreviewsbdtype = get_option('wshk_gprdreviewsbdtype');
$gprdreviewsbdcolor = get_option('wshk_gprdreviewsbdcolor');
$gprdreviewsbdradius = get_option('wshk_gprdreviewsbdradius');
$gprdreviewspadding = get_option('wshk_gprdreviewspadding');
$gprdreviewsbgcolor = get_option('wshk_gprdreviewsbgcolor');
  
  
  if ( is_product() ) {
  ?>
	
	<br />
  <div style="border: <?php echo $gprdreviewsbdsize;?>px <?php echo $gprdreviewsbdtype;?> <?php echo $gprdreviewsbdcolor;?>;border-radius: <?php echo $gprdreviewsbdradius;?>px;padding:<?php echo $gprdreviewspadding;?>px;background-color:<?php echo $gprdreviewsbgcolor;?>;">
      <?php echo $gprdrewveri;?>
<!--<h4 style="letter-spacing: 1px;"><span class="fa fa-info-circle"></span> Información relativa a los datos que proporcionas al dejar tu valoración</h4>

<p><strong>Responsable:</strong> Alberto Gómez Orta</p>

<p><strong>Finalidad:</strong> moderación de valoraciones</p>

<p><strong>Legitimación:</strong> tu consentimiento, mediante marcación de botón.</p>

<p><strong>Destinatarios:</strong> servidores de Webempresa (actual hosting de esta web).</p>

<p><strong>Derechos:</strong> acceso, rectificación, limitación y/o supresión de tus datos.</p>--></div><br />
	
	<?php
	
  }
}; 
         
// add the action 
add_action( 'comment_form_after', 'action_woocommerce_review_after_comment_text', 50, 4 ); 







//Since v.1.6.8


/*WC REGISTER CHECKBOX*/



global $pluginOptionsVal;
$pluginOptionsVal=get_wshk_sidebar_options();
      if(isset($pluginOptionsVal['wshk_gprdwcregisterform']) && $pluginOptionsVal['wshk_gprdwcregisterform']==92)
{ 



add_action( 'woocommerce_register_form', 'wshk_add_registration_privacy_policy', 11 );
   
function wshk_add_registration_privacy_policy() {
    
    $readandaccepto = get_option('wshk_gprdiread');
 $urlpoli = '/' . get_option('wshk_gprdurlslug');
 $polilink = __('Policy Privacy', 'woo-shortcodes-kit');
 
woocommerce_form_field( 'privacy_policy_reg', array(
    'type'          => 'checkbox',
    'class'         => array('form-row privacy'),
    'label_class'   => array('woocommerce-form__label woocommerce-form__label-for-checkbox checkbox'),
    'input_class'   => array('woocommerce-form__input woocommerce-form__input-checkbox input-checkbox'),
    'required'      => true,
    'label'         => $readandaccepto . ' <a href="' . $urlpoli . '" target="_blank">' . $polilink . '</a>',
));
  
}
  
// Show error if user does not tick
   
add_filter( 'woocommerce_registration_errors', 'wshk_validate_privacy_registration', 10, 3 );
  
function wshk_validate_privacy_registration( $errors, $username, $email ) {
if ( ! is_checkout() ) {
    if ( ! (int) isset( $_POST['privacy_policy_reg'] ) ) {
        $errors->add( 'privacy_policy_reg_error', __( 'You must agree to our privacy term by checking the box', 'woo-shortcodes-kit') );
    }
}
return $errors;
}

}


//Since v.1.6.8

/*WC REGISTER FORM LEGAL TEXT*/

// define the woocommerce_review_after_comment_text callback 
function action_woocommerce_register_form() { 
    // make action magic happen here... 
  
  $gprdregveri = get_option('wshk_gprdregveri');
  
  
  
$gprdregisterbdsize = get_option('wshk_gprdregisterbdsize');
$gprdregisterbdtype = get_option('wshk_gprdregisterbdtype');
$gprdregisterbdcolor = get_option('wshk_gprdregisterbdcolor');
$gprdregisterbdradius = get_option('wshk_gprdregisterbdradius');
$gprdregisterpadding = get_option('wshk_gprdregisterpadding');
$gprdregisterbgcolor = get_option('wshk_gprdregisterbgcolor');
  
  ?>
	
	<br />
  <div style="border: <?php echo $gprdregisterbdsize;?>px <?php echo $gprdregisterbdtype;?> <?php echo $gprdregisterbdcolor;?>;border-radius: <?php echo $gprdregisterbdradius;?>px;padding: <?php echo $gprdregisterpadding;?>px;background-color:<?php echo $gprdregisterbgcolor;?>;">
      <?php echo $gprdregveri;?></div><br />
	
	<?php
	
  
}; 
         
// add the action 


add_action( 'woocommerce_register_form_end', 'action_woocommerce_register_form', 12 );





//Since v.1.6.8

/*SEND CUSTOM ADMIN EMAIL IF SOME USER MAKE A ACCOUNT @ REGISTER FORM VALIDATION*/

function wshk_customer_registration_email_alert( $user_id ) {
    
    $gprduserlegalinfoo = get_option('wshk_gprduserlegalinfo');
    $user    = get_userdata( $user_id );
    $first_name = null;
    $last_name = null;
    $role = $user->roles;
    $email   = $user->user_email;
    $miasunto = __('New Customer Registration', 'woo-shortcodes-kit');
    $admiemail = get_option( 'admin_email' );
   
    if ( isset( $_POST['billing_first_name'] ) ) {
        $first_name = $_POST['billing_first_name'];
    }
    if ( isset( $_POST['billing_last_name'] ) ) {
        $last_name = $_POST['billing_last_name'];
    }
    $message = sprintf( __('Rejoice someone loves us! 
    A new customer %1$s %2$s with the email %3$s has registered.
    
    ', 'woo-shortcodes-kit' ), $first_name, $last_name, $email ) . $gprduserlegalinfoo ;
    
    
   
    // If new account doesn't have the 'customer' role don't do anything.
    if( !in_array( 'customer', $role ) ) {
        return;
    }
    wp_mail( $admiemail , $miasunto , $message );
    
    
}
add_action( 'user_register', 'wshk_customer_registration_email_alert' );



}





//Since v.1.6.8

/*ADD NAME AND SURNAME FIELDS TO WC REGISTER FORM*/




global $pluginOptionsVal;
$pluginOptionsVal=get_wshk_sidebar_options();
      if(isset($pluginOptionsVal['wshk_wcregisterformfieldsextra']) && $pluginOptionsVal['wshk_wcregisterformfieldsextra']==93)
{ 




// 1. ADDING
 
add_action( 'woocommerce_register_form_start', 'wshk_add_name_woo_account_registration' );
 
function wshk_add_name_woo_account_registration() {
    ?>
 
    <p class="form-row form-row-first">
    <label for="reg_billing_first_name"><?php _e( 'First name', 'woocommerce' ); ?> <span class="required">*</span></label>
    <input type="text" class="input-text" name="billing_first_name" id="reg_billing_first_name" value="<?php if ( ! empty( $_POST['billing_first_name'] ) ) esc_attr_e( $_POST['billing_first_name'] ); ?>" />
    </p>
 
    <p class="form-row form-row-last">
    <label for="reg_billing_last_name"><?php _e( 'Last name', 'woocommerce' ); ?> <span class="required">*</span></label>
    <input type="text" class="input-text" name="billing_last_name" id="reg_billing_last_name" value="<?php if ( ! empty( $_POST['billing_last_name'] ) ) esc_attr_e( $_POST['billing_last_name'] ); ?>" />
    </p>
 
    <div class="clear"></div>
 
    <?php
}
 

// VALIDATING
 
add_filter( 'woocommerce_registration_errors', 'wshk_validate_name_fields', 10, 3 );
 
function wshk_validate_name_fields( $errors, $username, $email ) {
    if ( isset( $_POST['billing_first_name'] ) && empty( $_POST['billing_first_name'] ) ) {
        $errors->add( 'billing_first_name_error', __( '<strong>Error</strong>: First name is required!', 'woocommerce' ) );
    }
    if ( isset( $_POST['billing_last_name'] ) && empty( $_POST['billing_last_name'] ) ) {
        $errors->add( 'billing_last_name_error', __( '<strong>Error</strong>: Last name is required!.', 'woocommerce' ) );
    }
    return $errors;
}
 

// SAVING
 
add_action( 'woocommerce_created_customer', 'wshk_save_name_fields' );
 
function wshk_save_name_fields( $customer_id ) {
    if ( isset( $_POST['billing_first_name'] ) ) {
        update_user_meta( $customer_id, 'billing_first_name', sanitize_text_field( $_POST['billing_first_name'] ) );
        update_user_meta( $customer_id, 'first_name', sanitize_text_field($_POST['billing_first_name']) );
    }
    if ( isset( $_POST['billing_last_name'] ) ) {
        update_user_meta( $customer_id, 'billing_last_name', sanitize_text_field( $_POST['billing_last_name'] ) );
        update_user_meta( $customer_id, 'last_name', sanitize_text_field($_POST['billing_last_name']) );
    }
 
}

}


//Since v.1.6.8
/*ALTERNATIVE FOR WOOCOMMERCE TERMS AND CONDITIONS IN CHECKOUT PAGE*/




global $pluginOptionsVal;
$pluginOptionsVal=get_wshk_sidebar_options();
      if(isset($pluginOptionsVal['wshk_wcnewtermsbox']) && $pluginOptionsVal['wshk_wcnewtermsbox']==94)
{ 




add_action( 'woocommerce_review_order_before_submit', 'wshk_add_checkout_privacy_policy', 9 );
   
function wshk_add_checkout_privacy_policy() {
  $termstexto = get_option('wshk_termstexto');
  $termslink = '/' . get_option('wshk_termslink');
  $termstextlink = get_option('wshk_termstextlink');
woocommerce_form_field( 'privacy_policy', array(
    'type'          => 'checkbox',
    'class'         => array('form-row privacy'),
    'label_class'   => array('woocommerce-form__label woocommerce-form__label-for-checkbox checkbox'),
    'input_class'   => array('woocommerce-form__input woocommerce-form__input-checkbox input-checkbox'),
    'required'      => true,
    'label'         => $termstexto . ' <a href="' . $termslink .'" target="_blank">' . $termstextlink . '</a>',
)); 
  
}
  
// Show notice if customer does not tick
   
add_action( 'woocommerce_checkout_process', 'wshk_not_approved_privacy' );
  
function wshk_not_approved_privacy() {
    if ( ! (int) isset( $_POST['privacy_policy'] ) ) {
        wc_add_notice( __( 'You must accept the web conditions to continue', 'woo-shortcodes-kit' ), 'error' );
    }
}
 
}


//Since v.1.6.8 
//CHECK IF CUSTOM REDIRECTIONS IS ACTIVE

if ( in_array( 'custom-redirections-for-wshk/custom-redirections-for-whsk.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    
    
//Since v.1.7.2
//WOOCOMMERCE SUBSCRIPTIONS COMPATIBILITY*/
/* Plugin URL: https://woocommerce.com/products/woocommerce-subscriptions */

//Since v.1.7.8
if(isset($pluginOptionsVal['wshk_enablesubscription']) && $pluginOptionsVal['wshk_enablesubscription']==3002)
{


if ( in_array( 'woocommerce-subscriptions/woocommerce-subscriptions.php' || 'woocommerce-subscriptions-master/woocommerce-subscriptions.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    
      


//Since v.1.7.2
//WooCommerce Subscriptions compatibility - related-orders

function wshk_relatord_get_template( $located, $template_name, $args, $template_path, $default_path ) {    
    if ( 'myaccount/related-orders.php' == $template_name ) {
         
        $located = plugin_dir_path( __FILE__ ) . '/mytemplates/related-orders.php';
        
    }
    
    return $located;
    
}
add_filter( 'wc_get_template', 'wshk_relatord_get_template', 10, 5 );


//Since v.1.7.2
//WooCommerce Subscriptions compatibility - related-subscriptions

function wshk_relatsub_get_template( $located, $template_name, $args, $template_path, $default_path ) {    
    if ( 'myaccount/related-subscriptions.php' == $template_name ) {
         
        $located = plugin_dir_path( __FILE__ ) . '/mytemplates/related-subscriptions.php';
        
    }
    
    return $located;
    
}
add_filter( 'wc_get_template', 'wshk_relatsub_get_template', 10, 5 );


//Since v.1.8.2
function wshk_mysub_get_template( $located, $template_name, $args, $template_path, $default_path ) {    
    if ( 'myaccount/my-subscriptions.php' == $template_name ) {
         
        $located = plugin_dir_path( __FILE__ ) . '/mytemplates/my-subscriptions.php';
        
    }
    
    return $located;
    
}
add_filter( 'wc_get_template', 'wshk_mysub_get_template', 10, 5 );



}
}

/*END WOO SUBSCRIPTIONS COMPATIBILITY*/




//Since v.1.8.2

/*SHOW THE SUBSCRIPTIONS*/
//Display the Woo subscriptions table, If you want display in any page or post, use this shortcode [woo_subscriptions]

//Since v.1.7.8
if(isset($pluginOptionsVal['wshk_enablesubscriptionshortcode']) && $pluginOptionsVal['wshk_enablesubscriptionshortcode']==3003)
{


function wshk_newstyle_mysubscriptions() {
    /*wc_get_template( 'myaccount/form-edit-account.php', array( 'user' => get_user_by( 'id', get_current_user_id() ) ) ); */
    
   
    if (  is_user_logged_in() && ( is_account_page() ) ) {
ob_start();
    //require dirname( __FILE__ ) . '/mytemplates/my-subscriptions.php';
    echo do_shortcode('[subscriptions]');
    global $wp;

    if ( ! empty( $wp->query_vars ) ) {
      foreach ( $wp->query_vars as $key => $value ) {
        // Ignore pagename param.
        if ( 'edit-address' === $key ) {
          continue;
        }
        
        if ( 'add-payment-method' === $key ) {
          continue;
        }
        
        if ( 'view-order' === $key ) {
          continue;
        }
        
        if ( 'payment-methods' === $key ) {
          continue;
        }


        if ( has_action( 'woocommerce_account_' . $key . '_endpoint' ) ) {
          do_action( 'woocommerce_account_' . $key . '_endpoint', $value );
          return ob_get_clean();
          
        }
      }
    }

    // No endpoint found? Default to dashboard.
    /*wc_get_template( 'myaccount/', array(
      'current_user' => get_user_by( 'id', get_current_user_id() ),
    ) );*/
    return ob_get_clean();
} 
}
add_shortcode ('woo_mysubscriptions', 'wshk_newstyle_mysubscriptions');

//Sustituir plantilla del tema por la del plugin
add_filter( 'wc_get_template', 'wshk_cma_get_templateesub', 10, 5 );
function wshk_cma_get_templateesub( $located, $template_name, $args, $template_path, $default_path ) {    
    if ( 'myaccount/view-subscription.php' == $template_name ) {
        $located = plugin_dir_path( __FILE__ ) . '/mytemplates/view-subscription.php';
    }
    
    return $located;
}

}
}












//START BILLING USER DATA

global $pluginOptionsVal;
$pluginOptionsVal=get_wshk_sidebar_options();
      if(isset($pluginOptionsVal['wshk_enablebillinguserdata']) && $pluginOptionsVal['wshk_enablebillinguserdata']==819)
{ 


//Since 1.7.3
//Display Billing user data

//ID
function wshk_billing_id(){
  global $wpdb;
  $customer = wp_get_current_user();
  
  $billingid = $customer->ID;
  ob_start();
  echo $billingid;  
  return ob_get_clean();
}

add_shortcode('woo-billing-id','wshk_billing_id');

//NAME
function wshk_billing_user_name(){
  global $wpdb;
  $customer = wp_get_current_user();
  
  
  $billingname = $customer->billing_first_name;
  ob_start();
  echo $billingname; 
  return ob_get_clean();
}

add_shortcode('woo-billing-name','wshk_billing_user_name');


//LAST NAME
function wshk_billing_lastname(){
  global $wpdb;
  $customer = wp_get_current_user();
  
  
  $billinglastname = $customer->billing_last_name;
  ob_start();
  echo $billinglastname;
  return ob_get_clean();
}

add_shortcode('woo-billing-lastname','wshk_billing_lastname');


//ADDRESS
function wshk_billing_address(){
  global $wpdb;
  $customer = wp_get_current_user();
  
  
  $billingaddress = $customer->billing_address_1;
  ob_start();
  echo $billingaddress;
  return ob_get_clean();
}

add_shortcode('woo-billing-address','wshk_billing_address');


//POSTCODE
function wshk_billing_postcode(){
  global $wpdb;
  $customer = wp_get_current_user();
  
  
  $billingpostcode = $customer->billing_postcode;
  ob_start();
  echo $billingpostcode;
  return ob_get_clean();
}

add_shortcode('woo-billing-postcode','wshk_billing_postcode');


//CITY
function wshk_billing_city(){
  global $wpdb;
  $customer = wp_get_current_user();
  
 
  $billingcity = $customer->billing_city;
  ob_start();
  echo $billingcity;
  return ob_get_clean();
}

add_shortcode('woo-billing-city','wshk_billing_city');


//PHONE
function wshk_billing_phone(){
  global $wpdb;
  $customer = wp_get_current_user();
  
  
  $billingphone = $customer->billing_phone;
  ob_start();
  echo $billingphone;
  return ob_get_clean();
}

add_shortcode('woo-billing-phone','wshk_billing_phone');


//EMAIL
function wshk_billing_email(){
  global $wpdb;
  $customer = wp_get_current_user();
  
  
  $billingemail = $customer->billing_email;
  ob_start();
  echo $billingemail;
  return ob_get_clean();
}

add_shortcode('woo-billing-email','wshk_billing_email');

}

global $pluginOptionsVal;
$pluginOptionsVal=get_wshk_sidebar_options();
      if(isset($pluginOptionsVal['wshk_enableshippinguserdata']) && $pluginOptionsVal['wshk_enableshippinguserdata']==820)
{ 

//Since 1.7.3
//Display Shipping user data

//ID
function wshk_shipping_id(){
  global $wpdb;
  $customer = wp_get_current_user();
  
  $shippingid = $customer->ID;
  ob_start();
  echo $shippingid;  
  return ob_get_clean();
}

add_shortcode('woo-shipping-id','wshk_shipping_id');

//NAME
function wshk_shipping_user_name(){
  global $wpdb;
  $customer = wp_get_current_user();
  
  
  $shippingname = $customer->shipping_first_name;
  ob_start();
  echo $shippingname; 
  return ob_get_clean();
}

add_shortcode('woo-shipping-name','wshk_shipping_user_name');


//LAST NAME
function wshk_shipping_lastname(){
  global $wpdb;
  $customer = wp_get_current_user();
  
  
  $shippinglastname = $customer->shipping_last_name;
  ob_start();
  echo $shippinglastname;
  return ob_get_clean();
}

add_shortcode('woo-shipping-lastname','wshk_shipping_lastname');


//ADDRESS
function wshk_shipping_address(){
  global $wpdb;
  $customer = wp_get_current_user();
  
  
  $shippingaddress = $customer->shipping_address_1;
  ob_start();
  echo $shippingaddress;
  return ob_get_clean();
}

add_shortcode('woo-shipping-address','wshk_shipping_address');


//POSTCODE
function wshk_shipping_postcode(){
  global $wpdb;
  $customer = wp_get_current_user();
  
  
  $shippingpostcode = $customer->shipping_postcode;
  ob_start();
  echo $shippingpostcode;
  return ob_get_clean();
}

add_shortcode('woo-shipping-postcode','wshk_shipping_postcode');


//CITY
function wshk_shipping_city(){
  global $wpdb;
  $customer = wp_get_current_user();
  
 
  $shippingcity = $customer->shipping_city;
  ob_start();
  echo $shippingcity;
  return ob_get_clean();
}

add_shortcode('woo-shipping-city','wshk_shipping_city');


//PHONE
function wshk_shipping_phone(){
  global $wpdb;
  $customer = wp_get_current_user();
  
  
  $shippingphone = $customer->shipping_phone;
  ob_start();
  echo $shippingphone;
  return ob_get_clean();
}

add_shortcode('woo-shipping-phone','wshk_shipping_phone');


//EMAIL
function wshk_shipping_email(){
  global $wpdb;
  $customer = wp_get_current_user();
  
  
  $shippingemail = $customer->shipping_email;
  ob_start();
  echo $shippingemail;
  return ob_get_clean();
}

add_shortcode('woo-shipping-email','wshk_shipping_email');
}



global $pluginOptionsVal;
$pluginOptionsVal=get_wshk_sidebar_options();
      if(isset($pluginOptionsVal['wshk_enabletotalspender']) && $pluginOptionsVal['wshk_enabletotalspender']==8821)
{ 

//Since 1.7.3
//Display user total spended

//TOTAL BALANCE
function wshk_new_test_balance(){
  // Get all customer orders
    $customer_orders = get_posts( array(
        'numberposts' => -1,
        'meta_key'    => '_customer_user',
        'meta_value'  => get_current_user_id(),
        'post_type'   => array( 'shop_order' ),
        'post_status' => array_keys( wc_get_order_statuses() ),
    ) );
    
    $total = 0;
    foreach ( $customer_orders as $customer_order ) {
        $order = wc_get_order( $customer_order );
        $total += $order->get_total();
    }

    return $total;
  
}
add_shortcode('woo-total-balance','wshk_new_test_balance');



//PENDING
function wshk_new_test_pending(){
  // Get all customer orders
    $customer_orders = get_posts( array(
        'numberposts' => -1,
        'meta_key'    => '_customer_user',
        'meta_value'  => get_current_user_id(),
        'post_type'   => array( 'shop_order' ),
        'post_status' => array( 'wc-pending' )
    ) );
    
    $total = 0;
    foreach ( $customer_orders as $customer_order ) {
        $order = wc_get_order( $customer_order );
        $total += $order->get_total();
    }

    return $total;
  
}
add_shortcode('woo-total-orders-pending','wshk_new_test_pending');



//ON HOLD
function wshk_new_test_on_hold(){
  // Get all customer orders
    $customer_orders = get_posts( array(
        'numberposts' => -1,
        'meta_key'    => '_customer_user',
        'meta_value'  => get_current_user_id(),
        'post_type'   => array( 'shop_order' ),
        'post_status' => array( 'wc-on-hold' )
    ) );
    
    $total = 0;
    foreach ( $customer_orders as $customer_order ) {
        $order = wc_get_order( $customer_order );
        $total += $order->get_total();
    }

    return $total;
  
}
add_shortcode('woo-total-orders-on-hold','wshk_new_test_on_hold');



//PROCESSING
function wshk_new_test_processing(){
  // Get all customer orders
    $customer_orders = get_posts( array(
        'numberposts' => -1,
        'meta_key'    => '_customer_user',
        'meta_value'  => get_current_user_id(),
        'post_type'   => array( 'shop_order' ),
        'post_status' => array( 'wc-processing' )
    ) );
    
    $total = 0;
    foreach ( $customer_orders as $customer_order ) {
        $order = wc_get_order( $customer_order );
        $total += $order->get_total();
    }

    return $total;
  
}
add_shortcode('woo-total-orders-processing','wshk_new_test_processing');



//COMPLETED
function wshk_new_test_completed(){
  // Get all customer orders
    $customer_orders = get_posts( array(
        'numberposts' => -1,
        'meta_key'    => '_customer_user',
        'meta_value'  => get_current_user_id(),
        'post_type'   => array( 'shop_order' ),
        'post_status' => array( 'wc-completed' )
    ) );
    
    $total = 0;
    foreach ( $customer_orders as $customer_order ) {
        $order = wc_get_order( $customer_order );
        $total += $order->get_total();
    }

    return $total;
  
}
add_shortcode('woo-total-orders-completed','wshk_new_test_completed');



//CANCELLED
function wshk_new_test_cancelled(){
  // Get all customer orders
    $customer_orders = get_posts( array(
        'numberposts' => -1,
        'meta_key'    => '_customer_user',
        'meta_value'  => get_current_user_id(),
        'post_type'   => array( 'shop_order' ),
        'post_status' => array( 'wc-cancelled' )
    ) );
    
    $total = 0;
    foreach ( $customer_orders as $customer_order ) {
        $order = wc_get_order( $customer_order );
        $total += $order->get_total();
    }

    return $total;
  
}
add_shortcode('woo-total-orders-cancelled','wshk_new_test_cancelled');



//REFUNDED
function wshk_new_test_refunded(){
  // Get all customer orders
    $customer_orders = get_posts( array(
        'numberposts' => -1,
        'meta_key'    => '_customer_user',
        'meta_value'  => get_current_user_id(),
        'post_type'   => array( 'shop_order' ),
        'post_status' => array( 'wc-refunded' )
    ) );
    
    $total = 0;
    foreach ( $customer_orders as $customer_order ) {
        $order = wc_get_order( $customer_order );
        $total += $order->get_total();
    }

    return $total;
  
}
add_shortcode('woo-total-orders-refunded','wshk_new_test_refunded');


//FAILED
function wshk_new_test_failed(){
  // Get all customer orders
    $customer_orders = get_posts( array(
        'numberposts' => -1,
        'meta_key'    => '_customer_user',
        'meta_value'  => get_current_user_id(),
        'post_type'   => array( 'shop_order' ),
        'post_status' => array( 'wc-failed' )
    ) );
    
    $total = 0;
    foreach ( $customer_orders as $customer_order ) {
        $order = wc_get_order( $customer_order );
        $total += $order->get_total();
    }

    return $total;
  
}
add_shortcode('woo-total-orders-failed','wshk_new_test_failed');

}




global $pluginOptionsVal;
$pluginOptionsVal=get_wshk_sidebar_options();
      if(isset($pluginOptionsVal['wshk_enableordercountser']) && $pluginOptionsVal['wshk_enableordercountser']==8822)
{ 

//Since 1.7.3
//Display user orders quantity

//TOTAL ORDERS COUNT

function wshk_test_get_customer_order_count() {
  
   // Get all customer orders
    $customer_orders = get_posts( array(
        'numberposts' => -1,
        'meta_key'    => '_customer_user',
        'meta_value'  => get_current_user_id(),
        'post_type'   => wc_get_order_types(),
        'post_status' => array_keys( wc_get_order_statuses() ),
    ) );
    
    $customer = wp_get_current_user();  
   
    // count orders
   
  	$testmytest = count($customer_orders);
    
    // Display total order number
   ob_start();
	  echo $testmytest; 
  return ob_get_clean();
}

add_shortcode('woo-order-count','wshk_test_get_customer_order_count');


//PENDING
function wshk_test_get_customer_order_pending_count() {
  
   // Get all customer orders
    $customer_orders = get_posts( array(
        'numberposts' => -1,
        'meta_key'    => '_customer_user',
        'meta_value'  => get_current_user_id(),
        'post_type'   => wc_get_order_types(),
        'post_status' => array( 'wc-pending' ),
    ) );
    
    $customer = wp_get_current_user();  
   
    // count orders
   
  	$testmytestpend = count($customer_orders);
    
    // Display total order number
   ob_start();
	  echo $testmytestpend; 
  return ob_get_clean();
}

add_shortcode('woo-order-count-pending','wshk_test_get_customer_order_pending_count');



//ON HOLD
function wshk_test_get_customer_order_onhold_count() {
  
   // Get all customer orders
    $customer_orders = get_posts( array(
        'numberposts' => -1,
        'meta_key'    => '_customer_user',
        'meta_value'  => get_current_user_id(),
        'post_type'   => wc_get_order_types(),
        'post_status' => array( 'wc-on-hold' ),
    ) );
    
    $customer = wp_get_current_user();  
   
    // count orders
   
  	$testmytestpen = count($customer_orders);
    
    // Display total order number
   ob_start();
	  echo $testmytestpen; 
  return ob_get_clean();
}

add_shortcode('woo-order-count-onhold','wshk_test_get_customer_order_onhold_count');



//PROCESSING
function wshk_test_get_customer_order_process_count() {
  
   // Get all customer orders
    $customer_orders = get_posts( array(
        'numberposts' => -1,
        'meta_key'    => '_customer_user',
        'meta_value'  => get_current_user_id(),
        'post_type'   => wc_get_order_types(),
        'post_status' => array( 'wc-processing' ),
    ) );
    
    $customer = wp_get_current_user();  
   
    // count orders
   
  	$testmytestpro = count($customer_orders);
    
    // Display total order number
   ob_start();
	  echo $testmytestpro; 
  return ob_get_clean();
}

add_shortcode('woo-order-count-process','wshk_test_get_customer_order_process_count');



//COMPLETED
function wshk_test_get_customer_order_completed_count() {
  
   // Get all customer orders
    $customer_orders = get_posts( array(
        'numberposts' => -1,
        'meta_key'    => '_customer_user',
        'meta_value'  => get_current_user_id(),
        'post_type'   => wc_get_order_types(),
        'post_status' => array( 'wc-completed' ),
    ) );
    
    $customer = wp_get_current_user();  
   
    // count orders
   
  	$testmytestcomp = count($customer_orders);
    
    // Display total order number
   ob_start();
	  echo $testmytestcomp; 
  return ob_get_clean();
}

add_shortcode('woo-order-count-completed','wshk_test_get_customer_order_completed_count');



//CANCELLED
function wshk_test_get_customer_order_cancelled_count() {
  
   // Get all customer orders
    $customer_orders = get_posts( array(
        'numberposts' => -1,
        'meta_key'    => '_customer_user',
        'meta_value'  => get_current_user_id(),
        'post_type'   => wc_get_order_types(),
        'post_status' => array( 'wc-cancelled' ),
    ) );
    
    $customer = wp_get_current_user();  
   
    // count orders
   
  	$testmytestcan = count($customer_orders);
    
    // Display total order number
   ob_start();
	  echo $testmytestcan; 
  return ob_get_clean();
}

add_shortcode('woo-order-count-cancelled','wshk_test_get_customer_order_cancelled_count');



//REFUNDED
function wshk_test_get_customer_order_refunded_count() {
  
   // Get all customer orders
    $customer_orders = get_posts( array(
        'numberposts' => -1,
        'meta_key'    => '_customer_user',
        'meta_value'  => get_current_user_id(),
        'post_type'   => wc_get_order_types(),
        'post_status' => array( 'wc-refunded' ),
    ) );
    
    $customer = wp_get_current_user();  
   
    // count orders
   
  	$testmytestrefu = count($customer_orders);
    
    // Display total order number
   ob_start();
	  echo $testmytestrefu; 
  return ob_get_clean();
}

add_shortcode('woo-order-count-refunded','wshk_test_get_customer_order_refunded_count');



//FAILED

function wshk_test_get_customer_order_failed_count() {
  
   // Get all customer orders
    $customer_orders = get_posts( array(
        'numberposts' => -1,
        'meta_key'    => '_customer_user',
        'meta_value'  => get_current_user_id(),
        'post_type'   => wc_get_order_types(),
        'post_status' => array( 'wc-failed' ),
    ) );
    
    $customer = wp_get_current_user();  
   
    // count orders
   
  	$testmytestfail = count($customer_orders);
    
    // Display total order number
   ob_start();
	  echo $testmytestfail; 
  return ob_get_clean();
}

add_shortcode('woo-order-count-failed','wshk_test_get_customer_order_failed_count');

}


global $pluginOptionsVal;
$pluginOptionsVal=get_wshk_sidebar_options();
      if(isset($pluginOptionsVal['wshk_enableproimage']) && $pluginOptionsVal['wshk_enableproimage']==8833)
{ 

//Since v.1.7.3
//Display product image in order details

add_filter( 'woocommerce_order_item_name', 'wshk_display_product_image_on_order_view_myaccount', 20, 3 );
function wshk_display_product_image_on_order_view_myaccount( $item_name, $item, $is_visible ) {

    if( is_wc_endpoint_url( 'view-order' ) ) {
        
        
    $prodimagesize = get_option('wshk_prodimagesize');
    $prodbordsize = get_option('wshk_prodimagebordsize');
    $prodbordtype = get_option('wshk_prodimagebordtype');
    $prodbordcolor = get_option('wshk_prodimagebordcolor');
    $prodbordradius = get_option('wshk_prodimagebordradius');
    
    $product   = $item->get_product();
    $thumbnail = $product->get_image(array( $prodimagesize, $prodimagesize)); // change width and height into whatever you like
    if( $product->get_image_id() > 0 )
    $item_name = '<style>div.item-thumbnail > span > img[class*=attachment-] {border:'.$prodbordsize.'px '.$prodbordtype.' '.$prodbordcolor.'; border-radius:'.$prodbordradius.'%;}</style><div class="item-thumbnail"><span style="margin-right:16px;">' . $thumbnail . '</span></div>' . $item_name;
    }
    return $item_name;
}
}









//Since v.1.7.9 - Updated v.1.8.0
//CUSTOM META BOX

add_action('admin_init','wshk_meta_init');
function wshk_meta_init()
{

   $accountpgid = get_option( 'woocommerce_myaccount_page_id' ); 
   $id = !empty($_POST['post_id']) ? $_POST['post_id'] : '';
   $post_id = !empty($_GET['post']) ? $_GET['post'] : $id ;
   
   // checks for post/page ID
   if ($post_id == $accountpgid)
   //if ($accountpgid == get_option( 'woocommerce_myaccount_page_id' ))
   {
   $wshkstringmetatitle = __( 'WSHK - BUILD YOUR CUSTOM ACCOUNT PAGE SHORTCODES STATUS', 'woo-shortcodes-kit' );
   add_meta_box('wshk_all_meta_1', $wshkstringmetatitle, 'wshk_meta_setup_1',   'page', 'normal', 'high');
   }
   
//add_action('save_post','wshk_meta_init');
}

function wshk_meta_setup_1()
{
	global $pluginOptionsVal;
$pluginOptionsVal=get_wshk_sidebar_options();
$wshkenabledteststring = __( 'ENABLED', 'woo-shortcodes-kit' );
$wshkdisabledteststring = __( 'DISABLED', 'woo-shortcodes-kit' );
//ORDERS LIST

if(isset($pluginOptionsVal['wshk_enableorderscontrol']) && $pluginOptionsVal['wshk_enableorderscontrol']==140)
{  
   
    
    $wshkaccstatusordlist = '<span style="color:green !important;">'.$wshkenabledteststring.' <span class="dashicons dashicons-yes"></span></span>';
} else {
    
    $wshkaccstatusordlist = '<span style="color:red !important;">'.$wshkdisabledteststring.' <span class="dashicons dashicons-no-alt"></span></span>';
    
}


//DOWNLOADS LIST

if(isset($pluginOptionsVal['wshk_enablemydownloadsht']) && $pluginOptionsVal['wshk_enablemydownloadsht']==2000)
{  
    //echo 'DASHBOARD SHORTCODE: ENABLE';
    $wshkaccstatusdowlist = '<span style="color:green !important;">'.$wshkenabledteststring.' <span class="dashicons dashicons-yes"></span></span>';
} else {
    
    $wshkaccstatusdowlist = '<span style="color:red !important;">'.$wshkdisabledteststring.' <span class="dashicons dashicons-no-alt"></span></span>';
    
}


//ADDRESSES

if(isset($pluginOptionsVal['wshk_enablemyaddressessht']) && $pluginOptionsVal['wshk_enablemyaddressessht']==2001)
{  
    //echo 'DASHBOARD SHORTCODE: ENABLE';
    $wshkaccstatusaddresses = '<span style="color:green !important;">'.$wshkenabledteststring.' <span class="dashicons dashicons-yes"></span></span>';
} else {
    
    $wshkaccstatusaddresses = '<span style="color:red !important;">'.$wshkdisabledteststring.' <span class="dashicons dashicons-no-alt"></span></span>';
    
}


//PAYMENTS METHODS

if(isset($pluginOptionsVal['wshk_enablemypaymentsht']) && $pluginOptionsVal['wshk_enablemypaymentsht']==2002)
{  
    //echo 'DASHBOARD SHORTCODE: ENABLE';
    $wshkaccstatuspayments = '<span style="color:green !important;">'.$wshkenabledteststring.' <span class="dashicons dashicons-yes"></span></span>';
} else {
    
    $wshkaccstatuspayments = '<span style="color:red !important;">'.$wshkdisabledteststring.' <span class="dashicons dashicons-no-alt"></span></span>';
    
}


//EDIT ACCOUNT

if(isset($pluginOptionsVal['wshk_enablemyeditaccsht']) && $pluginOptionsVal['wshk_enablemyeditaccsht']==2003)
{  
    //echo 'DASHBOARD SHORTCODE: ENABLE';
    $wshkaccstatusedaccount = '<span style="color:green !important;">'.$wshkenabledteststring.' <span class="dashicons dashicons-yes"></span></span>';
} else {
    
    $wshkaccstatusedaccount = '<span style="color:red !important;">'.$wshkdisabledteststring.' <span class="dashicons dashicons-no-alt"></span></span>';
    
}


//DASHBOARD

	if(isset($pluginOptionsVal['wshk_enabledashbsht']) && $pluginOptionsVal['wshk_enabledashbsht']==2004)
{  
    //echo 'DASHBOARD SHORTCODE: ENABLE';
    $wshkaccstatus = '<span style="color:green !important;">'.$wshkenabledteststring.' <span class="dashicons dashicons-yes"></span></span>';
} else {
    
    $wshkaccstatus = '<span style="color:red !important;">'.$wshkdisabledteststring.' <span class="dashicons dashicons-no-alt"></span></span>';
    
}



//USER GRAVATAR IMAGE

	if(isset($pluginOptionsVal['wshk_enablegravatar']) && $pluginOptionsVal['wshk_enablegravatar']==15)
{  
    //echo 'DASHBOARD SHORTCODE: ENABLE';
    $wshkaccstatusgravatar = '<span style="color:green !important;">'.$wshkenabledteststring.' <span class="dashicons dashicons-yes"></span></span>';
} else {
    
    $wshkaccstatusgravatar = '<span style="color:red !important;">'.$wshkdisabledteststring.' <span class="dashicons dashicons-no-alt"></span></span>';
    
}



//USERNAME

	if(isset($pluginOptionsVal['wshk_enableusername']) && $pluginOptionsVal['wshk_enableusername']==11)
{  
    //echo 'DASHBOARD SHORTCODE: ENABLE';
    $wshkaccstatususername = '<span style="color:green !important;">'.$wshkenabledteststring.' <span class="dashicons dashicons-yes"></span></span>';
} else {
    
    $wshkaccstatususername = '<span style="color:red !important;">'.$wshkdisabledteststring.' <span class="dashicons dashicons-no-alt"></span></span>';
    
}



//LOGOUT

	if(isset($pluginOptionsVal['wshk_enablelogoutbtn']) && $pluginOptionsVal['wshk_enablelogoutbtn']==12)
{  
    //echo 'DASHBOARD SHORTCODE: ENABLE';
    $wshkaccstatuslogout = '<span style="color:green !important;">'.$wshkenabledteststring.' <span class="dashicons dashicons-yes"></span></span>';
} else {
    
    $wshkaccstatuslogout = '<span style="color:red !important;">'.$wshkdisabledteststring.' <span class="dashicons dashicons-no-alt"></span></span>';
    
}



//LOGIN/REGISTER FORM

	if(isset($pluginOptionsVal['wshk_enableloginform']) && $pluginOptionsVal['wshk_enableloginform']==13)
{  
    //echo 'DASHBOARD SHORTCODE: ENABLE';
    $wshkaccstatusloginf = '<span style="color:green !important;">'.$wshkenabledteststring.' <span class="dashicons dashicons-yes"></span></span>';
} else {
    
    $wshkaccstatusloginf = '<span style="color:red !important;">'.$wshkdisabledteststring.' <span class="dashicons dashicons-no-alt"></span></span>';
    
}



//CUSTOMER REVIEWS WITH PRODUCT LINK

	if(isset($pluginOptionsVal['wshk_enablereviews']) && $pluginOptionsVal['wshk_enablereviews']==9)
{  
    //echo 'DASHBOARD SHORTCODE: ENABLE';
    $wshkaccstatuscusreview = '<span style="color:green !important;">'.$wshkenabledteststring.' <span class="dashicons dashicons-yes"></span></span>';
} else {
    
    $wshkaccstatuscusreview = '<span style="color:red !important;">'.$wshkdisabledteststring.' <span class="dashicons dashicons-no-alt"></span></span>';
    
}



//USER IP

	if(isset($pluginOptionsVal['wshk_enabletheipsht']) && $pluginOptionsVal['wshk_enabletheipsht']==2005)
{  
    //echo 'DASHBOARD SHORTCODE: ENABLE';
    $wshkaccstatususerip = '<span style="color:green !important;">'.$wshkenabledteststring.' <span class="dashicons dashicons-yes"></span></span>';
} else {
    
    $wshkaccstatususerip = '<span style="color:red !important;">'.$wshkdisabledteststring.' <span class="dashicons dashicons-no-alt"></span></span>';
    
}



//USER NAME AND SURNAME

	if(isset($pluginOptionsVal['wshk_enablethenamesurnsht']) && $pluginOptionsVal['wshk_enablethenamesurnsht']==2006)
{  
    //echo 'DASHBOARD SHORTCODE: ENABLE';
    $wshkaccstatusnamesurname = '<span style="color:green !important;">'.$wshkenabledteststring.' <span class="dashicons dashicons-yes"></span></span>';
} else {
    
    $wshkaccstatusnamesurname = '<span style="color:red !important;">'.$wshkdisabledteststring.' <span class="dashicons dashicons-no-alt"></span></span>';
    
}



//USER EMAIL

	if(isset($pluginOptionsVal['wshk_enabletheuseremailsht']) && $pluginOptionsVal['wshk_enabletheuseremailsht']==2007)
{  
    //echo 'DASHBOARD SHORTCODE: ENABLE';
    $wshkaccstatususeremail = '<span style="color:green !important;">'.$wshkenabledteststring.' <span class="dashicons dashicons-yes"></span></span>';
} else {
    
    $wshkaccstatususeremail = '<span style="color:red !important;">'.$wshkdisabledteststring.' <span class="dashicons dashicons-no-alt"></span></span>';
    
}



     ?>
    <table width="100%" cellpadding="10">
        <tr style="font-weight:bold;">
            <td width="53%"><?php esc_html_e( 'FUNCTION', 'woo-shortcodes-kit' ); ?></td>
            <td width="30%">SHORTCODE</td>
            <td width="17%"><?php esc_html_e( 'STATUS', 'woo-shortcodes-kit' ); ?></td>
        </tr>
        
        <tr>
            <td><?php esc_html_e( 'ORDERS LIST SHORTCODE', 'woo-shortcodes-kit' ); ?></td>
            <td>[woo_myorders]</td>
            <td><?php echo $wshkaccstatusordlist ?></td>
        </tr>
        
        <tr>
            <td><?php esc_html_e( 'DOWNLOADS LIST SHORTCODE', 'woo-shortcodes-kit' ); ?></td>
            <td>[woo_mydownloads]</td>
            <td><?php echo $wshkaccstatusdowlist ?></td>
        </tr>
        
        <tr>
            <td><?php esc_html_e( 'BILLING & SHIPPING ADDRESSES SHORTCODE', 'woo-shortcodes-kit' ); ?></td>
            <td>[woo_myaddress]</td>
            <td><?php echo $wshkaccstatusaddresses ?></td>
        </tr>
        
        <tr>
            <td><?php esc_html_e( 'PAYMENTS METHODS SHORTCODE', 'woo-shortcodes-kit' ); ?></td>
            <td>[woo_mypayment]</td>
            <td><?php echo $wshkaccstatuspayments ?></td>
        </tr>
        
        <tr>
            <td><?php esc_html_e( 'EDIT ACCOUNT SHORTCODE', 'woo-shortcodes-kit' ); ?></td>
            <td>[woo_myedit_account]</td>
            <td><?php echo $wshkaccstatusedaccount ?></td>
        </tr>
        
        <tr>
            <td><?php esc_html_e( 'DASHBOARD SHORTCODE', 'woo-shortcodes-kit' ); ?></td>
            <td>[woo_mydashboard]</td>
            <td><?php echo $wshkaccstatus ?></td>
        </tr>
        
        <tr>
            <td><?php esc_html_e( 'USER GRAVATAR IMAGE SHORTCODE', 'woo-shortcodes-kit' ); ?></td>
            <td>[woo_gravatar_image]</td>
            <td><?php echo $wshkaccstatusgravatar ?></td>
        </tr>
        
        <tr>
            <td><?php esc_html_e( 'USERNAME SHORTCODE', 'woo-shortcodes-kit' ); ?></td>
            <td>[woo_user_name]</td>
            <td><?php echo $wshkaccstatususername ?></td>
        </tr>
        
        <tr>
            <td><?php esc_html_e( 'LOGOUT BUTTON SHORTCODE', 'woo-shortcodes-kit' ); ?></td>
            <td>[woo_logout_button]</td>
            <td><?php echo $wshkaccstatuslogout ?></td>
        </tr>
        
        <tr>
            <td><?php esc_html_e( 'LOGIN/REGISTER FORM SHORTCODE', 'woo-shortcodes-kit' ); ?></td>
            <td>[woo_login_form]</td>
            <td><?php echo $wshkaccstatusloginf ?></td>
        </tr>
        
        <tr>
            <td><?php esc_html_e( 'CUSTOMER REVIEWS WITH PRODUCT LINK SHORTCODE', 'woo-shortcodes-kit' ); ?></td>
            <td>[woo_review_products]</td>
            <td><?php echo $wshkaccstatuscusreview ?></td>
        </tr>
        
        <tr>
            <td><?php esc_html_e( 'USER IP SHORTCODE', 'woo-shortcodes-kit' ); ?></td>
            <td>[woo_display_ip]</td>
            <td><?php echo $wshkaccstatususerip ?></td>
        </tr>
        
        <tr>
            <td><?php esc_html_e( 'USER NAME AND SURNAME SHORTCODE', 'woo-shortcodes-kit' ); ?></td>
            <td>[woo_display_nsurname]</td>
            <td><?php echo $wshkaccstatusnamesurname ?></td>
        </tr>
        
        <tr>
            <td><?php esc_html_e( 'USER EMAIL SHORTCODE', 'woo-shortcodes-kit' ); ?></td>
            <td>[woo_display_email]</td>
            <td><?php echo $wshkaccstatususeremail ?></td>
        </tr>
    </table>
    <br>
    <br>
    <a style="border:1px solid transparent;border-radius:3px;display:block;text-decoration:none;width:100%;padding-top:10px;padding-bottom:10px;font-size:16px;background-color:#a46497;color:white;text-align:center;" href="admin.php?page=woo-shortcodes-kit" target="_blank"><span class="dashicons dashicons-admin-generic"></span> <?php esc_html_e( 'GO TO SETTINGS', 'woo-shortcodes-kit' ); ?></a>
    <?php
    
}

/*END MY ACCOUNT PAGE META BOX*/



/*START LIMIT CART QUANTITY*/

//Since v.1.8.0

global $pluginOptionsVal;
$pluginOptionsVal=get_wshk_sidebar_options();

if(isset($pluginOptionsVal['wshk_onlyoneincartt']) && $pluginOptionsVal['wshk_onlyoneincartt']==2009) {  
  
    
 

add_filter( 'woocommerce_add_to_cart_validation', 'wshk_only_one_in_cart', 99, 2 );

function wshk_only_one_in_cart( $passed, $added_product_id ) {

global $woocommerce;

// empty cart: new item will replace previous
$_cartQty = count( $woocommerce->cart->get_cart() );
$proincartlimit = get_option('wshk_productsincart');
if($_cartQty >= $proincartlimit){
    $woocommerce->cart->empty_cart();   
}

// display a message if you like
//wc_add_notice( 'Product added to cart!', 'notice' );

return $passed;
}


}

/*END LIMIT CART QUANTITY*/


/*START CHANGE RETURN TO SHOP BUTTON TEXT AND REDIRECTION*/

//Since v.1.8.0

global $pluginOptionsVal;
$pluginOptionsVal=get_wshk_sidebar_options();

if(isset($pluginOptionsVal['wshk_returntoshopbtn']) && $pluginOptionsVal['wshk_returntoshopbtn']==2011) { 
    
 /*custom button text*/    


 //Sustituir plantilla del tema por la del plugin
add_filter( 'wc_get_template', 'wshk_cma_get_templatebtntext', 10, 5 );
function wshk_cma_get_templatebtntext( $located, $template_name, $args, $template_path, $default_path ) {    
    if ( 'cart/cart-empty.php' == $template_name ) {
       
        $located = WP_CONTENT_DIR . '/plugins/woo-shortcodes-kit/mytemplates/cart-empty.php';
    }
    
    return $located;
}
 
 /*custom redirection*/ 
 
 $retushopurlredi = get_option('wshk_retshopurlredi'); 
 if(!isset($retushopurlredi) || trim($retushopurlredi) == ''){


add_filter( 'woocommerce_return_to_shop_redirect', 'wshk_change_return_shop_url' );
 
function wshk_change_return_shop_url() {
return get_permalink( wc_get_page_id( 'shop' ) );
}

}else {
    
add_filter( 'woocommerce_return_to_shop_redirect', 'wshk_change_return_shop_url' );
 
function wshk_change_return_shop_url() {
 $retushopurlredi = get_option('wshk_retshopurlredi'); 
return home_url($retushopurlredi);    
}
}
    
}


/*END CHANGE RETURN TO SHOP BUTTON TEXT AND REDIRECTION*/