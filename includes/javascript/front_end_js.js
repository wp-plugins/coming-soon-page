jQuery(window).resize(function(){
	jQuery('#main_inform_div').css('height',jQuery(window).height()-28);
	jQuery('body').css('height',jQuery(window).height()-28);
	jQuery('#main_inform_div').width(jQuery(window).width()-28);	
});
jQuery(document).ready(function(e) {
	jQuery('#main_inform_div').css('height',jQuery(window).height()-28);
	jQuery('body').css('height',jQuery(window).height()-28);
	jQuery('#main_inform_div').width(jQuery(window).width()-28);	

	if(jQuery('#days').length>=1){
		setInterval(timer_coming_soon,1000)
	}
	
	jQuery('#main_inform_div').css('height',jQuery(window).height()-28);
	jQuery('#main_inform_div').width(jQuery(window).width()-28);
});