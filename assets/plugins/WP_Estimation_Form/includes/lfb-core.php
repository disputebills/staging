<?php

if (!defined('ABSPATH'))
    exit;

class LFB_Core
{

    /**
     * The single instance
     * @var    object
     * @access  private
     * @since    1.0.0
     */
    private static $_instance=null;

    /**
     * Settings class object
     * @var     object
     * @access  public
     * @since   1.0.0
     */
    public $settings=null;

    /**
     * The version number.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $_version;

    /**
     * The token.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $_token;

    /**
     * The main plugin file.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $file;

    /**
     * The main plugin directory.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $dir;

    /**
     * The plugin assets directory.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $assets_dir;

    /**
     * The plugin assets URL.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $assets_url;

    /**
     * Suffix for Javascripts.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $templates_url;

    /**
     * Suffix for Javascripts.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $script_suffix;

    /**
     * For menu instance
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $menu;

    /**
     * For template
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $plugin_slug;

    /*
     *  Current forms on page
     */
    public $currentForms;

    /*
     * Must load or not the js files ?
     */
    private $add_script;

    /**
     * Constructor function.
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function __construct($file='', $version='1.6.0')
    {
        $this->_version=$version;
        $this->_token='lfb';
        $this->plugin_slug='lfb';
        $this->currentForms=array();

        $this->file=$file;
        $this->dir=dirname($this->file);
        $this->assets_dir=trailingslashit($this->dir) . 'assets';
        $this->assets_url=esc_url(trailingslashit(plugins_url('/assets/', $this->file)));
        $this->templates_url=esc_url(trailingslashit(plugins_url('/templates/', $this->file)));

        add_shortcode('estimation_form', array($this, 'wpt_shortcode'));
        add_action('wp_ajax_nopriv_cart_save', array($this, 'cart_save'));
        add_action('wp_ajax_cart_save', array($this, 'cart_save'));
        add_action('wp_ajax_nopriv_send_email', array($this, 'send_email'));
        add_action('wp_ajax_send_email', array($this, 'send_email'));
        add_action('wp_ajax_nopriv_get_currentRef', array($this, 'get_currentRef'));
        add_action('wp_ajax_get_currentRef', array($this, 'get_currentRef'));

        add_action('wp_enqueue_scripts', array($this, 'frontend_enqueue_scripts'), 10, 1);
        add_filter('the_posts', array($this, 'conditionally_add_scripts_and_styles'));
        if (isset($_GET['lfb_action']) && $_GET['lfb_action'] == 'preview') {
            add_filter('template_include', array($this, 'load_lfb_template'));
        }
        add_action('plugins_loaded', array($this, 'init_localization'));



    }
    /**
     * Load popup template.
     * @access  public
     * @since   1.0.0
     * @return void
     */
    public function load_lfb_template($template)
    {
        $file=plugin_dir_path(__FILE__) . '../templates/lfb-preview.php';
        if (file_exists($file)) {
            return $file;
        }
    }

    /*
     * Plugin init localization
     */
    public function init_localization()
    {
        $moFiles=scandir(trailingslashit($this->dir) . 'languages/');
        foreach ($moFiles as $moFile) {
            if (strlen($moFile) > 3 && substr($moFile, -3) == '.mo' && strpos($moFile, get_locale()) > -1) {
                load_textdomain('lfb', trailingslashit($this->dir) . 'languages/' . $moFile);
            }
        }
    }

    public function frontend_enqueue_styles($hook='')
    {
        $settings=$this->getSettings();
        if ($settings->enabled || (isset($_GET['lfb_action'])&& $_GET['lfb_action'] == 'preview')) {
            global $wp_styles;
            wp_register_style($this->_token . '-lfb-reset', esc_url($this->assets_url) . 'css/lfb_frontend-reset.css', array(), $this->_version);
            wp_enqueue_style($this->_token . '-lfb-reset');
            wp_register_style($this->_token . '-frontend', esc_url($this->assets_url) . 'css/lfb_frontend.css', array(), $this->_version);
            wp_enqueue_style($this->_token . '-frontend');
        }
    }
    private function jsonRemoveUnicodeSequences($struct) {
        return preg_replace("/\\\\u([a-f0-9]{4})/e", "iconv('UCS-4LE','UTF-8',pack('V', hexdec('U$1')))", json_encode($struct));
    }

