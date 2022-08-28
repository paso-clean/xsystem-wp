<?php
const HTACCESS_PERMISSION = 0604;
const BEGIN_XSYSTEM_ADMIN_HTACCESS = '# BEGIN_XSYSTEM_ADMIN_HTACCESS';
const END_XSYSTEM_ADMIN_HTACCESS   = '# END_XSYSTEM_ADMIN_HTACCESS';
const XSYSTEM_ADMIN = XSYSTEM_PRODUCT . '_ADMIN';
const HTACCESS = '.htaccess';

const TMP_HTDOCCESS_DIR = ABSPATH . 'wp-content/plugins/' . XSYSTEM_WP . '/admin/tmp/';


class XsystemHtaccess{
	function check_xsystem_boot(){
		$htaccess_file = ABSPATH . HTACCESS;
		$htaccess = fopen ( $htaccess_file, "r" );
		$is_boot = false;

		while ( $line = fgets ( $htaccess, 4096 ) ) {
			if (false !== strpos ( $line, BEGIN_XSYSTEM_ADMIN_HTACCESS )) {
				$is_boot = true;
			}
		}

		fclose ( $htaccess );

		return $is_boot;

	}

	function update_htaccess($system_type = 'default',$apps = array('admin')){
		$htaccess_file = ABSPATH . HTACCESS;

		$htaccess = fopen ( $htaccess_file, "r" );

		$default_htaccess_flg = true;
		$default_htaccess = '';

		$xsystem_htaccess = '';

		while ( $line = fgets ( $htaccess, 4096 ) ) {

			if (false !== strpos ( $line, BEGIN_XSYSTEM_ADMIN_HTACCESS )) {
				$default_htaccess_flg = false;
			}

			if ($default_htaccess_flg) {
				$default_htaccess .= $line;
			}else{
				$xsystem_htaccess .= $line;
			}

			if (false !== strpos ( $line, END_XSYSTEM_ADMIN_HTACCESS )) {
				$default_htaccess_flg = true;
			}
		}

		fclose ( $htaccess );

		$tmp_htaccess_file = TMP_HTDOCCESS_DIR . 'tmp_htaccess';

		if (!file_exists ( $tmp_htaccess_file )) {
			touch ( $tmp_htaccess_file );
		}

		if ($system_type == XSYSTEM_ADMIN) {
			$htaccess_content = $this->set_htaccess($apps) . $default_htaccess;
		} else {
			$htaccess_content = $default_htaccess;
		}

		file_put_contents ( $tmp_htaccess_file, $htaccess_content );
		@chmod ( $tmp_htaccess_file, HTACCESS_PERMISSION );
		rename ( $tmp_htaccess_file, $htaccess_file );

	}

	function set_htaccess($apps) {
		$parse_url = parse_url( site_url( ) );
		if ( false === $parse_url ) {
			$base = '/';
		} else {
			if ( isset( $parse_url['path'] ) ) {
				$base = $parse_url['path'] . '/';
			} else {
				$base = '/';
			}
		}
		$htaccess_content = '';
		$htaccess_content = BEGIN_XSYSTEM_ADMIN_HTACCESS . "\n";
		$htaccess_content .= "<IfModule mod_rewrite.c>\n";
		$htaccess_content .= "RewriteEngine on\n";
		$htaccess_content .= "RewriteBase " . $base  . "\n";
		$htaccess_content .= "RewriteCond %{REQUEST_FILENAME} !-f\n";
		$htaccess_content .= "RewriteCond %{REQUEST_FILENAME} !-d\n";
		foreach($apps as $app){
			$htaccess_content .= "# XSYSTEM_ADMIN_APP_" . strtoupper($app) . "\n";
			$htaccess_content .= "RewriteRule ^" . XSYSTEM_PRODUCT . '_' . $app . "/ " . XSYSTEM_PRODUCT . '/' . XSYSTEM_PRODUCT . '_admin/' . $app . "/index.php [L]\n";
		}
		$htaccess_content .= "</IfModule>\n";
		$htaccess_content .= END_XSYSTEM_ADMIN_HTACCESS . "\n";

		return $htaccess_content;
	}
}

?>