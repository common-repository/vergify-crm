<?php

/*
Plugin Name: Vergify CRM
Plugin URI: https://wordpress.org/plugins/navigation/
Description: Client Management Software that is powerful, easy to use & integrates seamlessly with your WordPress website. Do more with features like Live Chat, Email Subscription, Survey Creator, Email Marketing & Email Integration.
Author: Vergify LLC
Version: 1.6.1
Author URI: https://www.vergify.com
*/

add_action('admin_menu', 'vergify_initialize');

function vergify_initialize() {  
$plugins_url = plugins_url('vergify-crm');
add_menu_page( 'Vergify', 'Vergify', 'manage_options', 'Vergify', 'Vergify', $plugins_url . '/images/vergifyicon.png',11 );
}

function vergify_load_custom_wp_admin_style($hook) {
        // Load only on ?page=mypluginname

        if(strtolower($hook) != 'toplevel_page_vergify') {
                return;
        }
wp_enqueue_style( 'style-name', plugins_url( 'css/style.css', __FILE__ )  );

}
add_action( 'admin_enqueue_scripts', 'vergify_load_custom_wp_admin_style' );

function Vergify() {
	 $url = plugins_url('vergify-crm'); ?>
     
<a href="<?php echo esc_url('https://www.vergify.com'); ?>" style=" outline: none;" target="_blank"><img src="<?php echo plugins_url( 'images/logo.png', __FILE__ ); ?>" style="display: inline-block; outline: none;" /></a>
<br>
     <?php	
	  global $wpdb; 
		/* Create table */
		$table_name = $wpdb->prefix.'vergify';
		$table = "CREATE TABLE $table_name (
			`id` INT(11) NOT NULL AUTO_INCREMENT ,
			`companyguid` varchar(255) NOT NULL,
			`status` text NOT NULL,
			`last_userid` varchar(20) NOT NULL,
			`admin_id` varchar(20) NOT NULL,
			PRIMARY KEY (`id`)) ENGINE = InnoDB";
		$wpdb->query($table);
		
		$table_name1 = $wpdb->prefix.'setting_url';
		$table1 = "CREATE TABLE $table_name1 (
			`id` INT(11) NOT NULL AUTO_INCREMENT,
			`url` varchar(255) NOT NULL,
			PRIMARY KEY (`id`)) ENGINE = InnoDB";
		$wpdb->query($table1);
		
		$table_name2 = $wpdb->prefix.'vergify_settings';
		$table2 = "CREATE TABLE $table_name2 (
			`id` INT(11) NOT NULL AUTO_INCREMENT,
			`uid` varchar(10) NOT NULL,
			`live_chat` varchar(10) NOT NULL,
			`email_subscriptions` varchar(10) NOT NULL,
			`Comments_Discussion` varchar(10) NOT NULL,
			`surveys` varchar(10) NOT NULL,
			`lead_generator` varchar(10) NOT NULL,
			PRIMARY KEY (`id`)) ENGINE = InnoDB";
		$wpdb->query($table2);			
	 ?>
 	
<?php
   echo '<div style="display: inline-block; width: auto; height: auto; margin-left: auto; margin-right: auto;">';
   $uid = 1;
