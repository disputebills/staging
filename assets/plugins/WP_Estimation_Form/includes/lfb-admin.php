<?php
if (!defined('ABSPATH'))
    exit;

class LFB_admin
{

    /**
     * The single instance
     * @var    object
     * @access  private
     * @since    1.0.0
     */
    private static $_instance = null;

    /**
     * The main plugin object.
     * @var    object
     * @access  public
     * @since    1.0.0
     */
    public $parent = null;

    /**
     * Prefix for plugin settings.
     * @var     string
     * @access  publicexport
     * Delete
     * @since   1.0.0
     */
    public $base = '';

    /**
     * Available settings for plugin.
     * @var     array
     * @access  public
     * @since   1.0.0
     */
    public $settings = array();

    /**
     * Is WooCommerce activated ?
     * @var     array
     * @access  public
     * @since   1.5.0
     */
    public $isWooEnabled = false;

    public function __construct($parent)
    {
        $this->parent = $parent;
        $this->base = 'wpt_';
        $this->dir = dirname($this->parent->file);
        add_action('admin_menu', array($this, 'add_menu_item'));
        add_action('admin_print_scripts', array($this, 'admin_scripts'));
        add_action('admin_print_styles', array($this, 'admin_styles'));
        add_action('wp_ajax_nopriv_lfb_saveStep', array($this, 'saveStep'));
        add_action('wp_ajax_lfb_saveStep', array($this, 'saveStep'));
        add_action('wp_ajax_nopriv_lfb_addStep', array($this, 'addStep'));
        add_action('wp_ajax_lfb_addStep', array($this, 'addStep'));
        add_action('wp_ajax_nopriv_lfb_loadStep', array($this, 'loadStep'));
        add_action('wp_ajax_lfb_loadStep', array($this, 'loadStep'));
        add_action('wp_ajax_nopriv_lfb_duplicateStep', array($this, 'duplicateStep'));
        add_action('wp_ajax_lfb_duplicateStep', array($this, 'duplicateStep'));
        add_action('wp_ajax_nopriv_lfb_removeStep', array($this, 'removeStep'));
        add_action('wp_ajax_lfb_removeStep', array($this, 'removeStep'));
        add_action('wp_ajax_nopriv_lfb_saveStepPosition', array($this, 'saveStepPosition'));
        add_action('wp_ajax_lfb_saveStepPosition', array($this, 'saveStepPosition'));
        add_action('wp_ajax_nopriv_lfb_newLink', array($this, 'newLink'));
        add_action('wp_ajax_lfb_newLink', array($this, 'newLink'));
        add_action('wp_ajax_nopriv_lfb_changePreviewHeight', array($this, 'changePreviewHeight'));
        add_action('wp_ajax_lfb_changePreviewHeight', array($this, 'changePreviewHeight'));
        add_action('wp_ajax_nopriv_lfb_saveLinks', array($this, 'saveLinks'));
        add_action('wp_ajax_lfb_saveLinks', array($this, 'saveLinks'));
        add_action('wp_ajax_nopriv_lfb_saveSettings', array($this, 'saveSettings'));
        add_action('wp_ajax_lfb_saveSettings', array($this, 'saveSettings'));
        add_action('wp_ajax_nopriv_lfb_loadSettings', array($this, 'loadSettings'));
        add_action('wp_ajax_lfb_loadSettings', array($this, 'loadSettings'));
        add_action('wp_ajax_nopriv_lfb_removeAllSteps', array($this, 'removeAllSteps'));
        add_action('wp_ajax_lfb_removeAllSteps', array($this, 'removeAllSteps'));
        add_action('wp_ajax_nopriv_lfb_addForm', array($this, 'addForm'));
        add_action('wp_ajax_lfb_addForm', array($this, 'addForm'));
        add_action('wp_ajax_nopriv_lfb_loadForm', array($this, 'loadForm'));
        add_action('wp_ajax_lfb_loadForm', array($this, 'loadForm'));
        add_action('wp_ajax_nopriv_lfb_saveForm', array($this, 'saveForm'));
        add_action('wp_ajax_lfb_saveForm', array($this, 'saveForm'));
        add_action('wp_ajax_nopriv_lfb_removeForm', array($this, 'removeForm'));
        add_action('wp_ajax_lfb_removeForm', array($this, 'removeForm'));
        add_action('wp_ajax_nopriv_lfb_loadFields', array($this, 'loadFields'));
        add_action('wp_ajax_lfb_loadFields', array($this, 'loadFields'));
        add_action('wp_ajax_nopriv_lfb_saveField', array($this, 'saveField'));
        add_action('wp_ajax_lfb_saveField', array($this, 'saveField'));
        add_action('wp_ajax_nopriv_lfb_saveItem', array($this, 'saveItem'));
        add_action('wp_ajax_lfb_saveItem', array($this, 'saveItem'));
        add_action('wp_ajax_nopriv_lfb_removeItem', array($this, 'removeItem'));
        add_action('wp_ajax_lfb_removeItem', array($this, 'removeItem'));
        add_action('wp_ajax_nopriv_lfb_exportForms', array($this, 'exportForms'));
        add_action('wp_ajax_lfb_exportForms', array($this, 'exportForms'));
        add_action('wp_ajax_nopriv_lfb_importForms', array($this, 'importForms'));
        add_action('wp_ajax_lfb_importForms', array($this, 'importForms'));
        add_action('wp_ajax_nopriv_lfb_checkLicense', array($this, 'checkLicense'));
        add_action('wp_ajax_lfb_checkLicense', array($this, 'checkLicense'));
        add_action('wp_ajax_nopriv_lfb_duplicateForm', array($this, 'duplicateForm'));
        add_action('wp_ajax_lfb_duplicateForm', array($this, 'duplicateForm'));
        add_action('wp_ajax_nopriv_lfb_duplicateItem', array($this, 'duplicateItem'));
        add_action('wp_ajax_lfb_duplicateItem', array($this, 'duplicateItem'));
        add_action('wp_ajax_nopriv_lfb_removeField', array($this, 'removeField'));
        add_action('wp_ajax_lfb_removeField', array($this, 'removeField'));
        add_action('wp_ajax_nopriv_lfb_loadLogs', array($this, 'loadLogs'));
        add_action('wp_ajax_lfb_loadLogs', array($this, 'loadLogs'));
        add_action('wp_ajax_nopriv_lfb_removeLog', array($this, 'removeLog'));
        add_action('wp_ajax_lfb_removeLog', array($this, 'removeLog'));
        add_action('wp_ajax_nopriv_lfb_loadLog', array($this, 'loadLog'));
        add_action('wp_ajax_lfb_loadLog', array($this, 'loadLog'));
        add_action('admin_init', array($this, 'checkAutomaticUpdates'));
        add_action('admin_head', array($this->parent, 'apply_styles'));

    }

    /**
     * Add menu to admin
     * @return void
     */
    public function add_menu_item()
    {
        add_menu_page(__("E&P Form Builder", 'lfb'), __("E&P Form Builder", 'lfb'), 'manage_options', 'lfb_menu', array($this, 'view_edit_lfb'), 'dashicons-format-aside');
        $menuSlag = 'lfb_menu';
    }

