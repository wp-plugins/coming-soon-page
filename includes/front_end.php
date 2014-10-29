<?php 

class coming_soon_front_end{
	private $menu_name;
	private $plugin_url;
	private $databese_parametrs;
	private $params;

	function __construct($params){
		
		$this->menu_name=$params['menu_name'];
		$this->databese_parametrs=$params['databese_parametrs'];
		if(isset($params['plugin_url']))
			$this->plugin_url=$params['plugin_url'];
		else
			$this->plugin_url=trailingslashit(dirname(plugins_url('',__FILE__)));
		
		add_action( 'wp_ajax_coming_soon_page_save_user_mail', array($this,'save_mailing_list') );
		add_action( 'wp_ajax_nopriv_coming_soon_page_save_user_mail', array($this,'save_mailing_list') );
		$this->params=$this->generete_params();
	}
	private function generete_params(){
		
			$param_name='general';
			foreach($this->databese_parametrs[$param_name] as $key => $value){
				$front_end_parametrs[$key]=get_option($key,$value);
			}
			$param_name='design';
			foreach($this->databese_parametrs[$param_name] as $key => $value){
				$front_end_parametrs[$key]=get_option($key,$value);
			}
			$param_name='mailing_list';
			foreach($this->databese_parametrs[$param_name] as $key => $value){
				$front_end_parametrs[$key]=get_option($key,$value);
			}
			
			return $front_end_parametrs;
			
		
		
	}
	public function save_mailing_list(){
		$params=$this->generete_params();
		
		if(isset($_POST['user_email']) && $_POST['user_email']!='' && $this->params['enable_mailing_list']=='on'){
			$email=$_POST['user_email'];
			if(filter_var($email, FILTER_VALIDATE_EMAIL)){
				$mailing_lists=json_decode(stripslashes($this->params['users_mailer']), true); 
				if(!$mailing_lists)
					$mailing_lists=array();
				if($mailing_lists==NULL)
					$mailing_lists=array();
				if(!in_array($email,$mailing_lists)) {
					array_push($mailing_lists, $email);
					update_option('users_mailer',json_encode($mailing_lists));
					echo "<div class='information_user'>You Have Been Successfully Subscribed!</div>";
				}
				else{
					echo "<div class='information_user'>You're Already Subscribed!</div>";
				}
			}
			else{
				echo "<div class='information_user'>Email Doesn't Exist</div>";
			}
		}
		else{
			echo "<div class='information_user'>Please Type Your Email</div>";
		}
		die();
	}
	private function get_real_ip()    {
		if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
		{
		  $ip=$_SERVER['HTTP_CLIENT_IP'];
		}
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
		{
		  $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else
		{
		  $ip=$_SERVER['REMOTE_ADDR'];
		}
		return $ip;		
	}
	private function getCurrentURL(){
		$currentURL = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
		$currentURL .= $_SERVER["SERVER_NAME"];
	 
		if($_SERVER["SERVER_PORT"] != "80" && $_SERVER["SERVER_PORT"] != "443")
		{
			$currentURL .= ":".$_SERVER["SERVER_PORT"];
		} 
	 
			$currentURL .= $_SERVER["REQUEST_URI"];
		return $currentURL;
	}
	private function if_time_is_pass(){
	
		$countdown= json_decode(stripslashes($this->params['coming_soon_page_countdown']), true);
		if($countdown['countedown_on']=='on'){
			if((isset($countdown['hours']) && isset($countdown['days']) && isset($countdown['start_day'])) && (($countdown['hours'] || $countdown['days']) && $countdown['start_day'])){ 
				$start_time=explode('/',$countdown['start_day']);
				$time_diferent=mktime(0,0,0, $start_time[1],$start_time[0],$start_time[2])+$countdown['days']*3600*24+$countdown['hours']*3600-mktime(date("H"), date('i'), date("s"), date("n"), date("j"), date("Y"));
				$day_left=(int)($time_diferent/(3600*24));
				$hourse_left=(int)(($time_diferent-$day_left*24*3600)/(3600));
				$minuts_left=(int)(($time_diferent-$day_left*24*3600-$hourse_left*3600)/(60));
				$seconds_left=(int)(($time_diferent-$day_left*24*3600-$hourse_left*3600 - $minuts_left*60));
			}
			else{
				return false;
			}
			if(($day_left<=0 && $hourse_left<=0 && $minuts_left<=0 && $seconds_left<=0)){
				return false;
			}
		}
		return true;
	
	}
	public function create_fornt_end(){
		if($this->params['coming_soon_page_mode']=='on'){
			if(!is_feed())
			{
				//if user not login page is redirect on coming soon template page
				$ips=json_decode(stripslashes($this->params['coming_soon_page_showed_ips']), true);
				if(!$ips)
					$ips=array();
				$in_range= in_array($this->get_real_ip(), $ips);
				
				if ( (!is_user_logged_in() && !$in_range && $this->if_time_is_pass()) || (isset($_GET['special_variable_for_live_previev']) && $_GET['special_variable_for_live_previev']=='sdfg564sfdh645fds4ghs515vsr5g48strh846sd6g41513btsd') )
				{
				
					//get path of our coming soon display page and redirecting
					$this->generete_front_end_html();
					exit();
				}
			}
		}
		else
		if((isset($_GET['special_variable_for_live_previev']) && $_GET['special_variable_for_live_previev']=='sdfg564sfdh645fds4ghs515vsr5g48strh846sd6g41513btsd')){
			$this->generete_front_end_html();
					exit();
		}
		
	}

	public function generete_front_end_html(){
		?>
		<!DOCTYPE html>
        <html lang="en">
          <head>
                <meta charset="utf-8">
                <title><?php  bloginfo( 'name' );  $site_description = get_bloginfo( 'description' );  ?>  </title>
                <meta name="viewport" content="width=device-width" />
				<meta name="viewport" content="initial-scale=1.0" />
                <meta name="description" content="<?php echo $this->params['coming_soon_page_meta_description']; ?>">
                <meta name="keywords" content="<?php echo $this->params['coming_soon_page_meta_keywords']; ?>">  
                
                <?php wp_print_scripts('jquery'); ?>
				
                <style>
				#title_style h1{
					font-size:<?php echo $this->params['coming_soon_page_page_title_font_size'];  ?>px;
					color:<?php echo $this->params['coming_soon_page_page_title_color']; ?>;
					font-size: 55px;
					padding: 0px;
					margin: 5px 0px 5px 0px;
				}
				#descrip{
					margin: 5px 0px 5px 0px;
				}
				.soc_icon_coneiner{
					display:inline-block;
					margin-top:2%;
					width: 100%;
				}
				.soc_icon_coneiner .soc_icon{
					width: 7%;
					display: inline-block;
					margin-left: 3%;
					margin-right: 3%;
				}
				