$changeaccount= isset($_GET['changeaccount']) ? $_GET['changeaccount'] : false; 
   $check_admin_reg = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'vergify WHERE admin_id = "'.$uid.'"');		
   $rowcount_count = $wpdb->num_rows;

   if($rowcount_count < '1' || $changeaccount == 'true'){      
   if(isset($_POST['submit'])):
    	global $wpdb;
    	$data['email'] = '';
    	$data['password'] = '';
    	$data['firstname'] = '';
    	$data['lastname'] = '';
    	$data['companyname'] = '';
        if(wp_verify_nonce( $_POST['submit_post'], 'create_account_action' ))
{
    	if(isset($_POST['email'])) {
    		$data['email'] = sanitize_email($_POST['email']);
    	}
    	if(isset($_POST['password'])) {
    		$data['password'] = sanitize_text_field($_POST['password']);
    	}
    	if(isset($_POST['firstname'])) {
    		$data['firstname'] = sanitize_text_field($_POST['firstname']);
    	}
    	if(isset($_POST['lastname'])) {
    		$data['lastname'] = sanitize_text_field($_POST['lastname']);
    	}
    	if(isset($_POST['companyname'])) {
    		$data['companyname'] = sanitize_text_field($_POST['companyname']);
    	}   
    	if($data['email'] == '' && $data['password'] == '' && $data['firstname'] == '' && $data['lastname'] == '' && $data['companyname'] == ''){
    		echo '<div style="margin-top:10%;" class="text-center">Please fill the required field.';
    		echo '<br><a href="">Go Back</a></div>';
    		die();
    	}
}
else
{
echo '<div style="margin-top:10%;" class="text-center">Access Denied.';
    		echo '<br><a href="">Go Back</a></div>';
    		die();
}	
    	$header = array('Content-type: application/x-www-form-urlencoded');
	$bare_url = "https://app.vergify.com/service/wordpress/settings.asmx/createaccount";
    	$url = wp_nonce_url( $bare_url, 'vergify-create-account');
    	

$body = array(
'firstname' => $data['firstname'],
'lastname' => $data['lastname'],
'companyname' => $data['companyname'],
    'email' => $data['email'],
    'password' => $data['password']
);
 
$args = array(
    'method' => 'POST',
    'body' => $body,
    'timeout' => '45',
    'redirection' => '5',
    'httpversion' => '1.0',
    'blocking' => true,
    'headers' => $header,
    'cookies' => array()
);
 
$resp = wp_remote_post( $url, $args );
        
		echo '<div style="margin-top:10%;" class="text-center">';
    	//print_r($resp); 
	    $xmlaa = simplexml_load_string($resp["body"]);
		?>
		<?php
		if (preg_match('/"([^"]+)"/', $xmlaa, $m)) {
			$Cguid = $m[1];   
		} 
			
		if($Cguid =='1')
		{
 echo vergify_getcreateaccount_form("<span style='color: red;'>Email Already Exists.</span>");
echo do_shortcode('[user-login-sortcode]');
				
		}
		else if($Cguid =='2')
		{
 echo vergify_getcreateaccount_form("<span style='color: red;'>Invalid Email Address</span>");
echo do_shortcode('[user-login-sortcode]');
				
		}
else if($Cguid =='3')
		{
 echo vergify_getcreateaccount_form("<span style='color: red;'>Please Enter A Password</span>");
echo do_shortcode('[user-login-sortcode]');
				
		}
else if($Cguid =='4')
		{
 echo vergify_getcreateaccount_form("<span style='color: red;'>Please Enter An Email Address</span>");
echo do_shortcode('[user-login-sortcode]');
				
		}
else if($Cguid =='5')
		{
 echo vergify_getcreateaccount_form("<span style='color: red;'>Please Enter Your Name</span>");
echo do_shortcode('[user-login-sortcode]');
				
		}
else if($Cguid =='6')
		{
 echo vergify_getcreateaccount_form("<span style='color: red;'>Please Enter Your Company Name/span>");
echo do_shortcode('[user-login-sortcode]');
				
		}
		else{
		$wpdb->query('TRUNCATE TABLE '.$wpdb->prefix.'vergify');
		$wpdb->query('TRUNCATE TABLE '.$wpdb->prefix.'vergify_settings');
			$c_uid = get_current_user_id();
			$wpdb->insert($wpdb->prefix.'vergify', array(
				'id' => '',
				'companyguid' => $Cguid,
				'status' => '1', 
				'admin_id' => $c_uid,
			));
	$wpdb->insert($wpdb->prefix.'vergify_settings', array(
				'id' => '',
				'uid' => '1',
				'live_chat' => '1',
				'email_subscriptions' => '1',
				'Comments_Discussion' => '0',
				'surveys' => '1',
				'lead_generator' => '1',
			));
			?>
			<script>
			 window.location.href = "<?php echo admin_url(); ?>admin.php?page=Vergify";
		    </script>
		<?php	
        }		
    else: 	
	  global $current_user;
	  $uid = 1;	  
	  $user_info = get_userdata($uid);
      $uroll = implode(', ', $user_info->roles);
      $first_name = $current_user->user_firstname;
      $lastname = $current_user->user_lastname;
      $user_email = $current_user->user_email; 	 	 
	if ($uroll == 'administrator') {
    echo vergify_getcreateaccount_form("");

	
	}	
	echo do_shortcode('[user-login-sortcode]');
	endif;  	
   }
   else{
    /*--------------Setting-check-box-admin--------*/
    $current_user_id = 1;
        
	if(isset($_POST['submit_checkbox'])){
if(wp_verify_nonce( $_POST['submit_post'], 'update_settings_action' ) == false){
echo '<div style="margin-top:10%;" class="text-center">Access Denied.';
    		echo '<br><a href="">Go Back</a></div>';
    		die();
}
		 global $wpdb;
		 $live_chat = sanitize_text_field($_POST['live_chat']);
         $email_subscriptions = sanitize_text_field($_POST['email_subscriptions']);
         $Comments_Discussion = sanitize_text_field($_POST['Comments_Discussion']);
         $surveys = sanitize_text_field($_POST['surveys']);
         $lead_generator = sanitize_text_field($_POST['lead_generator']);
  
         if($live_chat == ''){
			 $live_chat_val = '0';
		 }
		 else{
			 $live_chat_val = $live_chat;
		 }
		 if($email_subscriptions == ''){
			 $email_subscriptions_val = '0';
		 }
		 else{
			 $email_subscriptions_val = $email_subscriptions;
		 }
		 if($Comments_Discussion == ''){
			 $Comments_Discussion_val = '0';
		 }
		 else{
			 $Comments_Discussion_val = $Comments_Discussion;
		 }
		 if($surveys == ''){
			 $surveys_val = '0';
		 }
		 else{
			 $surveys_val = $surveys;
		 }
		 if($lead_generator == ''){
			 $lead_generator_val = '0';
		 }
		 else{
			 $lead_generator_val = $lead_generator;
		 }
		 $setting_page_checkbox = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."vergify_settings WHERE uid ='".$current_user_id."'");
         foreach($setting_page_checkbox as $setting_page_checkboxs){
			 $record_checkbox_id = $setting_page_checkboxs->id;
			 
		 } 		 
		 $rowcount1 = $wpdb->num_rows;
	     if($rowcount1 > '0'){
			   $wpdb->update($wpdb->prefix.'vergify_settings', array( 'live_chat' => $live_chat_val, 'email_subscriptions' => $email_subscriptions_val, 'Comments_Discussion' => $Comments_Discussion_val, 'surveys' => $surveys_val, 'lead_generator' => $lead_generator_val, 'uid' => $current_user_id),array('id'=>$record_checkbox_id)); 
			   echo 'Settings Saved';   
		 }
		 else{
		 $wpdb->insert($wpdb->prefix.'vergify_settings', array(
				'id' => '',
				'uid' => $current_user_id,
				'live_chat' => $live_chat_val,
				'email_subscriptions' => $email_subscriptions_val,
				'Comments_Discussion' => $Comments_Discussion_val,
				'surveys' => $surveys_val,
				'lead_generator' => $lead_generator_val,
			));

		 
		 }
	 }	 	 
	 echo '<form style="" method="POST" action="">'.  wp_nonce_field( 'update_settings_action', 'submit_post' );	 
      $setting_page_checkbox1 = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."vergify_settings WHERE uid ='".$current_user_id."'");
         foreach($setting_page_checkbox1 as $setting_page_checkboxs11){
			 $check_live_chat = $setting_page_checkboxs11->live_chat;
			 $check_email_subscriptions = $setting_page_checkboxs11->email_subscriptions;
			 $check_Comments_Discussion = $setting_page_checkboxs11->Comments_Discussion;
			 $check_surveys = $setting_page_checkboxs11->surveys;
			 $check_lead_generator = $setting_page_checkboxs11->lead_generator;
		 }   
	 ?>	
