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

$material_posts = get_field( 'lens_material', $product_id );
$has_materials  = ! empty( $material_posts ) && is_array( $material_posts );
$has_sub_brands = false;

if ( $has_materials ) {
    foreach ( $material_posts as $m_post ) {
        $mid = is_object( $m_post ) ? $m_post->ID : $m_post;
        if ( have_rows( 'material_sub_cards_one', $mid ) || have_rows( 'material_sub_cards_two', $mid ) ) {
            $has_sub_brands = true;
            break;
        }
    }
} else {
    $direct_mats = get_field( 'lens_materials', $product_id );
    if ( ! empty( $direct_mats ) && is_array( $direct_mats ) ) {
        $has_materials = true;
        foreach ( $direct_mats as $mat ) {
            if ( ! empty( $mat['lens_brands'] ) && is_array( $mat['lens_brands'] ) ) {
                $has_sub_brands = true;
                break;
            }
        }
    }
}

$wrapper_class = ( $has_materials && $has_sub_brands ) ? 'has-sub-cards' : 'no-sub-cards full-width-layout';
?>
<!-- STEP 3: MATERIAL -->
<section class="egc-step-panel" data-step="3">

    <div class="egc-material-wrapper <?php echo esc_attr( $wrapper_class ); ?>" id="egc-material-wrapper">
        
        <?php if ( $has_sub_brands ) : ?>
            <div class="egc-sub-cards-container" id="egc-sub-cards-container">
                <h2 class="egc-step-title"><?php esc_html_e( 'Select Lens Brand', 'eyeglass-customizer' ); ?></h2>
                <div class="egc-sub-cards-content" id="egc-sub-cards-content">
                    <p class="egc-placeholder-text"><?php esc_html_e( 'Please select a lens type from the right sidebar.', 'eyeglass-customizer' ); ?></p>
                </div>
            </div>
        <?php endif; ?>

        <div class="egc-main-cards-container">
            <h2 class="egc-step-title">
                <?php echo $has_sub_brands ? esc_html__( 'Select Lens Type', 'eyeglass-customizer' ) : esc_html__( 'Select Lens Material', 'eyeglass-customizer' ); ?>
            </h2>

            <div class="egc-card-list" id="egc-material-list">
                <?php if ( ! empty( $material_posts ) && is_array( $material_posts ) ) : ?>
                    <?php 
                    $mat_index = 0; 
                    foreach ( $material_posts as $m_post ) :
                        $mid = is_object( $m_post ) ? $m_post->ID : $m_post;
                        $m_obj = get_post( $mid );
                        if ( ! $m_obj ) continue;

                        $mat_title       = $m_obj->post_title;
                        $mat_description = $m_obj->post_content;
                        $mat_badge       = get_field( 'global_card_extra_badge', $mid );
                        $has_brands      = have_rows( 'material_sub_cards_one', $mid ) || have_rows( 'material_sub_cards_two', $mid );
                        ?>
                        
                        <div class="egc-card egc-material-card" data-material-id="mat-<?php echo esc_attr( $mat_index ); ?>" data-title="<?php echo esc_attr( $mat_title ); ?>">
                            
                            <?php if ( $mat_badge ) : ?>
                                <span class="egc-card-badge"><?php echo esc_html( $mat_badge ); ?></span>
                            <?php endif; ?>

                            <div class="egc-material-card-content">
                                <h3 class="egc-card-title"><?php echo esc_html( $mat_title ); ?></h3>
                                <?php if ( $mat_description ) : ?>
                                    <div class="egc-card-description"><?php echo wp_kses_post( $mat_description ); ?></div>
                                <?php endif; ?>
                            </div>

                            <button type="button" class="egc-btn egc-select-btn egc-select-material">
                                <?php esc_html_e( 'Select', 'eyeglass-customizer' ); ?>
                            </button>

                            <!-- Hidden Sub Cards Markup Container -->
                            <?php if ( $has_brands ) : ?>
                                <div class="egc-hidden-sub-brands" style="display: none;">
                                    <?php 
                                    if ( have_rows( 'material_sub_cards_one', $mid ) ) :
                                        while ( have_rows( 'material_sub_cards_one', $mid ) ) : the_row();
                                            $sub_img              = get_sub_field( 'material_sub_cards_one_image' );
                                            $brand_title          = get_sub_field( 'material_sub_cards_one_title' );
                                            $brand_benefits       = get_sub_field( 'material_sub_cards_one_badge' );
                                            $brand_recommendation = get_sub_field( 'material_sub_cards_one_description' );
                                            $brand_price          = get_sub_field( 'material_sub_cards_one_price' );
                                            $display_title        = $brand_title ?: $mat_title;

                                            $sub_img_url = is_array( $sub_img ) ? ( $sub_img['url'] ?? '' ) : ( is_numeric( $sub_img ) ? wp_get_attachment_image_url( $sub_img, 'full' ) : $sub_img );

                                            // Check for nested or related Material Sub Cards Two
                                            $sub_two_items = array();
                                            if ( have_rows( 'material_sub_cards_two' ) ) {
                                                while ( have_rows( 'material_sub_cards_two' ) ) {
                                                    the_row();
                                                    $s2_img   = get_sub_field( 'material_sub_cards_two_image' );
                                                    $s2_title = get_sub_field( 'material_sub_cards_two_title' );
                                                    $s2_desc  = get_sub_field( 'material_sub_cards_two_description' );
                                                    $s2_price = get_sub_field( 'material_sub_cards_two_price' );
                                                    $s2_badge = get_sub_field( 'material_sub_cards_two_badge' );

                                                    $s2_img_url = is_array( $s2_img ) ? ( $s2_img['url'] ?? '' ) : ( is_numeric( $s2_img ) ? wp_get_attachment_image_url( $s2_img, 'full' ) : $s2_img );

                                                    if ( ! empty( $s2_title ) ) {
                                                        $sub_two_items[] = array(
                                                            'title' => $s2_title,
                                                            'desc'  => $s2_desc,
                                                            'price' => is_numeric( $s2_price ) ? (float)$s2_price : 0,
                                                            'img'   => $s2_img_url,
                                                            'badge' => $s2_badge,
                                                        );
                                                    }
                                                }
                                            }

                                            $has_sub_two = ! empty( $sub_two_items );
                                            ?>
                                            <div class="egc-brand-card-block" style="margin-bottom: 20px;">
                                                
                                                <!-- Level 1 Sub Card Item -->
                                                <div class="egc-sub-card-item egc-clickable-sub-card"
                                                     data-has-sub-two="<?php echo $has_sub_two ? 'true' : 'false'; ?>"
                                                     data-title="<?php echo esc_attr( $display_title ); ?>"
                                                     data-price="<?php echo esc_attr( $brand_price ?: 0 ); ?>">
                                                    
                                                    <!-- Left Column: Image, Title & Prominent Price Display (Screenshot 3) -->
                                                    <div class="egc-sub-card-col-left">
                                                        <?php if ( $sub_img_url ) : ?>
                                                            <div class="egc-sub-card-image"><img src="<?php echo esc_url( $sub_img_url ); ?>" alt="<?php echo esc_attr( $display_title ); ?>" /></div>
                                                        <?php endif; ?>
                                                        <h4 class="egc-sub-card-title"><?php echo esc_html( $display_title ); ?></h4>

                                                        <!-- Price Display placed directly under the title -->
                                                        <div class="egc-sub-card-price-display" style="margin-top: 8px; font-size: 15px; font-weight: 700; color: #111;">
                                                            <?php echo ( (float)$brand_price > 0 ) ? wp_kses_post( wc_price( $brand_price ) ) : esc_html__( 'Free', 'eyeglass-customizer' ); ?>
                                                        </div>
                                                    </div>

                                                    <!-- Middle Column: Recommendation / Description -->
                                                    <div class="egc-sub-card-col-middle">
                                                        <?php if ( $brand_recommendation ) : ?>
                                                            <div class="egc-sub-card-recommendation"><?php echo wp_kses_post( $brand_recommendation ); ?></div>
                                                        <?php endif; ?>
                                                    </div>

                                                    <!-- Right Column: Badge & Action Button -->
                                                    <div class="egc-sub-card-col-right">
                                                        <?php if ( $brand_benefits ) : ?>
                                                            <div class="egc-sub-card-badge-tag"><?php echo esc_html( strip_tags( $brand_benefits ) ); ?></div>
                                                        <?php endif; ?>

                                                        <button type="button" class="egc-btn egc-select-btn egc-sub-select-btn">
                                                            <?php echo $has_sub_two ? esc_html__( 'Options ▾', 'eyeglass-customizer' ) : esc_html__( 'Select', 'eyeglass-customizer' ); ?>
                                                        </button>
                                                    </div>

                                                </div>

                                                <!-- Level 2 Sub Cards Accordion Container (Material Sub Cards Two) -->
                                                <?php if ( $has_sub_two ) : ?>
                                                    <div class="egc-sub-two-container" style="display: none; margin-top: 10px; padding: 14px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px;">
                                                        <h5 style="margin: 0 0 10px 0; font-size: 13px; font-weight: 700; text-transform: uppercase; color: #475569;">
                                                            <?php esc_html_e( 'Select Option / Lens Sub-Type:', 'eyeglass-customizer' ); ?>
                                                        </h5>
                                                        
                                                        <div class="egc-sub-two-list" style="display: flex; flex-direction: column; gap: 10px;">
                                                            <?php foreach ( $sub_two_items as $sub2 ) : ?>
                                                                <div class="egc-sub-two-card egc-clickable-sub-two"
                                                                     data-parent-title="<?php echo esc_attr( $display_title ); ?>"
                                                                     data-title="<?php echo esc_attr( $display_title . ' - ' . $sub2['title'] ); ?>"
                                                                     data-price="<?php echo esc_attr( $sub2['price'] ?: $brand_price ); ?>"
                                                                     style="display: flex; justify-content: space-between; align-items: center; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 4px; padding: 10px 14px; cursor: pointer; transition: all 0.2s ease;">
                                                                    
                                                                    <div style="display: flex; align-items: center; gap: 12px;">
                                                                        <?php if ( ! empty( $sub2['img'] ) ) : ?>
                                                                            <img src="<?php echo esc_url( $sub2['img'] ); ?>" alt="<?php echo esc_attr( $sub2['title'] ); ?>" style="max-height: 38px; max-width: 55px; object-fit: contain;" />
                                                                        <?php endif; ?>
                                                                        <div>
                                                                            <div style="font-weight: 700; font-size: 13px; color: #0f172a;"><?php echo esc_html( $sub2['title'] ); ?></div>
                                                                            <?php if ( ! empty( $sub2['desc'] ) ) : ?>
                                                                                <div style="font-size: 12px; color: #64748b; margin-top: 2px;"><?php echo wp_kses_post( $sub2['desc'] ); ?></div>
                                                                            <?php endif; ?>
                                                                        </div>
                                                                    </div>

                                                                    <div style="display: flex; align-items: center; gap: 14px;">
                                                                        <div style="font-weight: 700; font-size: 14px; color: #0f172a;">
                                                                            <?php echo ( (float)$sub2['price'] > 0 ) ? wp_kses_post( wc_price( $sub2['price'] ) ) : ( ( (float)$brand_price > 0 ) ? wp_kses_post( wc_price( $brand_price ) ) : esc_html__( 'Free', 'eyeglass-customizer' ) ); ?>
                                                                        </div>
                                                                        <button type="button" class="egc-btn egc-select-btn" style="background: #000000; color: #ffffff; padding: 6px 16px; font-size: 12px; border-radius: 3px;">
                                                                            <?php esc_html_e( 'Select', 'eyeglass-customizer' ); ?>
                                                                        </button>
                                                                    </div>

                                                                </div>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>

                                            </div>
                                        <?php endwhile; ?>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                        </div>
                        <?php $mat_index++; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Price Breakdown Summary Box -->
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

        </div>

    </div>

</section>