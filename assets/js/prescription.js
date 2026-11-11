/**
 * Step 2: Prescription Validation, Mandatory DOB Across All Tabs, File Capture & Prism Pricing
 */
window.egcPrescriptionFileUrl = '';

window.egcValidateStepTwo = function () {
    var $ = jQuery;

    // 1. Check Magnification Mode first if applicable
    if ( window.egcState.hasMagnification ) {
        var rxMode = $( 'input[name="egc_rx_mode"]:checked' ).val();
        if ( ! rxMode ) {
            alert( 'Please select whether you need Magnification power or full prescription.' );
            return false;
        }

        if ( 'magnification' === rxMode ) {
            var magVal = $( '#egc-magnification-power' ).val();
            if ( ! magVal ) {
                alert( 'Please select your Magnification Power.' );
                return false;
            }
        }
    }

    // 2. Check Tab-Specific Requirements (Fill Online / Upload / Send Later)
    if ( ! window.egcState.hasMagnification || $( 'input[name="egc_rx_mode"]:checked' ).val() === 'prescription' ) {
        var $activeBtn = $( '.egc-rx-tab-btn.is-active' );
        var activeTab  = $activeBtn.attr( 'data-tab' ) || '';

        if ( ! activeTab ) {
            var btnText = $activeBtn.text().trim().toLowerCase();
            if ( btnText.indexOf( 'upload' ) !== -1 ) {
                activeTab = 'upload';
            } else if ( btnText.indexOf( 'later' ) !== -1 || btnText.indexOf( 'send' ) !== -1 ) {
                activeTab = 'later';
            } else {
                activeTab = 'fill-online';
            }
        }

        if ( activeTab === 'upload' ) {
            var fileInput = $( '#egc-prescription-file' )[0];
            if ( ! fileInput || ! fileInput.files || fileInput.files.length === 0 ) {
                alert( 'Please upload your Prescription file to proceed.' );
                return false;
            }
            window.egcState.selections.prescriptionMode = 'upload';
        } else if ( activeTab === 'later' ) {
            window.egcState.selections.prescriptionMode = 'send_later';
        } else {
            window.egcState.selections.prescriptionMode = 'fill_online';

            var odSph = $( 'select[name="egc_od_sphere"]' ).val();
            var osSph = $( 'select[name="egc_os_sphere"]' ).val();

            if ( ! odSph || odSph.toLowerCase() === 'none' || ! osSph || osSph.toLowerCase() === 'none' ) {
                alert( 'Please select Sphere (SPH) values for both Right (OD) and Left (OS) eyes.' );
                return false;
            }

            var isDual = $( '#egc-pd-dual-toggle' ).is( ':checked' );
            if ( isDual ) {
                var pdLeft  = $( '#egc-left-pd-select' ).val();
                var pdRight = $( '#egc-right-pd-select' ).val();
                if ( ! pdLeft || ! pdRight ) {
                    alert( 'Please select both Left and Right PD values.' );
                    return false;
                }
            } else {
                var singlePd = $( '#egc-single-pd-select' ).val();
                if ( ! singlePd || singlePd === 'Select PD' ) {
                    alert( 'Please select your Pupil Distance (PD).' );
                    return false;
                }
            }
        }
    }

    // 3. MANDATORY WEARER DOB VALIDATION FOR ALL TABS (Fill Online, Upload RX, Send It Later)
    var $dobMonth = $( 'select[name="egc_dob_month"]' );
    var $dobYear  = $( 'select[name="egc_dob_year"]' );
    var dobMonth  = $dobMonth.val();
    var dobYear   = $dobYear.val();

    if ( ! dobMonth || ! dobYear ) {
        $dobMonth.css( 'border', ! dobMonth ? '1.5px solid #dc2626' : '1px solid #ccc' );
        $dobYear.css( 'border', ! dobYear ? '1.5px solid #dc2626' : '1px solid #ccc' );
        alert( 'Please select Wearer Date of Birth (both Month and Year) before continuing.' );
        return false;
    } else {
        $dobMonth.css( 'border', '1px solid #ccc' );
        $dobYear.css( 'border', '1px solid #ccc' );
    }

    return true;
};

