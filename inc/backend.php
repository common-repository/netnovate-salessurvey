<?php


/**
 * Setup admin
 */
function ssalessurvey_admin_init() {
	
	global $ss_plugin_config;
	
	$ss_plugin_config = array(
		'pluginName' => __('salessurvey Auctions Plugin for eBay', 'netnovate-salessurvey'), 
		'pluginVersion' => '1.31',
		'shortcode' => 'salessurvey',
		'ebayGlobalIds' => array(
			'EBAY-DE' => 'eBay ' . __('Germany', 'netnovate-salessurvey'),
			'EBAY-US' => 'eBay United States',
			'EBAY-AT' => 'eBay  ' . __('Austria', 'netnovate-salessurvey'),
			'EBAY-CH' => 'eBay  '. __('Switzerland', 'netnovate-salessurvey'),
			'EBAY-GB' => 'eBay UK',
			'EBAY-ENCA' => 'eBay Canada',
			'EBAY-FRCA' => 'eBay Canada (French)',
			'EBAY-AU' => 'eBay Australia',
			'EBAY-BE' => 'eBay Belgium',	
			'EBAY-FR' => 'eBay France',
			'EBAY-ES' => 'eBay Spain',			
			'EBAY-IT' => 'eBay Italy',
			'EBAY-NL' => 'eBay Netherlands',
			'EBAY-IE' => 'eBay Ireland',			
			'EBAY-PL' => 'eBay Poland'
		),
		'shopWidgetThemes' => array(
			'af' => __('Auctions & Feedback', 'netnovate-salessurvey'),
			'a' => __('Auctions', 'netnovate-salessurvey'),
			'f' => __('Feedback', 'netnovate-salessurvey'),
		),
		'tileSizes' => array(
			's' => __('Small', 'netnovate-salessurvey'),
			'm' => __('Medium', 'netnovate-salessurvey'),
			'l' => __('Large', 'netnovate-salessurvey'),
			'xl' => __('Very Large', 'netnovate-salessurvey'),
		),
		'articleSortOrder' => array(
			'EndTimeSoonest' => __('Items Ending First', 'netnovate-salessurvey'),
			'BestMatch' => __('Best Match', 'netnovate-salessurvey'),
			'StartTimeNewest' => __('New Items First', 'netnovate-salessurvey'),
			'PricePlusShippingHighest' => __('Highest Price Items First', 'netnovate-salessurvey'),
			'PricePlusShippingLowest' => __('Lowest Price Items First', 'netnovate-salessurvey'),
		)
	);

	
	//Permissions
	if(current_user_can('manage_options')) {
		
		// add metabox
		add_action('admin_head-post.php', 'salessurvey_create_custom_metabox');
		add_action('admin_head-post-new.php', 'salessurvey_create_custom_metabox');

		// save metabox data
		add_action('save_post', 'salessurvey_save_metabox', 10, 2);

		// CSS
		wp_register_style('ss_spectrum', plugins_url('assets/css/spectrum.min.css', dirname(__FILE__)));
		wp_enqueue_style('ss_spectrum');			
		wp_register_style('ss_backend_css', plugins_url('assets/css/backend.css', dirname(__FILE__)), array(), $ss_plugin_config['pluginVersion']);
		wp_enqueue_style('ss_backend_css');	

		// JS needed for metaxbox functionality
		wp_register_script('ss_spectrum', plugins_url('assets/js/spectrum.min.js', dirname(__FILE__)), array('jquery'));
		wp_enqueue_script('ss_spectrum');
		if(substr(get_locale(), 0, 3) == 'de_'){
			wp_register_script('ss_spectrum_lang', plugins_url('assets/js/jquery.spectrum-de.min.js', dirname(__FILE__)), array('ss_spectrum'));
			wp_enqueue_script('ss_spectrum_lang');
		}
		
		wp_register_script('ss_clipboard', plugins_url('assets/js/clipboard.min.js', dirname(__FILE__)), array('jquery'));
		wp_enqueue_script('ss_clipboard');		
		
	#	wp_register_script('ss_backend_js', plugins_url('assets/js/backend.js', dirname(__FILE__)), array('jquery'), $ss_plugin_config['pluginVersion']);
		wp_register_script('ss_backend_js', plugins_url('assets/js/backend.js', dirname(__FILE__)), array('jquery'), uniqid());
		
		$translationArray = array(
			'locale' => get_locale(),
			'noEbayNameSet' => __('Please set <b>eBay Username</b> first', 'netnovate-salessurvey')
		);
		
		wp_localize_script('ss_backend_js', 'backend', $translationArray);
		wp_enqueue_script('ss_backend_js');
		
	} else {
		wp_die(__('You are not allowed to access this part of the site'));
	}
}
add_action('admin_init', 'ssalessurvey_admin_init');