<a href="<?php echo admin_url(); ?>admin.php?page=Vergify&changeaccount=true" style="position: absolute; top: 10px; right: 10px;">Change Account</a>
<div style="font-size: 13pt; max-width: 90%;">Manage your client & your business with features like Services & Online Payments, Live Chat, Marketing, Discussion, Surveys & Email Subscriptions that will make operating and managing your business easier.</div> <br>
<div style="font-size: 12pt; max-width: 90%;">To Configure & Manage Your Account go to <a href="https://app.vergify.com" target="_blank">https://www.vergify.com</a></div>
<br><br>
<div style="font-size: 12pt; max-width: 90%;font-style: italic;">Note: To add Surveys or Lead Forms to a post or a page you need to be in "Text" mode.</div><br><br>
<div style="max-width: 250px; width: auto; text-align: left; display: inline-block; margin-left: auto; margin-right: auto;">
<div style="font-size: 15pt;">Settings</div>
<hr />
	 <div style="margin-bottom: 3px;"><input type="checkbox" value="1" name="live_chat" <?php if($check_live_chat == '1'){ echo 'checked="checked"'; } ?> /><span  sytle="font-size: 12pt;">Live Chat</span></div>
	 <div style="margin-bottom: 3px;"><input type="checkbox" value="1" name="email_subscriptions" <?php if($check_email_subscriptions == '1'){ echo 'checked="checked"'; } ?> /><span sytle="font-size: 12pt;">Email Subscriptions</span></div>
     
     <div style="margin-bottom: 3px;"><input type="checkbox" value="1" name="surveys" <?php if($check_surveys == '1'){ echo 'checked="checked"'; } ?> /><span sytle="font-size: 12pt;">Surveys</span></div>
     <div style="margin-bottom: 3px;"><input type="checkbox" value="1" name="lead_generator" <?php if($check_lead_generator == '1'){ echo 'checked="checked"'; } ?> /><span sytle="font-size: 12pt;">Lead Generator</span></div>
<div  style="visibility: hidden; margin-bottom: 3px;"><input type="checkbox" value="1" name="Comments_Discussion" <?php if($check_Comments_Discussion == '1'){ echo 'checked="checked"'; } ?> /><span sytle="font-size: 12pt;">Comments/Discussion</span></div>
</div><br>
	 <input name="submit_checkbox" value="Save Settings" id="" class="vergifybutton" type="submit" style="width:200px; cursor: pointer;"></form>
	 
<?php    
   }
   echo '</div>';
    
      
}

