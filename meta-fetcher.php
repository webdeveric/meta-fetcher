<?php
/*
Plugin Name: Meta Fetcher
Plugin Group: Shortcodes
Plugin URI: http://phplug.in/
Description: This plugin provides a simple shortcode that allows you to fetch meta information for the current <code>$post</code>. Example Usage: <code>[meta name="some_name_here" default="some default content"]</code>
Author: Eric King
Version: 0.3
Author URI: http://webdeveric.com/
*/

// This function checks to see if the argument passed indicates an affirmative.  It exists to account for various user inputs.
if ( ! function_exists('is_affirmative')):
    function is_affirmative($arg)
    {
        if (is_string($arg))
            $arg = strtolower($arg);
        return in_array($arg, array(true, 'true', 'yes', 'y', '1', 1));
    }
endif;

function wde_meta_fetcher_shortcode($atts, $content = null, $code = '')
{
    global $post;

    extract(
        shortcode_atts(
            array(
                'name'       => '',
                'default'    => '',
                'shortcode'  => true,
                'allowempty' => false,
                'filters'    => true
            ),
            $atts
        )
   );

    if (isset($post, $post->ID) && $name != '') {
        $value = get_post_meta($post->ID, $name, true);

        if ($value == '') {
            if (is_affirmative($allowempty))
                return $value;
            $value = $default;
        }

        if (is_affirmative($shortcode))
            $value = do_shortcode($value);

        if (is_affirmative($filters)) {
            $value = apply_filter('meta_fetcher_value', $name, $value);
            $value = apply_filter('meta_fetcher_' . $name , $value);
        }

        return $value;
    }
    return $default;
}
add_shortcode('meta', 'wde_meta_fetcher_shortcode');