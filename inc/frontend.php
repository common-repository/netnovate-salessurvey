<?php

function salessurvey_shortcode($attributes){
	
	global $post;
	
	$category = $attributes['category'];
		
	$postMeta = get_post_meta($post->ID);
	
	if($category == 'widget'){
		
		if(empty($postMeta['ss_ebay_sellerId'][0])){
			return '';
		}
		
		$apiUri = "https://www.salessurvey.de/##SCRIPT##.js?sellerId=##SELLERID##&globalId=##GLOBALID##&locale=##LOCALE##&theme=##THEME##&sortOrder=##SORTORDER##&tabs=##TABS##&tileSize=##TILESIZE##&bgColor=##BGCOLOR##&responsive=1&height=##HEIGHT##&items=##ITEMS##&fields%5Bimage%5D=1&fields%5Bbids%5D=1&fields%5Bprice%5D=1&fields%5BremainingTime%5D=1&fields%5BfeedbackIcon%5D=1&fields%5BfeedbackSeller%5D=1&fields%5BfeedbackTime%5D=1&responseType=json&source=wp-frontend";
	
		$script = $postMeta['ss_ebay_template'][0] . '-widget';
		$apiUri = str_replace('##SCRIPT##', $script, $apiUri);
				
		$sellerId = $postMeta['ss_ebay_sellerId'][0];
		$apiUri = str_replace('##SELLERID##', urlencode($sellerId), $apiUri);

		$apiUri = str_replace('##LOCALE##', get_locale(), $apiUri);
				
		$globalId = $postMeta['ss_ebay_globalId'][0];
		$apiUri = str_replace('##GLOBALID##', urlencode($globalId), $apiUri);
		
		$theme = $postMeta['ss_ebay_frame'][0];
		$apiUri = str_replace('##THEME##', $theme, $apiUri);
		
		$sortOrder = $postMeta['ss_ebay_sort_order'][0];
		$apiUri = str_replace('##SORTORDER##', $sortOrder, $apiUri);
		
		$tabs = $postMeta['ss_ebay_shop_theme'][0];
		$apiUri = str_replace('##TABS##', $tabs, $apiUri);
		
		$tileSize = $postMeta['ss_ebay_tile_size'][0];
		$apiUri = str_replace('##TILESIZE##', $tileSize, $apiUri);
		
		$bgColor = $postMeta['ss_ebay_frame_color'][0];
		$apiUri = str_replace('##BGCOLOR##', urlencode($bgColor), $apiUri);
		
		$height = $postMeta['ss_ebay_shop_height'][0];
		$apiUri = str_replace('##HEIGHT##', intval($height), $apiUri);
		
		$items = $postMeta['ss_ebay_num_items'][0];
		$apiUri = str_replace('##ITEMS##', intval($items), $apiUri);

		$response = wp_remote_get($apiUri);

		if(wp_remote_retrieve_response_code($response) == 200){
			$response = json_decode(wp_remote_retrieve_body($response), true);
			$content = $response['html'];
		}

	}
	
	if($category == 'badge'){
		
		$style = intval($attributes['style']);
		$sellerId = $postMeta['ss_ebay_sellerId'][0];
		$content = '<a href="https://www.salessurvey.de/Seller/'. $sellerId .'/?locale='. get_locale() .'" title="'. sprintf(__('Auctions & Feedback from %s', 'netnovate-salessurvey'), $sellerId) .'" target="_blank"><img src="https://www.salessurvey.de/Image/'. $style .'/'. $sellerId .'" border="0" alt="'. sprintf(__('Auctions & Feedback from %s', 'netnovate-salessurvey'), $sellerId) .'"></a>';
		
	}
	
	return $content;
}

add_shortcode('salessurvey', 'salessurvey_shortcode');

function salessurvey_register_frontend_script() {
	wp_register_script('ss_frontend', plugins_url('assets/js/frontend.js', dirname(__FILE__)), array('jquery'));
	wp_enqueue_script('ss_frontend');	
}
add_action('wp_footer', 'salessurvey_register_frontend_script');