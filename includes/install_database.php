<?php 

/// class for install databese

class install_database{
	
	public $installed_options; // all standart_options
	private $plugin_url;

	function __construct(){
		
		if(isset($params['plugin_url']))
			$this->plugin_url=$params['plugin_url'];
		else
			$this->plugin_url=trailingslashit(dirname(plugins_url('',__FILE__)));

		
		$this->installed_options=array(
			"general"=>array(
				"coming_soon_page_mode"					=> "off",
				"coming_soon_page_page_logo" 			   => $this->plugin_url.'images/template1/logo.png',
				"coming_soon_page_page_title"			  => "Coming Soon Page",
				"coming_soon_page_page_message" 			=> "<span style=\"font-size: 30px;\">We will be back soon. View some useful information on our coming soon page, share it with friends</span><br><br>",
				"coming_soon_page_meta_keywords" 		   => "",
				"coming_soon_page_meta_description"		=> "",
				"coming_soon_page_showed_ips"			  => "",
				"coming_soon_page_facebook"			   	=> "",
				"coming_soon_page_twitter" 		  		 => "",
				"coming_soon_page_google_plus" 			 => "",
				"coming_soon_page_youtube" 			 	 => "",
				"coming_soon_page_instagram"			   => "",
			),
			"design"=>array(
			
				"coming_soon_page_radio_backroun" 		  => "back_imge",
				"coming_soon_page_background_img" 		  => $this->plugin_url.'images/template1/background.jpg',
				"coming_soon_page_background_color" 		=> "#cacaca",

			),
			"mailing_list"=>array(
				"users_mailer" 		 					 => "",
				"sended_user_massage" 		  			  => "Hello Our Site Is Opened",
			)
		);
		
		
	}
	public function install_databese(){
		foreach( $this->installed_options as $key => $option ){
			if( get_option($key,FALSE) === FALSE ){
				add_option($key,$option);
			}
		}		
	}
}