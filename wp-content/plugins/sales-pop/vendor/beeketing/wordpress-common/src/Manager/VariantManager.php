<?php
/**
 * Created by PhpStorm.
 * User: tungquach
 * Date: 31/03/2017
 * Time: 17:17
 */

namespace BKSalesPopSDK\Manager;


use BKSalesPopSDK\Data\Constant;
use BKSalesPopSDK\Libraries\Helper;

class VariantManager
{
    /**
     * @var ImageManager
     */
    private $image_manager;

    /**
     * @var array
     */
    private static $terms = array();

    /**
     * @var array
     */
    private $option1s = array();

    /**
     * @var bool
     */
    private $is_option1s_loaded = false;

    /**
     * @var array
     */
    private $wc_products_variants = array();

    /**
     * @var array
     */
    private $wc_products_variants_images = array();

    /**
     * VariantManager constructor.
     */
    public function __construct()
    {
        $this->image_manager = new ImageManager();
    }

    /**
     * Set wc products variants
     * @param $wc_products_variants
     */
    public function set_wc_products_variants( $wc_products_variants )
    {
        $this->wc_products_variants = $wc_products_variants;
    }


    /**
     * Set wc products variants images
     * @param $images
     */
    public function set_wc_products_variants_images( $images )
    {
        $this->wc_products_variants_images = $images;
    }

    /**
     * Set option1s
     * @param $option1s
     */
    public function set_option1s( $option1s )
    {
        $this->option1s = $option1s;
        $this->is_option1s_loaded =  true;
    }

    /**
     * Get variants by product
     *
     * @param $product
     * @return array
     */
    public function get_variants_by_product( $product )
    {
        $variants = array();

        if ( $product->is_type( 'simple' ) ) {
            $variants[] = $this->format_variant( $product, $product );
        } else {
            $product_id = Helper::is_wc3() ? $product->get_id() : $product->id; // Check wc version
            if ( isset( $this->wc_products_variants[$product_id] ) ) {
                foreach ($this->wc_products_variants[$product_id] as $variant) {
                    if ( !$variant || !$variant->exists() ) {
                        continue;
                    }

                    $variant_id = $variant->get_id();
                    if ( $this->is_option1s_loaded ) {
                        $option1 = isset( $this->option1s[$variant_id] ) ? $this->option1s[$variant_id] : null;
                    } else {
                        $option1 = get_post_meta( $variant_id, '_beeketing_option1', true );
                    }
                    if ( $product->is_type( 'variable' ) || Helper::is_beeketing_hidden_name( $option1 ) ) {
                        $variants[] = $this->format_variant($variant, $product);
                    }
                }
            } else {
                $args = array(
                    'post_parent' => $product_id, // Check wc version
                    'post_type'   => 'product_variation',
                    'orderby'     => 'menu_order',
                    'order'       => 'ASC',
                    'fields'      => 'ids',
                    'post_status' => 'publish',
                    'numberposts' => -1
                );
                $variant_ids = get_posts( $args );

                foreach ( $variant_ids as $variant_id ) {
                    $variant = wc_get_product( $variant_id );
                    if ( !$variant || !$variant->exists() ) {
                        continue;
                    }

                    if ( $this->is_option1s_loaded ) {
                        $option1 = isset( $this->option1s[$variant_id] ) ? $this->option1s[$variant_id] : null;
                    } else {
                        $option1 = get_post_meta( $variant_id, '_beeketing_option1', true );
                    }
                    if ( $product->is_type( 'variable' ) || Helper::is_beeketing_hidden_name( $option1 ) ) {
                        $variants[] = $this->format_variant( $variant, $product );
                    }
                }
            }
        }

        return $variants;
    }

    /**
     * Get variant by id
     *
     * @param $id
     * @return array
     */
    public function get_variant_by_id( $id )
    {
        $variant = wc_get_product( $id );
        if ( !$variant ) {
            return array();
        }

        $parent_id = Helper::is_wc3() ? $variant->get_parent_id() : $variant->parent_id;
        if ( $parent_id ) {
            $parent = wc_get_product( $parent_id );
            $result = $this->format_variant( $variant, $parent );
        } else {
            $result = $this->format_variant( $variant, $variant );
        }

        return $result;
    }

