/**
 * Step 6: Summary Builder, Insignia Standard Display, DOB Always Visible Across All Modes & AJAX Add To Cart Submission
 */

window.egcCollectPrescriptionData = function () {
    var $ = jQuery;
    return {
        mode: window.egcState.selections.prescriptionMode || '',
        magnificationPower: $( '#egc-magnification-power' ).val() || '',
        od: {
            sph:  $( 'select[name="egc_od_sphere"]' ).val() || 'none',
            cyl:  $( 'select[name="egc_od_cylinder"]' ).val() || 'none',
            axis: $( 'select[name="egc_od_axis"]' ).val() || 'n-a',
            add:  $( 'select[name="egc_od_add"]' ).val() || 'none'
        },
        os: {
            sph:  $( 'select[name="egc_os_sphere"]' ).val() || 'none',
            cyl:  $( 'select[name="egc_os_cylinder"]' ).val() || 'none',
            axis: $( 'select[name="egc_os_axis"]' ).val() || 'n-a',
            add:  $( 'select[name="egc_os_add"]' ).val() || 'none'
        },
        pdType:   $( '#egc-pd-type' ).val() || 'single',
        pdNear:   $( '#egc-single-pd-select' ).val() || '',
        pdLeft:   $( '#egc-left-pd-select' ).val() || '',
        pdRight:  $( '#egc-right-pd-select' ).val() || '',
        dob: {
            month: $( 'select[name="egc_dob_month"]' ).val() || '',
            year:  $( 'select[name="egc_dob_year"]' ).val() || ''
        },
        fileName: $( '#egc-prescription-filename' ).text() || ''
    };
};

window.egcRenderPrescriptionTable = function ( rx ) {
    var html = '';

    // 1. Mode Specific Content
    if ( 'magnification' === rx.mode ) {
        html += '<p style="margin: 0 0 6px 0;"><strong>Power:</strong> ' + ( rx.magnificationPower || '&mdash;' ) + '</p>';
    } else if ( 'upload' === rx.mode ) {
        html += '<p style="margin: 0 0 6px 0;"><strong>Method:</strong> Uploaded File</p>';
        if ( rx.fileName ) {
            var fileUrl = window.egcPrescriptionFileUrl || '#';
            html += '<p class="egc-prescription-file-wrap" style="margin: 0 0 6px 0;"><strong>File:</strong> <a href="' + fileUrl + '" target="_blank" class="egc-gold-link">' + rx.fileName + ' ↗</a></p>';
        }
    } else if ( 'send_later' === rx.mode ) {
        html += '<p style="margin: 0 0 6px 0;"><strong>Method:</strong> Will Send Later</p>';
    } else {
        // Standard Fill Online table
        html += '<table class="egc-prescription-summary-table">';
        html += '<thead><tr><th></th><th>Sph</th><th>Cyl</th><th>Axis</th><th>Add</th></tr></thead>';
        html += '<tbody>';
        html += '<tr><th>OD (R)</th><td>' + rx.od.sph + '</td><td>' + rx.od.cyl + '</td><td>' + rx.od.axis + '</td><td>' + rx.od.add + '</td></tr>';
        html += '<tr><th>OS (L)</th><td>' + rx.os.sph + '</td><td>' + rx.os.cyl + '</td><td>' + rx.os.axis + '</td><td>' + rx.os.add + '</td></tr>';

        if ( 'dual' === rx.pdType ) {
            html += '<tr><th>Left PD</th><td colspan="4">' + ( rx.pdLeft || 'N/A' ) + '</td></tr>';
            html += '<tr><th>Right PD</th><td colspan="4">' + ( rx.pdRight || 'N/A' ) + '</td></tr>';
        } else {
            html += '<tr><th>Near PD</th><td colspan="4">' + ( rx.pdNear || 'N/A' ) + '</td></tr>';
        }

        html += '</tbody></table>';

        if ( rx.fileName ) {
            var fileUrl = window.egcPrescriptionFileUrl || '#';
            html += '<p class="egc-prescription-file-wrap" style="margin-top:6px;"><strong>Uploaded File:</strong> <a href="' + fileUrl + '" target="_blank" class="egc-gold-link">' + rx.fileName + ' ↗</a></p>';
        }
    }

    // 2. ALWAYS Display Wearer DOB regardless of mode (Fill online, Upload, or Send later)
    if ( rx.dob && rx.dob.month && rx.dob.year ) {
        html += '<div style="margin-top:8px; padding-top:6px; border-top:1px dashed #e2e8f0; font-size:13px; color:#222;">' +
                    '<strong>Wearer DOB:</strong> ' + rx.dob.month + ' / ' + rx.dob.year +
                '</div>';
    }

    // 3. ALWAYS Display Prism if selected
    if ( window.egcState.prices && window.egcState.prices.prism > 0 ) {
        html += '<div style="margin-top:4px; font-size:13px; color:#b32d00;">' +
                    '<strong>Prism:</strong> Yes (+' + window.egcFormatPrice( window.egcState.prices.prism ) + ')' +
                '</div>';
    }

    return html;
};

