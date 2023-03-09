<?php
    /**
     * Understrap Child Theme functions and definitions
     *
     * @package UnderstrapChild
     */

    // Exit if accessed directly.
    defined( 'ABSPATH' ) || exit;


    /**
     * Removes the parent themes stylesheet and scripts from inc/enqueue.php
     */
    function understrap_remove_scripts() {
        wp_dequeue_style( 'understrap-styles' );
        wp_deregister_style( 'understrap-styles' );

        wp_dequeue_script( 'understrap-scripts' );
        wp_deregister_script( 'understrap-scripts' );
    }

    add_action( 'wp_enqueue_scripts', 'understrap_remove_scripts', 20 );


    /**
     * Enqueue our stylesheet and javascript file
     */
    function theme_enqueue_styles() {

        // Get the theme data.
        $the_theme = wp_get_theme();

        $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '';
        // Grab asset urls.
        $theme_styles = "/css/child-theme{$suffix}.css";
        $theme_scripts = "/js/child-theme{$suffix}.js";
        $theme_custom_scripts = "/js/custom{$suffix}.js";

        wp_enqueue_style( 'child-understrap-styles', get_stylesheet_directory_uri() . $theme_styles, array(), $the_theme->get( 'Version' ) );
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'child-understrap-scripts', get_stylesheet_directory_uri() . $theme_scripts, array(), $the_theme->get( 'Version' ), true );
        wp_enqueue_script( 'child-custom-scripts', get_stylesheet_directory_uri() . $theme_custom_scripts, array( 'jquery' ), $the_theme->get( 'Version' ), true );
        wp_localize_script( 'child-custom-scripts', 'ajax', array(
            'url' => admin_url( 'admin-ajax.php' )
        ) );
        if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
            wp_enqueue_script( 'comment-reply' );
        }
    }

    add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );


    /**
     * Load the child theme's text domain
     */
    function add_child_theme_textdomain() {
        load_child_theme_textdomain( 'understrap-child', get_stylesheet_directory() . '/languages' );
    }

    add_action( 'after_setup_theme', 'add_child_theme_textdomain' );


    /**
     * Overrides the theme_mod to default to Bootstrap 5
     *
     * This function uses the `theme_mod_{$name}` hook and
     * can be duplicated to override other theme settings.
     *
     * @return string
     */
    function understrap_default_bootstrap_version() {
        return 'bootstrap5';
    }

    add_filter( 'theme_mod_understrap_bootstrap_version', 'understrap_default_bootstrap_version', 20 );


    /**
     * Loads javascript for showing customizer warning dialog.
     */
    function understrap_child_customize_controls_js() {
        wp_enqueue_script( 'understrap_child_customizer', get_stylesheet_directory_uri() . '/js/customizer-controls.js', array( 'customize-preview' ), '20130508', true );
    }

    add_action( 'customize_controls_enqueue_scripts', 'understrap_child_customize_controls_js' );


    add_action( 'wp_ajax_estate_filter', 'estate_filter' );
    add_action( 'wp_ajax_nopriv_estate_filter', 'estate_filter' );

    function estate_filter() {
        $response = array();

        $house_name = isset( $_POST['house_name'] ) ? $_POST['house_name'] : null;
        $location_coordinates = isset( $_POST['location_coordinates'] ) ? $_POST['location_coordinates'] : null;
        $number_of_floors = isset( $_POST['number_of_floors'] ) ? $_POST['number_of_floors'] : null;
        $building_type = isset( $_POST['building_type'] ) ? $_POST['building_type'] : null;
        $eco_friendliness = isset( $_POST['eco_friendliness'] ) ? $_POST['eco_friendliness'] : null;
        $paged = isset( $_POST['paged'] ) ? $_POST['paged'] : 1;

        $estate_args = array(
            'post_type'      => 'real_estate',
            'posts_per_page' => 5,
            'paged'          => $paged,
            'meta_query'     => [

            ]
        );

        if ( $house_name ) {
            $estate_args['meta_query'][] = [
                'key'     => 'house_name',
                'value'   => $house_name,
                'compare' => 'LIKE'
            ];
        }

        if ( $location_coordinates ) {
            $estate_args['meta_query'][] = [
                'key'     => 'location_coordinates',
                'value'   => $location_coordinates,
                'compare' => 'LIKE'
            ];
        }

        if ( $number_of_floors && $number_of_floors !== 'any' ) {
            $estate_args['meta_query'][] = [
                'key'     => 'number_of_floors',
                'value'   => $number_of_floors,
                'compare' => 'LIKE'
            ];
        }

        if ( $building_type && $building_type !== 'any' ) {
            $estate_args['meta_query'][] = [
                'key'     => 'building_type',
                'value'   => $building_type,
                'compare' => '='
            ];
        }

        if ( $eco_friendliness && $eco_friendliness !== 'any' ) {
            $estate_args['meta_query'][] = [
                'key'     => 'eco-friendliness',
                'value'   => $eco_friendliness,
                'compare' => 'LIKE'
            ];
        }


        $estate = new WP_Query( $estate_args );
        if ( $estate->have_posts() ):
            ob_start();
            while ( $estate->have_posts() ): $estate->the_post(); ?>
                <div class="col-md-4 estate-item">
                    <?php if ( $image = get_field( 'image' ) ): ?>
                        <div class="image">
                            <a href="<?php echo get_the_permalink() ?>">
                                <?php echo wp_get_attachment_image( $image['ID'], 'large' ); ?>
                            </a>
                        </div>
                        <?php if ( $house_name = get_field( 'house_name' ) ): ?>
                            <div class="name"><?php echo $house_name ?></div>
                        <?php else: ?>
                            <div class="name"><?php the_title() ?></div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php endwhile;
            $response['result'] = ob_get_clean();
            wp_reset_postdata();
        else:
            $response['result'] = '<div class="estate-item-not-found">Вибачте, але нічого не знайдено. Спробуйте щось інше</div>';
        endif;
        $max_num_pages = $estate->max_num_pages;
        if ( $max_num_pages > 1 ) {
            for ( $i = 1; $i <= $max_num_pages; $i ++ ) {
                $current = $i === 1 ? 'active' : null;

                $pagination .= '<li class="page-item">';
                $pagination .= '<a href="#" data-page="' . $i . '" class="page-link ' . $current . '">' . $i . '</a>';
                $pagination .= '</li>';
            }
        }

        $response['pagination'] = $pagination;
        $response['pages'] = $estate->max_num_pages;

        wp_send_json( $response );
    }