jQuery(document).ready(function(){
			
	jQuery('body').on('click', '.salessurvey_tab.tabbed', function(){		

		jQuery('.salessurvey_tab').removeClass('salessurvey_tabactive');
		jQuery(this).addClass('salessurvey_tabactive');	
		
		if(jQuery(this).attr('data-tab') == 'auctions'){
			jQuery('div [id*=salessurvey_auctionsframe_]').show();
			jQuery('div [id*=salessurvey_feedbackframe_]').hide();	
		} else {
			jQuery('div [id*=salessurvey_auctionsframe_]').hide();
			jQuery('div [id*=salessurvey_feedbackframe_]').show();	
		}

	});
	
});