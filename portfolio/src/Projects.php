<?php

/**
 * Class Projects
 * Adds Projects features
 * registers custom post type, taxonomies etc.
 *
 * @author Kordian Urban <kurban@wjadigital.com>
 */
class Projects
{

    const POST_TYPE = 'project';
    const META_URL = 'project_url';

    /**
     * Projects constructor
     * Contains WP hooks
     */
    public static function init() {
        add_action('init', ['Projects', 'registerEntities']);
        add_action('tf_create_options', ['Projects', 'registerMetaboxes']);

        add_shortcode('projects', ['Projects', 'widgetListing']);
    }

    /**
     * Registers Entities
     * including custom post types, taxonomies etc
     */
    public static function registerEntities()
    {
        register_post_type(self::POST_TYPE, [
            'labels' => array(
                'name' => __('Projects', Theme::TEXT_DOMAIN),
                'singular_name' => __('Project', Theme::TEXT_DOMAIN),
                'add_new' => _x('Add new', Theme::TEXT_DOMAIN),
                'add_new_item' => _x('Add new', Theme::TEXT_DOMAIN)
            ),
            'public' => true,
            'show_ui' => true,
            'capability_type' => 'post',
            'hierarchical' => false,
            'rewrite' => array('slug' => 'projects'),
            'supports' => array('title', 'editor', 'excerpt', 'page-attributes', 'revisions', 'thumbnail'),
            'menu_icon' => 'dashicons-portfolio'
        ]);
    }

    /**
     * Registers Metaboxes
     * using Titan Framework
     */
    public static function registerMetaboxes()
    {
        $box = Theme::$titan->createMetaBox(array(
            'name' => 'Details',
            'post_type' => self::POST_TYPE,
        ));

        $box->createOption(array(
            'id' => self::META_URL,
            'type' => 'text',
            'name' => 'URL'
        ));
    }

    /**
     * Displays Projects listing
     * Shotrcode callback
     *
     * @return string HTMl of the listing
     */
    public static function widgetListing()
    {
        ob_start();

        $projects = new WP_Query(array(
            'post_type' => self::POST_TYPE,
            'posts_per_page' => -1,
            'orderby' => 'menu_order',
            'order' => 'ASC',
        ));

        set_query_var( 'projects', $projects );
        get_template_part('template/widgets/projects-listing');

        $output = ob_get_clean();

        return $output;
    }

}