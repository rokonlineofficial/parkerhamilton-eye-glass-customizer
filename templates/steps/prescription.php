<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!-- STEP 2: PRESCRIPTION -->
<section class="egc-step-panel" data-step="2" style="max-width: 90%; margin-left: auto; margin-right: 0; text-align: left; font-family: sans-serif;">

    <h2 class="egc-step-title" style="text-align: center; font-size: 22px; font-weight: bold; margin-bottom: 20px; color: #222;">
        <?php esc_html_e( 'Enter your Prescription', 'eyeglass-customizer' ); ?>
    </h2>

    <!-- Hidden input to reliably track PD type -->
    <input type="hidden" id="egc-pd-type" name="egc_pd_type" value="single" />

    <!-- Magnification Toggle -->
    <div id="egc-magnification-toggle-area" class="egc-magnification-toggle-area" style="display:none; justify-content: flex-end; gap: 15px; margin-bottom: 15px;">
        <label class="egc-radio-option" style="font-size: 14px;">
            <input type="radio" name="egc_rx_mode" value="magnification" id="egc-mode-magnification" />
            <?php esc_html_e( 'Magnification (no prescription)', 'eyeglass-customizer' ); ?>
        </label>

        <div id="egc-magnification-power-wrap" class="egc-magnification-power-wrap" style="display:none;">
            <label for="egc-magnification-power" style="font-size: 13px;"><?php esc_html_e( 'Select power', 'eyeglass-customizer' ); ?></label>
            <select id="egc-magnification-power" name="egc_magnification_power" class="egc-select" style="font-size: 13px; padding: 4px;">
                <?php
                for ( $p = 1.00; $p <= 3.50; $p += 0.25 ) {
                    printf( '<option value="+%1$s">+%1$s</option>', esc_attr( number_format( $p, 2 ) ) );
                }
                ?>
            </select>
        </div>

        <label class="egc-radio-option" style="font-size: 14px;">
            <input type="radio" name="egc_rx_mode" value="prescription" id="egc-mode-prescription" />
            <?php esc_html_e( 'Use my prescription', 'eyeglass-customizer' ); ?>
        </label>
    </div>

    <!-- Outer Form Wrapper -->
    <div id="egc-full-prescription-form" class="egc-full-prescription-form" style="width: 100%;">

        <!-- 3 Tabs -->
        <div class="egc-rx-entry-tabs" style="display: flex; justify-content: space-between; gap: 10px; margin-bottom: 15px;">
            <button type="button" class="egc-btn egc-rx-tab-btn is-active egc-btn-purple" data-tab="fill-online" style="flex: 1; padding: 12px 10px; font-size: 14px; font-weight: bold; text-transform: uppercase; border-radius: 2px; color: #fff; background: #8a2be2; border: 1px solid #8a2be2; cursor: pointer;">
                <?php esc_html_e( 'FILL IT OUT ONLINE', 'eyeglass-customizer' ); ?>
            </button>
            <button type="button" class="egc-btn egc-rx-tab-btn" data-tab="upload" style="flex: 1; padding: 12px 10px; font-size: 14px; font-weight: bold; text-transform: uppercase; border-radius: 2px; color: #8a2be2; border: 1px solid #ccc; background: #fff; cursor: pointer;">
                <?php esc_html_e( 'UPLOAD YOUR RX', 'eyeglass-customizer' ); ?>
            </button>
            <button type="button" class="egc-btn egc-rx-tab-btn" data-tab="later" style="flex: 1; padding: 12px 10px; font-size: 14px; font-weight: bold; text-transform: uppercase; border-radius: 2px; color: #8a2be2; border: 1px solid #ccc; background: #fff; cursor: pointer;">
                <?php esc_html_e( 'SEND IT LATER', 'eyeglass-customizer' ); ?>
            </button>
        </div>

        <!-- TAB 1: Fill It Out Online -->
        <div id="egc-tab-content-fill-online" class="egc-tab-content-panel" style="display: block;">
            <table class="egc-rx-table" style="width: 100%; border-collapse: separate; border-spacing: 0; border: 1px solid #dcdcdc; margin-bottom: 0;">
                <thead>
                    <tr style="background-color: #e8f0fe; font-size: 13px; font-weight: bold; color: #333;">
                        <th style="width: 20%; padding: 10px; border-bottom: 1px solid #dcdcdc; border-right: 1px solid #dcdcdc;"></th>
                        <th style="width: 20%; padding: 10px; text-align: center; border-bottom: 1px solid #dcdcdc; border-right: 1px solid #dcdcdc;"><?php esc_html_e( 'Sphere', 'eyeglass-customizer' ); ?></th>
                        <th style="width: 20%; padding: 10px; text-align: center; border-bottom: 1px solid #dcdcdc; border-right: 1px solid #dcdcdc;"><?php esc_html_e( 'Cylinder', 'eyeglass-customizer' ); ?></th>
                        <th style="width: 20%; padding: 10px; text-align: center; border-bottom: 1px solid #dcdcdc; border-right: 1px solid #dcdcdc;"><?php esc_html_e( 'Axis', 'eyeglass-customizer' ); ?></th>
                        <th style="width: 20%; padding: 10px; text-align: center; border-bottom: 1px solid #dcdcdc;"><?php esc_html_e( 'Add', 'eyeglass-customizer' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th style="background-color: #e8f0fe; font-size: 13px; font-weight: normal; text-align: center; padding: 10px; border-bottom: 1px solid #dcdcdc; border-right: 1px solid #dcdcdc;">
                            <?php esc_html_e( 'Right (OD)', 'eyeglass-customizer' ); ?>
                        </th>
                        <td style="padding: 8px; border-bottom: 1px solid #dcdcdc; border-right: 1px solid #dcdcdc; text-align: center;"><?php echo egc_render_power_select( 'egc_od_sphere' ); ?></td>
                        <td style="padding: 8px; border-bottom: 1px solid #dcdcdc; border-right: 1px solid #dcdcdc; text-align: center;"><?php echo egc_render_power_select( 'egc_od_cylinder' ); ?></td>
                        <td style="padding: 8px; border-bottom: 1px solid #dcdcdc; border-right: 1px solid #dcdcdc; text-align: center;">
                            <select name="egc_od_axis" class="egc-select" style="width: 100%; height: 32px; font-size: 13px; border: 1px solid #ccc; border-radius: 3px;">
                                <option value="n-a"><?php esc_html_e( 'N/A', 'eyeglass-customizer' ); ?></option>
                                <?php for ( $a = 1; $a <= 180; $a++ ) : ?>
                                    <option value="<?php echo esc_attr( $a ); ?>"><?php echo esc_html( $a ); ?></option>
                                <?php endfor; ?>
                            </select>
                        </td>
                        <td style="padding: 8px; border-bottom: 1px solid #dcdcdc; text-align: center;"><?php echo egc_render_add_select( 'egc_od_add' ); ?></td>
                    </tr>

                    <tr>
                        <th style="background-color: #e8f0fe; font-size: 13px; font-weight: normal; text-align: center; padding: 10px; border-bottom: 1px solid #dcdcdc; border-right: 1px solid #dcdcdc;">
                            <?php esc_html_e( 'Left (OS)', 'eyeglass-customizer' ); ?>
                        </th>
                        <td style="padding: 8px; border-bottom: 1px solid #dcdcdc; border-right: 1px solid #dcdcdc; text-align: center;"><?php echo egc_render_power_select( 'egc_os_sphere' ); ?></td>
                        <td style="padding: 8px; border-bottom: 1px solid #dcdcdc; border-right: 1px solid #dcdcdc; text-align: center;"><?php echo egc_render_power_select( 'egc_os_cylinder' ); ?></td>
                        <td style="padding: 8px; border-bottom: 1px solid #dcdcdc; border-right: 1px solid #dcdcdc; text-align: center;">
                            <select name="egc_os_axis" class="egc-select" style="width: 100%; height: 32px; font-size: 13px; border: 1px solid #ccc; border-radius: 3px;">
                                <option value="n-a"><?php esc_html_e( 'N/A', 'eyeglass-customizer' ); ?></option>
                                <?php for ( $a = 1; $a <= 180; $a++ ) : ?>
                                    <option value="<?php echo esc_attr( $a ); ?>"><?php echo esc_html( $a ); ?></option>
                                <?php endfor; ?>
                            </select>
                        </td>
                        <td style="padding: 8px; border-bottom: 1px solid #dcdcdc; text-align: center;"><?php echo egc_render_add_select( 'egc_os_add' ); ?></td>
                    </tr>

                    <!-- PD Row -->
                    <tr class="egc-pd-table-row">
                        <th style="background-color: #e8f0fe; font-size: 13px; font-weight: normal; text-align: center; padding: 10px; border-bottom: 1px solid #dcdcdc; border-right: 1px solid #dcdcdc;">
                            <?php esc_html_e( 'PD', 'eyeglass-customizer' ); ?>
                        </th>
                        <td style="padding: 8px; border-bottom: 1px solid #dcdcdc; border-right: 1px solid #dcdcdc; text-align: center; vertical-align: top;">
                            <select id="egc-single-pd-select" name="egc_single_pd" class="egc-select" style="width: 100%; height: 32px; font-size: 13px; border: 1px solid #ccc;">
                                <option value=""><?php esc_html_e( 'Select PD', 'eyeglass-customizer' ); ?></option>
                                <?php for ( $pd = 40; $pd <= 80; $pd++ ) : ?>
                                    <option value="<?php echo esc_attr( $pd ); ?>"><?php echo esc_html( $pd ); ?></option>
                                <?php endfor; ?>
                            </select>
                        </td>
                        <td colspan="2" style="padding: 8px; border-bottom: 1px solid #dcdcdc; border-right: 1px solid #dcdcdc; text-align: center; vertical-align: top;">
                            <div style="margin-bottom: 8px; font-size: 12px;">
                                <label class="egc-checkbox-option" style="cursor: pointer; display: inline-flex; align-items: center; gap: 4px;">
                                    <input type="checkbox" id="egc-pd-dual-toggle" name="egc_has_dual_pd" value="1" />
                                    <?php esc_html_e( 'I have 2 numbers for my PD', 'eyeglass-customizer' ); ?>
                                </label>
                            </div>
                            <div style="display: flex; gap: 8px; justify-content: center;">
                                <select id="egc-left-pd-select" name="egc_left_pd" class="egc-select" disabled style="flex: 1; height: 32px; font-size: 12px; border: 1px solid #ccc; background: #f2f2f2;">
                                    <option value=""><?php esc_html_e( 'Left PD', 'eyeglass-customizer' ); ?></option>
                                    <?php for ( $lpd = 20; $lpd <= 40; $lpd += 0.5 ) : ?>
                                        <option value="<?php echo esc_attr( $lpd ); ?>"><?php echo esc_html( $lpd ); ?></option>
                                    <?php endfor; ?>
                                </select>
                                <select id="egc-right-pd-select" name="egc_right_pd" class="egc-select" disabled style="flex: 1; height: 32px; font-size: 12px; border: 1px solid #ccc; background: #f2f2f2;">
                                    <option value=""><?php esc_html_e( 'Right PD', 'eyeglass-customizer' ); ?></option>
                                    <?php for ( $rpd = 20; $rpd <= 40; $rpd += 0.5 ) : ?>
                                        <option value="<?php echo esc_attr( $rpd ); ?>"><?php echo esc_html( $rpd ); ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </td>
                        <td class="egc-pd-image-cell" style="padding: 6px; border-bottom: 1px solid #dcdcdc; text-align: center; vertical-align: middle;">
                            <img src="<?php echo esc_url( $pd_reference_image ); ?>" alt="<?php esc_attr_e( 'Pupillary Distance reference', 'eyeglass-customizer' ); ?>" class="egc-pd-reference-image" style="width: 100%; max-width: 130px; height: auto; display: block; margin: 0 auto;" />
                        </td>
                    </tr>

                    <tr>
                        <th style="background-color: #e8f0fe; border-right: 1px solid #dcdcdc;"></th>
                        <td colspan="4" style="padding: 15px; text-align: left; background-color: #ffffff;">
                            <p style="font-size: 13px; color: #333; margin-bottom: 4px;"><?php esc_html_e( 'Your prescription copy is still required to verify your RX prior to proceeding with your order.', 'eyeglass-customizer' ); ?></p>
                            <p class="egc-upload-hint" style="font-size: 12px; color: #666; margin-bottom: 12px;"><?php esc_html_e( 'Files: PDF, JPG, GIF, PNG, JPEG Max file size 20mb', 'eyeglass-customizer' ); ?></p>
                            
                            <button type="button" id="egc-upload-prescription-btn" class="egc-btn egc-btn-gold" style="background-color: #fca311; color: #000; font-weight: bold; padding: 8px 18px; font-size: 13px; border: none; border-radius: 3px; cursor: pointer;">
                                <?php esc_html_e( 'Upload Prescription', 'eyeglass-customizer' ); ?>
                            </button>
                            <input type="file" id="egc-prescription-file" name="egc_prescription_file" accept=".pdf,.jpg,.jpeg,.gif,.png" style="display:none;" />
                            <span id="egc-prescription-filename" class="egc-file-name" style="font-size: 12px; margin-left: 10px; color: #2b9348; font-weight: 500;"></span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- TAB 2: Upload RX -->
        <div id="egc-tab-content-upload" class="egc-tab-content-panel" style="display: none; border: 1px solid #dcdcdc; padding: 20px; background: #fff;">
            <p style="font-size: 13px; color: #333; margin-bottom: 15px;"><?php esc_html_e( 'To expedite the prescription verification process, upload an image of your current, valid prescription.', 'eyeglass-customizer' ); ?></p>
            <button type="button" class="egc-tab-upload-trigger egc-btn" style="background-color: #fca311; color: #000; font-weight: bold; padding: 10px 20px; font-size: 13px; border: none; border-radius: 3px; cursor: pointer; margin-bottom: 10px;">
                <?php esc_html_e( 'Upload Prescription File', 'eyeglass-customizer' ); ?>
            </button>
        </div>

        <!-- TAB 3: Send Later -->
        <div id="egc-tab-content-later" class="egc-tab-content-panel" style="display: none; border: 1px solid #dcdcdc; padding: 20px; background: #fff; font-size: 13px;">
            <p><?php esc_html_e( "Can't upload your prescription? Simply email or fax it to us.", 'eyeglass-customizer' ); ?></p>
        </div>
    </div>

    <!-- Bottom Right Controls: PRISM & DOB -->
    <div class="egc-rx-bottom-right-container" style="display: flex; flex-direction: column; align-items: flex-end; width: 100%; margin-top: 15px; font-size: 13px; color: #333;">
        
        <!-- PRISM OPTION -->
        <div class="egc-prism-row" style="margin-bottom: 8px; text-align: right; display: flex; align-items: center; justify-content: flex-end; gap: 8px; flex-wrap: wrap;">
            <label style="font-size: 13px; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;">
                <input type="radio" name="egc_prism" value="yes" style="margin: 0;" /> <?php esc_html_e( 'Yes', 'eyeglass-customizer' ); ?>
            </label>
            <label style="font-size: 13px; cursor: pointer; display: inline-flex; align-items: center; gap: 4px; margin-right: 5px;">
                <input type="radio" name="egc_prism" value="no" checked style="margin: 0;" /> <?php esc_html_e( 'No', 'eyeglass-customizer' ); ?>
            </label>
            <strong class="egc-prism-label" style="font-weight: bold; font-size: 13px; color: #000;"><?php esc_html_e( 'Add prism $25.00', 'eyeglass-customizer' ); ?></strong>
            <div class="egc-prism-hint" style="font-size: 11px; color: #666; width: 100%; text-align: right; margin-top: 1px;">
                <?php esc_html_e( '(only select if your RX has a prism)', 'eyeglass-customizer' ); ?>
            </div>
        </div>

        <!-- WEARER DOB: MANDATORY -->
        <div class="egc-dob-row" style="margin-bottom: 15px; display: flex; align-items: center; justify-content: flex-end; gap: 8px;">
            <label style="font-weight: bold; font-size: 13px; color: #000; margin: 0; white-space: nowrap;">
                <?php esc_html_e( 'Wearer DOB:', 'eyeglass-customizer' ); ?> <span style="color:red;">*</span>
            </label>
            <select name="egc_dob_month" id="egc-dob-month" class="egc-select" style="width: 95px; height: 30px; font-size: 12px; border: 1px solid #ccc; border-radius: 2px;">
                <option value=""><?php esc_html_e( '- Month -', 'eyeglass-customizer' ); ?></option>
                <?php for ( $m = 1; $m <= 12; $m++ ) : ?>
                    <option value="<?php echo esc_attr( $m ); ?>"><?php echo esc_html( date_i18n( 'M', mktime( 0, 0, 0, $m, 1 ) ) ); ?></option>
                <?php endfor; ?>
            </select>
            <select name="egc_dob_year" id="egc-dob-year" class="egc-select" style="width: 85px; height: 30px; font-size: 12px; border: 1px solid #ccc; border-radius: 2px;">
                <option value=""><?php esc_html_e( '- Year -', 'eyeglass-customizer' ); ?></option>
                <?php
                $current_year = (int) date( 'Y' );
                for ( $y = $current_year; $y >= $current_year - 100; $y-- ) : ?>
                    <option value="<?php echo esc_attr( $y ); ?>"><?php echo esc_html( $y ); ?></option>
                <?php endfor; ?>
            </select>
        </div>

        <!-- SAVE AND CONTINUE BUTTON -->
        <button type="button" class="egc-btn egc-next-btn egc-next-step" data-next="3" style="background-color: #000; color: #fff; padding: 10px 22px; font-weight: bold; border: none; cursor: pointer; border-radius: 2px; text-transform: uppercase; font-size: 13px; letter-spacing: 0.5px; margin-bottom: 12px;">
            <?php esc_html_e( 'Save and Continue', 'eyeglass-customizer' ); ?>
        </button>

        <!-- LIVE PRICE BREAKDOWN IN RIGHT CORNER -->
        <div class="egc-step-price-summary" style="text-align: right; min-width: 170px; font-size: 13px; color: #333;">
            <div style="display: flex; justify-content: space-between; gap: 20px; margin-bottom: 3px;">
                <span><?php esc_html_e( 'Frame Price:', 'eyeglass-customizer' ); ?></span>
                <strong class="egc-summary-value egc-frame-price-val" style="font-weight: bold;"><?php echo wc_price( $product->get_price() ); ?></strong>
            </div>
            <div style="display: flex; justify-content: space-between; gap: 20px; margin-bottom: 3px;">
                <span><?php esc_html_e( 'Lenses Price:', 'eyeglass-customizer' ); ?></span>
                <strong class="egc-summary-value egc-lens-price-val" style="font-weight: bold;"><?php echo wc_price( 0 ); ?></strong>
            </div>
            <div style="display: flex; justify-content: space-between; gap: 20px; font-size: 14px; font-weight: bold; border-top: 1px solid #eee; padding-top: 4px; margin-top: 4px; color: #000;">
                <span><?php esc_html_e( 'Grand Total:', 'eyeglass-customizer' ); ?></span>
                <span class="egc-summary-value egc-grand-total-val"><?php echo wc_price( $product->get_price() ); ?></span>
            </div>
        </div>

    </div>

</section>