    /**
     * Format variant
     *
     * @param $variant
     * @param $product
     * @return array
     */
    public function format_variant( $variant, $product )
    {
        // Check wc version
        if ( Helper::is_wc3() ) {
            $variant_id = $variant->get_id();
            $product_id = $product->get_id();
            $date_created = $variant->get_date_created();
            $date_modified = $variant->get_date_modified();
        } else {
            $variant_id = $variant->is_type( 'variation' ) ? $variant->get_variation_id() : $variant->id;
            $product_id = $product->is_type( 'variation' ) ? $product->get_variation_id() : $product->id;
            $date_created = $variant->date_created;
            $date_modified = $variant->date_modified;
        }

        // Get variant attributes
        $attributes = array();
        $variant_name = array();
        if ( $variant->is_type( 'variation' ) ) {
            $product_attributes = array();
            if ( $product->is_type( 'variable' ) ) {
                $product_attributes = $product->get_variation_attributes();
            }
            // Variation attributes
            foreach ( $variant->get_variation_attributes() as $attribute_name => $attribute ) {
                // Taxonomy-based attributes are prefixed with `pa_`, otherwise simply `attribute_`
                $product_attribute_name = str_replace('attribute_', null, $attribute_name);
                if (
                    !$attribute &&
                    isset( $product_attributes[$product_attribute_name] ) &&
                    is_array( $product_attributes[$product_attribute_name] )
                ) {
                    // Get first attribute if any attribute
                    $attribute = array_shift( $product_attributes[$product_attribute_name] );
                }

                $attributes[$attribute_name] = $attribute;
                // Get term by slug
                $term_name = null;
                $term_key = $product_attribute_name . $attribute;
                if ( isset( self::$terms[$term_key] ) ) {
                    $term_name = self::$terms[$term_key];
                } else {
                    $term = get_term_by('slug', $attribute, $product_attribute_name);
                    if ($term) {
                        self::$terms[$term_key] = $term->name;
                        $term_name = $term->name;
                    }
                }
                $variant_name[] = $term_name ? $term_name : $attribute;
            }

        }

        $option1 = null;
        if ( !$product->is_type( 'simple' ) ) {
            if ($this->is_option1s_loaded) {
                $option1 = isset($this->option1s[$variant_id]) ? $this->option1s[$variant_id] : null;
            } else {
                $option1 = get_post_meta($variant_id, '_beeketing_option1', true);
            }
        }

        $images = array();
        if ( $variant->is_type( 'variation' ) ) {
            $images = isset( $this->wc_products_variants_images[$variant_id] ) ?
                $this->wc_products_variants_images[$variant_id] :
                $this->image_manager->get_product_images_by_product( $variant );
        }

        $result = array(
            'id' => $variant_id,
            'product_id' => $product_id,
            'barcode' => '',
            'image_id' => isset($images[0]['id']) ? $images[0]['id'] : '',
            'title' => $option1 ? $option1 : ( $variant_name ? implode( '/', $variant_name ) : $variant->get_title() ),
            'price' => $variant->get_price(),
            'price_compare' => $variant->get_regular_price() ? $variant->get_regular_price() : '',
            'option1' => $option1 ? $option1 : ( isset( $variant_name[0] ) ? $variant_name[0] : $variant->get_title() ),
            'option2' => isset( $variant_name[1] ) ? $variant_name[1] : '',
            'option3' => isset( $variant_name[2] ) ? $variant_name[2] : '',
            'grams' => '',
            'position' => '',
            'sku' => $variant->get_sku(),
            'inventory_management' => Constant::PLATFORM,
            'inventory_policy' => $variant->managing_stock() || $variant->get_price() === '' ? 0 : 1,
            'inventory_quantity' => $variant->get_price() === '' ? 0 : $variant->get_stock_quantity(),
            'fulfillment_service' => '',
            'weight' => $variant->get_weight() ? $variant->get_weight() : '',
            'weight_unit' => '',
            'requires_shipping' => '',
            'taxable' => $variant->is_taxable(),
            'updated_at' => Helper::format_date( $date_modified ),
            'created_at' => Helper::format_date( $date_created ),
            'in_stock' => $variant->is_in_stock(),
            'attributes' => $attributes,
        );

        return $result;
    }

