<?php
/**
 * Plugin Name: Gravity Perks
 * Plugin URI: http://gravitywiz.com/2012/03/03/what-is-a-perk/?from=perks
 * Description: Effortlessly install and manage small functionality enhancements (aka "perks") for Gravity Forms.
 * Version: 1.2.9.1
 * Author: David Smith
 * Author URI: http://gravitywiz.com/
 * License: GPL2
 */

/**
 * Include the perk model as early as possible to when Perk plugins are loaded, they can safely extend
 * the GWPerk class.
 */
require_once( plugin_dir_path(__FILE__) . 'model/perk.php' );

/**
 * Used to hook into 'pre_update_option_active_plugins' filter and ensure that Gravity Perks
 * is loaded before any individual Perk plugins.
 */
register_activation_hook( __FILE__, array( 'GravityPerks', 'activation' ) );

add_action( 'init', array( 'GravityPerks', 'init' ) );
add_action( 'plugins_loaded', array( 'GravityPerks', 'init_perk_as_plugin_functionality' ) );

class GravityPerks {

    public static $version = '1.2.9.1';
    public static $tooltip_template = '<h6>%s</h6> %s';

    private static $basename;
    private static $slug = 'gravityperks';
    private static $url = 'http://gravitywiz.com/gravity-perks/';
    private static $min_gravity_forms_version = '1.8';
    private static $min_wp_version = '3.7';
    private static $api;

    /**
    * TODO: review...
    *
    * Need to store a modified version of the form object based the on the gform_admin_pre_render hook for use
    * in perk hooks.
    *
    * Example usage: GWPreventSubmit::add_form_setting()
    *
    * @var array
    */
    public static $form;

    /**
    * Set to true by the GWPerk class when any perk enqueues a form setting via the
    * GWPerk::enqueue_form_setting() function
    *
    * @var bool
    */
    public static $has_form_settings;

    /**
    * Set to true by the GWPerk class when any perk enqueues a field setting via the
    * GWPerk::enqueue_field_setting() function.
    *
    * @var bool
    */
    private static $has_field_settings;

    /**
    * When displaying a plugin row message, the first message display will also output a small style to fix the bottom
    * border styling issue which WP handles for plugins with updates, but not with notices.
    *
    * @see self::display_plugin_row_message()
    *
    * @var mixed
    *
    */
    private static $plugin_row_styled;

    // CACHE VARIABLES //

    private static $installed_perks;



    // INITIALIZE //

    public static function init() {

        self::define_constants();
        self::$basename = plugin_basename( __FILE__ );

        load_plugin_textdomain( 'gravityperks', false, '/gravityperks/languages' );

        self::maybe_setup();
        self::load_api();

        if(!self::is_gravity_forms_supported()) {
            return self::handle_error('gravity_forms_required');
        } else if(!self::is_wp_supported()) {
            return self::handle_error('wp_required');
        }

        self::register_scripts();

        if( is_admin() && !defined('DOING_AJAX') ) {
            global $pagenow;

            self::include_admin_files();

            // enqueue welcome pointer script
            add_action('admin_enqueue_scripts', array('GWPerks', 'welcome_pointer'));

            // show Perk item in GF menu
            add_filter('gform_addon_navigation', array('GWPerks', 'add_menu_item'));
            RGForms::add_settings_page('Perks', array('GWPerks', 'settings_page'));

            // show various plugin messages after the plugin row
            add_action('after_plugin_row_' . self::$basename, array('GWPerks', 'after_plugin_row'), 10, 2);

            if(self::is_gravity_perks_page()) {

                // all pages that should be loaded "before" admin
                switch( gwget('view') ) {

                case 'documentation':
                    require_once(self::get_base_path() . '/admin/manage_perks.php');
                    GWPerksPage::load_documentation();
                    break;

                case 'download':
                    require_once(self::get_base_path() . '/admin/download.php');
                    GWPerksDownload::process_actions();
                    break;

                case 'perk_info':
                    require_once(self::get_base_path() . '/admin/manage_perks.php');
                    GWPerksPage::load_perk_info();
                    break;

                case 'perk_settings':
                    require_once(self::get_base_path() . '/admin/manage_perks.php');
                    GWPerksPage::load_perk_settings();
                    break;

                default:
                    require_once(self::get_base_path() . '/admin/manage_perks.php');
                    add_thickbox();
                    GWPerksPage::process_actions();
                }

            }

            if( self::is_gravity_page(array('gf_edit_forms', 'gf_new_form')) ) {

                add_filter('gform_admin_pre_render', array('GWPerks', 'store_modified_form'), 11);
                add_action('gform_editor_js', array('GWPerks', 'add_form_editor_tabs'), 1);
                add_action('gform_editor_js', array('GWPerks', 'maybe_hide_perks_tab'), 99);

            }

        } else if(defined('DOING_AJAX') && DOING_AJAX) {

            add_action('wp_ajax_gwp_manage_perk', array('GWPerks', 'manage_perk'));

        } else {

            // front end actions...

        }

	    add_action( 'gform_logging_supported', array( __class__, 'enable_logging_support' ) );

        add_action( 'gform_field_standard_settings',   array( __class__, 'dynamic_setting_actions' ), 10, 2 );
        add_action( 'gform_field_appearance_settings', array( __class__, 'dynamic_setting_actions' ), 10, 2 );
        add_action( 'gform_field_advanced_settings',   array( __class__, 'dynamic_setting_actions' ), 10, 2 );

        // load and init all active perks
        self::initialize_perks();

    }

    public static function define_constants() {
        define( 'GW_STORE_URL',            'http://gravitywiz.com/gravity-perks/' ); // @used storefront_api.php
        define( 'GW_MANAGE_PERKS_URL',     admin_url( 'admin.php?page=gwp_perks' ) );
        define( 'GW_SETTINGS_URL',         admin_url( 'admin.php?page=gf_settings&addon=Perks&subview=Perks' ) );
        define( 'GW_REGISTER_LICENSE_URL', esc_url_raw( add_query_arg( array( 'register' => 1 ), GW_SETTINGS_URL ) ) );
        define( 'GW_SUPPORT_URL',          'http://gravitywiz.com/support/' );
        define( 'GW_BUY_GPERKS_URL',       'http://gravitywiz.com/gravity-perks/' );
        define( 'GW_GFORM_AFFILIATE_URL',  'http://bit.ly/gwizgravityforms' );
    }