				.soc_icon_coneiner .soc_icon img{
					width:100%;				
					max-height:82px;
				}
				.countdown > div{
					margin-top:2%;
					float:left;
					color:#fff;
					font-size:48px;
					font-weight:bold;
				}
				.element_conteiner{
					width:150px;
					display: inline-block;					
				}
				.time_left{
					border:none;
					border-radius:8px;
					background-color:#3DA8CC;
					font-size:30px;
					color:#000000;
					padding-bottom: 4%;
					padding-top: 5%;
					padding-left: 25%;
					padding-right: 25%;
					font-weight:bold;
					font-family: monospace;
					
				}
				.time_description{
					margin-top:6px;
					margin-bottom:6px;
					font-size:25px;
					color:#000000;
					font-weight:bold;
					font-family: monospace;
				}
				.information{
					background: rgba(<?php echo hexdec(substr($this->params['coming_soon_page_content_bg_color'],1, 2)); ?>,<?php echo hexdec(substr($this->params['coming_soon_page_content_bg_color'],3, 2)); ?>,<?php echo hexdec(substr($this->params['coming_soon_page_content_bg_color'],5, 2)); ?>,<?php echo 1-($this->params['coming_soon_page_content_trasparensy']/100)  ?>);
					border-radius:<?php echo $this->params['page_content_boreder_radius'] ?>px;
					width: 51%;
					/*height:71.5%;*/
					display:inline-block;
					z-index:999999;
					max-width:750px;
					width:100%;					
				}
				
				.information #logo img{
					max-height:210px;					
				}
                body{
				
					<?php switch($this->params['coming_soon_page_radio_backroun']){
						
							case 'back_color' :
								echo 'background-color:'.$this->params['coming_soon_page_background_color'];
							break;
							case 'back_imge' :
								echo 'background:url("'.$this->params['coming_soon_page_background_img'].'") no-repeat center center fixed;';
							break;
							case 'back_slider' :
							break;
						} ?>

						  -webkit-background-size: cover;
						  -moz-background-size: cover;
						  -o-background-size: cover;
						  background-size: cover;

					
				}
				<?php 			
