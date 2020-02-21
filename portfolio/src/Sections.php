<?php

/**
 * Class Sections
 * Adds Sections features
 * registers custom post type, taxonomies etc.
 *
 * @author Kordian Urban <kurban@wjadigital.com>
 */
class Sections
{

    /**
     * Sections constructor
     * Contains WP hooks
     */
    public static function init() {
        add_shortcode('sections', ['Sections', 'widgetListing']);
    }

    /**
     * Displays Sections listing
     * Shotrcode callback
     *
     * @return string HTMl of the listing
     */
    public static function widgetListing() {
        ob_start();

        $sections = new WP_Query(array(
            'post_type' => 'page',
            'post_status' => 'publish',
            'post_parent' => get_option('page_on_front'),
            'posts_per_page' => -1,
            'orderby' => 'menu_order',
            'order' => 'ASC',
        ));

        set_query_var( 'sections', $sections );
        get_template_part('template/widgets/sections-listing');

        $output = ob_get_clean();

        return $output;
    }

}