    public function apply_styles()
    {
      $settings=$this->getSettings();
      $output='';

      foreach ($this->currentForms as $currentForm) {
          if ($currentForm > 0 && !is_array($currentForm)) {
              $form=$this->getFormDatas($currentForm);
              if ($form) {
                  if (!$form->colorA || $form->colorA == "") {
                      $form->colorA=$settings->colorA;
                  }
                  if (!$form->colorB || $form->colorB == "") {
                      $form->colorB=$settings->colorB;
                  }
                  if (!$form->colorC || $form->colorC == "") {
                      $form->colorC=$settings->colorC;
                  }
                  if (!$form->item_pictures_size || $form->item_pictures_size == "") {
                      $form->item_pictures_size=$settings->item_pictures_size;
                  }


                  $output .= '#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]  {';
                  $output .= ' color:' . $form->colorB . '; ';
                  $output .= '}';
                  $output .= "\n";
                  $output .= '#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]   .tooltip-inner,#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]   #mainPanel .genSlide .genContent div.selectable span.icon_quantity,#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]   .dropdown-inverse {';
                  $output .= ' background-color:' . $form->colorB . '; ';
                  $output .= '}';
                  $output .= "\n";
                  $output .= '#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]   .tooltip.bottom .tooltip-arrow {';
                  $output .= ' border-bottom-color:' . $form->colorB . '; ';
                  $output .= '}';
                  $output .= "\n";
                  $output .= '#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]   .btn-primary,#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"] .gform_button,#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]   .btn-primary:hover,#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]   .btn-primary:active,#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]    .genPrice .progress .progress-bar-price,#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]    .progress-bar,#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]   .quantityBtns a,#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]   .btn-primary:active,#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]    .btn-primary.active,#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]    .open .dropdown-toggle.btn-primary,#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]   .dropdown-inverse li.active > a,#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]    .dropdown-inverse li.selected > a,#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]   .btn-primary:active,#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]
      .btn-primary.active,#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]   .open .dropdown-toggle.btn-primary,#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]   .btn-primary:hover,#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]    .btn-primary:focus,#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]    .btn-primary:active,#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]    .btn-primary.active,#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]    .open .dropdown-toggle.btn-primary {';
                  $output .= ' background-color:' . $form->colorA . '; ';
                  $output .= '}';
                  $output .= "\n";
                  $output .= '#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]   .has-switch > div.switch-on label,#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]   .form-group.focus .form-control,#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]   .form-control:focus {';
                  $output .= ' border-color:' . $form->colorA . '; ';
                  $output .= '}';
                  $output .= "\n";
                  $output .= '#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"] a:not(.btn),#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]   a:not(.btn):hover,#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]   a:not(.btn):active,#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]   #mainPanel .genSlide .genContent div.selectable.checked span.icon_select,#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]   #mainPanel #finalPrice,#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]    .ginput_product_price,#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]   .checkbox.checked,#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]    .radio.checked,#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]   .checkbox.checked .second-icon,#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]    .radio.checked .second-icon {';
                  $output .= ' color:' . $form->colorA . '; ';
                  $output .= '}';
                  $output .= "\n";
                  $output .= '#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]   #mainPanel .genSlide .genContent div.selectable .img {';
                  $output .= ' max-width:' . $form->item_pictures_size . 'px; ';
                  $output .= ' max-height:' . $form->item_pictures_size . 'px; ';
                  $output .= '}';
                  $output .= "\n";
                  $output .= '#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]   #mainPanel,#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]   .form-control {';
                  $output .= ' color:' . $form->colorC . '; ';
                  $output .= '}';
                  $output .= "\n";
                  $output .= '#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]   .form-control  {';
                  $output .= ' color:' . $form->colorC . '; ';
                  $output .= '}';
                  $output .= "\n";
                  $output .= '#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"] .genPrice .progress .progress-bar-price  {';
                  $output .= ' font-size:' . $form->priceFontSize . 'px; ';
                  $output .= '}';
                  $output .= "\n";
                  $output .= '#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"] .itemDes  {';
                  $output .= ' max-width:' . ($form->item_pictures_size) . 'px; ';
                  $output .= '}';
                  $output .= "\n";
                  $output .= '#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"] #mainPanel .genSlide .genContent div.selectable .wpe_itemQtField  {';
                  $output .= ' width:' . ($form->item_pictures_size) . 'px; ';
                  $output .= '}';
                  $output .= "\n";
              }
          }
      }
      if ($output != '') {
          $output="\n<style >\n" . $output . "</style>\n";
          echo $output;
      }

    }

    public function conditionally_add_scripts_and_styles($posts)
        {
            if (empty($posts)) return $posts;

            $shortcode_found=false;
            $form_id=0;
            $this->currentForms[]=array();
            foreach ($posts as $post) {
                $lastPos=0;
                while (($lastPos=strpos($post->post_content, '[estimation_form', $lastPos)) !== false) {
                    $shortcode_found=true;
                    $pos_start=strpos($post->post_content, 'form_id="', $lastPos + 16) + 9;
                    // $pos_end=strpos($post->post_content, '"', strpos($post->post_content, 'form_id="', strpos($post->post_content, '[estimation_form') + 16) + 10)-1;
                    $pos_end=strpos($post->post_content, '"', $pos_start);
                    $form_id=substr($post->post_content, $pos_start, $pos_end - $pos_start);
                    if ($form_id && $form_id > 0 && !is_array($form_id)) {
                        $this->currentForms[]=$form_id;
                    }
                    $lastPos=$lastPos + 16;
                }
            }

            if ($shortcode_found && count($this->currentForms) > 0) {
                $settings=$this->getSettings();

                // styles
                wp_register_style($this->_token . '-reset', esc_url($this->assets_url) . 'css/reset.css', array(), $this->_version);
                wp_register_style($this->_token . '-bootstrap', esc_url($this->assets_url) . 'css/bootstrap.min.css', array(), $this->_version);
                wp_register_style($this->_token . '-flat-ui', esc_url($this->assets_url) . 'css/flat-ui_frontend.css', array(), $this->_version);
                wp_register_style($this->_token . '-estimationpopup', esc_url($this->assets_url) . 'css/lfb_forms.css', array(), $this->_version);
                wp_enqueue_style($this->_token . '-reset');
                wp_enqueue_style($this->_token . '-bootstrap');
                wp_enqueue_style($this->_token . '-flat-ui');
                wp_enqueue_style($this->_token . '-estimationpopup');

                // scripts
                wp_register_script($this->_token . '-bootstrap-switch', esc_url($this->assets_url) . 'js/bootstrap-switch.js', array($this->_token . '-bootstrap'), $this->_version);
                wp_register_script($this->_token . '-bootstrap', esc_url($this->assets_url) . 'js/bootstrap.min.js', array("jquery-ui-core", "jquery-ui-position", "jquery-ui-datepicker"), $this->_version);
                wp_enqueue_script($this->_token . '-bootstrap');
                wp_enqueue_script($this->_token . '-bootstrap-switch');
                wp_register_script($this->_token . '-estimationpopup', esc_url($this->assets_url) . 'js/lfb_form.min.js', array($this->_token . '-bootstrap-switch'), $this->_version);
                wp_enqueue_script($this->_token . '-estimationpopup');

                include_once(ABSPATH . 'wp-admin/includes/plugin.php');
                $js_data=array();
                foreach ($this->currentForms as $formID) {

                    if ($formID > 0 && !is_array($formID)) {
                        $form=$this->getFormDatas($formID);

                        if ($form) {
                            if (is_plugin_active('gravityforms/gravityforms.php') && $form->gravityFormID > 0) {
                                gravity_form_enqueue_scripts($form->gravityFormID, true);
                                if (is_plugin_active('gravityformssignature/signature.php')) {
                                    wp_register_script('gforms_signature', esc_url($this->assets_url) . '../../gravityformssignature/super_signature/ss.js', array("gform_gravityforms"), $this->_version);
                                    wp_enqueue_script('gforms_signature');
                                }
                            }
                            if (!$form->colorA || $form->colorA == "") {
                                $form->colorA=$settings->colorA;
                            }

                            global $wpdb;
                            $table_name=$wpdb->prefix . "wpefc_links";
                            $links=$wpdb->get_results("SELECT * FROM $table_name WHERE formID=" . $formID);


                            $js_data[]=array(
                                'currentRef' => 0,
                                'ajaxurl' => admin_url('admin-ajax.php'),
                                'initialPrice' => $form->initial_price,
                                'max_price' => $form->max_price,
                                'currency' => $form->currency,
                                'currencyPosition' => $form->currencyPosition,
                                'intro_enabled' => $form->intro_enabled,
                                'save_to_cart' => $form->save_to_cart,
                                'colorA' => $form->colorA,
                                'close_url' => $form->close_url,
                                'animationsSpeed' => $form->animationsSpeed,
                                'email_toUser' => $form->email_toUser,
                                'showSteps' => $form->showSteps,
                                'formID' => $form->id,
                                'gravityFormID' => $form->gravityFormID,
                                'showInitialPrice' => $form->show_initialPrice,
                                'disableTipMobile' => $form->disableTipMobile,
                                'legalNoticeEnable'=>$form->legalNoticeEnable,
                                'links'=>$links,
                                'txt_yes' => __('Yes', 'lfb'),
                                'txt_no' => __('No', 'lfb')
                            );
                        }
                    }
                }
                wp_localize_script($this->_token . '-estimationpopup', 'wpe_forms', $js_data);

                add_action('wp_head', array($this, 'options_custom_styles'));

            }

            return $posts;
        }


