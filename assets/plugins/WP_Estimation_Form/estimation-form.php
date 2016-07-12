<?php
/*
 * Plugin Name: WP Estimation & Payment Forms
 * Version: 9.1.95
 * Plugin URI: http://codecanyon.net/item/wp-flat-estimation-payment-forms-/7818230
 * Description: This plugin allows you to create easily flat visual forms of paypal payment / cost estimation.
 * Author: Biscay Charly (loopus)
 * Author URI: http://www.loopus-plugins.com/
 * Requires at least: 3.8
 * Tested up to: 4.2.2
 *
 * @package WordPress
 * @author Biscay Charly (loopus)
 * @since 1.0.0
 */

if (!defined('ABSPATH'))
    exit;

register_activation_hook(__FILE__, 'lfb_install');
register_uninstall_hook(__FILE__, 'lfb_uninstall');

global $jal_db_version;
$jal_db_version = "1.1";

require_once('includes/lfb-core.php');
require_once('includes/lfb-admin.php');

function Estimation_Form()
{
    $version = 9.195;
    lfb_checkDBUpdates($version);
    $instance = LFB_Core::instance(__FILE__, $version);
    if (is_null($instance->menu)) {
        $instance->menu = LFB_admin::instance($instance);
    }

    return $instance;
}

/**
 * Installation. Runs on activation.
 * @access  public
 * @since   1.0.0
 * @return  void
 */
