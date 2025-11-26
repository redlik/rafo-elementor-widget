<?php
/**
 * Plugin Name: Rafo Elementor Widgets
 * Plugin URI:  https://example.com
 * Description: A starter/template plugin to add custom Elementor widgets.
 * Version:     0.1.0
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

function rafo_ew_activate() {
    // Run activation tasks here (capability setup, default options, etc.)
}

function rafo_ew_deactivate() {
    // Clean up tasks on deactivation if needed.
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

        // Register the widget with Elementor
        if ( class_exists( 'ItineraryWidget' ) ) {
            $widgets_manager->register( new \ItineraryWidget() );
        }
    }

    public function frontend_assets() {
        // Example: register frontend assets
//        wp_register_style( 'rafo-ew-frontend', RAFO_EW_URL . 'assets/css/frontend.css', array(), RAFO_EW_VERSION );
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