/**
 * Step 4: Coating Selection, Accordion Toggle & Color Swatch Handling
 */
jQuery( document ).ready( function ( $ ) {
    'use strict';

    /**
     * Load selected sidebar coating details into center container (Colors stay hidden initially)
     */
    function egcRenderCoatingCenter( $card ) {
        var $centerContainer = $( '#egc-coating-center-container' );
        var $centerContent   = $( '#egc-coating-center-content' );

        if ( ! $centerContent.length ) {
            return;
        }

        $( '.egc-coating-card' ).removeClass( 'is-active is-selected' );
        $card.addClass( 'is-active is-selected' );

        var $hiddenDetails = $card.find( '.egc-hidden-coating-details' );

        if ( $hiddenDetails.length && $.trim( $hiddenDetails.html() ) !== '' ) {
            $centerContent.html( $hiddenDetails.html() );

            // Ensure color section starts HIDDEN by default until user clicks center card
            var $colorSection = $centerContent.find( '.egc-lens-color-section' );
            if ( $colorSection.length ) {
                $colorSection.hide();
            }
        }

        // Mobile Smooth Scroll & Highlight Top Box (#C9A76D)
        if ( $( window ).width() <= 768 && $centerContainer.length ) {
            $centerContainer.removeClass( 'egc-box-highlight' );
            setTimeout( function() {
                $centerContainer.addClass( 'egc-box-highlight' );
            }, 10 );

            $( '#egc-wizard, html, body' ).animate( {
                scrollTop: $( '#egc-wizard' ).scrollTop() + ( $centerContainer.position().top - 70 )
            }, 300 );
        }
    }

    // MAIN COATING CARD CLICK (Right Sidebar / Bottom List)
    $( document ).on( 'click', '.egc-coating-card', function ( e ) {
        e.preventDefault();
        var $card = $( this );

        egcRenderCoatingCenter( $card );
    } );

    // CENTER FEATURED CARD CLICK (Accordion Toggle or Direct Selection for simple cards)
    $( document ).on( 'click', '.egc-featured-coating-card', function ( e ) {
        e.preventDefault();
        var $featuredCard = $( this );
        var $colorSection = $( '#egc-coating-center-content' ).find( '.egc-lens-color-section' );
        var title         = $featuredCard.data( 'title' ) || '';
        var hasColors     = String( $featuredCard.data( 'has-colors' ) ) === 'true';
        var img           = $featuredCard.find( '.egc-featured-left-image img, img' ).attr( 'src' ) || '';

        if ( hasColors && $colorSection.length ) {
            // Expand/collapse color options section
            $colorSection.slideToggle( 200 );
        } else {
            // Simple card without colors: select directly and proceed to Step 5
            $featuredCard.find( '.egc-purple-btn' ).text( 'Selected' );
            window.egcState.selections.coating    = title;
            window.egcState.selections.coatingImg = img;
            window.egcState.prices.coating        = 0;

            if ( typeof window.egcRecalculatePrice === 'function' ) {
                window.egcRecalculatePrice();
            }

            setTimeout( function() {
                if ( typeof window.egcGoToStep === 'function' ) {
                    window.egcGoToStep( 5 );
                }
            }, 180 );
        }
    } );

    // COLOR CARD CLICK: Selecting a specific lens color swatch
    $( document ).on( 'click', '.egc-color-card-item', function ( e ) {
        e.preventDefault();
        var $colorCard = $( this );
        var title      = $colorCard.data( 'title' ) || $colorCard.find( '.egc-color-card-title' ).text().trim();
        var price      = parseFloat( $colorCard.data( 'price' ) ) || 0;
        var img        = $colorCard.find( '.egc-color-card-img img, img' ).attr( 'src' ) || '';

        $( '.egc-color-card-item' ).removeClass( 'is-selected' );
        $colorCard.addClass( 'is-selected' );
        $colorCard.find( '.egc-select-btn' ).text( 'Selected' );

        window.egcState.selections.coating    = title;
        window.egcState.selections.coatingImg = img;
        window.egcState.prices.coating        = price;

        if ( typeof window.egcRecalculatePrice === 'function' ) {
            window.egcRecalculatePrice();
        }

        setTimeout( function() {
            if ( typeof window.egcGoToStep === 'function' ) {
                window.egcGoToStep( 5 );
            }
        }, 200 );
    } );
} );