        /*
         * Shortcode to integrate a form in a page
         */

        public function wpt_shortcode($attributes, $content=null)
        {
            $response="";
            extract(shortcode_atts(array(
                'form' => 0,
                'height' => 1000,
                'popup' => false,
                'fullscreen' => false,
                'form_id' => 0
            ), $attributes));
            if (is_numeric($height)) {
                $height .= 'px';
            }
            if ($form_id > 0 && !is_array($form_id)) {
                global $wpdb;
                $table_name=$wpdb->prefix . "wpefc_forms";
                $forms=array();
                $formReq=$wpdb->get_results("SELECT * FROM $table_name WHERE id=" . $form_id . " LIMIT 1");
                $form=$formReq[0];
                //$form=$formReq->form_page_id;
                $settings=$this->getSettings();
                $fields=$this->getFieldDatas($form->id);
                $steps=$this->getStepsData($form->id);
                $items=$this->getItemsData($form->id);

                if (!$form->save_to_cart) {
                    $form->save_to_cart='0';
                }
                $popupCss='';
                $fullscreenCss='';
                if ($popup) {
                    $popupCss='wpe_popup';
                }
                if ($fullscreen) {
                    $fullscreenCss='wpe_fullscreen';
                }

                $response .= '<div id="lfb_loader"></div><div id="lfb_bootstraped" class="lfb_bootstraped"><div id="estimation_popup" data-form="' . $form_id . '" class="wpe_bootstraped ' . $popupCss . ' ' . $fullscreenCss . '">
                <a id="wpe_close_btn" href="javascript:"><span class="fui-cross"></span></a>
                <div id="wpe_panel">
                <div class="container-fluid">
                    <div class="row">
                        <div class="">
                            <div id="startInfos">
                                <h1>' . $form->intro_title . '</h1>
                                <p>' . $form->intro_text . '</p>
                            </div>
                            <p>
                                <a href="javascript:" onclick="jQuery(\'#startInfos > p\').slideDown();" class="btn btn-large btn-primary" id="btnStart">' . $form->intro_btn . '</a>
                            </p>

                            <div id="genPrice" class="genPrice">
                                <div class="progress">
                                    <div class="progress-bar" style="width: 0%;">
                                        <div class="progress-bar-price">
                                            0 $
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <!-- /genPrice -->
                            <h2 id="finalText" class="stepTitle">' . $form->succeed_text . '</h2>
                        </div>
                        <!-- /col -->
                    </div>
                    <!-- /row -->
                <div id="mainPanel" class="palette-clouds" data-savecart="' . $form->save_to_cart . '">';
                $i=0;

                foreach ($steps as $dataSlide) {
                    if ($dataSlide->formID == $form->id) {
                    	$dataContent=json_decode($dataSlide->content);

                        $required='';
                        if ($dataSlide->itemRequired > 0) {
                            $required='data-required="true"';
                        }
                        $response .= '<div class="genSlide" data-start="'.$dataContent->start.'" data-stepid="'.$dataSlide->id.'" data-title="' . $dataSlide->title . '" ' . $required . ' data-dependitem="' . $dataSlide->itemDepend . '">';
                        $response .= '	<h2 class="stepTitle">' . $dataSlide->title . '</h2>';
                        $response .= '	<div class="genContent container-fluid">';
                        $response .= '		<div class="row">';
                        foreach ($items as $dataItem) {

                            if ($dataItem->stepID == $dataSlide->id) {
                                $chkDisplay=true;
                                $checked='';
                                $checkedCb='';
                                $prodID=0;
                                $itemRequired='';
                                if ($dataItem->isRequired) {
                                    $itemRequired='data-required="true"';
                                }
                                if ($dataItem->ischecked == 1) {
                                    $checked='prechecked';
                                    $checkedCb='checked';
                                }
                                if ($dataItem->wooProductID > 0) {
                                    $prodID=$dataItem->wooProductID;
                                    $product=new WC_Product($dataItem->wooProductID);
                                    if (!$product) {
                                      $chkDisplay=false;
                                    } else {
                                        if ($dataItem->wooVariation == 0) {
                                            $dataItem->price=$product->price;
                                            if ($product->get_stock_quantity() && $product->get_stock_quantity() < $dataItem->quantity_max) {
                                                $dataItem->quantity_max=$product->get_stock_quantity();
                                            }
                                            if ($product->get_stock_quantity() && $product->get_stock_quantity() < 1){
                                              $chkDisplay=false;
                                            }
                                        } else {
                                            $variable_product=new WC_Product_Variation($dataItem->wooVariation);
                                            $dataItem->price=$variable_product->price;
                                            if ($variable_product->get_stock_quantity() && $variable_product->get_stock_quantity() < $dataItem->quantity_max) {
                                                $dataItem->quantity_max=$variable_product->get_stock_quantity();
                                            }
                                            if ($product->get_stock_quantity() && $product->get_stock_quantity() < 1){
                                              $chkDisplay=false;
                                            }
                                        }
                                    }
                                } else if ($form->save_to_cart) {
                                    $dataItem->price=0;
                                }
                                $originalTitle=$dataItem->title;
			                          $dataShowPrice="";
                                if ($dataItem->showPrice) {
					                           $dataShowPrice='data-showprice="1"';
                                    if ($form->currencyPosition == 'right') {
                                      if($dataItem->operation == "+"){
                                        $dataItem->title=$dataItem->title . " : " . $dataItem->price . $form->currency;
                                      }
                                      if($dataItem->operation == "-"){
                                        $dataItem->title=$dataItem->title . " : -" . $dataItem->price . $form->currency;
                                      }
                                      if($dataItem->operation == "x"){
                                        $dataItem->title=$dataItem->title . " : +" . $dataItem->price .'%';
                                      }
                                      if($dataItem->operation == "/"){
                                        $dataItem->title=$dataItem->title . " : -" . $dataItem->price .'%';
                                      }
                                    } else {
                                      if($dataItem->operation == "+"){
                                        $dataItem->title=$dataItem->title . " : " . $form->currency . $dataItem->price;
                                      }
                                      if($dataItem->operation == "-"){
                                        $dataItem->title=$dataItem->title . " : -" . $form->currency. $dataItem->price;
                                      }
                                      if($dataItem->operation == "x"){
                                        $dataItem->title=$dataItem->title . " : +" . $dataItem->price .'%';
                                      }
                                      if($dataItem->operation == "/"){
                                        $dataItem->title=$dataItem->title . " : -" . $dataItem->price .'%';
                                      }
                                    }
                                }
                               $urlTag = "";
                               if($dataItem->urlTarget != ""){
                                 $urlTag .= 'data-urltarget="'.$dataItem->urlTarget.'"';
                               }
                                if ($chkDisplay) {
                                    $colClass='col-md-2';
                                    if ($dataItem->useRow) {
                                        $colClass='col-md-12';
                                    }

                                    if ($dataItem->type == 'picture') {
                                        $response .= '<div class="itemBloc ' . $colClass . '">';
                                        $group='';
                                        if ($dataItem->groupitems != "") {
                                            $group='data-group="' . $dataItem->groupitems . '"';
                                        }
                                        $tooltipPosition='bottom';
                                        if ($form->qtType == 1) {
                                            $tooltipPosition='top';
                                        }
                                        $response .= '<div class="selectable ' . $checked . '" '.$dataShowPrice.' '.$urlTag.' data-reduc="' . $dataItem->reduc_enabled . '" data-reducqt="' . $dataItem->reducsQt . '"  data-operation="' . $dataItem->operation . '" data-itemid="' . $dataItem->id . '"  ' . $group . '  data-prodid="' . $prodID . '" data-title="' . $dataItem->title . '" data-toggle="tooltip" title="' . $dataItem->title . '" data-originaltitle="' . $originalTitle . '" data-placement="' . $tooltipPosition . '" data-price="' . $dataItem->price . '">';
                                        $tint='false';
                                        if ($dataItem->imageTint) {
                                            $tint='true';
                                        }
                                        $response .= '<img data-tint="' . $tint . '" src="' . $dataItem->image . '" alt="' . $dataItem->title . '" class="img" />';

                                        $response .= '<span class="palette-clouds fui-cross icon_select"></span>';
                                        if ($dataItem->quantity_enabled) {
                                            if ($form->qtType == 1) {
                                                $qtMax='';
                                                if ($dataItem->quantity_max > 0) {
                                                    $qtMax='max="' . $dataItem->quantity_max . '"';
                                                } else {
                                                    $qtMax='max="10"';
                                                }
                                                if ($dataItem->quantity_min > 0) {
                                                    $qtMin= $dataItem->quantity_min . '"';
                                                } else {
                                                    $qtMin='1';
                                                }
                                                $response .= '<div class="form-group wpe_itemQtField">';
                                                $response .= ' <input class="wpe_qtfield form-control" min="'.$qtMin.'" ' . $qtMax . ' type="number" value="'.$qtMin.'" /> ';

                                                $response .= '</div>';
                                            } else {
                                                $response .= '<div class="quantityBtns" data-max="' . $dataItem->quantity_max . '" data-min="' . $dataItem->quantity_min . '">
                                                <a href="javascript:" data-btn="less">-</a>
                                                <a href="javascript:" data-btn="more">+</a>
                                                </div>';
                                                $valMin = 1;
                                                if ($dataItem->quantity_min > 0) {
                                                  $valMin = $dataItem->quantity_min;
                                                }
                                                $response .= '<span class="palette-turquoise icon_quantity">'.$valMin.'</span>';
                                            }
                                        }
                                        $response .= '</div>';
                                        if ($dataItem->description != "") {
                                            $cssWidth='';
                                            if ($dataItem->useRow) {
                                                $cssWidth='max-width: 100%;';
                                            }
                                            $response .= '<p class="itemDes" style="'.$cssWidth.'">' . $dataItem->description . '</p>';
                                        }
                                        $response .= '</div>';
                                    } else if ($dataItem->type == 'filefield') {
                                      $response .= '<div class="' . $colClass . '">';
                                      $response .= '<p>
                                              <label>' . $dataItem->title . '</label>
                                              <br/>
                                              <input type="file" '. $itemRequired .'  class="' . $checked . '" data-itemid="' . $dataItem->id . '" data-title="' . $dataItem->title . '" data-originaltitle="' . $originalTitle . '" />
                                              </p>
                                              ';

                                      if ($dataItem->description != "") {
                                          $response .= '<p class="itemDes" style="margin: 0 auto; max-width: 90%;">' . $dataItem->description . '</p>';
                                      }
                                      $response .= '</div>';
                                    } else if ($dataItem->type == 'qtfield') {
                                        $response .= '<div class="' . $colClass . '">';
                                        $response .= '<div class="form-group">';
                                        $response .= '<label>' . $dataItem->title . '</label>';
                                        $qtMax='';
                                        if ($qtMax > 0) {
                                            $qtMax='max="' . $dataItem->quantity_max . '"';
                                        }
                                        $response .= ' <input class="wpe_qtfield form-control" min="0" ' . $qtMax . ' '.$dataShowPrice.' type="number" value="0" data-reduc="' . $dataItem->reduc_enabled . '" data-price="' . $dataItem->price . '" data-reducqt="' . $dataItem->reducsQt . '" data-operation="' . $dataItem->operation . '" data-itemid="' . $dataItem->id . '" class="form-control" data-title="' . $dataItem->title . '" />
                                                ';

                                        if ($dataItem->description != "") {
                                            $response .= '<p class="itemDes" style="margin: 0 auto; max-width: 90%;">' . $dataItem->description . '</p>';
                                        }
                                        $response .= '</div>';
                                        $response .= '</div>';
                                    } else if ($dataItem->type == 'select') {
                                      $response .= '<div class="' . $colClass . '">';
                                      $response .= '<p>
                                              <label>' . $dataItem->title . '</label>
                                              <br/>
                                              <select class="form-control"  data-originaltitle="' . $originalTitle . '" data-itemid="' . $dataItem->id . '"  data-title="' . $dataItem->title . '" >';
                                              $optionsArray = explode('|',$dataItem->optionsValues);
                                              foreach($optionsArray as $option){
                                                if($option != ""){
                                                  $response .= '<option value="'.$option.'">'.$option.'</option>';
                                                }
                                              }
                                              $response .= '</select>
                                              </p>
                                              ';

                                      if ($dataItem->description != "") {
                                          $response .= '<p class="itemDes" style="margin: 0 auto; max-width: 90%;">' . $dataItem->description . '</p>';
                                      }
                                      $response .= '</div>';
                                    } else if ($dataItem->type == 'checkbox') {

                                        $group='';
                                        if ($dataItem->groupitems != "") {
                                            $group='data-group="' . $dataItem->groupitems . '"';
                                        }
                                        $response .= '<div class="' . $colClass . '">';
                                        $response .= '<p>
                                                <label>' . $dataItem->title . '</label>
                                                <br/>
                                                <input type="checkbox" ' . $group . ' class="' . $checked . '" '.$urlTag.' '.$dataShowPrice.' data-operation="' . $dataItem->operation . '" data-originaltitle="' . $originalTitle . '" data-itemid="' . $dataItem->id . '" data-prodid="' . $prodID . '" ' . $itemRequired . ' data-toggle="switch" ' . $checkedCb . ' data-price="' . $dataItem->price . '" data-title="' . $dataItem->title . '" />
                                                </p>
                                                ';

                                        if ($dataItem->description != "") {
                                            $response .= '<p class="itemDes" style="margin: 0 auto; max-width: 90%;">' . $dataItem->description . '</p>';
                                        }
                                        $response .= '</div>';
                                    } else {
                                        $response .= '<div class="' . $colClass . '">';
                                        $response .= '<div class="form-group">';
                                        $response .= '<label>' . $dataItem->title . '</label>
                                                <input type="text" data-itemid="' . $dataItem->id . '" class="form-control" ' . $itemRequired . ' data-title="' . $dataItem->title . '" data-originaltitle="' . $originalTitle . '" />
                                                ';

                                        if ($dataItem->description != "") {
                                            $response .= '<p class="itemDes" style="margin: 0 auto; max-width: 90%;">' . $dataItem->description . '</p>';
                                        }
                                        $response .= '</div>';
                                        $response .= '</div>';
                                    }
                                }
                            }
                        }

                        $response .= ' </div>';
                        $response .= ' </div>';
                        $response .= '<div class="errorMsg alert alert-danger">' . $form->errorMessage . '</div>';
                        $response .= '<p style="margin-top: 42px;" class="text-center">';
                        if (($form->save_to_cart) && ($i == count($steps) - 1)) {
                            $response .= '<a href="javascript:" id="#wpe_btnOrder" class="btn btn-wide btn-primary btn-next">' . $form->last_btn . '</a>';
                        } else {
                            $response .= '<a href="javascript:" class="btn btn-wide btn-primary btn-next">' . $form->btn_step . '</a>';
                        }
                        if ($dataContent->start == 0) {
                            $response .= '<br/><a href="javascript:"  class="linkPrevious">' . $form->previous_step . '</a>';
                        }
                        $response .= '</p>';

                        $response .= '</div>';
                        $i++;
                    }
                }
                $response .= '<div class="genSlide" id="finalSlide" data-stepid="final">
                <h2 class="stepTitle">' . $form->last_title . '</h2>
                <div class="genContent">
                    <div class="genContentSlide active">
                        <p>' . $form->last_text . '</p>';
                    $dispFinalPrice='';
                    if ($form->hideFinalPrice == 1){
                      $dispFinalPrice="display:none;";
                    }
                    $response .=  '<h3 id="finalPrice" style="'.$dispFinalPrice.'"></h3>';

                if ($form->gravityFormID > 0) {
                    gravity_form($form->gravityFormID, $display_title=false, $display_description=true, $display_inactive=false, $field_values=null, $ajax=true);
                } else {
                    foreach ($fields as $field) {
                        $response .= '<div class="form-group">';
                        $placeholder="";
                        $disp='';
                        $dispLabel='block';
                        if ($field->visibility == 'toggle') {
                            $disp='toggle';
                            $placeholder="";
                        } else {
                            $dispLabel='none';
                            $placeholder=$field->label;
                            if ($field->validation == 'fill') {
                                $req="true";
                            }
                        }
                        $response .= '<label for="field_' . $field->id . '" style="display: ' . $dispLabel . '">' . $field->label . '</label>';
                        if ($field->visibility == 'toggle') {
                            $response .= '<input id="field_' . $field->id . '_cb" type="checkbox" data-toggle="switch" data-fieldid="' . $field->id . '" /><br/>';
                        }
                        $req="false";
                        $emailField='';
                        if ($field->validation == 'email') {
                            $emailField='emailField';
                        }
                        if ($field->validation == 'fill') {
                            $req='true';
                        }

                        if ($field->typefield == 'textarea') {
                            $response .= '<textarea id="field_' . $field->id . '" data-required="' . $req . '"  class="form-control ' . $disp . ' ' . $emailField . '" placeholder="' . $placeholder . '"></textarea>';
                        } else {
                            $response .= '<input type="text" id="field_' . $field->id . '" data-required="' . $req . '" placeholder="' . $placeholder . '" class="form-control ' . $emailField . ' ' . $disp . '"/>';
                        }
                        $response .= '</div>';
                    }

                    $response .= '<p style="margin-bottom: 28px;">';
                }
                if ($form->legalNoticeEnable) {
                    $response .= '
                    <div id="lfb_legalNoticeContent">'.$form->legalNoticeContent.'</div>
                    <div class="form-group" style=" margin-top: 14px;">
                      <label for="lfb_legalCheckbox">'.$form->legalNoticeTitle.'</label>
                      <input type="checkbox" data-toggle="switch" id="lfb_legalCheckbox" class="form-control"/>
                    </div>';
                }

                if ($form->use_paypal) {
                    $response .= '<form id="wtmt_paypalForm" action="https://www.paypal.com/cgi-bin/webscr" method="post">
                            <a href="javascript:" id="btnOrderPaypal" class="btn btn-wide btn-primary">' . $form->last_btn . '</a>
                            <input type="submit" style="display: none;" name="submit"/>
                            <img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
                            <input type="hidden" name="add" value="1">
                            <input type="hidden" name="cmd" value="_xclick">

                            <input type="hidden" name="business" value="' . $form->paypal_email . '">
                            <input type="hidden" name="business_cs_email" value="' . $form->paypal_email . '">
                            <input type="hidden" name="item_name" value="' . $form->title . '">
                            <input type="hidden" name="item_number" value="A00001">
                            <input type="hidden" name="amount" value="1">
                            <input type="hidden" name="no_shipping" value="1">
                            <input type="hidden" name="cn"
                                   value="Message">
                            <input type="hidden" name="custom"
                                   value="Form content">
                            <input type="hidden" name="currency_code" value="' . $form->paypal_currency . '">
                            <input type="hidden" name="return"
                                   value="' . $form->close_url . '">
                        </form>';
                } else if ($form->gravityFormID == 0) {
                    $response .= ' <a href="javascript:" id="wpe_btnOrder" class="btn btn-wide btn-primary">' . $form->last_btn . '</a>';
                }
                if (count($steps) > 0) {
                    $response .= '<br/><a href="javascript:" class="linkPrevious">' . $form->previous_step . '</a>';
                }
                $response .= '</p>';
            }
            $response .= '</div>';
            $response .= '</div>';
            $response .= '</div>';
            $response .= '</div>';
            $response .= '</div>';
            $response .= '</div>';
            $response .= '</div>';
            $response .= '</div>';
            /* end*/


            return $response;
            // return '<iframe class="estimationForm_frameSC" src="' . get_permalink($form) . '" style="height:' . $height . '">' . $content . '</iframe>';
        }