function vergify_getcreateaccount_form($message)
{
$atts = '<div id="" class="signIn_page" style="width: 400px; max-width: 100%;">';
	$atts .= '<h3 class="hd-txt">Create Your New Account</h3>';
	$atts .= $message;
	$atts .='<div class="createYr_act-mid-block" style="width: 100%;" >';
	$atts .='<form style="" method="post" action="">'.  wp_nonce_field( 'create_account_action', 'submit_post' ) .'<table class="createYr_acttbl_second"><tbody><tr>';
	$atts .='<td style="width: 100%;  padding-top: 0px;" valign="top">';
    $atts .='<div class="inner-divv_block" style="background-color: #FFFFFF; padding: 20px; padding-top: 20px; width: 100%; ">';
    $atts .='<input name="firstname" class="txt-all-around fst" placeholder="First Name"  type="text" value="'.$first_name.'" required >';
    $atts .='<input name="lastname" id="" class="txt-all-around sec" placeholder="Last Name"  type="text" value="'.$lastname.'" required><span class="clearfix"></span><br><br>';
    $atts .='<input name="email" style="width: 100%;" id="" class="txt-all-around thrd" placeholder="Your Email"  type="text" value="'.$user_email.'" required><br><br>';
    $atts .='<input name="password" style="width: 100%;" id="" class="txt-all-around frth" placeholder="Password"  type="password" required><br><br>';
    $atts .='<input name="companyname" style="width: 100%;" id="" class="txt-all-around fth" placeholder="What is the name of your company?"  type="text" required><br><br>';
    $atts .='<div align="center"> <span class="termsoF-text">By clicking Sign Up you agree to <br>Vergify <a href="https://www.vergify.com/privacy-policy" target="_blank">Privacy Policy</a> and <a href="https://www.vergify.com/terms-of-service" target="_blank">Terms of Service</a></span></div><br>';
    $atts .='<input name="submit" value="Sign Up" id="" class="btn btn-xs form-btn font-12 loginwhite signIn" type="submit" style="width: 100%; cursor: pointer;">';
    $atts .='<div class="clearfix"></div><br>';
	$atts .='</div></td></tr></tbody></table></form>';
	$atts .='<input name="" id="" value="1" type="hidden"></div>';
	$atts .='<div class="createYr_act_btm-block">';
    
	$atts .='</div></div>';

return $atts;
}

function debug_to_console( $data ) {
if ( is_array( $data ) )
 $output = "<script>console.log( 'Debug Objects: " . implode( ',', $data) . "' );</script>";
 else
 $output = "<script>console.log( 'Debug Objects: " . $data . "' );</script>";
echo $output;
}

/*-------Login-short-code-------*/
function vergify_user_login_sortcode($atts){
	global $wpdb;
	$url = plugins_url('vergify-crm'); ?>

    <?php	
    if(isset($_POST['submit_login'])):
    	
    	$data['email'] = '';
    	$data['password'] = '';
    
    	if(isset($_POST['email'])) {
    		$data['email'] = sanitize_email($_POST['email']);
    	}
    	if(isset($_POST['password'])) {
    		$data['password'] = sanitize_text_field($_POST['password']);
    	}
    
    	if($data['email'] == '' && $data['password'] == ''){
    		echo '<div style="margin-top:10%;" class="text-center">Please fill the required field.';
    		echo '<br><a href="">Go Back</a></div>';
    		die();
    	}

 if(wp_verify_nonce( $_POST['submit_post'], 'login_account_action' ) == false){
echo '<div style="margin-top:10%;" class="text-center">Access Denied.';
    		echo '<br><a href="">Go Back</a></div>';
    		die();
}
    
    	$header = array('Content-type: application/x-www-form-urlencoded');
	$bare_url = "https://app.vergify.com/service/wordpress/settings.asmx/login";
    	$url = wp_nonce_url( $bare_url, 'vergify-login-account');
    	
	
$body = array(
    'email' => $data['email'],
    'password' => $data['password']
);
 
$args = array(
    'method' => 'POST',
    'body' => $body,
    'timeout' => '45',
    'redirection' => '5',
    'httpversion' => '1.0',
    'blocking' => true,
    'headers' => $header,
    'cookies' => array()
);
 
$resp = wp_remote_post( $url, $args );


		$xmlaa = simplexml_load_string($resp["body"]);
		if (preg_match('/"([^"]+)"/', $xmlaa, $m)) {
			$Cguid = $m[1];   
		}
//$Cguid = $resp["body"];		
		if($Cguid == '-1'){
			//echo 'Incorrect Email and/or Password';
echo vergify_getlogin_form("<span style='color: red;'>Incorrect Email and/or Password</span>");
		}
		else{
		$wpdb->query('TRUNCATE TABLE '.$wpdb->prefix.'vergify');
		$loginresults = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'vergify WHERE companyguid = "'.$Cguid.'"');	
		
		if(!$loginresults){
			global $wpdb;
			$c_uid = 1;
			$wpdb->insert($wpdb->prefix.'vergify', array(
				'id' => '',
				'companyguid' => $Cguid,
				'status' => '1',
				'admin_id' => $c_uid,
			));
$wpdb->insert($wpdb->prefix.'vergify_settings', array(
				'id' => '',
				'uid' => '1',
				'live_chat' => '1',
				'email_subscriptions' => '1',
				'Comments_Discussion' => '0',
				'surveys' => '1',
				'lead_generator' => '1',
			));
			?>
			
		<?php	
		}
?>
        <script>
			 window.location.href = "<?php echo admin_url(); ?>admin.php?page=Vergify";
		    </script> 
            <?php		
		}
 
    else: 
	
	return vergify_getlogin_form("");

	endif;
	
}
add_shortcode( 'user-login-sortcode', 'vergify_user_login_sortcode' );

