<?php
/*
It is a custom registration form only to checkig the .mil extention mail domain
*/
?>
<?php
	
	global $milExtendTML;
	
	$submit = false;
	$allpictures = array();
	
	$retirvepass = $_GET['verify'];
	
	//form submisstion 
	if($_POST['manulregistration'] == 'manualsubmit'):
				
		$submit = true;
		//get the user
		$user = $_POST['user'];
		//setting the values
		setcookie('manualregistration_login',trim($user['login']),0,'/');
		setcookie('manualregistration_email',trim($user['email']),0,'/');
		
		//temporarliy saving data
		//update_option('milext_temp')		
		$allpictures['image'] = $_FILES['image'];
		$allpictures['license'] = $_FILES['license'];
		$allpictures['militaryid'] = $_FILES['militaryid'];
		
		$milExtendTML->register_new_user($user['login'],$user['email'],$user['pass1'],$user['pass2'],$allpictures);
			
		
	endif;

?>
<div class="login" id="theme-my-login">

	<?php 
		
		//registration confirmation by admin and some email function
		if($_GET['approve'] == 'okay' && $_GET['authtoken'] ){
			$authtoken = $_GET['authtoken'];
			//if(is_admin()){
			if(current_user_can('create_users')){
			
				global $wpdb;
				$user_id = $wpdb->get_var("SELECT ID FROM $wpdb->users WHERE user_activation_key = '$authtoken'") ;
				if($user_id){
					$username = $wpdb->get_var("SELECT user_login FROM $wpdb->users WHERE user_activation_key = '$authtoken'");
					$usermail = $wpdb->get_var("SELECT user_email FROM $wpdb->users WHERE user_activation_key = '$authtoken'");
					$password = get_user_meta($user_id, 'milext_theme_mylogin_password', true);
					
					//emtying the database 
					delete_user_meta( $user_id, 'milext_theme_mylogin_verification', 'no' );
					delete_user_meta( $user_id, 'milext_theme_mylogin_password',$password );
					$wpdb->update($wpdb->users, array('user_activation_key' => ''), array('ID' => $user_id) );
					
					//confirmation mail
					$milExtendTML->confirmation_mail($usermail,$username,$password)	;			
					
					$authemessage = '<p class="message">Activation successful !<br/> The user will get an email with username and password.<br/> If you further want to modify the users, you can do this from admin panel</p>';
					
					$alluserlink = get_option('home').'/wp-admin/users.php';
					$dashboard = get_option('home').'/wp-admin/';
					$home = get_option('home');
															
					echo $authemessage ;
					
					echo '</div>';
					get_sidebar();
					get_footer();
									
				}
				else{
					
					echo '<p class="error" ><strong>ERROR</strong> : Invalid URL !</p>';
					echo '</div>';
					get_sidebar();
					get_footer();
				}
				
				exit;
			}
			else{
				$link = get_option('home').'/login/';
				$loginbutton = '<a href="'.$link.'"><input type="button" value="login"></a>';
				echo '<p class="error"><strong>ERROR:</strong> You have no admin previlege to access the page. Please Log in first and try again</p>';
				echo $loginbutton;
				echo '</div>';
				get_sidebar();
				get_footer();
				exit;
			}
		}
		
		$template->the_action_template_message( 'register' );
		//$action = get_option('home').'/login/?action=manualregister&milext=error&tml=form';
	 ?>
	<?php 
		
		//echo ($submit) ? '<p class="message">'.$this->reg_m.'</p>' : $milExtendTML->get_messages() ;
		 echo (count($milExtendTML->error) == 0)? '' : $milExtendTML->get_messages() ;
		
				
		echo $milExtendTML->reg_m ;
		if($retirvepass == 'no'){
			echo '<p class="error">Your account is not still activated by admin ! </p>';
		}
		else{
			echo (!$submit ) ? $milExtendTML->default_message : '';
		}
		
		
		
	?>
	<?php
		$username = $_COOKIE["manualregistration_login"];
		$useremail = $_COOKIE["manualregistration_email"];
		
	
	?>
	
	
    <form name="registerform" id="registerform" action="" method="post" enctype="multipart/form-data">
        <p>
            <label for="user[login]"><?php _e( 'Username', 'theme-my-login' ) ?></label>
            <input type="text" name="user[login]" id="user_login" class="input" value="<?php echo $username ?>" size="20" />
        </p>
        <p>
            <label for="user[email]"><?php _e( 'E-mail', 'theme-my-login' ) ?></label>
            <input type="text" name="user[email]" id="user_email" class="input" value="<?php echo $useremail ; ?>" size="20" />
        </p>
        
        <p>
            <label for="user[pass1]"><?php _e( 'Password:', 'theme-my-login' ) ?></label>
            <input type="password" name="user[pass1]" id="pass1" class="input" value="" size="20" />
        </p>
        
        <p>
            <label for="user[pass2]"><?php _e( 'Confirm Password:', 'theme-my-login' ) ?></label>
            <input type="password" name="user[pass2]" id="pass2" class="input" value="" size="20" />
        </p>
        
         <p>
            <label for="image"><?php _e( 'Upload your picture ( gif/jpg ) :', 'theme-my-login' ) ?></label>
            <input type="file" name="image" class="input" />
        </p>
        
        <p>
            <label for="license"><?php _e( 'Upload your driving license ( gif/jpg ) :', 'theme-my-login' ) ?></label>
            <input type="file" name="license" class="input" />
        </p>
        
        <p>
            <label for="militaryid"><?php _e( 'Upload your military id ( gif/jpg ) :', 'theme-my-login' ) ?></label>
            <input type="file" name="militaryid" class="input" />
        </p>
		
		 
		<input type="hidden" name="manulregistration" value="manualsubmit" />
		<input type="submit" name="wpmanulregistration" value="Register" />
	
    </form>
    
	<?php $template->the_action_links( array( 'register' => false ) ); ?>
</div>