function lfb_install()
{
    global $wpdb;
    global $jal_db_version;
    require_once(ABSPATH . '/wp-admin/includes/upgrade.php');

    add_option("jal_db_version", $jal_db_version);

    $db_table_name = $wpdb->prefix . "wpefc_forms";
    if ($wpdb->get_var("SHOW TABLES LIKE '$db_table_name'") != $db_table_name) {
        if (!empty($wpdb->charset))
            $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
        if (!empty($wpdb->collate))
            $charset_collate .= " COLLATE $wpdb->collate";

        $sql = "CREATE TABLE $db_table_name (
		        id mediumint(9) NOT NULL AUTO_INCREMENT,
		          title VARCHAR(120) NOT NULL,
              errorMessage VARCHAR(240) NOT NULL,
                intro_enabled BOOL,
                save_to_cart BOOL,
                use_paypal BOOL NOT NULL,
                paypal_email VARCHAR(250) NULL,
                paypal_currency VARCHAR(3) NOT NULL DEFAULT 'USD',
                close_url VARCHAR(250) NOT NULL DEFAULT '#',
                btn_step VARCHAR(120) NOT NULL,
                previous_step VARCHAR(120) NOT NULL,
                intro_title VARCHAR(120) NOT NULL,
                intro_text TEXT NOT NULL,
                intro_btn VARCHAR(120) NOT NULL,
                last_title VARCHAR(120) NOT NULL,
                last_text TEXT NOT NULL,
                last_btn VARCHAR(120) NOT NULL,
                last_msg_label VARCHAR(240) NOT NULL,
                initial_price FLOAT NOT NULL,
                max_price FLOAT NOT NULL,
                succeed_text TEXT NOT NULL,
                email VARCHAR(250) NOT NULL,
                email_adminContent TEXT NOT NULL,
                email_subject VARCHAR(250) NOT NULL,
                email_toUser BOOL NOT NULL,
                email_userSubject VARCHAR(250) NOT NULL,
                email_userContent TEXT NOT NULL,
                currency VARCHAR (32) NOT NULL,
                currencyPosition VARCHAR (32) NOT NULL,
                gravityFormID INT(9) NOT NULL,
                animationsSpeed FLOAT NOT NULL DEFAULT 0.5,
                showSteps BOOL NOT NULL,
                qtType BOOL NOT NULL,
                show_initialPrice BOOL NOT NULL,
                ref_root VARCHAR(16) NOT NULL DEFAULT 'A000',
                current_ref INT(8) NOT NULL DEFAULT 1,
                colorA VARCHAR(16) NOT NULL,
                colorB VARCHAR(16) NOT NULL,
                colorC VARCHAR(16) NOT NULL,
                item_pictures_size SMALLINT(9) NOT NULL,
                hideFinalPrice BOOL NOT NULL DEFAULT 0,
                priceFontSize SMALLINT NOT NULL DEFAULT 18,
                customCss TEXT NOT NULL,
                disableTipMobile BOOL NOT NULL,
                legalNoticeContent TEXT NOT NULL,
                legalNoticeTitle TEXT NOT NULL,
                legalNoticeEnable BOOL NOT NULL,
		UNIQUE KEY id (id)
		) $charset_collate;";
        dbDelta($sql);
    }

    $db_table_name = $wpdb->prefix . "wpefc_steps";
    if ($wpdb->get_var("SHOW TABLES LIKE '$db_table_name'") != $db_table_name) {
        if (!empty($wpdb->charset))
            $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
        if (!empty($wpdb->collate))
            $charset_collate .= " COLLATE $wpdb->collate";

        $sql = "CREATE TABLE $db_table_name (
    		id mediumint(9) NOT NULL AUTO_INCREMENT,
    		formID mediumint (9) NOT NULL,
    		start BOOL  NOT NULL DEFAULT 0,
    		title VARCHAR(120) NOT NULL,
    		content TEXT NOT NULL,
    		ordersort mediumint(9) NOT NULL,
    		itemRequired BOOL  NOT NULL DEFAULT 0,
    		itemDepend SMALLINT(5) NOT NULL,
    		interactions TEXT NOT NULL,
    		UNIQUE KEY id (id)
    		) $charset_collate;";
        dbDelta($sql);
    }

    $db_table_name = $wpdb->prefix . "wpefc_logs";
    if ($wpdb->get_var("SHOW TABLES LIKE '$db_table_name'") != $db_table_name) {
        if (!empty($wpdb->charset))
            $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
        if (!empty($wpdb->collate))
            $charset_collate .= " COLLATE $wpdb->collate";

        $sql = "CREATE TABLE $db_table_name (
    		id mediumint(9) NOT NULL AUTO_INCREMENT,
    		formID mediumint (9) NOT NULL,
    		ref VARCHAR(120) NOT NULL,
    		email VARCHAR(120) NOT NULL,
    		content TEXT NOT NULL,
        dateLog VARCHAR(64) NOT NULL,
    		UNIQUE KEY id (id)
    		) $charset_collate;";
            dbDelta($sql);
    }



    $db_table_name = $wpdb->prefix . "wpefc_items";
    if ($wpdb->get_var("SHOW TABLES LIKE '$db_table_name'") != $db_table_name) {
        if (!empty($wpdb->charset))
            $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
        if (!empty($wpdb->collate))
            $charset_collate .= " COLLATE $wpdb->collate";

        $sql = "CREATE TABLE $db_table_name (
  		   id mediumint(9) NOT NULL AUTO_INCREMENT,
  		   title VARCHAR(120) NOT NULL,
         description TEXT NOT NULL,
  		   ordersort mediumint(9) NOT NULL,
  		   image VARCHAR(250) NOT NULL,
  		   groupitems VARCHAR(120) NOT NULL,
  		   type VARCHAR(120) NOT NULL,
  		   stepID mediumint(9) NOT NULL,
  		   formID mediumint(9) NOT NULL,
  		    price FLOAT NOT NULL,
          operation VARCHAR(1) NOT NULL DEFAULT '+',
  		    ischecked BOOL,
          isRequired BOOL,
  		    quantity_enabled BOOL,
  		    quantity_max SMALLINT(5)  NOT NULL,
  		    quantity_min SMALLINT(5)  NOT NULL,
  		    reduc_enabled BOOL NOT NULL,
          reduc_qt SMALLINT(5) NOT NULL,
          reduc_value FLOAT NOT NULL,
          reducsQt TEXT NOT NULL,
  		    isWooLinked BOOL,
  		    wooProductID SMALLINT(5)  NOT NULL,
          wooVariation SMALLINT(9)  NOT NULL,
          imageTint BOOL,
          showPrice BOOL NOT NULL,
          useRow BOOL NOT NULL,
          optionsValues TEXT NOT NULL,
          urlTarget VARCHAR(250) NOT NULL,
  		UNIQUE KEY id (id)
		) $charset_collate;";
        dbDelta($sql);
    }


    $db_table_name = $wpdb->prefix . "wpefc_links";
    if ($wpdb->get_var("SHOW TABLES LIKE '$db_table_name'") != $db_table_name) {
        if (!empty($wpdb->charset))
            $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
        if (!empty($wpdb->collate))
            $charset_collate .= " COLLATE $wpdb->collate";

        $sql = "CREATE TABLE $db_table_name (
    		id mediumint(9) NOT NULL AUTO_INCREMENT,
    		formID mediumint (9) NOT NULL,
    		originID INT(9) NOT NULL,
    		destinationID INT(9) NOT NULL,
    		conditions TEXT NOT NULL,
    		UNIQUE KEY id (id)
    		) $charset_collate;";
        dbDelta($sql);
    }


    $db_table_name = $wpdb->prefix . "wpefc_fields";
    if ($wpdb->get_var("SHOW TABLES LIKE '$db_table_name'") != $db_table_name) {
        if (!empty($wpdb->charset))
            $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
        if (!empty($wpdb->collate))
            $charset_collate .= " COLLATE $wpdb->collate";

        $sql = "CREATE TABLE $db_table_name (
    		    id mediumint(9) NOT NULL AUTO_INCREMENT,
                formID SMALLINT(5) NOT NULL,
    		    label VARCHAR(120) NOT NULL,
    		    ordersort mediumint(9) NOT NULL,
    		    isRequired BOOL,
    		    typefield VARCHAR(32) NOT NULL,
    		    visibility VARCHAR(32) NOT NULL,
             validation VARCHAR(64) NOT NULL,
    		UNIQUE KEY id (id)
    		) $charset_collate;";
        dbDelta($sql);
    }

    $db_table_name = $wpdb->prefix . "wpefc_settings";
    if ($wpdb->get_var("SHOW TABLES LIKE '$db_table_name'") != $db_table_name) {
        if (!empty($wpdb->charset))
            $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
        if (!empty($wpdb->collate))
            $charset_collate .= " COLLATE $wpdb->collate";

        $sql = "CREATE TABLE $db_table_name (
  		id mediumint(9) NOT NULL AUTO_INCREMENT,
  		purchaseCode VARCHAR(250) NOT NULL,
  		previewHeight SMALLINT(5) NOT NULL DEFAULT 300,
  		    UNIQUE KEY id (id)
  		) $charset_collate;";
        dbDelta($sql);
        $rows_affected = $wpdb->insert($db_table_name, array('previewHeight' => 300));
    }


    global $isInstalled;
    $isInstalled = true;
}

