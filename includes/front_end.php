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
	public function create_fornt_end(){
		if($this->params['coming_soon_page_mode']=='on'){
			if(!is_feed())
			{
				//if user not login page is redirect on coming soon template page
				$ips=json_decode(stripslashes($this->params['coming_soon_page_showed_ips']), true);
				if(!$ips)
					$ips=array();
				
				
				if ( (!is_user_logged_in()) || (isset($_GET['special_variable_for_live_previev']) && $_GET['special_variable_for_live_previev']=='sdfg564sfdh645fds4ghs515vsr5g48strh846sd6g41513btsd') )
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
	
	// generet front end dinamic style
	
	private function generete_front_styles(){
		?>
	<style>
    #title_style h1{
        font-size:<?php echo $this->params['coming_soon_page_page_title_font_size'];  ?>px;
        color:<?php echo $this->params['coming_soon_page_page_title_color']; ?>;
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
            } ?>;
    }
    <?php
    $aligment_position='text-align:center;
                        vertical-align:middle;';
    ?>
    .aligment{
        <?php echo $aligment_position; ?>
    }
    </style>
	<?php
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
    <?php   wp_print_scripts('jquery'); 
	 		wp_print_scripts('coming-soon-script'); 
    		wp_print_styles('coming-soon-style');
    		$this->generete_front_styles();
    ?>
    
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
                    	<div style="clear:both"></div>
                    
                        <div class="soc_icon_coneiner">
							<?php if($this->params['coming_soon_page_facebook']){ ?>
                                <span class="soc_icon">
                                    <a href="<?php echo $this->params['coming_soon_page_facebook']; ?>"><img src="<?php echo $this->plugin_url.'images/template1/facebook.png' ?>" /></a>
                                </span>
                            <?php } ?>
                            <?php if($this->params['coming_soon_page_twitter']){ ?>
                                <span class="soc_icon">
                                    <a href="<?php echo $this->params['coming_soon_page_twitter']; ?>"><img src="<?php echo $this->plugin_url.'images/template1/twiter.png'; ?>" /></a>
                                </span>
                            <?php } ?>
                            <?php if($this->params['coming_soon_page_google_plus']){ ?>
                                <span class="soc_icon">
                                    <a href="<?php echo $this->params['coming_soon_page_google_plus']; ?>"><img src="<?php echo $this->plugin_url.'images/template1/gmail.png'; ?>" /></a>
                                </span>
                            <?php } ?>
                            <?php if($this->params['coming_soon_page_youtube']){ ?>
                                <span class="soc_icon">
                                    <a href="<?php echo $this->params['coming_soon_page_youtube']; ?>"><img src="<?php echo $this->plugin_url.'images/template1/youtobe.png'; ?>" /></a>
                                </span>
                            <?php } ?>
                            <?php if($this->params['coming_soon_page_instagram']){ ?>
                                <span class="soc_icon">
                                    <a href="<?php echo $this->params['coming_soon_page_instagram']; ?>"><img src="<?php echo $this->plugin_url.'images/template1/instagram.png'; ?>" /></a>
                                </span>
                            <?php } ?>
                        </div>
                    </center>
                </div>
            </span>
        </div>
        </div> 
    </body>
</html>
        <?php	
		
	}	
	
	
}




?>