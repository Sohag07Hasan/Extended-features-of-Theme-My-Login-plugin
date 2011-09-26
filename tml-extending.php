<?php
/*
 * plugin name: Theme My Login with .mil extention mail service
 * author: Mahibul Hasan
 * Description: It will work only Theme My Login plugin is activated. Make Sure Theme My Login plugin is activated.
 * plugin url: http://sohag07hasan.elance.com
 * author url: http://hasan-sohag.blogspot.com
 * 
 * */
 
$milExtendTML = new milExtendTML();
if($milExtendTML){
	add_action('init',array($milExtendTML,'newregistrationform'),12);
	add_action('init',array($milExtendTML,'force_reg_form'),50);
	add_action( 'authenticate', array( $milExtendTML, 'authenticate' ), 80, 3 );
}
 
 // extending class
class milExtendTML{
	
	//some important constant
	var $default_message = '<p class="error"> ERROR: Your email address is not from some .mil domain ! But you can still use this email to register. Please upload the following files for manual verification . </p>';
	
	var $error = array();
	var $pass_m = '';
	var $login_m = '';
	var $email_m = '';
	var $reg_m = '';
	var $image_m = '';
	var $militaryid_m = '';
	var $license_m = '';
	
	
	
	//construction
	function __construct(){
		//adding filters
		add_filter('user_registration_email',array($this,'emailchecking'));
		
		//add_filter('retrieve_password_message',array($this,'password_retrieval'),50,3);
		add_filter( 'retrieve_password_title', array( $this, 
		'retrieve_pass_title' ), 10, 2 );
		
		include dirname(__FILE__).'/tml-extending-options.php';
		
		//add_filter( 'login_redirect', array( &$this, 'login_control' ), 30, 2 );
		
		
	}
	
		
	//pass retrieval title
	function retrieve_pass_title( $title, $user_id){
				
		$user_status = get_user_meta($user_id, 'milext_theme_mylogin_verification', true);
		if($user_status == 'no'){
			$link = get_option('home').'/login/?action=manualregister&milext=error&tml=active&verify=no';
			header("Location:$link");
			//var_dump($link);
			exit;
		}
		else{
			$_title = $GLOBALS['theme_my_login']->options->get_option( array( 'email', 'retrieve_pass', 'title' ) );
			
			return empty( $_title ) ? $title : Theme_My_Login_Custom_Email::replace_vars( $_title, $user_id );
		}
		
	}
	
	
	
	//email checking if it has .mil extention
	function emailchecking($email){
		 	
		preg_match('/[@].+$/',$email,$b);
		preg_match('/.mil/',$b[0],$c);
		
		
		if($c[0] == '.mil'){
			return $email;
		}
		else{
			$link = get_option('home').'/login/?action=manualregister&milext=error&tml=active';
			
			header("Location:$link");
			exit;
		}
	}
	
	//function to include custom registration form
	function newregistrationform(){
		
		if($_GET['milext'] == 'error' && $_GET['tml'] == 'active' && $_GET['action'] == 'manualregister'){	
			add_filter('tml_template',array($this,'get_the_registrationform'));		
		}
	}
	
