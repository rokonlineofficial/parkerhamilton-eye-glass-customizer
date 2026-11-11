/**
 * Step 3: Material Selection, Sub-cards Level 1 & Level 2 Accordion Toggle
 */
jQuery( document ).ready( function ( $ ) {
    'use strict';

    function egcRenderSubCards( $card ) {
        var $subContainer    = $( '#egc-sub-cards-container' );
        var $subCardsContent = $( '#egc-sub-cards-content' );

        if ( ! $subContainer.length || ! $subCardsContent.length ) {
            return;
        }

        $( '.egc-material-card' ).removeClass( 'is-active is-selected' );
        $card.addClass( 'is-active is-selected' );

        var $hiddenSubBrands = $card.find( '.egc-hidden-sub-brands' );

        if ( $hiddenSubBrands.length && $.trim( $hiddenSubBrands.html() ) !== '' ) {
            $subCardsContent.html( $hiddenSubBrands.html() );
        } else {
            var title = $card.data( 'title' ) || '';
            var desc  = $card.find( '.egc-card-description' ).html() || '';

            var fallbackHtml =
                '<div class="egc-sub-card-item egc-clickable-sub-card" data-has-sub-two="false" data-title="' + title + '" data-price="0">' +
                    '<div class="egc-sub-card-col-left">' +
                        '<h4 class="egc-sub-card-title">' + title + '</h4>' +
                    '</div>' +
                    '<div class="egc-sub-card-col-middle">' +
                        '<div class="egc-sub-card-description">' + desc + '</div>' +
                    '</div>' +
                    '<div class="egc-sub-card-col-right">' +
                        '<button type="button" class="egc-btn egc-select-btn">Select</button>' +
                    '</div>' +
                '</div>';

            $subCardsContent.html( fallbackHtml );
        }

        // Mobile smooth scroll & glow
        if ( $( window ).width() <= 768 && $subContainer.length ) {
            $subContainer.removeClass( 'egc-box-highlight' );
            setTimeout( function() {
                $subContainer.addClass( 'egc-box-highlight' );
            }, 10 );

            $( '#egc-wizard, html, body' ).animate( {
                scrollTop: $( '#egc-wizard' ).scrollTop() + ( $subContainer.position().top - 70 )
            }, 300 );
        }
    }

    // Main Material Card Click (Right Sidebar)
    $( document ).on( 'click', '.egc-material-card', function ( e ) {
        e.preventDefault();
        var $card   = $( this );
        var wrapper = $( '#egc-material-wrapper' );

        if ( wrapper.hasClass( 'has-sub-cards' ) ) {
            egcRenderSubCards( $card );
        } else {
            $( '.egc-material-card' ).removeClass( 'is-selected' );
            $card.addClass( 'is-selected' );

            window.egcState.selections.material    = $card.data( 'title' );
            window.egcState.selections.materialImg = $card.find( 'img' ).attr( 'src' ) || '';
            window.egcState.prices.material        = 0;

            if ( typeof window.egcRecalculatePrice === 'function' ) {
                window.egcRecalculatePrice();
            }

            setTimeout( function() {
                if ( typeof window.egcGoToStep === 'function' ) {
                    window.egcGoToStep( 4 );
                }
            }, 150 );
        }
    } );

    // Level 1 Sub Card Click (Center Brand Card)
    $( document ).on( 'click', '.egc-clickable-sub-card', function ( e ) {
        e.preventDefault();
        var $item       = $( this );
        var $parentWrap = $item.closest( '.egc-brand-card-block' );
        var hasSubTwo   = String( $item.data( 'has-sub-two' ) ) === 'true';

        // If it has Sub Cards Two: Toggle accordion under it and DO NOT advance step
        if ( hasSubTwo && $parentWrap.find( '.egc-sub-two-container' ).length ) {
            var $subTwoBox = $parentWrap.find( '.egc-sub-two-container' );
            var $btn       = $item.find( '.egc-sub-select-btn' );

            $subTwoBox.slideToggle( 200, function() {
                if ( $subTwoBox.is( ':visible' ) ) {
                    $btn.text( 'Options ▴' );
                } else {
                    $btn.text( 'Options ▾' );
                }
            } );
            return;
        }

        // Simple Card without Sub Cards Two: Direct select and advance
        var title = $item.data( 'title' ) || $item.find( '.egc-sub-card-title' ).text().trim();
        var price = parseFloat( $item.data( 'price' ) ) || 0;
        var img   = $item.find( 'img' ).attr( 'src' ) || '';

        $( '.egc-sub-card-item' ).removeClass( 'is-selected' );
        $item.addClass( 'is-selected' );

        window.egcState.selections.material    = title;
        window.egcState.selections.materialImg = img;
        window.egcState.prices.material        = price;

        if ( typeof window.egcRecalculatePrice === 'function' ) {
            window.egcRecalculatePrice();
        }

        setTimeout( function() {
            if ( typeof window.egcGoToStep === 'function' ) {
                window.egcGoToStep( 4 );
            }
        }, 180 );
    } );

    // Level 2 Sub Card Click (Inside Material Sub Cards Two Accordion)
    $( document ).on( 'click', '.egc-clickable-sub-two', function ( e ) {
        e.preventDefault();
        var $sub2 = $( this );
        var title = $sub2.data( 'title' );
        var price = parseFloat( $sub2.data( 'price' ) ) || 0;
        var img   = $sub2.find( 'img' ).attr( 'src' ) || '';

        $( '.egc-sub-two-card' ).css( { 'border-color': '#cbd5e1', 'box-shadow': 'none' } );
        $sub2.css( { 'border-color': '#800080', 'box-shadow': '0 0 0 2px rgba(128,0,128,0.2)' } );
        $sub2.find( '.egc-select-btn' ).text( 'Selected' ).css( 'background', '#800080' );

        window.egcState.selections.material    = title;
        window.egcState.selections.materialImg = img;
        window.egcState.prices.material        = price;

        if ( typeof window.egcRecalculatePrice === 'function' ) {
            window.egcRecalculatePrice();
        }

        setTimeout( function() {
            if ( typeof window.egcGoToStep === 'function' ) {
                window.egcGoToStep( 4 );
            }
        }, 200 );
    } );

    // Accordion Toggle for Bottom Material Differences Notes
    $( document ).on( 'click', '.lenses-note-title', function ( e ) {
        e.preventDefault();
        var $title   = $( this );
        var $item    = $title.closest( '.lenses-note-item' );
        var $content = $item.find( '.collapse' );
        var $icon    = $title.find( '.collapsible' );

        if ( $content.is( ':visible' ) ) {
            $content.slideUp( 150 );
            $icon.text( '+' );
        } else {
            $( '.lenses-note-item .collapse' ).not( $content ).slideUp( 150 );
            $( '.lenses-note-title .collapsible' ).not( $icon ).text( '+' );
            $content.slideDown( 150 );
            $icon.text( '-' );
        }
    } );
} );