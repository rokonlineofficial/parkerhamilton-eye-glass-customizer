<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

if ( ! $product instanceof WC_Product ) {
	$product = wc_get_product( get_the_ID() );
}

if ( ! $product instanceof WC_Product ) {
	return;
}

$product_id   = $product->get_id();
$frame_price  = $product->get_price();
$featured_img = get_the_post_thumbnail_url( $product_id, 'large' );
if ( ! $featured_img ) {
	$featured_img = wc_placeholder_img_src( 'large' );
}

$pd_reference_image = plugin_dir_url( dirname( dirname( __FILE__ ) ) ) . 'assets/images/eyes.jpg';

// Fetch Brand and Category for the left sidebar display
$product_cats = wc_get_product_category_list( $product_id, ', ' );
$product_brand = '';
$brands = wp_get_post_terms( $product_id, 'product_brand', array( 'fields' => 'all' ) );
if ( empty( $brands ) || is_wp_error( $brands ) ) {
	$brands = wp_get_post_terms( $product_id, 'pwb-brand', array( 'fields' => 'all' ) );
}
if ( ! empty( $brands ) && ! is_wp_error( $brands ) ) {
	$brand_links = array();
	foreach ( $brands as $b ) {
		$brand_links[] = '<a href="' . esc_url( get_term_link( $b ) ) . '" class="egc-brand-link" style="color:#C9A76D; text-decoration:underline;">' . esc_html( $b->name ) . '</a>';
	}
	$product_brand = implode( ', ', $brand_links );
}

if ( ! function_exists( 'egc_render_power_select' ) ) {
	function egc_render_power_select( $name ) {
		ob_start();
		?>
		<select name="<?php echo esc_attr( $name ); ?>" class="egc-select">
			<option value="none"><?php esc_html_e( 'None', 'eyeglass-customizer' ); ?></option>
			<?php
			for ( $v = -20.00; $v <= 20.00; $v += 0.25 ) {
				if ( abs( $v ) < 0.001 ) continue;
				$label = ( $v > 0 ? '+' : '' ) . number_format( $v, 2 );
				printf( '<option value="%1$s">%1$s</option>', esc_attr( $label ) );
			}
			?>
		</select>
		<?php
		return ob_get_clean();
	}
}

if ( ! function_exists( 'egc_render_add_select' ) ) {
	function egc_render_add_select( $name ) {
		ob_start();
		?>
		<select name="<?php echo esc_attr( $name ); ?>" class="egc-select">
			<option value="none"><?php esc_html_e( 'None', 'eyeglass-customizer' ); ?></option>
			<?php
			for ( $v = 0.75; $v <= 3.50; $v += 0.25 ) {
				$label = '+' . number_format( $v, 2 );
				printf( '<option value="%1$s">%1$s</option>', esc_attr( $label ) );
			}
			?>
		</select>
		<?php
		return ob_get_clean();
	}
}
?>