    public static function activation() {
        self::init_perk_as_plugin_functionality();
    }

    /**
    * Get all active perks, load Perk objects, and initialize.
    *
    * By default, perks are only initialized by Gravity Perks. Since they are plugins they have the option to
    * initialize themselves; however, they will need to use a different init function name than "init" as this
    * will always be loaded by default.
    *
    * IF IS NETWORK ADMIN
    *     - only init network-activated perks
    *     - only handle errors for network-activated perks
    * IF IS SINGLE ADMIN
    *     - init network-activated perks and single-activated perks
    *     - only handles errors for
    *
    */
    private static function initialize_perks() {

        $network_perks = get_site_option('gwp_active_network_perks');

        // if on the network admin, only handle network-activated perks
        $perks = is_network_admin() ? array() : get_option('gwp_active_perks');

        if( !$network_perks )
            $network_perks = array();

        if( !$perks )
            $perks = array();

        $perks = array_merge($network_perks, $perks);

        foreach($perks as $perk_file => $perk_data) {

            $perk = GWPerk::get_perk($perk_file);

            if( is_wp_error($perk) ) {
                continue;
            }

            if( $perk->is_supported() ) {

                $perk->init();

            } else {

                foreach( $perk->get_failed_requirements() as $requirement ) {
                    self::handle_error( gwar( $requirement, 'code' ), $perk_file, gwar( $requirement, 'message' ) );
                }

            }

        }

    }

    /**
    * Include admin files required on all pages
    *
    */
    private static function include_admin_files() {
        require_once(self::get_base_path() . '/model/notice.php');
    }

    private static function maybe_setup() {

        // maybe set up Gravity Perks; only on admin requests for single site installs and always for multisite
        $is_non_ajax_admin = is_admin() && ( defined( 'DOING_AJAX' ) && DOING_AJAX === true ) === false;
        if( ! $is_non_ajax_admin && ! is_multisite() )
            return;

        $has_version_changed = get_option( 'gperks_version' ) != self::$version;

        // making sure version has really changed; gets around aggressive caching issue on some sites that cause setup to run multiple times
        if( $has_version_changed && is_callable( array( 'GFForms', 'get_wp_option' ) ) )
            $has_version_changed = GFForms::get_wp_option( 'gperks_version' ) != self::$version;

        if( ! $has_version_changed )
            return;

        self::setup();

    }

    private static function setup() {

        // force license to be revalidated
        self::flush_license();

        update_option( 'gperks_version', self::$version );

    }





    // CLASS INTERFACE //

    /**
    * Called by perks when the "Perks" field settings tab is required.
    */
    public static function enqueue_field_settings() {
        self::$has_field_settings = true;
    }



    // ERRORS AND NOTICES //

    private static function handle_error($error_slug, $plugin_file = false, $message = '') {
        global $pagenow;

        $plugin_file = $plugin_file ? $plugin_file : self::$basename;
        $is_perk = $plugin_file != self::$basename;
        $action = $is_perk ? array('GWPerks', 'after_perk_plugin_row') : array('GWPerks', 'after_plugin_row');

        // only display on plugins.php page when there is no action (ie 'delete-selected')
        $query_action = isset( $_GET['action'] ) ? $_GET['action'] : false;
        $is_plugins_page = $pagenow == 'plugins.php' && !$query_action;

        switch($error_slug) {

        case 'gravity_forms_required':

            // if GF is not supported, only show notices on GF pages and the plugins page
            if( !self::is_gravity_page() && !$is_plugins_page )
                return;

            $message = self::get_message($error_slug, $plugin_file);
            $message_function = create_function('', 'GWPerks::display_admin_message(\'<p>' . $message . '</p>\', \'error\');');

            add_action('admin_notices', $message_function);
            add_action('network_admin_notices', $message_function);
            add_action('after_plugin_row_' . $plugin_file, $action, 10, 2);

            break;

        case 'wp_required':

            // if WP min version is not met, only show notices on GF pages and the plugins page
            if( !self::is_gravity_page() && !$is_plugins_page )
                return;

            $message = self::get_message($error_slug, $plugin_file);
            $message_function = create_function('', 'GWPerks::display_admin_message(\'<p>' . $message . '</p>\', \'error\');');

            add_action('admin_notices', $message_function);
            add_action('network_admin_notices', $message_function);
            add_action('after_plugin_row_' . $plugin_file, $action, 10, 2);

            break;

        case 'gravity_perks_required':

            // if Gravity Perks is not supported, only show notices on Gravity Form page, Gravity Perk pages
            // and the WP plugins page; this case only applies to individual perks and not the core GP plugin
            if( !self::is_gravity_page() && !self::is_gravity_perks_page() && !$is_plugins_page )
                return;

            $message = self::get_message($error_slug, $plugin_file);
            $message_function = create_function('', 'GWPerks::display_admin_message(\'<p>' . $message . '</p>\', \'error\');');

            add_action('admin_notices', $message_function);
            add_action('network_admin_notices', $message_function);
            add_action('after_plugin_row_' . $plugin_file, $action, 10, 2);

            break;

        default:

            if( !$message || !$is_plugins_page )
                return;

            $message_function = create_function('', 'GWPerks::display_admin_message(\'<p>' . $message . '</p>\', \'error\');');

            add_action('admin_notices', $message_function);
            add_action('network_admin_notices', $message_function);
            add_action('after_plugin_row_' . $plugin_file, $action, 10, 2);

        }

        if( isset($message_function) )
            wp_enqueue_style('gwp-plugins', self::get_base_url() . '/styles/plugins.css' );

        return false;
    }