        /*
        * Styles integration
        */
        public function options_custom_styles()
        {

            $settings=$this->getSettings();
            $output='';

            foreach ($this->currentForms as $currentForm) {
                if ($currentForm > 0 && !is_array($currentForm)) {
                    $form=$this->getFormDatas($currentForm);
                    if ($form) {
                        if (!$form->colorA || $form->colorA == "") {
                            $form->colorA=$settings->colorA;
                        }
                        if (!$form->colorB || $form->colorB == "") {
                            $form->colorB=$settings->colorB;
                        }
                        if (!$form->colorC || $form->colorC == "") {
                            $form->colorC=$settings->colorC;
                        }
                        if (!$form->item_pictures_size || $form->item_pictures_size == "") {
                            $form->item_pictures_size=$settings->item_pictures_size;
                        }


                        $output .= '#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]  {';
                        $output .= ' color:' . $form->colorB . '; ';
                        $output .= '}';
                        $output .= "\n";
                        $output .= '#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]   .tooltip-inner,#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]   #mainPanel .genSlide .genContent div.selectable span.icon_quantity,#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]   .dropdown-inverse {';
                        $output .= ' background-color:' . $form->colorB . '; ';
                        $output .= '}';
                        $output .= "\n";
                        $output .= '#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]   .tooltip.bottom .tooltip-arrow {';
                        $output .= ' border-bottom-color:' . $form->colorB . '; ';
                        $output .= '}';
                        $output .= "\n";
                        $output .= '#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]   .btn-primary,#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"] .gform_button,#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]   .btn-primary:hover,#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]   .btn-primary:active,#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]    .genPrice .progress .progress-bar-price,#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]    .progress-bar,#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]   .quantityBtns a,#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]   .btn-primary:active,#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]    .btn-primary.active,#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]    .open .dropdown-toggle.btn-primary,#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]   .dropdown-inverse li.active > a,#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]    .dropdown-inverse li.selected > a,#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]   .btn-primary:active,#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]
            .btn-primary.active,#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]   .open .dropdown-toggle.btn-primary,#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]   .btn-primary:hover,#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]    .btn-primary:focus,#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]    .btn-primary:active,#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]    .btn-primary.active,#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]    .open .dropdown-toggle.btn-primary {';
                        $output .= ' background-color:' . $form->colorA . '; ';
                        $output .= '}';
                        $output .= "\n";
                        $output .= '#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]   .has-switch > div.switch-on label,#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]   .form-group.focus .form-control,#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]   .form-control:focus {';
                        $output .= ' border-color:' . $form->colorA . '; ';
                        $output .= '}';
                        $output .= "\n";
                        $output .= '#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"] a:not(.btn),#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]   a:not(.btn):hover,#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]   a:not(.btn):active,#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]   #mainPanel .genSlide .genContent div.selectable.checked span.icon_select,#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]   #mainPanel #finalPrice,#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]    .ginput_product_price,#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]   .checkbox.checked,#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]    .radio.checked,#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]   .checkbox.checked .second-icon,#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]    .radio.checked .second-icon {';
                        $output .= ' color:' . $form->colorA . '; ';
                        $output .= '}';
                        $output .= "\n";
                        $output .= '#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]   #mainPanel .genSlide .genContent div.selectable .img {';
                        $output .= ' max-width:' . $form->item_pictures_size . 'px; ';
                        $output .= ' max-height:' . $form->item_pictures_size . 'px; ';
                        $output .= '}';
                        $output .= "\n";
                        $output .= '#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]   #mainPanel,#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]   .form-control {';
                        $output .= ' color:' . $form->colorC . '; ';
                        $output .= '}';
                        $output .= "\n";
                        $output .= '#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"]   .form-control  {';
                        $output .= ' color:' . $form->colorC . '; ';
                        $output .= '}';
                        $output .= "\n";
                        $output .= '#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"] .genPrice .progress .progress-bar-price  {';
                        $output .= ' font-size:' . $form->priceFontSize . 'px; ';
                        $output .= '}';
                        $output .= "\n";
                        $output .= '#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"] .itemDes  {';
                        $output .= ' max-width:' . ($form->item_pictures_size) . 'px; ';
                        $output .= '}';
                        $output .= "\n";
                        $output .= '#estimation_popup.wpe_bootstraped[data-form="' . $form->id . '"] #mainPanel .genSlide .genContent div.selectable .wpe_itemQtField  {';
                        $output .= ' width:' . ($form->item_pictures_size) . 'px; ';
                        $output .= '}';
                        $output .= "\n";


                        if($form->customCss != ""){
                          $output .= $form->customCss;
                          $output .= "\n";
                        }

                    }
                }
            }
            if ($output != '') {
                $output="\n<style >\n" . $output . "</style>\n";
                echo $output;
            }
        }


