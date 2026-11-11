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
$img_base   = plugin_dir_url( dirname( dirname( __FILE__ ) ) ) . 'assets/images/';

// 1. Fetch from ACF Relationship field 'lens_feature'
$feature_posts = get_field( 'lens_feature', $product_id );
if ( empty( $feature_posts ) || ! is_array( $feature_posts ) ) {
    $feature_posts = get_field( 'lens_features', $product_id );
}

// 2. Fallback: If no features assigned, fetch all published lens feature posts
if ( empty( $feature_posts ) || ! is_array( $feature_posts ) ) {
    $feature_posts = get_posts( array(
        'post_type'      => array( 'lens_feature', 'lens_features' ),
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'orderby'        => 'menu_order title',
        'order'          => 'ASC',
    ) );
}

$has_features = ! empty( $feature_posts ) && is_array( $feature_posts );
?>
<!-- STEP 5: FEATURES -->
<section class="egc-step-panel" data-step="5">

    <h2 class="egc-step-title"><?php esc_html_e( 'Select Additional Lens Options', 'eyeglass-customizer' ); ?></h2>

    <!-- Comparison Promo Banner -->
    <div class="egc-features-promo-banner">
        <h3 class="egc-promo-title"><?php esc_html_e( 'AVOID GLARE BY ADDING ANTI-GLARE COATING.', 'eyeglass-customizer' ); ?></h3>
        <div class="egc-promo-image-wrap">
            <img src="<?php echo esc_url( $img_base . 'Eyespecs-Anti-Glare-Anti-Reflective.png' ); ?>" alt="<?php esc_attr_e( 'Anti-Glare Comparison', 'eyeglass-customizer' ); ?>" />
        </div>
    </div>

    <!-- Main Features Table Container -->
    <div class="egc-features-table-container">

        <!-- 1. Header Row (9 Advantage Columns) -->
        <div class="row even option-head row-option">
            <div class="col-xl-2 col-lg-2 col-md-2 col-sm-4 col-7 order-xl-0 order-lg-0 order-md-0 order-sm-0 order-0 border-sm-0 border-xs-0 egc-head-title-col">
                <?php esc_html_e( 'Features / Advantages', 'eyeglass-customizer' ); ?>
            </div>

            <!-- Col 1: Anti-Glare -->
            <div class="sm-hidden xs-hidden col order-xl-1 order-lg-1 order-md-1 order-sm-1 order-1 td-adv-title border-sm-0 border-xs-0">
                <div class="option-advantage-title advantage-with-description">
                    <div class="question-mark">i</div>
                    <img src="<?php echo esc_url( $img_base . 'Anti_Glare.png' ); ?>" alt="Anti-Reflective/Glare">
                </div>
                <div class="option-advantage-description" style="display: none;">
                    <p><strong>Anti-reflective and Anti-glare benefits</strong></p>
                    <ul>
                        <li>Reducing reflections and glare.</li>
                        <li>Allows more light to pass through the lens, improving visual clarity.</li>
                        <li>Prolong the life of your lenses.</li>
                    </ul>
                </div>
            </div>

            <!-- Col 2: Scratch Resistant -->
            <div class="sm-hidden xs-hidden col order-xl-2 order-lg-2 order-md-2 order-sm-2 order-2 td-adv-title border-sm-0 border-xs-0">
                <div class="option-advantage-title advantage-with-description">
                    <div class="question-mark">i</div>
                    <img src="<?php echo esc_url( $img_base . 'Resistes_scratches.png' ); ?>" alt="Scratch Resistant">
                </div>
                <div class="option-advantage-description" style="display: none;">
                    <p>Scratch Coat is applied to the exterior of the lenses to help extend lens life.</p>
                </div>
            </div>

            <!-- Col 3: Repels Dust -->
            <div class="sm-hidden xs-hidden col order-xl-3 order-lg-3 order-md-3 order-sm-3 order-3 td-adv-title border-sm-0 border-xs-0">
                <div class="option-advantage-title advantage-with-description">
                    <div class="question-mark">i</div>
                    <img src="<?php echo esc_url( $img_base . 'Repels_Dust.png' ); ?>" alt="Repels Dust">
                </div>
                <div class="option-advantage-description" style="display: none;">
                    <p>Dust repellence stops particles from sticking to your lenses.</p>
                </div>
            </div>

            <!-- Col 4: Repels Water -->
            <div class="sm-hidden xs-hidden col order-xl-4 order-lg-4 order-md-4 order-sm-4 order-4 td-adv-title border-sm-0 border-xs-0">
                <div class="option-advantage-title advantage-with-description">
                    <div class="question-mark">i</div>
                    <img src="<?php echo esc_url( $img_base . 'Repels_Water.png' ); ?>" alt="Repels Water">
                </div>
                <div class="option-advantage-description" style="display: none;">
                    <p>A superhydrophobic coating is applied to repel water.</p>
                </div>
            </div>

            <!-- Col 5: Smudge Resistant -->
            <div class="sm-hidden xs-hidden col order-xl-5 order-lg-5 order-md-5 order-sm-5 order-5 td-adv-title border-sm-0 border-xs-0">
                <div class="option-advantage-title advantage-with-description">
                    <div class="question-mark">i</div>
                    <img src="<?php echo esc_url( $img_base . 'Resists_Smudges.png' ); ?>" alt="Smudge Resistant">
                </div>
                <div class="option-advantage-description" style="display: none;">
                    <p>Oleophobic coating protects lenses against grease and smudges.</p>
                </div>
            </div>

            <!-- Col 6: UV Protection -->
            <div class="sm-hidden xs-hidden col order-xl-6 order-lg-6 order-md-6 order-sm-6 order-6 td-adv-title border-sm-0 border-xs-0">
                <div class="option-advantage-title advantage-with-description">
                    <div class="question-mark">i</div>
                    <img src="<?php echo esc_url( $img_base . 'UV_Protection.png' ); ?>" alt="UV Protection">
                </div>
                <div class="option-advantage-description" style="display: none;">
                    <p>UV coating shields your eyes from damaging UV rays.</p>
                </div>
            </div>

            <!-- Col 7: Add Logo -->
            <div class="sm-hidden xs-hidden col order-xl-7 order-lg-7 order-md-7 order-sm-7 order-7 td-adv-title border-sm-0 border-xs-0">
                <div class="option-advantage-title advantage-with-description">
                    <div class="question-mark">i</div>
                    <img src="<?php echo esc_url( $img_base . 'ADD-LOGO.jpg' ); ?>" alt="Add Logo">
                </div>
                <div class="option-advantage-description" style="display: none;">
                    <p>Add authentic brand insignia / engraving to lens.</p>
                </div>
            </div>

            <!-- Col 8: Lens-360 Warranty -->
            <div class="sm-hidden xs-hidden col order-xl-8 order-lg-8 order-md-8 order-sm-8 order-8 td-adv-title border-sm-0 border-xs-0">
                <div class="option-advantage-title advantage-with-description">
                    <div class="question-mark">i</div>
                    <img src="<?php echo esc_url( $img_base . 'Lens-360-Warranty.png' ); ?>" alt="Lens-360° Warranty">
                </div>
                <div class="option-advantage-description" style="display: none;">
                    <p>Full lens warranty package covering up to one year.</p>
                </div>
            </div>

            <!-- Col 9: 12-Months Warranty -->
            <div class="sm-hidden xs-hidden col order-xl-9 order-lg-9 order-md-9 order-sm-9 order-9 td-adv-title border-sm-0 border-xs-0">
                <div class="option-advantage-title advantage-with-description">
                    <div class="question-mark">i</div>
                    <img src="<?php echo esc_url( $img_base . '12Months_Warranty.png' ); ?>" alt="12-Months Warranty">
                </div>
                <div class="option-advantage-description" style="display: none;">
                    <p>Covers a one-time unconditional lens replacement.</p>
                </div>
            </div>

            <div class="sm-hidden xs-hidden col-xl-1 col-lg-1 col-md-1 col-sm-6 col-3 order-xl-10 order-lg-10 order-md-10 order-sm-1 order-10 td-price border-sm-0 border-xs-0"></div>
            <div class="sm-hidden xs-hidden col-xl-1 col-lg-1 col-sm-2 col-4 order-xl-11 order-lg-11 order-md-11 order-sm-2 order-2 border-sm-0 border-xs-0 select-lens-option"></div>
        </div>

        <!-- 2. FIXED ROW 1: BRAND ENGRAVING / INSIGNIA (UNTOUCHED, EXACTLY AS CLIENT APPROVED) -->
        <div class="row row-option egc-feature-item-row" data-price="0" data-title="Add Brand Engraving to Lens">
            <div class="col-xl-2 col-lg-2 col-md-2 col-sm-4 col-7 egc-feature-title-cell">
                <strong><?php esc_html_e( 'Add Brand Engraving to Lens', 'eyeglass-customizer' ); ?></strong>
            </div>
            <div class="col egc-check-cell"></div>
            <div class="col egc-check-cell"></div>
            <div class="col egc-check-cell"></div>
            <div class="col egc-check-cell"></div>
            <div class="col egc-check-cell"></div>
            <div class="col egc-check-cell"></div>
            <div class="col egc-check-cell"><span class="check-mark">✓</span></div>
            <div class="col egc-check-cell"></div>
            <div class="col egc-check-cell"></div>

            <div class="col-xl-1 col-lg-1 col-md-1 col-sm-6 col-3 egc-feature-price-cell">
                <strong style="color: #2e8b3d;"><?php esc_html_e( 'Standard', 'eyeglass-customizer' ); ?></strong>
            </div>

            <div class="col-xl-1 col-lg-1 col-sm-2 col-4 egc-feature-action-cell" style="display:flex; flex-direction:column; gap:6px; align-items:flex-end;">
                <select class="egc-feature-dropdown-select" name="egc_engraving_option" style="width: 100%;">
                    <option value="None" data-price="0"><?php esc_html_e( 'Add ▾', 'eyeglass-customizer' ); ?></option>
                    <option value="White Color" data-price="0">White Color</option>
                    <option value="Black Logo" data-price="0">Black Logo</option>
                    <option value="Red Color" data-price="0">Red Color</option>
                    <option value="Gold Color" data-price="0">Gold Color</option>
                    <option value="Silver Color" data-price="0">Silver Color</option>
                    <option value="Green Color" data-price="0">Green Color</option>
                    <option value="Blue Color" data-price="0">Blue Color</option>
                    <option value="Orange Color" data-price="0">Orange Color</option>
                </select>

                <input type="text"
                       id="egc-custom-engraving-text"
                       class="egc-custom-engraving-text"
                       placeholder="<?php esc_attr_e( 'Enter custom engraving text...', 'eyeglass-customizer' ); ?>"
                       style="display:none; width: 100%; font-size: 11px; padding: 5px 6px; border: 1px solid #ccc; border-radius: 3px; box-sizing: border-box; margin-top: 4px;" />
            </div>
        </div>

        <!-- 3. DYNAMIC ROWS FOR ALL OTHER ACF FEATURES (specs360, Crizal, DuraVision, Silver Flash, Specs AR, etc.) -->
        <?php if ( $has_features ) : ?>
            <?php 
            foreach ( $feature_posts as $f_item ) :
                $fid   = is_object( $f_item ) ? $f_item->ID : $f_item;
                $f_obj = get_post( $fid );
                if ( ! $f_obj ) continue;

                $raw_title = $f_obj->post_title;

                // Skip engraving if it appears in the relationship list since it's already rendered above
                if ( stripos( $raw_title, 'engraving' ) !== false || stripos( $raw_title, 'logo' ) !== false ) {
                    continue;
                }

                $f_video = get_field( 'feature_video_url', $fid ) ?: get_field( 'global_card_video_url', $fid );
                $f_price = get_field( 'global_card_extra_price', $fid );
                if ( $f_price === null || $f_price === '' ) {
                    $f_price = get_field( 'feature_price', $fid );
                }

                // If price is written in title like "specs360AR+Elite $19" or "Silver Flash Mirror $89.00", auto-extract
                if ( ( $f_price === null || $f_price === '' || (float)$f_price === 0.0 ) && preg_match( '/\$([\d\.]+)/', $raw_title, $price_match ) ) {
                    $f_price = (float) $price_match[1];
                }
                $f_price = is_numeric( $f_price ) ? (float) $f_price : 0;

                // Clean display title
                $display_title = preg_replace( '/\s*\$[\d\.]+\s*$/', '', $raw_title );

                // Feature Title Image
                $f_img   = get_the_post_thumbnail_url( $fid, 'medium' ) ?: get_field( 'feature_title_image', $fid );
                $img_url = is_array( $f_img ) ? ( $f_img['url'] ?? '' ) : ( is_numeric( $f_img ) ? wp_get_attachment_image_url( $f_img, 'full' ) : $f_img );

                $is_warranty = ( stripos( $raw_title, '360' ) !== false || stripos( $raw_title, 'warranty' ) !== false );
                ?>

                <div class="row row-option egc-feature-item-row" data-price="<?php echo esc_attr( $f_price ); ?>" data-title="<?php echo esc_attr( $display_title ); ?>">
                    
                    <!-- Left Title / Logo Column -->
                    <div class="col-xl-2 col-lg-2 col-md-2 col-sm-4 col-7 egc-feature-title-cell">
                        <?php if ( $img_url ) : ?>
                            <img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $display_title ); ?>" class="egc-feature-title-img" />
                        <?php else : ?>
                            <strong><?php echo esc_html( $display_title ); ?></strong>
                        <?php endif; ?>

                        <?php if ( ! empty( $f_video ) ) : ?>
                            <button type="button" class="egc-video-trigger-btn" onclick="openLensVideoFrame('<?php echo esc_url( $f_video ); ?>')" title="<?php esc_attr_e( 'Watch Video', 'eyeglass-customizer' ); ?>">
                                <img src="<?php echo esc_url( $img_base . 'watch-video-button-2.png' ); ?>" alt="<?php esc_attr_e( 'Watch Video', 'eyeglass-customizer' ); ?>" class="egc-watch-video-icon" />
                            </button>
                        <?php endif; ?>
                    </div>

                    <!-- 9 Checkmark Columns -->
                    <div class="col egc-check-cell"><span class="check-mark">✓<sup>+</sup></span></div>
                    <div class="col egc-check-cell"><span class="check-mark">✓<sup>+</sup></span></div>
                    <div class="col egc-check-cell"><span class="check-mark">✓<sup>+</sup></span></div>
                    <div class="col egc-check-cell"><span class="check-mark">✓<sup>+</sup></span></div>
                    <div class="col egc-check-cell"><span class="check-mark">✓<sup>+</sup></span></div>
                    <div class="col egc-check-cell"><span class="check-mark">✓<sup>+</sup></span></div>
                    <div class="col egc-check-cell"></div>
                    <div class="col egc-check-cell"><?php if ( $is_warranty ) echo '<span class="check-mark">✓</span>'; ?></div>
                    <div class="col egc-check-cell"><?php if ( $is_warranty ) echo '<span class="check-mark">✓</span>'; ?></div>

                    <!-- Price Column -->
                    <div class="col-xl-1 col-lg-1 col-md-1 col-sm-6 col-3 egc-feature-price-cell">
                        <?php if ( $f_price > 0 ) : ?>
                            + <?php echo wp_kses_post( wc_price( $f_price ) ); ?>
                        <?php else : ?>
                            <?php esc_html_e( 'Free', 'eyeglass-customizer' ); ?>
                        <?php endif; ?>
                    </div>

                    <!-- Action Column -->
                    <div class="col-xl-1 col-lg-1 col-sm-2 col-4 egc-feature-action-cell">
                        <button type="button" class="egc-btn egc-toggle-feature-btn" data-active="false">
                            <?php esc_html_e( 'Add', 'eyeglass-customizer' ); ?>
                        </button>
                    </div>

                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <div class="egc-feature-note-footnote">
            <small>[+] Offers best in class protection</small>
        </div>

    </div>

    <!-- Footer Nav & Bottom Right Price Breakdown -->
    <div class="egc-step5-footer-wrap">
        <button type="button" class="egc-btn egc-btn-red egc-continue-btn" id="egc-continue-to-summary">
            <?php esc_html_e( 'Continue', 'eyeglass-customizer' ); ?>
        </button>

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

        <div class="egc-warranty-box">
            <h4 class="egc-warranty-title"><?php esc_html_e( 'Warranty', 'eyeglass-customizer' ); ?></h4>
            <p class="egc-warranty-desc">
                <?php esc_html_e( 'We mean it! Any order placed with our unconditional lens warranty will be fully cover with a one-time lens replacement.', 'eyeglass-customizer' ); ?>
            </p>
            <a href="#" class="egc-warranty-link" onclick="event.preventDefault();"><?php esc_html_e( 'Warranty Details', 'eyeglass-customizer' ); ?></a>
        </div>
    </div>

    <!-- Video Modal Popup -->
    <div id="egc-video-modal-overlay" class="egc-video-modal-overlay" style="display:none;">
        <div class="egc-video-modal-content">
            <button type="button" class="egc-video-modal-close" onclick="closeLensVideoFrame()">&times;</button>
            <iframe id="egc-video-iframe" src="" frameborder="0" allowfullscreen></iframe>
        </div>
    </div>

</section>