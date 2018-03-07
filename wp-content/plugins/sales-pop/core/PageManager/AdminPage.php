<?php
/**
 * Admin page setup
 *
 * @since      1.0.0
 * @author     Beeketing
 */

namespace Beeketing\SalesPop\PageManager;


use Beeketing\SalesPop\Data\Constant;
use BKSalesPopSDK\Libraries\Helper;

class AdminPage
{
    /**
     * Constructor
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        $this->hooks();
    }

    /**
     * Setup Hooks
     *
     * @since 1.0.0
     */
    public function hooks()
    {
        add_action( 'admin_menu', array( $this, 'app_menu' ), 200 );
    }

    /**
     * Sidebar tab
     *
     * @since 1.0.0
     */
    public function app_menu()
    {
        $mainTitle = __( 'Sales Pop' );
        $ucsTitle = __( 'Upsell & Cross-sell' );

        // Add to admin_menu
        add_menu_page(
            'Sales Pop Menu Page',
            $mainTitle,
            'edit_theme_options',
            Constant::PLUGIN_ADMIN_URL,
            array( $this, 'main_page_content' ),
            plugins_url( 'dist/images/icon.png', SALESPOP_PLUGIN_DIRNAME )
        );
    }

    /**
     * Main page content.
     *
     * @since 1.0.0
     */
    public function main_page_content()
    {
        include( SALESPOP_PLUGIN_DIR . 'templates/admin/index.html' );
    }

    /**
     * Up sell and cross sell page content.
     */
    public function ucs_page_content()
    {
        include( SALESPOP_PLUGIN_DIR . 'templates/admin/ucs.html' );
    }
}