<?php 
/**
 * Plugin Name: GP Multi-page Form Navigation
 * Description: Navigate between form pages quickly by converting the page steps into page links or creating your own custom page links.
 * Plugin URI: http://gravitywiz.com/
 * Version: 1.0.beta2.2
 * Author: David Smith <david@gravitywiz.com>
 * Author URI: http://gravitywiz.com
 * License: GPLv2
 * Perk: True
 */

if( ! class_exists( 'GWPerk' ) ) {
	return;
}

class GP_Multi_Page_Navigation extends GWPerk {
    
    public $version = '1.0.beta2.2';
    protected $min_gravity_perks_version = '1.2.7';
    protected $min_gravity_forms_version = '1.9.1.12';

    public $_args = array();
    
    public function init() {

        $this->add_tooltip( $this->key( 'enable' ), sprintf( '<h6>%s</h6> %s', __( 'Enable Page Navigation', 'gravityperks' ), __( 'Convert the form\'s page names into clickable links that allow the user to navigation between the pages of the form.', 'gravityperks' ) ) );
        $this->add_tooltip( $this->key( 'activation_type' ), sprintf( '<h6>%s</h6> %s', __( 'Activation Type', 'gravityperks' ), __( 'Specify when the user should be able to navigate between pages and to which pages the user should be able to navigate.', 'gravityperks' ) ) );

        // admin
        add_action( 'gform_editor_js', array( $this, 'form_editor_ui' ) );

        // frontend
        add_action( 'gform_enqueue_scripts', array( $this, 'enqueue_form_scripts' ) );
        add_filter( 'gform_register_init_scripts', array( $this, 'register_init_scripts' ) );
        add_filter( 'gform_form_tag', array( $this, 'add_page_status_inputs' ), 10, 2 );

	    // run later so plugins using this hook will be bypassed as well
        add_filter( 'gform_validation', array( $this, 'maybe_bypass_validation' ), 20 );
	    add_action( 'gform_pre_process', array( $this, 'force_all_page_validation' ) );

    }

