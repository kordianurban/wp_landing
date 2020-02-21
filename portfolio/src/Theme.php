<?php

/**
 * Class Theme
 *
 * Contains full theme setup
 * adds registrations
 * removes unnecessary default functionalities
 * modifies default Wordpress output
 *
 * @author Kordian Urban <kurban@wjadigital.com>
 */
class Theme {

    const TEXT_DOMAIN = 'portfolio';
    const HELPER_INPUT = 'portfolio_helper_input';
    const HELPER_INPUT_VALUE = 'save';

    public static $titan;

	/**
	 * Initializes Theme
	 */
	public static function init() {
        self::setupBackend();
        self::setupFrontend();
        self::initTitan();
    }

    /**
     * Setup backend
     * Register functionalities
     */
    public static function setupBackend() {
        self::setupLoginScreen();
        self::setupImageUploads();

        add_theme_support( 'post-thumbnails' );
        add_post_type_support( 'page', 'excerpt' );
        add_action( 'after_setup_theme', ['Theme', 'registerNavigation'] );
        add_action( 'after_setup_theme', ['Theme', 'enableElementorHeaderFooter'] );
        add_action( 'admin_enqueue_scripts', ['Theme', 'registerBackendAssets'] );
        add_filter( 'excerpt_more', ['Theme', 'customExcerptMore'] );
        add_filter( 'upload_mimes', ['Theme', 'uploadMimes'] );

        //add_shortcode( 'accordion', ['Theme', 'widgetAccordion'] );
        add_shortcode( 'latest_news', ['Theme', 'widgetLatestPosts'] );
        add_shortcode( 'subpages_navigation', ['Theme', 'widgetSubpagesNavigation'] );
    }

    /**
     * Setup frontend functionalities
     * Register functionalities
     * Modify default HTML output
     */
    public static function setupFrontend() {
        add_theme_support( 'html5', array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
        ));

