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

$product_id = $product->get_id();

// 1. Primary check: ACF Relationship field 'rx_type'
$rx_posts = get_field( 'rx_type', $product_id );
$has_rx   = ! empty( $rx_posts ) && is_array( $rx_posts );

// 2. Fallback check: Direct repeater 'lens_rx_types'
$has_direct_repeater = have_rows( 'lens_rx_types', $product_id );

// 3. Fallback: If no RX assigned to this product, query ALL published RX Type CPT posts
if ( ! $has_rx && ! $has_direct_repeater ) {
    $all_rx = get_posts( array(
        'post_type'      => 'rx_type',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'orderby'        => 'menu_order title',
        'order'          => 'ASC',
    ) );
    if ( ! empty( $all_rx ) ) {
        $rx_posts = $all_rx;
        $has_rx   = true;
    }
}
?>
<!-- STEP 1: TYPE -->
<section class="egc-step-panel is-active" data-step="1">

    <h2 class="egc-step-title"><?php esc_html_e( 'RX Type', 'eyeglass-customizer' ); ?></h2>
    <p class="egc-step-subtitle"><?php esc_html_e( 'Select the Type of Lenses', 'eyeglass-customizer' ); ?></p>

    <div class="egc-card-list" id="egc-rx-type-list">
        <?php if ( $has_rx ) : ?>
            <?php 
            foreach ( $rx_posts as $rx_post ) :
                $cpt_id = is_object( $rx_post ) ? $rx_post->ID : $rx_post;
                $rx_post_obj = get_post( $cpt_id );
                if ( ! $rx_post_obj ) continue;

                $raw_title      = $rx_post_obj->post_title;
                $rx_description = $rx_post_obj->post_content;
                $rx_badge       = get_field( 'global_card_extra_badge', $cpt_id );

                // Price detection: ACF field or title extraction
                $rx_price = get_field( 'global_card_extra_price', $cpt_id );
                if ( $rx_price === null || $rx_price === '' ) {
                    $rx_price = get_field( 'rx_type_price', $cpt_id );
                }
                if ( ( $rx_price === null || $rx_price === '' ) && preg_match( '/\$([\d\.]+)/', $raw_title, $price_match ) ) {
                    $rx_price = (float) $price_match[1];
                }
                $rx_price = is_numeric( $rx_price ) ? (float) $rx_price : 0;

                // Clean title if price was embedded in it
                $display_title = preg_replace( '/\s*\$[\d\.]+\s*$/', '', $raw_title );
                
                // Image handling
                $rx_image_url = get_the_post_thumbnail_url( $cpt_id, 'medium' );
                if ( ! $rx_image_url && have_rows( 'rx_type_sub_cards', $cpt_id ) ) {
                    while ( have_rows( 'rx_type_sub_cards', $cpt_id ) ) {
                        the_row();
                        $sub_img = get_sub_field( 'rx_type_sub_cards_image' );
                        if ( $sub_img ) {
                            $rx_image_url = is_array( $sub_img ) ? ( $sub_img['url'] ?? '' ) : $sub_img;
                            break;
                        }
                    }
                }

                $has_magnification = ( stripos( $display_title, 'reader' ) !== false || stripos( $display_title, 'reading' ) !== false ) ? 'true' : 'false';
                ?>
                <div class="egc-card egc-rx-type-card egc-clickable-card"
                    data-title="<?php echo esc_attr( $display_title ); ?>"
                    data-price="<?php echo esc_attr( $rx_price ); ?>"
                    data-has-magnification="<?php echo esc_attr( $has_magnification ); ?>">

                    <?php if ( ! empty( $rx_badge ) ) : ?>
                        <span class="egc-card-badge"><?php echo esc_html( $rx_badge ); ?></span>
                    <?php endif; ?>

                    <div class="egc-card-image">
                        <?php if ( ! empty( $rx_image_url ) ) : ?>
                            <img src="<?php echo esc_url( $rx_image_url ); ?>" alt="<?php echo esc_attr( $display_title ); ?>" />
                        <?php endif; ?>
                    </div>

                    <div class="egc-card-body">
                        <h3 class="egc-card-title"><?php echo esc_html( $display_title ); ?></h3>
                        <?php if ( ! empty( $rx_description ) ) : ?>
                            <div class="egc-card-description">
                                <?php 
                                $lines = array_filter( array_map( 'trim', explode( "\n", strip_tags( $rx_description ) ) ) );
                                if ( ! empty( $lines ) ) : ?>
                                    <ul>
                                        <?php foreach ( $lines as $line ) : ?>
                                            <li><?php echo esc_html( $line ); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="egc-card-action">
                        <!-- Shows price ONLY if price exists and is greater than 0 -->
                        <?php if ( $rx_price > 0 ) : ?>
                            <span class="egc-card-price" style="font-weight: 700; font-size: 15px; color: #111;">
                                + <?php echo wp_kses_post( wc_price( $rx_price ) ); ?>
                            </span>
                        <?php endif; ?>

                        <button type="button" class="egc-btn egc-select-btn egc-select-rx-type">
                            <?php esc_html_e( 'Select', 'eyeglass-customizer' ); ?>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>

        <?php elseif ( $has_direct_repeater ) : ?>
            <?php while ( have_rows( 'lens_rx_types', $product_id ) ) : the_row(); ?>
                <?php
                $rx_title       = get_sub_field( 'rx_types_title' );
                $rx_image       = get_sub_field( 'rx_type_image' );
                $rx_description = get_sub_field( 'rx_type_description' );
                $rx_price       = get_sub_field( 'rx_type_price' );
                $rx_price       = is_numeric( $rx_price ) ? (float) $rx_price : 0;
                $rx_image_url   = is_array( $rx_image ) ? ( $rx_image['url'] ?? '' ) : ( is_numeric( $rx_image ) ? wp_get_attachment_image_url( $rx_image, 'full' ) : $rx_image );
                $has_magnification = ( stripos( $rx_title, 'reader' ) !== false || stripos( $rx_title, 'reading' ) !== false ) ? 'true' : 'false';
                ?>
                <div class="egc-card egc-rx-type-card egc-clickable-card"
                    data-title="<?php echo esc_attr( $rx_title ); ?>"
                    data-price="<?php echo esc_attr( $rx_price ); ?>"
                    data-has-magnification="<?php echo esc_attr( $has_magnification ); ?>">

                    <div class="egc-card-image">
                        <?php if ( ! empty( $rx_image_url ) ) : ?>
                            <img src="<?php echo esc_url( $rx_image_url ); ?>" alt="<?php echo esc_attr( $rx_title ); ?>" />
                        <?php endif; ?>
                    </div>

                    <div class="egc-card-body">
                        <h3 class="egc-card-title"><?php echo esc_html( $rx_title ); ?></h3>
                        <?php if ( ! empty( $rx_description ) ) : ?>
                            <div class="egc-card-description"><?php echo wp_kses_post( $rx_description ); ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="egc-card-action">
                        <!-- Shows price ONLY if price exists and is greater than 0 -->
                        <?php if ( $rx_price > 0 ) : ?>
                            <span class="egc-card-price" style="font-weight: 700; font-size: 15px; color: #111;">
                                + <?php echo wp_kses_post( wc_price( $rx_price ) ); ?>
                            </span>
                        <?php endif; ?>

                        <button type="button" class="egc-btn egc-select-btn egc-select-rx-type">
                            <?php esc_html_e( 'Select', 'eyeglass-customizer' ); ?>
                        </button>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>

    <!-- Bottom Right Price Summary -->
    <div class="egc-step-price-summary">
        <div class="egc-summary-row">
            <span class="egc-summary-label"><?php esc_html_e( 'Frame Price:', 'eyeglass-customizer' ); ?></span>
            <span class="egc-summary-value egc-frame-price-val"><?php echo wc_price( $product->get_price() ); ?></span>
        </div>
        <div class="egc-summary-row">
            <span class="egc-summary-label"><?php esc_html_e( 'Lenses Price:', 'eyeglass-customizer' ); ?></span>
            <span class="egc-summary-value egc-lens-price-val"><?php echo wc_price( 0 ); ?></span>
        </div>
        <div class="egc-summary-row egc-grand-total">
            <span class="egc-summary-label"><?php esc_html_e( 'Grand Total:', 'eyeglass-customizer' ); ?></span>
            <span class="egc-summary-value egc-grand-total-val"><?php echo wc_price( $product->get_price() ); ?></span>
        </div>
    </div>
</section>