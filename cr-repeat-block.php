<?php 
/*
 Plugin Name: CR Repeat Block
 Plugin URI: https://github.com/WP-Panda/Cr-Repeat-Block
 Description: Плагин предназначен для добавления повторяющихся блоков контента в разные записи.
 Version: 1.5.0
 Author: WP_Panda
 Author URI: http://wp-panda.com/pay
 Text Domain: cr-repeat-block
 Domain Path: /languages/
 */

/*  Copyright 2015  WP Panda  (email: yoowordpress@yandex.ru)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

 if( !defined( 'ABSPATH' ) ) exit;


register_activation_hook( __FILE__, 'myplugin_activate' );
/**
 * Добавление флага для сброса правил перезаписи.
 */
function myplugin_activate() {
    if ( ! get_option( 'cr_rep_block_flush_rewrite_rules_flag' ) ) {
        add_option( 'cr_rep_block_flush_rewrite_rules_flag', true );
    }
}



add_action( 'init', 'cr_rep_block_flush_rewrite_rules_maybe', 20 );
/**
 * Flush rewrite rules if the previously added flag exists,
 * and then remove the flag.
 */
function cr_rep_block_flush_rewrite_rules_maybe() {
    if ( get_option( 'cr_rep_block_flush_rewrite_rules_flag' ) ) {
        flush_rewrite_rules();
        delete_option( 'cr_rep_block_flush_rewrite_rules_flag' );
    }
}


add_action('plugins_loaded', 'cr_rep_block_init');
function cr_rep_block_init(){
    load_plugin_textdomain( 'cr-repeat-block', false, dirname( plugin_basename( __FILE__ ) ). '/languages/'  );
}

/*
 *Add post type cr-contents-blocks
 */
if ( ! function_exists( 'cr_rep_block_create_cr_contents_blocks') ) {
	function cr_rep_block_create_cr_contents_blocks() {
		register_post_type( 'cr-contents-blocks',
			array(
				'labels' => array(
					'name' => __( 'Duplicate Blocks','cr-repeat-block' ),
					'singular_name' => __( 'Duplicate Blocks','cr-repeat-block' )
				),
			'public' => true,
			'has_archive' => false,
			)
		);
	}

	add_action( 'init', 'cr_rep_block_create_cr_contents_blocks' );
}

/*
 *Add custom columns 
 */
if ( ! function_exists( 'cr_rep_block_cr_columns_register') ) {
	function cr_rep_block_cr_columns_register( $columns ) {
		$out = array();
        $i=0;
		foreach($columns as $col=>$name){
			$out[$col] = $name;
            if(++$i==3)
            $out['post_page_id'] = __( 'Block Shortcode','cr-repeat-block' );
            if(++$i==4)
            $out['text'] = __( 'Content','cr-repeat-block' );
		}
	 	return $out;
	}

	add_filter('manage_cr-contents-blocks_posts_columns', 'cr_rep_block_cr_columns_register', 5);
}

/*
 *Add content in custom columns
 */
if ( ! function_exists( 'cr_rep_block_columns_display') ) {
	function cr_rep_block_columns_display( $column, $id ) {
		$out = '';
		if($column === 'post_page_id') 
		  $out .= "[cr_block id='{$id}']";

		if($column === 'text') { 
			$text = get_the_excerpt();
				if ( !$text )
					$text = '<em>' . __( 'Content Missing','cr-repeat-block' ) . '</em>';
		 	}
		$out .=$text;

		echo $out;
	}

	add_action( 'manage_cr-contents-blocks_posts_custom_column', 'cr_rep_block_columns_display', 10, 2 );
}

/*
 *Add shortcode
 */
if ( ! function_exists( 'cr_rep_block_short') ) {
	function cr_rep_block__short( $atts, $content = null ) {
		extract( shortcode_atts( array(
			"id" => ''
		), $atts ) );
		$post_get = get_post( $atts['id'] );
		return '<span class="short">' . $post_get->post_content . '</span>';
	}

	add_shortcode("cr_block", "cr_rep_block_short");
}