        add_action( 'init', ['Theme', 'disableEmoji'] );
        add_filter( 'the_content', ['Theme', 'disableImgAutoP'] );
        add_action( 'wp_enqueue_scripts', ['Theme', 'registerAssets'] );
        add_filter( 'body_class', ['Theme', 'modifyBodyClass'] );
        add_filter( 'nav_menu_css_class', ['Theme', 'modifySpecificNavClasses'], 10, 2 );
        // add_action( 'wp_nav_menu', ['Theme', 'modifyNavClasses'] );
        // add_action( 'wp_list_pages', ['Theme', 'modifyNavClasses'] );
        // add_action( 'wp_list_categories', ['Theme', 'modifyNavClasses'] );
        add_filter( 'wp_get_attachment_link', ['Theme', 'galleryMediaLink'], 10, 4 );
        add_action( 'wp_print_styles', ['Theme', 'removeCF7Styles'], 100 );
        add_filter( 'excerpt_length', ['Theme', 'customExcerptLength'], 999);
        add_filter( 'the_content', ['Theme', 'addSvgSupport'] );
    }

    /**
     * Enable footer support for Elementor plugin
     */
    public static function enableElementorHeaderFooter() {
        add_theme_support( 'header-footer-elementor' );
    }

    /**
     * Initializes Titan Framework instance
     */
    public static function initTitan() {
        add_action( 'tf_create_options', function() {
            self::$titan = TitanFramework::getInstance(self::TEXT_DOMAIN);
        });
    }

    /**
     * Enqueues Theme backend assets
     * Adds styles, scripts and localizing ajax
     */
    public static function registerBackendAssets() {
        // $current_screen = get_current_screen();
        wp_enqueue_style( self::TEXT_DOMAIN . '-admin-style', get_template_directory_uri() . '/assets/css/gutenberg.css', array(), date('Ymd') . 'v1' );
        wp_enqueue_script( self::TEXT_DOMAIN . '-admin-scripts', get_template_directory_uri() . '/assets/js/gutenberg.js', array(), date('Ymd') . 'v2', true );
    }

	/**
     * Register navigation
     * Hook to after_setup_theme
     */
	public static function registerNavigation() {
        register_nav_menus( array(
            'main' => esc_html__( 'Main menu', self::TEXT_DOMAIN ),
        ));
    }

    /**
     * Setup login screen
     * Adds default logo and backlink
     */
    public static function setupLoginScreen() {
        add_action( 'login_enqueue_scripts', function() {
            echo '<style>
                #login h1 a, .login h1 a {
                    width: 100%;
                    background-image: url('. get_stylesheet_directory_uri() . '/assets/images/logo-login.png);
                    background-size: contain;
                    background-position: center center;
                    height: 108px;
                }
            </style>';
        });


        add_filter( 'login_headerurl', function() {
            return home_url();
        });
    }

    /**
     * Enqueues Theme assets
     * Adds styles, scripts and localizing ajax
     */
    public static function registerAssets() {
        wp_enqueue_style( self::TEXT_DOMAIN . '-style', get_stylesheet_uri(), array(), date('Ymd') . 's1' );

        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( self::TEXT_DOMAIN . '-plugins', get_template_directory_uri() . '/assets/js/vendor.js', array(), date('Ymd') . 's1', true );
        wp_enqueue_script( self::TEXT_DOMAIN . '-scripts', get_template_directory_uri() . '/assets/js/scripts.js', array(), date('Ymd') . 's1', true );

        wp_localize_script( self::TEXT_DOMAIN . '-scripts', 'wp_ajax', array('ajaxurl' => admin_url('admin-ajax.php')));
    }

    /**
     * Disables Emoji
     */
    public static function disableEmoji() {
        remove_action( 'admin_print_styles', 'print_emoji_styles' );
        remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
        remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
        remove_action( 'wp_print_styles', 'print_emoji_styles' );
        remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
        remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
        remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
    }

    /**
     * Add filter to body class
     * Removes dfault classes and adds custom ones
     *
     * @param $classes default classes added by Wordpress
     * @return string $classes stripped classes
     */
    public static function modifyBodyClass($classes) {
        global $post;

        if ( is_front_page() ) {
            $classes = array('page-home');
        }
        elseif ( is_404() ) {
            $classes = array('page-404');
        }
        elseif ( is_category() || is_tag() ) {
            $term = get_queried_object();
            $classes = array(
				'blog',
                'single-term',
                'term-' . $term->taxonomy,
                'term-' . $term->term_id
            );
        }

        elseif ( is_singular() ) {
            $classes = array(
                'single-' . $post->post_type,
                $post->post_type . '-' . $post->post_name
            );
        }

        return array_unique($classes);
    }

    /**
     * Add filter to menu items classes
     * Modifies generated classes
     *
     * @param $classes default item classes
     * @param $item specific menu item
     * @return array modified item classes
     */
    public static function modifySpecificNavClasses( $classes, $item ) {
        if ( ( !is_post_type_archive( 'post' ) && !is_singular( 'post' ) ) && $item->title == 'Blog' ) {
            $classes = array_diff( $classes, array( 'current_page_parent' ) );
        }

        return $classes;
    }

    /**
     * Add filter to navigation
     * Removes dfault classes and adds custom ones
     *
     * @param $content default classes added by Wordpress
     * @return string $content stripped classes
     */
    public static function modifyNavClasses($content) {
        $pattern = '# class=(\'|")([-\w ]+)(\'|")#';
        preg_match_all($pattern, $content, $class_attrs);
        $num_class_attrs = (isset($class_attrs)) ? count($class_attrs[0]) : 0;

        $replace = array (
            'current' => 'active',
            'menu-item-has-children' => 'has-children'
        );

        for ($i = 0; $i < $num_class_attrs; $i++) {
            $classes = array();

            foreach ($replace as $old => $new) {
                if ( strpos($class_attrs[2][$i], $old) !== false ) {
                    $classes[] = $new;
                }
            }

            if (count($classes) > 0) {
                $content = preg_replace("#{$class_attrs[0][$i]}#", ' class="'. implode(' ', $classes) .'"', $content);
            }
            else {
                $content = preg_replace("#{$class_attrs[0][$i]}#", '', $content);
            }

            $content = preg_replace('/id="menu-item-\d+" /', '', $content);
        }

        return $content;
    }

    /**
     * Adds filter to the content
     * Disables auto wrapping images into p tag
     *
     * @param $content default Wordpress output
     * @return string $content stripped content
     */
    public static function disableImgAutoP($content) {
        return preg_replace('/<p>(\s*)(<img .* \/>)(\s*)<\/p>/iU', '\2', $content);
    }

    /**
     * Get thumbnail background url
     *
     * @param int|void $post_id
     * @return string
     */
    public static function getThumbnailBg( $size = 'medium', $post_id = false ) {
        if ( !$post_id ) {
            global $post;
            $post_id = $post->ID;
        }

        $img = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), $size );

        if ( !$img ) {
            $img = array(self::asset('logo-post.jpg'));
        }

        return 'style="background-image: url(\''. $img[0] .'\');"';
    }

    /**
     * Modify gallery image url
     * Changes default full size image to be large
     */
    public static function galleryMediaLink( $content, $post_id, $size, $permalink ) {
        if (! $permalink) {
            $image = wp_get_attachment_image_src( $post_id, 'large' );
            $new_content = preg_replace('/href=\'(.*?)\'/', 'href=\'' . $image[0] . '\'', $content );
            return $new_content;
        } else {
            return $content;
        }
    }

    /**
     * Returns asset url of given filename
     *
     * @parem string $asset Name of the file to return its URL
     */
    public static function asset( $asset = '' ) {
        if ( strlen($asset) > 3 ) {
            $asset = get_template_directory_uri() . '/assets/images/' . $asset;
        }

        return $asset;
    }

    /**
     * Taxonomy helper
     * Saves custom term metadata
     */
    public static function saveTermMeta( $term_id, $meta_names ) {
        if ( $_POST[self::HELPER_INPUT] == self::HELPER_INPUT_VALUE ) {
            foreach ( $meta_names as $meta_name ) {
                $meta_value  = get_term_meta( $term_id, $meta_name, true );
                $new_meta_value = isset( $_POST[$meta_name] ) ? $_POST[$meta_name] : ''; //sanitize_text_field

                if ( $meta_value && '' === $new_meta_value ) {
                    delete_term_meta( $term_id, $meta_name );
                }
                elseif ( $meta_value !== $new_meta_value ) {
                    update_term_meta( $term_id, $meta_name, $new_meta_value, $meta_value);
                }
            }
        }
    }

    /**
     * Removes CF7 default styles
     */
    public static function removeCF7Styles() {
        wp_deregister_style('contact-form-7');
    }

    /**
     * Registers custom image sizes
     *
     */
    public static function setupImageUploads() {
        add_image_size( 'customfull', 1600, 1600 );
        add_filter( 'wp_generate_attachment_metadata', ['Theme', 'modifyFullImageUpload'] );
    }

    /**
     * Replaces Full image size with custom one
     *
     * @param $image_data
     * @return mixed
     */
    public static function modifyFullImageUpload($image_data) {
        // if there is no large image : return
        if (!isset($image_data['sizes']['customfull'])) return $image_data;

        // paths to the uploaded image and the large image
        $upload_dir = wp_upload_dir();
        $uploaded_image_location = $upload_dir['basedir'] . '/' .$image_data['file'];

        $current_subdir = substr($image_data['file'],0,strrpos($image_data['file'],"/"));
        $large_image_location = $upload_dir['basedir'] . '/'.$current_subdir.'/'.$image_data['sizes']['customfull']['file'];

        // delete the uploaded image
        unlink($uploaded_image_location);

        // rename the large image
        rename($large_image_location,$uploaded_image_location);

        // update image metadata and return them
        $image_data['width'] = $image_data['sizes']['customfull']['width'];
        $image_data['height'] = $image_data['sizes']['customfull']['height'];
        unset($image_data['sizes']['customfull']);

        return $image_data;
    }

    /**
     * Pagination for WP_Query
     *
     * @param string $pages
     * @param int $range
     * @return string
     */
    public static function pagination( $pages = '', $range = 5 ) {
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        $showitems = ($range * 2) + 1;

        if ($pages == '') {
            global $wp_query;

            $pages = $wp_query->max_num_pages;

            if(!$pages) {
                $pages = 1;
            }
        }

        if ( $pages != 1 ) {
            $html = '<ul class="widget pagination">';

            if ( $paged != 1 ) {
                $html .= '<li class="controls"><a href="'. get_pagenum_link(1) .'">&lsaquo;</a></li>';
            }

            for ($i=1; $i <= $pages; $i++) {
                if ( $pages != 1 && ( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems ) ) {
                    if ($paged == $i):
                        $html .= '<li class="active"><a href="#">'. $i .'</a></li>';
                    else:
                        $html .= '<li><a href="'. get_pagenum_link($i) .'">'.$i.'</a></li>';
                    endif;
                }
            }

            if ( $paged != $pages ) {
                $html .= '<li class="controls"><a href="'. get_pagenum_link($pages) .'">&rsaquo;</a></li>';
            }

            $html .= "</ul>";
        }

        return $html;
    }

    /**
     * Modifies WP Uploader
     * adds support for additional mime types
     */
    public static function uploadMimes( $mimes ) {
        $mimes['svg'] 	= 'image/svg';
        return $mimes;
    }

    /**
     * adds SVG support
     * @param $content
     * @return string|string[]
     */
    public static function addSvgSupport($content) {
        preg_match_all('#.*src="([^\"]+\.svg)".*#', $content, $imgs);
        //var_dump($imgs[1]);

        if ( isset($imgs[1]) ) {
            foreach ( $imgs[1] as $i => $img ) {
                $content = str_replace($imgs[0][$i], file_get_contents($imgs[1][$i]), $content);
            }
        }

        return $content;
    }

    /**
     * Gets Titan Framework Image
     *
     * @return string
     */
    public static function getImageURL( $metavalue, $post_id ) {
        $imageID = Theme::$titan->getOption( $metavalue, $post_id);

        if ( is_numeric( $imageID ) ) {
            $imageAttachment = wp_get_attachment_image_src( $imageID );
            $imageURL = $imageAttachment[0];
        }

        return $imageURL;
    }

    /**
     * Custom Excerpt Length
     * @param $length
     * @return int
     */
    public static function customExcerptLength( $length ) {
        return 20;
    }

    /**
     * Change the excerpt more string
     * @return string
     */
    public static function customExcerptMore( $more ) {
        return ' ...';
    }

    /**
     * Displays accordion widget
     * Shotrcode callback
     *
     * @return string HTMl of the accordion
     */
    public static function widgetAccordion($atts, $content = null) {
        extract(shortcode_atts( array(
            'title' => 'Learn more',
        ), $atts ));

        $output = '<div class="accordion">
            <h5>'. $title .'</h5>
            <div>'. apply_filters('the_content', $content) .'</div>
        </div>';

        return $output;
    }

    /**
     * Post listing (3 latest posts)
     * Shotrcode callback
     *
     * @return string HTMl of the listing
     */
    public static function widgetLatestPosts()
    {
        ob_start();

        query_posts(array(
            'post_type' => 'post',
            'posts_per_page' => 3,
        ));

        get_template_part('template/widgets/latest-posts');

        wp_reset_query();

        $output = ob_get_clean();

        return $output;
    }

    /**
     * Displays subpages navigation
     * Shotrcode callback
     *
     * @return string HTMl of the listing
     */
    public static function widgetSubpagesNavigation()
    {
        ob_start();

        global $post;

        $subpages = array();

        if ( $post->post_type == 'page' ) {
            $parent = $post;

            if ( $parent->post_parent != 0 ) {
                while ( $parent->post_parent != 0 ) {
                    $parent = get_post($parent->post_parent);
                }
            }

            $subpages = get_posts(array(
                'post_type' => $parent->post_type,
                'post_parent' => $parent->ID,
                'orderby' => 'menu_order',
                'order' => 'ASC',
            ));
        }

        set_query_var( 'subpages', $subpages );
        get_template_part('template/widgets/subpages-navigation');

        $output = ob_get_clean();

        return $output;
    }
}
