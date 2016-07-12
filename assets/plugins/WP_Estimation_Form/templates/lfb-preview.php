<?php
/**
 * Template Name: WP Estimation & Payment Forms Preview
 *
 * @package WordPress
 * @subpackage WP Estimation & Payment Forms
 */
$lfb= LFB_Core::instance(__FILE__, '1.0');
$formID = $_GET['form'];
$form = $lfb->getFormDatas($formID);
wp_register_style($lfb->_token . '-reset', esc_url($lfb->assets_url) . 'css/reset.css', array(), $lfb->_version);
wp_register_style($lfb->_token . '-bootstrap', esc_url($lfb->assets_url) . 'css/bootstrap.min.css', array(), $lfb->_version);
wp_register_style($lfb->_token . '-flat-ui', esc_url($lfb->assets_url) . 'css/flat-ui_frontend.css', array(), $lfb->_version);
wp_register_style($lfb->_token . '-estimationpopup', esc_url($lfb->assets_url) . 'css/lfb_forms.css', array(), $lfb->_version);
wp_enqueue_style($lfb->_token . '-reset');
wp_enqueue_style($lfb->_token . '-bootstrap');
wp_enqueue_style($lfb->_token . '-flat-ui');
wp_enqueue_style($lfb->_token . '-estimationpopup');

// scripts
wp_register_script($lfb->_token . '-bootstrap-switch', esc_url($lfb->assets_url) . 'js/bootstrap-switch.js', array('jquery', "jquery-ui-core"), $lfb->_version);
wp_register_script($lfb->_token . '-bootstrap', esc_url($lfb->assets_url) . 'js/bootstrap.min.js', array("jquery"), $lfb->_version);
wp_enqueue_script($lfb->_token . '-bootstrap');
wp_enqueue_script($lfb->_token . '-bootstrap-switch');
wp_register_script($lfb->_token . '-estimationpopup', esc_url($lfb->assets_url) . 'js/lfb_form.min.js', array("jquery-ui-core", "jquery-ui-position", "jquery-ui-datepicker"), $lfb->_version);
wp_enqueue_script($lfb->_token . '-estimationpopup');

$lfb->currentForms[] = $formID;
add_action('wp_head', array($lfb, 'options_custom_styles'));
include_once(ABSPATH . 'wp-admin/includes/plugin.php');
$js_data = array();

if ($form) {
    if (is_plugin_active('gravityforms/gravityforms.php') && $form->gravityFormID > 0) {
        gravity_form_enqueue_scripts($form->gravityFormID, true);
        if (is_plugin_active('gravityformssignature/signature.php')) {
            wp_register_script('gforms_signature', esc_url($lfb->assets_url) . '../../gravityformssignature/super_signature/ss.js', array("gform_gravityforms"), $lfb->_version);
            wp_enqueue_script('gforms_signature');
        }
    }
    if (!$form->colorA || $form->colorA == "") {
        $form->colorA = $settings->colorA;
    }

    global $wpdb;
    $table_name = $wpdb->prefix . "wpefc_links";
    $links = $wpdb->get_results("SELECT * FROM $table_name WHERE formID=" . $formID);

    $js_data[] = array(
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
    'legalNoticeEnable'=>$form->legalNoticeEnable,
    'links'=>$links,
    'txt_yes' => __('Yes', 'lfb'),
    'txt_no' => __('No', 'lfb')
  );
}
wp_localize_script($lfb->_token . '-estimationpopup', 'wpe_forms', $js_data);
add_action('wp_head', array($lfb, 'options_custom_styles'));

get_header();
function lfb_content($content) {
  $content = '[estimation_form form_id="'.$_GET['form'].'" fullscreen="true"]';
  return do_shortcode( $content );
}
add_filter( 'the_content', 'lfb_content', 20 );
echo '<div id="lfb_preview">';
the_content();
echo '</div>'
?>