    public static function get_message($message_slug, $plugin_file = false) {

        $min_gravity_forms_version = self::$min_gravity_forms_version;
        $min_wp_version = self::$min_wp_version;

        // if a $plugin_file is provided AND it is not the same as the base plugin, let's assume it is a perk
        $is_perk = $plugin_file && $plugin_file != self::$basename;

        if( $is_perk ) {
            require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
            $perk = GWPerk::get_perk( $plugin_file );
            $perk_data = GWPerk::get_perk_data( $plugin_file );
            $min_gravity_forms_version = $perk->get_property('min_gravity_forms_version');
            $min_wp_version = $perk->get_property('min_wp_version');
        }

        switch($message_slug) {

        case 'gravity_forms_required':
            if(isset($perk)) {
                return sprintf(__('%1$s requires Gravity Forms %2$s or greater. Activate it now or %3$spurchase it today!%4$s', 'gravityperks'),
                    $perk_data['Name'], $min_gravity_forms_version, '<a href="' . GW_GFORM_AFFILIATE_URL . '">', '</a>');
            } else {
                return sprintf(__('Gravity Forms %1$s or greater is required. Activate it now or %2$spurchase it today!%3$s', 'gravityperks'),
                    $min_gravity_forms_version, '<a href="' . GW_GFORM_AFFILIATE_URL . '">', '</a>');
            }


        case 'wp_required':
            if(isset($perk)) {
                return sprintf(__('%1$s requires WordPress %2$s or greater. You must upgrade WordPress in order to use this perk.', 'gravityperks'),
                    $perk_data['Name'], $min_wp_version);
            } else {
                return sprintf(__('Gravity Perks requires WordPress %1$s or greater. You must upgrade WordPress in order to use Gravity Perks.', 'gravityperks'),
                    $min_wp_version);
            }

        case 'gravity_perks_required':
            return sprintf(__('%1$s requires Gravity Perks %2$s or greater. Activate it now or %3$spurchase it today!%4$s', 'gravityperks'),
                $perk_data['Name'], $perk->get_property('min_gravity_perks_version'), '<a href="' . GW_BUY_GPERKS_URL . '" target="_blank">', '</a>');

        case 'register_gravity_perks':
            if(isset($perk)) {
                return sprintf(__('%1$sRegister%2$s your copy of Gravity Perks to receive access to automatic upgrades and support for this perk. Need a license key? %3$sPurchase one now.%2$s', 'gravityperks'),
                    '<a href="' . admin_url('admin.php?page=gf_settings&addon=Perks') . '">', '</a>', '<a href="' . GW_BUY_GPERKS_URL . '" target="_blank">');
            } else {
                return sprintf(__('%1$sRegister%2$s your copy of Gravity Perks to receive access to automatic upgrades and support. Need a license key? %3$sPurchase one now.%2$s', 'gravityperks'),
                    '<a href="' . admin_url('admin.php?page=gf_settings&addon=Perks') . '">', '</a>', '<a href="' . GW_BUY_GPERKS_URL . '" target="_blank">');
            }

        }

        return '';
    }

    public static function after_plugin_row($plugin_file, $plugin_data) {

        if(!self::is_gravity_forms_supported()) {

            $message = self::get_message('gravity_forms_required');
            self::display_plugin_row_message($message, $plugin_data, true);

        }
        else if(!self::is_wp_supported()) {

            $message = self::get_message('wp_required');
            self::display_plugin_row_message($message, $plugin_data, true);

        }
        else {

            if(!self::has_valid_license()) {
                $message = self::get_message('register_gravity_perks');
                self::display_plugin_row_message($message, $plugin_data);
            }

        }

    }

    public static function after_perk_plugin_row($plugin_file, $plugin_data) {

        $perk = GWPerk::get_perk($plugin_file);

        if(is_wp_error($perk))
            return;

        if( !$perk->is_supported() ) {

            $messages = $perk->get_requirement_messages( $perk->get_failed_requirements() );
            $message = count($messages) > 1 ? '<ul><li>' . implode( '</li><li>', $messages ) . '</li></ul>' : $messages[0];
            self::display_plugin_row_message(  $message, $plugin_data, true );

        }
        else {

            if(!self::has_valid_license()) {
                $message = self::get_message('register_gravity_perks', $plugin_file);
                self::display_plugin_row_message($message, $plugin_data);
            }

        }

    }

    public static function display_admin_message($message, $class) {
        ?>

        <div id="message" class="<?php echo $class; ?> gwp-message"><?php echo $message; ?></div>

        <?php
    }

    public static function display_plugin_row_message( $message, $plugin_data, $is_error = false ) {

	    $id = sanitize_title( $plugin_data['Name'] );

	    ?>

	    <style type="text/css"> #<?php echo $id; ?> td, #<?php echo $id; ?> th { border-bottom: 0; } </style>

        <tr class="plugin-update-tr gwp-plugin-notice">
	        <td colspan="3" class="plugin-update">
				<div class="update-message"><?php echo $message; ?></div>
	        </td>
        </tr>

	    <?php
    }



    // IS SUPPORTED //

    public static function is_gravity_forms_supported($min_version = false) {
        $min_version = $min_version ? $min_version : self::$min_gravity_forms_version;
        return class_exists('GFCommon') && version_compare(GFCommon::$version, $min_version, '>=');
    }

    public static function is_wp_supported($min_version = false) {
        $min_version = $min_version ? $min_version : self::$min_wp_version;
        return version_compare(get_bloginfo('version'), $min_version, '>=');
    }

    public static function get_version() {
        return self::$version;
    }



    // PERKS AS PLUGINS //