/**
 * Save the custom field data
 */
function salessurvey_save_metabox($post_id, $post) {

	if(!(wp_is_post_revision($post_id) || wp_is_post_autosave($post_id))) {
	
		foreach($_POST as $param => $value){
			if(substr($param,0,3) == 'ss_'){	
				if(empty($value)){
					delete_post_meta($post_id, $param);
				} else {
					update_post_meta($post_id, sanitize_text_field($param), sanitize_text_field($value));
				}
			}	
		}
	}
}


function salessurvey_create_custom_metabox() {
	add_meta_box('ss-metabox', __('salessurvey Auctions Plugin', 'netnovate-salessurvey'), 'salessurvey_create_metabox_fields', array('post', 'page'), 'normal', 'high');
}

function salessurvey_get_meta_value($key, $default = ''){
	
	global $post;
	
	$postMeta = get_post_meta($post->ID);
	
	if(isset($postMeta[$key])){
		if(is_array($postMeta[$key])){
			return $postMeta[$key][0];
		} else {
			return $postMeta[$key];	
		}
	} else {
		return $default;
	}
	
}

function salessurvey_create_metabox_fields(){
	
	global $post, $ss_plugin_config;

	$globalOptions = get_option('ss_options');

	
	//Get post meta	
	$postMeta = get_post_meta($post->ID);
	
	$ss_ebay_template = salessurvey_get_meta_value('ss_ebay_template', 'shop');
	
	?>
	<div id="ss_meta_options">
		
		<div style="padding:5px 7px; border:1px solid #c3e6cb; border-radius:5px; background:#D4EDDA;">
			<div class="control-group">
				<label class="control-label" for="ss_ebay_sellerId" style="font-size:1.2em"><?php echo __('eBay Username', 'netnovate-salessurvey');?></label>
				<div class="controls">
					<input type="text" name="ss_ebay_sellerId" id="ss_ebay_sellerId" style="font-size:1.2em; font-weight:bold" value="<?php echo salessurvey_get_meta_value('ss_ebay_sellerId', $globalOptions['ss_global_sellerId']) ;?>" />
					<button type="button" class="button ss-refresh-preview"><?php echo __('Refresh', 'netnovate-salessurvey');?></a>
				</div>
			</div>
		</div>
		
		<div class="nav-tab-wrapper" style="margin-bottom:0">
			<a class="nav-tab nav-tab-active" data-target="ss_meta_content_auctions"><?php echo __('Auctions Widgets', 'netnovate-salessurvey');?></a>
			<a class="nav-tab" data-target="ss_meta_content_badges" id="ss_meta_content_badges_tab"><?php echo __('Feedback-Badges', 'netnovate-salessurvey');?></a>				
		</div>
		
		<div class="ss_meta_content" id="ss_meta_content_auctions" style="padding:15px 10px; border:1px solid #CCC; border-top:0">
			<table>
				<tr>
					<td style="width:510px;vertical-align:top">
						<h3 style="margin-top:0"><?php echo __('Select Template:', 'netnovate-salessurvey');?></h3>

						<div class="pull-left ss_template <?php echo (($ss_ebay_template == 'shop') ? 'ss_template_active' : '');?>" data-widget="shop">
							<img src="<?php echo plugins_url('assets/img/widget_template1.png', dirname(__FILE__)) ;?>" style="width:100px" /><br>
							<div style="text-align:center; margin-bottom:10px"><small><?php echo __('Shop-Widget', 'netnovate-salessurvey');?><br/><br/></small></div>
						</div>				
						<div class="pull-left ss_template <?php echo (($ss_ebay_template == 'auctions') ? 'ss_template_active' : '');?>" data-widget="auctions">
							<img src="<?php echo plugins_url('assets/img/widget_template2.png', dirname(__FILE__)) ;?>" style="width:100px" />
							<div style="text-align:center; margin-bottom:10px"><small><?php echo __('Item-Slider <br/>(horizontal)', 'netnovate-salessurvey');?></small></div>
						</div>				
						<div class="pull-left ss_template <?php echo (($ss_ebay_template == 'auctionsv') ? 'ss_template_active' : '');?>" data-widget="auctionsv">
							<img src="<?php echo plugins_url('assets/img/widget_template3.png', dirname(__FILE__)) ;?>" style="width:100px" />
							<div style="text-align:center; margin-bottom:10px"><small><?php echo __('Item-Slider <br/>(vertical)', 'netnovate-salessurvey');?></small></div>
						</div>
						<div class="pull-left ss_template <?php echo (($ss_ebay_template == 'auctionsgallery') ? 'ss_template_active' : '');?>" data-widget="auctionsgallery">
							<img src="<?php echo plugins_url('assets/img/widget_template4.png', dirname(__FILE__)) ;?>" style="width:100px" />
							<div style="text-align:center; margin-bottom:10px"><small><?php echo __('Item-Gallery<br/>Widget <br/>', 'netnovate-salessurvey');?></small></div>
						</div>
						<br style="clear:left" />
						
					</td>
					<td style="vertical-align:top;padding-left:20px">
						<h3 style="margin-top:0"><?php echo __('Code:', 'netnovate-salessurvey');?></h3>
						<div><?php echo __('Place the following code within the content editor to specify where the widget will be shown.', 'netnovate-salessurvey');?></div>
						<div class="widget-codebox" id="widget-codebox">
							[salessurvey category="widget"]
						</div>
						<button type="button" class="button btn-clipboard" data-clipboard-target="#widget-codebox" /><?php echo __('Copy', 'netnovate-salessurvey');?></button>

					</td>
				</tr>
			</table>
			<table style="width:100%">
				<tr>
					<td style="width:410px; vertical-align:top">
						<h3><?php echo __('Settings:', 'netnovate-salessurvey');?></h3>
						<input type="hidden" value="<?php echo $ss_ebay_template ;?>" name="ss_ebay_template" id="ss_ebay_template" />
						<fieldset class="ss_meta_options">							
							<div class="control-group">
								<label class="control-label" for="ss_ebay_globalId"><?php echo __('eBay Website', 'netnovate-salessurvey');?></label>
								<div class="controls">
									<select name="ss_ebay_globalId" id="ss_ebay_globalId">
									<?php 
									$defaultGlobalId = salessurvey_get_meta_value('ss_ebay_globalId') ? salessurvey_get_meta_value('ss_ebay_globalId') : $globalOptions['ss_global_globalId'];
									
									if(!$defaultGlobalId){
										if(stristr(get_locale(), 'de_')) {
											$defaultGlobalId = 'EBAY-DE';	
										} else if(get_locale() == 'en_CA') {
											$defaultGlobalId = 'EBAY-ENCA';	
										} else if(get_locale() == 'en_GB') {
											$defaultGlobalId = 'EBAY-GB';	
										} else if(get_locale() == 'en_AU') {
											$defaultGlobalId = 'EBAY-AU';	
										} else {
											$defaultGlobalId ='EBAY-US';
										}									
									}
									
									foreach($ss_plugin_config['ebayGlobalIds'] as $globalId => $website){
										
										echo '<option value="'. $globalId .'" '. (($globalId == $defaultGlobalId) ? 'selected' : '') .'>'. $website .'</option>';
									}
									?>
									</select>
									<div class="ss-tooltip" data-title="<?php echo __('This is the eBay website where your items are usually listed.', 'netnovate-salessurvey');?>">&quest;</div>
								</div>
							</div>							
							<div class="control-group">
								<label class="control-label" for="ss_ebay_sort_order"><?php echo __('Sort Order', 'netnovate-salessurvey');?></label>
								<div class="controls">
									<select name="ss_ebay_sort_order" id="ss_ebay_sort_order">
									<?php  
									foreach($ss_plugin_config['articleSortOrder'] as $sortOrder => $description){
										echo '<option value="'. $sortOrder .'" '. (($sortOrder == salessurvey_get_meta_value('ss_ebay_sort_order')) ? 'selected' : '') .'>'. $description .'</option>';
									}
									?>
									</select>
									<div class="ss-tooltip" data-title="<?php echo __('Set sort order of displayed items here.', 'netnovate-salessurvey');?>">&quest;</div>
								</div>
							</div>						
							<div class="control-group" id="ss_setting_theme">
								<label class="control-label" for="ss_ebay_shop_theme"><?php echo __('Display', 'netnovate-salessurvey');?></label>
								<div class="controls">
									<select name="ss_ebay_shop_theme" id="ss_ebay_shop_theme">
									<?php  
									foreach($ss_plugin_config['shopWidgetThemes'] as $value => $description){
										echo '<option value="'. $value .'" '. (($value == salessurvey_get_meta_value('ss_ebay_shop_theme')) ? 'selected' : '') .'>'. $description .'</option>';
									}
									?>
									</select>
								</div>	
							</div>	
							<div class="control-group" id="ss_setting_tilesize">
								<label class="control-label" for="ss_ebay_tile_size"><?php echo __('Tile Size', 'netnovate-salessurvey');?></label>
								<div class="controls">
									<select name="ss_ebay_tile_size" id="ss_ebay_tile_size">
									<?php  
									foreach($ss_plugin_config['tileSizes'] as $value => $description){
										echo '<option value="'. $value .'" '. (($value == salessurvey_get_meta_value('ss_ebay_tile_size', 'm')) ? 'selected' : '') .'>'. $description .'</option>';
									}
									?>
									</select>
									<div class="ss-tooltip" data-title="<?php echo __('Set size of item tiles.', 'netnovate-salessurvey');?>">&quest;</div>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="ss_ebay_shop_height"><?php echo __('Widget Height', 'netnovate-salessurvey');?></label>
								<div class="controls">
									<input type="text" name="ss_ebay_shop_height" id="ss_ebay_shop_height" value="<?php echo salessurvey_get_meta_value('ss_ebay_shop_height', 500) ;?>" />px
									<div class="ss-tooltip" data-title="<?php echo __('Define display height of widget here', 'netnovate-salessurvey');?>">&quest;</div>
								</div>									
							</div>
							<div class="control-group">
								<label class="control-label" for="ss_ebay_num_items"><?php echo __('Number of Items', 'netnovate-salessurvey');?></label>
								<div class="controls">
									<input type="text" name="ss_ebay_num_items" id="ss_ebay_num_items" value="<?php echo salessurvey_get_meta_value('ss_ebay_num_items', 20) ;?>" />
									<div class="ss-tooltip" data-title="<?php echo __('Set the number of displayed items per page (max. 100)', 'netnovate-salessurvey');?>">&quest;</div>
								</div>				
							</div>
							<div class="control-group" id="ss_setting_frame">
								<label class="control-label" for="ss_ebay_frame"><?php echo __('Frame', 'netnovate-salessurvey');?></label>
								<div class="controls">
									<input type="checkbox" name="ss_ebay_frame" id="ss_ebay_frame" value="1" <?php echo ((salessurvey_get_meta_value('ss_ebay_frame') == 1) ? 'checked="checked"' : '') ;?> />									
								</div>
							</div>
							<div class="control-group" id="ss_setting_frame_color">
								<label class="control-label" for="ss_ebay_frame_color"><?php echo __('Framecolor', 'netnovate-salessurvey');?></label>
								<div class="controls">
									<input type="text" name="ss_ebay_frame_color" id="ss_ebay_frame_color" value="<?php echo salessurvey_get_meta_value('ss_ebay_frame_color', '#5abf2b') ;?>" />
								</div>
							</div>
							<div style="text-align:center; padding-top:20px">
								<button type="button" class="button ss-refresh-preview"><?php echo __('Refresh Preview', 'netnovate-salessurvey');?></a>
							</div>
						</fieldset>
					</td>
					<td style="vertical-align:top">
						<h3><?php echo __('Preview:', 'netnovate-salessurvey');?></h3>
						<div id="ss_preview"></div>
					</td>
				</tr>
			</table>
		</div>
		<div class="ss_meta_content hidden" id="ss_meta_content_badges" style="padding:15px 10px; border:1px solid #CCC; border-top:0">
			
			<div id="ss_meta_content_badges_inactive">
				<?php echo __('Please set <b>eBay Username</b> first', 'netnovate-salessurvey');?>
			</div>
				
			<div id="ss_meta_content_badges_active" style="display:none">
				
				<div>
					<?php echo __('Please copy the code shown below each badge and paste it into content editor to that position where you want to show the badge.', 'netnovate-salessurvey');?>
					<div><h3><?php echo __('Horizontal Banner', 'netnovate-salessurvey');?></h3></div>
					<div class="pull-left" style="margin:6px 10px;">
						<div style="text-align:center">
							<small style="margin-bottom:10px; display:block">155x30 Pixel</small>
							<img src="https://www.salessurvey.de/Image/6/<?php echo salessurvey_get_meta_value('ss_ebay_sellerId', $globalOptions['ss_global_sellerId']) ;?>?locale=<?php echo get_locale();?>" class="ss-feedback-badge-image" data-image="6" data-locale="<?php echo get_locale();?>" style="border:0; cursor:pointer" />
						</div>					
						<div style="text-align:center; margin-top:8px">
							<?php echo __('Code:', 'netnovate-salessurvey');?><br/>
							<div class="feedback-badge-codebox" id="feedback-badge-codebox-6">
								[salessurvey category="badge" style="6"]
							</div>
							<div style="padding-top:7px;">
								<button type="button" class="button btn-clipboard" data-clipboard-target="#feedback-badge-codebox-6" /><?php echo __('Copy', 'netnovate-salessurvey');?></button>
							</div>
						</div>
					</div>		
					<div class="pull-left" style="margin:6px 10px;">
						<div style="text-align:center">
							<small style="margin-bottom:10px; display:block">250x40 Pixel</small>
							<img src="https://www.salessurvey.de/Image/1/<?php echo salessurvey_get_meta_value('ss_ebay_sellerId', $globalOptions['ss_global_sellerId']) ;?>?locale=<?php echo get_locale();?>" class="ss-feedback-badge-image" data-image="1" data-locale="<?php echo get_locale();?>" style="border:0; cursor:pointer" />
						</div>							
						<div style="text-align:center; margin-top:8px">
							<?php echo __('Code:', 'netnovate-salessurvey');?><br/>
							<div class="feedback-badge-codebox" id="feedback-badge-codebox-1">
								[salessurvey category="badge" style="1"]
							</div>
							<div style="padding-top:7px;">
								<button type="button" class="button btn-clipboard" data-clipboard-target="#feedback-badge-codebox-1" /><?php echo __('Copy', 'netnovate-salessurvey');?></button>
							</div>
						</div>
					</div>
					<div class="pull-left" style="margin:6px 10px;">
						<div style="text-align:center">
							<small style="margin-bottom:10px; display:block">468x75 Pixel</small>
							<img src="https://www.salessurvey.de/Image/3/<?php echo salessurvey_get_meta_value('ss_ebay_sellerId', $globalOptions['ss_global_sellerId']) ;?>?locale=<?php echo get_locale();?>" class="ss-feedback-badge-image" data-image="3" data-locale="<?php echo get_locale();?>" style="border:0; cursor:pointer" />
						</div>	
						<div style="text-align:center; margin-top:8px">
							<?php echo __('Code:', 'netnovate-salessurvey');?><br/>
							<div class="feedback-badge-codebox" id="feedback-badge-codebox-3">
								[salessurvey category="badge" style="3"]
							</div>
							<div style="padding-top:7px;">
								<button type="button" class="button btn-clipboard" data-clipboard-target="#feedback-badge-codebox-3" /><?php echo __('Copy', 'netnovate-salessurvey');?></button>
							</div>
						</div>
					</div>
				</div>
				<br style="clear:left" />
				<div>
					<div><h3><?php echo __('Squared', 'netnovate-salessurvey');?></h3></div>
					<div class="pull-left" style="margin:6px 10px;">
						<div style="text-align:center">
							<small style="margin-bottom:10px; display:block">190x150 Pixel</small>
							<img src="https://www.salessurvey.de/Image/9/<?php echo salessurvey_get_meta_value('ss_ebay_sellerId', $globalOptions['ss_global_sellerId']) ;?>?locale=<?php echo get_locale();?>" class="ss-feedback-badge-image" data-image="9" data-locale="<?php echo get_locale();?>" style="border:0; cursor:pointer" />
						</div>
						<div style="text-align:center; margin-top:8px">
							<?php echo __('Code:', 'netnovate-salessurvey');?><br/>
							<div class="feedback-badge-codebox" id="feedback-badge-codebox-9">
								[salessurvey category="badge" style="9"]
							</div>
							<div style="padding-top:7px;">
								<button type="button" class="button btn-clipboard" data-clipboard-target="#feedback-badge-codebox-9" /><?php echo __('Copy', 'netnovate-salessurvey');?></button>
							</div>
						</div>
					</div>						
				
					<div class="pull-left" style="margin:6px 10px;">
						<div style="text-align:center">
							<small style="margin-bottom:10px; display:block">200x200 Pixel</small>
							<img src="https://www.salessurvey.de/Image/2/<?php echo salessurvey_get_meta_value('ss_ebay_sellerId', $globalOptions['ss_global_sellerId']) ;?>?locale=<?php echo get_locale();?>" class="ss-feedback-badge-image" data-image="2" data-locale="<?php echo get_locale();?>" style="border:0; cursor:pointer" />
						</div>
						<div style="text-align:center; margin-top:8px">
							<?php echo __('Code:', 'netnovate-salessurvey');?><br/>
							<div class="feedback-badge-codebox" id="feedback-badge-codebox-2">
								[salessurvey category="badge" style="2"]
							</div>
							<div style="padding-top:7px;">
								<button type="button" class="button btn-clipboard" data-clipboard-target="#feedback-badge-codebox-2" /><?php echo __('Copy', 'netnovate-salessurvey');?></button>
							</div>
						</div>
					</div>	
					<div class="pull-left" style="margin:6px 10px;">
						<div style="text-align:center">
							<small style="margin-bottom:10px; display:block">160x230 Pixel</small>
							<img src="https://www.salessurvey.de/Image/8/<?php echo salessurvey_get_meta_value('ss_ebay_sellerId', $globalOptions['ss_global_sellerId']) ;?>?locale=<?php echo get_locale();?>" class="ss-feedback-badge-image" data-image="8" data-locale="<?php echo get_locale();?>" style="border:0; cursor:pointer" />
						</div>
						<div style="text-align:center; margin-top:8px">
							<?php echo __('Code:', 'netnovate-salessurvey');?><br/>
							<div class="feedback-badge-codebox" id="feedback-badge-codebox-8">
								[salessurvey category="badge" style="8"]
							</div>
							<div style="padding-top:7px;">
								<button type="button" class="button btn-clipboard" data-clipboard-target="#feedback-badge-codebox-8" /><?php echo __('Copy', 'netnovate-salessurvey');?></button>
							</div>
						</div>
					</div>						
					<div class="pull-left" style="margin:6px 10px;">
						<div style="text-align:center">
							<small style="margin-bottom:10px; display:block">250x250 Pixel</small>
							<img src="https://www.salessurvey.de/Image/4/<?php echo salessurvey_get_meta_value('ss_ebay_sellerId', $globalOptions['ss_global_sellerId']) ;?>?locale=<?php echo get_locale();?>" class="ss-feedback-badge-image" data-image="4" data-locale="<?php echo get_locale();?>" style="border:0; cursor:pointer" />
						</div>
						<div style="text-align:center; margin-top:8px">
							<?php echo __('Code:', 'netnovate-salessurvey');?><br/>
							<div class="feedback-badge-codebox" id="feedback-badge-codebox-4">
								[salessurvey category="badge" style="4"]
							</div>
							<div style="padding-top:7px;">
								<button type="button" class="button btn-clipboard" data-clipboard-target="#feedback-badge-codebox-4" /><?php echo __('Copy', 'netnovate-salessurvey');?></button>
							</div>
						</div>
					</div>					

				</div>
				<br style="clear:left" />
				<div>
					<div><h3><?php echo __('Round', 'netnovate-salessurvey');?></h3></div>
					<div class="pull-left" style="margin:6px 10px;">
						<div style="text-align:center">
							<small style="margin-bottom:10px; display:block">155x155 Pixel</small>
							<img src="https://www.salessurvey.de/Image/7/<?php echo salessurvey_get_meta_value('ss_ebay_sellerId', $globalOptions['ss_global_sellerId']) ;?>?locale=<?php echo get_locale();?>" class="ss-feedback-badge-image" data-image="7" data-locale="<?php echo get_locale();?>" style="border:0; cursor:pointer" />
						</div>
						<div style="text-align:center; margin-top:8px">
							<?php echo __('Code:', 'netnovate-salessurvey');?><br/>
							<div class="feedback-badge-codebox" id="feedback-badge-codebox-7">
								[salessurvey category="badge" style="7"]
							</div>
							<div style="padding-top:7px;">
								<button type="button" class="button btn-clipboard" data-clipboard-target="#feedback-badge-codebox-7" /><?php echo __('Copy', 'netnovate-salessurvey');?></button>
							</div>
						</div>
					</div>
					<div class="pull-left" style="margin:6px 10px;">
						<div style="text-align:center">
							<small style="margin-bottom:10px; display:block">220x220 Pixel</small>
							<img src="https://www.salessurvey.de/Image/5/<?php echo salessurvey_get_meta_value('ss_ebay_sellerId', $globalOptions['ss_global_sellerId']) ;?>?locale=<?php echo get_locale();?>" class="ss-feedback-badge-image" data-image="5" data-locale="<?php echo get_locale();?>" style="border:0; cursor:pointer" />
						</div>
						<div style="text-align:center; margin-top:8px">
							<?php echo __('Code:', 'netnovate-salessurvey');?><br/>
							<div class="feedback-badge-codebox" id="feedback-badge-codebox-5">
								[salessurvey category="badge" style="5"]
							</div>
							<div style="padding-top:7px;">
								<button type="button" class="button btn-clipboard" data-clipboard-target="#feedback-badge-codebox-5" /><?php echo __('Copy', 'netnovate-salessurvey');?></button>
							</div>
						</div>
					</div>						
					<div class="pull-left" style="margin:6px 10px;">
						<div style="text-align:center">
							<small style="margin-bottom:10px; display:block">250x250 Pixel</small>
							<img src="https://www.salessurvey.de/Image/10/<?php echo salessurvey_get_meta_value('ss_ebay_sellerId', $globalOptions['ss_global_sellerId']) ;?>?locale=<?php echo get_locale();?>" class="ss-feedback-badge-image" data-image="10" data-locale="<?php echo get_locale();?>" style="border:0; cursor:pointer" />
						</div>
						<div style="text-align:center; margin-top:8px">
							<?php echo __('Code:', 'netnovate-salessurvey');?><br/>
							<div class="feedback-badge-codebox" id="feedback-badge-codebox-10">
								[salessurvey category="badge" style="10"]
							</div>
							<div style="padding-top:7px;">
								<button type="button" class="button btn-clipboard" data-clipboard-target="#feedback-badge-codebox-10" /><?php echo __('Copy', 'netnovate-salessurvey');?></button>
							</div>
						</div>
					</div>						
					<div class="pull-left" style="margin:6px 10px;">
						<div style="text-align:center">
							<small style="margin-bottom:10px; display:block">300x250 Pixel</small>
							<img src="https://www.salessurvey.de/Image/11/<?php echo salessurvey_get_meta_value('ss_ebay_sellerId', $globalOptions['ss_global_sellerId']) ;?>?locale=<?php echo get_locale();?>" class="ss-feedback-badge-image" data-image="11" data-locale="<?php echo get_locale();?>" style="border:0; cursor:pointer" />
						</div>
						<div style="text-align:center; margin-top:8px">
							<?php echo __('Code:', 'netnovate-salessurvey');?><br/>
							<div class="feedback-badge-codebox" id="feedback-badge-codebox-11">
								[salessurvey category="badge" style="11"]
							</div>
							<div style="padding-top:7px;">
								<button type="button" class="button btn-clipboard" data-clipboard-target="#feedback-badge-codebox-11" /><?php echo __('Copy', 'netnovate-salessurvey');?></button>
							</div>
						</div>
					</div>	
					
				</div>
				<br style="clear:left" />
			</div>
		</div>
	</div>

	<?php
}