function vergify_getlogin_form($message)
{
$atts = '<div class="loginorseperatordiv" style="width: 400px; max-width: 100%; float: left; "> OR </div>';
	$atts .= '<div class="signIn_page" style="width: 400px; max-width: 100%;">';
	$atts .= '<h3 class="hd-txt">Sign In</h3><br>';   
$atts .= $message; 
    $atts .= '<div class="inner-divv_block" style="background-color: #FFFFFF; padding-top: 0px;width: 400px; max-width: 100%;">';
    $atts .= '<form style="background-color: #FFFFFF;" method="post" action="" autocomplete="off">'.  wp_nonce_field( 'login_account_action', 'submit_post' ) .'<table id="taBle_1"><tbody><tr><td>';
    $atts .= '<div class="input-boxx" style="background-color: #FFFFFF;">';
    $atts .= '<input name="email" id="" class="txt-all-around" placeholder="Email address" autocomplete="off"  type="text"><br><br>';            
    $atts .= '<input name="password"  class="txt-all-around" placeholder="Password" autocomplete="off"  type="password"><br><br>';
    $atts .= '<input name="submit_login" value="Sign In"  class="btn btn-xs form-btn font-12 loginwhite signIn"  type="submit" style="cursor: pointer;">';
    $atts .= '</div></td></tr></tbody>';    
    $atts .= '</table></form>';   
    $atts .= '</div>';
 	$atts .= '<div class="bottom-textt"  align="center">';
    $atts .= '<div class="clearfix"></div>';
    $atts .= '<a href=""><br>';
    $atts .= '<a href="https://app.vergify.com/user/forgotpassword" class="bootom-line-text" target="_blank">Forgot Your Password?</a>';
    $atts .= '</div>';
    $atts .= '</div>';
	return $atts;
}

/*-----Forget-password-short-code-----*/
function vergify_Forget_password_sortcode($atts){
	 $url = plugins_url('vergify-crm'); ?>

     <?php	
	 $atts = '<div class="main-div"><div id="" class="forgotPswd_page"><div class="main-div-blk" >';
     $atts .= '<h3 class="heading-text">Forgot Your Password?</h3><br>';
     $atts .= '<span class="para-text">An email will be sent to you with a link to reset your password.<br></span><br>';
     $atts .= '<label>Email</label><br>';
     $atts .= '<input name="" id="" class="txt-underline main-div-txt" type="text"><br><br><br><br>';
     $atts .= '<input name="" value="Submit" id="" class="btn btn-xs form-btn font-12 sUB" type="submit">';
     $atts .= '</div></div></div>';
	 return $atts;
}
add_shortcode( 'Forget-password-sortcode', 'vergify_Forget_password_sortcode' );


/*--------------Setting-Checkbox-Call-Function-----------*/