window.egcSetupStepTwoVisibility = function () {
    var $ = jQuery;
    if ( window.egcState.hasMagnification ) {
        $( '#egc-magnification-toggle-area' ).show();
        $( '#egc-full-prescription-form' ).hide();
        $( 'input[name="egc_rx_mode"]' ).prop( 'checked', false );
        $( '#egc-magnification-power-wrap' ).hide();
    } else {
        $( '#egc-magnification-toggle-area' ).hide();
        $( '#egc-full-prescription-form' ).show();
    }
};

jQuery( document ).ready( function ( $ ) {
    'use strict';

    // Clear red border when user selects DOB month or year
    $( document ).on( 'change', 'select[name="egc_dob_month"], select[name="egc_dob_year"]', function () {
        if ( $( this ).val() ) {
            $( this ).css( 'border', '1px solid #ccc' );
        }
    } );

    // Prism Selection Pricing ($25.00)
    $( document ).on( 'change', 'input[name="egc_prism"]', function () {
        var isPrism = $( 'input[name="egc_prism"]:checked' ).val() === 'yes';
        
        window.egcState.prices.prism = isPrism ? 25.00 : 0;
        window.egcState.selections.hasPrism = isPrism;

        if ( typeof window.egcRecalculatePrice === 'function' ) {
            window.egcRecalculatePrice();
        }
    } );

    $( '#egc-mode-magnification' ).on( 'change', function () {
        if ( $( this ).is( ':checked' ) ) {
            window.egcState.selections.prescriptionMode = 'magnification';
            $( '#egc-magnification-power-wrap' ).show();
            $( '#egc-full-prescription-form' ).hide();
        }
    } );

    $( '#egc-mode-prescription' ).on( 'change', function () {
        if ( $( this ).is( ':checked' ) ) {
            window.egcState.selections.prescriptionMode = 'prescription';
            $( '#egc-magnification-power-wrap' ).hide();
            $( '#egc-full-prescription-form' ).slideDown( 200 );
        }
    } );

    // Dual PD Toggle
    $( '#egc-pd-dual-toggle' ).on( 'change', function () {
        var isDual = $( this ).is( ':checked' );

        if ( isDual ) {
            $( '#egc-single-pd-select' ).val( '' ).prop( 'disabled', true ).css( 'background', '#f2f2f2' );
            $( '#egc-left-pd-select, #egc-right-pd-select' ).prop( 'disabled', false ).css( 'background', '#ffffff' );
            $( '#egc-pd-type' ).val( 'dual' );
        } else {
            $( '#egc-single-pd-select' ).prop( 'disabled', false ).css( 'background', '#ffffff' );
            $( '#egc-left-pd-select, #egc-right-pd-select' ).val( '' ).prop( 'disabled', true ).css( 'background', '#f2f2f2' );
            $( '#egc-pd-type' ).val( 'single' );
        }
    } );

    // Prescription Tabs Toggle
    $( '.egc-rx-tab-btn' ).on( 'click', function () {
        var targetTab = $( this ).attr( 'data-tab' );

        $( '.egc-rx-tab-btn' ).css( { 'background': '#fff', 'color': '#8a2be2', 'border': '1px solid #ccc' } ).removeClass( 'is-active' );
        $( this ).css( { 'background': '#8a2be2', 'color': '#fff', 'border': '1px solid #8a2be2' } ).addClass( 'is-active' );

        $( '.egc-tab-content-panel' ).hide();

        if ( targetTab === 'fill-online' ) {
            $( '#egc-tab-content-fill-online' ).show();
        } else if ( targetTab === 'upload' ) {
            $( '#egc-tab-content-upload' ).show();
        } else if ( targetTab === 'later' ) {
            $( '#egc-tab-content-later' ).show();
        }
    } );

    $( '#egc-upload-prescription-btn, .egc-tab-upload-trigger' ).on( 'click', function () {
        $( '#egc-prescription-file' ).trigger( 'click' );
    } );

    $( '#egc-prescription-file' ).on( 'change', function () {
        if ( this.files && this.files[0] ) {
            var file = this.files[0];
            $( '#egc-prescription-filename' ).text( file.name );
            window.egcPrescriptionFileUrl = URL.createObjectURL( file );
        } else {
            $( '#egc-prescription-filename' ).text( '' );
            window.egcPrescriptionFileUrl = '';
        }
    } );
} );