    /**
    * Initalize all functionality that enables Perks to be plugins but also managed as perks.
    *
    */
    public static function init_perk_as_plugin_functionality() {

        add_filter('extra_plugin_headers', array( __class__, 'extra_perk_headers'));

        // any time a plugin is activated/deactivated, refresh the list of active perks
        add_action('update_site_option_active_sitewide_plugins', array(__class__, 'refresh_active_sitewide_perks'));
        add_action('update_option_active_plugins', array(__class__, 'refresh_active_perks'));

        // any time a plugin is activated/deactivated, reorder plugin loading to load Gravity Perks first
        add_filter('pre_update_site_option_active_sitewide_plugins', array(__class__, 'reorder_plugins'));
        add_filter('pre_update_option_active_plugins', array( __class__, 'reorder_plugins'));

        // add "manage perks" link after perk install/update
        add_filter('install_plugin_complete_actions', array(__class__, 'add_manage_perks_action'), 10, 2);
        add_filter('update_plugin_complete_actions', array(__class__, 'add_manage_perks_action'), 10, 2);

        // display "back to perks" link on plugins page
        add_action('pre_current_active_plugins', array(__CLASS__, 'display_perks_status_message'));

        // when deleting plugins, output a script to update the "No, Return me to the plugin list" button verbiage
        add_action('admin_action_delete-selected', array( __class__, 'maybe_add_perk_delete_confirmation' ) );

        if( is_multisite() ) {

            // prevent perks from being network activated if Gravity Perks is not network activated, priority 11 so it fires after 'save_last_modified_plugin'
            add_action('admin_action_activate', array( __class__, 'require_gravity_perks_network_activation' ), 11);

        }

        // save last modified plugin (installed, deleted, activated, deactivated) and save blog ID requesting action
        self::setup_last_modified_functions();

	    do_action( 'gperks_loaded' );

    }

    public static function setup_last_modified_functions() {

        foreach( self::get_plugin_actions() as $action ) {
            add_action("admin_action_{$action}", array( __class__, 'save_last_modified_plugin') );
            if( is_multisite() )
                add_action("admin_action_{$action}", array( __class__, 'save_requesting_blog_id') );
        }

    }

    public static function add_manage_perks_action( $actions, $plugin_file ) {

        if( !GWPerks::is_last_modified_plugin_perk() )
            return $actions;

        // if we're coming from Manage Perk's page...
        if( self::is_request_from_gravity_perks() ) {
            $actions['manage_perks'] = '<a href="' . GW_MANAGE_PERKS_URL . '">' . __('Back to Manage Perks page', 'gravityperks') . '</a>';
            unset($actions['plugins_page']);
        } else {
            $actions['manage_perks'] = '<a href="' . GW_MANAGE_PERKS_URL . '">' . __('Manage Perks', 'gravityperks') . '</a>';
        }

        if( isset( $actions['activate_plugin'] ) )
            $actions['activate_plugin'] = str_replace( __( 'Activate Plugin' ), __( 'Activate Perk', 'gravityperks' ), $actions['activate_plugin'] );

        return $actions;
    }

    /**
    * Pull the "Perk" header out of the plugin header data. Used to determine if the plugin is intended to be
    * run by Gravity Perks.
    *
    */
    public static function extra_perk_headers( $headers ) {
	    array_push( $headers, 'Perk' );
        return $headers;
    }

    /**
    * Refresh the list of active perks. Triggered anytime the "active_plugins" or "active_sitewide_plugins" option is updated.
    * This option is updated anytime a plugin is activated or deactivated.
    *
    */
    public static function refresh_active_perks($old_value) {

        $plugins = self::get_plugins();
        $perks = array();
        $network_perks = array();

        foreach($plugins as $plugin_file => $plugin) {

            // skip all non-perk plugins
            if( gwar($plugin, 'Perk') != 'True' )
                continue;

            if( is_multisite() && is_plugin_active_for_network($plugin_file) ) {
                $network_perks[$plugin_file] = $plugin;
            } else if( is_plugin_active($plugin_file) ) {
                $perks[$plugin_file] = $plugin;
            }

        }

        // if multsite, update network perks
        if( is_multisite() )
            update_site_option('gwp_active_network_perks', $network_perks);

        // update active perks every time
        update_option('gwp_active_perks', $perks);

    }

    public static function refresh_active_sitewide_perks($old_value) {
        self::refresh_active_perks($old_value);
    }

    /**
    * Update plugin loading order. Anytime the "active_plugins" option is updated, this function reorders the plugins, placing
    * Gravity Perks as the first plugin to load to ensure that it is loaded before any individual Perk plugin.
    *
    */
    public static function reorder_plugins($plugins) {

        $perks_file = plugin_basename(__FILE__);

        $index = array_search($perks_file, $plugins);
        if($index === false)
            $index = array_key_exists($perks_file, $plugins) ? $perks_file : false;

        if($index === false)
            return $plugins;

        $perks_item = array($index => $plugins[$index]);
        unset($plugins[$index]);

        if(is_numeric($index)) {
            array_unshift($plugins, $perks_file);
        } else {
            $plugins = array_merge($perks_item, $plugins);
        }

        return $plugins;
    }

    public static function save_last_modified_plugin($value) {

	    $plugins = array();

        switch( gwget('action') ) {
        case 'activate':
        case 'deactivate':
        case 'install-plugin':
            $plugins = array( gwar( $_REQUEST, 'plugin' ) );
            break;
        case 'delete-selected':
            $plugins = $_REQUEST['checked'];
            break;
        }

        $is_perk = gwget( 'from' ) == 'gwp' || self::is_plugin_file_perk( $plugins );

        update_option( 'gperk_last_modified_plugin', $plugins );
        update_option( 'gperk_is_last_modified_plugin_perk', $is_perk );

    }

    public static function get_last_modified_plugin() {
        return get_option('gperk_last_modified_plugin');
    }

    public static function is_last_modified_plugin_perk() {
        return get_option( 'gperk_is_last_modified_plugin_perk' ) == true;
    }

    public static function save_requesting_blog_id($value) {
        $blog_id = isset($_REQUEST['blog_id']) ? $_REQUEST['blog_id'] : false;
        update_option( 'gperk_requestee_blog_id', $blog_id );
    }

    public static function get_requesting_blog_id() {
        return get_option('gperk_requestee_blog_id');
    }

    public static function get_plugin_actions() {
        return array('install-plugin', 'delete-selected', 'deactivate', 'activate');
    }

