<?php
/**
 * Plugin Name: SocialMediaStream
 * Plugin URI: https://www.socialmediastream.nl
 * Description: Embed a social media stream into your site.
 * Version: 1.0
 * Author: SocialMediaStream.nl
 * License: GPLv2
 */

/**
 * Class SocialMediaStream
 */
class SocialMediaStream
{
    /**
     * SocialMediaStream constructor.
     *
     */
    public function __construct()
    {
        add_action('wp_enqueue_scripts', array($this, 'enqueueScripts'), 0);
        add_action('admin_enqueue_scripts', array($this, 'adminEnquequeScripts'), 0);

        if (is_admin()) {
            add_action('admin_head', array($this, 'init'));
        }
    }

    /**
     * Init plugin.
     */
    public function init()
    {
        global $typenow;

        if ((!current_user_can('edit_posts') && !current_user_can('edit_pages')) ||
            (!in_array($typenow, array('post', 'page')))) {
            return;
        }

        if ('true' === get_user_option('rich_editing')) {
            add_filter('mce_external_plugins', array($this, 'registerTinyMCEPlugin'));
            add_filter('mce_buttons', array($this, 'addTinyMCEButton'));
        }
    }

    /**
     * Enqueue scripts
     */
    public function enqueueScripts()
    {
        wp_enqueue_script(
            'socialmediastream',
            '//api.socialmediastream.nl/js/embed.js',
            array(),
            false,
            true
        );
    }

    /**
     * Enqueue admin scripts
     */
    function adminEnquequeScripts()
    {
        wp_enqueue_style(
            'sms-admin',
            plugins_url('/assets/js/tinymce/plugins/socialmediastream/plugin.css', __FILE__)
        );
    }

    /**
     * Register TinyMCE plugin.
     *
     * @param $plugin_array
     * @return mixed
     */
    function registerTinyMCEPlugin($plugin_array)
    {
        $plugin_array['noneditable'] = plugin_dir_url(__FILE__) . 'assets/js/tinymce/plugins/noneditable/plugin.js';
        $plugin_array['socialmediastream'] = plugin_dir_url(__FILE__) . 'assets/js/tinymce/plugins/socialmediastream/plugin.js';

        return $plugin_array;
    }

    /**
     * Register TinyMCE Button.
     *
     * @param $buttons
     * @return array
     */
    public function addTinyMCEButton($buttons)
    {
        array_push($buttons, 'socialmediastream');

        return $buttons;
    }

    /**
     * Render shortcode.
     *
     * @param $arguments
     * @return string
     */
    public static function render($arguments)
    {
        $arguments = wp_parse_args($arguments, array(
            'stream' => 'error',
        ));

        return sprintf('<div class="sms" data-stream="%1$s"></div>', $arguments['stream']);
    }
}

$socialMediaStream = new SocialMediaStream();

if (!function_exists('socialmediastream')) {
    function socialmediastream($arguments)
    {
        echo SocialMediaStream::render($arguments);
    }
}

if (!function_exists('socialmediastream_shortcode')) {
    function socialmediastream_shortcode($arguments)
    {
        extract(shortcode_atts(array(
            'stream' => 'error',
        ), $arguments));

        return SocialMediaStream::render($arguments);
    }
}

add_shortcode('socialmediastream', 'socialmediastream_shortcode');
?>