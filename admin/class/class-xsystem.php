<?php

class Xsystem{

	function init(){
		$this->create_xsystem_dir();
	}

	function create_xsystem_dir(){
		$xsystem_dir = ABSPATH . XSYSTEM_PRODUCT;
		$img_dir = $xsystem_dir . '/img';
		if(!file_exists($xsystem_dir)){
			mkdir($xsystem_dir, 0755);
		}
		if(!file_exists($img_dir)){
			mkdir($img_dir, 0755);
		}
	}

	function copy_source(){
		// global $wp_filesystem;
		$dir = WP_PLUGIN_DIR . '/' . XSYSTEM_WP . '/sources/';
		$new_dir  = ABSPATH . XSYSTEM_PRODUCT . '/';

		$lists = $this->get_file_list($dir);
		foreach($lists as $list){
			$tmp = str_replace($dir,'',dirname($list));
			$dir_names = explode("/",$tmp);
			$parent = '';
			foreach($dir_names as $dirname){
				$dir_path = $new_dir . $parent . $dirname;
				if(!file_exists($dir_path)){
					mkdir($dir_path, 0755);
				}
				$parent = $parent . $dirname . '/';
			}
		}

		foreach($lists as $source_file){
			$new_file = $new_dir . str_replace($dir,'',$source_file);
			copy($source_file,$new_file);
		}
		
	}
	
	function get_file_list($dir) {
		$files = scandir($dir);
		$files = array_filter($files, function ($file) {
			return !in_array($file, array('.', '..'));
		});
		$list = array();
		foreach ($files as $file) {
			$fullpath = rtrim($dir, '/') . '/' . $file;
			if (is_file($fullpath)) {
				$list[] = $fullpath;
			}
			if (is_dir($fullpath)) {
				$list = array_merge($list, $this->get_file_list($fullpath));
			}
		}
		return $list;
	}