    public static function display_perks_status_message() {

        foreach(array('activate', 'deleted', 'deactivate', 'install', 'gwp_error') as $action) {
            if( isset($_GET[$action]) ) {
                $current_action = $action;
                break;
            }
        }

        if(!isset($current_action))
            return;

        $blog_id = is_multisite() ? self::get_requesting_blog_id() : false;
        $is_perk = self::is_last_modified_plugin_perk();
        $is_error = false;
        $message = '';

        if( !$is_perk )
            return;

        switch($current_action) {
        case 'activate':
            $message = __('You\'ve just activated a <strong>perk</strong>. ', 'gravityperks');
            break;
        case 'deactivate':
            $message = __('You\'ve just deactivated a <strong>perk</strong>. ', 'gravityperks');
            break;
        case 'deleted':
            $delete_result = get_transient('plugins_delete_result_' . get_current_user_id() );
            if( !is_wp_error($delete_result) )
                $message = __('You\'ve just deleted a <strong>perk</strong>. ', 'gravityperks');
            break;
        case 'gwp_error':
            $is_error = true;
            switch(gwget('gwp_error')) {
            case 'networkperks':
                $message = __('Gravity Perks must be network activated before a <strong>perk</strong> can be network activated.', 'gravityperks');
                break;
            }
        }

        if( !$is_error ) {
            if( is_multisite() ) {
                $site_select = self::get_manage_perks_site_select();
                $message .= sprintf( __('Manage all your perks on the %sManage Perks%s %s page.', 'gravityperks'),
                    '<a href="javascript:void(0);" onclick="jQuery(this).hide(); jQuery(\'#manage-perks-site-select\').show();">', '</a>', $site_select);
            } else {
                $message .= sprintf( __('Manage all your perks on the %sManage Perks%s page.', 'gravityperks'), '<a href="' . get_admin_url($blog_id, 'admin.php?page=gwp_perks') . '">', '</a>' );
            }
        }

        ?>

        <div class="<?php echo $is_error ? 'error' : 'updated'; ?> gwp-message">
            <p><?php echo $message; ?></p>
        </div>

        <style type="text/css">
            #message + div.gwp-message { margin-top:-17px; border-top-style: dotted;
                border-top-right-radius: 0; border-top-left-radius: 0; }
        </style>