/*
* Global Plugin settings section
*/
function salessurvey_admin_settings(){

	if(current_user_can('manage_options')) {
		
		register_setting('ss_options', 'ss_options', 'salessurvey_options_validate');

		add_settings_section('ss_ebay_defaults', __('Global Settings', 'netnovate-salessurvey'), 'salessurvey_settings_defaults_text', 'ss_general');
		add_settings_field('ss_global_sellerId', __('eBay Username', 'netnovate-salessurvey'), 'salessurvey_global_sellerId_setting', 'ss_general', 'ss_ebay_defaults');
		add_settings_field('ss_global_globalId', __('eBay Website', 'netnovate-salessurvey'), 'salessurvey_global_globalId_setting', 'ss_general', 'ss_ebay_defaults');

	}
}
add_action('admin_init', 'salessurvey_admin_settings');


function salessurvey_settings_defaults_text(){
	echo '<div>'. __('These values are used as default with every added widget', 'netnovate-salessurvey') .'</div>';
}

function salessurvey_global_globalId_setting() {
	
	global $ss_plugin_config;
	
	$options = get_option('ss_options');

	if(is_array($options) && array_key_exists('ss_global_globalId', $options)) {
		$ss_global_globalId = $options['ss_global_globalId'];
	} else if(stristr(get_locale(), 'de_')) {
		$ss_global_globalId = 'EBAY-DE';	
	} else if(get_locale() == 'en_CA') {
		$ss_global_globalId = 'EBAY-ENCA';	
	} else if(get_locale() == 'en_GB') {
		$ss_global_globalId = 'EBAY-GB';	
	} else if(get_locale() == 'en_AU') {
		$ss_global_globalId = 'EBAY-AU';	
	} else {
		$ss_global_globalId = __('EBAY-US', 'netnovate-salessurvey');
	}

	echo '<select name="ss_options[ss_global_globalId]" id="ss_global_globalId">';
	foreach($ss_plugin_config['ebayGlobalIds'] as $globalId => $description) {
		$selected = ($ss_global_globalId == $globalId) ? ' selected="selected"' : '';
		echo '<option value="' . $globalId . '"' . $selected . '>' . $description . '</option>';	
	}
	echo '</select>
	<div class="ss-tooltip" data-title="'. __('Please set your default eBay Website', 'netnovate-salessurvey') .'">&quest;</div>';

}

