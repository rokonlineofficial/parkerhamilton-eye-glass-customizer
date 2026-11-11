<?php
/**
 * Plugin Name: Rok Online Step Form Plugin
 * Description: Advanced custom six step form for Simple & Variable products.
 * Version: 3.0
 * Author: Rok Online
 * Author URI: https://rokonline.com/
 * Plugin URI: https://rokonline.com/
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once plugin_dir_path( __FILE__ ) . 'includes/class-egc-assets.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-egc-template-loader.php';

class Eye_Glass_Customizer {

    public function __construct() {
        $assets = new EGC_Assets();
        add_action( 'wp_enqueue_scripts', array( $assets, 'enqueue_assets' ), 999 );

        // 0. Render Product Color Swatches above Add to Cart area
        add_action( 'woocommerce_before_add_to_cart_form', array( $this, 'render_product_color_swatches' ), 5 );

        // 1. Simple Products: Replace native add-to-cart form
        add_action( 'woocommerce_before_add_to_cart_form', array( $this, 'egc_start_capture_native_form' ), 6 );
        add_action( 'woocommerce_after_add_to_cart_form', array( $this, 'egc_replace_native_form' ), 999 );

        // 2. Variable Products: Render custom RX button
        add_action( 'woocommerce_before_variations_form', array( $this, 'render_variable_rx_button' ), 5 );
        add_action( 'woocommerce_single_variation', array( $this, 'render_variable_rx_button' ), 15 );
        
        // Render modal globally on product page
        add_action( 'wp_footer', array( $this, 'render_fullscreen_modal' ) );

        // AJAX Handlers
        add_action( 'wp_ajax_egc_add_customized_product', array( $this, 'handle_add_to_cart_ajax' ) );
        add_action( 'wp_ajax_nopriv_egc_add_customized_product', array( $this, 'handle_add_to_cart_ajax' ) );

        // Recalculate custom combined price in Cart & Checkout
        add_action( 'woocommerce_before_calculate_totals', array( $this, 'calculate_custom_cart_item_price' ), 20, 1 );

        // Display customizer choices in Cart & Checkout tables
        add_filter( 'woocommerce_get_item_data', array( $this, 'display_custom_cart_item_data' ), 10, 2 );

        // Permanently save customizer choices to WooCommerce Order Line Item Meta
        add_action( 'woocommerce_checkout_create_order_line_item', array( $this, 'save_custom_data_to_order_line_item' ), 10, 4 );

        // Admin Order Details: Display complete customization as a clean HTML table
        add_action( 'woocommerce_after_order_itemmeta', array( $this, 'render_admin_order_item_table' ), 10, 3 );
    }

    /**
     * Render Product Color Swatches directly from ACF 'lens_variations' repeater
     */
    public function render_product_color_swatches() {
        global $product;

        if ( ! $product instanceof WC_Product ) {
            return;
        }

        $product_id = $product->get_id();

        if ( ! have_rows( 'lens_variations', $product_id ) ) {
            return;
        }
        ?>
        <div class="egc-swatches-wrapper">
            <span class="egc-swatch-label"><?php esc_html_e( 'Select Color:', 'eyeglass-customizer' ); ?></span>

            <div class="egc-swatches-list">
                <?php
                $first        = true;
                $active_title = '';
                $active_label = '';
                $active_price = 0;
                $active_img   = '';

                while ( have_rows( 'lens_variations', $product_id ) ) : the_row();
                    $color_name  = get_sub_field( 'color_name' );
                    $swatch_img  = get_sub_field( 'swatch_image' );
                    $color_price = get_sub_field( 'color_price' );
                    $color_price = is_numeric( $color_price ) ? (float) $color_price : 0;

                    $price_label = $color_price > 0
                        ? '+' . html_entity_decode( wp_strip_all_tags( wc_price( $color_price ) ), ENT_QUOTES, 'UTF-8' )
                        : '';

                    $img_url = '';
                    if ( is_array( $swatch_img ) ) {
                        $img_url = $swatch_img['url'] ?? '';
                    } elseif ( is_numeric( $swatch_img ) ) {
                        $img_url = (string) wp_get_attachment_image_url( $swatch_img, 'medium' );
                    } else {
                        $img_url = (string) $swatch_img;
                    }

                    if ( $first ) {
                        $active_title = $color_name;
                        $active_label = $price_label;
                        $active_price = $color_price;
                        $active_img   = $img_url;
                    }
                    ?>
                    <div class="egc-swatch-item <?php echo $first ? 'is-active' : ''; ?>"
                         data-color-name="<?php echo esc_attr( $color_name ); ?>"
                         data-color-price="<?php echo esc_attr( $color_price ); ?>"
                         data-color-price-label="<?php echo esc_attr( $price_label ); ?>"
                         data-swatch-img="<?php echo esc_url( $img_url ); ?>">

                        <div class="egc-swatch-thumb-box">
                            <?php if ( ! empty( $img_url ) ) : ?>
                                <img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $color_name ); ?>" />
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php
                    $first = false;
                endwhile;
                ?>
            </div>

            <div class="egc-active-color-name" id="egc-active-color-name">
                <?php echo esc_html( $active_title ); ?>
            </div>
            <div class="egc-active-color-price" id="egc-active-color-price">
                <?php echo esc_html( $active_label ); ?>
            </div>
        </div>
        <?php
    }

    /**
     * Render RX Modal Trigger Button for Variable Products
     */
    public function render_variable_rx_button() {
        static $rendered = false;
        if ( $rendered ) return;
        $rendered = true;
        ?>
        <div class="egc-variable-rx-wrapper" style="margin-bottom: 15px;">
            <button type="button" id="egc-open-rx-modal" class="egc-btn egc-btn-purple">
                <?php esc_html_e( 'Add Custom or RX Lenses', 'eyeglass-customizer' ); ?>
            </button>
        </div>
        <?php
    }

    public function egc_start_capture_native_form() {
        global $product;
        if ( $product instanceof WC_Product && ! $product->is_type( 'variable' ) ) {
            ob_start();
        }
    }

    public function egc_replace_native_form() {
        global $product;
        if ( $product instanceof WC_Product && ! $product->is_type( 'variable' ) ) {
            ob_end_clean();
            $this->render_custom_add_to_cart_area();
        }
    }

    /**
     * Render Custom Add to Cart area for Simple Products
     */
    public function render_custom_add_to_cart_area() {
        global $product;
        if ( ! $product instanceof WC_Product || $product->is_type( 'variable' ) ) {
            return;
        }
        ?>
        <div class="egc-actions-wrapper">
            <button type="button" id="egc-open-rx-modal" class="egc-btn egc-btn-purple">
                <?php esc_html_e( 'Add Custom or RX Lenses', 'eyeglass-customizer' ); ?>
            </button>

            <form class="cart egc-add-to-cart-form" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype="multipart/form-data">
                <div class="egc-qty-cart-container">
                    <select name="quantity" id="egc-quantity-select" class="egc-qty-select" aria-label="<?php esc_attr_e( 'Quantity', 'eyeglass-customizer' ); ?>">
                        <?php for ( $i = 1; $i <= 10; $i++ ) : ?>
                            <option value="<?php echo esc_attr( $i ); ?>"><?php echo esc_html( $i ); ?></option>
                        <?php endfor; ?>
                    </select>

                    <button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="egc-btn egc-btn-add-to-cart single_add_to_cart_button">
                        <?php echo esc_html( $product->single_add_to_cart_text() ); ?>
                    </button>
                </div>
            </form>
        </div>
        <?php
    }

    public function render_fullscreen_modal() {
        if ( ! is_product() ) {
            return;
        }
        EGC_Template_Loader::get_template( 'wizard-modal.php' );
    }

    /**
     * Handle AJAX Add to Cart request
     */
    public function handle_add_to_cart_ajax() {
        check_ajax_referer( 'egc_customizer_nonce', 'security' );

        if ( null === WC()->cart ) {
            wc_load_cart();
        }

        $product_id     = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
        $variation_id   = isset( $_POST['variation_id'] ) ? absint( $_POST['variation_id'] ) : 0;
        $selections_raw = isset( $_POST['selections'] ) ? wp_unslash( $_POST['selections'] ) : '';

        if ( ! $product_id ) {
            wp_send_json_error( array( 'message' => 'Invalid Product ID' ) );
        }

        $product = wc_get_product( $product_id );
        if ( ! $product ) {
            wp_send_json_error( array( 'message' => 'Product not found' ) );
        }

        $passed_product_id    = $product_id;
        $passed_variation_id  = 0;
        $variation_attributes = array();

        if ( $product->is_type( 'variable' ) ) {
            if ( $variation_id > 0 ) {
                $passed_variation_id = $variation_id;
                $variation_product   = wc_get_product( $variation_id );
                if ( $variation_product ) {
                    $variation_attributes = $variation_product->get_variation_attributes();
                }
            } else {
                $children = $product->get_children();
                if ( ! empty( $children ) ) {
                    $passed_variation_id = reset( $children );
                    $variation_product   = wc_get_product( $passed_variation_id );
                    if ( $variation_product ) {
                        $variation_attributes = $variation_product->get_variation_attributes();
                    }
                }
            }
        }

        $selections_data = json_decode( $selections_raw, true );

        // Handle uploaded prescription file
        if ( ! empty( $_FILES['egc_prescription_file']['name'] ) ) {
            if ( ! function_exists( 'wp_handle_upload' ) ) {
                require_once ABSPATH . 'wp-admin/includes/file.php';
                require_once ABSPATH . 'wp-admin/includes/image.php';
                require_once ABSPATH . 'wp-admin/includes/media.php';
            }

            $uploaded_file    = $_FILES['egc_prescription_file'];
            $upload_overrides = array( 'test_form' => false );
            $movefile         = wp_handle_upload( $uploaded_file, $upload_overrides );

            if ( $movefile && ! isset( $movefile['error'] ) ) {
                if ( ! isset( $selections_data['prescription'] ) || ! is_array( $selections_data['prescription'] ) ) {
                    $selections_data['prescription'] = array();
                }
                $selections_data['prescription']['fileUrl']  = $movefile['url'];
                $selections_data['prescription']['fileName'] = basename( $movefile['file'] );
            }
        }

        $cart_item_data = array(
            'egc_customization' => $selections_data,
            'egc_fixed_base'    => (float) ( $passed_variation_id ? wc_get_product( $passed_variation_id )->get_price() : $product->get_price() )
        );

        $passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $passed_product_id, 1, $passed_variation_id, $variation_attributes );

        if ( $passed_validation ) {
            $cart_item_key = WC()->cart->add_to_cart( $passed_product_id, 1, $passed_variation_id, $variation_attributes, $cart_item_data );

            if ( $cart_item_key ) {
                wp_send_json_success();
            }
        }

        $errors = wc_get_notices( 'error' );
        wc_clear_notices();
        $error_message = ! empty( $errors ) ? strip_tags( implode( ' ', array_column( $errors, 'notice' ) ) ) : 'Failed to add item to cart.';

        wp_send_json_error( array( 'message' => $error_message ) );
        wp_die();
    }

    /**
     * Recalculate custom pricing on cart and checkout
     */
    public function calculate_custom_cart_item_price( $cart ) {
        if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
            return;
        }

        foreach ( $cart->get_cart() as $cart_item ) {
            if ( isset( $cart_item['egc_customization'] ) ) {
                $customization = $cart_item['egc_customization'];
                $base_price    = isset( $cart_item['egc_fixed_base'] ) ? (float) $cart_item['egc_fixed_base'] : (float) $cart_item['data']->get_price();
                $extra_price   = 0;

                if ( ! empty( $customization['prices'] ) && is_array( $customization['prices'] ) ) {
                    $p = $customization['prices'];
                    $extra_price += isset( $p['swatch'] ) ? (float) $p['swatch'] : 0;
                    $extra_price += isset( $p['rxType'] ) ? (float) $p['rxType'] : 0;
                    $extra_price += isset( $p['material'] ) ? (float) $p['material'] : 0;
                    $extra_price += isset( $p['coating'] ) ? (float) $p['coating'] : 0;
                    $extra_price += isset( $p['prism'] ) ? (float) $p['prism'] : 0;
                    $extra_price += isset( $p['magnification'] ) ? (float) $p['magnification'] : 0;
                }

                if ( ! empty( $customization['features'] ) && is_array( $customization['features'] ) ) {
                    foreach ( $customization['features'] as $feature ) {
                        if ( isset( $feature['price'] ) && empty( $feature['isStandard'] ) ) {
                            $extra_price += (float) $feature['price'];
                        }
                    }
                }

                $cart_item['data']->set_price( $base_price + $extra_price );
            }
        }
    }

    /**
     * Display customizer data on Cart and Checkout pages
     */
    public function display_custom_cart_item_data( $item_data, $cart_item ) {
        if ( isset( $cart_item['egc_customization'] ) ) {
            $custom = $cart_item['egc_customization'];

            if ( ! empty( $custom['swatch']['title'] ) ) {
                $swatch_val = esc_html( $custom['swatch']['title'] );
                if ( ! empty( $custom['swatch']['price'] ) && (float)$custom['swatch']['price'] > 0 ) {
                    $swatch_val .= ' (+' . wc_price( $custom['swatch']['price'] ) . ')';
                }
                $item_data[] = array(
                    'name'  => __( 'Frame Color', 'eyeglass-customizer' ),
                    'value' => $swatch_val,
                );
            }

            if ( ! empty( $custom['rxType'] ) ) {
                $item_data[] = array(
                    'name'  => __( 'RX Type', 'eyeglass-customizer' ),
                    'value' => esc_html( $custom['rxType'] ),
                );
            }

            if ( ! empty( $custom['material'] ) ) {
                $item_data[] = array(
                    'name'  => __( 'Material', 'eyeglass-customizer' ),
                    'value' => esc_html( $custom['material'] ),
                );
            }

            if ( ! empty( $custom['coating'] ) ) {
                $item_data[] = array(
                    'name'  => __( 'Coating', 'eyeglass-customizer' ),
                    'value' => esc_html( $custom['coating'] ),
                );
            }

            if ( ! empty( $custom['features'] ) && is_array( $custom['features'] ) ) {
                $feature_titles = array_column( $custom['features'], 'title' );
                $item_data[] = array(
                    'name'  => __( 'Features', 'eyeglass-customizer' ),
                    'value' => esc_html( implode( ', ', $feature_titles ) ),
                );
            }

            if ( ! empty( $custom['engravingCustomText'] ) ) {
                $item_data[] = array(
                    'name'  => __( 'Custom Engraving Text', 'eyeglass-customizer' ),
                    'value' => esc_html( $custom['engravingCustomText'] ),
                );
            }
        }

        return $item_data;
    }

    /**
     * Save customized data permanently to WooCommerce Order Line Item Meta
     */
    public function save_custom_data_to_order_line_item( $item, $cart_item_key, $values, $order ) {
        if ( isset( $values['egc_customization'] ) ) {
            $custom = $values['egc_customization'];

            $item->add_meta_data( '_egc_full_customization', $custom, true );

            if ( ! empty( $custom['swatch']['title'] ) ) {
                $item->add_meta_data( __( 'Frame Color', 'eyeglass-customizer' ), $custom['swatch']['title'], true );
            }
            if ( ! empty( $custom['rxType'] ) ) {
                $item->add_meta_data( __( 'RX Type', 'eyeglass-customizer' ), $custom['rxType'], true );
            }
            if ( ! empty( $custom['material'] ) ) {
                $item->add_meta_data( __( 'Material', 'eyeglass-customizer' ), $custom['material'], true );
            }
            if ( ! empty( $custom['coating'] ) ) {
                $item->add_meta_data( __( 'Coating', 'eyeglass-customizer' ), $custom['coating'], true );
            }
            if ( ! empty( $custom['engravingCustomText'] ) ) {
                $item->add_meta_data( __( 'Custom Engraving Text', 'eyeglass-customizer' ), $custom['engravingCustomText'], true );
            }
        }
    }

    /**
     * Render Customer Customization Details as an HTML Table in WordPress Admin Order Page
     */
    public function render_admin_order_item_table( $item_id, $item, $product ) {
        $custom = $item->get_meta( '_egc_full_customization' );
        if ( empty( $custom ) || ! is_array( $custom ) ) {
            return;
        }

        $rx     = $custom['prescription'] ?? array();
        $swatch = $custom['swatch'] ?? array();
        ?>
        <div class="egc-admin-order-customization" style="margin-top: 12px; padding: 12px; background: #fdfdfd; border: 1px solid #ccd0d4; border-radius: 4px; font-size: 13px;">
            <h4 style="margin: 0 0 10px 0; color: #1d2327; font-size: 14px; border-bottom: 2px solid #800080; padding-bottom: 4px;">
                👓 <?php esc_html_e( 'Customer Lens & Frame Customization', 'eyeglass-customizer' ); ?>
            </h4>

            <table class="widefat striped" style="border: 1px solid #e2e4e7;">
                <tbody>
                    <?php if ( ! empty( $swatch['title'] ) ) : ?>
                    <tr>
                        <td style="width: 25%; font-weight: bold;"><?php esc_html_e( 'Frame Color Swatch', 'eyeglass-customizer' ); ?></td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <?php if ( ! empty( $swatch['img'] ) ) : ?>
                                    <img src="<?php echo esc_url( $swatch['img'] ); ?>" style="width: 42px; height: 42px; object-fit: contain; border: 1px solid #ccc; border-radius: 3px; background:#fff;" />
                                <?php endif; ?>
                                <strong><?php echo esc_html( $swatch['title'] ); ?></strong>
                                <?php if ( ! empty( $swatch['price'] ) && (float)$swatch['price'] > 0 ) : ?>
                                    <span style="color: #2b9348; font-weight: bold;">(+<?php echo wc_price( $swatch['price'] ); ?>)</span>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>

                    <?php if ( ! empty( $custom['rxType'] ) ) : ?>
                    <tr>
                        <td style="font-weight: bold;"><?php esc_html_e( 'RX Type', 'eyeglass-customizer' ); ?></td>
                        <td><?php echo esc_html( $custom['rxType'] ); ?></td>
                    </tr>
                    <?php endif; ?>

                    <!-- Prescription details -->
                    <?php if ( ! empty( $rx ) ) : ?>
                    <tr>
                        <td style="font-weight: bold; vertical-align: top;"><?php esc_html_e( 'Prescription (OD / OS)', 'eyeglass-customizer' ); ?></td>
                        <td>
                            <?php if ( ( $rx['mode'] ?? '' ) === 'magnification' ) : ?>
                                <strong><?php esc_html_e( 'Magnification Power:', 'eyeglass-customizer' ); ?></strong> <?php echo esc_html( $rx['magnificationPower'] ?? '-' ); ?>
                            <?php elseif ( ( $rx['mode'] ?? '' ) === 'upload' ) : ?>
                                <em><?php esc_html_e( 'Uploaded File Method', 'eyeglass-customizer' ); ?></em>
                            <?php elseif ( ( $rx['mode'] ?? '' ) === 'send_later' ) : ?>
                                <em><?php esc_html_e( 'Customer will send later via Email/Fax', 'eyeglass-customizer' ); ?></em>
                            <?php else : ?>
                                <table style="width: 100%; max-width: 500px; border-collapse: collapse; text-align: center; margin-bottom: 6px;" border="1">
                                    <tr style="background:#f0f0f1;">
                                        <th></th><th>Sph</th><th>Cyl</th><th>Axis</th><th>Add</th>
                                    </tr>
                                    <tr>
                                        <th>Right (OD)</th>
                                        <td><?php echo esc_html( $rx['od']['sph'] ?? '-' ); ?></td>
                                        <td><?php echo esc_html( $rx['od']['cyl'] ?? '-' ); ?></td>
                                        <td><?php echo esc_html( $rx['od']['axis'] ?? '-' ); ?></td>
                                        <td><?php echo esc_html( $rx['od']['add'] ?? '-' ); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Left (OS)</th>
                                        <td><?php echo esc_html( $rx['os']['sph'] ?? '-' ); ?></td>
                                        <td><?php echo esc_html( $rx['os']['cyl'] ?? '-' ); ?></td>
                                        <td><?php echo esc_html( $rx['os']['axis'] ?? '-' ); ?></td>
                                        <td><?php echo esc_html( $rx['os']['add'] ?? '-' ); ?></td>
                                    </tr>
                                </table>

                                <div>
                                    <strong>PD: </strong>
                                    <?php if ( ( $rx['pdType'] ?? '' ) === 'dual' ) : ?>
                                        Left PD: <strong><?php echo esc_html( $rx['pdLeft'] ?? 'N/A' ); ?></strong> | Right PD: <strong><?php echo esc_html( $rx['pdRight'] ?? 'N/A' ); ?></strong>
                                    <?php else : ?>
                                        Near/Single PD: <strong><?php echo esc_html( $rx['pdNear'] ?? 'N/A' ); ?></strong>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <?php if ( ! empty( $rx['dob']['month'] ) && ! empty( $rx['dob']['year'] ) ) : ?>
                                <div style="margin-top: 4px;">
                                    <strong><?php esc_html_e( 'Wearer DOB:', 'eyeglass-customizer' ); ?></strong>
                                    <?php echo esc_html( $rx['dob']['month'] . ' / ' . $rx['dob']['year'] ); ?>
                                </div>
                            <?php endif; ?>

                            <?php if ( ! empty( $custom['prices']['prism'] ) && (float)$custom['prices']['prism'] > 0 ) : ?>
                                <div style="margin-top: 4px; color: #b32d00;">
                                    <strong><?php esc_html_e( 'Prism Correction:', 'eyeglass-customizer' ); ?></strong> Yes (+<?php echo wc_price( $custom['prices']['prism'] ); ?>)
                                </div>
                            <?php endif; ?>

                            <?php if ( ! empty( $rx['fileUrl'] ) ) : ?>
                                <div style="margin-top: 6px;">
                                    <strong><?php esc_html_e( 'Prescription File:', 'eyeglass-customizer' ); ?></strong>
                                    <a href="<?php echo esc_url( $rx['fileUrl'] ); ?>" target="_blank" style="display:inline-block; background:#2271b1; color:#fff; padding:3px 8px; border-radius:3px; text-decoration:none;">
                                        📥 <?php echo esc_html( $rx['fileName'] ?: 'Download File' ); ?> ↗
                                    </a>
                                </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endif; ?>

                    <?php if ( ! empty( $custom['material'] ) ) : ?>
                    <tr>
                        <td style="font-weight: bold;"><?php esc_html_e( 'Material', 'eyeglass-customizer' ); ?></td>
                        <td><?php echo esc_html( $custom['material'] ); ?></td>
                    </tr>
                    <?php endif; ?>

                    <?php if ( ! empty( $custom['coating'] ) ) : ?>
                    <tr>
                        <td style="font-weight: bold;"><?php esc_html_e( 'Coating', 'eyeglass-customizer' ); ?></td>
                        <td><?php echo esc_html( $custom['coating'] ); ?></td>
                    </tr>
                    <?php endif; ?>

                    <!-- Additional Options & Custom Engraving Text Display -->
                    <?php if ( ! empty( $custom['features'] ) && is_array( $custom['features'] ) ) : ?>
                    <tr>
                        <td style="font-weight: bold; vertical-align: top;"><?php esc_html_e( 'Additional Options', 'eyeglass-customizer' ); ?></td>
                        <td>
                            <ul style="margin: 0; padding-left: 18px;">
                                <?php foreach ( $custom['features'] as $f ) : ?>
                                    <li>
                                        <strong><?php echo esc_html( $f['title'] ); ?></strong>
                                        <?php if ( ! empty( $f['isStandard'] ) || empty( $f['price'] ) ) : ?>
                                            <span style="color:#2e8b3d; font-weight:bold;">(<?php esc_html_e( 'Standard Included', 'eyeglass-customizer' ); ?>)</span>
                                        <?php else : ?>
                                            (+<?php echo wc_price( $f['price'] ); ?>)
                                        <?php endif; ?>

                                        <?php if ( ! empty( $f['customText'] ) ) : ?>
                                            <div style="font-size:12px; color:#555; margin-left:5px;">
                                                ✍️ <em>Custom Text: "<?php echo esc_html( $f['customText'] ); ?>"</em>
                                            </div>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </td>
                    </tr>
                    <?php endif; ?>

                    <?php if ( ! empty( $custom['comments'] ) ) : ?>
                    <tr>
                        <td style="font-weight: bold;"><?php esc_html_e( 'Customer Comments', 'eyeglass-customizer' ); ?></td>
                        <td><em><?php echo esc_html( $custom['comments'] ); ?></em></td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php
    }
}

new Eye_Glass_Customizer();