        <?php

    }

    public static function maybe_add_perk_delete_confirmation() {
        // only show 'Return to Manage Perks Page' button if request came from GPerks
        if(  self::is_request_from_gravity_perks() )
            add_action('in_admin_footer', array( __class__, 'add_perk_delete_confirmation_script' ) );
    }

    /**
    * Add 'Return to Manage Perks Page' button on plugin delete confirmation screen.
    *
    * There is no way to filter the default buttons on this screen, so let's add our own button and hide
    * the existing button via JS.
    *
    */
    public static function add_perk_delete_confirmation_script() {
        ?>

        <script type="text/javascript">
            (function($){

                var cancelButton = $('input[value="<?php _e( 'No, Return me to the plugin list' ); ?>"]');
                var perksButton = $('<input value="<?php _e('No, Return to Manage Perks page', 'gravityperks'); ?>" type="submit" class="button" />');
                perksButton.insertAfter(cancelButton);
                cancelButton.hide();

            })(jQuery);
        </script>

        <?php
    }

    public static function get_manage_perks_site_select() {
        $blogs = get_blogs_of_user( get_current_user_id() );
        $site_select = '
            <span id="manage-perks-site-select" style="display:none;">
                <select onchange="if(this.value != \'\') { window.location.href = this.value };">
                    <option value="">Select Site</option>';

        foreach($blogs as $blog) {
            $site_select .= '<option value="' . get_admin_url($blog->userblog_id, 'admin.php?page=gwp_perks') . '">' . $blog->blogname . '</option>';
        }

        $site_select .= '
                </select>
            </span>';

        return $site_select;
    }

    public static function require_gravity_perks_network_activation() {

        if( ! is_network_admin() || ! self::is_last_modified_plugin_perk() || self::is_gravity_perks_network_activated() ) {
	        return;
        }

        $plugin = gwar($_REQUEST, 'plugin');
        $redirect = self_admin_url( 'plugins.php?gwp_error=networkperks&plugin=' . $plugin );
        wp_redirect( esc_url_raw( add_query_arg( '_error_nonce', wp_create_nonce('plugin-activation-error_' . $plugin ), $redirect ) ) );
        exit;

    }

    public static function is_gravity_perks_network_activated() {

        if( !is_multisite() )
            return false;

        foreach( wp_get_active_network_plugins() as $plugin ) {
            $plugin_file = plugin_basename( $plugin );
            if( plugin_basename(__file__) == $plugin_file  && is_plugin_active_for_network( $plugin_file ) )
                return true;
        }

        return false;
    }



    // API & LICENSING //

    public static function load_api() {
        require_once( dirname( __FILE__ ) . '/includes/storefront_api.php' );
        self::$api = new GWAPI( array(
            'plugin_file' => plugin_basename(__FILE__),
            'version' => GWPerks::get_version(),
            'license' => GWPerks::get_license_key(),
            'item_name' => 'Gravity Perks',
            'author' => 'David Smith',
            ));
    }

    public static function get_api() {
        return self::$api;
    }

    public static function has_valid_license( $flush = false ) {
        return self::$api->has_valid_license( $flush );
    }

    public static function flush_license() {
        delete_transient( 'gwp_has_valid_license' );
    }

    public static function get_license_key() {
        $settings = get_site_option('gwp_settings');
        return isset( $settings['license_key'] ) ? trim( $settings['license_key'] ) : false;
    }

    /**
    * Returns a complete list of available perks from API.
    *
    */
    public static function get_available_perks() {
        $perks = self::$api->get_perks();
        return !$perks ? array() : $perks;
    }

    /**
    * Retrieve all installed perks.
    *
    */
    public static function get_installed_perks() {

        if(!empty(self::$installed_perks))
            return self::$installed_perks;

        $plugins = self::get_plugins();
        $perks = array();

        foreach($plugins as $plugin_file => $plugin_data) {
            if(isset($plugin_data['Perk']) && $plugin_data['Perk'])
                $perks[$plugin_file] = $plugin_data;
        }

        return $perks;
    }



    // WP ADMIN INTEGRATION //

    /**
    * Hook into Gravity Forms menu and add "Perks" as a submenu item.
    *
    * @param mixed $addon_menus
    */
    public static function add_menu_item($addon_menus) {

        $menu = array(
            'label' => __('Perks', 'gravityperks'),
            'permission' => 'administrator',
            'name' => 'gwp_perks',
            'callback' => array('GWPerks', 'load_page')
            );

        $addon_menus[] = $menu;

        return $addon_menus;
    }

    public static function settings_page() {
        require_once(self::get_base_path() . '/admin/settings.php');
        GWPerksSettings::settings_page();
    }

    /**
    * @TODO: might not be used...
    *
    * Hook into WP and modify the actions available after installing a perk plugin.
    *
    */
    public static function install_plugin_complete_actions($install_actions, $api, $plugin_file) {

        if(!isset($api->is_perk) || !$api->is_perk)
            return $install_actions;

        unset($install_actions['plugins_page']);

        $perks_page_url = gwget('blog_id') ? get_admin_url(gwget('blog_id'), 'admin.php?page=gwp_perks') : GW_MANAGE_PERKS_URL;
        $install_actions['perks_page'] = '<a href="' . $perks_page_url . '" title="' . __('Return to Perks Page', 'gravityperks') . '" target="_parent">' . __('Return to Perks Page', 'gravityperks') . '</a>';

        return $install_actions;
    }

    /**
    * Register scripts and init the gperk object
    *
    */
    public static function register_scripts() {

        wp_register_style('gwp-admin', self::get_base_url() . '/styles/admin.css');

        wp_register_script( 'gwp-common',   self::get_base_url() . '/scripts/common.js',   array( 'jquery' ), GravityPerks::$version );
        wp_register_script( 'gwp-admin',    self::get_base_url() . '/scripts/admin.js',    array( 'jquery', 'gwp-common' ), GravityPerks::$version );
        wp_register_script( 'gwp-frontend', self::get_base_url() . '/scripts/frontend.js', array( 'jquery', 'gwp-common' ), GravityPerks::$version );
        wp_register_script( 'gwp-repeater', self::get_base_url() . '/scripts/repeater.js', array( 'jquery' ), GravityPerks::$version );

        // register our scripts with Gravity Forms so they are not blocked when noconflict mode is enabled
        add_filter( 'gform_noconflict_scripts', create_function('$scripts', 'return array_merge($scripts, array("gwp-admin", "gwp-frontend", "gwp-common"));') );
        add_filter( 'gform_noconflict_styles', create_function('$styles', 'return array_merge($styles, array("gwp-admin"));') );

        require_once(GFCommon::get_base_path() . '/currency.php');

        wp_localize_script('gwp-common', 'gperk', array(
            'baseUrl' => self::get_base_url(),
            'gformBaseUrl' => GFCommon::get_base_url(),
            'currency' => RGCurrency::get_currency(GFCommon::get_currency())
            ));

        add_action('admin_enqueue_scripts', array('GWPerks', 'enqueue_scripts'));

    }

    /**
    * Enqueue Javascript
    *
    * In the admin, include admin.js (and common.js by dependency) on all Gravity Form and Gravity Perk pages.
    * On the front-end, common.js and frontend.js are included when enqueued by a perk.
    *
    */
    public static function enqueue_scripts() {

        GWPerks::enqueue_styles();

        if(self::is_gravity_perks_page() || self::is_gravity_page() ) {
            wp_enqueue_script('gwp-admin');
        }

    }

    public static function enqueue_styles() {

        if(self::is_gravity_perks_page() || self::is_gravity_page() ) {
            wp_enqueue_style('gwp-admin');
        }

    }



    // AJAX //

    public static function manage_perk() {
        require_once(GWPerks::get_base_path() . '/admin/manage_perks.php');
        GWPerksPage::ajax_manage_perk();
    }

    public static function json_and_die($data) {
        echo json_encode($data);
        die();
    }



    // HELPERS //

    public static function get_base_url(){
        $folder = basename(dirname(__FILE__));
        return plugins_url( $folder );
    }

    public static function get_base_path(){
        $folder = basename(dirname(__FILE__));
        return WP_PLUGIN_DIR . "/" . $folder;
    }

    public static function is_gravity_page() {
        return class_exists( 'RGForms' ) ? RGForms::is_gravity_page() : false;
    }

    private static function is_gravity_perks_page($page = false){

        $current_page = self::get_current_page();
        $gp_pages = array('gwp_perks', 'gwp_settings');

        if($page)
            return $current_page == $page;

        return in_array($current_page, $gp_pages);
    }

    public static function is_plugin_file_perk( $plugin ) {

        $plugins = is_array($plugin) ? $plugin : array($plugin);

        foreach($plugins as $plugin) {
            if( GWPerk::is_perk( $plugin ) )
                return true;
        }

        return false;
    }

    public static function is_request_from_gravity_perks() {
        return isset( $_GET['gwp'] ) || ( isset( $_GET['from'] ) && $_GET['from'] == 'gwp' );
    }

    /**
     * Play it safe and require WP's plugin.php before calling the get_plugins() function.
     *
     * @return array An array of installed plugins.
     */
    public static function get_plugins( $clear_cache = false ) {
        require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

        $plugins = get_plugins();

        if( $clear_cache || ! self::plugins_have_perk_plugin_header( $plugins ) ) {
            wp_cache_delete( 'plugins', 'plugins' );
            $plugins = get_plugins();
        }

        return $plugins;
    }

    /**
    * Confirm whether the our custom plugin header 'Perk' is available.
    *
    * When activating Gravity Perks, the plugin cache has already been created without the custom 'Perk' header.
    *
    */
    public static function plugins_have_perk_plugin_header( $plugins ) {
        $plugin = reset( $plugins );
        return $plugin && isset( $plugin['Perk'] );
    }

    /**
    * Handle showing welcome pointer.
    *
    */
    public static function welcome_pointer() {

        $dismissed = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );

        if(in_array('gwp_welcome', $dismissed) || self::is_gravity_perks_page() )
            return;

        wp_enqueue_style( 'wp-pointer' );
        wp_enqueue_script( 'wp-pointer' );

        add_action('admin_print_footer_scripts', array('GWPerks', 'welcome_pointer_script'));

    }

    public static function welcome_pointer_script() {

        $pointer_content = '<h3>' . __('Welcome to Gravity Perks', 'gravityperks') . '</h3>';
        $pointer_content .= '<p>' . __('Good to see you running Gravity Perks! Click the <strong>Perks</strong> link (to the left) to take a quick tour.', 'gravityperks') . '</p>';
        ?>

        <script type="text/javascript">
        //<![CDATA[
        jQuery(document).ready( function($) {
            $('.wp-submenu a[href="admin.php?page=gwp_perks"]').pointer({
                content: '<?php echo $pointer_content; ?>',
                position: {
                    edge: 'left',
                    align: 'center'
                },
                open: function(event, elements) {
                    elements.element.css('backgroundColor', 'rgba( 255, 255, 255, 0.15' );
                },
                close: function(event, elements) {
                    $.post( ajaxurl, {
                        pointer: 'gwp_welcome',
                        action: 'dismiss-wp-pointer'
                    });
                    elements.element.css('backgroundColor', 'transparent');
                    $('a[href="#manage"]').pointer('open');
                }
            }).pointer('open');
        });
        //]]>
        </script>

        <?php
    }

    public static function is_pointer_dismissed( $pointer_name ) {
        $dismissed = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );
        return in_array( $pointer_name, $dismissed );
    }

    public static function dismiss_pointer( $pointer ) {

        if( is_array( $pointer ) ) {
            foreach( $pointer as $pntr ) {
                self::dismiss_pointer( $pntr );
            }
        } else {

            $dismissed = array_filter( explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) ) );
            if ( in_array( $pointer, $dismissed ) )
                return;

            $dismissed[] = $pointer;
            $dismissed = implode( ',', $dismissed );

            update_user_meta( get_current_user_id(), 'dismissed_wp_pointers', $dismissed );

        }

    }

    public static function format_changelog( $string ) {
        $string = wp_strip_all_tags( $string );
        $string = stripslashes( $string );
        $string = implode( "\n", array_map( 'trim', explode( "\n", $string ) ) );
        return self::markdown( $string );
    }

    public static function markdown( $string ) {

        if( !function_exists('Markdown') )
            require_once( GWPerks::get_base_path() . '/includes/markdown.php' );

        return Markdown( $string );
    }

    public static function apply_filters( $filter_base, $modifiers, $value ) {

        if( ! is_array( $modifiers ) )
            $modifiers = array( $modifiers );

        array_unshift( $modifiers, '' );

        $args = array_slice( func_get_args(), 3 );

        // apply default filter first
        $value = self::call_apply_filters( $filter_base, $value, $args );

        // apply modified versions of filter
        foreach( $modifiers as $modifier ) {
            $value = self::call_apply_filters( "{$filter_base}_{$modifier}", $value, $args );
        }

        return $value;
    }

    private static function call_apply_filters( $filter_name, $value, $args ) {
        return apply_filters( $filter_name, $value,
                isset( $args[0] ) ? $args[0] : null,
                isset( $args[1] ) ? $args[1] : null,
                isset( $args[2] ) ? $args[2] : null,
                isset( $args[3] ) ? $args[3] : null,
                isset( $args[4] ) ? $args[4] : null,
                isset( $args[5] ) ? $args[5] : null,
                isset( $args[6] ) ? $args[6] : null,
                isset( $args[7] ) ? $args[7] : null,
                isset( $args[8] ) ? $args[8] : null,
                isset( $args[9] ) ? $args[9] : null,
                isset( $args[10] ) ? $args[10] : null
                );
    }

    public static function dynamic_setting_actions( $position, $form_id ) {

	    $action = current_filter() . '_' . $position;

        if( did_action( $action ) < 1 ) {
            do_action( $action, $form_id );
            //echo $action . '<br />';
        }
    }

    public static function drop_tables( $tables ) {
        global $wpdb;

        $tables = is_array( $tables ) ? $tables : array( $tables );

        foreach( $tables as $table ) {
            $wpdb->query( "DROP TABLE IF EXISTS {$table}" );
        }

    }



	// LOGGING //

	public static function enable_logging_support( $plugins ) {
		$plugins['gravityperks'] = __( 'Gravity Perks', 'gravityperks' );
		return $plugins;
	}

	public static function log_error($message) {
		if( class_exists( 'GFLogging' ) ) {
			GFLogging::include_logger();
			GFLogging::log_message( 'gravityperks', $message, KLogger::ERROR );
		}
	}

	public static function log_debug($message) {
		if( class_exists( 'GFLogging' ) ) {
			GFLogging::include_logger();
			GFLogging::log_message( 'gravityperks', $message, KLogger::DEBUG );
		}
	}










    // REEVALUATE ALL CODE BELOW THIS LINE //





    public static function load_page() {
        if(gwget('view') == 'download') {
            require_once(self::get_base_path() . '/admin/download.php');
            GWPerksDownload::load_page();
        }
        else {
            require_once(self::get_base_path() . '/admin/manage_perks.php');
            GWPerksPage::load_page();
        }

    }


    public static function add_perk_dir() {;

        if(!file_exists(GWP_PERKS_DIR))
            return mkdir(GWP_PERKS_DIR, 0755);

        return true;
    }

    private static function get_current_page() {
        return $current_page = trim(strtolower(gwget('page')));
    }

    public static function is_local() {
        return $_SERVER['REMOTE_ADDR'] == '127.0.0.1';
    }



    public static function flush_installed_perks() {
        delete_site_option('gwp_installed_perks');
    }

    public static function perk_row($perk) {

        $all_perks = self::get_perks_listing();
        $current_version = $perk->meta['version'];
        $available_version = self::get_latest_version($perk->slug);
        $message = '';

        // new version of perk is available
        if( version_compare( $current_version, $available_version, '<' ) ) {

            $details_url = $perk->get_link_for('upgrade_details');
            $message = sprintf( __( 'There is a new version of %s available.', 'gravityperks' ), $perk->meta['name'] );

            if( self::has_valid_key() ) {
                $message .= sprintf(__('%1$sView version %2$s details%3$s or %4$supdate automatically%3$s.', 'gravityperks'),
                    '<a href="' . $details_url . '" class="thickbox">',
                    self::get_latest_version($perk->slug),
                    '</a>',
                    '<a href="' . $perk->get_link_for('upgrade') . '">'
                    );
            } else {
                $message .= sprintf(__('%1$sView version %2$s details%3$s or %4$sregister your copy of Gravity Perks%3$s.', 'gravityperks'),
                    '<a href="' . $details_url . '" class="thickbox">',
                    self::get_latest_version($perk->slug),
                    '</a>',
                    '<a href="' . $perk->get_link_for('plugin_settings') . '">'
                    );
            }

        } else if(!$perk->has_min_perks_version()) {
            $wp_updates_url = admin_url('update-core.php');
            $message = sprintf(__('Gravity Perks %s is required. <a href="%s">Update Gravity Perks</a>.', 'gravityperks'), $perk->get_property('min_perks_version'), $wp_updates_url);
        } else if(!$perk->has_min_gforms_version()) {
            $wp_updates_url = admin_url('update-core.php');
            $message = sprintf(__('Gravity Forms %s is required. <a href="%s">Update Gravity Forms</a>.', 'gravityperks'), $perk->get_property('min_gforms_version'), $wp_updates_url);
        }

        if($message) {

            $wp_list_table = new GWPerksTable();

            echo '<tr class="plugin-update-tr"><td colspan="' . $wp_list_table->get_column_count() . '" class="plugin-update colspanchange"><div class="update-message" style="background-color:#FFEBE8;">';
            echo $message;
            echo '</div></td></tr>';

        }

    }

    public static function get_latest_version($slug) {

        $all_perks = self::get_perks_listing();

        if(!$all_perks)
            return false;

        foreach($all_perks as $perk) {
            if($perk['slug'] == $slug)
                return $perk['version'];
        }

        return false;
    }


    /**
    * Adds a new "Perks" tab to the form and/or field settings where perk objects can
    * will load their form settings
    *
    * @param array $form: GF Form object
    */
    public static function add_form_editor_tabs() {

        if( ! self::$has_form_settings && ! self::$has_field_settings )
            return;

        ?>

        <style type="text/css">
            .gws-child-setting { display:none; padding: 10px 0 10px 15px; margin: 6px 0 0 6px; border-left: 2px solid #eee; }
        </style>

        <script type="text/javascript">

        jQuery(document).ready(function($){

            <?php if(self::$has_form_settings): ?>
                gperk.addTab( $('#form_settings'), '#gws_form_tab', '<?php _e('Perks', 'gravityperks') ?>');
            <?php endif; ?>

            <?php if(self::$has_field_settings): ?>
                gperk.addTab( $('#field_settings'), '#gws_field_tab', '<?php _e('Perks', 'gravityperks') ?>');
            <?php endif; ?>

        });

        </script>

        <?php if( self::$has_form_settings ): ?>
        <div id="gws_form_tab">
            <ul class="gforms_form_settings">
                <?php do_action( 'gws_form_settings' ); ?>
            </ul>
        </div>
        <?php endif; ?>

        <?php if( self::$has_field_settings ): ?>
        <div id="gws_field_tab">
            <ul class="gforms_field_settings">
                <?php do_action( 'gws_field_settings' ); ?>
                <?php do_action( 'gperk_field_settings' ); ?>
            </ul>
        </div>
        <?php endif; ?>

        <?php
    }

    public static function maybe_hide_perks_tab() {
        ?>

        <script type="text/javascript">
        /**
        * Hide custom field settings tab if no settings are displayed for the selected field type
        */
        jQuery(document).bind( 'gform_load_field_settings', function( event, field ) {
            // show/hide the "no settings" message
            gperk.togglePerksTab()
        });
        </script>

        <?php
    }

    public static function store_modified_form( $form ) {
        self::$form = $form;
        return $form;
    }




    /**
    * Get all perk options or optionally specify a slug to get a specific perk's options.
    * If slug provided and no options found, return default perk options.
    *
    * @param mixed $slug Perk slug
    * @return Perk options array or array of of perk options arrays
    */
    public static function get_perk_options($slug = false) {

        $all_perk_options = get_option('gwp_perk_options');

        if(!$all_perk_options)
            $all_perk_options = array();

        if($slug) {
            foreach($all_perk_options as $perk_options) {
                if($perk_options['slug'] == $slug)
                    return $perk_options;
            }
            require_once(self::get_base_path() . '/model/perk.php');
            return GWPerk::get_default_perk_options($slug);
        }

        return $all_perk_options;
    }

    public static function get_options_from_installed_perks() {

        $perks = GWPerks::get_installed_perks();
        $all_perk_options = array();

        foreach($perks as $perk) {
            $all_perk_options[] = $perk->get_save_options();
        }

        return $all_perk_options;
    }

    public static function update_perk_option($updated_options) {

        $all_perk_options = self::get_perk_options();
        $is_new = true;

        foreach($all_perk_options as &$perk_options) {

            if($perk_options['slug'] == $updated_options['slug']) {
                $is_new = false;
                $perk_options = $updated_options;
            }

        }

        if($is_new)
            $all_perk_options[$updated_options['slug']] = $updated_options;

        return update_option('gwp_perk_options', $all_perk_options);
    }

    public static function is_debug() {

        $enabled_via_constant = defined( 'GP_DEBUG' ) && GP_DEBUG;
        $enabled_via_query = isset( $_GET['gp_debug'] ) && current_user_can( 'update_core' );

        return $enabled_via_constant || $enabled_via_query;
    }

}

class GWPerks extends GravityPerks { }

if( !function_exists( 'print_rr' ) ) {
    function print_rr( $array ) {
        echo '<pre>';
        print_r( $array );
        echo '</pre>';
    }
}

if( !function_exists( 'gwget' ) ) {
    function gwget( $name ) {
        return gwar( $_GET, $name );
    }
}

if( !function_exists( 'gwpost' ) ) {
    function gwpost( $name ) {
        return gwar( $_POST, $name );
    }
}

if( !function_exists( 'gwar' ) ) {
    function gwar( $array, $name ) {
        return isset( $array[$name] ) ? $array[$name] : '';
    }
}

if( !function_exists( 'gwars' ) ) {
    function gwars( $array, $name ) {
        $names = explode( '/', $name );
        $val = $array;
        foreach( $names as $current_name ) {
            $val = gwar( $val, $current_name );
        }
        return $val;
    }
}