<?php
/**
 * App api
 *
 * @since      1.0.0
 * @author     Beeketing
 *
 */

namespace Beeketing\SalesPop\Api;


use Beeketing\SalesPop\Data\Constant;
use BKSalesPopSDK\Api\CommonApi;
use BKSalesPopSDK\Data\AppCodes;
use BKSalesPopSDK\Libraries\SettingHelper;

class App extends CommonApi
{
    private $api_key;

    /**
     * App constructor.
     *
     * @param $api_key
     */
    public function __construct( $api_key )
    {
        $this->api_key = $api_key;
        $setting_helper = new SettingHelper();
        $setting_helper->set_app_setting_key( \BKSalesPopSDK\Data\AppSettingKeys::SALESPOP_KEY );
        $setting_helper->set_plugin_version( SALESPOP_VERSION );

        parent::__construct(
            $setting_helper,
            SALESPOP_PATH,
            SALESPOP_API,
            $api_key,
            AppCodes::SALESPOP,
            Constant::PLUGIN_ADMIN_URL
        );
    }

    /**
     * Get routers
     *
     * @return array
     */
    public function get_routers()
    {
        $result = $this->get( 'sales_pop/routers' );

        if ( $result && !isset( $result['errors'] ) ) {
            foreach ( $result as &$item ) {
                if ( strpos( $item, 'http' ) === false ) {
                    $end_point = SALESPOP_PATH;
                    if ( SALESPOP_ENVIRONMENT == 'local' ) {
                        $end_point = str_replace( '/app_dev.php', '', $end_point );
                    }
                    $item = $end_point . $item;
                }
            }

            return $result;
        }

        return array();
    }

    /**
     * Get api urls
     *
     * @return array
     */
    public function get_api_urls()
    {
        return array_merge( array(
            'app_status_url' => $this->get_app_status_url(),
            'app_status_update_url' => $this->get_app_status_update_url(),
            'notifications_url' => $this->get_notifications_url(),
            'notification_update_url' => $this->get_notification_update_url(),
            'notification_delete_url' => $this->get_notification_delete_url(),
        ), parent::get_api_urls() );
    }

    /**
     * Get notifications url
     *
     * @return string
     */
    private function get_notifications_url()
    {
        return $this->get_url( 'sales_pop/notifications' );
    }

    /**
     * Get notification update url
     *
     * @return string
     */
    private function get_notification_update_url()
    {
        return $this->get_url( 'sales_pop/notifications/{id}/{status}' );
    }

    /**
     * Get notification update url
     *
     * @return string
     */
    private function get_notification_delete_url()
    {
        return $this->get_url( 'sales_pop/notifications' );
    }

    /**
     * Get app status url
     *
     * @return string
     */
    private function get_app_status_url()
    {
        return $this->get_url( 'sales_pop/app_status' );
    }

    /**
     * Get app status update url
     *
     * @return string
     */
    private function get_app_status_update_url()
    {
        return $this->get_url( 'sales_pop/app_status/{status}' );
    }
}