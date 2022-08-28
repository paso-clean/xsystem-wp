<?php
require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
require_once WP_PLUGIN_DIR . '/' . XSYSTEM_WP . '/admin/class/class-xsystem.php';
require_once WP_PLUGIN_DIR . '/' . XSYSTEM_WP . '/admin/class/class-htaccess.php';
$xsystem = new Xsystem();
$htaccess = new XsystemHtaccess();
$xsystem->init();

if(!file_exists(ABSPATH . XSYSTEM_PRODUCT . '/' . XSYSTEM_PRODUCT . '_admin/')){
	// $xsystem->copy_source();
	$xsystem->deploy_zip();
}

if($_POST['register'] == 1){

	if ($_POST ['activation'] == 1) {
		$xsystem_admin_app_dir =  ABSPATH . XSYSTEM_PRODUCT . '/' . XSYSTEM_PRODUCT . '_admin/';
		$dirs = scandir($xsystem_admin_app_dir);$excludes = array(
			'.',
			'..',
			'.htaccess',
		);
		
		$apps = array();
		foreach ($dirs AS $dir) {
			if (in_array($dir, $excludes)) {
				continue;
			}
		
			$dir_path = $xsystem_admin_app_dir . $dir;
			if (is_dir($dir_path)) {
				$apps[] = $dir;
			}
		}
		$htaccess->update_htaccess(XSYSTEM_PRODUCT . '_ADMIN',$apps);
	}else {
		$htaccess->update_htaccess('default');
	}
	$xsystem->create_xsystem_config();
}

$is_boot = $htaccess->check_xsystem_boot();


$user = wp_get_current_user();
$user_login = $user->user_login;

$logged_in_key = 'wordpress_logged_in_' . md5(site_url());
$wp_hash = $hash = hash_hmac('sha256', $_COOKIE[$logged_in_key], $user_login); 

?>

<?php if($is_boot){ ?>
<div style="padding:20px 0 20px 10px;font-size:20px;color:#0000ff">XSYSTEM起動中</div>
<?php }else{ ?>
	<div style="padding:20px 0 20px 10px;font-size:20px;color:#ff0000">XSYSTEM停止中</div>
<?php } ?>

<form method="post">
<input type='hidden' name='option_page' value='general' />
<input type="hidden" name="action" value="update" />
<select name="activation">
	<option value="1" <?php if($is_boot){ echo 'selected '; } ?> >XSYSTEM起動</option>
	<option value="0" <?php if(!$is_boot){ echo 'selected '; } ?> >XSYSTEM停止</option>
</select>
<input type="submit" name="submit" id="submit" class="button button-primary" value="実行"  />
<input type="hidden" name="register" value="1" />
</form>

<?php if($is_boot){ ?>
<hr>
<div style="padding:20px 0 20px 10px;font-size:20px;color:#0000ff">
<a href="<?php echo site_url() . '/xsystem_admin/onetime/auth/' . $user_login . '/' . $wp_hash . '/'; ?>" target="_blank"><button type="button" class="button button-primary">管理ページ</button></a>
</div>
<?php } ?>