	//changing the default registration form
	function get_the_registrationform($link){
		//var_dump($link);
		//exit;		
		$link = dirname(__FILE__).'/register-form.php';
		return $link;
	}
	
	
	/**
	 * Handles registering a new user.
	 *
	 * @since 6.0
	 * @access public
	 *
	 * @param string $user_login User's username for logging in
	 * @param string $user_email User's email address to send password and add
	 * @return int|WP_Error Either user's ID or error on failure.
	 */
	function register_new_user( $user_login, $user_email,$pass1,$pass2,$allpictures=array()) {
		//var_dump($allpictures);
		
		//exit;		
		
		$user_id = '';
		$sanitized_user_login = sanitize_user( $user_login );		
		
		// Check the username
		if ( $sanitized_user_login == '' ) {
						
			$this->login_m = '<strong>USERNAME</strong>: Please enter a username' ;
			$this->error[] = 'login';
			
		} 
		elseif ( !validate_username( $user_login ) ) {
			
			$this->login_m = '<strong>USERNAME</strong>: This username is invalid because it uses illegal characters. Please enter a valid username.'; 
			$sanitized_user_login = '';
			$this->error[] = 'login';
		}
		elseif ( username_exists( $sanitized_user_login ) ) {
			
			$this->login_m = '<strong>USERNAME</strong>: This username is already registered, please choose another one.' ;
			$this->error[] = 'login';
		}

		// Check the e-mail address
		if ( '' == $user_email ) {
			
			$this->email_m = '<strong>Email</strong>: Please type your e-mail address.' ;
			$this->error[] = 'email' ;
		}
		elseif ( !is_email( $user_email ) ) {
			$this->error[] = 'email' ;
			$user_email = '';
			$this->email_m = '<strong>Email</strong>: Invalid Email Address.' ;
		} 
		elseif ( email_exists( $user_email ) ) {
			$this->error[] = 'email' ;
			$this->email_m = '<strong>Email</strong>: This email is already registered, please choose another one.' ;
		}
		
		//manipulation of files and link retrieval
		$image_link = $this->attachment_add($allpictures["image"],'image','Your Picture');
		
		$license_link = $this->attachment_add($allpictures["license"],'license','Driving License');
		
		$militaryid_link = $this->attachment_add($allpictures["militaryid"],'militaryid','Military ID');
		
				
		//get password
		$user_pass = $this->get_password($pass1,$pass2);
		
		if(count($this->error) == 0) : 
		
			$user_id = wp_create_user( $sanitized_user_login, $user_pass, $user_email );
			if ( !$user_id ) {
								
				$this->reg_m = '<p class="message"><strong>ERROR</strong>: Couldn&#8217;t register you... please contact the <a href="mailto:%s">webmaster</a> !'.get_option( 'admin_email' ).'</p>' ;
			}
			else{
				$this->reg_m = '<p class="message">Your registration was successful. Admin will confirm your registration and a confirmation mail will be sent.</p>';
				
				$att_opts = array(
				'post_mime_type'=> $image['type'],
				'post_title' => $image['name'],
				'post_content' => '',
				'post_status' => 'inherit'
				);
				
				$attach_id = wp_insert_attachment( $att_opts, $attachmentlink);
				
				//Set update user option
				update_user_option( $user_id, 'default_password_nag', true, true );
							
				update_user_meta( $user_id, 'milext_theme_mylogin_verification', 'no');update_user_meta( $user_id, 'milext_theme_mylogin_password', $user_pass );
				$activationkey = $this->generate_password(20,false);
				global $wpdb;
				
				$wpdb->update( $wpdb->users, array('user_activation_key' => $activationkey),array('ID'=>$user_id),array('%s'),array('%d'));
				
				
				$this->emailsend($user_email,$user_id,$image_link,$license_link,$militaryid_link,$activationkey);
			}
			
			//adding attachment
			

			
		endif; 
		
	}
	
	//email the message to both the user and admin
	function emailsend($email,$user_id,$image_link="",$license_link="",$militaryid_link="",$activationkey=""){
		
		if(!function_exists('wp_mail')) : 
			include ABSPATH.'wp-includes/pluggable.php' ;
		endif;
		$blogname = get_option('blogname');
			
		$value = get_option('extend_tml_tml');
		$adminmail = $value['adminmail'] ;	
		
		$headers = 'From : '.$blogname.' < '.$adminmail.' >' . "\r\n" .
		'Reply-To: '.$adminmail . "\r\n" .
		'X-Mailer: PHP/' . phpversion();
		$subject = "New Registration";
		
		$message = "Someone wants to register your site.\n\n Click here to see the image \n\n".$image_link."\n\n";
		
		$message .= "Click here to see this driving license \n\n".$license_link."\n\n";
		$message .= "Click here to see this Military ID \n\n".$militaryid_link."\n\n";
		
		$message .= " User has email address \n\n $email \n\n Click here to approve the registration. \n\n";
		
		$link = get_option('home').'/login/?action=manualregister&milext=error&tml=active&approve=okay&authtoken='.$activationkey ;
		
		$message .= $link;
		//email to the admin
		wp_mail($adminmail,$subject,$message,$headers);
		
		//email to the user
		$m_user = "Thank you for registering the site \n\n Your account information will be checked by the admin and a confirmation link with user name and password will be sent. ";
		wp_mail($email,'Thank You',$m_user,$headers);
		
	}
	
	
	//password checking
	function get_password($a,$b){
		
		if(strlen($a) < 6){
			$this->pass_m = '<strong>PASSWORD : </strong>Password must be at least 6 character long';
			$this->error[] = 'pass';
			return null;
		}
		if($a != $b){
			$this->pass_m = '<strong>PASSWORD: </strong>Password not matched';
			$this->error[] = 'pass';
			return null;
		}
		
		return $a;
	}
	
