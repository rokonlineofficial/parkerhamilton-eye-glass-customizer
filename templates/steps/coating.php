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

// 1. Primary check: ACF Relationship field for Lens Coating CPT
$coating_posts = get_field( 'lens_coating', $product_id );
$has_coatings  = ! empty( $coating_posts ) && is_array( $coating_posts );

// Retrieve main product image thumbnail for color cards
$featured_img = get_the_post_thumbnail_url( $product_id, 'medium' );
if ( ! $featured_img ) {
	$featured_img = wc_placeholder_img_src( 'medium' );
}
?>
<!-- STEP 4: COATING -->
<section class="egc-step-panel" data-step="4">

    <div class="egc-coating-wrapper <?php echo $has_coatings ? 'has-coatings' : 'no-coatings'; ?>" id="egc-coating-wrapper">
        
        <!-- CENTER AREA: Selected Coating Details & Lens Color Grid -->
        <div class="egc-coating-center-container" id="egc-coating-center-container">
            <h2 class="egc-step-title"><?php esc_html_e( 'Select Visibility Coating', 'eyeglass-customizer' ); ?></h2>
            
            <div class="egc-coating-center-content" id="egc-coating-center-content">
                <p class="egc-placeholder-text"><?php esc_html_e( 'Please select a lens visibility option from the right sidebar.', 'eyeglass-customizer' ); ?></p>
            </div>
        </div>

        <!-- RIGHT SIDEBAR: Main Coatings List -->
        <div class="egc-coating-sidebar-container">
            <h2 class="egc-step-title"><?php esc_html_e( 'Select Lens Visibility', 'eyeglass-customizer' ); ?></h2>

            <div class="egc-card-list" id="egc-coating-list">
                <?php if ( ! empty( $coating_posts ) && is_array( $coating_posts ) ) : ?>
                    <?php 
                    $coat_index = 0; 
                    foreach ( $coating_posts as $c_post ) :
                        $cid = is_object( $c_post ) ? $c_post->ID : $c_post;
                        $c_obj = get_post( $cid );
                        if ( ! $c_obj ) continue;

                        $coat_title       = $c_obj->post_title;
                        $coat_description = $c_obj->post_content;
                        $coat_badge       = get_field( 'global_card_extra_badge', $cid );

                        $left_img_url  = get_the_post_thumbnail_url( $cid, 'medium' );
                        $right_img_url = '';

                        $colors_list = array();

                        // Query Coating Sub Cards One Repeater
                        if ( have_rows( 'coating_sub_cards_one', $cid ) ) {
                            while ( have_rows( 'coating_sub_cards_one', $cid ) ) {
                                the_row();
                                $sub_badge = get_sub_field( 'coating_sub_cards_one_badge' );
                                $sub_img   = get_sub_field( 'coating_sub_cards_one_image' );
                                $sub_title = get_sub_field( 'coating_sub_cards_one_title' );
                                $sub_desc  = get_sub_field( 'coating_sub_cards_one_description' );
                                $sub_price = get_sub_field( 'coating_sub_cards_one_price' );

                                if ( ! empty( $sub_img ) && empty( $right_img_url ) ) {
                                    $right_img_url = is_array( $sub_img ) ? ( $sub_img['url'] ?? '' ) : $sub_img;
                                }

                                // Query nested color repeater: coating_sub_cards_two
                                if ( have_rows( 'coating_sub_cards_two' ) ) {
                                    while ( have_rows( 'coating_sub_cards_two' ) ) {
                                        the_row();
                                        $c_name   = get_sub_field( 'color_name' );
                                        $c_swatch = get_sub_field( 'swatch_image' );
                                        $c_price  = get_sub_field( 'color_price' );

                                        if ( ! empty( $c_name ) || ! empty( $c_swatch ) ) {
                                            $colors_list[] = array(
                                                'badge_image' => $c_swatch,
                                                'title'       => $c_name,
                                                'price'       => $c_price ? $c_price : $sub_price,
                                            );
                                        }
                                    }
                                }
                            }
                        }

                        // Product level color swatch fallback
                        if ( empty( $colors_list ) && have_rows( 'lens_variations', $product_id ) ) {
                            while ( have_rows( 'lens_variations', $product_id ) ) {
                                the_row();
                                $colors_list[] = array(
                                    'badge_image' => get_sub_field( 'swatch_image' ),
                                    'title'       => get_sub_field( 'color_name' ),
                                    'price'       => get_sub_field( 'color_price' ),
                                );
                            }
                        }

                        $has_sub_colors = ! empty( $colors_list );
                        ?>

                        <div class="egc-card egc-coating-card" data-coating-id="coat-<?php echo esc_attr( $coat_index ); ?>" data-title="<?php echo esc_attr( $coat_title ); ?>" data-has-colors="<?php echo $has_sub_colors ? 'true' : 'false'; ?>">

                            <?php if ( $coat_badge ) : ?>
                                <span class="egc-card-badge"><?php echo esc_html( $coat_badge ); ?></span>
                            <?php endif; ?>

                            <div class="egc-card-body">
                                <h3 class="egc-card-title"><?php echo esc_html( $coat_title ); ?></h3>
                                <?php if ( $coat_description ) : ?>
                                    <div class="egc-card-description"><?php echo wp_kses_post( $coat_description ); ?></div>
                                <?php endif; ?>
                            </div>

                            <button type="button" class="egc-btn egc-select-btn">
                                <?php esc_html_e( 'Select', 'eyeglass-customizer' ); ?>
                            </button>

                            <!-- HIDDEN DETAILS HTML -->
                            <div class="egc-hidden-coating-details" style="display:none;">
                                
                                <div class="egc-featured-coating-card" data-title="<?php echo esc_attr( $coat_title ); ?>" data-has-colors="<?php echo $has_sub_colors ? 'true' : 'false'; ?>">
                                    <?php if ( $coat_badge ) : ?>
                                        <span class="egc-card-badge egc-badge-top-right"><?php echo esc_html( $coat_badge ); ?></span>
                                    <?php endif; ?>

                                    <?php if ( $left_img_url ) : ?>
                                        <div class="egc-featured-left-image">
                                            <img src="<?php echo esc_url( $left_img_url ); ?>" alt="<?php echo esc_attr( $coat_title ); ?>" />
                                        </div>
                                    <?php endif; ?>

                                    <div class="egc-featured-coating-body">
                                        <h3 class="egc-featured-title"><?php echo esc_html( $coat_title ); ?></h3>
                                        <?php if ( $coat_description ) : ?>
                                            <div class="egc-featured-description"><?php echo wp_kses_post( $coat_description ); ?></div>
                                        <?php endif; ?>
                                    </div>

                                    <?php if ( $right_img_url ) : ?>
                                        <div class="egc-featured-right-image">
                                            <img src="<?php echo esc_url( $right_img_url ); ?>" alt="<?php echo esc_attr( $coat_title ); ?>" />
                                        </div>
                                    <?php endif; ?>

                                    <button type="button" class="egc-btn egc-select-btn egc-purple-btn">
                                        <?php esc_html_e( 'Select', 'eyeglass-customizer' ); ?>
                                    </button>
                                </div>

                                <!-- Color Grid Section -->
                                <?php if ( $has_sub_colors ) : ?>
                                    <div class="egc-lens-color-section" style="display:none;">
                                        <h3 class="egc-color-section-title"><?php esc_html_e( 'Select Lens Color', 'eyeglass-customizer' ); ?></h3>
                                        
                                        <div class="egc-color-cards-grid">
                                            <?php foreach ( $colors_list as $col ) : ?>
                                                <?php
                                                $badge_img   = $col['badge_image'];
                                                $color_title = $col['title'];
                                                $color_price = $col['price'];

                                                $img_url = '';
                                                if ( is_array( $badge_img ) ) {
                                                    $img_url = $badge_img['url'] ?? '';
                                                } elseif ( is_numeric( $badge_img ) ) {
                                                    $img_url = wp_get_attachment_image_url( $badge_img, 'full' );
                                                } else {
                                                    $img_url = (string) $badge_img;
                                                }
                                                ?>
                                                
                                                <div class="egc-color-card-item egc-clickable-color-card" data-title="<?php echo esc_attr( $color_title ? $color_title : $coat_title ); ?>" data-price="<?php echo esc_attr( $color_price ? $color_price : 0 ); ?>">
                                                    
                                                    <?php if ( ! empty( $img_url ) ) : ?>
                                                        <div class="egc-color-card-img">
                                                            <img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $color_title ); ?>" />
                                                        </div>
                                                    <?php endif; ?>

                                                    <?php if ( ! empty( $featured_img ) ) : ?>
                                                        <div class="egc-color-card-product-thumb">
                                                            <img src="<?php echo esc_url( $featured_img ); ?>" alt="<?php echo esc_attr( $product->get_name() ); ?>" />
                                                        </div>
                                                    <?php endif; ?>

                                                    <h4 class="egc-color-card-title"><?php echo esc_html( $color_title ); ?></h4>

                                                    <button type="button" class="egc-btn egc-select-btn">
                                                        <?php esc_html_e( 'Select', 'eyeglass-customizer' ); ?>
                                                    </button>

                                                    <?php if ( $color_price !== '' && $color_price !== false && (float)$color_price > 0 ) : ?>
                                                        <div class="egc-color-card-price">
                                                            <?php echo wp_kses_post( wc_price( $color_price ) ); ?>
                                                        </div>
                                                    <?php endif; ?>

                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                            </div>

                        </div>
                        <?php $coat_index++; ?>
                    <?php endforeach; ?>

                <?php elseif ( have_rows( 'lens_coatings', $product_id ) ) : ?>
                    <!-- FALLBACK FOR DIRECT REPEATER -->
                    <?php $coat_index = 0; ?>
                    <?php while ( have_rows( 'lens_coatings', $product_id ) ) : the_row(); ?>
                        <?php
                        $coat_title       = get_sub_field( 'coating_title' );
                        $coat_description = get_sub_field( 'coating_description' );
                        $coat_badge       = get_sub_field( 'coating_badge' );
                        ?>
                        <div class="egc-card egc-coating-card" data-coating-id="coat-<?php echo esc_attr( $coat_index ); ?>" data-title="<?php echo esc_attr( $coat_title ); ?>" data-has-colors="false">
                            <?php if ( $coat_badge ) : ?>
                                <span class="egc-card-badge"><?php echo esc_html( $coat_badge ); ?></span>
                            <?php endif; ?>
                            <div class="egc-card-body">
                                <h3 class="egc-card-title"><?php echo esc_html( $coat_title ); ?></h3>
                                <?php if ( $coat_description ) : ?>
                                    <div class="egc-card-description"><?php echo wp_kses_post( $coat_description ); ?></div>
                                <?php endif; ?>
                            </div>
                            <button type="button" class="egc-btn egc-select-btn">
                                <?php esc_html_e( 'Select', 'eyeglass-customizer' ); ?>
                            </button>
                        </div>
                        <?php $coat_index++; ?>
                    <?php endwhile; ?>
                <?php else : ?>
                    <p><?php esc_html_e( 'No coating options configured for this product.', 'eyeglass-customizer' ); ?></p>
                <?php endif; ?>
            </div>

            <!-- PRICE BREAKDOWN SUMMARY BOX -->
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