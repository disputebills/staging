(function($){

    window.GPMultiPageNavigation = function( args ) {

        var self = this;

        self.formId    = args.formId;
        self.$formElem = $( 'form#gform_' + self.formId );

        self.lastPage           = args.lastPage;
        self.activationType     = args.activationType;
        self.labels             = args.labels;
        self.enableSubmissionFromLastPageWithErrors = args.enableSubmissionFromLastPageWithErrors;

        self.init = function() {

            // set page specific elements
            self.$footer                = $( '#gform_page_' + self.formId + '_' + self.getCurrentPage() + ' .gform_page_footer' );
            self.$saveAndContinueButton = self.$footer.find( 'a.gform_save_link' );

            if( self.activationType == 'last_page' && ! self.isLastPageReached() ) {
                return;
            }

            var $steps = $( 'form#gform_' + self.formId + ' .gf_step' );

            $steps.each( function() {

                var stepNumber = parseInt( $( this ).find( 'span.gf_step_number' ).text() );

                if( self.activationType == 'progression' && stepNumber > self.getPageProgression() ) {
                    return;
                }

                if( stepNumber != self.getCurrentPage() ) {
                    $( this ).html( self.getPageLinkMarkup( stepNumber, $( this ).html() ) ).addClass( 'gpmpn-step-linked' );
                } else {
                    $( this ).addClass( 'gpmpn-step-current' );
                }

            } );

            if( self.activationType == 'last_page' && ! self.isLastPage() && self.isLastPageReached() ) {
                self.addBackToLastPageButton();
            } else if( self.activationType == 'progression' && self.getCurrentPage() < self.getPageProgression() ) {
                self.addBackToLastPageButton( self.getPageProgression() );
            } else if( self.activationType == 'first_page' && ! self.isLastPage() && self.wasFinalSubmissionAttempted() ) {
                self.addNextPageWithErrorsButton();
            }

            var pageLinksSelector = 'a.gpmpn-page-link, a.gwmpn-page-link';

            $( document ).on( 'click', pageLinksSelector, function( event ) {
                event.preventDefault();

                var hrefArray = $( this ).attr( 'href' ).split( '#' );

                if( hrefArray.length >= 2 ) {

                    var $parentForm      = $( this ).parents( 'form' ),
                        $formElem        = $parentForm.length > 0 ? $parentForm : $( '.gform_wrapper form' ),
                        gpmpn            = $formElem.data( 'GPMultiPageNavigation' ),
                        pageNumber       = hrefArray.pop();
                        //bypassValidation = gpmpn.activationType == 'first_page';

                    GPMultiPageNavigation.postToPage( pageNumber, gpmpn.formId, true );

                }

            } );

            self.$formElem.data( 'GPMultiPageNavigation', self );

        };

        self.getPageLinkMarkup = function( stepNumber, content ) {
            return '<a href="#' + stepNumber + '" class="gwmpn-page-link gwmpn-default gpmpn-page-link gpmpn-default">' + content + '</a>';
        };

        self.addBackToLastPageButton = function( page ) {

            var page    = typeof page == 'undefined' ? self.lastPage : page,
                $button = '<input type="button" onclick="GPMultiPageNavigation.postToPage( ' + page + ', ' + self.formId + ' );" value="' + self.labels.backToLastPage + '" class="button gform_button gform_last_page_button">';

            self.insertButton( $button );

        };

        self.addNextPageWithErrorsButton = function() {

            var page     = 0,
                label    = self.getErrorPagesCount() > 1 ? self.labels.nextPageWithErrors : self.labels.submit,
                cssClass = self.getErrorPagesCount() > 1 ? 'gform_next_page_errors_button' : 'gform_resubmit_button',
                $button  = '<input type="button" onclick="GPMultiPageNavigation.postToPage( ' + page + ', ' + self.formId + ' );" value="' + label + '" class="button gform_button ' + cssClass + '">';

            if( self.getErrorPagesCount() <= 1 && ! self.enableSubmissionFromLastPageWithErrors ) {
                self.addBackToLastPageButton();
            } else {
                self.insertButton( $button );
            }

        };

        self.insertButton = function( $button ) {
            if( self.$saveAndContinueButton.length > 0 ) {
                self.$saveAndContinueButton.before( $button );
            } else {
                self.$footer.append( $button );
            }
        };

        self.getCurrentPage = function() {

            if( ! self.currentPage ) {
                self.currentPage = self.$formElem.find( 'input#gform_source_page_number_' + self.formId ).val();
            }

            return self.currentPage;
        };

        self.getPageProgression = function() {
            return parseInt( $( 'input#gw_page_progression' ).val() );
        };

        self.getErrorPagesCount = function() {

            if( ! self.errorPagesCount ) {
                self.errorPagesCount = self.$formElem.find( 'input#gw_error_pages_count' ).val();
            }

            return self.errorPagesCount;
        };

        self.isLastPage = function() {
            return self.getCurrentPage() >= self.lastPage;
        };

        self.isLastPageReached = function() {
            return self.isLastPage() || self.$formElem.find( 'input#gw_last_page_reached' ).val() == true;
        };

        self.wasFinalSubmissionAttempted = function() {
            return self.$formElem.find( 'input#gw_final_submission_attempted' ).val() == true
        };

        GPMultiPageNavigation.postToPage = function( page, formId, bypassValidation ) {

            var $form = $( 'form#gform_' + formId ),
                $targetPageInput = $form.find( 'input#gform_target_page_number_' + formId );

            $targetPageInput.val( page );

            if( bypassValidation ) {
                var $bypassValidationInput = $( '<input type="hidden" name="gw_bypass_validation" id="gw_bypass_validation" value="1" />' );
                $form.append( $bypassValidationInput );
            }

            $form.submit();

        };

        self.init();

    }

})(jQuery);

/**
 * Take over Gravity Forms gformInitSpinner function which allows us to append the spinner after other custom buttons
 */
window.gformOrigInitSpinner = window.gformInitSpinner;
window.gformInitSpinner = function( formId, spinnerUrl ) {

    if( typeof spinnerUrl == 'undefined' || ! spinnerUrl ) {
        spinnerUrl = gform.applyFilters( 'gform_spinner_url', gf_global.spinnerUrl, formId );
    }

    jQuery( '#gform_' + formId ).submit( function() {
        if( jQuery( '#gform_ajax_spinner_' + formId ).length == 0 ) {
            jQuery( '.gform_page_footer' ).append( '<img id="gform_ajax_spinner_' + formId + '"  class="gform_ajax_spinner" src="' + spinnerUrl + '" alt="" />' );
        }
    } );

};