        private function isUpdated()
        {
            $settings=$this->getSettings();
            if ($settings->updated) {
                return false;
            } else {
                return true;
            }
        }

        public function frontend_enqueue_scripts($hook='')
        {

            wp_register_script($this->_token . '-frontend', esc_url($this->assets_url) . 'js/lfb_frontend.min.js', array('jquery'), $this->_version);
            wp_enqueue_script($this->_token . '-frontend');
        }


        /* Ajax : get Current ref */
        public function get_currentRef()
        {
          $rep=false;
            $settings=$this->getSettings();
            if (isset($_POST['formID']) && !is_array($_POST['formID'])) {
                $formID=$_POST['formID'];

                global $wpdb;
                $table_name=$wpdb->prefix . "wpefc_forms";
                $rows=$wpdb->get_results("SELECT * FROM $table_name WHERE id=$formID LIMIT 1");
                $form=$rows[0];
                $current_ref=$form->current_ref + 1;
                $wpdb->update($table_name, array('current_ref' => $current_ref), array('id' => $form->id));
                $rep=$form->ref_root . $current_ref;
            }
            echo $rep;
            die();
        }

        /*
         * Ajax : send email
         */
        public function send_email()
        {
            $settings=$this->getSettings();
            $formID=$_POST['formID'];

            global $wpdb;
            $table_name=$wpdb->prefix . "wpefc_forms";
            $rows=$wpdb->get_results("SELECT * FROM $table_name WHERE id=$formID LIMIT 1");
            $form=$rows[0];

            $current_ref=$form->current_ref + 1;
            $wpdb->update($table_name, array('current_ref' => $current_ref), array('id' => $form->id));
            if (!isset($_POST['gravity']) || $_POST['gravity'] == 0) {

                if ($_POST['email_toUser'] == '1') {

                    $content=$form->email_userContent;
                    $content=str_replace("[customer_email]", $_POST['email'], $content);
                    $content=str_replace("[project_content]", $_POST['content'], $content);
                    $content=str_replace("[information_content]", $_POST['informations'], $content);
                    $content=str_replace("[total_price]", $_POST['totalTxt'], $content);
                    $content=str_replace("[ref]", $form->ref_root . $current_ref, $content);

                    add_filter('wp_mail_content_type', create_function('', 'return "text/html"; '));
                    wp_mail($_POST['email'], $form->email_userSubject, $content);
                }

                $content=$form->email_adminContent;
                $content=str_replace("[customer_email]", $form->ref_root . $current_ref, $content);
                $content=str_replace("[project_content]", $_POST['content'], $content);
                $content=str_replace("[information_content]", $_POST['informations'], $content);
                $content=str_replace("[total_price]", $_POST['totalTxt'], $content);
                $content=str_replace("[ref]", $form->ref_root . $current_ref, $content);

                add_filter('wp_mail_content_type', create_function('', 'return "text/html"; '));
                if (wp_mail($form->email, $form->email_subject . ' - ' . $form->ref_root . $current_ref, $content)) {
                    //echo 'true';
                } else {
                    //echo 'false';
                }
                $email="";
                if(isset($_POST['email'])){
                  $email=$_POST['email'];
                }

                $table_name=$wpdb->prefix . "wpefc_logs";
                $wpdb->insert($table_name, array('ref' => $form->ref_root . $current_ref,'email'=>$email,'formID'=>$formID,'dateLog'=>date('Y-m-d'),'content'=>$content));

            }


            echo $form->ref_root . $current_ref;
            die();
        }