	//messages
	function get_messages($a=''){
	
		return '<p class="error">' . $this->login_m .'<br/>'. $this->pass_m .'<br/>'. $this->email_m .'<br/>'.$this->image_m. '<br/>' . $this->license_m. '<br/>'. $this->militaryid_m .'</p>' ;
	}
	
	//attachment manipulation
	function attachment_add($image,$name,$header){
		
		if($image['type'] == 'image/jpeg' || $image['type'] == 'image/gif' ){
			
			$temp = $image['tmp_name'];
			$updir = wp_upload_dir();
			
			$basedir = $updir['basedir'];
			if( !is_dir( $basedir.'/extthememylogin' ) ) @ mkdir( $basedir.'/extthememylogin' );
			$t = time();
			//$name = $image['name'];
			
			$s = preg_replace( '/([^.]+)/', "\${1}--$t", $name, 1 );
			$s = preg_replace('/[ ]/','',$s,1);
			@ move_uploaded_file($temp,$basedir.'/extthememylogin/'.$s);
						
			$attachmentlink = $updir['baseurl'].'/extthememylogin/'.$s;
			return $attachmentlink ; 
		}
		else{
			$this->error[] = $name;
			//$this->image_m = '<strong>Attachment: </strong> Only gif/jpg format is allowed';
			$message = '<strong>' . $header . ': </strong> Only gif/jpg format is allowed';
			
			switch($name){
				case 'image' :
					$this->image_m = $message;
					break;
				case 'license' : 
					$this->license_m = $message;
					break;
				case 'militaryid' : 
					$this->militaryid_m = $message;
					break;
					
			}
			return false;
		}
		
	}
	
	//password generator
	function generate_password( $length = 12, $special_chars = true, $extra_special_chars = false ) {
			$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
			if ( $special_chars )
				$chars .= '!@#$%^&*()';
			if ( $extra_special_chars )
				$chars .= '-_ []{}<>~`+=,.;:/?|';

			$password = '';
			for ( $i = 0; $i < $length; $i++ ) {
				$password .= substr($chars, rand(0, strlen($chars) - 1), 1);
			}
			
			return $password ;
			
	}
	
	//confirmation mail
	function confirmation_mail($to,$name,$pass){
		
		if(!function_exists('wp_mail')) : 
			include ABSPATH.'wp-includes/pluggable.php' ;
		endif;
		$blogname = get_option('blogname');
		
		$value = get_option('extend_tml_tml');
		$adminmail = $value['adminmail'] ;		
		
		$headers = 'From : '.$blogname.' < '.$adminmail.' >' . "\r\n" .
		'Reply-To: '.$adminmail . "\r\n" .
		'X-Mailer: PHP/' . phpversion();
		
		$subject = "Congratulations !";
		$message = "Admin has just approved your registration.\n\n Here is your details \n\n";
		$message .= "username : $name \n\n password : $pass \n\n";
		$message .= "Click the link to login the site \n\n ";
		$link = get_option('home').'/login/?action=login';
		
		$message .= $link;
		//email to the admin
		wp_mail($to,$subject,$message,$headers);
		
	}
	
	//forcing including the registration form
	function force_reg_form(){
		//localhost/wordpresslatest/login/?action=manualregister&milext=error&tml=active&approve=okay&authtoken=EVaDLPYBcuhTYPFHjnun
		if($_GET['action'] == 'manualregister' && $_GET['milext'] == 'error' && $_GET['tml'] == 'active' && $_GET['approve'] == 'okay' && $_GET['authtoken']){
			get_header();					
			include dirname(__FILE__).'/register-form.php';
									
		}
		
	}
	
	//authenticating while login
	function authenticate( $user, $username, $password ) {
		$user_id = $user->ID;
		$user_status = get_user_meta($user_id, 'milext_theme_mylogin_verification', true);
				
		if($user_status == 'no'){
			$link = get_option('home').'/login/?action=manualregister&milext=error&tml=active&verify=no';
			header("Location:$link");
			//var_dump($link);
			exit;
		}
		return $user;				
		
	}
	
}



?>
