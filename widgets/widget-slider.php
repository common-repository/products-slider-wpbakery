<?php
/**
 * widget-slider.php
 * 
 * Plugin Name: PSFWPB_products_carouse_VC
 * Description: A widget that displays carousel of products.
 * Version: 1.0
 * Author: ElementInvader
*/
 
add_action( 'vc_before_init', 'PSFWPB_products_carouse_VC' );
function PSFWPB_products_carouse_VC() {
    if (class_exists('WPBakeryShortCode')) {
		$args = array(
			'taxonomy'     => 'product_cat',
			'type'         => 'post',
			'child_of'     => 0,
			'parent'       => '',
			'orderby'      => 'name',
			'order'        => 'ASC',
			'hide_empty'   => 1,
			'hierarchical' => 1,
			'exclude'      => '',
			'include'      => '',
			'number'       => 0,
			'pad_counts'   => false,
		);

		$categories_list = [];
		$all_categories = get_categories( $args );
		$categories_list ['Any'] = __( 'Any', 'psfwpb' );
		foreach ($all_categories as $cat) {
			$categories_list [$cat->slug] = $cat->cat_name.' ('.$cat->category_count .')';
		}

        vc_map( array(
        	'name' => __( 'Products Slider for WPBakery', 'psfwpb' ),
        	'base' => 'PSFWPBproducts',
        	'group' => 'PSFWPBproducts',
        	'icon' => 'vc_general vc_element-icon icon-wpb-woocommerce',
        	'show_settings_on_create' => true,
			'param_name' => 'woocommerce',
			'class' => 'woocommerce',
        	'category' => __( 'WooCommerce', 'psfwpb' ),
        //"controls"	=> 'popup_delete',
        	'description' => __( 'Products details', 'psfwpb' ),
        	'params' => array(
        		array(
        			'type' => 'textfield',
        			'heading' => __( 'Limit Products', 'psfwpb' ),
        			'param_name' => 'limit',
        			'description' => __( 'You can enter limit of products (int)', 'psfwpb' )
				),
        		array(
        			'type' => 'textfield',
        			'heading' => __( 'Columns', 'psfwpb' ),
        			'param_name' => 'columns',
        			'description' => __( 'The columns attribute controls how many columns wide the products should be before wrapping', 'psfwpb' )
				),
        		array(
        			'type' => 'textfield',
        			'heading' => __( 'Custom Query', 'psfwpb' ),
        			'param_name' => 'sql',
        			'description' => __( 'You can enter custom query like "category_name=staff", Other query will be ignored', 'psfwpb' )
				),
				array(
        			'type' => 'textfield',
        			'heading' => __( 'Search Products', 'psfwpb' ),
        			'param_name' => 's',
        			'description' => __( 'Search Products.', 'psfwpb' )
        		),
				array(
        			'type' => 'dropdown',
        			'heading' => __( 'Category', 'psfwpb' ),
        			'param_name' => 'category',
        			'value' => array_flip($categories_list),
        			'description' => __( 'Select wanted category.', 'psfwpb' )
        		),
				array(
        			'type' => 'dropdown',
        			'heading' => __( 'Type', 'psfwpb'),
					'param_name' => 'type',
        			'value' => array_flip([
						'none'  => __('All Visible', 'psfwpb'),
						'featured'    => __('Featured', 'psfwpb'),
						'featured_only_onsale'    => __('Featured Only On Sale', 'psfwpb'),
						'onsale'    => __('On Sale', 'psfwpb'),
						'onsale_featured'    => __('On Sale + Featured', 'psfwpb'),
					]),
        			'description' => __( 'Select Type', 'psfwpb' )
        		),
				array(
        			'type' => 'dropdown',
        			'heading' => __( 'Order By', 'psfwpb'),
					'param_name' => 'order_by',
        			'value' => array_flip([
						'none'  => __('None', 'psfwpb'),
						'ID'    => __('ID', 'psfwpb'),
						'author' => __('Author', 'psfwpb'),
						'title' => __('Title', 'psfwpb'),
						'name'  => __('Name', 'psfwpb'),
						'date'  => __('Date', 'psfwpb'),
						'modified' => __('Modified', 'psfwpb'),
						'rand'  => __('Random', 'psfwpb'),
						'comment_count' => __('Comment count', 'psfwpb'),
						'menu_order ' => __('Menu order', 'psfwpb'),
					]),
        			'description' => 
						sprintf(__( 'Select how to sort retrieved products. More at %1$sWordPress codex page%2$s.', 'psfwpb' ), '<a href="https://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">', '</a>')
				),
				array(
        			'type' => 'dropdown',
					'param_name' => 'order',
        			'heading' => __( 'Order', 'psfwpb'),
					'value' => array(
						__('Ascending', 'psfwpb') => 'ASC',
						__('Descending', 'psfwpb') => 'DESC',
					),
        			'description' => 
						sprintf(__( 'Designates the ascending or descending order. More at %1$sWordPress codex page%2$s.', 'psfwpb' ), '<a href="https://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">', '</a>')
        		),
				array(
        			'type' => 'textfield',
        			'heading' => __( 'Css class', 'psfwpb' ),
        			'param_name' => 'custom_css_class',
        			'description' => __( 'Set custom css class for widget', 'psfwpb' )
				),
				array(
					"type" => "checkbox",
					"class" => "",
					"heading" => __( "Hide Widget", "psfwpb" ),
					"param_name" => "hide",
					"value" => __( "", "psfwpb" ),
					"description" => __( "Temporary hide widget", "psfwpb" )
				)
        	)
        ) );
        
        class WPBakeryShortCode_PSFWPBproducts extends WPBakeryShortCode {
            public function content( $atts, $content = null ) {
                global $wpdb;
				wp_enqueue_style('slick', plugins_url('/assets/libs/slick/slick.css', PSFWPB__FILE__));
				wp_enqueue_style('slick-theme', plugins_url('/assets/libs/slick/slick-theme.css', PSFWPB__FILE__));
				wp_enqueue_script('slick', plugins_url('/assets/libs/slick/slick.min.js', PSFWPB__FILE__));

                $atts = shortcode_atts(array(
                    'id'=>NULL,
                    'order'=>'ASC',
                    'order_by'=>'',
                    'category'=>'',
                    'sql'=>'',
                    'type'=>'',
                    'columns'=>'4',
                    'limit'=>'12',
                    'hide'=>'',
                    's'=>'',
                    'custom_css_class'=>'',
                ), $atts);

				if($atts['hide'] =='true') {
					return false;
				}

				$tax_query = [];
				$tax_query[] = 
								array(
									'taxonomy' => 'product_visibility',
									'field' => 'name',
									'terms' => 'exclude-from-catalog',
									'operator' => 'NOT IN',
								);

				$post__in = '';

				if($atts['type'] == 'onsale_featured') {
					//first query
					$args_custom =  array(
						'post_type'      => 'product',
						'posts_per_page' =>  -1,
						'tax_query'           => $tax_query,
					) ;
					$args_custom['meta_query'] = array(
						'relation' => 'OR',
						array( // Simple products type
							'key'           => '_sale_price',
							'value'         => 0,
							'compare'       => '>',
							'type'          => 'numeric'
						),
						array( // Variable products type
							'key'           => '_min_variation_sale_price',
							'value'         => 0,
							'compare'       => '>',
							'type'          => 'numeric'
						)
					);
					$get_products1 = get_posts($args_custom);
							
					//second query
					$tax_query_custom[] = array(
						'taxonomy' => 'product_visibility',
						'field'    => 'name',
						'terms'    => 'featured',
						'operator' => 'IN', // or 'NOT IN' to exclude feature products
					);
					$args_custom =  array(
						'post_type'      => 'product',
						'posts_per_page' =>  -1,
						'tax_query'           => $tax_query_custom,
					) ;
					$get_products2 = get_posts($args_custom);
		
					$postids = array();
					foreach( $get_products1 as $item ) {
						$postids[]=$item->ID; //create a new query only of the post ids
					}
					
					foreach( $get_products2 as $item ) {
						$postids[]=$item->ID; //create a new query only of the post ids
					}

					$post__in = array_unique($postids); //remove duplicate post ids
				}
				if($atts['type'] == 'featured' || $atts['type'] == 'featured_only_onsale')
					$tax_query[] = array(
						'taxonomy' => 'product_visibility',
						'field'    => 'name',
						'terms'    => 'featured',
						'operator' => 'IN', // or 'NOT IN' to exclude feature products
					);

				$meta_query = [];
				if($atts['type'] == 'onsale' || $atts['type'] == 'featured_only_onsale'){
					$meta_query = array(
						'relation' => 'OR',
						array( // Simple products type
							'key'           => '_sale_price',
							'value'         => 0,
							'compare'       => '>',
							'type'          => 'numeric'
						),
						array( // Variable products type
							'key'           => '_min_variation_sale_price',
							'value'         => 0,
							'compare'       => '>',
							'type'          => 'numeric'
						)
					);
				}
				$args =  array(
					'post_type'      => 'product',
					'posts_per_page' =>  $atts['limit'],
					'orderby'      =>  $atts['order_by'],
					'order'        =>  $atts['order'],
					'product_cat' => $atts['category'],
					'tax_query'           => $tax_query,
					'meta_query'           => $meta_query,
					'post__in'           => $post__in,
					's'=>$atts['s'],
				) ;

				if(!empty($atts['sql'])) {
					$get_products = new WP_Query('post_type=product&'.$atts['sql']);
				} else {
					$get_products = new WP_Query($args);
				}

				?>
				<div class='psfwpb_product_carousel psfwpb_product_carousel_ini<?php echo esc_attr($atts['id']);?> <?php echo esc_attr($atts['custom_css_class']);?>'>
				<div class='woocommerce'>
				<?php
					if($get_products->have_posts()) {
						do_action('woocommerce_before_shop_loop');
						woocommerce_product_loop_start();
						while ( $get_products->have_posts() ) { $get_products->the_post();  
							$post_object = get_post($get_products->post->ID);
							
							setup_postdata($GLOBALS['post'] = & $post_object);

							/* loop from template file
							wc_get_template_part('content', 'product');
							*/

							?>
							<li <?php wc_product_class( '', $post_object ); ?>>
							<?php

							/**
							 * Hook: woocommerce_before_shop_loop_item.
							 *
							 * @hooked woocommerce_template_loop_product_link_open - 10
							 */
							do_action( 'woocommerce_before_shop_loop_item' );

							/**
							 * Hook: woocommerce_before_shop_loop_item_title.
							 *
							 * @hooked woocommerce_show_product_loop_sale_flash - 10
							 * @hooked woocommerce_template_loop_product_thumbnail - 10
							 */
							do_action( 'woocommerce_before_shop_loop_item_title' );

							/**
							 * Hook: woocommerce_shop_loop_item_title.
							 *
							 * @hooked woocommerce_template_loop_product_title - 10
							 */
							do_action( 'woocommerce_shop_loop_item_title' );

							/**
							 * Hook: woocommerce_after_shop_loop_item_title.
							 *
							 * @hooked woocommerce_template_loop_rating - 5
							 * @hooked woocommerce_template_loop_price - 10
							 */
							do_action( 'woocommerce_after_shop_loop_item_title' );

							/**
							 * Hook: woocommerce_after_shop_loop_item.
							 *
							 * @hooked woocommerce_template_loop_product_link_close - 5
							 * @hooked woocommerce_template_loop_add_to_cart - 10
							 */
							do_action( 'woocommerce_after_shop_loop_item' );
							?>
							</li>
							<?php

						}
						wp_reset_postdata();
						woocommerce_product_loop_end();
						do_action('woocommerce_after_shop_loop');
					} else {
						do_action('woocommerce_no_products_found');
					}
				?>
				</div>
				<script>
					jQuery(document).ready(function($){
						$('.psfwpb_product_carousel_ini<?php echo esc_js($atts['id']);?> .products').slick({
							dots: true,
							arrows: true,
							infinite: true,
							slidesToShow: <?php echo esc_js($atts['columns']);?>,
							slidesToScroll: <?php echo esc_js($atts['columns']);?>,
							responsive: [
								{
								breakpoint: 600,
								settings: {
										slidesToShow: 1,
										slidesToScroll: 1
									}
								},
							]
						});
					});
				</script>
				</div>
                <?php
            }
        }
   }
}

