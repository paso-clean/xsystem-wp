<?php
/*
 * Plugin Name: XSYSTEM
 * Plugin URI: https://jibuncoin.com
 * Description: XSYSTEMプラグイン。
 * Author: Yoshihiro Kawamura
 * Product: xsystem
 * XSYSTEM WP: xsystem-wp
 * Version: 1.0.0
 * App: app
 * App Test:app_test
 * Api: api
 * Author URI: https://jibuncoin.com
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$data = get_file_data( __FILE__ ,array('name'=>'Plugin Name','description'=>'Description','product'=>'Product','xsystem-wp'=>'XSYSTEM WP','version'=>'Version','app'=>'App','app_test'=>'App Test','api'=>'Api'));
define( 'XSYSTEM_PLUGIN_NAME', $data['name'] );
define( 'XSYSTEM_DESCRIPTION', $data['description'] );
define( 'XSYSTEM_PRODUCT', $data['product'] );
define( 'XSYSTEM_WP', $data['xsystem-wp'] );
define( 'XSYSTEM_VERSION', $data['version'] );
define( 'XSYSTEM_APP', $data['app'] );
define( 'XSYSTEM_APP_TEST', $data['app_test'] );
define( 'XSYSTEM_API', $data['api'] );


add_action ( 'admin_menu', function() {
	add_menu_page ( __ ( XSYSTEM_PLUGIN_NAME, XSYSTEM_PRODUCT ), __ ( XSYSTEM_PLUGIN_NAME, XSYSTEM_PRODUCT ), 'manage_options', XSYSTEM_PRODUCT,
		function(){
			include dirname(__FILE__) . '/admin/index.php';
		});
});


register_activation_hook(__FILE__, function() {
  global $wpdb;
  $charset_collate = $wpdb->get_charset_collate();
  $sql = "
  CREATE TABLE `" . XSYSTEM_PRODUCT . "_registers` (
    `register_code` varchar(20) NOT NULL PRIMARY KEY,
    `name` varchar(20) NOT NULL,
    `email` varchar(100) NOT NULL,
    `password` varchar(255),
    `status` varchar(20) NOT NULL,
    `param` text,
    `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
  ) $charset_collate;
  CREATE TABLE `" . XSYSTEM_PRODUCT . "_users` (
    `user_code` varchar(20) NOT NULL PRIMARY KEY,
    `name1` varchar(20) NOT NULL,
    `name2` varchar(20) NOT NULL,
    `name1_kana` varchar(20),
    `name2_kana` varchar(20),
    `email` varchar(100) NOT NULL UNIQUE,
    `password` varchar(255) NOT NULL,
    `zipcode` varchar(10) NOT NULL,
    `address` varchar(100) NOT NULL,
    `tel` varchar(20) NOT NULL,
    `birth` varchar(20) NOT NULL,
    `sex` varchar(20) NOT NULL,
    `active` tinyint(1) NOT NULL DEFAULT '1',
    `secure` tinyint(1) NOT NULL DEFAULT '0',
    `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
  ) $charset_collate;
  CREATE TABLE `" . XSYSTEM_PRODUCT . "_sessions` (
    `session_code` varchar(20) NOT NULL PRIMARY KEY,
    `session_name` varchar(20) NOT NULL,
    `session_type` varchar(20) NOT NULL,
    `target_code` varchar(20) NOT NULL,
    `target_type` varchar(20) NOT NULL,
    `active` tinyint(1) NOT NULL,
    `domain` text,
    `expires_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
  ) $charset_collate;
  CREATE TABLE `" . XSYSTEM_PRODUCT . "_entity_objects` (
    `entity_code` varchar(20) NOT NULL,
    `entity_type` varchar(30) NOT NULL,
    `object_entity` varchar(30) NOT NULL DEFAULT 'object',
    `object_code` varchar(20) NOT NULL
  ) $charset_collate;
  CREATE TABLE `" . XSYSTEM_PRODUCT . "_objects` (
    `object_code` varchar(20) NOT NULL PRIMARY KEY,
    `object_name` varchar(30) NOT NULL DEFAULT 'object',
    `object_type` varchar(30) NOT NULL DEFAULT 'object',
    `object` text,
    `active` tinyint(1) NOT NULL DEFAULT '1',
    `object_num` int UNSIGNED NOT NULL DEFAULT '0',
    `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
  ) $charset_collate;
  CREATE TABLE `" . XSYSTEM_PRODUCT . "_groups` (
    `group_code` varchar(20) NOT NULL PRIMARY KEY,
    `group_name` varchar(100) NOT NULL,
    `active` tinyint(1) NOT NULL DEFAULT '1',
    `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
  ) $charset_collate;
  CREATE TABLE `" . XSYSTEM_PRODUCT . "_coins` (
    `coin_code` varchar(100) NOT NULL PRIMARY KEY,
    `real_coin_name` varchar(100) NOT NULL,
    `coin_name` varchar(30) NOT NULL,
    `coin_version` varchar(30) NOT NULL,
    `coin_limit` bigint(20) NOT NULL DEFAULT '0',
    `owner_code` varchar(20) NOT NULL,
    `owner_type` varchar(30) NOT NULL,
    `is_active` tinyint(1) NOT NULL DEFAULT '0',
    `expires_at` datetime NOT NULL,
    `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
  ) $charset_collate;

  ";
  require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
  dbDelta ( $sql );
});

?>