    public function getSettings()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "wpefc_settings";
        $settings = $wpdb->get_results("SELECT * FROM $table_name WHERE id=1 LIMIT 1");
        $settings = $settings[0];
        return $settings;

    }


    /*
     * Main view
     */
    public function view_edit_lfb()
    {
        global $wpdb;
        $this->checkFields();
        $settings = $this->getSettings();
        wp_enqueue_style('thickbox');
        wp_enqueue_script('thickbox');


        echo '<div id="lfb_loader"></div>';
        echo '<div id="lfb_bootstraped" class="lfb_bootstraped lfb_panel">';
        echo '<div id="estimation_popup" class="wpe_bootstraped">';

        echo '<div id="lfb_formWrapper" >';
        echo '<div class="lfb_winHeader col-md-12 palette palette-turquoise">
               <span class="glyphicon glyphicon-th-list"></span>' . __('Estimation & Payment Forms', 'lfb') . '';
        echo '<div class="btn-toolbar">';
        echo '<div class="btn-group">';
        echo '<a class="btn btn-primary" href="javascript:" onclick="lfb_closeSettings();" data-toggle="tooltip" title="'.__('Return to the forms list','lfb').'" data-placement="left"><span class="glyphicon glyphicon-list"></span></a>';
        echo '</div>';
        echo '</div>'; // eof toolbar
        echo '</div>'; // eof lfb_winHeader
        echo '<div class="clearfix"></div>';


        echo '<div id="lfb_panelSettings">';
        echo '<div class="container-fluid lfb_container" style="max-width: 90%;margin: 0 auto;margin-top: 18px;">';
        echo '</div>'; // eof container
        echo '</div>'; // eof lfb_panelSettings

        echo '<div id="lfb_panelLogs">';
        echo '<div class="container-fluid lfb_container" style="max-width: 90%;margin: 0 auto;margin-top: 18px;">';
        echo '<div class="col-md-12">';
       // echo '<h3>' . __('Forms List', 'lfb') . '</h3>';

        echo '<div role="tabpanel">';
        echo '<ul class="nav nav-tabs" role="tablist" >
                <li role="presentation" class="active" ><a href="#wpefc_formsTabGeneral" aria-controls="general" role="tab" data-toggle="tab" ><span class="glyphicon glyphicon-th-list" ></span > ' . __('Orders List', 'lfb') . ' </a ></li >
                </ul >';
        echo '<div class="tab-content" >';
        echo '<div role="tabpanel" class="tab-pane active" id="wpefc_formsTabGeneral" >';
        echo '<table id="lfb_logsTable" class="table">';
        echo '<thead>';
        echo '<th>' . __('Reference', 'lfb') . '</th>';
        echo '<th>' . __('Email', 'lfb') . '</th>';
        echo '<th>' . __('Actions', 'lfb') . '</th>';
        echo '</thead>';
        echo '<tbody>';
        echo '</tbody>';
        echo '</table>';

        echo '</div>'; // eof tab-content
        echo '</div>'; // eof wpefc_formsTabGeneral
        echo '</div>'; // eof tabpanel

        echo '</div>'; // eof col-md-12"
        echo '</div>'; // eof lfb_container

        echo '</div>'; // eof lfb_panelLogs


        echo '<div class="clearfix"></div>';

        echo '<div id="lfb_panelFormsList">';
        echo '<div class="container-fluid lfb_container" style="max-width: 90%;margin: 0 auto;margin-top: 18px;">';
        echo '<div class="col-md-12">';
        echo '<div role="tabpanel">';
        echo '<ul class="nav nav-tabs" role="tablist" >
                <li role="presentation" class="active" ><a href="#wpefc_formsTabGeneral" aria-controls="general" role="tab" data-toggle="tab" ><span class="glyphicon glyphicon-th-list" ></span > ' . __('Forms List', 'lfb') . ' </a ></li >
                </ul >';
        echo '<div class="tab-content" >';
        echo '<div role="tabpanel" class="tab-pane active" id="wpefc_formsTabGeneral" >';

        echo '<p style="text-align: right; margin-top: 18px;">
            <a href="javascript:" style="margin-right: 12px;" onclick="lfb_addForm();" class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span>' . __('Add a new Form', 'lfb') . ' </a>
            <a href="javascript:" style="margin-right: 12px;" onclick=" jQuery(\'#lfb_winImport\').modal(\'show\');" class="btn btn-warning"><span class="glyphicon glyphicon-import"></span>' . __('Import forms', 'lfb') . ' </a>
            <a href="javascript:" onclick="lfb_exportForms();" class="btn btn-default"><span class="glyphicon glyphicon-export"></span>' . __('Export all forms', 'lfb') . ' </a>
         </p>';
        echo '<table class="table">';
        echo '<thead>';
        echo '<th>' . __('Form title', 'lfb') . '</th>';
        echo '<th>' . __('Shortcode', 'lfb') . '</th>';
        echo '<th>' . __('Actions', 'lfb') . '</th>';
        echo '</thead>';
        echo '<tbody>';
        $table_name = $wpdb->prefix . "wpefc_forms";
        $forms = $wpdb->get_results("SELECT * FROM $table_name ORDER BY id ASC");
        foreach ($forms as $form) {
            echo '<tr>';
            echo '<td><a href="javascript:" onclick="lfb_loadForm(' . $form->id . ');">' . $form->title . '</a></td>';
            echo '<td><a href="javascript:" onclick="lfb_showShortcodeWin('.$form->id.');" class="btn btn-info btn-circle "><span class="glyphicon glyphicon-info-sign"></span></a><code>[estimation_form form_id="'.$form->id.'"]</code></td>';
            echo '<td>';
            echo '<a href="javascript:" onclick="lfb_loadForm(' . $form->id . ');" class="btn btn-primary btn-circle " data-toggle="tooltip" title="'.__('Edit this form','lfb').'" data-placement="bottom"><span class="glyphicon glyphicon-pencil"></span></a>';
            echo '<a href="'.get_home_url().'?lfb_action=preview&form='.$form->id.'" target="_blank"  class="btn btn-default btn-circle " data-toggle="tooltip" title="'.__('Preview this form','lfb').'" data-placement="bottom"><span class="glyphicon glyphicon-eye-open"></span></a>';
            echo '<a href="javascript:" onclick="lfb_loadLogs(' . $form->id . ');" class="btn btn-default btn-circle " data-toggle="tooltip" title="'.__('View orders of this form','lfb').'" data-placement="bottom"><span class="glyphicon glyphicon-list-alt"></span></a>';
            echo '<a href="javascript:" onclick="lfb_duplicateForm(' . $form->id . ');" class="btn btn-default btn-circle " data-toggle="tooltip" title="'.__('Duplicate this form','lfb').'" data-placement="bottom"><span class="glyphicon glyphicon-duplicate"></span></a>';
            echo '<a href="javascript:" onclick="lfb_removeForm(' . $form->id . ');" class="btn btn-danger btn-circle " data-toggle="tooltip" title="'.__('Delete this form','lfb').'" data-placement="bottom"><span class="glyphicon glyphicon-trash"></span></a>';
            echo '</td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';



        echo '</div>'; // eof tab-content
        echo '</div>'; // eof wpefc_formsTabGeneral
        echo '</div>'; // eof tabpanel


        echo '</div>'; // eof col-md-12
        echo '</div>'; // eof container
        echo '</div>'; // eof lfb_panelFormsList


        echo '<div id="lfb_panelPreview">';
        echo '<div class="clearfix"></div>';
        echo '<div style="max-width: 90%;margin: 0 auto;margin-top: 18px;">
                <p class="text-right" style="float:right;">
                 <a href="javascript:" style="margin-right: 12px;" onclick="lfb_addStep( \'' . __('My Step', 'lfb') . '\');" class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span>' . __("Add a step", 'lfb') . '</a>
                <a href="javascript:" id="lfb_btnPreview" target="_blank" style="margin-right: 12px;"  class="btn btn-default"><span class="glyphicon glyphicon-eye-open"></span>' . __("View the form", 'lfb') . '</a>
                <a href="javascript:" onclick="lfb_showShortcodeWin();" style="margin-right: 12px;"  class="btn btn-default"><span class="glyphicon glyphicon-info-sign"></span>'.__('Shortcode','lfb').'</a>
                <a href="javascript:" data-toggle="modal" data-target="#modal_removeAllSteps" class="btn btn-danger"><span class="glyphicon glyphicon-trash"></span>' . __("Remove all steps", 'lfb') . '</a>
                </p>
                <h3>'.__('Steps manager','lfb').'</h3>

                <div class="clearfix"></div>
            </div>
        ';

        echo '
        <!-- Modal -->
        <div class="modal fade" id="modal_removeAllSteps" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-body">
                ' . __('Are you sure you want to delete all steps ?', 'lfb') . '
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal"  onclick="lfb_removeAllSteps();" >' . __('Yes', 'lfb') . '</button>
                <button type="button" class="btn btn-default" data-dismiss="modal" >' . __('No', 'lfb') . '</button>
              </div>
            </div>
          </div>
        </div>';

        echo '<div id="lfb_stepsOverflow">';
        echo '<div id="lfb_stepsContainer">';
        echo '<canvas id="lfb_stepsCanvas"></canvas>';
        echo '</div>';
        echo '</div>';


        echo '<div id="lfb_formFields" style="max-width: 90%;margin: 0 auto;margin-top: 18px;" >
                <h3>'.__('Form settings','lfb').'</h3>
            <div role="tabpanel" >

              <!--Nav tabs-->
              <ul class="nav nav-tabs" role="tablist" >
                <li role="presentation" class="active" ><a href="#lfb_tabGeneral" aria-controls="general" role="tab" data-toggle="tab" ><span class="glyphicon glyphicon-cog" ></span > ' . __('General', 'lfb') . ' </a ></li >
                <li role="presentation" ><a href="#lfb_tabEmail" aria-controls="email" role="tab" data-toggle="tab" ><span class="glyphicon glyphicon-envelope" ></span > ' . __('Email', 'lfb') . ' </a ></li >
                <li role="presentation" ><a href="#lfb_tabLastStep" aria-controls="last step" role="tab" data-toggle="tab" ><span class="glyphicon glyphicon-list" ></span > ' . __('Last Step', 'lfb') . ' </a ></li >
                <li role="presentation" ><a href="#lfb_tabDesign" aria-controls="design" role="tab" data-toggle="tab" ><span class="glyphicon glyphicon-tint" ></span > ' . __('Design', 'lfb') . ' </a ></li >
              </ul >

              <!--Tab panes-->
              <div class="tab-content" >
                <div role="tabpanel" class="tab-pane active" id="lfb_tabGeneral" >
                    <div class="row-fluid" >
                        <div class="col-md-6" >
                            <h4 > ' . __('Texts', 'lfb') . ' </h4 >
                            <div class="form-group" >
                                <label > ' . __('Title', 'lfb') . ' </label >
                                <input type="text" name="title" class="form-control" />
                                <small> ' . __('The form title', 'lfb') . ' </small>
                            </div>
                            <div class="form-group" >
                                <label > ' . __('Selection required') . ' </label >
                                <input type="text" name="errorMessage" class="form-control" />
                                <small> ' . __('Something like "You need to select an item to continue"', 'lfb') . ' </small>
                            </div>
                            <div class="form-group" >
                                <label > ' . __('Button "next step"') . ' </label >
                                <input type="text" name="btn_step" class="form-control" />
                                <small> ' . __('Something like "NEXT STEP"', 'lfb') . ' </small>
                            </div>
                            <div class="form-group" >
                                <label > ' . __('Link "previous step"') . ' </label >
                                <input type="text" name="previous_step" class="form-control" />
                                <small> ' . __('Something like "return to previous step"', 'lfb') . ' </small>
                            </div>
                             <h4 > ' . __('Introduction', 'lfb') . ' </h4 >
                            <div class="form-group" >
                                <label> ' . __('Enable Introduction ? ') . ' </label >
                                <input type="checkbox"  name="intro_enabled" data-switch="switch" data-on-label="'.__('Yes','lfb').'" data-off-label="'.__('No','lfb').'" />
                                <small> ' . __('Is Introduction enabled ? ', 'lfb') . ' </small>
                            </div>
                             <div class="form-group" >
                                <label > ' . __('Introduction title', 'lfb') . ' </label >
                                <input type="text" name="intro_title" class="form-control" />
                                <small> ' . __('Something like "HOW MUCH TO MAKE MY WEBSITE ?"', 'lfb') . ' </small>
                            </div>
                            <div class="form-group" >
                                <label > ' . __('Introduction text', 'lfb') . ' </label >
                                <input type="text" name="intro_text" class="form-control" />
                                <small> ' . __('Something like "Estimate the cost of a website easily using this awesome tool."', 'lfb') . ' </small>
                            </div>
                            <div class="form-group" >
                                <label > ' . __('Introduction button', 'lfb') . ' </label >
                                <input type="text" name="intro_btn" class="form-control" />
                                <small> ' . __('Something like "GET STARTED"', 'lfb') . ' </small>
                            </div>
                        </div>
                        <div class="col-md-6" >
                            <h4 > ' . __('Options', 'lfb') . ' </h4 >
                            <div class="form-group" >
                                <label > ' . __('Order reference prefix') . ' </label >
                                <input type="text" name="ref_root" class="form-control" />
                                <small> ' . __('Enter a prefix for the order reference', 'lfb') . ' </small>
                            </div>
                            <div class="form-group" >
                                <label > ' . __('Initial price') . ' </label >
                                <input type="number" step="any" name="initial_price" class="form-control" />
                                <small> ' . __('Starting price', 'lfb') . ' </small>
                            </div>
                            <div class="form-group" >
                                <label > ' . __('Maximum price') . ' </label >
                                <input type="number" step="any"  name="max_price" class="form-control" />
                                <small> ' . __('Leave blank for automatic calculation', 'lfb') . ' </small>
                            </div>
                            <div class="form-group" >
                                <label > ' . __('Hide initial price in the progress bar ? ') . ' </label >
                                <input type="checkbox"  name="show_initialPrice" data-switch="switch" data-on-label="'.__('Yes','lfb').'" data-off-label="'.__('No','lfb').'"class=""   />
                                <small> ' . __('Display or hide the initial price from progress bar', 'lfb') . ' </small>
                            </div>
                            <div class="form-group" >
                                <label > ' . __('Progress bar shows') . ' </label >
                                <select  name="showSteps" class="form-control" />
                                    <option value="0" > ' . __('Price', 'lfb') . ' </option >
                                    <option value="1" > ' . __('Step', 'lfb') . ' </option >
                                </select >
                                <small> ' . __('The progress bar can show the price or step number', 'lfb') . ' </small>
                            </div>
                            <div class="form-group" >
                                <label > ' . __('Currency') . ' </label >
                                <input type="text"  name="currency" class="form-control" />
                                <small> ' . __('$, € , £ ...', 'lfb') . ' </small>
                            </div>
                            <div class="form-group" >
                                <label > ' . __('Currency Position') . ' </label >
                                <select  name="currencyPosition" class="form-control" />
                                    <option value="right" > ' . __('Right', 'lfb') . ' </option >
                                    <option value="left" > ' . __('Left', 'lfb') . ' </option >
                                </select >
                                <small> ' . __('Sets the currency position in the price', 'lfb') . ' </small>
                            </div>
                            <div class="form-group" >
                                <label > ' . __('Animations speed') . ' </label >
                                <input type="number" step="0.1"  name="animationsSpeed" class="form-control" />
                                <small> ' . __('Sets the animations speed, in seconds(default : 0.5)', 'lfb') . ' </small>
                            </div>
                            <div class="form-group" >
                                <label > ' . __('Quantity selection style') . ' </label >
                                <select  name="qtType" class="form-control" />
                                    <option value="0" > ' . __('Buttons', 'lfb') . ' </option >
                                    <option value="1" > ' . __('Field', 'lfb') . ' </option >
                                </select >
                                <small> ' . __('If "field", tooltip will be positionned on top', 'lfb') . ' </small>
                            </div>
                            <div class="form-group" >
                                <label > ' . __('Hide tooltips on touch devices ?') . ' </label >
                                <input type="checkbox"  name="disableTipMobile" data-switch="switch" data-on-label="'.__('Yes','lfb').'" data-off-label="'.__('No','lfb').'"class=""   />
                                <small> ' . __('Hide tooltips on touch devices ?', 'lfb') . ' </small>
                            </div>

                        </div>
                    </div>
                    <div class="clearfix" ></div>
                </div>
                <div role="tabpanel" class="tab-pane" id="lfb_tabEmail" >
                    <div class="row-fluid" >
                        <div class="col-md-6" >
                            <h4 > ' . __('Admin email', 'lfb') . ' </h4 >
                            <div class="form-group" >
                                <label > ' . __('Admin email', 'lfb') . ' </label >
                                <input type="text" name="email" class="form-control" />
                                <small> ' . __('Email that will receive requests', 'lfb') . ' </small>
                            </div>
                             <div class="form-group" >
                                <label > ' . __('Admin email subject', 'lfb') . ' </label >
                                <input type="text" name="email_subject" class="form-control" />
                                <small> ' . __('Something like "New order from your website"', 'lfb') . ' </small>
                            </div>
                            <div class="form-group" >
                               <!-- <label> ' . __('Admin email content', 'lfb') . ' </label> -->
                                    <p><strong> ' . __('Variables', 'lfb') . ' :</strong></p >
                                <div class="palette palette-turquoise" >
                                    <p>
    [project_content] : ' . __('Selected items list', 'lfb') . ' <br/>
                                        <strong>[information_content]</strong> : ' . __('Last step form values', 'lfb') . ' <br/>
                                        <strong>[total_price]</strong> : ' . __('Total Price', 'lfb') . ' <br/>
                                        <strong>[ref]</strong> : ' . __('Order reference', 'lfb') . ' <br/>
                                    </p >
                                </div>
                                <div id="email_adminContent_editor" >
        ' . wp_editor('<p>Ref: <strong>[ref]</strong></p><h2 style="color: #008080;">Information</h2><hr/><span style="font-weight: 600; color: #444444;">[information_content]</span><span style="color: #444444;"> </span><hr/><h2 style="color: #008080;">Project</h2><hr/>[project_content]<hr/><h4>Total: <strong><span style="color: #444444;">[total_price]</span></strong></h4>', 'email_adminContent', array('tinymce' => array('height' => 300))) . '
    </div>
                            </div>
                        </div>
                             <div class="col-md-6" >
                            <h4 > ' . __('Customer email', 'lfb') . ' </h4 >
                             <div class="form-group" >
                                <label > ' . __('Send email to the customer ? ') . ' </label >
                                <input type="checkbox"  name="email_toUser" data-switch="switch" data-on-label="'.__('Yes','lfb').'" data-off-label="'.__('No','lfb').'" />
                                <small> ' . __('If true, the user will receive a confirmation email', 'lfb') . ' </small>
                            </div>
                            <div id="lfb_formEmailUser" >
                             <div class="form-group" >
                                <label > ' . __('Customer email subject', 'lfb') . ' </label >
                                <input type="text" name="email_userSubject" class="form-control" />
                                <small> ' . __('Something like "Order confirmation"', 'lfb') . ' </small>
                            </div>
                            <div class="form-group" >
                                    <p><strong > ' . __('Variables', 'lfb') . ' :</strong ></p >
                                <div class="palette palette-turquoise" >
                                    <p>
    [project_content] : ' . __('Selected items list', 'lfb') . ' <br/>
                                        <strong>[information_content]</strong> : ' . __('Last step form values', 'lfb') . ' <br/>
                                        <strong>[total_price]</strong> : ' . __('Total Price', 'lfb') . ' <br/>
                                        <strong>[ref]</strong> : ' . __('Order reference', 'lfb') . ' <br/>
                                    </p >
                                </div>
                                <div id="email_userContent_editor" >
        ' . wp_editor('<p>Ref: <strong>[ref]</strong></p><h2 style="color: #008080;">Information</h2><hr/><span style="font-weight: 600; color: #444444;">[information_content]</span><span style="color: #444444;"> </span><hr/><h2 style="color: #008080;">Project</h2><hr/>[project_content]<hr/><h4>Total: <strong><span style="color: #444444;">[total_price]</span></strong></h4>', 'email_userContent', array('tinymce' => array('height' => 300))) . '
    </div>
                            </div>
                        </div>

                    </div>
                    <div class="clearfix" ></div>
                </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="lfb_tabLastStep" >
                    <div class="row-fluid" >
                        <div class="col-md-6" >
                            <div class="form-group" >
                                <label > ' . __('Call an url on close', 'lfb') . ' </label >
                                <input type="text" name="close_url" class="form-control" />
                                <small> ' . __('Complete this field if you want to call a specific url on close . Otherwise leave it empty.', 'lfb') . ' </small>
                            </div>
                                <div class="form-group" >
                                    <label > ' . __('Hide the final price ?', 'lfb') . ' </label >
                                    <input  type="checkbox"  name="hideFinalPrice" data-switch="switch" data-on-label="'.__('Yes','lfb').'" data-off-label="'.__('No','lfb').'"/>
                                    <small> ' . __('Set on true to hide the price on the last step.', 'lfb') . ' </small>
                                </div>
                            <h4 > ' . __('Texts', 'lfb') . ' </h4 >
                             <div class="form-group" >
                                <label > ' . __('Last step title', 'lfb') . ' </label >
                                <input type="text" name="last_title" class="form-control" />
                                <small> ' . __('Something like "Final cost", "Result" ...', 'lfb') . ' </small>
                            </div>
                             <div class="form-group" >
                                <label > ' . __('Last step text', 'lfb') . ' </label >
                                <input type="text" name="last_text" class="form-control" />
                                <small> ' . __('Something like "The final estimated price is :"', 'lfb') . ' </small>
                            </div>
                             <div class="form-group" >
                                <label > ' . __('Last step button', 'lfb') . ' </label >
                                <input type="text" name="last_btn" class="form-control" />
                                <small> ' . __('Something like "ORDER MY WEBSITE"', 'lfb') . ' </small>
                            </div>
                             <div class="form-group" >
                                <label > ' . __('Succeed text', 'lfb') . ' </label >
                                <input type="text" name="succeed_text" class="form-control" />
                                <small> ' . __('Something like "Thanks, we will contact you soon"', 'lfb') . ' </small>
                            </div>

                            <h4 > ' . __('Legal notice', 'lfb') . ' </h4 >
                            <div class="form-group" >
                                <label > ' . __('Enable legal notice ?') . ' </label >
                                <input type="checkbox"  name="legalNoticeEnable" data-switch="switch" data-on-label="'.__('Yes','lfb').'" data-off-label="'.__('No','lfb').'" />
                                <small> ' . __('If true, the user must accept the notice before submitting the form', 'lfb') . ' </small>
                            </div>
                            <div class="form-group" >
                               <label > ' . __('Sentence of acceptance', 'lfb') . ' </label >
                               <input type="text" name="legalNoticeTitle" class="form-control" />
                               <small> ' . __('Something like "I certify I completely read and I accept the legal notice by validating this form"', 'lfb') . ' </small>
                           </div>
                           <div class="form-group" >
                              <label > ' . __('Content of the legal notice', 'lfb') . ' </label >
                              <textarea name="legalNoticeContent" class="form-control"></textarea>
                              <small> ' . __('Write your legal notice here', 'lfb') . ' </small>
                          </div>
                        </div>
                        <div class="col-md-6" >
                            <h4> ' . __('Paypal payment', 'lfb') . ' </h4 >
                            <div class="form-group" >
                                <label > ' . __('Use paypal payment') . ' </label >
                                <input type="checkbox"  name="use_paypal" data-switch="switch" data-on-label="'.__('Yes','lfb').'" data-off-label="'.__('No','lfb').'" />
                                <small> ' . __('If true, the user will be redirected on the payment page', 'lfb') . ' </small>
                            </div>
                            <div id="lfb_formPaypal" >
                             <div class="form-group" >
                                <label > ' . __('Paypal email', 'lfb') . ' </label >
                                <input type="text" name="paypal_email" class="form-control" />
                                <small> ' . __('Enter your paypal email', 'lfb') . ' </small>
                            </div>
                            <div class="form-group" >
                                <label > ' . __('Use paypal payment') . ' </label >
                                <select name="paypal_currency" class="form-control" />
                                    <option value="AUD" > AUD</option >
                                    <option value="CAD" > CAD</option >
                                    <option value="CZK" > CZK</option >
                                    <option value="DKK" > DKK</option >
                                    <option value="EUR" > EUR</option >
                                    <option value="HKD" > HKD</option >
                                    <option value="HUF" > HUF</option >
                                    <option value="JPY" > JPY</option >
                                    <option value="NOK" > NOK</option >
                                    <option value="MXN" > MXN </option >
                                    <option value="NZD" > NZD</option >
                                    <option value="PLN" > PLN</option >
                                    <option value="GBP" > GBP</option >
                                    <option value="SGD" > SGD</option >
                                    <option value="SEK" > SEK</option >
                                    <option value="CHF" > CHF</option >
                                    <option value="USD" > USD</option >
                                    <option value="RUB" > RUB</option >
                                    <option value="PHP" > PHP</option >
                                    <option value="ILS" > ILS</option >
                                    <option value="BRL" > BRL</option >
                                </select >
                                <small> ' . __('Enter your paypal currency', 'lfb') . ' </small>
                            </div>
                            </div> ';

        if (is_plugin_active('gravityforms/gravityforms.php')) {
            echo ' <h4>' . __('Gravity Form', 'lfb') . ' </h4>
                                 <div class="form-group" >
                                <label> ' . __('Assign a Gravity Form to the last step') . ' </label>
                                <select name="gravityFormID" class="form-control" />
                                    <option value="0" > ' . __('None', 'lfb') . ' </option> ';
            $formsG = RGFormsModel::get_forms(null, "title");
            foreach ($formsG as $formG) {
                echo '<option value="' . $formG->id . '" > ' . $formG->title . '</option > ';
            }
            echo '
                                </select>
                                <small> ' . __('If true, the user will be redirected on the payment page', 'lfb') . ' </small>
                            </div>
    ';

        }
        if (is_plugin_active('woocommerce/woocommerce.php')) {
            $disp = '';
        } else {
            $disp = 'style="display:none;"';
        }
        echo ' <div ' . $disp . ' ><h4 > ' . __('Woo Commerce', 'lfb') . ' </h4 >
                            <div class="form-group" >
                                    <label > ' . __('Add selected items to cart') . ' </label >
                                    <input type="checkbox"  name="save_to_cart" data-switch="switch" data-on-label="'.__('Yes','lfb').'" data-off-label="'.__('No','lfb').'" />
                                    <small> ' . __('If true, all items with price must beings products of the woo catalog', 'lfb') . ' </small>
                                </div>
                        </div>

                        <div class="col-md-12" id="lfb_finalStepFields" >
                            <h4 > ' . __('Fields of the final step', 'lfb') . ' </h4 >
                            <p style="text-align: left;" ><a href="javascript:" id="lfb_addFieldBtn" onclick="lfb_editField(0);" class="btn btn-primary" ><span class="glyphicon glyphicon-plus" ></span > ' . __('Add a field', 'lfb') . ' </a ></p >
                            <table class="table table-striped table-bordered" >
                                <thead >
                                    <tr >
                                        <th > ' . __('Label', 'lfb') . ' </th >
                                        <th > ' . __('Order', 'lfb') . ' </th >
                                        <th > ' . __('Type', 'lfb') . ' </th >
                                        <th > ' . __('Actions', 'lfb') . ' </th >
                                    </tr >
                                </thead >
                                <tbody >
                                </tbody >
                            </table >


                        </div><div class="clearfix" ></div>
                        </div>
                    <div class="clearfix" ></div>
    ';


        echo ' </div></div>
                  <!--    <div class="clearfix" ></div>
               </div> -->
                <div role="tabpanel" class="tab-pane" id="lfb_tabDesign" >
                    <div class="row-fluid" >
                            <div class="col-md-12" >
                                <h4 > ' . __('Design', 'lfb') . ' </h4 >
                            </div>
                            <div class="col-md-6" >
                                <div class="form-group" >
                                    <label > ' . __('Main color', 'lfb') . ' </label >
                                    <input type="text" name="colorA" class="form-control colorpick" />
                                    <small> ' . __('ex : #1abc9c', 'lfb') . '</small>
                                </div>
                                <div class="form-group" >
                                    <label > ' . __('Secondary  color', 'lfb') . ' </label >
                                    <input type="text" name="colorB" class="form-control colorpick" />
                                    <small> ' . __('ex : #34495e', 'lfb') . '</small>
                                </div>
                                  <div class="form-group" >
                                      <label > ' . __('Texts color', 'lfb') . ' </label >
                                      <input type="text" name="colorC" class="form-control colorpick" />
                                      <small> ' . __('ex : #bdc3c7', 'lfb') . '</small>
                                  </div>
                            </div>
                            <div class="col-md-6" >
                                <div class="form-group" >
                                    <label > ' . __('Pictures size', 'lfb') . ' </label >
                                    <input type="number" name="item_pictures_size" class="form-control" />
                                    <small> ' . __('Enter a size in pixels(ex : 64)', 'lfb') . ' </small>
                                </div>
                                <div class="form-group" >
                                    <label > ' . __('Price font size', 'lfb') . ' </label >
                                    <input type="number" name="priceFontSize" class="form-control" />
                                    <small> ' . __('Enter a font size(ex : 18)', 'lfb') . ' </small>
                                </div>
                            </div>
                            <div class="col-md-12">

                            <div class="form-group" >
                                <label > ' . __('Custom css rules', 'lfb') . ' </label >
                                <textarea name="customCss" class="form-control" style=" width: 100%; max-width: inherit; height: 100px;}"></textarea>
                                <small> ' . __('Enter your custom css code here', 'lfb') . '</small>
                            </div>
                            </div>
                    </div>
                    <div class="clearfix" ></div>

                </div>
				<p style="text-align: center; padding-top: 18px;" ><a href="javascript:" onclick="lfb_saveForm();" class="btn btn-lg btn-primary" ><span class="glyphicon glyphicon-floppy-disk" ></span > ' . __('Save', 'lfb') . ' </a ></p >
              </div>

            </div> ';
        echo '<div class="clearfix" ></div>';


        echo '</div> ';


        echo '</div> ';
        echo ' <div id="lfb_fieldBubble" class="container-fluid" >
                <div >
                    <input type="hidden" name="id" class="form-control" />
                <div class="col-md-6" >
                <div class="form-group" >
                    <label > ' . __('Label', 'lfb') . ' </label >
                    <input type="text" name="label" class="form-control" />
                    <small> ' . __('This is the field label', 'lfb') . ' </small>
                </div>
                <div class="form-group" >
                    <label > ' . __('Order', 'lfb') . ' </label >
                    <input type="number" name="ordersort" class="form-control" />
                    <small> ' . __('Fields take place according to the order', 'lfb') . ' </small>
                </div>
                <div class="form-group" >
                    <label > ' . __('Type of field', 'lfb') . ' </label >
                    <select name="typefield" class="form-control" />
                        <option value="input" selected="" selected > Input</option >
                        <option value="textarea" > Textarea</option >
                    </select >
                    <small> ' . __('Choose a type', 'lfb') . ' </small>
                </div>
                </div>
                <div class="col-md-6" >
                <div class="form-group" >
                    <label > ' . __('Validation', 'lfb') . ' </label >
                    <select name="validation" class="form-control" />
                        <option value="" selected > None</option >
                        <option value="fill" > Must be filled </option >
                        <option value="email" > Email</option >
                    </select >
                    <small> ' . __('Select a validation method', 'lfb') . ' </small>
                </div>
                <div class="form-group" >
                    <label > ' . __('Toggle or displayed ? ', 'lfb') . ' </label >
                    <select name="visibility" class="form-control" />
                        <option value="display" selected > Displayed</option >
                        <option value="toggle" > Toggle</option >
                    </select >
                </div>

                <div class="form-group" >
                    <label ></label >
                    <a href="javascript:" onclick="lfb_saveField();" style="display: inline-block; width: 190px;" class="btn btn-primary btn-block" ><span class="glyphicon glyphicon-floppy-disk"></span> Save</a>
                </div>


                </div>
                </div>
            </div> ';

        echo '<div id="lfb_winLink" class="lfb_window container-fluid"> ';
        echo '<div class="lfb_winHeader col-md-12 palette palette-turquoise" ><span class="glyphicon glyphicon-pencil" ></span > ' . __('Edit a link', 'lfb');

        echo ' <div class="btn-toolbar"> ';
        echo '<div class="btn-group" > ';
        echo '<a class="btn btn-primary" href="javascript:" ><span class="glyphicon glyphicon-remove lfb_btnWinClose" ></span ></a > ';
        echo '</div> ';
        echo '</div> '; // eof toolbar
        echo '</div> '; // eof header

        echo '<div class="clearfix"></div><div class="container-fluid lfb_container"   style="max-width: 90%;margin: 0 auto;margin-top: 18px;"> ';
        echo '<div role="tabpanel">';
        echo '<ul class="nav nav-tabs" role="tablist" >
                <li role="presentation" class="active" ><a href="#lfb_linkTabGeneral" aria-controls="general" role="tab" data-toggle="tab" ><span class="glyphicon glyphicon-cog" ></span > ' . __('Link conditions', 'lfb') . ' </a ></li >
                </ul >';
        echo '<div class="tab-content" >';
        echo '<div role="tabpanel" class="tab-pane active" id="lfb_linkTabGeneral" >';

        echo '<div id="lfb_linkInteractions" > ';
        echo '<div id="lfb_linkStepsPreview">
                <div id="lfb_linkOriginStep" class="lfb_stepBloc "><div class="lfb_stepBlocWrapper"><h4 id="lfb_linkOriginTitle"></h4></div> </div>
                <div id="lfb_linkStepArrow"></div>
                <div id="lfb_linkDestinationStep" class="lfb_stepBloc  "><div class="lfb_stepBlocWrapper"><h4 id="lfb_linkDestinationTitle"></h4></div></div>
              </div>';
        echo '<p><a href="javascript:" class="btn btn-primary" onclick="lfb_addLinkInteraction();" ><span class="glyphicon glyphicon-plus" ></span > ' . __('Add a condition', 'lfb') . ' </a></p> ';
        echo '<table id="lfb_conditionsTable" class="table">
                <thead>
                    <tr>
                        <th>' . __('Element', 'lfb') . '</th>
                        <th>' . __('Condition', 'lfb') . '</th>
                        <th>' . __('Value', 'lfb') . '</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody></tbody>
              </table>';

        echo '</div> '; // eof row
        echo '<div class="row" ><div class="col-md-12" ><p><a href="javascript:" onclick="lfb_linkSave();" class="btn btn-primary" style="margin-top: 24px;" ><span class="glyphicon glyphicon-ok" ></span > ' . __('Save', 'lfb') . ' </a >
              <a href="javascript:" onclick="lfb_linkDel();" class="btn btn-danger" style="margin-top: 24px;" ><span class="glyphicon glyphicon-trash" ></span > ' . __('Delete', 'lfb') . ' </a ></p ></div></div> ';

        echo '<div class="clearfix"></div>';
        echo '</div> '; // eof container

        echo '</div> '; // eof lfb_winLink
        echo '</div> ';
        echo '</div> ';
        echo '</div> ';


        echo '<div id="lfb_linkBubble"> ';
        echo '<div class="" > ';
        echo '<p id="lfb_linkNoInteraction" > ' . __('Please add interactions to the origin step') . ' </p > ';
        echo '<div id="lfb_linkInteractions" > ';
        echo '<a href="javascript:" class="btn btn-warning" onclick="lfb_addLinkInteraction();" ><span class="glyphicon glyphicon-plus" ></span > ' . __('Add a condition', 'lfb') . ' </a > ';
        echo '</div> ';
        echo '<p><a href="javascript:" onclick="lfb_interactionSave();" class="btn btn-primary" style="margin-top: 24px;" ><span class="glyphicon glyphicon-ok" ></span > ' . __('Save', 'lfb') . ' </a >
              <a href="javascript:" onclick="lfb_interactionDel();" class="btn btn-danger" style="margin-top: 24px;" ><span class="glyphicon glyphicon-trash" ></span > ' . __('Delete', 'lfb') . ' </a ></p > ';
        echo '</div> '; // eof lfb_itemWindowPanel
        echo '</div> '; // eof lfb_linkBubble

        echo '<div id="lfb_actionBubble">';

        echo '<div class="lfb_itemWindowPanel" > ';

        echo '<div class="" >
            <select id="lfb_actionSelect" class="form-control select select-primary select-block mbl" data-toggle="select" >
            <option value="" > ' . __('Nothing', 'lfb') . ' </option >
            <option value="changeUrl" > ' . __('Redirect to a page', 'lfb') . ' </option >
            <option value="executeJS" > ' . __('Execute JS code', 'lfb') . ' </option >
            <option value="showElement" > ' . __('Show an element', 'lfb') . ' </option >
            <option value="sendEmail" > ' . __('Send an email of the dialog', 'lfb') . ' </option >
            <option value="sendInteractions" > ' . __('Send past interactions as post variables to a page', 'lfb') . ' </option >
            </select >
        </div> ';

        echo '<div id="lfb_actionContent" > ';
        echo '<div data-type="changeUrl" > ';
        echo '<div class="" ><input type="text" class="form-control" name="url" placeholder="' . __('Enter the url here : http://...', 'lfb') . '" /> </div> ';
        echo '</div> '; // eof changeUrl
        echo '<div data-type="executeJS" > ';
        echo '<div class="" ><textarea class="form-control" name="executeJS" placeholder="' . __('Enter your Javascript code here', 'lfb') . '" ></textarea ></div> ';
        echo '</div> '; // eof executeJS
        echo '<div data-type="showElement" > ';
        echo '<a href="javascript:"class="btn btn-default" onclick="lfb_startSelectElement();" ><span class="glyphicon glyphicon-search" ></span > ' . __('Select an element', 'lfb') . ' </a ><input type="hidden" name="element" /><input type="hidden" name="url" /><br />';
        echo '<div id="lfb_actionElementSelected" ><span class="glyphicon glyphicon-ok-circle" ></span > ' . "&nbsp;" . __('Element selected', 'lfb') . ' </div> ';
        echo '</div> '; // eof showElement
        echo '<div data-type="sendEmail" > ';
        echo '<div class="" ><input type="text" class="form-control" name="email" placeholder="' . __('Enter the receipt email', 'lfb') . '" /> </div> ';
        echo '<div class="" ><input type="text" class="form-control" name="subject" placeholder="' . __('Enter the subject', 'lfb') . '" /> </div> ';
        echo '</div> '; // eof sendEmail
        echo '<div data-type="sendInteractions" > ';
        echo '<div class="" ><input type="text" class="form-control" name="url" placeholder="' . __('Enter the php page url here : http://...', 'lfb') . '" /> </div> ';
        echo '</div> '; // eof sendInteractions
        echo '</div> '; // eof lfb_actionContent
        echo '<p><a href="javascript:" onclick="lfb_actionSave();" class="btn btn-primary" style="margin-top: 24px;" ><span class="glyphicon glyphicon-ok" ></span > ' . __('Save', 'lfb') . ' </a >
               <a href="javascript:" onclick="lfb_actionDel();" class="btn btn-danger" style="margin-top: 24px;" ><span class="glyphicon glyphicon-trash" ></span > ' . __('Delete', 'lfb') . ' </a ></p > ';
        echo '</div> '; // eof lfb_itemWindowPanel
        // echo '</div> ';
        echo '</div> '; // eof lfb_actionBubble


        echo '<div id="lfb_interactionBubble" > ';
        echo '<div class="form-group" style="display: none;" ><label > ' . __('Unique ID', 'lfb') . ' </label ><input type="text" placeholder="' . __('Enter a unique ID', 'lfb') . '" class="form-control" name="itemID" /></div> ';
        echo '<div class="" >
            <select id="lfb_interactionSelect" class="form-control select select-primary select-block mbl" data-toggle="select" >
            <option value="" > ' . __('Nothing', 'lfb') . ' </option >
            <option value="textfield" > ' . __('Text field', 'lfb') . ' </option >
            <option value="numberfield" > ' . __('Number field', 'lfb') . ' </option >
            <option value="select" > ' . __('Select', 'lfb') . ' </option >
            <option value="button" > ' . __('Button', 'lfb') . ' </option >
            </select >
        </div> ';
        echo '<div id="lfb_interactionContent" > ';
        echo '<div data-type="textfield" > ';
        echo '<div class="form-group" ><label > ' . __('Label', 'lfb') . ' </label ><input type="text" placeholder="' . __('Label', 'lfb') . '" class="form-control" name="label" /></div> ';
        echo '<div class="form-group" ><label > ' . __('Validation', 'lfb') . ' </label ><select id="lfb_interactionValidationSelect" name="validation" class="form-control" > ';
        echo '<option value="" > ' . __('Nothing', 'lfb') . ' </option > ';
        echo '<option value="fill" > ' . __('Must be filled', 'lfb') . ' </option > ';
        echo '<option value="email" > ' . __('Email', 'lfb') . ' </option > ';
        echo '</select ></div> ';
        echo '</div> '; // eof textfield
        echo '<div data-type="numberfield" > ';
        echo '<div class="form-group" ><label > ' . __('Label', 'lfb') . ' </label ><input type="text" placeholder="' . __('Label', 'lfb') . '" name="label"  class="form-control" /></div> ';
        echo '<div class="form-group" ><label > ' . __('Use decimals', 'lfb') . ' </label ><select name="decimals" class="form-control select multiselect-primary select-block mbl" data-toggle="select" > ';
        echo '<option value="0" > ' . __('No', 'lfb') . ' </option > ';
        echo '<option value="1" > ' . __('Yes', 'lfb') . ' </option > ';
        echo '</select></div> ';
        echo '<div class="form-group" ><label > ' . __('Minimum', 'lfb') . ' </label ><input type="number" step="any" class="form-control"  name="min" /></div> ';
        echo '<div class="form-group" ><label > ' . __('Maximum', 'lfb') . ' </label ><input type="number" step="any"  class="form-control" name="max" /></div> ';
        echo '<div class="form-group" ><label > ' . __('Validation', 'lfb') . ' </label ><select id="lfb_interactionValidationSelectNum" name="validation" class="form-control" > ';
        echo '<option value="" > ' . __('Nothing', 'lfb') . ' </option > ';
        echo '<option value="fill" > ' . __('Must be filled', 'lfb') . ' </option > ';
        echo '</select ></div> ';
        echo '</div> '; // eof numberfield
        echo '<div data-type="select" > ';
        echo '<div class="form-group default" ><label > ' . __('Label', 'lfb') . ' </label ><input type="text" placeholder="' . __('Label', 'lfb') . '" class="form-control" name="label" /></div> ';
        echo '</div> '; // eof select

        echo '<div data-type="button" > ';
        echo '<div class="form-group" ><label > ' . __('Label', 'lfb') . ' </label ><input type="text" placeholder="' . __('Label', 'lfb') . '" class="form-control" name="label" /></div> ';
        echo '</div> '; // eof button

        echo '<p><a href="javascript:" onclick="lfb_interactionSave();" class="btn btn-primary" style="margin-top: 24px;" ><span class="glyphicon glyphicon-ok" ></span > ' . __('Save', 'lfb') . ' </a >
              <a href="javascript:" onclick="lfb_interactionDel();" class="btn btn-danger" style="margin-top: 24px;" ><span class="glyphicon glyphicon-trash" ></span > ' . __('Delete', 'lfb') . ' </a ></p > ';
        echo '</div> '; // eof lfb_interactionContent
        echo '</div> '; // eof lfb_interactionBubble

        echo '<div id="lfb_winStep" class="lfb_window container-fluid">';
        echo '<div class="lfb_winHeader col-md-12 palette palette-turquoise"><span class="glyphicon glyphicon-pencil"></span>' . __('Edit a step', 'lfb');

        echo '<div class="btn-toolbar">';
        echo '<div class="btn-group">';
        echo '<a class="btn btn-primary" href="javascript:"><span class="glyphicon glyphicon-remove lfb_btnWinClose"></span></a>';
        echo '</div>';
        echo '</div>'; // eof toolbar
        echo '</div>'; // eof header
        echo '<div class="clearfix"></div>';
        echo '<div class="container-fluid  lfb_container"  style="max-width: 90%;margin: 0 auto;margin-top: 18px;">';
        echo '<div role="tabpanel">';
        echo '<ul class="nav nav-tabs" role="tablist" >
                <li role="presentation" class="active" ><a href="#lfb_stepTabGeneral" aria-controls="general" role="tab" data-toggle="tab" ><span class="glyphicon glyphicon-cog" ></span > ' . __('Step', 'lfb') . ' </a ></li >
                </ul >';
        echo '<div class="tab-content" >';
        echo '<div role="tabpanel" class="tab-pane active" id="lfb_stepTabGeneral" >';
        echo '<h4>' . __('Step options', 'lfb') . ' </h4>';
        echo '<div class="col-md-6">';
        echo '<div class="form-group" >
                    <label> ' . __('Title', 'lfb') . ' </label >
                    <input type="text" name="title" class="form-control" />
                    <small> ' . __('This is the step name', 'lfb') . ' </small>
                </div>';
        echo '</div>'; // eof col-md-6
        echo '<div class="col-md-6">';
        echo '<div class="form-group" >
                    <label> ' . __('Selection required', 'lfb') . ' </label >
                     <select name="itemRequired" class="form-control" />
                        <option value="0" > ' . __('No', 'lfb') . ' </option >
                        <option value="1" > ' . __('Yes', 'lfb') . ' </option >
                    </select>
                    <small> ' . __('If true, the user must select at least one item to continue', 'lfb') . ' </small>
                </div>';
        echo '</div>'; // eof col-md-6
        echo '<div class="col-md-12">';
        echo '<p ><a href="javascript:" class="btn btn-primary" onclick="lfb_saveStep();"><span class="glyphicon glyphicon-floppy-disk"></span>' . __('Save', 'lfb') . '</a></p>';
        echo '</div>'; // eof col-md-12
        echo '<div class="clearfix"></div>';


        echo '<div role="tabpanel" id="lfb_itemsList" style="margin-top: 24px;">';
        echo '<h4>' . __('Items List', 'lfb') . ' </h4>';
        echo '<div id="lfb_itemTab" >';
        echo '<div class="col-md-12">';
        echo '<p style="padding-top: 24px;"><a href="javascript:" onclick="lfb_editItem(0);" class="btn btn-default"><span class="glyphicon glyphicon-plus"></span>' . __('Add a new Item', 'lfb') . '</a></p>';
        echo '<table id="lfb_itemsTable" class="table">';
        echo '<thead>
                <th>' . __('Title', 'lfb') . '</th>
                <th>' . __('Group', 'lfb') . '</th>
                <th>' . __('Actions', 'lfb') . '</th>
            </thead>';
        echo '<tbody>';
        echo '</tbody>';
        echo '</table>';
        echo '</div>'; // eof col-md-12
        echo '<div class="clearfix"></div>';
        echo '</div>'; // eof lfb_itemTab
        echo '</div>'; // eof tabpanel

        echo '</div>'; // eof lfb_stepTabGeneral
        echo '</div>'; // eof tab-content
        echo '</div>'; // eof tabpanel

        echo '</div>'; // eof lfb_container
        echo '</div>'; // eof win step


        echo '<div id="lfb_winItem" class="lfb_window container-fluid">';
        echo '<div class="lfb_winHeader col-md-12 palette palette-turquoise"><span class="glyphicon glyphicon-pencil"></span>' . __('Edit an item', 'lfb');

        echo '<div class="btn-toolbar">';
        echo '<div class="btn-group">';
        echo '<a class="btn btn-primary" href="javascript:"><span class="glyphicon glyphicon-remove lfb_btnWinClose"></span></a>';
        echo '</div>';
        echo '</div>'; // eof toolbar
        echo '</div>'; // eof header
        echo '<div class="clearfix"></div>';
        echo '<div class="container-fluid  lfb_container"  style="max-width: 90%;margin: 0 auto;margin-top: 18px;">';
        echo '<div role="tabpanel">';
        echo '<ul class="nav nav-tabs" role="tablist" >
                <li role="presentation" class="active" ><a href="#lfb_itemTabGeneral" aria-controls="general" role="tab" data-toggle="tab" ><span class="glyphicon glyphicon-cog" ></span > ' . __('Item options', 'lfb') . ' </a ></li >
                </ul >';
        echo '<div class="tab-content" >';
        echo '<div role="tabpanel" class="tab-pane active" id="lfb_itemTabGeneral" >';
        echo '<div class="col-md-6">';
        echo '<div class="form-group" >
                    <label> ' . __('Title', 'lfb') . ' </label >
                    <input type="text" name="title" class="form-control" />
                    <small> ' . __('This is the item name', 'lfb') . ' </small>
                </div>';
        echo '<div class="form-group" >
                    <label> ' . __('Order', 'lfb') . ' </label >
                    <input type="number" name="ordersort" class="form-control" />
                    <small> ' . __('Items take place according to the order defined', 'lfb') . ' </small>
                </div>';
        echo '<div class="form-group" >
                    <label> ' . __('Small description', 'lfb') . ' </label >
                    <textarea name="description" class="form-control" ></textarea>
                    <small> ' . __('Item small description. You can leave it empty.', 'lfb') . ' </small>
                </div>';
        echo '<div class="form-group" >
                    <label> ' . __('Group name', 'lfb') . ' </label >
                    <input type="text" name="groupitems" class="form-control" />
                    <small> ' . __('Only one of the items of a same group can be selected', 'lfb') . ' </small>
                </div>';

        echo '<div class="form-group" >
                    <label> ' . __('Type', 'lfb') . ' </label >
                    <select name="type" class="form-control">
                        <option value="picture">' . __('Picture', 'lfb') . '</option>
                        <option value="checkbox">' . __('Checkbox', 'lfb') . '</option>
                        <option value="textfield">' . __('Text field', 'lfb') . '</option>
                        <option value="select">' . __('Select field', 'lfb') . '</option>
                    </select>
                    <small> ' . __('Select a type of item', 'lfb') . ' </small>
                </div>';

          echo '<div id="lfb_itemOptionsValuesPanel"><table id="lfb_itemOptionsValues" class="table">';
          echo '<thead>';
          echo '<tr>';
          echo '<th>' . __('Options of select field', 'lfb') . '</th>';
          echo '<th></th>';
          echo '</tr>';
          echo '</thead>';
          echo '<tbody>';
          echo '<tr class="static">';
          echo '<td><div class="form-group" style="top: 10px;"><input type="text" id="option_new_value" class="form-control" value="" placeholder="' . __('Option value', 'efb') . '"></div></td>';
          echo '<td style="width: 200px;"><a href="javascript:" onclick="lfb_add_option();" class="btn btn-default"><span class="glyphicon glyphicon-plus" style="margin-right:8px;"></span>' . __('Add a new option', 'efb') . '</a></td>';
          echo '</tr>';
          echo '</tbody>';
          echo '</table></div>';

        echo '<div class="form-group picOnly" >
                    <label > ' . __('Picture', 'lfb') . ' </label >
                    <input type="text" name="image" class="form-control " style="max-width: 140px; margin-right: 10px;display: inline-block;" />
                    <a class="btn btn-default imageBtn" style=" display: inline-block;">' . __('Upload Image', 'lfb') . '</a>
                    <small display: block;> ' . __('Select a picture', 'lfb') . ' </small>
                </div>';
        echo '<div class="form-group picOnly" >
                    <label> ' . __('Tint image ?', 'lfb') . ' </label >
                    <input type="checkbox"  name="imageTint" data-switch="switch" data-on-label="'.__('Yes','lfb').'" data-off-label="'.__('No','lfb').'" />
                    <small> ' . __('Automaticly fill the picture with the main color', 'lfb') . ' </small>
                </div>';

        echo '<div class="form-group " >
                    <label> ' . __('Open url on click ?', 'lfb') . ' </label >
                    <input type="text"  name="urlTarget" class="form-control" placeholder="http://..."  />
                    <small> ' . __('If you fill an url, it will be opened in a new tab on selection', 'lfb') . ' </small>
                </div>';

        echo '<div class="form-group" >
                    <label> ' . __('Display price in title ?', 'lfb') . ' </label >
                    <input type="checkbox"  name="showPrice" data-switch="switch" data-on-label="'.__('Yes','lfb').'" data-off-label="'.__('No','lfb').'" />
                    <small> ' . __('Shows the price in the item title', 'lfb') . ' </small>
                </div>';
        echo '<div class="form-group" >
                    <label> ' . __('Use column or row ?', 'lfb') . ' </label >
                    <select name="useRow" class="form-control">
                        <option value="0">' . __('Column', 'lfb') . '</option>
                        <option value="1">' . __('Row', 'lfb') . '</option>
                    </select>
                    <small> ' . __('The item will be displayed as column or full row', 'lfb') . ' </small>
                </div>';

        echo '</div>'; // eof col-md-6
        echo '<div class="col-md-6">';
        if (is_plugin_active('woocommerce/woocommerce.php')) {
            $disp = '';
        } else {
            $disp = 'style="display:none;"';
        }
        echo '<div class="form-group" ' . $disp . '>
                    <label> ' . __('Woocommerce product', 'lfb') . ' </label>
                   <select name="wooProductID" class="form-control">
                        ';
        echo '<option value="0"> ' . __('None', 'lfb') . '</option>';
        if (is_plugin_active('woocommerce/woocommerce.php')) {
            $args = array('post_type' => 'product', 'posts_per_page' => 299, 'orderby' => 'category', 'order' => 'ASC');
            $products = get_posts($args);
            foreach ($products as $productI) {
                $product = get_product($productI->ID);
                $cat = '';
                $cats = $product->get_categories(',');
                $cats = explode(',', $cats);
                foreach ($cats as $catI) {
                    $cat = $cat . $catI . ' > ';
                }
                $sel = '';
                $dataMax = '';
                $dataImg = '';
                if ($product->is_type('simple')) {
                    /*if ($datas && $datas->wooProductID == $productI->ID) {
                        $sel='selected';
                    }*/

                    if ($product->get_stock_quantity() && $product->get_stock_quantity() > 0) {
                        if ($product->get_stock_quantity() > 5) {
                            $dataMax = 'data-max="5"';
                        } else {
                            $dataMax = 'data-max="' . $product->get_stock_quantity() . '"';
                        }
                    }
                    // check image
                    $argsI = array('post_type' => 'attachment', 'numberposts' => -1, 'post_status' => null, 'post_parent' => $productI->ID);
                    $attachments = get_posts($argsI);
                    if ($attachments[0]) {
                        $imgDom = wp_get_attachment_image($attachments[count($attachments) - 1]->ID, 'thumbnail');
                        $img = substr($imgDom, strpos($imgDom, 'src="') + 5, strpos($imgDom, '"', stripos($imgDom, 'src="') + 6) - (strpos($imgDom, 'src="') + 5));

                        $dataImg = 'data-img="' . $img . '"';
                    }

                    echo '<option ' . $sel . ' ' . $dataImg . ' ' . $dataMax . ' value="' . $productI->ID . '" data-title="' . $productI->post_title . '">' . $cat . $productI->post_title . '</option>';
                } else if ($product->is_type('variable')) {
                    $available_variations = $product->get_available_variations();
                    foreach ($available_variations as $variation) {
                        $variable_product = new WC_Product_Variation($variation['variation_id']);
                        /*if ($datas && $datas->wooProductID == $productI->ID . '_' . $variation['variation_id']) {
                            $sel='selected';
                        }*/
                        if ($variable_product->get_stock_quantity() && $variable_product->get_stock_quantity() > 0) {
                            if ($variable_product->get_stock_quantity() > 5) {
                                $dataMax = 'data-max="5"';
                            } else {
                                $dataMax = 'data-max="' . $variable_product->get_stock_quantity() . '"';
                            }
                        }
                        // check image
                        $argsI = array('post_type' => 'attachment', 'numberposts' => -1, 'post_status' => null, 'post_parent' => $productI->ID);
                        $attachments = get_posts($argsI);
                        if ($attachments[0]) {
                            $imgDom = wp_get_attachment_image($attachments[count($attachments) - 1]->ID, 'thumbnail');
                            $img = substr($imgDom, strpos($imgDom, 'src="') + 5, strpos($imgDom, '"', stripos($imgDom, 'src="') + 6) - (strpos($imgDom, 'src="') + 5));

                            $dataImg = 'data-img="' . $img . '"';
                        }
                        echo '<option ' . $sel . ' ' . $dataImg . ' ' . $dataMax . ' value="' . $productI->ID . '" data-woovariation="' . $variation['variation_id'] . '" data-title="' . $productI->post_title . ' - ' . $variation['sku'] . '">' . $cat . $productI->post_title . ' - ' . $variation['sku'] . '</option>';
                    }
                }
            }
        }
        echo '    </select>
                    <small> ' . __('You can select a product from your catalog', 'lfb') . ' </small>
                </div>';
        echo '<div class="form-group wooMasked" >
                    <label> ' . __('Price', 'lfb') . ' </label><label style="display: none;">' . __('Percentage', 'lfb') . '</label>
                    <input type="number" name="price" step="any" class="form-control" />
                    <small> ' . __('Sets the item price', 'lfb') . ' </small>
                </div>';
        echo '<div class="form-group" >
                    <label> ' . __('Calculation', 'lfb') . ' </label >
                    <select name="operation" class="form-control">
                        <option value="+">' . __('+', 'lfb') . '</option>
                        <option value="-">' . __('-', 'lfb') . '</option>
                        <option value="x">' . __('x', 'lfb') . '</option>
                        <option value="/">' . __('/', 'lfb') . '</option>
                    </select>
                    <small> ' . __('Image, checkbox or textfield ?', 'lfb') . ' </small>
                </div>';
        echo '<div class="form-group" >
                   <label> ' . __('Is selected ?', 'lfb') . ' </label >
                   <input type="checkbox"  name="ischecked" data-switch="switch" data-on-label="'.__('Yes','lfb').'" data-off-label="'.__('No','lfb').'" />
                    <small> ' . __('Is the item selected by default ?', 'lfb') . ' </small>
                </div>';
        echo '<div class="form-group" >
                    <label> ' . __('Is required ?', 'lfb') . ' </label >
                    <input type="checkbox"  name="isRequired" data-switch="switch" data-on-label="'.__('Yes','lfb').'" data-off-label="'.__('No','lfb').'" />
                    <small> ' . __('Is the item required to continue ?', 'lfb') . ' </small>
                </div>';

        echo '<div class="form-group" >
                    <label> ' . __('Enable quantity choice ?', 'lfb') . ' </label >
                    <input type="checkbox"  name="quantity_enabled" data-switch="switch" data-on-label="'.__('Yes','lfb').'" data-off-label="'.__('No','lfb').'" />
                    <small> ' . __('Can the user select a quantity for this item ?', 'lfb') . ' </small>
                </div>';
        echo '<div id="efp_itemQuantity">';
        echo '<div class="form-group" >
                    <label> ' . __('Max quantity', 'lfb') . ' </label >
                    <input type="number" name="quantity_max" class="form-control" />
                    <small> ' . __('Sets the maximum quantity that can be selected', 'lfb') . ' </small>
                </div>';
        echo '<div class="form-group" >
                    <label> ' . __('Min quantity', 'lfb') . ' </label >
                    <input type="number" name="quantity_min" class="form-control" />
                    <small> ' . __('Sets the minimum quantity that can be selected', 'lfb') . ' </small>
                </div>';
        echo '<div class="form-group" >
                    <label> ' . __('Apply reductions on quantities ?', 'lfb') . ' </label >
                    <input type="checkbox"  name="reduc_enabled" data-switch="switch" data-on-label="'.__('Yes','lfb').'" data-off-label="'.__('No','lfb').'" />
                    <small> ' . __('Apply reductions on quantities ?', 'lfb') . ' </small>
                </div>';
        echo '<table id="lfb_itemPricesGrid" class="table">';
        echo '<thead>';
        echo '<tr>';
        echo '<th>' . __('If quantity > than', 'lfb') . '</th>';
        echo '<th>' . __('Item price becomes', 'lfb') . '</th>';
        echo '<th></th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        echo '<tr class="static">';
        echo '<td><input type="number" id="reduc_new_qt" value="" placeholder="' . __('Quantity', 'efb') . '"></td>';
        echo '<td><input type="number" id="reduc_new_price" value="" placeholder="' . __('New item price', 'efb') . '"></td>';
        echo '<td><a href="javascript:" onclick="lfb_add_reduc();" class="btn btn-default"><span class="glyphicon glyphicon-plus" style="margin-right:8px;"></span>' . __('Add a new reduction', 'efb') . '</a></td>';
        echo '</tr>';
        echo '</tbody>';
        echo '</table>';
        echo '</div>'; // eof efp_itemQuantity


        echo '</div>'; // eof col-md-6
        echo '<div class="col-md-12">';
        echo '<p ><a href="javascript:" class="btn btn-primary" onclick="lfb_saveItem();"><span class="glyphicon glyphicon-floppy-disk"></span>' . __('Save', 'lfb') . '</a></p>';
        echo '</div>'; // eof col-md-12
        echo '<div class="clearfix"></div>';

        echo '</div>'; // eof lfb_stepTabGeneral
        echo '</div>'; // eof tab-content
        echo '</div>'; // eof tabpanel

        echo '</div>'; // eof lfb_container
        echo '</div>'; // eof win item

        echo '<div id="lfb_winLog" class="modal fade ">
                         <div class="modal-dialog">
                           <div class="modal-content">
                             <div class="modal-body">
                             </div>
                             <div class="modal-footer" style="text-align: center;">
                                 <a href="javascript:" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span>' . __('Close', 'lfb') . '</a>
                             </div><!-- /.modal-footer -->
                           </div><!-- /.modal-content -->
                         </div><!-- /.modal-dialog -->
                       </div><!-- /.modal -->';

        echo '<div id="lfb_winShortcode" class="modal fade ">
                         <div class="modal-dialog">
                           <div class="modal-content">
                             <div class="modal-header">
                               <h4 class="modal-title">'.__('Shortcode','lfb').'</h4>
                             </div>
                             <div class="modal-body">
                                <p style="margin-bottom: 0px;"><strong>'.__('Integrate form in a page','lfb').':</strong></p>
                                <pre class="palette palette-silver" ><b>[estimation_form form_id="<span data-displayid="1">1</span>"]</b></pre>
                                <p style="margin-bottom: 0px;"><strong>'.__('To use in fullscreen','lfb').':</strong></p>
                                <pre class="palette palette-silver" ><b>[estimation_form form_id="<span data-displayid="1">1</span>" fullscreen="true"]</b></pre>
                                <p style="margin-bottom: 0px;"><strong>'.__('To use as popup','lfb').':</strong></p>
                                <pre class="palette palette-silver"><b>[estimation_form form_id="<span data-displayid="1">1</span>" popup="true"]</b></pre>
                                <p style="margin-bottom: 0px;">To open the popup, simply use the css class "<b>open-estimation-form form-<span data-displayid="1">1</span></b>".</p>
                                <pre class="palette palette-silver"><b>&lt;a href="#" class="open-estimation-form form-<span data-displayid="1">1</span>"&gt;Open Form&lt;/a&gt;</b></pre>
                             </div>
                             <div class="modal-footer" style="text-align: center;">
                                 <a href="javascript:" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span>' . __('Close', 'lfb') . '</a>
                             </div><!-- /.modal-footer -->
                           </div><!-- /.modal-content -->
                         </div><!-- /.modal-dialog -->
                       </div><!-- /.modal -->';

	    $dispS = '';
        if($settings->purchaseCode == ""){
          $dispS = 'true';
        }
         echo '<div id="lfb_winActivation" class="modal fade " data-show="'.$dispS.'" >
                          <div class="modal-dialog">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h4 class="modal-title">The license must be verified</h4>
                              </div>
                              <div class="modal-body">
                                <div id="lfb_iconLock"></div>
                                <p style="margin-bottom: 14px;">
                                	The license of this plugin isn\'t verified.<br/>Please fill the field below with your purchase code :
                                </p>
                                <div class="form-group">
                                	<input type="text" class="form-control" style="display:inline-block; width: 312px; margin-bottom: 4px" name="purchaseCode" placeholder="Enter your puchase code here"/>
                                	<a href="javascript:" onclick="lfb_checkLicense();" class="btn btn-primary"><span class="glyphicon glyphicon-check"></span>Verify</a>
                                	<br/>
                                	<span style="font-size:12px;"><a href="'.$this->parent->assets_url.'img/purchase_code_1200.png" target="_blank">Where I can find my purchase code ?</a></span>
                                </div>
                                <div class="alert alert-danger" style="font-size:12px;  margin-bottom: 0px;" >
                                	<span class="glyphicon glyphicon-warning-sign" style="margin-right: 12px;float: left;font-size: 22px;margin-top: 10px;margin-bottom: 10px;"></span>
                                  Each website using this plugin needs a legal license (1 license = 1 website). <br/>
                                  You can find more information on envato licenses <a href="http://codecanyon.net/licenses/standard" target="_blank">clicking here</a>.<br/>
                                     If you need to buy a new license of this plugin, <a href="http://codecanyon.net/item/wp-flat-estimation-payment-forms-/7818230?ref=loopus" target="_blank">click here</a>.
                                </div>
                              </div>
                              <div class="modal-footer" style="text-align: center;">
              								<a href="javascript:"  id="lfb_closeWinActivationBtn" class="btn btn-default disabled"><span class="glyphicon glyphicon-remove"></span><span class="lfb_text">Close</span></a>
              							  </div><!-- /.modal-footer -->
                            </div><!-- /.modal-content -->
                          </div><!-- /.modal-dialog -->
                        </div><!-- /.modal -->';

                echo '<div id="lfb_winImport" class="modal fade">
                          <div class="modal-dialog">
                            <div class="modal-content">
                              <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title">' . __('Import data', 'lfb') . '</h4>
                              </div>
                              <div class="modal-body">
                               <div class="alert alert-danger"><p>' . __('Be carreful : all existing forms and steps will be erased importing new data.', 'lfb') . '</p></div>
                                   <form id="lfb_winImportForm" method="post" enctype="multipart/form-data">
                                       <div class="form-group">
                                        <input type="hidden" name="action" value="lfb_importForms"/>
                                        <label>' . __('Select the .zip data file', 'lfb') . '</label><input name="importFile" type="file" class="" />
                                       </div>
                                  </form>
                              </div>
                              <div class="modal-footer">
                                <a href="javascript:" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span>' . __('Cancel', 'lfb') . '</a>
                                <a href="javascript:" class="btn btn-primary" onclick="lfb_importForms();"><span class="glyphicon glyphicon-floppy-disk"></span>' . __('Import', 'lfb') . '</a>
                            </div>
                            </div><!-- /.modal-content -->
                          </div><!-- /.modal-dialog -->
                        </div><!-- /.modal -->';


        echo '<div id="lfb_winExport" class="modal fade">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">' . __('Export data', 'lfb') . '</h4>
                      </div>
                      <div class="modal-body">
                        <p style="text-align: center;"><a href="' . $this->parent->assets_url . '../tmp/export_estimation_form.zip" target="_blank" onclick="jQuery(\'#lfb_winExport\').modal(\'hide\');" class="btn btn-primary btn-lg" id="lfb_exportLink"><span class="glyphicon glyphicon-floppy-disk"></span>' . __('Download the exported data', 'lfb') . '</a></p>
                      </div>
                    </div><!-- /.modal-content -->
                  </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->';

        echo ' </div><!-- /wpe_bootstraped -->';

    }


    /* Load Logs */
    function loadLogs(){
        global $wpdb;
      $formID = $_POST['formID'];
      $rep = "";
      $table_name = $wpdb->prefix . "wpefc_logs";
      $logs = $wpdb->get_results("SELECT * FROM $table_name WHERE formID=".$formID." ORDER BY id DESC");
      foreach ($logs as $log) {
        $formTitle = "";
        $rep .= '<tr>
                <td><a href="javascript:" onlick="lfb_loadLog('.$log->id.');">'.$log->ref.'</a></td>
                        <td>'.$log->email.'</td>
                    <td><a href="javascript:" onclick="lfb_loadLog(' . $log->id . ');" class="btn btn-primary" data-toggle="tooltip" title="'.__('View this order','lfb').'" data-placement="bottom"><span class="glyphicon glyphicon-search"></span></a>
                    <a href="javascript:" onclick="lfb_removeLog(' . $log->id . ');" class="btn btn-danger" data-toggle="tooltip" title="'.__('Delete this order','lfb').'" data-placement="bottom"><span class="glyphicon glyphicon-trash"></span></a></td>
          </tr>';
      }
      echo $rep;
      die();
    }

    /* Load Log */
    function loadLog(){
        global $wpdb;
      $logID = $_POST['logID'];
      $rep = "";
      $table_name = $wpdb->prefix . "wpefc_logs";
      $log = $wpdb->get_results("SELECT * FROM $table_name WHERE id=".$logID);
      if(count($log)>0){
        $log = $log[0];
        $rep = $log->content;
      }
      echo $rep;
    die();
    }

    /* Remove Log */
    function removeLog(){
        global $wpdb;
      $logID = $_POST['logID'];
      $table_name = $wpdb->prefix . "wpefc_logs";
      $wpdb->delete($table_name, array('id' => $logID));
      die();
    }


    /*
    * Load admin styles
    */
    function admin_styles()
    {
        if (isset($_GET['page']) && strpos($_GET['page'], 'lfb') !== false) {
            wp_register_style($this->parent->_token . ' - reset', esc_url($this->parent->assets_url) . 'css / reset.css', array(), $this->parent->_version);
            wp_enqueue_style('jquery - ui - datepicker - style', '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css');
            wp_register_style($this->parent->_token . '-bootstrap', esc_url($this->parent->assets_url) . 'css/bootstrap.min.css', array(), $this->parent->_version);
            wp_register_style($this->parent->_token . '-flat-ui', esc_url($this->parent->assets_url) . 'css/flat-ui_frontend.css', array(), $this->parent->_version);
            wp_register_style($this->parent->_token . '-colpick', esc_url($this->parent->assets_url) . 'css/colpick.css', array(), $this->parent->_version);
            wp_register_style($this->parent->_token . '-lfb-admin', esc_url($this->parent->assets_url) . 'css/lfb_admin.css', array(), $this->parent->_version);
            wp_enqueue_style($this->parent->_token . '-reset');
            wp_enqueue_style($this->parent->_token . '-bootstrap');
            wp_enqueue_style($this->parent->_token . '-flat-ui');
            wp_enqueue_style($this->parent->_token . '-colpick');
            wp_enqueue_style($this->parent->_token . '-lfb-admin');
        }
    }

    /*
     * Load admin scripts
     */
    function admin_scripts()
    {
        if (isset($_GET['page']) && strpos($_GET['page'], 'lfb') !== false) {
            wp_register_script($this->parent->_token . '-bootstrap', esc_url($this->parent->assets_url) . 'js/bootstrap.min.js', array('jquery', "jquery-ui-core"), $this->parent->_version);
            wp_enqueue_script($this->parent->_token . '-bootstrap');
           wp_register_script($this->parent->_token . '-bootstrap-switch', esc_url($this->parent->assets_url) . 'js/bootstrap-switch.js', array('jquery', "jquery-ui-core"), $this->parent->_version);
            wp_enqueue_script($this->parent->_token . '-bootstrap-switch');
            wp_register_script($this->parent->_token . '-colpick', esc_url($this->parent->assets_url) . 'js/colpick.js', array('jquery'), $this->parent->_version);
            wp_enqueue_script($this->parent->_token . '-colpick');
            wp_enqueue_script('tiny_mce');
            wp_register_script($this->parent->_token . '-lfb-admin', esc_url($this->parent->assets_url) . 'js/lfb_admin.min.js', array("jquery-ui-draggable", "jquery-ui-droppable", "jquery-ui-resizable", "jquery-ui-sortable", "jquery-ui-datepicker",$this->parent->_token . '-bootstrap-switch',), $this->parent->_version);
            wp_enqueue_script($this->parent->_token . '-lfb-admin');

            $js_data[] = array(
                'assetsUrl' => esc_url($this->parent->assets_url),
                'websiteUrl' => esc_url(get_home_url()),
                'texts' => array(
                    'tip_flagStep' => __('Click the flag icon to set this step at first step', 'lfb'),
                    'tip_linkStep' => __('Start a link to another step', 'lfb'),
                    'tip_delStep' => __('Remove this step', 'lfb'),
                    'tip_duplicateStep'=> __('Duplicate this step', 'lfb'),
                    'tip_editStep' => __('Edit this step', 'lfb'),
                    'tip_editLink' => __('Edit a link', 'lfb'),
                    'isSelected' => __('Is selected', 'lfb'),
                    'isUnselected' => __('Is unselected', 'lfb'),
                    'isSuperior' => __('Is superior to', 'lfb'),
                    'isInferior' => __('Is inferior to', 'lfb'),
                    'isEqual' => __('Is equal to', 'lfb'),
                    'isQuantitySuperior' => __('Quantity selected is superior to', 'lfb'),
                    'isQuantityInferior' => __('Quantity selected is inferior to', 'lfb'),
                    'isQuantityEqual' => __('Quantity is equal to', 'lfb'),
                    'totalPrice' => __('Total price', 'lfb'),
                    'isFilled' => __('Is Filled', 'lfb'),
                    'errorExport' => __('An error occurred during the exportation. Please verify that your server supports the ZipArchive php library ', 'lfb'),
                    'errorImport' => __('An error occurred during the importation. Please verify that your server supports the ZipArchive php library ', 'lfb'),
                    'Yes' => __('Yes', 'lfb'),
                    'No' => __('No', 'lfb')
                )
            );
            wp_localize_script($this->parent->_token . '-lfb-admin', 'lfb_data', $js_data);
        }
    }

    private function jsonRemoveUnicodeSequences($struct)
    {
      return json_encode($struct);
    }

    public function addForm()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "wpefc_forms";
        $wpdb->insert($table_name, array('title' => 'My new Form', 'btn_step' => "NEXT STEP", 'previous_step' => "return to previous step", 'intro_title' => "HOW MUCH TO MAKE MY WEBSITE ?", 'intro_text' => "Estimate the cost of a website easily using this awesome tool.", 'intro_btn' => "GET STARTED", 'last_title' => "Final cost", 'last_text' => "The final estimated price is : ", 'last_btn' => "ORDER MY WEBSITE", 'last_msg_label' => "Do you want to write a message ? ", 'succeed_text' => "Thanks, we will contact you soon", 'initial_price' => 0, 'email' => 'loopus_web@hotmail.fr', 'email_subject' => 'New order from your website', 'currency' => '$','currencyPosition'=>'left', 'errorMessage' => 'You need to select an item to continue', 'intro_enabled' => 1, 'email_userSubject' => 'Order confirmation',
            'email_adminContent' => '<p>Ref: <strong>[ref]</strong></p><h2 style="color: #008080;">Information</h2><hr/><span style="font-weight: 600; color: #444444;">[information_content]</span><span style="color: #444444;"> </span><hr/><h2 style="color: #008080;">Project</h2><hr/>[project_content]<hr/><h4>Total: <strong><span style="color: #444444;">[total_price]</span></strong></h4>',
            'email_userContent' => '<p>Ref: <strong>[ref]</strong></p><h2 style="color: #008080;">Information</h2><hr/><span style="font-weight: 600; color: #444444;">[information_content]</span><span style="color: #444444;"> </span><hr/><h2 style="color: #008080;">Project</h2><hr/>[project_content]<hr/><h4>Total: <strong><span style="color: #444444;">[total_price]</span></strong></h4>',
            'colorA' => '#1abc9c', 'colorB' => '#34495e', 'colorC' => '#bdc3c7', 'item_pictures_size' => 64));

        $formID = $wpdb->insert_id;

        $table_name = $wpdb->prefix . "wpefc_fields";
        $wpdb->insert($table_name, array('formID' => $formID, 'label' => "Enter your email", 'isRequired' => 1, 'typefield' => 'input', 'visibility' => 'display', 'validation' => 'email'));
        $wpdb->insert($table_name, array('formID' => $formID, 'label' => "Do you want to write a message ?", 'isRequired' => 0, 'typefield' => 'textarea', 'visibility' => 'toggle'));

        echo $formID;
        die();
    }

    public function duplicateStep(){
      global $wpdb;
      $table_name = $wpdb->prefix . "wpefc_steps";
      $stepID = $_POST['stepID'];
      $steps = $wpdb->get_results("SELECT * FROM $table_name WHERE id=".$stepID);
      $step = $steps[0];
      $step->title = $step->title . ' (1)';
      $step->start = 0;
      unset($step->id);

      $content = json_decode($step->content);
      $content->previewPosX += 40;
      $content->previewPosY += 40;
      $content->start = 0;
      $step->content =  stripslashes($this->jsonRemoveUnicodeSequences($content));

      //$wpdb->insert($table_name, array('content' => $this->jsonRemoveUnicodeSequences($content), 'start' => 0,'title'=>$step->title,'itemRequired'=>$step->itemRequired ));
      $wpdb->insert($table_name, (array)$step);
      $newID = $wpdb->insert_id;

      $table_name = $wpdb->prefix . "wpefc_items";
      $items = $wpdb->get_results("SELECT * FROM $table_name WHERE stepID=$stepID");
      foreach($items as $item){
        $item->stepID = $newID;
        unset($item->id);
        $wpdb->insert($table_name, (array)$item);
      }
      die();

    }

    public function duplicateItem(){
        global $wpdb;
        $table_name = $wpdb->prefix . "wpefc_items";
        $itemID = $_POST['itemID'];
        $items = $wpdb->get_results("SELECT * FROM $table_name WHERE id=".$itemID);
        $item = $items[0];
        $item->title = $item->title . ' (1)';
        unset($item->id);
        $wpdb->insert($table_name, (array)$item);
        die();
    }

    /*
     * Check for  updates
     */
     function checkAutomaticUpdates(){
       $settings = $this->getSettings();
       if($settings && $settings->purchaseCode != ""){
         require_once('wp-updates-plugin.php');
         new WPUpdatesPluginUpdater_1141( 'http://wp-updates.com/api/2/plugin', 'WP_Estimation_Form/estimation-form.php');
       }
     }

    public function duplicateForm(){
        global $wpdb;
        $table_name = $wpdb->prefix . "wpefc_forms";
        $formID = $_POST['formID'];

        $table_forms = $wpdb->prefix . "wpefc_forms";
        $table_steps = $wpdb->prefix . "wpefc_steps";
        $table_items = $wpdb->prefix . "wpefc_items";
        $table_fields = $wpdb->prefix . "wpefc_fields";
        $table_links = $wpdb->prefix . "wpefc_links";
        $forms = $wpdb->get_results("SELECT * FROM $table_forms WHERE id=$formID LIMIT 1");
        $form = $forms[0];
        unset($form->id);
        $form->title = $form->title . ' (1)';
        $wpdb->insert($table_forms, (array)$form);
        $newFormID = $wpdb->insert_id;
        $fields = $wpdb->get_results("SELECT * FROM $table_fields WHERE formID=$formID");
        foreach ($fields as $field) {
            unset($field->id);
            $field->formID = $newFormID;
            $wpdb->insert($table_fields, (array) $field);
        }
        $stepsReplacement = array();
        $itemsReplacement = array();

        $steps = $wpdb->get_results("SELECT * FROM $table_steps WHERE formID=$formID");
        foreach ($steps as $step) {
            $step->formID = $newFormID;
            $stepID = $step->id;
            unset($step->id);
            $wpdb->insert($table_steps, (array)$step);
            $newStepID = $wpdb->insert_id;
            $stepsReplacement[$stepID] = $newStepID;
          //  array_push($stepsReplacement,$stepID,$newStepID);

        /*    $links = $wpdb->get_results("SELECT * FROM $table_links WHERE originID=$stepID");
            foreach ($links as $link) {
              unset($link->id);
              $link->originID = $newStepID;
              $link->formID = $newFormID;

              $wpdb->insert($table_links, (array)$link);
            }
            $links = $wpdb->get_results("SELECT * FROM $table_links WHERE destinationID=$stepID");
            foreach ($links as $link) {
              unset($link->id);
              $link->destinationID = $newStepID;
              $link->formID = $newFormID;
              $wpdb->insert($table_links, (array)$link);
            }*/

            $items = $wpdb->get_results("SELECT * FROM $table_items WHERE stepID=$stepID");
            foreach ($items as $item) {
              $itemID = $item->id;
                unset($item->id);
                $item->stepID = $newStepID;
                $item->formID = $newFormID;
                $wpdb->insert($table_items, (array)$item);
                $newItemID = $wpdb->insert_id;

                $itemsReplacement[$itemID] = $newItemID;
              //  array_push($itemsReplacement,$itemID,$newItemID);
            }
        }
        $links = $wpdb->get_results("SELECT * FROM $table_links WHERE formID=$formID");
        foreach ($links as $link) {
          unset($link->id);
          $link->originID = $stepsReplacement[$link->originID];
          $link->destinationID = $stepsReplacement[$link->destinationID];
          $link->formID = $newFormID;

          $conditions = json_decode($link->conditions);
        //  print_r($conditions);
          foreach ($conditions as $condition) {
            $oldStep = substr($condition->interaction,0,strpos($condition->interaction,'_'));
            $oldItem= substr($condition->interaction,strpos($condition->interaction,'_')+1);
            $condition->interaction = $stepsReplacement[$oldStep].'_'.$itemsReplacement[$oldItem];
            echo $oldStep.'/'.$oldItem.' -- ';
            echo $condition->interaction.' ** '."\n";
          }
          $wpdb->insert($table_links, array('conditions' => $this->jsonRemoveUnicodeSequences($conditions),'originID'=>$link->originID,'destinationID'=>$link->destinationID,'formID'=>$newFormID));
        }


        die();
    }

    public function saveForm()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "wpefc_forms";
        $formID = $_POST['formID'];
        $sqlDatas = array();
        foreach ($_POST as $key => $value) {
            if ($key != 'action' && $key != 'id' && $key != 'pll_ajax_backend'&& $key != "undefined"&& $key != "formID") {
                if($key == 'email_adminContent'){
                    $value = str_replace("../wp-content/", get_home_url().'/wp-content/',$value);
                        $value = str_replace("../", get_home_url().'/',$value);
                }
                    if($key == 'email_userContent'){
                        $value = str_replace("../wp-content/", get_home_url().'/wp-content/',$value);
                        $value = str_replace("../", get_home_url().'/',$value);
                    }
                $sqlDatas[$key] = stripslashes($value);
            }
        }
        if ($formID > 0) {
            $wpdb->update($table_name, $sqlDatas, array('id' => $formID));
            $response = $formID;
        } else {
            if (isset($_POST['title'])) {
                $wpdb->insert($table_name, $sqlDatas);
                $lastid = $wpdb->insert_id;
                $response = $lastid;
            }
        }
        echo $response;
        die();
    }

    public function removeForm()
    {
        global $wpdb;
        $formID = $_POST['formID'];
        $table_name = $wpdb->prefix . "wpefc_forms";
        $wpdb->delete($table_name, array('id' => $formID));
        $table_name = $wpdb->prefix . "wpefc_fields";
        $wpdb->delete($table_name, array('formID' => $formID));
          $table_name = $wpdb->prefix . "wpefc_items";
          $wpdb->delete($table_name, array('formID' => $formID));
        die();
    }

    public function checkFields(){
        global $wpdb;
        $table_name = $wpdb->prefix . "wpefc_forms";
        $forms = $wpdb->get_results("SELECT * FROM $table_name");
        foreach ($forms as $form) {
          $table_nameF = $wpdb->prefix . "wpefc_fields";
          $fields = $wpdb->get_results("SELECT * FROM $table_nameF WHERE formID=".$form->id);
          $chk = false;
          foreach ($fields as $field) {
            if($field->type == 'input' && $field->validation == "email"){
              $chk = true;
            }
          }
          if(!$chk){
            $wpdb->insert($table_nameF, array('formID' => $form->id, 'validation' => "email", 'typefield' => "input", 'label' => "Email",'isRequired'=>1));
          }

        }
    }


    public function checkLicense(){
      global $wpdb;
      try {
          $url = 'http://www.loopus-plugins.com/updates/update.php?checkCode=7818230&code=' . $_POST['code'];
          $ch = curl_init($url);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          $rep = curl_exec($ch);
          if ($rep != '0410') {
            $table_name = $wpdb->prefix . "wpefc_settings";
            $wpdb->update($table_name, array('purchaseCode' => $_POST['code']), array('id' => 1));
          } else {
            echo '1';
          }
      } catch (Exception $e) {
          $table_name = $wpdb->prefix . "wpefc_settings";
          $wpdb->update($table_name, array('purchaseCode' => $_POST['code']), array('id' => 1));
      }
      die();
    }

    public
    function loadSettings()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "wpefc_settings";
        $settings = $wpdb->get_results("SELECT * FROM $table_name WHERE id=1 LIMIT 1");
        $settings = $settings[0];
        echo json_encode($settings);
        die();
    }

    public function saveStepPosition()
    {
        global $wpdb;
        $stepID = $_POST['stepID'];
        $posX = $_POST['posX'];
        $posY = $_POST['posY'];
        $table_name = $wpdb->prefix . "wpefc_steps";
        $step = $wpdb->get_results("SELECT * FROM $table_name WHERE id=" . $stepID . ' LIMIT 1');
        $step = $step[0];
        $content = json_decode($step->content);
        $content->previewPosX = $posX;
        $content->previewPosY = $posY;

        $wpdb->update($table_name, array('content' => stripslashes($this->jsonRemoveUnicodeSequences($content))), array('id' => $stepID));
        die();

    }

    public function newLink()
    {
        global $wpdb;
        $formID = $_POST['formID'];
        $originID = $_POST['originStepID'];
        $destinationID = $_POST['destinationStepID'];
        $table_name = $wpdb->prefix . "wpefc_links";
        $wpdb->insert($table_name, array('originID' => $originID, 'destinationID' => $destinationID, 'conditions' => '[]', 'formID' => $formID));
        echo $wpdb->insert_id;
        die();
    }


    public function loadForm()
    {
        global $wpdb;
        $formID = $_POST['formID'];
        $rep = new stdClass();
        $rep->steps = array();

        $table_name = $wpdb->prefix . "wpefc_forms";
        $forms = $wpdb->get_results("SELECT * FROM $table_name WHERE id=" . $formID);
        $rep->form = $forms[0];

        $table_name = $wpdb->prefix . "wpefc_settings";
        $params = $wpdb->get_results("SELECT * FROM $table_name");
        $rep->params = $params[0];

        $table_name = $wpdb->prefix . "wpefc_steps";
        $steps = $wpdb->get_results("SELECT * FROM $table_name WHERE formID=" . $formID);
        foreach ($steps as $step) {
            $table_name = $wpdb->prefix . "wpefc_items";
            $items = $wpdb->get_results("SELECT * FROM $table_name WHERE stepID=" . $step->id . " ORDER BY ordersort ASC");
            $step->items = $items;
            $rep->steps[] = $step;
        }

        $table_name = $wpdb->prefix . "wpefc_links";
        $links = $wpdb->get_results("SELECT * FROM $table_name WHERE formID=" . $formID);
        $rep->links = $links;

        $table_name = $wpdb->prefix . "wpefc_fields";
        $fields = $wpdb->get_results("SELECT * FROM $table_name WHERE formID=" . $formID);
        $rep->fields = $fields;


        echo($this->jsonRemoveUnicodeSequences($rep));
        die();
    }

    public  function loadFields()
    {
        global $wpdb;
        $formID = $_POST['formID'];
        $table_name = $wpdb->prefix . "wpefc_fields";
        $fields = $wpdb->get_results("SELECT * FROM $table_name WHERE formID=" . $formID . " ORDER BY ordersort ASC");
        echo($this->jsonRemoveUnicodeSequences($fields));
        die();
    }

    public function removeField(){
          global $wpdb;
          $table_name = $wpdb->prefix . "wpefc_fields";
          $fieldID = $_POST['fieldID'];
          $wpdb->delete($table_name, array('id' => $fieldID));
          die();
    }

    public  function saveField()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "wpefc_fields";
        $fieldID = $_POST['id'];
        $formID = $_POST['formID'];
        $sqlDatas = array();
        foreach ($_POST as $key => $value) {
            if ($key != 'action' && $key != 'id' && $key != 'pll_ajax_backend') {
                $sqlDatas[$key] = stripslashes($value);
            }
        }
        if ($fieldID > 0) {
            $wpdb->update($table_name, $sqlDatas, array('id' => $fieldID));
            $response = $_POST['id'];
        } else {
            $sqlDatas['formID'] = $formID;
            $wpdb->insert($table_name, $sqlDatas);
            $lastid = $wpdb->insert_id;
            $response = $lastid;
        }
        echo $response;
        die();
    }

    public  function removeAllSteps()
    {
        global $wpdb;
        $formID = $_POST['formID'];

        $table_name = $wpdb->prefix . "wpefc_steps";
        $steps = $wpdb->get_results("SELECT * FROM $table_name WHERE formID=" . $formID);
        foreach($steps as $step) {
          $table_nameL = $wpdb->prefix . "wpefc_links";
          $wpdb->delete($table_nameL, array('originID' => $step->id));
          $wpdb->delete($table_nameL, array('destinationID' => $step->id));
        }

        $wpdb->delete($table_name, array('formID' => $formID));
        die();
    }


    public function removeItem()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "wpefc_items";
        $wpdb->delete($table_name, array('id' => $_POST['itemID']));

        die();
    }

    public function removeStep()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "wpefc_steps";

        $wpdb->delete($table_name, array('id' => $_POST['stepID']));
        $table_name = $wpdb->prefix . "wpefc_links";
        $wpdb->delete($table_name, array('originID' => $_POST['stepID']));
        $wpdb->delete($table_name, array('destinationID' => $_POST['stepID']));

        die();
    }

    public  function addStep()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "wpefc_steps";
        $formID = $_POST['formID'];

        $data = new stdClass();
        $data->start = $_POST['start'];

        $stepsStart = $wpdb->get_results("SELECT * FROM $table_name WHERE formID=".$formID." AND start=1");
        if(count($stepsStart) == 0){
          $data->start = 1;
        }

        if($data->start == 1){
          $steps = $wpdb->get_results("SELECT * FROM $table_name WHERE formID=".$formID." AND start=1");
          foreach($steps as $step){
            $dataContent = json_decode($step->content);
            $dataContent->start = 0;
            $wpdb->update($table_name, array('content' => $this->jsonRemoveUnicodeSequences($dataContent), 'start' => 0), array('id' => $data->id));
          }
        }
        $data->previewPosX = $_POST['previewPosX'];
        $data->previewPosY = $_POST['previewPosY'];
        $data->actions = array();



        $wpdb->insert($table_name, array('content' => $this->jsonRemoveUnicodeSequences($data), 'title' => __('My Step', 'lfb'), 'formID' => $formID,'start'=>$data->start));
        $data->id = $wpdb->insert_id;
        $wpdb->update($table_name, array('content' => $this->jsonRemoveUnicodeSequences($data), 'formID' => $formID), array('id' => $data->id));
        echo json_encode((array)$data);
        die();
    }

    public function loadStep()
    {
        global $wpdb;
        $rep = new stdClass();
        $table_name = $wpdb->prefix . "wpefc_steps";
        $step = $wpdb->get_results("SELECT * FROM $table_name WHERE id='" . $_POST['stepID'] . "' LIMIT 1");
        $rep->step = $step[0];
        $table_name = $wpdb->prefix . "wpefc_items";
        $items = $wpdb->get_results("SELECT * FROM $table_name WHERE stepID='" . $_POST['stepID'] . "' ORDER BY ordersort ASC");
        $rep->items = $items;
        echo $this->jsonRemoveUnicodeSequences((array)$rep);
        die();
    }


    public function saveItem()
    {
        global $wpdb;
        $formID = $_POST['formID'];
        $stepID = $_POST['stepID'];
        $itemID = $_POST['id'];


        $table_name = $wpdb->prefix . "wpefc_items";

        $sqlDatas = array();
        foreach ($_POST as $key => $value) {
            if ($key != 'action' && $key != 'id' && $key != 'pll_ajax_backend') {
                $sqlDatas[$key] = stripslashes($value);
            }
        }
        if ($itemID > 0) {
            $wpdb->update($table_name, $sqlDatas, array('id' => $itemID));
            $response = $_POST['id'];
        } else {
            $sqlDatas['formID'] = $formID;
            $sqlDatas['stepID'] = $stepID;
            $wpdb->insert($table_name, $sqlDatas);
            $itemID = $wpdb->insert_id;
        }
        echo $itemID;
        die();

    }


    public function saveStep()
    {
        global $wpdb;
        $formID = $_POST['formID'];
        $stepID = $_POST['id'];
        $table_name = $wpdb->prefix . "wpefc_steps";

        $sqlDatas = array();
        foreach ($_POST as $key => $value) {
            if ($key != 'action' && $key != 'id' && $key != 'pll_ajax_backend') {
                $sqlDatas[$key] = stripslashes($value);
            }
        }

        if ($stepID > 0) {
            $wpdb->update($table_name, $sqlDatas, array('id' => $stepID));
            $response = $_POST['id'];

        } else {
            $sqlDatas['formID'] = $formID;
            $wpdb->insert($table_name, $sqlDatas);
            $stepID = $wpdb->insert_id;
        }
        echo $stepID;
        die();

    }

    public
    function changePreviewHeight()
    {
        global $wpdb;
        $height = $_POST['height'];
        $table_name = $wpdb->prefix . "wpefc_settings";
        $wpdb->update($table_name, array('previewHeight' => $height), array('id' => 1));
        die();
    }

    public
    function saveLinks()
    {
        global $wpdb;
        $formID = $_POST['formID'];
        $table_name = $wpdb->prefix . "wpefc_links";
        $wpdb->query("DELETE FROM $table_name WHERE formID=" . $formID . " AND id>0");

        $links = json_decode(stripslashes($_POST['links']));
        foreach ($links as $link) {
            if ($link->destinationID > 0) {
                $wpdb->insert($table_name, array('formID' => $formID, 'originID' => $link->originID, 'destinationID' => $link->destinationID, 'conditions' => $this->jsonRemoveUnicodeSequences($link->conditions)));
            }
        }
        echo '1';
        die();

    }

    public function importForms()
    {
        global $wpdb;
        $displayForm = true;
        $settings = $this->getSettings();
        $code = $settings->purchaseCode;
        //$pageID = $settings->form_page_id;
        if (isset($_FILES['importFile'])) {
            $error = false;
            if (!is_dir(plugin_dir_path(__FILE__) . '../tmp')) {
                mkdir(plugin_dir_path(__FILE__) . '../tmp');
                chmod(plugin_dir_path(__FILE__) . '../tmp', 0747);
            }
            $target_path = plugin_dir_path(__FILE__) . '../tmp/export_estimation_form.zip';
            if (@move_uploaded_file($_FILES['importFile']['tmp_name'], $target_path)) {


                $upload_dir = wp_upload_dir();
                if (!is_dir($upload_dir['path'])) {
                    mkdir($upload_dir['path']);
                }

                $zip = new ZipArchive;
                $res = $zip->open($target_path);
                if ($res === TRUE) {
                    $zip->extractTo(plugin_dir_path(__FILE__) . '../tmp/');
                    $zip->close();

                    $formsData = array();

                    $jsonfilename = 'export_estimation_form.json';
                    if (!file_exists(plugin_dir_path(__FILE__) . '../tmp/export_estimation_form.json')) {
                        $jsonfilename = 'export_estimation_form';
                    }

                    $file = file_get_contents(plugin_dir_path(__FILE__) . '../tmp/' . $jsonfilename);
                    $dataJson = json_decode($file, true);

                    $table_name = $wpdb->prefix . "wpefc_settings";
                    $wpdb->query("TRUNCATE TABLE $table_name");
                    $value = $dataJson['settings'][0];
                    if (array_key_exists('intro_title', $value)) {
                        foreach ($value as $keyV => $valueV) {
                            if ($keyV != 'colorA' && $keyV != 'colorB' && $keyV != 'colorC' && $keyV != 'item_pictures_size') {
                                $formsData[$keyV] = $valueV;
                            }
                        }
                    }
                    if (!array_key_exists('colorC', $value)) {
                        $value['colorC'] = '#bdc3c7';
                    }
                    $previewHeight = 300;
                    if (isset($value['previewHeight']) && $value['previewHeight'] > 0){
                    	$previewHeight = $value['previewHeight'];
                    }

                    $wpdb->insert($table_name, array('previewHeight' => $previewHeight,'purchaseCode'=>$code));

                    $table_name = $wpdb->prefix . "wpefc_forms";
                    $wpdb->query("TRUNCATE TABLE $table_name");
                    if (array_key_exists('forms', $dataJson)) {
                        foreach ($dataJson['forms'] as $key => $value) {
                            if (!array_key_exists('email_adminContent', $value)) {
                                $value['email_adminContent'] = '<p>Ref: <strong>[ref]</strong></p><h2 style="color: #008080;">Information</h2><hr/><span style="font-weight: 600; color: #444444;">[information_content]</span><span style="color: #444444;"> </span><hr/><h2 style="color: #008080;">Project</h2><hr/>[project_content]<hr/><h4>Total: <strong><span style="color: #444444;">[total_price]</span></strong></h4>';
                                $value['email_userContent'] = '<p>Ref: <strong>[ref]</strong></p><h2 style="color: #008080;">Information</h2><hr/><span style="font-weight: 600; color: #444444;">[information_content]</span><span style="color: #444444;"> </span><hr/><h2 style="color: #008080;">Project</h2><hr/>[project_content]<hr/><h4>Total: <strong><span style="color: #444444;">[total_price]</span></strong></h4>';

                            }
            							if(array_key_exists('form_page_id', $value)) {
            								unset($value['form_page_id']);
            							}
                            $wpdb->insert($table_name, $value);
                        }
                    }

                    $table_name = $wpdb->prefix . "wpefc_steps";
                    $wpdb->query("TRUNCATE TABLE $table_name");
                    $prevPosX = 40;
                    $firstStep = false;
                    foreach ($dataJson['steps'] as $key => $value) {
                        if (!array_key_exists('formID', $value)) {
                            $value['formID'] = 1;
                        }
                        if (!array_key_exists('content', $value)) {
                        	$start = 0;
                        	if(!$firstStep && $value['ordersort'] == 0){
                        		$start = 1;
                        		$value['start'] = 1;
                            $firstStep = true;
                        	}
                        	$value['content'] = '{"start":"'.$start.'","previewPosX":"'.$prevPosX.'","previewPosY":"140","actions":[],"id":'.$value['id'].'}';
                        	$prevPosX += 200;
                        }
                        $wpdb->insert($table_name, $value);
                    }

                    $table_name = $wpdb->prefix . "wpefc_fields";
                    $wpdb->query("TRUNCATE TABLE $table_name");
                    if (array_key_exists('fields', $dataJson)) {
                        foreach ($dataJson['fields'] as $key => $value) {
                            if (!array_key_exists('validation', $value) && $value['id'] == '1') {
                                $value['validation'] = 'email';
                            }
                            if(array_key_exists('height', $value)) {
                            	unset($value['height']);
                            }
                            $wpdb->insert($table_name, $value);
                        }
                    }

                    $table_name = $wpdb->prefix . "wpefc_links";
                    $wpdb->query("TRUNCATE TABLE $table_name");
                    if (array_key_exists('links', $dataJson)) {
                        foreach ($dataJson['links'] as $key => $value) {
                            $wpdb->insert($table_name, $value);
                        }
                    }

                    $table_name = $wpdb->prefix . "wpefc_logs";
                    $wpdb->query("TRUNCATE TABLE $table_name");
                    if (array_key_exists('logs', $dataJson)) {
                        foreach ($dataJson['logs'] as $key => $value) {
                            $wpdb->insert($table_name, $value);
                        }
                    }

                    // Check links
                    $table_name = $wpdb->prefix . "wpefc_forms";
                    $forms = $wpdb->get_results("SELECT * FROM $table_name");
                    foreach ($forms as $form) {
                    	$table_name = $wpdb->prefix . "wpefc_links";
                    	$links = $wpdb->get_results("SELECT * FROM $table_name WHERE formID=".$form->id);
                    	if(count($links) ==0){

	                    	$stepStartID = 0;
	                    	$stepStart = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix . "wpefc_steps WHERE start=1 AND formID=".$form->id);
	                    	if(count($stepStart)>0){
	                    		$stepStart = $stepStart[0];
	                    		$stepStartID = $stepStart->id;
	                    	}
	                    	$steps = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix . "wpefc_steps WHERE formID=".$form->id." AND start=0 ORDER BY ordersort ASC, id ASC");
	                    	$i = 0;
	                    	$prevStepID = 0;
	                    	foreach ($steps as $step) {
	                    		if ($i ==0 && $stepStartID >0){
	                    			$wpdb->insert($wpdb->prefix . "wpefc_links", array('originID'=>$stepStartID,'destinationID'=>$step->id,'formID'=>$form->id,'conditions'=>'[]'));
	                    		} else if ($i >0 && $prevStepID > 0){
	                    			$wpdb->insert($wpdb->prefix . "wpefc_links", array('originID'=>$prevStepID,'destinationID'=>$step->id,'formID'=>$form->id,'conditions'=>'[]'));
	                    		}
	                    		$prevStepID = $step->id;
	                    		$i++;
	                    	}

                    	}

                    }



                    $table_name = $wpdb->prefix . "wpefc_items";
                    $wpdb->query("TRUNCATE TABLE $table_name");
                    foreach ($dataJson['items'] as $key => $value) {

                        if ($value['image'] && $value['image'] != "") {
                            $img_name = substr($value['image'], strrpos($value['image'], '/') + 1);
                            $imagePath = substr($value['image'], 0, strrpos($value['image'], '/'));
                            if (!file_exists(site_url() . '/' . $value['image'])) {
                                if (!is_dir($imagePath)) {
                                    $imagePath = wp_upload_dir();
                                    // mkdir($imagePath, 0747, true);
                                }
                                if (strrpos($value['image'], "uploads") === false) {
                                    $value['image'] = 'uploads' . $value['image'];
                                }
                                if (is_file(plugin_dir_path(__FILE__) . '../tmp/' . $img_name)) {
                                    copy(plugin_dir_path(__FILE__) . '../tmp/' . $img_name, $imagePath['basedir'] . $imagePath['subdir'] . '/' . $img_name);
                                }
                            }
                            $value['image'] = $imagePath['url'] . '/' . $img_name;
                        }
                        if (array_key_exists('reduc_qt', $value)) {
                            unset($value['reduc_qt']);
                            unset($value['reduc_value']);
                        }

                        $wpdb->insert($table_name, $value);
                    }


                    // check if form exists
                    $table_name = $wpdb->prefix . "wpefc_forms";
                    $forms = $wpdb->get_results("SELECT * FROM $table_name LIMIT 1");
                    if (!$forms || count($forms) == 0) {
                        $formsData['title'] = 'My Estimation Form';
                        $wpdb->insert($table_name, $formsData);
                    }


                    $files = glob(plugin_dir_path(__FILE__) . '../tmp/*');
                    foreach ($files as $file) {
                        if (is_file($file))
                            unlink($file);
                    }

                } else {
                    $error = true;
                }
            } else {
                $error = true;
            }
            if ($error) {
                echo __('An error occurred during the transfer', 'lfb');
                die();
            } else {
                $displayForm = false;
                echo 1;
                die();
            }
        }
    }

    public function exportForms()
    {
        global $wpdb;
        if (!is_dir(plugin_dir_path(__FILE__) . '../tmp')) {
            mkdir(plugin_dir_path(__FILE__) . '../tmp');
            chmod(plugin_dir_path(__FILE__) . '../tmp', 0747);
        }

        $destination = plugin_dir_path(__FILE__) . '../tmp/export_estimation_form.zip';
        if (file_exists($destination)) {
            unlink($destination);
        }
        $zip = new ZipArchive();
        if ($zip->open($destination, ZipArchive::CREATE) !== true) {
            return false;
        }

        $jsonExport = array();
        $table_name = $wpdb->prefix . "wpefc_settings";
        $settings = $this->getSettings();
        $settings->purchaseCode = "";
        $jsonExport['settings'] = array();
        $jsonExport['settings'][] = $settings;


        $table_name = $wpdb->prefix . "wpefc_forms";
        $forms = array();
        foreach ($wpdb->get_results("SELECT * FROM $table_name") as $key => $row) {
            $forms[] = $row;
        }
        $jsonExport['forms'] = $forms;

        $table_name = $wpdb->prefix . "wpefc_logs";
        $logs = array();
        foreach ($wpdb->get_results("SELECT * FROM $table_name") as $key => $row) {
          $logs[] = $row;
        }
        $jsonExport['logs'] = $logs;

        $table_name = $wpdb->prefix . "wpefc_steps";
        $steps = array();
        foreach ($wpdb->get_results("SELECT * FROM $table_name") as $key => $row) {
            $steps[] = $row;
        }
        $jsonExport['steps'] = $steps;

        $table_name = $wpdb->prefix . "wpefc_fields";
        $steps = array();
        foreach ($wpdb->get_results("SELECT * FROM $table_name") as $key => $row) {
            $steps[] = $row;
        }
        $jsonExport['fields'] = $steps;

        $table_name = $wpdb->prefix . "wpefc_links";
        $steps = array();
        foreach ($wpdb->get_results("SELECT * FROM $table_name") as $key => $row) {
            $steps[] = $row;
        }
        $jsonExport['links'] = $steps;


        $table_name = $wpdb->prefix . "wpefc_items";
        $items = array();
        foreach ($wpdb->get_results("SELECT * FROM $table_name") as $key => $row) {
            $items[] = $row;
            if ($row->image != "") {
                $original_image = $row->image;
                $upload_dir = wp_upload_dir();
                $pos1 = strrpos($original_image, '/');
                $pos2 = strrpos($row->image, '/', 0 - (strlen($row->image) - $pos1) - 1);
                $pos3 = strrpos($row->image, '/', 0 - (strlen($row->image) - $pos2) - 1);
                $row->image = substr($row->image, strlen(site_url()) + 1);
                if (strrpos($row->image, "wp-content") > -1) {
                    $row->image = substr($row->image, strrpos($row->image, "wp-content") + 11);
                }
                if (substr($row->image, 0, 17) == '/uploads/uploads/') {
                    $row->image = substr($row->image, 9);
                }
                $zip->addfile($this->dir . "/../../" . $row->image, substr($original_image, $pos1 + 1));
            }
        }

        $jsonExport['items'] = $items;
        $fp = fopen(plugin_dir_path(__FILE__) . '../tmp/export_estimation_form.json', 'w');
        fwrite($fp, json_encode($jsonExport));
        fclose($fp);

        $zip->addfile(plugin_dir_path(__FILE__) . '../tmp/export_estimation_form.json', 'export_estimation_form.json');
        $zip->close();
        echo '1';
        die();

    }


    /**
     * Main Instance
     *
     *
     * @since 1.0.0
     * @static
     * @return Main instance
     */
    public
    static function instance($parent)
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self($parent);
        }
        return self::$_instance;
    }

    // End instance()

    /**
     * Cloning is forbidden.
     *
     * @since 1.0.0
     */
    public function __clone()
    {
        _doing_it_wrong(__FUNCTION__, __(''), $this->parent->_version);
    }

// End __clone()

    /**
     * Unserializing instances of this class is forbidden.
     *
     * @since 1.0.0
     */
    public function __wakeup()
    {
        _doing_it_wrong(__FUNCTION__, __(''), $this->parent->_version);
    }

// End __wakeup()
}
