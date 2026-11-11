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
?>
<!-- STEP 6: SUMMARY -->
<section class="egc-step-panel" data-step="6">

    <h2 class="egc-step-title"><?php esc_html_e( 'Check Lenses Summary', 'eyeglass-customizer' ); ?></h2>

    <!-- ROW 1: FRAME COLOR SWATCH, RX TYPE & PRESCRIPTION -->
    <div class="egc-summary-cards-row egc-row-3col">
        
        <!-- Box 0: Frame Color Swatch -->
        <div class="egc-summary-card-box" id="egc-sum-swatch-box">
            <h4 class="egc-sum-box-title"><?php esc_html_e( 'Frame Color', 'eyeglass-customizer' ); ?></h4>
            <div class="egc-sum-box-body" id="egc-sum-swatch-content">
                <p class="egc-sum-val">&mdash;</p>
            </div>
        </div>

        <!-- Box 1: Rx Type -->
        <div class="egc-summary-card-box">
            <h4 class="egc-sum-box-title"><?php esc_html_e( 'Rx Type', 'eyeglass-customizer' ); ?></h4>
            <div class="egc-sum-box-body" id="egc-sum-rxtype-content">
                <p class="egc-sum-val">&mdash;</p>
            </div>
        </div>

        <!-- Box 2: Prescription -->
        <div class="egc-summary-card-box egc-sum-box-large">
            <h4 class="egc-sum-box-title"><?php esc_html_e( 'Prescription', 'eyeglass-customizer' ); ?></h4>
            <div class="egc-sum-box-body" id="egc-sum-prescription-content">
                <p class="egc-sum-val">&mdash;</p>
            </div>
        </div>

    </div>

    <!-- ROW 2: MATERIAL, COATING & FEATURES -->
    <div class="egc-summary-cards-row egc-row-3col">
        
        <!-- Box 3: Material -->
        <div class="egc-summary-card-box">
            <h4 class="egc-sum-box-title"><?php esc_html_e( 'Material', 'eyeglass-customizer' ); ?></h4>
            <div class="egc-sum-box-body" id="egc-sum-material-content">
                <p class="egc-sum-val">&mdash;</p>
            </div>
        </div>

        <!-- Box 4: Coating -->
        <div class="egc-summary-card-box">
            <h4 class="egc-sum-box-title"><?php esc_html_e( 'Coating', 'eyeglass-customizer' ); ?></h4>
            <div class="egc-sum-box-body" id="egc-sum-coating-content">
                <p class="egc-sum-val">&mdash;</p>
            </div>
        </div>

        <!-- Box 5: Features (Includes Insignia as standard and Custom Engraving text) -->
        <div class="egc-summary-card-box">
            <h4 class="egc-sum-box-title"><?php esc_html_e( 'Features', 'eyeglass-customizer' ); ?></h4>
            <div class="egc-sum-box-body" id="egc-sum-features-content">
                <p class="egc-sum-val">&mdash;</p>
            </div>
        </div>

    </div>

    <!-- BOTTOM SECTION: 50% Comments (Left) vs 50% Grand Total & Full-Width Stacked Actions (Right) -->
    <div class="egc-summary-bottom-section" style="display:flex; gap:30px; align-items:flex-start; margin-top:24px; padding-top:20px; border-top:1px solid #eaeaea;">
        
        <!-- Left 50% Column: Moved Comments Box -->
        <div class="egc-summary-comments-col" style="flex: 0 0 50%; max-width: 50%; width: 50%; box-sizing: border-box;">
            <div class="egc-summary-comments">
                <label for="egc-summary-comments" style="display:block; font-size:14px; font-weight:700; margin-bottom:8px; color:#111;">
                    <strong><?php esc_html_e( 'Any additional comments:', 'eyeglass-customizer' ); ?></strong>
                </label>
                <textarea id="egc-summary-comments" name="egc_summary_comments" rows="5" placeholder="<?php esc_attr_e( 'Enter any notes or special instructions...', 'eyeglass-customizer' ); ?>" style="width:100%; border:1px solid #ccc; border-radius:4px; padding:12px; font-size:13px; resize:vertical; box-sizing:border-box; min-height:130px;"></textarea>
            </div>
        </div>

        <!-- Right 50% Column: Prominent Grand Total & Full-Width Stacked Buttons -->
        <div class="egc-summary-action-col" style="flex: 1 1 50%; max-width: 50%; width: 50%; box-sizing: border-box;">
            
            <!-- Prominent Grand Total Box -->
            <div class="egc-prominent-total-box" style="margin-bottom: 14px;">
                <span class="egc-total-label"><?php esc_html_e( 'Grand Total:', 'eyeglass-customizer' ); ?></span>
                <span class="egc-total-amount" id="egc-prominent-grand-total">$0.00</span>
            </div>

            <!-- Success Notification Banner -->
            <div id="egc-success-banner" class="egc-alert egc-alert-success" style="display:none; margin-bottom: 12px;">
                <?php esc_html_e( 'Frame and Lenses successfully added to the Cart!', 'eyeglass-customizer' ); ?>
            </div>

            <input type="hidden" id="egc-selections-json" name="egc_selections_json" value="" />
            <input type="hidden" id="egc-checkout-url" value="<?php echo esc_url( wc_get_checkout_url() ); ?>" />

            <!-- Add to Cart Full Width Button -->
            <button type="button" id="egc-submit-customization" class="egc-btn egc-btn-purple egc-btn-large egc-full-btn" style="width:100%; height:46px; font-size:15px; font-weight:700; border-radius:4px; margin-bottom:10px;">
                <?php esc_html_e( 'Add To Cart', 'eyeglass-customizer' ); ?>
            </button>

            <!-- Post Cart Actions: Full Width & Stacked Vertically (Up and Down) -->
            <div id="egc-post-cart-actions" class="egc-post-cart-actions" style="display:none; flex-direction:column; gap:10px; width:100%;">
                <button type="button" id="egc-continue-shopping-btn" class="egc-btn egc-btn-continue-shopping" style="width:100%; text-align:center; padding:12px 16px; border-radius:4px;">
                    <?php esc_html_e( 'Continue Shopping', 'eyeglass-customizer' ); ?>
                </button>
                <button type="button" id="egc-proceed-checkout-btn" class="egc-btn egc-btn-proceed-checkout" style="width:100%; text-align:center; padding:12px 16px; border-radius:4px;">
                    <?php esc_html_e( 'Proceed to Checkout', 'eyeglass-customizer' ); ?>
                </button>
            </div>

        </div>

    </div>

</section>