function vergify_live_chat_call($atts){

	 $url = plugins_url('vergify-crm'); ?>

     <?php
  	 global $wpdb;
         $uid = 1;
		 $setting_page_checkbox1 = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."vergify_settings WHERE uid ='".$uid."'");
		 $rowcount = $wpdb->num_rows;		
         foreach($setting_page_checkbox1 as $setting_page_checkboxs11){
			 $live_chat_status = $setting_page_checkboxs11->live_chat;
		 } 	
         $get_cguid = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."vergify WHERE admin_id ='".$uid."'");	 
         foreach($get_cguid as $get_cguids){
			$companyguid_value = $get_cguids->companyguid;
		 }      		 
    	$data['vergifycompanyguid'] = '';

    	if($live_chat_status == '0' || $rowcount == '0'){
    	}
        else{ 
    	
		
		?>

		<script>
        var companyguid = '<?php echo $companyguid_value ?>';
        jQuery.ajax({
            type: "POST",
            contentType: "application/json; charset=utf-8",
            url: '<?php echo esc_url('https://app.vergify.com/service/wordpress/settings.asmx/getlivechat'); ?>',
            dataType: 'json',
            data: JSON.stringify({ 'vergifycompanyguid': companyguid }),
            success: function (data) {
                
                jQuery('head').prepend(data.d.slice(1, -1));

            },
            error: function (request, status, error) {
                
            }
        });
       </script>
		<?php
        }		
}
add_shortcode( 'Live-Chat-Call', 'vergify_live_chat_call' );
add_action('wp_head', 'vergify_live_chat_call');
			
		
function vergify_email_subscriptions_call($atts){
	 $url = plugins_url('vergify-crm'); ?>

     <?php
	
  	global $wpdb;
		  
        $uid = 1;		   
		$setting_page_status = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."vergify_settings WHERE uid ='".$uid."'");
		$rowcount = $wpdb->num_rows;
        foreach($setting_page_status as $setting_page_statuss){
			 $active_deactive_show = $setting_page_statuss->email_subscriptions;
		} 
		/*----check-c-guid-in-db-----*/
         $get_cguid = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."vergify WHERE admin_id ='".$uid."'");		 
         foreach($get_cguid as $get_cguids){
			$companyguid_value = $get_cguids->companyguid;
			//echo 'lokesh';
		 } 
    	$data['vergifycompanyguid'] = '';	
    	//if(isset($_POST['vergifycompanyguid'])) {
    		$data['vergifycompanyguid'] = $companyguid_value;
    	//}
    	if($active_deactive_show == '0' || $rowcount == '0'){
    	}
        else{
    	
		?>

		<script>
        var companyguid = '<?php echo $companyguid_value ?>';
        jQuery.ajax({
            type: "POST",
            contentType: "application/json; charset=utf-8",
            url: '<?php echo esc_url('https://app.vergify.com/service/wordpress/settings.asmx/getemailsubscription'); ?>',
            dataType: 'json',
            data: JSON.stringify({ 'vergifycompanyguid': companyguid }),
            success: function (data) {
                jQuery('head').prepend(data.d.slice(1, -1));

            },
            error: function (request, status, error) {
               
            }
        });
       </script>
		<?php
		
        }		
}
add_shortcode( 'Email-Subscriptions-call', 'vergify_email_subscriptions_call' );
add_action('wp_head', 'vergify_email_subscriptions_call');



?>