<div id="egc-fullscreen-modal" class="egc-fullscreen-modal" style="display:none;">
    <div class="egc-modal-inner">

        <button type="button" id="egc-close-rx-modal" class="egc-close-btn" aria-label="<?php esc_attr_e( 'Close', 'eyeglass-customizer' ); ?>">
            &times;
        </button>

        <div class="egc-modal-content">

            <div id="egc-wizard"
                class="egc-wizard"
                data-product-id="<?php echo esc_attr( $product_id ); ?>"
                data-frame-price="<?php echo esc_attr( $frame_price ); ?>"
                data-ajax-url="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>"
                data-security="<?php echo esc_attr( wp_create_nonce( 'egc_customizer_nonce' ) ); ?>">

                <!-- Step Progress Header Bar -->
                <div class="egc-step-header">
                    <button type="button" id="egc-step-back-btn" class="egc-nav-btn egc-back-btn" style="display:none;">
                        <?php esc_html_e( 'Back', 'eyeglass-customizer' ); ?>
                    </button>

                    <ul class="egc-step-progress">
                        <li class="egc-progress-item is-active" data-step="1">
                            <span class="egc-progress-dot"></span>
                            <span class="egc-progress-label"><?php esc_html_e( 'RX Type', 'eyeglass-customizer' ); ?></span>
                        </li>
                        <li class="egc-progress-item" data-step="2">
                            <span class="egc-progress-dot"></span>
                            <span class="egc-progress-label"><?php esc_html_e( 'Prescription', 'eyeglass-customizer' ); ?></span>
                        </li>
                        <li class="egc-progress-item" data-step="3">
                            <span class="egc-progress-dot"></span>
                            <span class="egc-progress-label"><?php esc_html_e( 'Material', 'eyeglass-customizer' ); ?></span>
                        </li>
                        <li class="egc-progress-item" data-step="4">
                            <span class="egc-progress-dot"></span>
                            <span class="egc-progress-label"><?php esc_html_e( 'Coating', 'eyeglass-customizer' ); ?></span>
                        </li>
                        <li class="egc-progress-item" data-step="5">
                            <span class="egc-progress-dot"></span>
                            <span class="egc-progress-label"><?php esc_html_e( 'Features', 'eyeglass-customizer' ); ?></span>
                        </li>
                        <li class="egc-progress-item" data-step="6">
                            <span class="egc-progress-dot"></span>
                            <span class="egc-progress-label"><?php esc_html_e( 'Summary', 'eyeglass-customizer' ); ?></span>
                        </li>
                    </ul>

                    <button type="button" id="egc-close-rx-modal" class="egc-close-btn egc-header-close" aria-label="<?php esc_attr_e( 'Close', 'eyeglass-customizer' ); ?>">
                        <?php esc_html_e( 'Close', 'eyeglass-customizer' ); ?>
                    </button>
                </div>

                <!-- Two Column Layout -->
                <div class="egc-wizard-body">

                    <!-- Left Column: Product Image, Title, Swatch Badge & Moved Brand/Category with Anchor Color -->
                    <div class="egc-wizard-left">
                        <div class="egc-product-image-wrap">
                            <img src="<?php echo esc_url( $featured_img ); ?>" alt="<?php echo esc_attr( $product->get_name() ); ?>" id="egc-product-image" />
                        </div>
                        <div class="egc-product-title-wrap" style="text-align: center; margin-top: 15px;">
                            <h3 style="font-size: 15px; font-weight: 700; color: #111; margin: 0 0 6px 0; line-height: 1.4;">
                                <?php echo esc_html( $product->get_name() ); ?>
                            </h3>
                            <div id="egc-modal-swatch-badge" style="font-size: 13px; font-weight: 600; color: #555; margin-bottom: 12px;"></div>

                            <!-- Brand and Category moved here with anchor link color -->
                            <div class="egc-left-meta-details" style="font-size: 12px; color: #444; border-top: 1px solid #f0f0f0; padding-top: 10px; line-height: 1.6;">
                                <?php if ( ! empty( $product_brand ) ) : ?>
                                    <p style="margin: 0 0 4px 0;"><strong><?php esc_html_e( 'Brand:', 'eyeglass-customizer' ); ?></strong> <?php echo wp_kses_post( $product_brand ); ?></p>
                                <?php endif; ?>
                                <?php if ( ! empty( $product_cats ) ) : ?>
                                    <p style="margin: 0;"><strong><?php esc_html_e( 'Category:', 'eyeglass-customizer' ); ?></strong> <?php echo wp_kses_post( $product_cats ); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Step Wizard Panels -->
                    <div class="egc-wizard-right">
                        <?php EGC_Template_Loader::get_template( 'rx-type.php', array( 'product' => $product ) ); ?>
                        <?php EGC_Template_Loader::get_template( 'prescription.php', array( 'product' => $product, 'pd_reference_image' => $pd_reference_image ) ); ?>
                        <?php EGC_Template_Loader::get_template( 'material.php', array( 'product' => $product ) ); ?>
                        <?php EGC_Template_Loader::get_template( 'coating.php', array( 'product' => $product ) ); ?>
                        <?php EGC_Template_Loader::get_template( 'features.php', array( 'product' => $product ) ); ?>
                        <?php EGC_Template_Loader::get_template( 'summary.php', array( 'product' => $product, 'featured_img' => $featured_img ) ); ?>
                    </div>

                </div>

            </div>

        </div>
    </div>
</div>