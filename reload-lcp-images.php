<?php
/**
 * Move Floating Social Bar in Genesis
 *
 * @package   VIA_Preload_Images
 * @author    Vlada Radivojevic <vlada.radivojevic.bg@gmail.com>
 * @license   GPL-2.0+
 * @copyright 2021 Vlada Radivojevic, VIA Tech
 *
 * @wordpress-plugin
 * Plugin Name:       Preload LCP Images
 * Plugin URI:        https://github.com/vladicaradivojevic/preload-lcp-images
 * Description:       Preload LCP Images
 * Version:           1.0.0
 * Author:            Vlada Radivojevic
 * Author URI:        https://github.com/vladicaradivojevic/preload-lcp-images
 * Text Domain:       preload-lcp-images
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/vladicaradivojevic/preload-lcp-images
 * GitHub Branch:     master
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Preload attachment image, defaults to post thumbnail
 */
function preload_post_thumbnail() {
    global $post;
    /** Prevent preloading for specific content types or post types */
    if ( ! is_singular() ) {
        return;
    }
    /** Adjust image size based on post type or other factor. */
    $image_size = 'full';

    if ( is_singular( 'product' ) ) {
        $image_size = 'woocommerce_single';

    } else if ( is_singular( 'post' ) ) {
        $image_size = 'large';

    }
    $image_size = apply_filters( 'preload_post_thumbnail_image_size', $image_size, $post );
    /** Get post thumbnail if an attachment ID isn't specified. */
    $thumbnail_id = apply_filters( 'preload_post_thumbnail_id', get_post_thumbnail_id( $post->ID ), $post );

    /** Get the image */
    $image = wp_get_attachment_image_src( $thumbnail_id, $image_size );
    $src = '';
    $additional_attr_array = array();
    $additional_attr = '';

    if ( $image ) {
        list( $src, $width, $height ) = $image;

        /**
         * The following code which generates the srcset is plucked straight
         * out of wp_get_attachment_image() for consistency as it's important
         * that the output matches otherwise the preloading could become ineffective.
         */
        $image_meta = wp_get_attachment_metadata( $thumbnail_id );

        if ( is_array( $image_meta ) ) {
            $size_array = array( absint( $width ), absint( $height ) );
            $srcset     = wp_calculate_image_srcset( $size_array, $src, $image_meta, $thumbnail_id );
            $sizes      = wp_calculate_image_sizes( $size_array, $src, $image_meta, $thumbnail_id );

            if ( $srcset && ( $sizes || ! empty( $attr['sizes'] ) ) ) {
                $additional_attr_array['imagesrcset'] = $srcset;

                if ( empty( $attr['sizes'] ) ) {
                    $additional_attr_array['imagesizes'] = $sizes;
                }
            }
        }

        foreach ( $additional_attr_array as $name => $value ) {
            $additional_attr .= "$name=" . '"' . $value . '" ';
        }

    } else {
        /** Early exit if no image is found. */
        return;
    }

    /** Output the link HTML tag */
    printf( '<link rel="preload" as="image" href="%s" %s/>', esc_url( $src ), $additional_attr );
	printf('<link rel="preload" as="image" href="%s" imagesrcset="%s"', "https://thebreadguru.com/wp-content/uploads/2016/07/cropped-cher2.jpg", "https://thebreadguru.com/wp-content/uploads/2016/07/cropped-cher2.jpg");
}
add_action( 'wp_head', 'preload_post_thumbnail' );
