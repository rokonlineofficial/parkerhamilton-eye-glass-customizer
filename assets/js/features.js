/**
 * Step 5: Features Selection, Custom Engraving Text Input & YouTube Embed Modal
 */

window.openLensVideoFrame = function ( url ) {
    if ( ! url ) return;

    var embedUrl = url;

    // Transform standard YouTube links to embed format with autoplay
    if ( url.indexOf( 'youtube.com/watch?v=' ) !== -1 ) {
        var videoId = url.split( 'v=' )[1];
        var ampersandPos = videoId.indexOf( '&' );
        if ( ampersandPos !== -1 ) {
            videoId = videoId.substring( 0, ampersandPos );
        }
        embedUrl = 'https://www.youtube.com/embed/' + videoId + '?autoplay=1';
    } else if ( url.indexOf( 'youtu.be/' ) !== -1 ) {
        var videoId = url.split( 'youtu.be/' )[1];
        embedUrl = 'https://www.youtube.com/embed/' + videoId + '?autoplay=1';
    }

    jQuery( '#egc-video-iframe' ).attr( 'src', embedUrl );
    jQuery( '#egc-video-modal-overlay' ).fadeIn( 200 );
};

window.closeLensVideoFrame = function () {
    jQuery( '#egc-video-modal-overlay' ).fadeOut( 200, function() {
        jQuery( '#egc-video-iframe' ).attr( 'src', '' );
    } );
};

jQuery( document ).ready( function ( $ ) {
    'use strict';

    // Tooltip popup trigger on hover / click for header advantage icons
    $( document ).on( 'mouseenter click', '.option-advantage-title, .td-adv-title', function ( e ) {
        e.stopPropagation();
        var $cell = $( this ).closest( '.td-adv-title' );
        var $desc = $cell.find( '.option-advantage-description' );

        $( '.option-advantage-description' ).not( $desc ).stop( true, true ).hide();
        $desc.stop( true, true ).fadeIn( 150 );
    } );

    $( document ).on( 'mouseleave', '.td-adv-title', function () {
        $( this ).find( '.option-advantage-description' ).stop( true, true ).fadeOut( 150 );
    } );

    $( document ).on( 'click', function ( e ) {
        if ( ! $( e.target ).closest( '.td-adv-title' ).length ) {
            $( '.option-advantage-description' ).stop( true, true ).fadeOut( 150 );
        }
    } );

    // Toggle Add / Remove Button Handler
    $( document ).on( 'click', '.egc-toggle-feature-btn', function ( e ) {
        e.preventDefault();
        var $btn  = $( this );
        var $row  = $btn.closest( '.egc-feature-item-row' );
        var title = $row.data( 'title' );
        var price = parseFloat( $row.data( 'price' ) ) || 0;
        var img   = $row.find( '.egc-feature-title-img, img' ).attr( 'src' ) || '';

        var isActive = $btn.hasClass( 'is-active' ) || $btn.text().trim().toLowerCase() === 'remove';

        if ( isActive ) {
            $btn.removeClass( 'is-active' ).text( 'Add' );
            $row.removeClass( 'is-selected' );

            window.egcState.selections.features = window.egcState.selections.features.filter( function ( f ) {
                return f.title !== title;
            } );
        } else {
            $btn.addClass( 'is-active' ).text( 'Remove' );
            $row.addClass( 'is-selected' );

            window.egcState.selections.features.push( { title: title, price: price, img: img } );
        }

        if ( typeof window.egcRecalculatePrice === 'function' ) {
            window.egcRecalculatePrice();
        }
    } );

    // Dropdown Select Handler for Logo/Engraving Options + Toggle Custom Text Box
    $( document ).on( 'change', '.egc-feature-dropdown-select', function () {
        var $select      = $( this );
        var $row         = $select.closest( '.egc-feature-item-row' );
        var $customInput = $row.find( '.egc-custom-engraving-text' );
        var baseTitle    = $row.data( 'title' ) || 'Brand Engraving';
        var selectedVal  = $select.val();
        var img          = $row.find( '.egc-feature-title-img, img' ).attr( 'src' ) || '';

        // Remove previous engraving selection
        window.egcState.selections.features = window.egcState.selections.features.filter( function ( f ) {
            return f.type !== 'engraving' && f.title.indexOf( baseTitle ) === -1;
        } );

        if ( selectedVal && selectedVal !== 'None' ) {
            $select.addClass( 'is-active' );
            $customInput.slideDown( 150 ).focus();

            var customText = $customInput.val().trim();
            var fullTitle  = baseTitle + ': ' + selectedVal + ( customText ? ' ("' + customText + '")' : '' );

            window.egcState.selections.engravingOption     = selectedVal;
            window.egcState.selections.engravingCustomText = customText;

            // Insignia is Standard / Free ($0)
            window.egcState.selections.features.push( {
                title: fullTitle,
                price: 0,
                img: img,
                type: 'engraving',
                color: selectedVal,
                customText: customText,
                isStandard: true
            } );
        } else {
            $select.removeClass( 'is-active' );
            $customInput.val( '' ).slideUp( 150 );
            window.egcState.selections.engravingOption     = '';
            window.egcState.selections.engravingCustomText = '';
        }

        if ( typeof window.egcRecalculatePrice === 'function' ) {
            window.egcRecalculatePrice();
        }
    } );

    // Real-time Custom Engraving Text Input Capture
    $( document ).on( 'input keyup', '.egc-custom-engraving-text', function () {
        var customText = $( this ).val().trim();
        window.egcState.selections.engravingCustomText = customText;

        window.egcState.selections.features.forEach( function ( f ) {
            if ( f.type === 'engraving' ) {
                f.customText = customText;
                var baseTitle = 'Brand Engraving: ' + ( f.color || '' );
                f.title = baseTitle + ( customText ? ' ("' + customText + '")' : '' );
            }
        } );
    } );

    // Continue to Summary Button
    $( document ).on( 'click', '#egc-continue-to-summary', function ( e ) {
        e.preventDefault();
        if ( typeof window.egcGoToStep === 'function' ) {
            window.egcGoToStep( 6 );
        }
    } );
} );