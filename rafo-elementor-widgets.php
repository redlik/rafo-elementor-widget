<?php
/**
 * Plugin Name: Rafo Elementor Widgets
 * Plugin URI:  https://example.com
 * Description: A starter/template plugin to add custom Elementor widgets.
 * Version:     0.9.0
 * Author:      Ralph
 * Text Domain: rafo-elementor-widgets
 * Domain Path: /languages
 */


if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'RAFO_EW_FILE', __FILE__ );
define( 'RAFO_EW_DIR', plugin_dir_path( RAFO_EW_FILE ) );
define( 'RAFO_EW_URL',  plugin_dir_url( RAFO_EW_FILE ) );
define( 'RAFO_EW_VERSION', '0.1.0' );

/* Activation / Deactivation */
register_activation_hook( RAFO_EW_FILE, 'rafo_ew_activate' );
register_deactivation_hook( RAFO_EW_FILE, 'rafo_ew_deactivate' );

add_action( 'acf/init', 'add_tour_banner_field_group' );

function rafo_ew_activate() {
       	
    }

function rafo_ew_deactivate() {
    // Clean up tasks on deactivation if needed.
}

function add_tour_banner_field_group() {
    if (!is_field_group_exists('Tour Banner')) {
        if( function_exists('acf_add_local_field_group') ):

        acf_add_local_field_group(array(
                'key' => 'group_'. md5('tour_banner'),
                'title' => 'Tour Banner',
                'fields' => array(
                    array(
                        'key' => 'field_6929bb74bed86',
                        'label' => 'Tour banner',
                        'name' => 'tour_banner',
                        'aria-label' => '',
                        'type' => 'text',
                        'instructions' => 'Insert text for the banner (max 20 characters)',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'maxlength' => 20,
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                    ),
                    array(
                        'key' => 'field_6932cff7e6bf8',
                        'label' => 'Banner Colour',
                        'name' => 'banner_colour',
                        'aria-label' => '',
                        'type' => 'select',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'choices' => array(
                            '#a80e03' => 'Red',
                            '#dbb716' => 'Yellow',
                            '#1679db' => 'Blue',
                            '#37db16' => 'Green',
                        ),
                        'default_value' => '',
                        'return_format' => 'value',
                        'multiple' => 0,
                        'allow_null' => 0,
                        'ui' => 0,
                        'ajax' => 0,
                        'placeholder' => 'Select banner colour',
                    ),
                ),
                'location' => array(
                    array(
                        array(
                            'param' => 'post_type',
                            'operator' => '==',
                            'value' => 'tours',
                        ),
                    ),
                ),
                'menu_order' => 0,
                'position' => 'normal',
                'style' => 'default',
                'label_placement' => 'top',
                'instruction_placement' => 'label',
                'hide_on_screen' => '',
                'active' => true,
                'description' => '',
                'show_in_rest' => 0,
            ));
        endif;
    }
}

function is_field_group_exists($value, $type='post_title') {
        $exists = false;
        if ($field_groups = get_posts(array('post_type'=>'acf-field-group'))) {
            foreach ($field_groups as $field_group) {
                error_log($field_group->$type);
                if ($field_group->$type == $value) {
                    $exists = true;
                }
            }
        }
        return $exists;
}

/**
 * Main plugin class
 */
final class Rafo_Elementor_Widgets {
    private static $instance = null;

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'plugins_loaded', array( $this, 'init' ) );
    }

    public function init() {
        $this->load_textdomain();

        if ( ! $this->is_elementor_active() ) {
            add_action( 'admin_notices', array( $this, 'admin_notice_elementor_required' ) );
            return;
        }

        // Register category and widgets with Elementor
        add_action( 'elementor/elements/categories_registered', array( $this, 'register_category' ) );
        add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );

        // Enqueue assets
        add_action( 'wp_enqueue_scripts', array( $this, 'frontend_assets' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_assets' ) );
    }

    private function is_elementor_active() {
        return defined( 'ELEMENTOR_VERSION' ) || class_exists( '\Elementor\Plugin' );
    }

    public function admin_notice_elementor_required() {
        echo '<div class="notice notice-error"><p>' . esc_html__( 'Rafo Elementor Widgets requires Elementor to be installed and activated.', 'rafo-elementor-widgets' ) . '</p></div>';
    }

    public function load_textdomain() {
        load_plugin_textdomain( 'rafo-elementor-widgets', false, dirname( plugin_basename( RAFO_EW_FILE ) ) . '/languages' );
    }

    public function register_category( $elements_manager ) {
        $elements_manager->add_category(
            'rafo-elements',
            array(
                'title' => __( 'Rafo Widgets', 'rafo-elementor-widgets' ),
                'icon'  => 'fa fa-plug',
            )
        );
    }

    public function register_widgets( $widgets_manager ) {
        // Ensure Elementor widget base exists
        if ( ! class_exists( '\Elementor\Widget_Base' ) ) {
            return;
        }

        require_once RAFO_EW_DIR . 'inc/Elementor/ItineraryWidget.php';    
        require_once RAFO_EW_DIR . 'inc/Elementor/BannerWidget.php';    

        // Register the widget with Elementor
        if ( class_exists( 'ItineraryWidget' ) ) {
            $widgets_manager->register( new \ItineraryWidget() );
        }

        if ( class_exists( 'BannerWidget' ) ) {
            $widgets_manager->register( new \BannerWidget() );
        };
    }

    public function frontend_assets() {
        // Example: register frontend assets
        wp_register_style( 'rafo-ew-frontend', RAFO_EW_URL . 'assets/css/rafo-frontend.min.css', array(), RAFO_EW_VERSION );
        wp_register_style( 'rafo-ew-banner', RAFO_EW_URL . 'assets/css/rafo-banner.css', array(), RAFO_EW_VERSION );
        wp_register_style( 'rafo-styles', RAFO_EW_URL . 'assets/css/rafo-styles.css', array(), RAFO_EW_VERSION );
        wp_register_script( 'rafo-ew-frontend', RAFO_EW_URL . 'assets/js/rafo-frontend.js', array( 'jquery' ), RAFO_EW_VERSION, true );
        // Enqueue when needed in widget render or here globally:
        // wp_enqueue_style( 'rafo-ew-frontend' );
        // wp_enqueue_script( 'rafo-ew-frontend' );
    }

    public function admin_assets() {
        // Example: admin/editor assets
        wp_register_style( 'rafo-ew-admin', RAFO_EW_URL . 'assets/css/admin.css', array(), RAFO_EW_VERSION );
    }
}

/* Bootstrap */
Rafo_Elementor_Widgets::instance();