// End install()

/**
 * Update database
 * @access  public
 * @since   2.0
 * @return  void
 */
function lfb_checkDBUpdates($version)
{
    global $wpdb;
    $installed_ver = get_option("wpecf_version");
    require_once(ABSPATH . '/wp-admin/includes/upgrade.php');


			if (!$installed_ver || $installed_ver < 8.5) {
				$db_table_name = $wpdb->prefix . "lfb_items";
				if ($wpdb->get_var("SHOW TABLES LIKE '$db_table_name'") == $db_table_name) {
				  $sql = "RENAME TABLE " . $db_table_name . " TO ".$wpdb->prefix."wpefc_items;";
				  $wpdb->query($sql);
				} else {
				$db_table_name = $wpdb->prefix . "wpefc_items";
				if ($wpdb->get_var("SHOW TABLES LIKE '$db_table_name'") != $db_table_name) {
					if (!empty($wpdb->charset))
					$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
					if (!empty($wpdb->collate))
						$charset_collate .= " COLLATE $wpdb->collate";

					$sql = "CREATE TABLE $db_table_name (
							id mediumint(9) NOT NULL AUTO_INCREMENT,
							  title VARCHAR(120) NOT NULL,
							description TEXT NOT NULL,
								  ordersort mediumint(9) NOT NULL,
							image VARCHAR(250) NOT NULL,
							groupitems VARCHAR(120) NOT NULL,
							type VARCHAR(120) NOT NULL,
							stepID mediumint(9) NOT NULL,
							formID mediumint(9) NOT NULL,
							price FLOAT NOT NULL,
							operation VARCHAR(1) NOT NULL DEFAULT '+',
							ischecked BOOL,
							isRequired BOOL,
							quantity_enabled BOOL,
							quantity_max SMALLINT(5)  NOT NULL,
							reduc_enabled BOOL NOT NULL,
							reduc_qt SMALLINT(5) NOT NULL,
							reduc_value FLOAT NOT NULL,
							reducsQt TEXT NOT NULL,
							isWooLinked BOOL,
							wooProductID SMALLINT(5)  NOT NULL,
							wooVariation SMALLINT(9)  NOT NULL,
							imageTint BOOL,
							showPrice BOOL NOT NULL,
							useRow BOOL NOT NULL,
							UNIQUE KEY id (id)
							) $charset_collate;";
							dbDelta($sql);
					}
				}
      }


			if (!$installed_ver || $installed_ver < 9.11) {
        $db_table_name = $wpdb->prefix . "wpefc_logs";
        if ($wpdb->get_var("SHOW TABLES LIKE '$db_table_name'") != $db_table_name) {
            if (!empty($wpdb->charset))
                $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
            if (!empty($wpdb->collate))
                $charset_collate .= " COLLATE $wpdb->collate";

            $sql = "CREATE TABLE $db_table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            formID mediumint (9) NOT NULL,
            ref VARCHAR(120) NOT NULL,
            email VARCHAR(120) NOT NULL,
            content TEXT NOT NULL,
            dateLog VARCHAR(64) NOT NULL,
            UNIQUE KEY id (id)
            ) $charset_collate;";
                dbDelta($sql);
        }
      }

			if (!$installed_ver || $installed_ver < 9.14) {
        $table_name = $wpdb->prefix . "wpefc_forms";
        $sql = "ALTER TABLE " . $table_name . " ADD  hideFinalPrice BOOL DEFAULT 0;";
        $wpdb->query($sql);
      }

			if (!$installed_ver || $installed_ver < 9.15) {
        $table_name = $wpdb->prefix . "wpefc_forms";
        $sql = "ALTER TABLE " . $table_name . " ADD  priceFontSize SMALLINT NOT NULL DEFAULT 18;";
        $wpdb->query($sql);
      }


  			if (!$installed_ver || $installed_ver < 9.182) {
          $table_name = $wpdb->prefix . "wpefc_forms";
          $sql = "ALTER TABLE " . $table_name . " ADD  customCss TEXT NOT NULL;";
          $wpdb->query($sql);
          $table_name = $wpdb->prefix . "wpefc_items";
          $sql = "ALTER TABLE " . $table_name . " ADD  optionsValues TEXT NOT NULL;";
          $wpdb->query($sql);
        }

        if (!$installed_ver || $installed_ver < 9.186) {
          $table_name = $wpdb->prefix . "wpefc_forms";
          $sql = "ALTER TABLE " . $table_name . " ADD disableTipMobile BOOL NOT NULL;";
          $wpdb->query($sql);
        }
          if (!$installed_ver || $installed_ver < 9.187) {
            $table_name = $wpdb->prefix . "wpefc_items";
            $sql = "ALTER TABLE " . $table_name . " ADD quantity_min SMALLINT(5)  NOT NULL;";
            $wpdb->query($sql);

            $sql = "ALTER TABLE " . $table_name . " MODIFY COLUMN wooProductID mediumint(9) NOT NULL;";
            $wpdb->query($sql);
            $sql = "ALTER TABLE " . $table_name . " MODIFY COLUMN wooVariation mediumint(9) NOT NULL;";
            $wpdb->query($sql);
          }
          if (!$installed_ver || $installed_ver < 9.193) {
            $table_name = $wpdb->prefix . "wpefc_forms";
            $sql = "ALTER TABLE " . $table_name . " ADD legalNoticeContent TEXT NOT NULL;";
            $wpdb->query($sql);
            $sql = "ALTER TABLE " . $table_name . " ADD legalNoticeTitle TEXT NOT NULL;";
            $wpdb->query($sql);
            $sql = "ALTER TABLE " . $table_name . " ADD legalNoticeEnable BOOL NOT NULL;";
            $wpdb->query($sql);
          }
          if (!$installed_ver || $installed_ver < 9.195) {
            $table_name = $wpdb->prefix . "wpefc_items";
            $sql = "ALTER TABLE " . $table_name . " ADD urlTarget VARCHAR(250)  NOT NULL;";
            $wpdb->query($sql);
          }

    update_option("wpecf_version", $version);
}

/**
 * Uninstallation.
 * @access  public
 * @since   1.0.0
 * @return  void
 */
function lfb_uninstall()
{
    global $wpdb;
    global $jal_db_version;
    $table_name = $wpdb->prefix . "wpefc_steps";
    $wpdb->query("DROP TABLE IF EXISTS $table_name");
    $table_name = $wpdb->prefix . "wpefc_items";
    $wpdb->query("DROP TABLE IF EXISTS $table_name");
    $table_name = $wpdb->prefix . "wpefc_links";
    $wpdb->query("DROP TABLE IF EXISTS $table_name");
    $table_name = $wpdb->prefix . "wpefc_settings";
    $wpdb->query("DROP TABLE IF EXISTS $table_name");
    $table_name = $wpdb->prefix . "wpefc_forms";
    $wpdb->query("DROP TABLE IF EXISTS $table_name");
    $table_name = $wpdb->prefix . "wpefc_fields";
    $wpdb->query("DROP TABLE IF EXISTS $table_name");
}
// End uninstall()

Estimation_Form();