        /**
             * Get  fields datas
             * @since   1.6.0
             * @return object
             */
            public function getFieldsData()
            {
                global $wpdb;
                $table_name=$wpdb->prefix . "wpefc_fields";
                $rows=$wpdb->get_results("SELECT * FROM $table_name  ORDER BY ordersort ASC");
                return $rows;
            }

            /**
             * Get  fields from specific form
             * @since   1.6.0
             * @return object
             */
            public function getFieldDatas($form_id)
            {
                global $wpdb;
                $table_name=$wpdb->prefix . "wpefc_fields";
                $rows=$wpdb->get_results("SELECT * FROM $table_name WHERE formID=$form_id ORDER BY ordersort ASC");
                return $rows;
            }

            /**
             * Get  form by pageID
             * @since   1.6.0
             * @return object
             */
            public function getFormByPageID($pageID)
            {
                global $wpdb;
                $table_name=$wpdb->prefix . "wpefc_forms";
                $rows=$wpdb->get_results("SELECT * FROM $table_name WHERE form_page_id=$pageID LIMIT 1");
                if ($rows) {
                    return $rows[0];
                } else {
                    return null;
                }
            }

            /**
             * Get Forms datas
             * @return Array
             */
            private function getFormsData()
            {
                global $wpdb;
                $table_name=$wpdb->prefix . "wpefc_forms";
                $rows=$wpdb->get_results("SELECT * FROM $table_name");
                return $rows;
            }