    public function form_editor_ui() {
        ?>

        <li id="gpmpn-settings" style="display:none;">

            <div class="gpmpn-setting-row">

                <div style="margin:12px 0;">
                    <input type="checkbox" name="<?php echo $this->key( 'enable' ); ?>" id="<?php echo $this->key( 'enable' ); ?>" value="1" onclick="gpmpn.toggleSettings( this.checked, true );" />
                    <label for="<?php echo $this->key( 'enable' ); ?>"><?php _e( 'Enable Page Navigation', 'gravityperks' ); ?> <?php gform_tooltip( $this->key( 'enable' ) ); ?></label>
                </div>

                <div class="gws-child-settings" style="display:none;">

                    <div id="gpmpn-activation-type-setting" class="gws-setting-row">
                        <label for="<?php echo $this->key( 'activation_type' ); ?>"><?php _e( 'Activation Type', 'gravityperks' ); ?> <?php gform_tooltip( $this->key( 'activation_type' ) ); ?></label><br />
                        <div style="margin-top:8px;">
                            <label for="<?php echo $this->key( 'activation_type' ); ?>"><?php _e( 'User can navigate to any form page', 'gravityperks' ); ?></label>
                            <select style="width:170px;" id="<?php echo $this->key( 'activation_type' ); ?>" name="<?php echo $this->key( 'activation_type' ); ?>" onchange="gpmpn.setPaginationProp( '<?php echo $this->key( 'activation_type' ); ?>', this.value );">
                                <option value="progression"><?php _e( 'they have completed', 'gravityperks' ); ?></option>
                                <option value="first_page"><?php _e( 'from the start', 'gravityperks' ); ?></option>
                                <option value="last_page"><?php _e( 'after reaching the last page', 'gravityperks' ); ?></option>
                            </select>
                        </div>
                    </div>

                    <div id="<?php echo $this->key( 'indicator_message' ); ?>" class="gws-setting-row">
                        <div class="gws-setting-message">
                            Page navigation will automatically work with the "Steps" progress indicator. When enabled with the "Progress Bar" or no progress indicator,
                                you must create custom page links to navigate between pages. <a href="http://gravitywiz.com/gravity-forms-multi-page-form-navigation/#options">Learn More</a>
                        </div>
                    </div>

                </div>

            </div>

        </li>

        <style type="text/css">

            .gws-setting-row { margin: 0 0 14px; }
            .gws-child-settings { border-left: 2px solid #eee; padding: 15px 15px 1px; margin-left: 5px; }
            .gws-setting-message { background-color: #FFFFE0; border: 1px solid #F4EFB8; padding: 10px; }

        </style>

        <script type="text/javascript">
            ( function( $ ) {

                window.gpmpn = {

                    $pageOptionsTabProps: $( '#gform_pagination_settings_tab_1' ),
                    $settingsElem:        $( '#gpmpn-settings' ).show(),

                    init: function() {

                        // add our custom pagination settings to the "Start Paging" settings
                        gpmpn.$pageOptionsTabProps.children( 'ul' ).append( gpmpn.$settingsElem );

                        // take control of GFs InitPaginationOptions() function so we can mark our inputs as checked (if applicable)
                        gpmpn.overrideInitPaginationOptionsFunction();

                        // save our custom pagination settings when the form is saved (otherwise they will be lost)
                        gform.addFilter( 'gform_pre_form_editor_save', function( form ) {

                            if( ! form.pagination ) {
                                return form;
                            }

                            form.pagination[ gpmpn.key( 'enable' ) ] = $( '#' + gpmpn.key( 'enable' ) ).is( ':checked' );
                            form.pagination[ gpmpn.key( 'activation_type' ) ] = form.pagination.type == 'steps' ? $( '#' + gpmpn.key( 'activation_type' ) ).val() : 'manual';

                            return form;
                        } );

                    },

                    toggleSettings: function( isChecked ) {

                        var $childSettings    = gpmpn.$settingsElem.find( '.gws-child-settings' ),
                            $activationType   = $( '#' + gpmpn.key( 'activation_type' ) ),
                            $indicatorMessage = $( '#' + gpmpn.key( 'indicator_message' ) ),
                            isStepsSelected   = $( '#pagination_type_steps' ).is( ':checked' );

                        gpmpn.setPaginationProp( gpmpn.key( 'enable' ), isChecked );
                        gpmpn.setPaginationProp( gpmpn.key( 'activation_type' ), $activationType.val() ? $activationType.val() : 'progression' );

                        if( isChecked ) {

                            $childSettings.slideDown();

                            if( ! isStepsSelected ) {
                                $indicatorMessage.show();
                                $( '#gpmpn-activation-type-setting' ).hide();
                            } else {
                                $indicatorMessage.hide();
                                $( '#gpmpn-activation-type-setting' ).show();
                            }

                        } else {
                            $childSettings.slideUp();
                        }

                    },

                    setPaginationProp: function( prop, value ) {
                        form.pagination[ prop ] = value;
                    },

                    getPaginationProp: function( prop ) {

                        if( ! form.pagination ) {
                            return false;
                        }

                        return form.pagination[ prop ];
                    },

                    overrideInitPaginationOptionsFunction: function() {

                        var initPaginationOptions = window.InitPaginationOptions;

                        window.InitPaginationOptions = function() {

                            initPaginationOptions();

                            if( ! form.pagination ) {
                                return;
                            }

                            var isStepsSelected = gpmpn.getPaginationProp( 'type' ),
                                type            = gpmpn.getPaginationProp( gpmpn.key( 'activation_type' ) ),
                                type            = ! isStepsSelected ? 'manual' : type && type != 'manual' ? type : 'progression';

                            $( '#' + gpmpn.key( 'enable' ) ).prop( 'checked', gpmpn.getPaginationProp( gpmpn.key( 'enable' ) ) );
                            $( '#' + gpmpn.key( 'activation_type' ) ).val( type );

                            gpmpn.toggleSettings( gpmpn.getPaginationProp( gpmpn.key( 'enable' ) ) );

                        };

                    },

                    key: function( value ) {
                        var key = '<?php echo $this->key( '' ); ?>';
                        return key + value;
                    }

                };

                gpmpn.init();

            } )( jQuery );
        </script>

        <?php
    }

    public function enqueue_form_scripts( $form ) {

        if( $this->is_navigation_enabled( $form ) ) {

            wp_enqueue_script( 'gp-multi-page-navigation', $this->get_base_url() . '/js/gp-multi-page-navigation.js', array( 'jquery' ) );

            $this->register_noconflict_script( 'gp-multi-page-navigation' );

        }

    }

    public function add_page_status_inputs( $form_tag, $form ) {

        if( ! $this->is_navigation_enabled( $form ) ) {
            return $form_tag;
        }

        $inputs = '';

        if( in_array( $this->get_activation_type( $form ), array( 'last_page', 'first_page' ) ) && $this->is_last_page_reached( $form ) ) {
            $inputs .= '<input id="gw_last_page_reached" name="gw_last_page_reached" value="1" type="hidden" />';
        }

	    // primarily required for "progression" nav type but also used by other types to check if a page is being "resubmitted"
        $page_progression = $this->get_page_progression( $form );
        $inputs .= '<input id="gw_page_progression" name="gw_page_progression" value="' . $page_progression . '" type="hidden" />';

        if( $this->was_final_submission_attempted( $form ) ) {
            $inputs .= '<input id="gw_final_submission_attempted" name="gw_final_submission_attempted" value="1" type="hidden" />';
        }

        if( $error_pages_count = count( $this->get_all_pages_with_validation_error( $form ) ) ) {
            $inputs .= sprintf( '<input id="gw_error_pages_count" name="gw_error_pages_count" value="%d" type="hidden" />', $error_pages_count );
        }

        $form_tag .= $inputs;

        return $form_tag;
    }

    public function register_init_scripts( $form ) {

        if( ! $this->is_navigation_enabled( $form ) ) {
            return;
        }

        $page_number = GFFormDisplay::get_current_page( $form['id'] );
        $last_page   = count( $form['pagination']['pages'] );

        $args = array(
            'formId'             => $form['id'],
            'lastPage'           => $last_page,
            'activationType'     => $this->get_activation_type( $form ),
            'labels'             => apply_filters( 'gpmpn_frontend_labels', array(
                'backToLastPage'     => __( 'Back to Last Page', 'gravityperks' ),
                'submit'             => _x( 'Submit', 'Option to submit multi-page form after validation error', 'gravityperks' ),
                'nextPageWithErrors' => __( 'Next Page with Errors', 'gravityperks' )
            ), $form ),
            'enableSubmissionFromLastPageWithErrors' => apply_filters( 'gpmpn_enable_submission_from_last_page_with_errors', true, $form )
        );

        $script = "new GPMultiPageNavigation( " . json_encode( $args ) . " );";

        GFFormDisplay::add_init_script( $form['id'], 'gpmpn', GFFormDisplay::ON_PAGE_RENDER, $script );

    }

    public function maybe_bypass_validation( $validation_result ) {

        if( $validation_result['is_valid'] ) {
            return $validation_result;
        }

        $form = $validation_result['form'];

        if( $this->is_bypass_validation_enabled( $form ) ) {
            $validation_result['is_valid'] = true;
        } else if( $this->is_activate_on_first_page( $form ) ) {
            $validation_result['failed_validation_page'] = $this->get_first_page_with_validation_error( $form );
            add_filter( 'gform_validation_message_' . $form['id'], array( $this, 'modify_validation_message' ), 10, 2 );
        } else if( $this->is_page_resubmission( $form ) ) {
            /*
             * If the user navigates to an earlier page, in 'progression' or 'last_reached' mode, and then uses the page
             * navigation to move forward in the form, we need to validate all pages between the page they navigated
             * back to and the page they are now navigating forward to.
             */
            $first_page_with_error = $this->get_first_page_with_validation_error( $form );
            if( $first_page_with_error < $this->get_target_page( $form ) ) {
                $validation_result['failed_validation_page'] = $this->get_first_page_with_validation_error( $form );
                add_filter( 'gform_validation_message_' . $form['id'], array( $this, 'modify_validation_message' ), 10, 2 );
            } else {
	            $validation_result['is_valid'] = true;
	            $validation_result['form'] = $this->remove_validation_errors( $form );
            }
        }

        return $validation_result;
    }

    public function modify_validation_message( $message, $form ) {

        $pages_with_erors = $this->get_all_pages_with_validation_error( $form );

        $message = array();

        $message[] = __( 'There was a problem with your submission.', 'gravityforms' );

        if( count( $pages_with_erors ) > 1 ) {
            $message[] = __( 'There are multiple pages with errors.', 'gravityperks' );
            $message[] = __( 'You have been redirected to the first page with errors.', 'gravityperks' );
        }

        $message[] = __( 'Errors have been highlighted below.', 'gravityforms' );

        return sprintf( '<div class="validation_error">%s</div>', implode( ' ', $message ) );
    }

    public function get_first_page_with_validation_error( $form ) {

        $pages = $this->get_all_pages_with_validation_error( $form );
        $page_numbers = array_keys( $pages );

        return reset( $page_numbers );
    }

    public function get_all_pages_with_validation_error( $form ) {

        $pages = array();

        foreach( $form['fields'] as $field ) {
            if( $field->failed_validation ) {
                if( ! isset( $pages[ $field->pageNumber ] ) ) {
                    $pages[ $field->pageNumber ] = 1;
                } else {
                    $pages[ $field->pageNumber ]++;
                }
            }
        }

        return $pages;

    }

    public function add_all_fields_to_last_page( $form ) {

        $last_page = count( $form['pagination']['pages'] );

        foreach( $form['fields'] as &$field ) {
            $field['origPageNumber'] = $field['pageNumber'];
            $field['pageNumber'] = $last_page;
        }

        return $form;
    }

    public function restore_fields_to_original_pages( $validation_result ) {

        foreach( $validation_result['form']['fields'] as &$field ) {
            $field['pageNumber'] = $field['origPageNumber'];
        }

        return $validation_result;
    }

    /**
     * Force ALL pages to be validated (rather than the last page submitted) by setting the source page to 0.
     *
     * This should only occur when the activation type is "first_page" and the last form page is being submitted.
     *
     * @param $form
     */
    public function force_all_page_validation( $form ) {

	    if( ! $this->is_navigation_enabled( $form ) ) {
		    return $form;
	    }

        $is_last_page                            = GFFormDisplay::is_last_page( $form );
        $is_saving_for_later                     = rgpost( 'gform_save' ) == true;
	    $validate_for_first_page_activation_type = $is_last_page && $this->is_activate_on_first_page( $form );
	    $force_all_page_validation               = ! $is_saving_for_later && ( $validate_for_first_page_activation_type || $this->is_page_resubmission( $form ) );

        // @todo: GF now supports all page validation on the last page, remove this in future version
        if( $force_all_page_validation ) {
	        $_POST["gform_source_page_number_{$form['id']}"] = 0;
        }

        return $form;
    }

	public function remove_validation_errors( $form ) {
		foreach( $form['fields'] as &$field ) {
			$field->failed_validation = false;
		}
		return $form;
	}



    // # HELPERS

    public function is_last_page_reached( $form ) {
        return rgpost( 'gw_last_page_reached' ) || GFFormDisplay::is_last_page( $form, 'render' );
    }

    public function is_navigation_enabled( $form ) {
        return rgars( $form, 'pagination/' . $this->key( 'enable' ) ) == true;
    }

    public function get_activation_type( $form ) {
        $type = rgars( $form, 'pagination/' . $this->key( 'activation_type' ) );
        return $type ? $type : 'progression';
    }

    public function is_activate_on_last_page( $form ) {
        return $this->get_activation_type( $form ) == 'last_page';
    }

	public function is_activate_on_first_page( $form ) {
		return $this->get_activation_type( $form ) == 'first_page' || rgars( $form, 'pagination/type' ) != 'steps';
	}

    public function is_bypass_validation_enabled( $form ) {
         return $this->is_activate_on_first_page( $form ) && rgpost( 'gw_bypass_validation' );
    }

    public function was_final_submission_attempted( $form ) {
        return rgpost( 'gw_final_submission_attempted' ) || (string) rgpost( sprintf( 'gform_target_page_number_%s', $form['id'] ) ) === '0';
    }

	public function is_page_resubmission( $form ) {
		return GFFormDisplay::get_source_page( $form['id'] ) < $this->get_page_progression( $form );
	}

    public function get_page_progression( $form ) {
        return (int) max( intval( rgpost( 'gw_page_progression' ) ), GFFormDisplay::get_current_page( $form['id'] ) );
    }

    public function get_target_page( $form ) {

        $current_page = GFFormDisplay::get_source_page( $form['id'] );
        $field_values = GFForms::post( 'gform_field_values' );
        $target_page  = (int) GFFormDisplay::get_target_page( $form, $current_page, $field_values );

        return $target_page;
    }

    public function documentation() {
        return array(
            'type'  => 'url',
            'value' => 'http://gravitywiz.com/gravity-forms-multi-page-form-navigation'
        );
    }
    
}
