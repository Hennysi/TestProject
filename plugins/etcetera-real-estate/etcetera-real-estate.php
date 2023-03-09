<?php
    /**
     * Plugin Name: Etcetera Real Estate
     * Description: This plugin for test project in Etcetera Agency.
     * Version: 1.0
     * Author: Rostislav Demenko
     **/

    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }

    if ( ! class_exists( 'ERE' ) ) {
        class ERE {
            private $ere_widget;

            public function __construct() {
                // Register District Taxonomy
                add_action( 'init', array( $this, 'ERE_taxonomy' ) );

                // Register Real Estate Post Type
                add_action( 'init', array( $this, 'ERE_post_type' ) );

                // Add Real Estate Filter Shortcode
                add_shortcode( 'ERE_filter', array( $this, 'ERE_shortcode' ) );

                // Register Real Estate Widget
                $this->ere_widget = new ERE_Widget();

                // Filter by Eco Friendliness
                add_action( 'pre_get_posts', array( $this, 'ERE_order' ) );

            }

            public function ERE_taxonomy() {
                $labels = [
                    'name'              => 'Райони',
                    'singular_name'     => 'Район',
                    'search_items'      => 'Пошук району',
                    'all_items'         => 'Всі райони',
                    'view_item '        => 'Перегляд району',
                    'parent_item'       => 'Родитель району',
                    'parent_item_colon' => 'Родитель району:',
                    'edit_item'         => 'Редагувати район',
                    'update_item'       => 'Обновити район',
                    'add_new_item'      => 'Додати новий район',
                    'new_item_name'     => 'Назва нового району',
                    'menu_name'         => 'Райони',
                    'back_to_items'     => '← Назад до районів',
                ];
                $args = [
                    'label'             => '',
                    'labels'            => $labels,
                    'description'       => '',
                    'public'            => true,
                    'hierarchical'      => true,
                    'rewrite'           => true,
                    'capabilities'      => array(),
                    'meta_box_cb'       => null,
                    'show_admin_column' => false,
                    'show_in_rest'      => null,
                    'rest_base'         => null,
                ];

                register_taxonomy( 'district', [ 'real_estate' ], $args );
            }

            public function ERE_post_type() {
                $labels = [
                    'name'               => __( 'Об\'єкти нерухомості', 'rCode' ),
                    'singular_name'      => __( 'Об\'єкт нерухомості', 'rCode' ),
                    'menu_name'          => __( 'Об\'єкти нерухомості', 'rCode' ),
                    'name_admin_bar'     => __( 'Об\'єкти нерухомості', 'rCode' ),
                    'add_new'            => __( 'Додати новий', 'rCode' ),
                    'add_new_item'       => __( 'Додати нову нерухомість', 'rCode' ),
                    'new_item'           => __( 'Нова нерухомість', 'rCode' ),
                    'edit_item'          => __( 'Редагувати нерухомість', 'rCode' ),
                    'view_item'          => __( 'Переглянути нерухомість', 'rCode' ),
                    'all_items'          => __( 'Всі нерухомості', 'rCode' ),
                    'search_items'       => __( 'Пошук нерухомості', 'rCode' ),
                    'not_found'          => __( 'Нерухомість не знайдено.', 'rCode' ),
                    'not_found_in_trash' => __( 'В кошику не знайдено нерухомість.', 'rCode' )
                ];

                $args = [
                    'labels'             => $labels,
                    'public'             => true,
                    'publicly_queryable' => true,
                    'show_ui'            => true,
                    'show_in_menu'       => true,
                    'query_var'          => true,
                    'rewrite'            => array( 'slug' => 'real_estate' ),
                    'capability_type'    => 'post',
                    'has_archive'        => true,
                    'hierarchical'       => true,
                    'menu_position'      => null,
                    'supports'           => array( 'title', )
                ];

                register_post_type( 'real_estate', $args );
            }

            public function ERE_shortcode() {
                $filter_values = [];
                $estate_args = array(
                    'post_type'      => 'real_estate',
                    'posts_per_page' => - 1,
                );
                $estate = new WP_Query( $estate_args );
                if ( $estate->have_posts() ):
                    while ( $estate->have_posts() ): $estate->the_post();
                        $filter_values['number_of_floors'][] = get_field( 'number_of_floors' );

                        $building_type = get_field( 'building_type' );
                        $filter_values['building_type'][ $building_type['value'] ] = $building_type['label'];
                        $filter_values['eco_friendliness'][] = get_field( 'eco-friendliness' );
                    endwhile;
                    wp_reset_postdata();
                endif;

                $html = '<form id="ERE_filtering" method="POST">';
                $html .= '<input id="house_name" name="house_name" placeholder="Назва будинку">';
                $html .= '<input id="location_coordinates" name="location_coordinates" placeholder="Координати місцезнаходження">';
                $html .= '<select id="number_of_floors" name="number_of_floors">';
                $html .= '<option value="any">Кількість поверхів</option>';
                for ( $i = 1; $i <= max( $filter_values['number_of_floors'] ); $i ++ ) {
                    $html .= '<option value="' . $i . '">' . $i . '</option>';
                }
                $html .= '</select>';
                $html .= '<select id="building_type" name="building_type">';
                $html .= '<option value="any">Тип будівлі</option>';
                foreach ( $filter_values['building_type'] as $building_v => $building_n ) {
                    $html .= '<option value="' . $building_v . '">' . $building_n . '</option>';
                }
                $html .= '</select>';
                $html .= '<select id="eco_friendliness" name="eco_friendliness">';
                $html .= '<option value="any">Екологічність</option>';
                for ( $i = 1; $i <= max( $filter_values['eco_friendliness'] ); $i ++ ) {
                    $html .= '<option value="' . $i . '">' . $i . '</option>';
                }
                $html .= '</select>';
                $html .= ' <button type="submit">Знайти</button>';
                $html .= '</form>';

                return $html;
            }

            public function ERE_order( $query ) {
                if ( $query->is_archive() && $query->get( 'post_type' ) == 'real_estate' ) {
                    $query->set( 'meta_key', 'eco-friendliness' );
                    $query->set( 'orderby', 'meta_value_num' );
                    $query->set( 'order', 'DESC' );
                }
            }

        }
    }

    class ERE_Widget extends WP_Widget {

        function __construct() {
            parent::__construct( 'my_widget', // Widget ID
                __( 'My Widget', 'text_domain' ), // Widget name
                array( 'description' => __( 'A widget for displaying my custom content', 'text_domain' ), ) // Widget description
            );
        }

        public function widget( $args, $instance ) {
            echo $args['before_widget'];
            $filter_values = [];
            $estate_args = array(
                'post_type'      => 'real_estate',
                'posts_per_page' => - 1,
            );
            $estate = new WP_Query( $estate_args );
            if ( $estate->have_posts() ):
                while ( $estate->have_posts() ): $estate->the_post();
                    $filter_values['number_of_floors'][] = get_field( 'number_of_floors' );

                    $building_type = get_field( 'building_type' );
                    $filter_values['building_type'][ $building_type['value'] ] = $building_type['label'];
                    $filter_values['eco_friendliness'][] = get_field( 'eco-friendliness' );
                endwhile;
                wp_reset_postdata();
            endif;

            $html = '<form id="ERE_filtering" method="POST">';
            $html .= '<input id="house_name" name="house_name" placeholder="Назва будинку">';
            $html .= '<input id="location_coordinates" name="location_coordinates" placeholder="Координати місцезнаходження">';
            $html .= '<select id="number_of_floors" name="number_of_floors">';
            $html .= '<option value="any">Кількість поверхів</option>';
            for ( $i = 1; $i <= max( $filter_values['number_of_floors'] ); $i ++ ) {
                $html .= '<option value="' . $i . '">' . $i . '</option>';
            }
            $html .= '</select>';
            $html .= '<select id="building_type" name="building_type">';
            $html .= '<option value="any">Тип будівлі</option>';
            foreach ( $filter_values['building_type'] as $building_v => $building_n ) {
                $html .= '<option value="' . $building_v . '">' . $building_n . '</option>';
            }
            $html .= '</select>';
            $html .= '<select id="eco_friendliness" name="eco_friendliness">';
            $html .= '<option value="any">Екологічність</option>';
            for ( $i = 1; $i <= max( $filter_values['eco_friendliness'] ); $i ++ ) {
                $html .= '<option value="' . $i . '">' . $i . '</option>';
            }
            $html .= '</select>';
            $html .= ' <button type="submit">Знайти</button>';
            $html .= '</form>';

            echo $html;
            echo $args['after_widget'];
        }
    }

    add_action( 'widgets_init', function () {
        register_widget( 'ERE_Widget' );
    } );

    $ERE = new ERE();