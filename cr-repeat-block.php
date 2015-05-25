<?php 
/*
 Plugin Name: CR Repeat Block
 Plugin URI: https://github.com/WP-Panda/Cr-Repeat-Block
 Description: Плагин предназначен для добавления повторяющихся блоков контента в разные записи.
 Version: 1.0.0
 Author: Мaksim (WP_Panda) Popov
 Author URI: http://wp-panda.com/pay
 */

 if( !defined( 'ABSPATH' ) ) exit;

/*
 *Add post type cr-contents-blocks
 */
if ( ! function_exists( 'create_cr_contents_blocks') ) {
	function create_cr_contents_blocks() {
		register_post_type( 'cr-contents-blocks',
			array(
				'labels' => array(
					'name' => __( 'Контентные блоки' ),
					'singular_name' => __( 'Контентные блоки' )
				),
			'public' => true,
			'has_archive' => false,
			)
		);
	}

	add_action( 'init', 'create_cr_contents_blocks' );
}

/*
 *Add custom columns 
 */
if ( ! function_exists( 'cr_columns_register') ) {
	function cr_columns_register( $columns ) {
		$out = array();
		foreach($columns as $col=>$name){
			$out['text'] = __( 'Содержимое', 'my-plugin' );
			$out['post_page_id'] = 'ID';
			$out[$col] = $name;
		}
	 	return $out;
	}

	add_filter('manage_cr-contents-blocks_posts_columns', 'cr_columns_register', 5);
}

/*
 *Add content in custom columns
 */
if ( ! function_exists( 'cr_columns_display') ) {
	function cr_columns_display( $column, $id ) {
		$out = '';
		if($column === 'post_page_id') 
		  $out .= $id;

		if($column === 'text') { 
			$text = get_the_excerpt();
				if ( !$text )
					$text = '<em>' . __( 'Контента то нет, надо бы добавить', 'wp_panda' ) . '</em>';
		 	}
		$out .=$text;

		echo $out;
	}

	add_action( 'manage_cr-contents-blocks_posts_custom_column', 'cr_columns_display', 10, 2 );					
}

/*
 *Add shortcode
 */
if ( ! function_exists( 'cr_block_short') ) {
	function cr_block_short( $atts, $content = null ) {
		extract( shortcode_atts( array(
			"id" => ''
		), $atts ) );
		$post_get = get_post( $id );
		return '<span class="short">' . $post_get->post_content . '</span>';
	}

	add_shortcode("cr_block", "cr_block_short");
}