function salessurvey_global_sellerId_setting() {
	
	$options = get_option('ss_options');

	if(is_array($options) && array_key_exists('ss_global_sellerId', $options)) {
		$ss_global_sellerId = $options['ss_global_sellerId'];
	} else {
		$ss_global_sellerId = '';
	}
		
	echo '
	<input type="text" id="ss_global_sellerId" class="regular-text" name="ss_options[ss_global_sellerId]" value="' . $ss_global_sellerId . '" />
	<div class="ss-tooltip" data-title="'. __('Set your eBay Username, this one is used by default in all widgets.', 'netnovate-salessurvey') .'">&quest;</div>
	';	
}


function salessurvey_options_validate($input) {
	$output = array();
	foreach($input as $key => $value) {
		$output[$key] = sanitize_text_field($value);
	}
	return $output;
}


function salessurvey_add_action_link($links) {
 
	$linkToAdd = array(
		'<a href="' . admin_url('options-general.php?page=ss_options') . '">'. __('Settings', 'netnovate-salessurvey') .'</a>',	 
		'<a href="' . ('https://wordpress.org/support/plugin/netnovate-salessurvey/reviews/') . '" target="_blank">'. __('Rate Plugin', 'netnovate-salessurvey') .'</a>',		 
	);

	return array_merge($linkToAdd, $links);
}
add_filter('plugin_action_links_netnovate-salessurvey/netnovate-salessurvey.php', 'salessurvey_add_action_link');