	function create_xsystem_config(){
		global $wpdb;

				
		$xsystem_dir = ABSPATH  . XSYSTEM_PRODUCT . '/';
		chmod($xsystem_dir, 0755);

		$file = $xsystem_dir . 'xsystem-config.php';
		if(!file_exists($file)) {
			touch($file);
		}
		$script = "<?php\n";

		$script .= "if (!defined('XSYSTEM_PRODUCT')) {\n";
		$script .= "	define('XSYSTEM_PRODUCT', '" . XSYSTEM_PRODUCT . "');\n";
		$script .= "}\n\n";

		$res = get_option('xsystem_license');
		$app_name = $res['app_name'];
		if(!isset($app_name)){
			$app_name = $this->get_app_title();
		}

		$script .= "if (!defined('APP_NAME')) {\n";
		$script .= "	define('APP_NAME', '" . $app_name . "');\n";
		$script .= "}\n\n";

		$script .= "if (!defined('APP_URL')) {\n";
		$script .= "	define('APP_URL', '" . home_url() . "/');\n";
		$script .= "}\n\n";


		$script .= "if (!defined('APP_URI')) {\n";
		if ($_SERVER['HTTPS'] != '') {
			$http = 'https://';
		}else{
			$http = 'http://';
		}
		$script .= "	define('APP_URI', '" . str_replace($http .$_SERVER['HTTP_HOST'], '',  site_url() ) . "');\n";
		$script .= "}\n\n";


		$script .= "if (!defined('LANG')) {\n";
		$script .= "	define('LANG', '" . get_locale( ) . "');\n";
		$script .= "}\n\n";

		$script .= "if (!defined('ABSPATH')) {\n";
		$script .= "	define('ABSPATH', '" . ABSPATH . "');\n";
		$script .= "}\n\n";

		$script .= "if (!defined('COOKIEHASH')) {\n";
		$script .= "	define('COOKIEHASH', '" . COOKIEHASH . "');\n";
		$script .= "}\n\n";

		$script .= "if (!defined('XSYSTEM_DIR')) {\n";
		$script .= "	define('XSYSTEM_DIR', '" . ABSPATH . XSYSTEM_PRODUCT . "/');\n";
		$script .= "}\n\n";

		$script .= "if (!defined('XSYSTEM_ADMIN_DIR')) {\n";
		$script .= "	define('XSYSTEM_ADMIN_DIR', XSYSTEM_DIR  .  XSYSTEM_PRODUCT . '_admin/' );\n";
		$script .= "}\n\n";

		$script .= "if (!defined('XSYSTEM_ADMIN_URL')) {\n";
		$script .= "	define('XSYSTEM_ADMIN_URL', APP_URL  .  XSYSTEM_PRODUCT . '_admin/' );\n";
		$script .= "}\n\n";

		$script .= "if (!defined('XSYSTEM_COMMON_URL')) {\n";
		$script .= "	define('XSYSTEM_COMMON_URL', APP_URL  . 'common/' );\n";
		$script .= "}\n\n";

		$script .= "if (!defined('XSYSTEM_COMMON_ASSET_URL')) {\n";
		$script .= "	define('XSYSTEM_COMMON_ASSET_URL', APP_URL . XSYSTEM_PRODUCT . '/' . XSYSTEM_PRODUCT . '_common/asset/' );\n";
		$script .= "}\n\n";

		$script .= "if (!defined('XSYSTEM_COMMON_DIR')) {\n";
		$script .= "	define('XSYSTEM_COMMON_DIR', ABSPATH  .  XSYSTEM_PRODUCT . '/xsystem_common/' );\n";
		$script .= "}\n\n";

		$script .= "if (!defined('XSYSTEM_IMG_URL')) {\n";
		$script .= "	define('XSYSTEM_IMG_URL', APP_URL  .  XSYSTEM_PRODUCT . '/img/' );\n";
		$script .= "}\n\n";

		$script .= "if (!defined('XSYSTEM_IMG_DIR')) {\n";
		$script .= "	define('XSYSTEM_IMG_DIR', ABSPATH  .  XSYSTEM_PRODUCT . '/img/' );\n";
		$script .= "}\n\n";

		$script .= "if (!defined('DB_NAME')) {\n";
		$script .= "	define('DB_NAME', '" . DB_NAME . "');\n";
		$script .= "}\n\n";

		$script .= "if (!defined('DB_USER')) {\n";
		$script .= "	define('DB_USER', '" . DB_USER . "');\n";
		$script .= "}\n\n";

		$script .= "if (!defined('DB_PASSWORD')) {\n";
		$script .= "	define('DB_PASSWORD', '" . DB_PASSWORD . "');\n";
		$script .= "}\n\n";

		$script .= "if (!defined('DB_HOST')) {\n";
		$script .= "	define('DB_HOST', '" . DB_HOST . "');\n";
		$script .= "}\n\n";

		$script .= "if (!defined('DB_CHARSET')) {\n";
		$script .= "	define('DB_CHARSET', '" . DB_CHARSET . "');\n";
		$script .= "}\n\n";

		$script .= "if (!defined('DB_COLLATE')) {\n";
		$script .= "	define('DB_COLLATE', '" . DB_COLLATE . "');\n";
		$script .= "}\n\n";

		$script .= "if (!defined('WP_TABLE_PREFIX')) {\n";
		$script .= "	define('WP_TABLE_PREFIX', '" . $wpdb->prefix . "');\n";
		$script .= "}\n\n";

		$script .= "?>";
		file_put_contents ( $file, $script );

	}

	function get_app_title(){
		$app_title = 'APP.';
		$res = get_option('xsystem_license');
		$app_title = $res['app_name'];
		return $app_title;
	}

	function deploy_zip(){
		$xsystem_zip = dirname(dirname(dirname(__FILE__))) . '/xsystem.zip';
		echo $xsystem_zip;
		$zip = new ZipArchive;
		if ($zip->open($xsystem_zip) === TRUE) {
		  $zip->extractTo(ABSPATH);
		  $zip->close();
		  echo '<div>成功</div>';
		} else {
		  echo '<div>失敗</div>';
		}
	}

}
?>