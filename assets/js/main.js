;( function( $ ) {
    'use strict';
    
    $( function() {
        var $pageTemplate          = $( '#page_template' ),
            $wpTemplatePrview      = $( '#wp-template-preview' ),
            $previewImage          = $wpTemplatePrview.find( 'img' ),
            $deletableEmptyP       = $wpTemplatePrview.next(),
            templatePreviews       = $wpTemplatePrview.data( 'template-images' ),
            $wpTemplatePrviewLinks = $( '#wp-template-preview-links' ),
            $previewLinkTag        = $wpTemplatePrviewLinks.find('a').first(),
            templatePreviewLinks   = $wpTemplatePrviewLinks.data( 'template-preview-links' );

        if ( $deletableEmptyP.is( 'p' ) && 0 === $deletableEmptyP.text().length ) {
            $deletableEmptyP.remove();
        }

        $pageTemplate.after( $wpTemplatePrview.detach() );

        $pageTemplate.on( 'change.wpTemplatePreview', function() {
            var template = $( this ).val();
            if ( $.isPlainObject( templatePreviews ) && typeof templatePreviews[ template ] !== 'undefined' ) {
                $previewImage.attr( 'src', templatePreviews[ template ] );
            }

            if ( ! $previewImage.prop( 'src' ) ) {
                $wpTemplatePrview.addClass( 'hidden' );
            } else {
                $wpTemplatePrview.removeClass( 'hidden' );
            }

            if ( $.isPlainObject( templatePreviewLinks ) && typeof templatePreviewLinks[ template ] !== 'undefined' ) {
                $previewLinkTag.attr( 'href', templatePreviewLinks[ template ] );
            } else {
                $previewLinkTag.attr( 'href', '#' );
            }

            if ( ! $previewLinkTag.prop( 'href' ) || $previewLinkTag.attr( 'href' ) == '#' ) {
                $wpTemplatePrviewLinks.addClass( 'hidden' );
            } else {
                $wpTemplatePrviewLinks.removeClass( 'hidden' );
            }
        } );

        $pageTemplate.trigger( 'change.wpTemplatePreview' );
    } );
    
} ( jQuery ) );
