/**
 * Eye Glass Customizer - Global State & Navigation
 */

window.egcState = {
    currentStep: 1,
    totalSteps: 6,
    hasMagnification: false,
    prices: {
        frame: 0,
        swatch: 0,
        rxType: 0,
        magnification: 0,
        material: 0,
        coating: 0,
        prism: 0,
        features: 0
    },
    selections: {
        swatch: {
            title: '',
            price: 0,
            img: ''
        },
        rxType: '',
        prescriptionMode: 'fill_online',
        material: '',
        coating: '',
        features: [],
        prescription: {},
        comments: ''
    }
};

window.egcCurrencySymbol = '$';

window.egcFormatPrice = function ( amount ) {
    return window.egcCurrencySymbol + parseFloat( amount || 0 ).toFixed( 2 );
};

/**
 * Recalculate price and update bottom-right corner across ALL steps
 */
window.egcRecalculatePrice = function () {
    var lensesTotal =
        ( window.egcState.prices.rxType || 0 ) +
        ( window.egcState.prices.magnification || 0 ) +
        ( window.egcState.prices.material || 0 ) +
        ( window.egcState.prices.coating || 0 ) +
        ( window.egcState.prices.prism || 0 );

    if ( window.egcState.selections.features && window.egcState.selections.features.length ) {
        window.egcState.selections.features.forEach( function ( f ) {
            lensesTotal += parseFloat( f.price || 0 );
        } );
    }

    var frameTotal = ( window.egcState.prices.frame || 0 ) + ( window.egcState.prices.swatch || 0 );
    var grandTotal = frameTotal + lensesTotal;

    // Update all step price summaries in bottom right
    jQuery( '.egc-frame-price-val' ).text( window.egcFormatPrice( frameTotal ) );
    jQuery( '.egc-lens-price-val' ).text( window.egcFormatPrice( lensesTotal ) );
    jQuery( '.egc-grand-total-val' ).text( window.egcFormatPrice( grandTotal ) );
    jQuery( '#egc-prominent-grand-total' ).text( window.egcFormatPrice( grandTotal ) );
};

window.egcGoToStep = function ( step ) {
    if ( step < 1 || step > window.egcState.totalSteps ) {
        return;
    }

    window.egcState.currentStep = step;

    jQuery( '.egc-step-panel' ).hide().removeClass( 'is-active' );
    jQuery( '.egc-step-panel[data-step="' + step + '"]' ).show().addClass( 'is-active' );

    jQuery( '.egc-progress-item, .egc-progress-step' ).removeClass( 'is-active is-complete' );
    jQuery( '.egc-progress-item, .egc-progress-step' ).each( function () {
        var itemStep = parseInt( jQuery( this ).data( 'step' ), 10 );
        if ( itemStep < step ) {
            jQuery( this ).addClass( 'is-complete' );
        } else if ( itemStep === step ) {
            jQuery( this ).addClass( 'is-active' );
        }
    } );

    if ( step === 1 ) {
        jQuery( '#egc-step-back-btn' ).hide();
    } else {
        jQuery( '#egc-step-back-btn' ).show();
    }

    if ( step === 6 && typeof window.egcBuildSummary === 'function' ) {
        window.egcBuildSummary();
    }

    jQuery( '#egc-wizard' ).scrollTop( 0 );
    jQuery( 'html, body' ).animate( { scrollTop: 0 }, 250 );
};

jQuery( document ).ready( function ( $ ) {
    'use strict';

    if ( $( '#egc-wizard' ).length ) {
        window.egcState.prices.frame = parseFloat( $( '#egc-wizard' ).data( 'frame-price' ) ) || 0;
    }

    // Capture initial swatch selection on page load
    var $initialActiveSwatch = $( '.egc-swatch-item.is-active' ).first();
    if ( $initialActiveSwatch.length ) {
        window.egcState.selections.swatch = {
            title: $initialActiveSwatch.data( 'color-name' ) || '',
            price: parseFloat( $initialActiveSwatch.data( 'color-price' ) ) || 0,
            img:   $initialActiveSwatch.data( 'swatch-img' ) || ''
        };
        window.egcState.prices.swatch = window.egcState.selections.swatch.price;
        $( '#egc-modal-swatch-badge' ).text( 'Color: ' + window.egcState.selections.swatch.title );
    }

    // Swatch Click: Selects swatch without replacing product gallery image
    $( document ).on( 'click', '.egc-swatch-item', function ( e ) {
        e.preventDefault();
        e.stopPropagation();

        var $swatch    = $( this );
        var colorName  = $swatch.data( 'color-name' );
        var colorPrice = parseFloat( $swatch.data( 'color-price' ) ) || 0;
        var priceLabel = $swatch.data( 'color-price-label' ) || '';
        var swatchImg  = $swatch.data( 'swatch-img' ) || '';

        $( '.egc-swatch-item' ).removeClass( 'is-active' );
        $swatch.addClass( 'is-active' );

        $( '#egc-active-color-name' ).text( colorName );
        $( '#egc-active-color-price' ).text( priceLabel );
        $( '#egc-modal-swatch-badge' ).text( 'Color: ' + colorName );

        window.egcState.selections.swatch = {
            title: colorName,
            price: colorPrice,
            img: swatchImg
        };
        window.egcState.prices.swatch = colorPrice;

        window.egcRecalculatePrice();
    } );

    // Modal open / close handlers
    $( document ).on( 'click', '#egc-open-rx-modal', function ( e ) {
        e.preventDefault();
        window.egcRecalculatePrice();
        $( '#egc-fullscreen-modal' ).fadeIn( 250 );
        $( 'body' ).addClass( 'egc-modal-open' ).css( 'overflow', 'hidden' );
    } );

    $( document ).on( 'click', '#egc-close-rx-modal, .egc-btn-continue-shopping', function ( e ) {
        e.preventDefault();
        $( '#egc-fullscreen-modal' ).fadeOut( 250 );
        $( 'body' ).removeClass( 'egc-modal-open' ).css( 'overflow', 'auto' );
    } );

    // Back button
    $( '#egc-step-back-btn' ).on( 'click', function () {
        window.egcGoToStep( window.egcState.currentStep - 1 );
    } );

    // Next step navigation trigger
    $( document ).on( 'click', '.egc-next-step, .egc-next-btn', function ( e ) {
        e.preventDefault();
        var nextStep = parseInt( $( this ).data( 'next' ), 10 );

        if ( window.egcState.currentStep === 2 && nextStep > 2 ) {
            if ( typeof window.egcValidateStepTwo === 'function' && ! window.egcValidateStepTwo() ) {
                return false;
            }
        }

        if ( nextStep ) {
            window.egcGoToStep( nextStep );
        }
    } );

    if ( $( '#egc-wizard' ).length ) {
        window.egcGoToStep( 1 );
    }
} );