            /**
             * Get specific Form datas
             * @return object
             */
            public function getFormDatas($form_id)
            {
                global $wpdb;
                $table_name=$wpdb->prefix . "wpefc_forms";
                $rows=$wpdb->get_results("SELECT * FROM $table_name WHERE id=$form_id LIMIT 1");
                if(count($rows)>0){
                  return $rows[0];
                } else {
                  return null;
                }
            }


             /**
             * Return steps data.
             * @access  public
             * @since   1.0.0
             * @return  object
             */
            public function getStepsData($form_id)
            {
                global $wpdb;
                $table_name=$wpdb->prefix . "wpefc_steps";
                $rows=$wpdb->get_results("SELECT * FROM $table_name WHERE formID=$form_id ORDER BY ordersort");
                return $rows;
            }


                /**
                 * Return items data.
                 * @access  public
                 * @since   1.0.0
                 * @return  object
                 */
                public function getItemsData($form_id)
                {
                    global $wpdb;
                    $results=array();
                    $table_name=$wpdb->prefix . "wpefc_steps";
                    $steps=$wpdb->get_results("SELECT * FROM $table_name WHERE formID=$form_id ORDER BY ordersort");
                    foreach ($steps as $step) {
                        $table_name=$wpdb->prefix . "wpefc_items";
                        $rows=$wpdb->get_results("SELECT * FROM $table_name WHERE stepID=$step->id ORDER BY ordersort");
                        foreach ($rows as $row) {
                            $results[]=$row;
                        }
                    }
                    return $results;
                }

