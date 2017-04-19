<?php
/**
 * @package custom post image
 * Plugin Name: Custom post image
 * Version: 0.2
 * Description: Custom post 's images for faster loading
 * Author: Niteco
 * Author URI: http://niteco.se/
 * Plugin URI: PLUGIN SITE HERE
 * Text Domain: custom-post-image
 * Domain Path: /languages
 */

if (!defined('CUSTOM_IMAGE_WIDTH')) {
    define('CUSTOM_IMAGE_WIDTH', 1400);
}
if (!defined('CUSTOM_IMAGE_SIZE')) {
    define('CUSTOM_IMAGE_SIZE', 'medium_large'. CUSTOM_IMAGE_WIDTH);
}

add_action( 'after_setup_theme', 'add_custom_image_size' );
if (!function_exists('add_custom_image_size')) {
    function add_custom_image_size() {
        add_image_size( CUSTOM_IMAGE_SIZE, CUSTOM_IMAGE_WIDTH, 0, false );
    }
}

add_filter( 'the_content', 'filter_post_image' );
if (!function_exists('filter_post_image')) {
    function filter_post_image( $content ) {
        preg_match_all( '/<img[^>]+>/i', $content, $result );
        $search = $replace = array();
        foreach ( $result[0] as $img_tag ) {
            $item = array();
            preg_match_all( '/(alt|title|class|src)=("[^"]*")/i', $img_tag, $img );
            foreach ( $img[1] as $i => $index ) {
                $item[ $index ] = $img[2][ $i ];
            }

            if ( isset( $item['class'] ) && strpos( $item['class'], 'wp-image-' ) !== false ) {
                $attachment = array();
                preg_match( '/wp-image-([0-9]*)/i', $item['class'], $attachment );
                if ( empty( $attachment[1] ) ) {
                    continue;
                }
                $image_id = $attachment[1];
                $image = wp_get_attachment_image_src( $image_id, CUSTOM_IMAGE_SIZE );
                if ($image[3]) {
                    $search[]  = $item['src'];
                    $replace[] = '"' . $image[0] . '"';
                }
            }
        }

        return str_replace( $search, $replace, $content );
    }
}