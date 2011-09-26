<?php
/*
 *plugin settings section
 * 
 * */
 
 if(!class_exists('tem_extending_options')) : 
	class tem_extending_options{

		//creating an options page
		function optionsPage(){
			add_options_page('Theme MY Login Extened','Theme MY Login Extened','activate_plugins','tml_extend',array($this,'optionsPageDetails'));
		}

		//creating options page in admin panel
		function optionsPageDetails(){
			//starin html form
		?>
			<div class="wrap">
				<?php screen_icon('options-general'); ?>
				<h2>Extended Theme My Login</h2>
				<form action="options.php" method="post">
					<?php
						settings_fields('tml_extend_options');
						do_settings_sections('tml_extend');
					?>
					<br/>
					<br/>
					<input type="submit" class="button-primary" value="submit" />
				</form>
			</div>
			
		<?php

		}

		//registering options
		function registerOption(){
			register_setting('tml_extend_options','extend_tml_tml',array($this,'data_validation'));
			add_settings_section('first_section',' ',array($this,'first_settings_section'),'tml_extend');

			add_settings_field('first_input','<strong>Email ( where notifications go )</strong> ',array($this,'first_settings_field'),'tml_extend','first_section');			
			//add_settings_field('second_input','Message for visitors',array($this,'second_settings_field'),'tml_extend','first_section');
		}


		//first settins sections callback
		function first_settings_section(){
			echo '';

		}		


		//first_settings_field for the first sections
		function first_settings_field(){
			$value = get_option('extend_tml_tml');
			
			echo '<input style="width:220px" type="text" name="extend_tml_tml[adminmail]" value="'.$value["adminmail"].'" />';		
			
		}

		//second input for the second section
		function second_settings_field(){
			$value = get_option('extend_tml_tml');		
			echo '<textarea cols="50" rows="5" name="extend_tml_tml[visitorsmessage]">'.$value['visitorsmessage'].'</textarea>';
		}


		//validating data
		function data_validation($data){			
			$sdata = array();
			$sdata['adminmail'] = preg_replace('/[ ]/','',$data['adminmail']);
			return $sdata;
						
		}
	}

	$tml_extoption = new tem_extending_options();
	add_action('admin_menu',array($tml_extoption,'optionsPage'));
	add_action('admin_init',array($tml_extoption,'registerOption'));
endif;

?>
