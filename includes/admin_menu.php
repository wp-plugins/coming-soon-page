<?php 

/*############  Coming Soon Admin Menu Class ################*/

class coming_soon_admin_menu{
	
	private $menu_name;
	private $databese_parametrs;
	private $plugin_url;
	
	function __construct($param){
		$this->menu_name=$param['menu_name'];
		$this->databese_parametrs=$param['databese_parametrs'];
		if(isset($params['plugin_url']))
			$this->plugin_url=$params['plugin_url'];
		else
			$this->plugin_url=trailingslashit(dirname(plugins_url('',__FILE__)));

		add_action( 'wp_ajax_coming_soon_page_save', array($this,'save_in_databese') );
		add_action( 'wp_ajax_coming_soon_send_mail', array($this,'sending_mail') );
	}
	
	public function create_menu(){
		$manage_page = add_menu_page( $this->menu_name, $this->menu_name, 'manage_options', str_replace( ' ', '-', $this->menu_name), array($this, 'main_menu_function'));
		add_action('admin_print_styles-' .$manage_page, array($this,'menu_requeried_scripts'));	
	}
	
	public function menu_requeried_scripts(){
		wp_enqueue_script('wp-color-picker');
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'jquery-ui-datepicker' ); 
		wp_enqueue_style('jquery-ui-style');
		wp_enqueue_script( 'jquery-ui-slider' );
		wp_enqueue_style('coming-soon-admin-style');
		
		
		if (function_exists('add_thickbox')) add_thickbox();
	}
	
	private function generete_parametrs($page_name){
		$page_parametrs=array();
		if(isset($this->databese_parametrs[$page_name])){
			foreach($this->databese_parametrs[$page_name] as $key => $value){
				$page_parametrs[$key]=get_option($key,$value);
			}
			return $page_parametrs;
		}
		return NULL;
		
	}
	public function sending_mail(){
		$mailing_lists=json_decode(stripslashes(get_option('users_mailer','')), true);
		if($mailing_lists==NULL)
			$mailing_lists=array();
		$not_sending_mails=array();
		$sending_mails=array();
		if(!(isset($_POST['massage_title']) && $_POST['massage_title']!='')){
			echo 'Type Message Title';
			die();
		}
		if(!(isset($_POST['massage_description']) && $_POST['massage_description']!='')){
			echo 'Type Message';
			die();
		}		
		foreach($mailing_lists as $key => $mail){
			$send=wp_mail( $mail, $_POST['massage_title'], $_POST['massage_description']);
			if(!$send){
				array_push($not_sending_mails,$mail);
			}
			else
			{
				array_push($sending_mails,$key);
			}
		}
		foreach($sending_mails as $key){
			unset($mailing_lists[$key]);
		}
		update_option('users_mailer',json_encode($mailing_lists));
		if(count($not_sending_mails)){
			foreach($not_sending_mails as $errors){
				echo $errors.',';
			}
			echo " Mails Not sended";
			exit;
		}
		die('Your message was sent successfully.');
		
	}
	public function save_in_databese(){
		$kk=1;		
		if(isset($_POST['coming_soon_options_nonce']) && wp_verify_nonce( $_POST['coming_soon_options_nonce'],'coming_soon_options_nonce')){
			foreach($this->databese_parametrs[$_POST['curent_page']] as $key => $value){
				if(isset($_POST[$key]))
					update_option($key,$_POST[$key]);
				else{
					$kk=0;
					echo 'parametr'.$key.' ERROR NOT SAVE';
				}
			}	
		}
			if($kk){ echo "Changes have been saved";}
		die();
	}
	
	public function main_menu_function(){
		
		?>
        <style>
        #saving_soon{
			background-color:rgba(102,102,102,0.5);
			position:fixed;
			width:100%;
			z-index:99999;
			
		}
		#loading_text{
			margin: auto;
			position: absolute;
			top: 0; left: 0; bottom: 0; right: 0;
			width: 40px;
			height: 40px;
		 }
        .group{
			padding-left:20px;
			padding-bottom:20px;
		}
        </style>
        <script>
	jQuery(document).ready(function() {
			var link_my_plugin="http://wpdevart.com/wordpress-coming-soon-plugin/"
		if (typeof(localStorage) != 'undefined' ) {
			active_tab = localStorage.getItem("active_tab");
			if(active_tab==link_my_plugin)
				localStorage.setItem("active_tab",'options-group-1-tab');
		}
		if (active_tab != '' && jQuery(active_tab).length ) {
			jQuery(active_tab).fadeIn();
		} else {
			jQuery('.group:first').fadeIn();
		}
		jQuery('.group .collapsed').each(function(){
			jQuery(this).find('input:checked').parent().parent().parent().nextAll().each( 
				function(){
					if (jQuery(this).hasClass('last')) {
						jQuery(this).removeClass('hidden');
							return false;
						}
					jQuery(this).filter('.hidden').removeClass('hidden');
				});
		});
		if (active_tab != '' && jQuery(active_tab + '-tab').length ) {
			jQuery(active_tab + '-tab').addClass('nav-tab-active');
		}
		else {
			jQuery('.nav-tab-wrapper a:first').addClass('nav-tab-active');
		}
		
		jQuery('.nav-tab-wrapper a').click(function(evt) {
			jQuery('.nav-tab-wrapper a').removeClass('nav-tab-active');
			var clicked_group = jQuery(this).attr('href');
			if(jQuery(this).attr('href')==link_my_plugin){
				window.location=link_my_plugin;
				return false;
			}
			jQuery(this).addClass('nav-tab-active').blur();			
			if (typeof(localStorage) != 'undefined' ) {
				localStorage.setItem("active_tab", jQuery(this).attr('href'));
			}
			jQuery('.group').hide();
			jQuery(clicked_group).fadeIn();
			evt.preventDefault();
			
			// Editor Height (needs improvement)
			jQuery('.wp-editor-wrap').each(function() {
				var editor_iframe = jQuery(this).find('iframe');
				if ( editor_iframe.height() < 30 ) {
					editor_iframe.css({'height':'auto'});
				}
			});
		
		});
								
		jQuery('.group .collapsed input:checkbox').click(unhideHidden);
					
		function unhideHidden(){
			if (jQuery(this).attr('checked')) {
				jQuery(this).parent().parent().parent().nextAll().removeClass('hidden');
			}
			else {
				jQuery(this).parent().parent().parent().nextAll().each( 
				function(){
					if (jQuery(this).filter('.last').length) {
						jQuery(this).addClass('hidden');
						return false;		
						}
					jQuery(this).addClass('hidden');
				});
								
			}
		}
		
		// Image Options
		jQuery('.of-radio-img-img').click(function(){
			jQuery(this).parent().parent().find('.of-radio-img-img').removeClass('of-radio-img-selected');
			jQuery(this).addClass('of-radio-img-selected');		
		});
			
		jQuery('.of-radio-img-label').hide();
		jQuery('.of-radio-img-img').show();
		jQuery('.of-radio-img-radio').hide();
		});
	</script>
    <div class="main_div">
        <h2>Coming Soon</h2>
        <h2 class="nav-tab-wrapper in-page-tab">
            <a id="options-group-1-tab" class="nav-tab" title="SEO" href="#options-group-1">General Settings</a>
            <a id="options-group-2-tab" class="nav-tab" title="Layout Editor" href="#options-group-2">Design Option</a>
            <a id="options-group-3-tab" class="nav-tab" title="General" href="#options-group-3">Mailing List</a>
            <a id="options-group-4-tab" class="nav-tab" title="Homepage" href="#options-group-4">Liev Preview</a>
			<a id="options-group-4-tab" class="nav-tab" title="Homepage" href="http://wpdevart.com/wordpress-coming-soon-plugin/"><span style="color: rgba(216, 19, 19, 1); font-weight: bold; font-size: 21px;">Upgrade to Pro Version</span></a>

        </h2>
        <div id="optionsframework-metabox" class="metabox-holder">
            <div id="optionsframework" class="postbox">
                
                    <div id="options-group-1" class="group general_settings" style="display: none;"><h3>General Settings</h3> <?php $this->generete_general_settings_page($this->generete_parametrs('general'));  ?></div>
                    <div id="options-group-2" class="group design_settings" style="display: none;"><h3>Design Options</h3> <?php $this->generete_design_options_page($this->generete_parametrs('design')); ?></div>
                    <div id="options-group-3" class="group templates" style="display: none;"><h3>Mailing List</h3><?php $this->generete_mailing_list_options_page($this->generete_parametrs('mailing_list')); ?></div>
                    <div id="options-group-4" class="group Homepage" style="display: none;"><h3><span style="color:red"> Please refresh this page to see changes(F5).</span></h3><?php $this->generete_mailing_live_previev_page($this->generete_parametrs('mailing_list')); ?></div>
            
			</div>
         </div>
      </div>
       
         <?php
	}
	
	
	public function generete_general_settings_page($page_parametrs){

		?>
        <form method="post" id="coming_soon_options_form" action="admin.php?page='<?php echo  str_replace( ' ', '-', $this->menu_name); ?>'">	
            <div class="option_group">
            	<h4 class="option_title">Coming Soon Mode</h4>
                <label for="coming_soon_page_modeon">Enable Coming Soon</label>
                <input type="radio" onchange="generete_radio_input_hidden(this)"  name="coming_soon_page_moderadio" id="coming_soon_page_modeon" value="on" <?php checked($page_parametrs['coming_soon_page_mode'], "on"); ?> />
                <br /><br />
                <label for="coming_soon_page_modeoff">Disable Coming soon</label>
                <input type="radio" onchange="generete_radio_input_hidden(this)" name="coming_soon_page_moderadio" id="coming_soon_page_modeoff" value="off" <?php checked($page_parametrs['coming_soon_page_mode'], "off"); ?>/>
               
                <input type="hidden" class="insert_value_this" name="coming_soon_page_mode" id="coming_soon_page_mode" value="<?php echo $page_parametrs['coming_soon_page_mode'] ?>"  />
            </div>
             <div class="option_group">
             	<h4  class="option_title">Mailing List </h4>

                <label for="enable_mailing_liston">Enable Mailing List</label>
                <input type="radio" onchange="generete_radio_input_hidden(this)"  name="enable_mailing_listradio" id="enable_mailing_liston" value="on" <?php checked($page_parametrs['enable_mailing_list'], "on"); ?> />
				<br /><br />
                <label for="enable_mailing_listoff">Disable Mailing List</label>
                <input type="radio" onchange="generete_radio_input_hidden(this)" name="enable_mailing_listradio" id="enable_mailing_listoff" value="off" <?php checked($page_parametrs['enable_mailing_list'], "off"); ?>/>

                <input type="hidden" class="insert_value_this" name="enable_mailing_list" id="enable_mailing_list" value="<?php echo $page_parametrs['enable_mailing_list'] ?>"  />
            </div>
            <div class="option_group">
                <h4 class="option_title">Mailing list input text</h4>
                <input type="text" size="60" value="<?php echo htmlspecialchars(stripslashes($page_parametrs['mailing_list_value_of_emptyt'])) ?>" id="mailing_list_value_of_emptyt" name="mailing_list_value_of_emptyt" />
            </div>
            <div class="option_group">
                <h4 class="option_title">Mailing list send button text</h4>
                <input type="text" size="60" value="<?php echo htmlspecialchars(stripslashes($page_parametrs['mailing_list_button_value'])) ?>" id="mailing_list_button_value" name="mailing_list_button_value" />
            </div>
            <div class="option_group">
                <h4 class="option_title">Logo</h4>
                <div id="upload_image">
                    <input type="text" class="upload" id="coming_soon_page_page_logo" name="coming_soon_page_page_logo" size="60" value="<?php echo $page_parametrs['coming_soon_page_page_logo'] ?>"/>
                    <input class="upload-button" type="button" value="Upload Image"/>
                </div>
            </div>            
            <div class="option_group">
                <h4 class="option_title">Title</h4>
                <input type="text" size="60" value="<?php echo htmlspecialchars(stripslashes($page_parametrs['coming_soon_page_page_title'])) ?>" id="coming_soon_page_page_title" name="coming_soon_page_page_title" />
            </div>

            <div class="option_group">
                <h4 class="option_title">Message</h4>
               <div style="width:600px"> <?php wp_editor( stripslashes($page_parametrs['coming_soon_page_page_message']), 'coming_soon_page_page_message', $settings = array('media_buttons'=>false,'textarea_rows'=>5) ); ?></div>
            </div>
            <div class="option_group">
                <h4 class="option_title">Meta Keywords</h4><br />
                    <input type="text" class="upload" id="coming_soon_page_meta_keywords" name="coming_soon_page_meta_keywords" size="60" value="<?php echo $page_parametrs['coming_soon_page_meta_keywords'] ?>"/>
            </div>
            <div class="option_group">
                <h4 class="option_title">Meta Description</h4>
                    <input type="text" class="upload" id="coming_soon_page_meta_description" name="coming_soon_page_meta_description" size="60" value="<?php echo $page_parametrs['coming_soon_page_meta_description'] ?>"/>
            </div>

            <div class="option_group">
                <h4 class="option_title">Countdown(Timer)</h4><br />
               <?php  $countdown= json_decode(stripslashes($page_parametrs['coming_soon_page_countdown']), true); 		   ?>
                    <input type="text" onchange="refresh_countdown()"  placeholder="day"   id="coming_soon_page_countdownday" size="6" value="<?php if(isset($countdown['days'])) echo $countdown['days']; ?>"/>
                    <input type="text"  onchange="refresh_countdown()"  placeholder="housrse" id="coming_soon_page_countdownhour" size="6" value="<?php if(isset($countdown['hours'])) echo $countdown['hours']; ?>"/>
                    <input type="text"  onchange="refresh_countdown()"  placeholder="Start Date"  id="coming_soon_page_countdownstart_day" size="12" value="<?php if(isset($countdown['start_day'])) echo $countdown['start_day']; ?>"/>
                   <h4 style="margin-top: 26px;" class="option_title">Continue showing Coming soon page after ending date.</h4>
					<input type="checkbox" onchange="refresh_countdown()" <?php checked($countdown['countedown_on'], "on"); ?>  value="on" id="coming_soon_page_countdownstart_on"/>

				   <input type="hidden" value='<?php echo stripslashes($page_parametrs['coming_soon_page_countdown']) ?>' id="coming_soon_page_countdown" name="coming_soon_page_countdown" />
               
            </div>
            <div class="option_group" >
             	<h4 class="option_title">Disable coming soon page for this ips</h4>
                <div id="no_blocked_ips">
                </div>
            </div>
            <div class="option_group" >
             	<h4 class="option_title">Disable coming soon page for this urls <span style="color:rgba(216, 19, 19, 1);">Pro feature!</span></h4>
                <div class="emelent_coming_soon_page_showed_urls"> 
					<input onclick="alert(text_of_upgrate_version); return false" type="text" placeholder="Type Ip URl" value="">
					<span class="remove_element remove_element_coming_soon_page_showed_urls"></span> 				
                </div>
            </div>
            <div class="option_group">
                <h4 class="option_title">Facebook</h4><br />               
                <input type="text" class="upload" id="coming_soon_page_facebook" name="coming_soon_page_facebook" size="60" value="<?php echo $page_parametrs['coming_soon_page_facebook'] ?>"/>
            </div>
            <div class="option_group">
                <h4 class="option_title">Twitter</h4><br />
                <input type="text" class="upload" id="coming_soon_page_twitter" name="coming_soon_page_twitter" size="60" value="<?php echo $page_parametrs['coming_soon_page_twitter'] ?>"/>
            </div>
            <div class="option_group">
                <h4 class="option_title">Google Plus</h4><br />
                <input type="text" class="upload" id="coming_soon_page_google_plus" name="coming_soon_page_google_plus" size="60" value="<?php echo $page_parametrs['coming_soon_page_google_plus'] ?>"/>
            </div>
            <div class="option_group">
                <h4 class="option_title">YouTube</h4><br />
                <input type="text" class="upload" id="coming_soon_page_youtube" name="coming_soon_page_youtube" size="60" value="<?php echo $page_parametrs['coming_soon_page_youtube'] ?>"/>
            </div>
            
            <div class="option_group">
                <h4 class="option_title">Instagram</h4><br />
                <input type="text" class="upload" id="coming_soon_page_instagram" name="coming_soon_page_instagram" size="60" value="<?php echo $page_parametrs['coming_soon_page_instagram'] ?>"/>
            </div>
            <button type="button" id="save_button" class="save_button button button-primary">Save General Settings</button>
            <?php wp_nonce_field('coming_soon_options_nonce','coming_soon_options_nonce'); ?>
        </form>
         
         <script>
		 
		 
		 ///////////////					///////////////
		 ///////////////	MANY INPUTS		///////////////
		 ///////////////					///////////////
		 var many_inputs={
				main_element_for_inserting_element:'no_blocked_ips',
				element_name_and_id:'coming_soon_page_showed_ips',
				value_jsone_encoded:'',
				jsone_no_any_problem:1,
				placeholder:'Type Ip Here',
				
				// create all elements
				creates_elements:function(){
					var local_this=this;
					
					try {
						var object_value=JSON.parse(this.value_jsone_encoded);
					}
					catch (err) {
					  this.jsone_no_any_problem=0;
					}
					
					if(this.jsone_no_any_problem){
						for(key in object_value){
							var element=this.creat_single_element(object_value[key]);
							jQuery('#'+this.main_element_for_inserting_element).append(element);
						}
					}
					
					var element=this.creat_single_element();
					jQuery('#'+this.main_element_for_inserting_element).append(element);
					
                	var hidden_element_for_values= '<input type="hidden" value="" id="'+this.element_name_and_id+'" name="'+this.element_name_and_id+'" />'
					jQuery('#'+this.main_element_for_inserting_element).prepend(hidden_element_for_values);
					local_this.insert_value_on_hidden_element();
					
				},
		
				// function for creating element
				creat_single_element:function(element_value){
					var local_this=this;
					element_value = typeof element_value !== 'undefined' ? element_value : '';
					jQuery('#'+this.main_element_for_inserting_element).append(element=' <div class="emelent_'+this.element_name_and_id+'"> <input type="text" placeholder="'+this.placeholder+'" value="'+element_value+'" /><span class="remove_element remove_element_'+this.element_name_and_id+'"></span>  </div>');
					jQuery(this.get_last_element()).children('span').click(function(){
						local_this.remove_single_element(jQuery(this));
					});
					var next_element_focus=false
					jQuery(this.get_last_element()).children('input').keydown(function(){
							if(event.which == 13)
								next_element_focus=true;
							else
								next_element_focus=false;
					});
					jQuery(this.get_last_element()).children('input').change(function(){
							if(jQuery(jQuery('.emelent_'+local_this.element_name_and_id)).index(jQuery(this).parent())==jQuery('.emelent_'+local_this.element_name_and_id).length-1){
								jQuery('#'+local_this.main_element_for_inserting_element).append(local_this.creat_single_element())
								if(next_element_focus)
									jQuery('.emelent_'+local_this.element_name_and_id).eq(jQuery('.emelent_'+local_this.element_name_and_id).length-1).children('input').focus();
								next_element_focus=false;
							}
							local_this.insert_value_on_hidden_element();
								
					});

				},				
				// function for remove element
				remove_single_element:function(element){
					if(jQuery('.emelent_'+this.element_name_and_id).length>1)
						jQuery(element).parent().remove();
					this.insert_value_on_hidden_element();
				},
				
				// set input json encoded value of all inputs
				insert_value_on_hidden_element:function(){
					var input_value={}
					var z=0;
				
					jQuery('.emelent_'+this.element_name_and_id).each(function(index, element) {
                        input_value[z]=jQuery(this).children('input').val();
						z++;
                    });
					z--;
					if( input_value[z]=='')
						delete input_value[z];
					jQuery('#'+this.element_name_and_id).val(JSON.stringify(input_value));
				},
				get_last_element:function(){
					return jQuery('#'+this.main_element_for_inserting_element+' .emelent_'+this.element_name_and_id).eq(jQuery('#'+this.main_element_for_inserting_element+' .emelent_'+this.element_name_and_id).length-1);
				}
			}
			
		 ///////////////					///////////////
		 ///////////////	MANY INPUTS	END	///////////////
		 ///////////////					///////////////
		 
			
			 jQuery(document).ready(function() {
				var currentTime = new Date();
				var month = currentTime.getMonth();
				var day = currentTime.getDate();
				var year = currentTime.getFullYear();
				jQuery("#coming_soon_page_countdownstart_day").datepicker({
					inline: true,
					nextText: '&rarr;',
					prevText: '&larr;',
					showOtherMonths: true,
					dateFormat: 'dd/mm/yy',
					dayNamesMin: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
					maxDate: new Date(year,month,day)
				});
				
				
				many_inputs.main_element_for_inserting_element='no_blocked_ips';
				many_inputs.element_name_and_id='coming_soon_page_showed_ips';
				many_inputs.placeholder='Type Ip Here';
				many_inputs.value_jsone_encoded='<?php echo stripslashes($page_parametrs['coming_soon_page_showed_ips']) ?>';
				many_inputs.creates_elements();
				
				
				 //// ajax
				jQuery('#save_button').click(function(){
					if(tinymce.get( 'coming_soon_page_page_title')!=null)
						tinymce.get( 'coming_soon_page_page_title').save();
					if(tinymce.get( 'coming_soon_page_page_message')!=null)
						tinymce.get( 'coming_soon_page_page_message').save()
					jQuery('body').prepend('<div id="saving_soon"><span id="loading_text"><img src="<?php echo $this->plugin_url.'images/loading_big.gif' ?>" /></span></div>');
					jQuery('#saving_soon').height(jQuery(window).height());
					//jQuery('#saving_soon').click(function(){jQuery('#saving_soon').remove()})
					jQuery.ajax({
						type:'POST',
						url: "<?php echo admin_url( 'admin-ajax.php?action=coming_soon_page_save' ); ?>",
						data: {curent_page:'general',coming_soon_options_nonce:jQuery('#coming_soon_options_nonce').val()<?php foreach($page_parametrs as $key => $value){echo ','.$key.':jQuery("#'.$key.'").val()';} ?>},
					}).done(function(date) {
						alert(date)
					  jQuery('#saving_soon').remove();
					});
				});
			
		 
		         /// for upload 
					/* set uploader size in resizing*/
				jQuery('.upload-button').click(function () {
					window.parent.uploadID = jQuery(this).prev('input');
					/*grab the specific input*/
					formfield = jQuery('.upload').attr('name');
					tb_show('', 'media-upload.php?type=image&height=640&width=1000&TB_iframe=true');
					return false;
				});
				window.send_to_editor = function (html) {
					imgurl = jQuery('img', html).attr('src');
					window.parent.uploadID.val(imgurl);
					/*assign the value to the input*/
					tb_remove();
				};
				
			});
			function refresh_countdown(){
				var countdown={}
				countdown['days']=jQuery('#coming_soon_page_countdownday').val();
				countdown['hours']=jQuery('#coming_soon_page_countdownhour').val();
				countdown['start_day']=jQuery('#coming_soon_page_countdownstart_day').val();
				if(jQuery('#coming_soon_page_countdownstart_on').prop('checked'))
					countdown['countedown_on']=jQuery('#coming_soon_page_countdownstart_on').val();
				else
					countdown['countedown_on']='';
				jQuery('#coming_soon_page_countdown').val(JSON.stringify(countdown))
			}
			function generete_radio_input_hidden(element){
				jQuery(element).parent().find('.insert_value_this').val(jQuery(element).parent().find('input[type=radio]:checked').val())
				
			}
			var text_of_upgrate_version='If you want to use this feature upgrade to Coming soon Pro';
         </script>
         <?php
	
	
	}
	public function generete_design_options_page($page_parametrs){
		?>
		<form method="post" id="coming_soon_options_form" action="admin.php?page='<?php echo  str_replace( ' ', '-', $this->menu_name); ?>'">	
            <div class="option_group">
            <h4 class="option_title">Coming soon page background type</h4>	
                <label for="coming_soon_page_radio_backroundcolor">Background Color</label>
                <input type="radio" onchange="generete_radio_input(this)" name="coming_soon_page_radio_backroundradio" id="coming_soon_page_radio_backroundcolor" value="back_color" <?php checked($page_parametrs['coming_soon_page_radio_backroun'], "back_color"); ?>/>
               	
                <label for="coming_soon_page_radio_backroundimage">Background Image</label>
                <input type="radio" onchange="generete_radio_input(this)"  name="coming_soon_page_radio_backroundradio" id="coming_soon_page_radio_backroundimage" value="back_imge" <?php checked($page_parametrs['coming_soon_page_radio_backroun'], "back_imge"); ?> />
                
                <label for="coming_soon_page_radio_backroundslider">Background Slider <span style="color:rgba(216, 19, 19, 1);">Pro feature!</span></label>
                <input type="radio" onclick="alert(text_of_upgrate_version); return false"  name="coming_soon_page_radio_backroundradio" />
                <input type="hidden" class="insert_design_value_this" name="coming_soon_page_radio_backroun" id="coming_soon_page_radio_backroun" value="<?php echo $page_parametrs['coming_soon_page_radio_backroun'] ?>"  />
            </div>
            <div class="option_group back_color">
                <h4 class="option_title">Background Color</h4>
                <div id="upload_image">
                    <input type="text" class="upload" id="coming_soon_page_background_color" name="coming_soon_page_background_color" size="60" value="<?php echo $page_parametrs['coming_soon_page_background_color'] ?>"/>
                </div>
            </div>            
            <div class="option_group back_imge">
                <h4 class="option_title">Background Image</h4>
                    <input type="text" class="upload" id="coming_soon_page_background_img" name="coming_soon_page_background_img" size="60" value="<?php echo $page_parametrs['coming_soon_page_background_img'] ?>"/>
                    <input class="upload-button" type="button" value="Upload Image"/>
            </div>

            <div class="option_group back_slider">
          
                <h4 class="option_title">Background Slider</h4>
                 <?php $images= json_decode(stripslashes($page_parametrs['coming_soon_page_background_imgs']), true);  
                 if(count($images) >1){
				 foreach($images as $image){
				 
				 ?>
                <div class="slider_images_div">
                    <input type="text" class="upload_many_images" size="60" value="<?php echo $image ?>"/>
                    <input class="upload-button  button button-primary" type="button" value="Upload Image"/>
                    <input class="remove_upload_image" type="button" value="Remove"/>
                </div>
                <br />
                <?php }}else {?>
                <div class="slider_images_div">
                    <input type="text" class="upload_many_images" size="60" value=""/>
                    <input class="upload-button  button button-primary" type="button" value="Upload Image"/>
                    <input class="remove_upload_image" type="button" value="Remove"/>
                </div>
                <br />
                <?php }?>

                <input type="hidden" value="<?php echo stripslashes($page_parametrs['coming_soon_page_background_imgs']) ?>" name="coming_soon_page_background_imgs" id="coming_soon_page_background_imgs"/>
                <input class="add_upload_image_button button button-primary"  type="button" value="Add"/>
            </div>

             <div class="option_group">
                <h4 class="option_title">Title Font Size</h4>
                	<div id="upload_image">
                    	<input type="text" class="upload" id="coming_soon_page_page_title_font_size" name="coming_soon_page_page_title_font_size" size="3" value="<?php echo $page_parametrs['coming_soon_page_page_title_font_size'] ?>"/>Px
                	</div>
            </div>
            
            <div class="option_group">
                <h4 class="option_title">Title Color</h4>
                	<div id="upload_image">
                    	<input type="text" class="upload" id="coming_soon_page_page_title_color" name="coming_soon_page_page_title_color" size="60" value="<?php echo $page_parametrs['coming_soon_page_page_title_color'] ?>"/>
                	</div>
            </div>
            
            <div class="option_group">
                <h4 class="option_title">Content position <span style="color:rgba(216, 19, 19, 1);">Pro feature!</span></h4>

                <table class="bws_position_table">
                    <tbody>
                      <tr>
                        <td><input type="radio"  onclick="alert(text_of_upgrate_version); return false" onclick="alert(text_of_upgrate_version); return false"  name="page_content_position_radio"></td>
                        <td><input type="radio"  onclick="alert(text_of_upgrate_version); return false"  name="page_content_position_radio"></td>
                        <td><input type="radio"  onclick="alert(text_of_upgrate_version); return false"  name="page_content_position_radio"></td>
                      </tr>
                      <tr>
                        <td><input type="radio"  onclick="alert(text_of_upgrate_version); return false" name="page_content_position_radio"></td>
                        <td><input type="radio"  onclick="alert(text_of_upgrate_version); return false" checked="checked" name="page_content_position_radio"></td>
                        <td><input type="radio"  onclick="alert(text_of_upgrate_version); return false" name="page_content_position_radio"></td>
                      </tr>
                      <tr>
                        <td><input type="radio"  onclick="alert(text_of_upgrate_version); return false" name="page_content_position_radio"></td>
                        <td><input type="radio"  onclick="alert(text_of_upgrate_version); return false" name="page_content_position_radio"></td>
                        <td><input type="radio"  onclick="alert(text_of_upgrate_version); return false" name="page_content_position_radio"></td>
                      </tr>
                    </tbody>	
                </table>
                <input type="hidden" class="insert_value_this" name="page_content_position" id="page_content_position" value="<?php echo $page_parametrs['page_content_position'] ?>"  />
            </div>
            
            <div class="option_group">
                <h4 class="option_title">Coming soon Content background color</h4>
                	<div >
                    	<input type="text"  id="coming_soon_page_content_bg_color" name="coming_soon_page_content_bg_color" size="60" value="<?php echo $page_parametrs['coming_soon_page_content_bg_color'] ?>"/>
                	</div>
            </div>
            
            <div class="option_group">
                <h4 class="option_title">Coming page Content background transparency</h4>
                <input type="text" size="3" name="coming_soon_page_content_trasparensy" value="50" id="coming_soon_page_content_trasparensy" style="border:0; color:#f6931f; font-weight:bold; width:35px" >%
               <div style="width:240px" id="slider-coming_soon_page_content_trasparensy" class="ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all">
                   <span class="ui-slider-handle ui-state-default ui-corner-all" tabindex="0" style="left: 0%;"></span>
               </div>
            </div>
            
            <div class="option_group">
                <h4 class="option_title">Content border radius</h4>
                	<div>
                    	<input type="text"  id="page_content_boreder_radius" name="page_content_boreder_radius" size="3" value="<?php echo $page_parametrs['page_content_boreder_radius'] ?>"/>Px
                	</div>
            </div>
            
            
            
           <div class="option_group">
                <h4 class="option_title">Countdown button background color <span style="color:rgba(216, 19, 19, 1);">Pro feature!</span></h4>
                	<div class='disabled_for_pro'>
                    	<div class="wp-picker-container"><a tabindex="0" class="wp-color-result" title="Select Color" data-current="Current Color" style="background-color: rgb(61, 168, 204);"></a></div>
                	</div>
            </div>
            
            <div class="option_group">
                <h4 class="option_title">Countdown text color <span style="color:rgba(216, 19, 19, 1);">Pro feature!</span></h4>
                	<div class='disabled_for_pro'>
                    	<div class="wp-picker-container"><a tabindex="0" class="wp-color-result" title="Select Color" data-current="Current Color" style="background-color: rgb(0, 0, 0);"></a></div>
                	</div>
            </div>
            <div class="option_group">
                <h4 class="option_title">Countdown border radius <span style="color:rgba(216, 19, 19, 1);">Pro feature!</span></h4>
                	<div>
                    	<input type="text"  onclick="alert(text_of_upgrate_version); return false"   size="3" value="8"/>Px
                	</div>
            </div>
            
            <div class="option_group">
                <h4 class="option_title">Countdown font-size <span style="color:rgba(216, 19, 19, 1);">Pro feature!</span></h4>
                	<div>
                    	<input type="text"  onclick="alert(text_of_upgrate_version); return false"   size="3" value="30"/>Px
                	</div>
            </div>




            <div class="option_group">
                <h4 class="option_title">Mailing list button background color <span style="color:rgba(216, 19, 19, 1);">Pro feature!</span></h4>
                	<div class='disabled_for_pro'>
                    	<div class="wp-picker-container"><a tabindex="0" class="wp-color-result" title="Select Color" data-current="Current Color" style="background-color: rgb(61, 168, 204);"></a></div>
                	</div>
            </div>
            <div class="option_group">
                <h4 class="option_title">Mailing list button text color <span style="color:rgba(216, 19, 19, 1);">Pro feature!</span></h4>
                	<div class='disabled_for_pro'>
                    	<div class="wp-picker-container"><a tabindex="0" class="wp-color-result" title="Select Color" data-current="Current Color" style="background-color: rgb(0, 0, 0);"></a></div>
                	</div>
            </div>
            <div class="option_group">
                <h4 class="option_title">Mailing list input text color <span style="color:rgba(216, 19, 19, 1);">Pro feature!</span></h4>
                	<div class='disabled_for_pro'>
                    	<div class="wp-picker-container"><a tabindex="0" class="wp-color-result" title="Select Color" data-current="Current Color" style="background-color: rgb(0, 0, 0);"></a></div>
                	</div>
            </div>
            <div class="option_group">
                <h4 class="option_title">Mailing list button and input font-size <span style="color:rgba(216, 19, 19, 1);">Pro feature!</span></h4>
                	<div>
                    	<input type="text"  onclick="alert(text_of_upgrate_version); return false"   size="3" value="25"/>Px
                	</div>
            </div>
            <div class="option_group">
                <h4 class="option_title">Text color that will appear after sending Email. <span style="color:rgba(216, 19, 19, 1);">Pro feature!</span></h4>
                	<div class='disabled_for_pro'>
                    	<div class="wp-picker-container"><a tabindex="0" class="wp-color-result" title="Select Color" data-current="Current Color" style="background-color: rgb(255, 255, 255);"></a></div>
                	</div>
            </div>
            
            
            
            <div class="option_group">
                <h4 class="option_title">Facebook button background image</h4>
                <div id="upload_image">
                    <input type="text" class="upload" id="social_facbook_bacground_image" name="social_facbook_bacground_image" size="60" value="<?php echo $page_parametrs['social_facbook_bacground_image'] ?>"/>
                    <input class="upload-button" type="button" value="Upload Image"/>
                </div>
            </div>
            
            <div class="option_group">
                <h4 class="option_title">Twitter button background image</h4>
                <div id="upload_image">
                    <input type="text" class="upload" id="social_twiter_bacground_image" name="social_twiter_bacground_image" size="60" value="<?php echo $page_parametrs['social_twiter_bacground_image'] ?>"/>
                    <input class="upload-button" type="button" value="Upload Image"/>
                </div>
            </div>
            
            <div class="option_group">
                <h4 class="option_title">Google+ button background image</h4>
                <div id="upload_image">
                    <input type="text" class="upload" id="social_google_bacground_image" name="social_google_bacground_image" size="60" value="<?php echo $page_parametrs['social_google_bacground_image'] ?>"/>
                    <input class="upload-button" type="button" value="Upload Image"/>
                </div>
            </div>
            
            <div class="option_group">
                <h4 class="option_title">YouTube button background image</h4>
                <div id="upload_image">
                    <input type="text" class="upload" id="social_youtobe_bacground_image" name="social_youtobe_bacground_image" size="60" value="<?php echo $page_parametrs['social_youtobe_bacground_image'] ?>"/>
                    <input class="upload-button" type="button" value="Upload Image"/>
                </div>
            </div>
            
            <div class="option_group">
                <h4 class="option_title">Instagram button background image</h4>
                <div id="upload_image">
                    <input type="text" class="upload" id="social_instagram_bacground_image" name="social_instagram_bacground_image" size="60" value="<?php echo $page_parametrs['social_instagram_bacground_image'] ?>"/>
                    <input class="upload-button" type="button" value="Upload Image"/>
                </div>
            </div>            

            <button type="button" id="save_button_design" class="save_button button button-primary">Save Design Settings</button>
            <?php wp_nonce_field('coming_soon_options_nonce','coming_soon_options_nonce'); ?>
        </form>
		<script>
		jQuery(document).ready(function(e) {
			
			
			jQuery('.add_upload_image_button').click(function(){
					jQuery('.slider_images_div').eq(jQuery('.slider_images_div').length-1).after(jQuery('<div class="slider_images_div"><br><input type="text" class="upload_many_images" size="60" value=""/><input class="upload-button  button button-primary" type="button" value="Upload Image"/><input class="remove_upload_image" type="button" value="Remove"/></div>'))
			initial_last_element_functions(this);
			})
			jQuery('.remove_upload_image').click(function(){
				if(jQuery('.remove_upload_image').length>1)
					jQuery(this).parent().remove()	
			})
            jQuery("#coming_soon_page_background_color").wpColorPicker();
			jQuery("#coming_soon_page_content_bg_color").wpColorPicker();
			jQuery("#coming_soon_page_page_title_color").wpColorPicker();
			jQuery("#countdown_button_color").wpColorPicker();
			jQuery("#countdown_font_color").wpColorPicker();
			jQuery("#mail_buttton_color").wpColorPicker();
			jQuery("#mail_buttton_font_color").wpColorPicker();
			jQuery("#sendmail_input_font_clolor").wpColorPicker();
			jQuery("#sendmail_after_text_color").wpColorPicker();
			jQuery('.disabled_for_pro div').click(function(){
				alert(text_of_upgrate_version); 
				return false;
			})
			 generete_radio_input(jQuery('#coming_soon_page_radio_backroundcolor'));
			 jQuery('#save_button_design').click(function(){
					jQuery('body').prepend('<div id="saving_soon"><span id="loading_text"><img src="<?php echo $this->plugin_url.'images/loading_big.gif' ?>" /></span></div>');
					jQuery('#saving_soon').height(jQuery(window).height());
					//jQuery('#saving_soon').click(function(){jQuery('#saving_soon').remove()});
					generete_slider_images();
					generete_radio_input_hidden(jQuery('#page_content_position'));
					jQuery.ajax({
						type:'POST',
						url: "<?php echo admin_url( 'admin-ajax.php?action=coming_soon_page_save' ); ?>",
						data: {curent_page:'design',coming_soon_options_nonce:jQuery('#coming_soon_options_nonce').val()<?php foreach($page_parametrs as $key => $value){echo ','.$key.':jQuery("#'.$key.'").val()';} ?>},
					}).done(function(date) {
						alert(date)
					  jQuery('#saving_soon').remove();
					});
				});

			jQuery( "#slider-coming_soon_page_content_trasparensy" ).slider({
				orientation: "horizontal",
				range: "min",
				value: <?php echo $page_parametrs['coming_soon_page_content_trasparensy'] ?>,
				min: 0,
				max: 100,
				slide: function( event, ui ) {
					jQuery( "#coming_soon_page_content_trasparensy" ).val( ui.value );
				}
			});
			jQuery( "#coming_soon_page_content_trasparensy" ).val(jQuery( "#slider-coming_soon_page_content_trasparensy" ).slider( "value" ) );			 
        });
        function initial_last_element_functions(element_of_add){
			jQuery('.remove_upload_image').eq(jQuery('.remove_upload_image').length-1).click(function(){
				if(jQuery('.remove_upload_image').length>1)
					jQuery(this).parent().remove()	
			})
			jQuery(element_of_add).parent().find('.upload-button').eq(jQuery(element_of_add).parent().find('.upload-button').length-1).click(function () {
					window.parent.uploadID = jQuery(this).prev('input');
					/*grab the specific input*/
					formfield = jQuery('.upload').attr('name');
					tb_show('', 'media-upload.php?type=image&height=640&width=1000&TB_iframe=true');
					return false;
				});
		}
        function generete_radio_input(element){
			
			jQuery('.insert_design_value_this').val(jQuery(element).parent().find('input[type=radio]:checked').val())
			jQuery('.back_color').hide();
			jQuery('.back_imge').hide();
			jQuery('.back_slider').hide();
			jQuery('.'+jQuery(element).parent().find('input[type=radio]:checked').val()).show();
		}
		function generete_slider_images(){
				var slider_images_url={};
				var i=0;
				jQuery('.upload_many_images').each(function() {
				 	slider_images_url[i]=jQuery( this ).val();
					i++;
				});
				jQuery('#coming_soon_page_background_imgs').val(JSON.stringify(slider_images_url))
		}
		
		
        </script>
		<?php
	}
	
	public function generete_mailing_list_options_page($page_parametrs){
		?>
			<style>
            .mail_user{
				margin-right:3px;
				margin-top:15px;
				background-color: #f5f5f5;
				border: 1px solid #d9d9d9;
				cursor: default;
				display: block;
				height: 20px;
				white-space: nowrap;
				-webkit-border-radius: 3px;
				border-radius: 3px;
				font-size: 13px;
				display: inline-block;
				padding: 3px;
				vertical-align: top;
				font-family: arial,sans-serif;
			}
            
            </style>
        <?php 
		$mailing_lists=json_decode(stripslashes($page_parametrs['users_mailer']),true);
		if($mailing_lists==null)
		$mailing_lists=array();
		foreach($mailing_lists as $email){
			echo "<span class='mail_user'>".$email."</span>";
		}
		?><br /><br />
        	<form method="post" id="coming_soon_options_form_send_mail" action="admin.php?page='<?php echo  str_replace( ' ', '-', $this->menu_name); ?>'">
            	<input type="text" value="" placeholder="Message Title" style="width:400px;" id="massage_title" /><br />
                <textarea id="massage_description" placeholder="Message" style="width:400px; height:300px"></textarea><br /><br /><br />
                <button type="button" id="send_mailing" class="save_button button button-primary">Send Mail</button>
        	</form>
		
		<script>
			jQuery(document).ready(function(e) {
				jQuery('#send_mailing').click(function(){
					jQuery('body').prepend('<div id="saving_soon"><span id="loading_text"><img src="<?php echo $this->plugin_url.'images/loading_big.gif' ?>" /></span></div>');
					jQuery('#saving_soon').height(jQuery(window).height());
					jQuery.ajax({
						type:'POST',
						url: "<?php echo admin_url( 'admin-ajax.php?action=coming_soon_send_mail' ); ?>",
						data: {massage_description:jQuery('#massage_description').val(),massage_title:jQuery('#massage_title').val()},
					}).done(function(date) {
						alert(date)
					  jQuery('#saving_soon').remove();
					});  
				});
			});       
        </script>
		
		<?php 
	}
	public function generete_mailing_live_previev_page(){
		
		?>
        <iframe src="<?php echo site_url(); ?>/?special_variable_for_live_previev=sdfg564sfdh645fds4ghs515vsr5g48strh846sd6g41513btsd" width="100%" height="900px"></iframe>
        <?php
	}
	
}