    /**
     * Create variant
     *
     * @param $product
     * @param $content
     * @return array|\WP_Error
     */
    public function create_variant( $product, $content )
    {
        // Check option1
        if ( !isset( $content[ 'option1' ] ) ) {
            return new \WP_Error( 'beeketing_error', 'Option1 is required' );
        }

        // Get product data
        if ( !$product ) {
            return new \WP_Error( 'beeketing_error', 'Product not found' );
        }

        // Check variant exists
        foreach ( $product['variants'] as $variant ) {
            if ( $variant['option1'] == $content['option1'] ) {
                return new \WP_Error( 'beeketing_error', 'Variant existed' );
            }
        }

        // If product variation doesn't exist, add one
        $product_id = $product['id'];
        $admin = get_users( 'orderby=nicename&role=administrator&number=1' );
        $variation = array(
            'post_author'       => $admin[0]->ID,
            'post_status'       => 'publish',
            'post_name'         => 'product-' . $product_id . '-variation',
            'post_parent'       => $product_id,
            'post_title'        => $content['option1'],
            'post_type'         => 'product_variation',
            'comment_status'    => 'closed',
            'ping_status'       => 'closed',
        );

        // Insert
        $variant_id = wp_insert_post( $variation );

        // Update meta
        if ( isset( $content['price'] ) ) {
            update_post_meta( $variant_id, '_price', $content['price'] );
            update_post_meta( $variant_id, '_regular_price', $content['price'] );
        }

        if ( isset( $content['option1'] ) ) {
            update_post_meta( $variant_id, '_beeketing_option1', $content['option1'] );
        }

        // Check simple product
        $check_product = wc_get_product( $product_id );
        if ( $check_product->is_type( 'simple' ) ) {
            // If downloadable product
            $downloadable = get_post_meta($product_id, '_downloadable', true);
            if ($downloadable) {
                update_post_meta($variant_id, '_downloadable', $downloadable);
                $downloadable_files = get_post_meta($product_id, '_downloadable_files', true);
                update_post_meta($variant_id, '_downloadable_files', $downloadable_files);
            }

            // If virtual product
            $virtual = get_post_meta($product_id, '_virtual', true);
            if ($virtual) {
                update_post_meta($variant_id, '_virtual', $virtual);
            }
        }

        if ( isset( $content['sku'] ) ) {
            update_post_meta( $variant_id, '_sku', $content['sku'] );
        }

        if ( isset( $content['attributes'] ) ) {
            foreach ( $content['attributes'] as $name => $attribute ) {
                update_post_meta( $variant_id, $name, $attribute );
            }
        }

        return $this->get_variant_by_id( $variant_id );
    }

    /**
     * Update variant
     *
     * @param $id
     * @param $content
     * @return array|\WP_Error
     */
    public function update_variant( $id, $content )
    {
        $variant = get_post( $id );

        if ( ! $variant ) {
            return new \WP_Error( 'beeketing_error', 'Variant not found' );
        }

        // Update meta
        if ( isset( $content['price'] ) ) {
            update_post_meta( $id, '_price', $content['price'] );
            update_post_meta( $id, '_regular_price', $content['price'] );
        }

        if ( isset( $content['price_compare'] ) ) {
            update_post_meta( $id, '_sale_price', $content['price_compare'] );
        }

        if ( isset( $content['inventory_policy'] ) && $content['inventory_policy'] ) {
            update_post_meta( $id, '_manage_stock', 'no' );
            delete_post_meta( $id, '_backorders' );
        }

        if ( isset( $content['inventory_quantity'] ) ) {
            update_post_meta( $id, '_stock', $content['inventory_quantity'] );
        }

        if ( isset( $content['sku'] ) ) {
            update_post_meta( $id, '_sku', $content['sku'] );
        }

        if ( isset( $content['option1'] ) ) {
            update_post_meta( $id, '_beeketing_option1', $content['option1'] );
        }

        if ( isset( $data['attributes'] ) ) {
            foreach ( $data['attributes'] as $name => $attribute ) {
                update_post_meta( $id, $name, $attribute );
            }
        }

        if ( isset( $content['title'] ) ) {
            wp_update_post( array(
                'ID' => $id,
                'post_title' => $content['title'],
            ) );
        }

        return $this->get_variant_by_id( $id );
    }

    /**
     * Delete variant
     *
     * @param $variant_id
     * @return bool
     */
    public function delete_variant( $variant_id )
    {
        $variation = get_post( $variant_id );
        if ( $variation && 'product_variation' == $variation->post_type ) {
            return wp_delete_post( $variant_id );
        }

        return false;
    }
}