window.egcBuildSummary = function () {
    var $ = jQuery;
    window.egcState.selections.prices = window.egcState.prices;

    // 0. Frame Color Swatch Box
    var swatch = window.egcState.selections.swatch || {};
    var swatchHtml = '';
    if ( swatch.img ) {
        swatchHtml += '<div class="egc-sum-box-img"><img src="' + swatch.img + '" alt="' + ( swatch.title || '' ) + '" /></div>';
    }
    swatchHtml += '<p class="egc-sum-val">' + ( swatch.title || 'Standard' ) + '</p>';
    if ( swatch.price > 0 ) {
        swatchHtml += '<p style="color:#C9A76D; font-weight:bold; margin-top:4px;">+' + window.egcFormatPrice( swatch.price ) + '</p>';
    }
    $( '#egc-sum-swatch-content' ).html( swatchHtml );

    // 1. Rx Type Box
    var rxTitle = window.egcState.selections.rxType || 'None';
    var rxImg   = window.egcState.selections.rxTypeImg || '';
    var rxHtml  = '';
    if ( rxImg ) {
        rxHtml += '<div class="egc-sum-box-img"><img src="' + rxImg + '" alt="' + rxTitle + '" /></div>';
    }
    rxHtml += '<p class="egc-sum-val">' + rxTitle + '</p>';
    $( '#egc-sum-rxtype-content' ).html( rxHtml );

    // 2. Prescription Box (Now always shows DOB & Prism for all modes)
    var rxData = window.egcCollectPrescriptionData();
    window.egcState.selections.prescription = rxData;
    $( '#egc-sum-prescription-content' ).html( window.egcRenderPrescriptionTable( rxData ) );

    // 3. Material Box
    var matTitle = window.egcState.selections.material || 'None';
    var matImg   = window.egcState.selections.materialImg || '';
    var matHtml  = '';
    if ( matImg ) {
        matHtml += '<div class="egc-sum-box-img"><img src="' + matImg + '" alt="' + matTitle + '" /></div>';
    }
    matHtml += '<p class="egc-sum-val">' + matTitle + '</p>';
    $( '#egc-sum-material-content' ).html( matHtml );

    // 4. Coating Box
    var coatTitle = window.egcState.selections.coating || 'None';
    var coatImg   = window.egcState.selections.coatingImg || '';
    var coatHtml  = '';
    if ( coatImg ) {
        coatHtml += '<div class="egc-sum-box-img"><img src="' + coatImg + '" alt="' + coatTitle + '" /></div>';
    }
    coatHtml += '<p class="egc-sum-val">' + coatTitle + '</p>';
    $( '#egc-sum-coating-content' ).html( coatHtml );

    // 5. Features Box (Displays Insignia as Standard and shows custom text)
    var featHtml = '';
    if ( window.egcState.selections.features.length ) {
        featHtml = '<div class="egc-sum-features-grid">';
        window.egcState.selections.features.forEach( function ( f ) {
            featHtml += '<div class="egc-sum-feature-item">';
            if ( f.img ) {
                featHtml += '<img src="' + f.img + '" alt="' + f.title + '" class="egc-sum-feature-thumb" />';
            }
            var priceBadge = ( f.isStandard || ! f.price ) 
                ? '<span style="color:#2e8b3d; font-weight:700;">Standard</span>' 
                : '+' + window.egcFormatPrice( f.price );

            featHtml += '<div class="egc-sum-feature-meta">' +
                            '<span class="egc-sum-feature-title">' + f.title + '</span>' +
                            '<span class="egc-sum-feature-price">' + priceBadge + '</span>' +
                        '</div>';
            featHtml += '</div>';
        } );
        featHtml += '</div>';
    } else {
        featHtml = '<p class="egc-sum-val">None</p>';
    }
    $( '#egc-sum-features-content' ).html( featHtml );

    window.egcRecalculatePrice();
    $( '#egc-selections-json' ).val( JSON.stringify( window.egcState.selections ) );
};

jQuery( document ).ready( function ( $ ) {
    'use strict';

    // AJAX Add To Cart Submission
    $( '#egc-submit-customization' ).on( 'click', function () {
        var $btn = $( this );

        // Capture comments from the left 50% textarea
        window.egcState.selections.comments = $( '#egc-summary-comments' ).val();
        window.egcState.selections.prices   = window.egcState.prices;

        var productId    = $( '#egc-wizard' ).data( 'product-id' );
        var variationId  = $( 'input[name="variation_id"]' ).val() || $( 'input.variation_id' ).val() || 0;
        var ajaxUrl      = $( '#egc-wizard' ).data( 'ajax-url' );
        var security     = $( '#egc-wizard' ).data( 'security' );

        var formData = new FormData();
        formData.append( 'action', 'egc_add_customized_product' );
        formData.append( 'security', security );
        formData.append( 'product_id', productId );
        formData.append( 'variation_id', variationId );
        formData.append( 'selections', JSON.stringify( window.egcState.selections ) );

        var fileInput = $( '#egc-prescription-file' )[0];
        if ( fileInput && fileInput.files && fileInput.files[0] ) {
            formData.append( 'egc_prescription_file', fileInput.files[0] );
        }

        $btn.prop( 'disabled', true ).text( 'Adding...' );

        $.ajax( {
            url: ajaxUrl,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function ( response ) {
                if ( response && response.success ) {
                    $( '#egc-success-banner' ).fadeIn( 200 );
                    $( '.egc-step-header' ).addClass( 'egc-nav-hidden' );
                    $( '#egc-submit-customization' ).hide();
                    $( '#egc-post-cart-actions' ).fadeIn( 200 );
                    $( document.body ).trigger( 'wc_fragment_refresh' );
                } else {
                    $btn.prop( 'disabled', false ).text( 'Add To Cart' );
                    alert( ( response && response.data && response.data.message ) ? response.data.message : 'Error adding to cart.' );
                }
            },
            error: function () {
                $btn.prop( 'disabled', false ).text( 'Add To Cart' );
                alert( 'Network error. Please try again.' );
            }
        } );
    } );

    $( document ).on( 'click', '#egc-continue-shopping-btn', function () {
        $( '#egc-close-rx-modal' ).trigger( 'click' );
    } );

    $( document ).on( 'click', '#egc-proceed-checkout-btn', function () {
        var checkoutUrl = $( '#egc-checkout-url' ).val();
        if ( checkoutUrl ) {
            window.location.href = checkoutUrl;
        }
    } );
} );