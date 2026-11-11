/**
 * Step 1: RX Type Selection Interaction
 */
jQuery( document ).ready( function ( $ ) {
    'use strict';

    $( document ).on( 'click', '.egc-rx-type-card', function ( e ) {
        var $card = $( this );

        $( '.egc-rx-type-card' ).removeClass( 'is-selected' );
        $card.addClass( 'is-selected' );

        var img = $card.find( '.egc-card-image img, img' ).attr( 'src' ) || '';

        window.egcState.selections.rxType    = $card.data( 'title' );
        window.egcState.selections.rxTypeImg = img;
        window.egcState.prices.rxType        = parseFloat( $card.data( 'price' ) ) || 0;
        window.egcState.hasMagnification     = ( 'true' === String( $card.data( 'has-magnification' ) ) );

        if ( typeof window.egcSetupStepTwoVisibility === 'function' ) {
            window.egcSetupStepTwoVisibility();
        }

        window.egcRecalculatePrice();

        var currentLensPrice = window.egcState.prices.rxType;
        var grandTotal = window.egcState.prices.frame + currentLensPrice;

        $( '.egc-lens-price-val' ).text( window.egcFormatPrice( currentLensPrice ) );
        $( '.egc-grand-total-val' ).text( window.egcFormatPrice( grandTotal ) );

        setTimeout( function() {
            window.egcGoToStep( 2 );
        }, 150 );
    } );
} );