            // End getItemsData()


            /**
             * Save form datas to cart (woocommerce only)
             * @access  public
             * @since   1.0.0
             * @return  void
             */
            public function cart_save()
            {
                global $woocommerce;
                $products=$_POST['products'];
                foreach ($products as $product) {
                    $productWoo=new WC_Product($product['product_id']);
                    $existInCart=false;
                    foreach ($woocommerce->cart->get_cart() as $cart_item_key => $cart_item) {
                        if ($cart_item['product_id'] == $product['product_id']) {
                            $cart_item['quantity'] += $product['quantity'];
                        }
                    }
                    if (!$existInCart) {
                        $woocommerce->cart->add_to_cart($product['product_id'], $product['quantity']);
                    }
                }
                die();

            }




    /**
     * Main LFB_Core Instance
     *
     *
     * @since 1.0.0
     * @static
     * @see BSS_Core()
     * @return Main LFB_Core instance
     */
    public static function instance($file='', $version='1.0.0')
    {
        if (is_null(self::$_instance)) {
            self::$_instance=new self($file, $version);
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
    }

// End __clone()

    /**
     * Unserializing instances of this class is forbidden.
     *
     * @since 1.0.0
     */
    public function __wakeup()
    {
        //  _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?'), $this->_version);
    }

// End __wakeup()

    /**
     * Return settings.
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function getSettings()
    {
        global $wpdb;
        $table_name=$wpdb->prefix . "wpefc_settings";
        $settings=$wpdb->get_results("SELECT * FROM $table_name WHERE id=1 LIMIT 1");
        return $settings[0];
    }
    // End getSettings()


    /**
     * Log the plugin version number.
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    private function _log_version_number()
    {
        update_option($this->_token . '_version', $this->_version);
    }

}
