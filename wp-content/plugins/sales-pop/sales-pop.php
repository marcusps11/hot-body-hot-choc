<?php
/**
 * Plugin Name: Sales Pop
 * Plugin URI: https://beeketing.com/sales-pop?utm_channel=appstore&utm_medium=woolisting&utm_term=shortdesc&utm_fromapp=spop
 * Description: The best social-proof idea to increase customer's buying confidence and trust on your brand: show live sales notification popups to create the sense of a busy store and motivate customers to start buying.
 * Version: 1.1.7
 * Author: Beeketing
 * Author URI: https://beeketing.com
 */

use Beeketing\SalesPop\Api\App;
use BKSalesPopSDK\Api\BridgeApi;
use Beeketing\SalesPop\Data\Constant;
use Beeketing\SalesPop\PageManager\AdminPage;
use BKSalesPopSDK\Data\Setting;
use BKSalesPopSDK\Libraries\Helper;
use BKSalesPopSDK\Libraries\SettingHelper;


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Define plugin constants
define( 'SALESPOP_VERSION', '1.1.7' );
define( 'SALESPOP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'SALESPOP_PLUGIN_DIRNAME', __FILE__ );

// Require plugin autoload
require_once( SALESPOP_PLUGIN_DIR . 'vendor/autoload.php' );

// Get environment
$env = Helper::get_local_file_contents( SALESPOP_PLUGIN_DIR . 'env' );
$env = trim( $env );

if ( !$env ) {
    throw new Exception( 'Can not get env' );
}

define( 'SALESPOP_ENVIRONMENT', $env );

if ( ! class_exists( 'SalesPop' ) ):

    class SalesPop {
        /**
         * @var AdminPage $admin_page;
         *
         * @since 1.0.0
         */
        private $admin_page;

        /**
         * @var App $api_app
         *
         * @since 1.0.0
         */
        private $api_app;

        /**
         * @var BridgeApi
         *
         * @since 1.0.0
         */
        private $bridge_api;

        /**
         * @var SettingHelper
         *
         * @since 1.0.0
         */
        private $setting_helper;

        /**
         * @var string
         */
        private $api_key;

        /**
         * The single instance of the class
         *
         * @since 1.0.0
         */
        private static $_instance = null;

        /**
         * Get instance
         *
         * @return SalesPop
         * @since 1.0.0
         */
        public static function instance() {
            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self();
            }

            return self::$_instance;
        }

        /**
         * Constructor
         *
         * @since 1.0.0
         */
        public function __construct()
        {
            $this->setting_helper = new SettingHelper();
            $this->setting_helper->set_app_setting_key( \BKSalesPopSDK\Data\AppSettingKeys::SALESPOP_KEY );

            $this->api_key = $this->setting_helper->get_settings( Setting::SETTING_API_KEY );

            // Init api app
            $this->api_app = new App( $this->api_key );

            // Bridge api
            $this->bridge_api = new BridgeApi( \BKSalesPopSDK\Data\AppSettingKeys::SALESPOP_KEY, $this->api_key );

            // Plugin hooks
            $this->hooks();
        }

        /**
         * Hooks
         *
         * @since 1.0.0
         */
        private function hooks()
        {
            // Initialize plugin parts
            add_action( 'plugins_loaded', array( $this, 'init' ) );

            // Plugin updates
            add_action( 'admin_init', array( $this, 'admin_init' ) );

            if ( is_admin() ) {
                // Plugin activation
                add_action( 'activated_plugin', array( $this, 'plugin_activation') );
            }
        }

        /**
         * Init
         *
         * @since 1.0.0
         */
        public function init()
        {
            if ( is_admin() ) {
                $this->admin_page = new AdminPage();
            }
        }

        /**
         * Admin init
         */
        public function admin_init()
        {
            // Check plugin version
            $this->check_version();

            // Listen ajax
            $this->ajax();

            // Add the plugin page Settings and Docs links
            add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_links' ) );

            // Register plugin deactivation hook
            register_deactivation_hook( __FILE__, array( $this, 'plugin_deactivation' ) );

            // Enqueue scripts
            add_action( 'admin_enqueue_scripts', array( $this, 'register_script' ) );

            // Enqueue styles
            add_action( 'admin_enqueue_scripts', array( $this, 'register_style' ) );
        }

        /**
         * Enqueue and localize js
         *
         * @since 1.0.0
         * @param $hook
         */
        public function register_script( $hook )
        {
            // Load only on plugin page
            if (
                $hook != 'toplevel_page_' . Constant::PLUGIN_ADMIN_URL &&
                strpos( $hook, 'page_' . Constant::PLUGIN_UCS_URL ) === false
            ) {
                return;
            }

            $app_name = SALESPOP_ENVIRONMENT == 'local' ? 'app' : 'app.min';

            // Enqueue script
            wp_register_script( 'beeketing_app_script', plugins_url( 'dist/js/' . $app_name . '.js', __FILE__ ) , array( 'jquery' ), true, false );
            wp_enqueue_script( 'beeketing_app_script' );

            if ( $hook == 'toplevel_page_' . Constant::PLUGIN_ADMIN_URL ) {
                $current_user = wp_get_current_user();
                $routers = $this->api_app->get_routers();

                $beeketing_email = false;
                if ( !$this->api_key ) {
                    $beeketing_email = $this->api_app->get_user_email();
                }

                $beeketing_url = Helper::get_beeketing_plugin_url();
                if ( Helper::is_beeketing_active() ) {
                    $beeketing_url = admin_url('admin.php?page=' . Constant::BEEKETING_PLUGIN_ADMIN_URL);
                }

                $data = array(
                    'plugin_url' => plugins_url('/', __FILE__),
                    'routers' => $routers,
                    'api_urls' => $this->api_app->get_api_urls(),
                    'api_key' => $this->api_key,
                    'user_display_name' => $current_user->display_name,
                    'user_email' => $current_user->user_email,
                    'site_url' => site_url(),
                    'domain' => Helper::beeketing_get_shop_domain(),
                    'beeketing_email' => $beeketing_email,
                    'is_woocommerce_active' => Helper::is_woocommerce_active(),
                    'woocommerce_plugin_url' => Helper::get_woocommerce_plugin_url(),
                    'beeketing_url' => $beeketing_url,
                    'check_active' => Helper::is_beeketing_active(),
                );
            } else {
                $beeketing_apps_data = $this->api_app->get_more_apps();
                $available_app_codes = array(
                    \BKSalesPopSDK\Data\AppCodes::BOOSTSALES,
                    \BKSalesPopSDK\Data\AppCodes::CHECKOUTBOOST,
                    \BKSalesPopSDK\Data\AppCodes::MAILBOT,
                );
                foreach ( $beeketing_apps_data as $key => $beeketing_app_data ) {
                    // Remove not supported app
                    if (
                        !isset( $beeketing_app_data['app_code'] ) ||
                        !in_array( $beeketing_app_data['app_code'], $available_app_codes )
                    ) {
                        unset( $beeketing_apps_data[$key] );
                    }
                }

                $beeketing_url = Helper::get_beeketing_plugin_url();
                if ( Helper::is_beeketing_active() ) {
                    $beeketing_url = admin_url('admin.php?page=' . Constant::BEEKETING_PLUGIN_ADMIN_URL);
                }
                $data = array(
                    'plugin_url' => plugins_url( '/', __FILE__ ),
                    'beeketing_plugin_url' => $beeketing_url,
                    'apps' => $beeketing_apps_data,
                );
            }

            wp_localize_script('beeketing_app_script', 'beeketing_app_vars', $data);
        }

        /**
         * Enqueue style
         *
         * @since 1.0.0
         * @param $hook
         */
        public function register_style( $hook )
        {
            wp_register_style(
                'beeketing_sales_pop_global_style',
                plugins_url( 'dist/css/global.css', __FILE__ ), array(), true, 'all'
            );
            wp_enqueue_style( 'beeketing_sales_pop_global_style' );

            // Load only on plugin page
            if (
                $hook != 'toplevel_page_' . Constant::PLUGIN_ADMIN_URL &&
                strpos( $hook, 'page_' . Constant::PLUGIN_UCS_URL ) === false
            ) {
                return;
            }

            wp_register_style( 'beeketing_app_style', plugins_url( 'dist/css/app.css', __FILE__ ), array(), true, 'all' );
            wp_enqueue_style( 'beeketing_app_style' );
        }

        /**
         * Ajax
         *
         * @since 1.0.0
         */
        public function ajax()
        {
            add_action( 'wp_ajax_salespop_verify_account_callback', array( $this, 'verify_account_callback' ) );
            add_action( 'wp_ajax_salespop_app_tracking_callback', array( $this, 'app_tracking_callback' ) );
        }

        /**
         * App tracking callback
         */
        public function app_tracking_callback() {
            if ( !isset( $_POST['params'] ) ) {
                wp_send_json_error();
                wp_die();
            }

            $result = $this->api_app->send_tracking_event( $_POST['params'] );
            if ( $result ) {
                wp_send_json_success();
            } else {
                wp_send_json_error();
            }
            wp_die();
        }

        /**
         * Verify account callback
         *
         * @since 1.0.0
         */
        public function verify_account_callback() {
            $api_key = $this->api_app->register_shop();

            wp_send_json_success( array(
                'api_key' => $api_key,
            ) );
            wp_die();
        }

        /**
         * Plugin links
         *
         * @param $links
         * @return array
         * @since 1.0.0
         */
        public function plugin_links( $links )
        {
            $more_links = array();
            $more_links['settings'] = '<a href="' . admin_url( 'admin.php?page=' . Constant::PLUGIN_ADMIN_URL ) . '">' . __( 'Settings', 'beeketing' ) . '</a>';

            return array_merge( $more_links, $links );
        }

        /**
         * Check version
         *
         * @since 1.0.0
         */
        public function check_version()
        {
            // Update version number if its not the same
            if ( SALESPOP_VERSION != $this->setting_helper->get_settings( Setting::SETTING_PLUGIN_VERSION ) ) {
                $this->setting_helper->update_settings( Setting::SETTING_PLUGIN_VERSION, SALESPOP_VERSION );
            }
        }

        /**
         * Plugin activation
         *
         * @param $plugin
         * @since 1.0.0
         */
        public function plugin_activation( $plugin )
        {
            if ( $plugin == plugin_basename( __FILE__ ) ) {
                // Send tracking
                $event = \Beeketing\SalesPop\Data\Event::PLUGIN_FIRST_ACTIVATE;
                if ( $this->api_key ) {
                    $event = \Beeketing\SalesPop\Data\Event::PLUGIN_ACTIVATION;
                }
                $this->api_app->send_tracking_event( array(
                    'event' => $event,
                    'plugin' => 'sale_notification',
                ) );

                exit( wp_redirect( admin_url( 'admin.php?page=' . Constant::PLUGIN_ADMIN_URL ) ) );
            }
        }

        /**
         * Plugin deactivation
         */
        public function plugin_deactivation()
        {
            // Send tracking
            $this->api_app->send_tracking_event( array(
                'event' => \Beeketing\SalesPop\Data\Event::PLUGIN_DEACTIVATION,
                'plugin' => 'sale_notification',
            ) );
        }

        /**
         * Plugin uninstall
         *
         * @since 1.0.0
         */
        public function plugin_uninstall()
        {
            // Send tracking
            $this->api_app->send_tracking_event( array(
                'event' => \Beeketing\SalesPop\Data\Event::PLUGIN_UNINSTALL,
                'plugin' => 'sale_notification',
            ) );

            $this->api_app->uninstall_app();
            delete_option( \BKSalesPopSDK\Data\AppSettingKeys::SALESPOP_KEY );
        }
    }

    // Run plugin
    new SalesPop();

endif;