<?php
/*-------------------Add-Button-In-Post----------------*/
function vergify_shortcode_button_script() 
{ if(wp_script_is("quicktags"))
    {   
				global $wpdb;
				$uid = 1;  	
				$setting_page_status = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."vergify_settings WHERE uid ='".$uid."'");
				$rowcount = $wpdb->num_rows;
				 foreach($setting_page_status as $setting_page_statuss){
					 $active_deactive_survey = $setting_page_statuss->surveys;
					 $active_deactive_Discus = $setting_page_statuss->Comments_Discussion;
					 $active_deactive_lead_g = $setting_page_statuss->lead_generator;
				}
				/*----check-c-guid-in-db-----*/
				$get_cguid = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."vergify WHERE admin_id ='".$uid."'");		 
				foreach($get_cguid as $get_cguids){					
					$companyguid_value = $get_cguids->companyguid;					
				}  

        ?>  

            <script type="text/javascript">         
               
                function getSel()
                {
                    var txtarea = document.getElementById("content");
                    var start = txtarea.selectionStart;
                    var finish = txtarea.selectionEnd;
                    return txtarea.value.substring(start, finish);
                }
				<!----1----->
				<?php if($active_deactive_Discus == '1'){ ?>
                
				<?php } 
				if($active_deactive_survey == '1'){ ?>
				<!----2----->
				
				<?php } 
				if($active_deactive_lead_g == '1'){
				?>
				<!----3----->
				
				<?php }
                if($active_deactive_survey == '1'){	?>
				<!----4----->
				 QTags.addButton( 
                    "code_shortcode4", 
                    "Survey", 
                    surveys_Call_List
                );
                function surveys_Call_List()
                {
                    var selected_text = getSel();
                    <!-------Servay-list-popup----------->
					var companyguid = '<?php echo $companyguid_value; ?>';
					jQuery.ajax({
						type: "POST",
						contentType: "application/json; charset=utf-8",
						url: '<?php echo esc_url('https://app.vergify.com/service/wordpress/settings.asmx/getsurveylist'); ?>',
						dataType: 'json',
						data: JSON.stringify({ 'vergifycompanyguid': companyguid }),
						success: function (data) {
							var obj = JSON.parse(data.d);
							var tbl = jQuery("<table style='width: 100%;table-layout: fixed;' />").attr("id", "myvergifysurveytable");
							var divcontainer = jQuery("#surveylistpopup");
							divcontainer.empty();
							divcontainer.append('<h3 style="font-size: 16pt; weight: bold; padding: 0px; margin: 0px; text-align: center;">Select A Survey</h3><br />').append(tbl);
							jQuery("#myvergifysurveytable").append('<thead><tr><th style="text-align: left;padding-left:2px; padding-right:2px; border-bottom: 1px solid #CCCCCC; color: #808080; font-size: 13pt;">Survey Title</th><th style="text-align: left;padding-left:2px; padding-right:2px; border-bottom: 1px solid #CCCCCC; color: #808080; font-size: 13pt;">Date Created</th></tr></thead>');
							for (var i = 0; i < obj.length; i++) {
								var tr = "<tr>";
								var tdtitle = "<td style='padding-left:2px; padding-right:2px; padding-top: 2px;'><span style='cursor: pointer; color: blue;' id='vergifysurvey_" + obj[i]["guid"] + "'>" + obj[i]["title"] + "</span></td>";
								var tddatecreated = "<td style='padding-left:2px; padding-right:2px; padding-top: 2px;'>" + obj[i]["datecreated"] + "</td></tr>";
								
								jQuery("#myvergifysurveytable").append(tr + tdtitle + tddatecreated);
							/*---------================================create-short-code-----=====================================================-*/ 
								jQuery("#vergifysurvey_" + obj[i]["guid"]).click(function (event) {
									var surveyguid = event.target.id.replace('vergifysurvey_', '');
									
									var surveyshortcode = '[vergifysurvey_code foo="' + surveyguid + '"]';

									QTags.insertContent(surveyshortcode);
									divcontainer.hide();
								});																
							}
							if (obj.length == 0) {
								divcontainer.empty();
								divcontainer.append('<span style="font-size: 16pt; weight: bold;">You haven\'t created any Surveys </span><br /><br /><span><a target="_blank" href="https://app.vergify.com/user/login">Try Creating One</a> </span>')
							}
							divcontainer.append('<span style="position: absolute; right: 2px; top: 0; cursor: pointer;" onclick="jQuery(this).parent().hide();" title="Close Window"><img src="<?php echo plugins_url('vergify-crm') ?>/images/exit.png" /></span>');

jQuery("#myvergifysurveytable tbody").css("overflow-y","auto").css("max-height","350px");
							displaypopup(divcontainer);
						},
						error: function (request, status, error) {
							
						}
					});
                }
				<?php } 
				if($active_deactive_lead_g == '1'){ ?>
				<!----5----->
				<!--#####lead-generator-list#####------>
				 QTags.addButton( 
                    "code_shortcode5", 
                    "Lead Generator", 
                    lead_generator_list
                );
                function lead_generator_list()
                {
                    var selected_text = getSel();
                    <!-------Lead-list-popup----------->
					var companyguid = '<?php echo $companyguid_value; ?>';
						jQuery.ajax({
							type: "POST",
							contentType: "application/json; charset=utf-8",
							url: '<?php echo esc_url('https://app.vergify.com/service/wordpress/settings.asmx/getleadgeneratorlist'); ?>',
							dataType: 'json',
							data: JSON.stringify({ 'vergifycompanyguid': companyguid }),
							success: function (data) {

								var obj = JSON.parse(data.d);
								var tbl = jQuery("<table style='width: 100%;table-layout: fixed;' />").attr("id", "myvergifyleadformtable");
								var divcontainer = jQuery("#leadgeneratorlistpopup");
								divcontainer.empty();
								divcontainer.append('<h3 style="font-size: 16pt; margin: 0px; padding: 0px; text-align: center; weight: bold;">Select A Form</h3><br />').append(tbl);
								jQuery("#myvergifyleadformtable").append('<tr><th style="text-align: left;padding-left:2px; padding-right:2px; border-bottom: 1px solid #CCCCCC; color: #808080; font-size: 13pt;">Form Title</th><th style="text-align: left;padding-left:2px; padding-right:2px; border-bottom: 1px solid #CCCCCC; color: #808080; font-size: 13pt;">Date Created</th></tr>');
								for (var i = 0; i < obj.length; i++) {
									var tr = "<tr>";
									var tdtitle = "<td style='padding-left:2px; padding-right:2px; padding-top: 2px;'><span style='cursor: pointer; color: blue;' id='vergifyleadform_" + obj[i]["Guid"] + "'>" + obj[i]["Title"] + "</span></td>";
									var tddatecreated = "<td style='padding-left:2px; padding-right:2px; padding-top: 2px;'>" + obj[i]["DateCreated"] + "</td></tr>";

									jQuery("#myvergifyleadformtable").append(tr + tdtitle + tddatecreated);
									var lead_gen_id = obj[i]["Guid"];
							        /*---------================================create-short-code-----=====================================================-*/ 
	
									jQuery("#vergifyleadform_" + obj[i]["Guid"]).click(function (event) {
										var leadformguid = event.target.id.replace('vergifyleadform_', '');
										var leadgeneratorshortcode = '[lead_generate foo="' + leadformguid + '"]';

										QTags.insertContent(leadgeneratorshortcode);
										divcontainer.hide();
									});

								}
								if (obj.length == 0)
								{
									divcontainer.empty();
									divcontainer.append('<span style="font-size: 16pt; weight: bold;">You haven\'t created any Lead Generator Forms </span><br /><br /><span><a target="_blank" href="https://app.vergify.com/user/login">Try Creating One</a> </span>')
								}
								divcontainer.append('<span style="position: absolute; right: 2px; top: 0; cursor: pointer;" title="Close Window" onclick="jQuery(this).parent().hide();"><img src="<?php echo plugins_url('vergify-crm') ?>/images/exit.png" /></span>');
								displaypopup(divcontainer);
							},
							error: function (request, status, error) {
								
							}
						});
			    }
				<?php } ?>	
				
				 function displaypopup(mypopup) {
                mypopup.css({ position: 'absolute', top: '50%', left: '50%', margin: '-' + (mypopup.height() / 2) + 'px 0 0 -' + (mypopup.width() / 2) + 'px' });
                mypopup.css("z-index", "9999999").show();
                if (mypopup.offset().top < 5) {
                mypopup.css({ top: '0', position: 'absolute' }).show();
                mypopup.css("margin-top", "4px").show();
                }
                mypopup.css("height", "auto").show();
                }
            </script>
        <?php
    }
}
add_action("admin_print_footer_scripts", "vergify_shortcode_button_script");