function salessurvey_set_admin_page() {
	//Permissions
	if(current_user_can('manage_options')) {
		add_options_page(__('salessurvey Auctions Plugin', 'netnovate-salessurvey'), __('salessurvey Auctions', 'netnovate-salessurvey'), 'manage_options', 'ss_options', 'salessurvey_show_options_page');
	}
}
add_action('admin_menu', 'salessurvey_set_admin_page');


function salessurvey_show_options_page() {
		
	?>
	<div id="salessurvey_admin_options_container">
	
		<h1><?php echo __('salessurvey Auctions Plugin', 'netnovate-salessurvey');?></h1>
		<div><?php echo __('Please use the "salessurvey auctions plugin" box below content editor to add auctions- or feedback-widgets to your pages or posts.', 'netnovate-salessurvey');?></div>
		<div><?php echo __('In case you want to add a widget into sidebar, please configure that under "Appearance" -> "<a href="widgets.php">Widgets</a>".', 'netnovate-salessurvey');?></div>
		<?php

		echo '<form action="' . admin_url('options.php') . '" method="post">';		
		settings_fields('ss_options');
		do_settings_sections('ss_general');
		echo '<input class="button button-primary" name="Submit" type="submit" value="'. __('Save Settings', 'netnovate-salessurvey') .'" />
		</form>';
		
		?>
	</div>
	<?php
	
}