-				
				$aligment_position='text-align:center;
									vertical-align:middle;';
									?>
				#main_inform_div{
					display:table;
					padding:10px;
				}
				.aligment{
					display:table-cell;
					<?php echo $aligment_position; ?>
					width: 10%;
				}
				.user_mail_options{
					
				}
				#user_email{
					font-size:25px;
					border-radius:8px;
					color:#000000;
					width: 90%;
					max-width: 350px;
					padding-left:8px;
					padding-top: 6px;
					padding-bottom: 3px;
					font-family: monospace;
				}
				.information_user{
					margin-top:10px;
					color:#FFFFFF;
					font-size:25px;
					font-weight:bold;
				}
				#Send_mail_button{
				cursor:pointer;
					margin-top:8px;
					border:none;
					border-radius:8px;
					background-color:#3DA8CC;
					font-size:25px;
					color:#000000;
					padding-bottom: 5px;
					padding-top: 7px;
					padding-left: 12px;
					padding-right: 12px;
					font-weight:bold;
					font-family: monospace;
				}
			
				#slider{
					left: 0px; top: 0px; overflow: hidden; margin: 0px; padding: 0px; height: 680px; width: 1903px; z-index: -999999; position: fixed;
				}
                </style>
          </head>
           <body>
		   
            <div id="main_inform_div">
            <span class="aligment">
              <div class="information">
                        <center>
                            <div id="logo" >
                                <img src="<?php echo stripslashes($this->params['coming_soon_page_page_logo']); ?>" />
                            </div>
                            <?php if($this->params['coming_soon_page_page_title']){ ?>
                            <div id="title_style" >
                                <h1><?php echo stripslashes($this->params['coming_soon_page_page_title']) ?></h1>
                            </div>
                            <?php } ?>
                            <div id="descrip" >
                                <?php echo stripslashes($this->params['coming_soon_page_page_message']) ?>
                            </div>
                             <?php $countdown= json_decode(stripslashes($this->params['coming_soon_page_countdown']), true);
							if((isset($countdown['hours']) && isset($countdown['days']) && isset($countdown['start_day'])) && (($countdown['hours'] || $countdown['days']) && $countdown['start_day'])){ 
							  $start_time=explode('/',$countdown['start_day']);
							  $time_diferent=mktime(0,0,0, $start_time[1],$start_time[0],$start_time[2])+$countdown['days']*3600*24+$countdown['hours']*3600-mktime(date("H"), date('i'), date("s"), date("n"), date("j"), date("Y"));
							  $day_left=(int)($time_diferent/(3600*24));
							  $hourse_left=(int)(($time_diferent-$day_left*24*3600)/(3600));
							  $minuts_left=(int)(($time_diferent-$day_left*24*3600-$hourse_left*3600)/(60));
							  $seconds_left=(int)(($time_diferent-$day_left*24*3600-$hourse_left*3600 - $minuts_left*60));
							  
							  if(!($day_left<=0 && $hourse_left<=0 && $minuts_left<=0 && $seconds_left<=0)){
							    		?>
							 
                                <div class="countdown">
                                	<span class="element_conteiner"><button disabled id="days" class="time_left"><?php echo $day_left; ?></button><div class="time_description">Days</div></span>
                                	<span class="element_conteiner"><button disabled id="hourse" class="time_left"><?php echo $hourse_left; ?></button><div class="time_description">Hours</div></span>
                                	<span class="element_conteiner"><button disabled id="minutes" class="time_left"><?php echo $minuts_left; ?></button><div class="time_description">Minutes</div></span>
                                	<span class="element_conteiner"><button disabled id="secondes" class="time_left"><?php echo $seconds_left; ?></button><div class="time_description">Seconds</div></span>
                                </div>
								
								<?php } } ?>
                                <div style="clear:both"></div>
								
                                <?php if($this->params['enable_mailing_list']=='on') { ?>
                                <div class="user_mail">
                                	
                                        <div class="user_mail_options">
                                            <input type="text" placeholder="<?php echo $this->params['mailing_list_value_of_emptyt'] ?>" name="email" id="user_email">
                                            <button type="button" id="Send_mail_button" class="Send_mail_button button button-primary"><?php echo $this->params['mailing_list_button_value'] ?></button>
                                            <div id="user_loading_and_saving"></div>
                                       </div> 
                                 
                                	
                                </div><?php } ?>
							 <div style="clear:both"></div>
							 
                            <div class="soc_icon_coneiner">
								<?php if($this->params['coming_soon_page_facebook']){ ?>
                                    <span class="soc_icon">
                                    	<a href="<?php $this->params['coming_soon_page_facebook']; ?>"><img src="<?php echo $this->params['social_facbook_bacground_image']; ?>" /></a>
                                    </span>
                                <?php } ?>
								<?php if($this->params['coming_soon_page_twitter']){ ?>
                                    <span class="soc_icon">
                                    	<a href="<?php $this->params['coming_soon_page_twitter']; ?>"><img src="<?php echo $this->params['social_twiter_bacground_image']; ?>" /></a>
                                    </span>
                                <?php } ?>
								<?php if($this->params['coming_soon_page_google_plus']){ ?>
                                    <span class="soc_icon">
                                    	<a href="<?php $this->params['coming_soon_page_google_plus']; ?>"><img src="<?php echo $this->params['social_google_bacground_image']; ?>" /></a>
                                    </span>
                                <?php } ?>
								<?php if($this->params['coming_soon_page_youtube']){ ?>
                                    <span class="soc_icon">
                                    	<a href="<?php $this->params['coming_soon_page_youtube']; ?>"><img src="<?php echo $this->params['social_youtobe_bacground_image']; ?>" /></a>
                                    </span>
                                <?php } ?>
								<?php if($this->params['coming_soon_page_instagram']){ ?>
                                    <span class="soc_icon">
                                    	<a href="<?php $this->params['coming_soon_page_instagram']; ?>"><img src="<?php echo $this->params['social_instagram_bacground_image']; ?>" /></a>
                                    </span>
                                <?php } ?>
                                
                                
                                
                                
                            </div>
                        </center>
                </div>
             </span>
               </div>
					</div> 

             <script type="text/javascript"> 
			 
			 function backgroun_custom_sizes(){
				/*with_of_window=jQuery(window).width()
				heigth_of_window=jQuery(window).height()
				if((with_of_window*1920/1080)<heigth_of_window){
					jQuery('body').css('background-size', with_of_window+'px'+' auto')
				}else{
					jQuery('body').css('background-size','auto '+ heigth_of_window+'px')
				}	*/
			 }
			 
			 jQuery(window).resize(function(){
				 jQuery('#main_inform_div').css('height',jQuery(window).height()-28);
				 jQuery('#main_inform_div').width(jQuery(window).width()-28);
				 /*	jQuery('body *').each(function(index, element) {
					initial_width=1920;
					curent_width=jQuery(window).width();
					kaifcent=curent_width/initial_width;
					jQuery(this).css("font-size", "")
					if(parseInt(jQuery(this).css('font-size'))){
						jQuery(this).css('font-size',parseInt(jQuery(this).css('font-size'))*kaifcent+'px')
					}
				});		*/		
				backgroun_custom_sizes();

			
			});
			
			
			jQuery(document).ready(function(e) {
			backgroun_custom_sizes();
				if(jQuery('#days').length>=1){
					setInterval(timer_coming_soon,1000)
				}
				/*jQuery('body *').each(function(index, element) {
					initial_width=1920;
					curent_width=jQuery(window).width();
					kaifcent=curent_width/initial_width;
					jQuery(this).css("font-size", "")
					if(parseInt(jQuery(this).css('font-size'))){
						jQuery(this).css('font-size',parseInt(jQuery(this).css('font-size'))*kaifcent+'px')
					}
				});*/
			  jQuery('#main_inform_div').css('height',jQuery(window).height()-28);
			  jQuery('#main_inform_div').width(jQuery(window).width()-28);
			  main_inform_div
			  jQuery('#Send_mail_button').click(function(){
				   jQuery('#user_loading_and_saving').html('<img width="35px" src="<?php echo $this->plugin_url.'images/loading.gif' ?>" />')
					jQuery.ajax({
						type:'POST',
						url: "<?php echo admin_url( 'admin-ajax.php?action=coming_soon_page_save_user_mail' ); ?>",
						data: {user_email:jQuery('#user_email').val()}
					}).done(function(date) {
					  jQuery('#user_loading_and_saving').html(date);
					  jQuery('#saving_soon').remove();
					});
			  })
			});
			function timer_coming_soon(){
				var days_left=parseInt(jQuery('#days').text());
				var hours_left=parseInt(jQuery('#hourse').text());;
				var minutes_left=parseInt(jQuery('#minutes').text());;
				var secondes_left=parseInt(jQuery('#secondes').text());;
				var all_time=days_left*24*3600+hours_left*3600+minutes_left*60+secondes_left;
				all_time--;
				days_left=parseInt(all_time/(3600*24));
				hours_left=parseInt((all_time-days_left*3600*24)/(3600));
				minutes_left=parseInt((all_time-days_left*3600*24-hours_left*3600)/(60));
				secondes_left=parseInt((all_time-days_left*3600*24-hours_left*3600-minutes_left*60));
				if((""+days_left+"").length>1)
					jQuery('#days').html(days_left);
				else
					jQuery('#days').html('0'+days_left);
				if((""+hours_left+"").length>1)
					jQuery('#hourse').html(hours_left);
				else
					jQuery('#hourse').html('0'+hours_left);
				if((""+minutes_left+"").length>1)
					jQuery('#minutes').html(minutes_left);
				else
					jQuery('#minutes').html('0'+minutes_left);
				if((""+secondes_left+"").length>1)
					jQuery('#secondes').html(secondes_left);
				else
					jQuery('#secondes').html('0'+secondes_left);
				if(days_left<=0 && hours_left<=0 && minutes_left<=0 && secondes_left<=0){
					window.location="http://democomingsoon.wpdevart.com"
				}
			}
			


			 </script> 		
            </body>
        </html>
        <?php	
		
	}	
	
	
}




?>