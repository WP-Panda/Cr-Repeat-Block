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

/**
 * Подключает стили и скрипты
 */
add_action('admin_enqueue_scripts', 'cr_rep_block_register_scripts');
function cr_rep_block_register_scripts($hook) {

    wp_register_script('custom_jquery', plugins_url('/assets/js/cr-repeat-block.js', __FILE__), array('jquery'), '2.5.1');
    wp_register_style('new_style', plugins_url('/assets/css/style.css', __FILE__), false, '1.0.0', 'all');

    //скрипты и стили только для списка блоков
    if( 'edit.php' === $hook && isset($_GET['post_type']) && 'cr-contents-blocks' ===  $_GET['post_type']  ) {
        wp_enqueue_script('custom_jquery');
        wp_enqueue_style('new_style');
    }
}

/**
 * Добавляет тип записей cr-contents-blocks
 */

function cr_rep_block_create_cr_contents_blocks() {
    register_post_type( 'cr-contents-blocks',
        array(
            'labels' => array(
                'name' => __( 'Duplicate Blocks','cr-repeat-block' ),
                'singular_name' => __( 'Duplicate Blocks','cr-repeat-block' )
            ),
            'public' => true,
            'has_archive' => false,
            'menu_icon' => 'dashicons-editor-justify'
        )
    );
}

add_action( 'init', 'cr_rep_block_create_cr_contents_blocks' );

/**
 * Добавляет колонки для шорткодов
 */

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


/**
 * Наполняет колонку кодов и контента
 */

function cr_rep_block_columns_display( $column, $id )
{
    $out = '';
    if ($column === 'post_page_id') {
        $out .= sprintf("<span class='copy-code'>[cr_repeat_block id=\"%s\"]</span><i class='copy-icon dashicons dashicons-admin-page'></i>",$id);
    }

    if ($column === 'text') {
        $text = get_the_excerpt();
        if (!$text)
            $text .= '<em style="color:#d54e21"><i class="dashicons dashicons-warning"></i>  ' . __('Content Missing', 'cr-repeat-block') . '</em>';
        $out .= $text;
    }

    echo $out;
}

add_action( 'manage_cr-contents-blocks_posts_custom_column', 'cr_rep_block_columns_display', 10, 2 );


/**
 * Добавляет шорткоды повторяющихся блоков
 */

function cr_rep_block_short( $atts, $content = null ) {
    extract( shortcode_atts( array(
        "id" => ''
    ), $atts ) );
    $post_get = get_post( $atts['id'] );
    return '<span class="short">' . $post_get->post_content . '</span>';
}

add_shortcode("cr_repeat_block", "cr_rep_block_short");

/**
 * Добавляет селект с шорткодами
 */
add_action('media_buttons','add_sc_select',11);
function add_sc_select(){
    $out = sprintf('&nbsp;<select id="sc_select"><option>%s</option>',__('Cr Repeat Block'));
    $cr_blocks_ids = get_posts('post_type=cr-contents-blocks');
    $shortcodes_list ='';
    foreach ($cr_blocks_ids as $key){
        $out .= sprintf('<option value=\'[cr_repeat_block id="%s"]\'>%s</option>',$key->ID,$key->post_title);
    }
    $out .= '</select>';
    echo $out;
}
add_action('admin_head', 'button_js');


/**
 * Скрипт кнопки визуального редактора
 */
function button_js()
{
    echo '<script type="text/javascript">
    jQuery(document).ready(function(){
       jQuery("#sc_select").change(function() {
              send_to_editor(jQuery("#sc_select :selected").val());
                  return false;
        });
    });
    </script>';
}