function vergify_frontheader() {
	echo ' <div id="leadgeneratorlistpopup" style="-webkit-box-shadow: 3px 3px 13px 1px rgba(128,128,128,1);-moz-box-shadow: 3px 3px 13px 1px rgba(128,128,128,1);box-shadow: 3px 3px 13px 1px rgba(128,128,128,1); display: none; width: 400px; height: 400px; border: 1px solid #D1C9C2; padding: 4px; background-color: white; "></div>';
     echo '<div id="surveylistpopup" style="-webkit-box-shadow: 3px 3px 13px 1px rgba(128,128,128,1);-moz-box-shadow: 3px 3px 13px 1px rgba(128,128,128,1);box-shadow: 3px 3px 13px 1px rgba(128,128,128,1); display: none; width: 400px; height: 400px; border: 1px solid #D1C9C2;  padding: 4px; background-color: white; "></div>';
}
add_action('admin_head', 'vergify_frontheader');

/*----=============================================================Lead-Generator-List-(get-post-editor)=========================================================================*/

function vergify_leadforgenshortcode_call99($atts){
if(is_page() || is_single()){
	 $url = plugins_url('vergify-crm');  
	 $atts = shortcode_atts(array(
	 'foo'=>'no',
	 ),$atts,'lead_generate');  
	 ?>

	 <div id="vergifyleadgenerator"></div>
     <script>
		jQuery(function () {
            var divcontainer = jQuery('#vergifyleadgenerator');
            var leadgeneratorshortcode = 'vergifyleadgenerator-<?php echo $atts['foo']; ?>'; 

			var leadgeneratorguid = leadgeneratorshortcode.replace('vergifyleadgenerator-', '');
            jQuery.ajax({
                type: "POST",
                contentType: "application/json; charset=utf-8",
                url: '<?php echo esc_url('https://app.vergify.com/service/wordpress/settings.asmx/getleadgenerator'); ?>',
                dataType: 'json',
                data: JSON.stringify({ 'leadformguid': leadgeneratorguid }),
                success: function (data) {
                    var surveyscript = data.d.slice(1, -1); 
                    divcontainer.empty();
                    divcontainer.prepend(surveyscript);
                },
                error: function (request, status, error) {
                    
                }
            });

        });
    </script> 
<?php	 	
}
}
add_shortcode( 'lead_generate', 'vergify_leadforgenshortcode_call99' );

/*----=============================================================Servay-List-(get-post-editor)=========================================================================*/

function vergify_surveyshortcode_call99($atts){
if(is_page() || is_single()){
	 $url = plugins_url('vergify-crm');  
	 $atts = shortcode_atts(array(
	 'foo'=>'no',
	 ),$atts,'vergifysurvey_code');  
	 ?>

	 <div id="vergifysurvey"></div>
     <script>
		jQuery(function () {
            var divcontainer = jQuery('#vergifysurvey');
            var getsurveyshortcode = 'vergifysurvey-<?php echo $atts['foo']?>';
            var surveyguid = getsurveyshortcode.replace('vergifysurvey-', '');
            jQuery.ajax({
                type: "POST",
                contentType: "application/json; charset=utf-8",
                url: '<?php echo esc_url('https://app.vergify.com/service/wordpress/settings.asmx/getsurvey'); ?>',
                dataType: 'json',
                data: JSON.stringify({ 'surveyguid': surveyguid }),
                success: function (data) {
                    var surveyscript = data.d.slice(1, -1); 
                    divcontainer.empty();
                    divcontainer.prepend(surveyscript);
                },
                error: function (request, status, error) {
                    
                }
            });

        });
    </script> 
<?php	
} 	
}
add_shortcode( 'vergifysurvey_code', 'vergify_surveyshortcode_call99' );



