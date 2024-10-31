<?php

class salessurvey_widget_auction extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'ss_auction_widget',
			__('eBay Items', 'netnovate-salessurvey'),
			array(
				'description' => __('Use this Widget to display your eBay Items for examle in the Sidebar.', 'netnovate-salessurvey')
			)
		);		

	}
	
	public function widget($args, $instance) {

		$globalOptions = get_option('ss_options');
		
		$widgetTitle = isset($instance['ss_badge_widget_title']) ? $instance['ss_badge_widget_title'] : __('eBay Items', 'netnovate-salessurvey');
		$sellerId = $instance['ss_badge_widget_sellerId'] ? $instance['ss_badge_widget_sellerId'] : $globalOptions['ss_global_sellerId'];
		$globalId = $instance['ss_badge_widget_globalId'] ? $instance['ss_badge_widget_globalId'] : $globalOptions['ss_global_globalId'];
		$style = $instance['ss_badge_widget_style'] ? $instance['ss_badge_widget_style'] : 'auctionsv';
		$height = $instance['ss_badge_widget_height'] ? intval($instance['ss_badge_widget_height']) : 500;
		$items = $instance['ss_badge_widget_items'] ? intval($instance['ss_badge_widget_items']) : 12;
		$tileSize = $instance['ss_badge_widget_tilesize'] ? $instance['ss_badge_widget_tilesize'] : 'm';
		$sortOrder = $instance['ss_badge_widget_sortorder'] ? $instance['ss_badge_widget_sortorder'] : 'EndTimeSoonest';
			
		?>
		<aside class="widget" style="margin:10px 0">
		<?php
		
		if(!empty($widgetTitle)){
			?>
			<h1 class="widget-title" style="margin-bottom:10px"><?php echo $widgetTitle;?></h1>
			<?php
		}

		if($sellerId){
			
			$widgetId = uniqid();
			?>
		
			<!-- START salessurvey.de widget -->
			<div id="salessurvey_widget_<?php echo $widgetId;?>"></div>
			<script type="text/javascript" src="https://www.salessurvey.de/<?php echo $style;?>-widget.js?sellerId=<?php echo $sellerId;?>&globalId=<?php echo $globalId;?>&locale=<?php echo get_locale();?>&sortOrder=<?php echo $sortOrder;?>&theme=noframe&tileSize=<?php echo $tileSize;?>&tabs=af&fontColor=%23FFFFFF&textShadow=1&bgColor=%23444444&width=240&responsive=1&height=<?php echo $height;?>&scrollBehavior=continuous&scrollSpeed=9500&items=<?php echo $items;?>&fields%5Bimage%5D=1&fields%5Bbids%5D=1&fields%5Bprice%5D=1&fields%5BremainingTime%5D=1&fields%5BfeedbackIcon%5D=1&fields%5BfeedbackSeller%5D=1&fields%5BfeedbackTime%5D=1&suffix=<?php echo $widgetId;?>&source=wp-widget"></script>
			<!-- END salessurvey.de widget -->
		
			<?php
		} else {
			_e('Invalid eBay Username given.', 'netnovate-salessurvey');
		}
		
		?>
		</aside>
		<?php
		
    }
	
	public function form($instance) {

		global $ss_plugin_config;
	
		$globalOptions = get_option('ss_options');
	
		if(isset($instance['ss_badge_widget_sellerId'])) {
           $ss_badge_widget_sellerId = $instance['ss_badge_widget_sellerId'];
        } else {
		   $ss_badge_widget_sellerId = $globalOptions['ss_global_sellerId'];	
		}		
		
		if(isset($instance['ss_badge_widget_globalId'])) {
           $ss_badge_widget_globalId = $instance['ss_badge_widget_globalId'];
        } else if($globalOptions['ss_global_globalId']){
		   $ss_badge_widget_globalId = $globalOptions['ss_global_globalId'];	
		} else {
			if(stristr(get_locale(), 'de_')) {
				$ss_badge_widget_globalId = 'EBAY-DE';	
			} else if(get_locale() == 'en_CA') {
				$ss_badge_widget_globalId = 'EBAY-ENCA';	
			} else if(get_locale() == 'en_GB') {
				$ss_badge_widget_globalId = 'EBAY-GB';	
			} else if(get_locale() == 'en_AU') {
				$ss_badge_widget_globalId = 'EBAY-AU';	
			} else {
				$ss_badge_widget_globalId ='EBAY-US';
			}	
		}
		
		if(isset($instance['ss_badge_widget_title'])) {
           $ss_badge_widget_title = $instance['ss_badge_widget_title'];
        } else {
		   $ss_badge_widget_title = __('eBay Items', 'netnovate-salessurvey');	
		}	
		
		if(isset($instance['ss_badge_widget_style'])) {
           $ss_badge_widget_style = $instance['ss_badge_widget_style'];
        } else {
		   $ss_badge_widget_style = 'auctionsv';	
		}	
		
		if(isset($instance['ss_badge_widget_height'])) {
           $ss_badge_widget_height = $instance['ss_badge_widget_height'];
        } else {
		   $ss_badge_widget_height = 500;	
		}		
		
		if(isset($instance['ss_badge_widget_items'])) {
           $ss_badge_widget_items = $instance['ss_badge_widget_items'];
        } else {
		   $ss_badge_widget_items = 12;	
		}		
		
		if(isset($instance['ss_badge_widget_tilesize'])) {
           $ss_badge_widget_tilesize = $instance['ss_badge_widget_tilesize'];
        } else {
		   $ss_badge_widget_tilesize = 'm';	
		}		
		
		if(isset($instance['ss_badge_widget_sortorder'])) {
           $ss_badge_widget_sortorder = $instance['ss_badge_widget_sortorder'];
        } else {
		   $ss_badge_widget_sortorder = 'EndTimeSoonest';	
		}

		?>
		
		<div class="ss-widget-container" id="<?php echo $this->get_field_id('ss_badge_conatiner'); ?>">		
			<div class="control-group">
				<label class="control-label" for="<?php echo $this->get_field_name('ss_badge_widget_title'); ?>"><?php echo __('Widget Title', 'netnovate-salessurvey');?></label>
				<div class="controls">
					<input type="text" name="<?php echo $this->get_field_name('ss_badge_widget_title'); ?>" id="<?php echo $this->get_field_id('ss_badge_widget_title'); ?>" value="<?php echo $ss_badge_widget_title;?>" />
				</div>
			</div>			
			<div class="control-group">
				<label class="control-label" for="<?php echo $this->get_field_name('ss_badge_widget_sellerId'); ?>"><?php echo __('eBay Username', 'netnovate-salessurvey');?></label>
				<div class="controls">
					<input type="text" class="ss-widget-sellerId" name="<?php echo $this->get_field_name('ss_badge_widget_sellerId'); ?>" id="<?php echo $this->get_field_id('ss_badge_widget_sellerId'); ?>" value="<?php echo $ss_badge_widget_sellerId;?>" />
				</div>
			</div>				
			<div class="control-group">
				<label class="control-label" for="<?php echo $this->get_field_name('ss_badge_widget_globalId'); ?>"><?php echo __('eBay Website', 'netnovate-salessurvey');?></label>
				<div class="controls">
					<select name="<?php echo $this->get_field_name('ss_badge_widget_globalId'); ?>" id="<?php echo $this->get_field_id('ss_badge_widget_globalId'); ?>">
						<?php 
						foreach($ss_plugin_config['ebayGlobalIds'] as $globalId => $website){
							echo '<option value="'. $globalId .'" '. (($globalId == $ss_badge_widget_globalId) ? 'selected' : '') .'>'. $website .'</option>';
						}
						?>
					</select>
				</div>
			</div>			
			<div class="control-group">
				<label class="control-label" for="<?php echo $this->get_field_name('ss_badge_widget_style'); ?>"><?php echo __('Style', 'netnovate-salessurvey');?></label>
				<div class="controls">
					<select class="ss-widget-style" name="<?php echo $this->get_field_name('ss_badge_widget_style'); ?>" id="<?php echo $this->get_field_id('ss_badge_widget_style'); ?>">
					<?php 
					$styleArray = array(
						'auctionsv' => __('Slider vertical', 'netnovate-salessurvey'),
						'auctions' => __('Slider horizontal', 'netnovate-salessurvey'),
						'auctionsgallery' => __('Itemgallery', 'netnovate-salessurvey'),
					);
					foreach($styleArray as $style => $description){
						echo '<option value="'. $style .'" '. (($style == $ss_badge_widget_style) ? 'selected' : '') .'>'. $description .'</option>';
					}
					?>
					</select>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="<?php echo $this->get_field_name('ss_badge_widget_sortorder'); ?>"><?php echo __('Sort Order', 'netnovate-salessurvey');?></label>
				<div class="controls">
					<select name="<?php echo $this->get_field_name('ss_badge_widget_sortorder'); ?>" id="<?php echo $this->get_field_id('ss_badge_widget_sortorder'); ?>">
					<?php
					foreach($ss_plugin_config['articleSortOrder'] as $sortorder => $description){
						echo '<option value="'. $sortorder .'" '. (($sortorder == $ss_badge_widget_sortorder) ? 'selected' : '') .'>'. $description .'</option>';
					}
					?>
					</select>
				</div>
			</div>	
			<div class="control-group">
				<label class="control-label" for="<?php echo $this->get_field_name('ss_badge_widget_tilesize'); ?>"><?php echo __('Tile Size', 'netnovate-salessurvey');?></label>
				<div class="controls">
					<select name="<?php echo $this->get_field_name('ss_badge_widget_tilesize'); ?>" id="<?php echo $this->get_field_id('ss_badge_widget_tilesize'); ?>">
					<?php
					foreach($ss_plugin_config['tileSizes'] as $tilesize => $description){
						echo '<option value="'. $tilesize .'" '. (($tilesize == $ss_badge_widget_tilesize) ? 'selected' : '') .'>'. $description .'</option>';
					}
					?>
					</select>
				</div>
			</div>				
			<div class="control-group">
				<label class="control-label" for="<?php echo $this->get_field_name('ss_badge_widget_height'); ?>"><?php echo __('Widget Height', 'netnovate-salessurvey');?></label>
				<div class="controls">
					<input type="text" name="<?php echo $this->get_field_name('ss_badge_widget_height'); ?>" id="<?php echo $this->get_field_id('ss_badge_widget_height'); ?>" value="<?php echo $ss_badge_widget_height;?>" />px
				</div>
			</div>				
			<div class="control-group">
				<label class="control-label" for="<?php echo $this->get_field_name('ss_badge_widget_items'); ?>"><?php echo __('Number of Items', 'netnovate-salessurvey');?></label>
				<div class="controls">
					<input type="text" name="<?php echo $this->get_field_name('ss_badge_widget_items'); ?>" id="<?php echo $this->get_field_id('ss_badge_widget_items'); ?>" value="<?php echo $ss_badge_widget_items;?>" />
				</div>
			</div>				
			<br/>
		</div>
		<?php
	}
	
	public function update($new_instance, $old_instance) {

		$instance = array();
        $instance['ss_badge_widget_title'] = (!empty($new_instance['ss_badge_widget_title'])) ? sanitize_text_field($new_instance['ss_badge_widget_title']) : '';
        $instance['ss_badge_widget_sellerId'] =(!empty($new_instance['ss_badge_widget_sellerId'])) ? sanitize_text_field($new_instance['ss_badge_widget_sellerId']) : '';
        $instance['ss_badge_widget_globalId'] =(!empty($new_instance['ss_badge_widget_globalId'])) ? sanitize_text_field($new_instance['ss_badge_widget_globalId']) : '';
        $instance['ss_badge_widget_style'] =(!empty($new_instance['ss_badge_widget_style'])) ? sanitize_text_field($new_instance['ss_badge_widget_style']) : '';
        $instance['ss_badge_widget_height'] =(!empty($new_instance['ss_badge_widget_height'])) ? intval($new_instance['ss_badge_widget_height']) : 500;
        $instance['ss_badge_widget_items'] =(!empty($new_instance['ss_badge_widget_items'])) ? intval($new_instance['ss_badge_widget_items']) : 12;
        $instance['ss_badge_widget_tilesize'] =(!empty($new_instance['ss_badge_widget_tilesize'])) ? sanitize_text_field($new_instance['ss_badge_widget_tilesize']) : 'm';
        $instance['ss_badge_widget_sortorder'] =(!empty($new_instance['ss_badge_widget_sortorder'])) ? sanitize_text_field($new_instance['ss_badge_widget_sortorder']) : 'EndTimeSoonest';

        return $instance;
    
    }	
	
}






class salessurvey_widget_badge extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'ss_badge_widget',
			__('eBay Feedback', 'netnovate-salessurvey'),
			array(
				'description' => __('Use this Widget to add eBay-Feedback-Bagdes to your Sidebar.', 'netnovate-salessurvey')
			)
		);		
	}
	
	public function widget($args, $instance) {
		
		
		$globalOptions = get_option('ss_options');
		
		$sellerId = $instance['ss_badge_widget_sellerId'] ? $instance['ss_badge_widget_sellerId'] : $globalOptions['ss_global_sellerId'];
		$widgetTitle = isset($instance['ss_badge_widget_title']) ? $instance['ss_badge_widget_title'] : __('eBay Feedback', 'netnovate-salessurvey');
		
		?>
		<aside class="widget" style="margin:10px 0">
		<?php
		
		if(!empty($widgetTitle)){
			?>
			<h1 class="widget-title" style="margin-bottom:10px"><?php echo $widgetTitle;?></h1>
			<?php
		}
		
		if($sellerId){
		?>
			<a href="https://www.salessurvey.de/Seller/<?php echo $sellerId;?>/" title="<?php echo sprintf(__('Auctions and Feedback of %s', 'netnovate-salessurvey'), $sellerId);?>" target="_blank">
				<img src="https://www.salessurvey.de/Image/<?php echo $instance['ss_badge_widget_style'];?>/<?php echo $sellerId;?>?locale=<?php echo get_locale();?>" border="0" style="max-width:100%" alt="<?php echo sprintf(__('Auctions and Feedback of %s', 'netnovate-salessurvey'), $sellerId);?>">
			</a>
			<?php
		} else {		
			 _e('Invalid eBay Username given.', 'netnovate-salessurvey');
		}
		
		?>
		</aside>
		<?php
		
    }
	
	public function form($instance) {

		$globalOptions = get_option('ss_options');
	
		if(isset($instance['ss_badge_widget_sellerId'])) {
           $ss_badge_widget_sellerId = $instance['ss_badge_widget_sellerId'];
        } else {
		   $ss_badge_widget_sellerId = $globalOptions['ss_global_sellerId'];	
		}		
		
		if(isset($instance['ss_badge_widget_title'])) {
           $ss_badge_widget_title = $instance['ss_badge_widget_title'];
        } else {
		   $ss_badge_widget_title = __('eBay Feedback', 'netnovate-salessurvey');	
		}	
		
		if(isset($instance['ss_badge_widget_style'])) {
           $ss_badge_widget_style = $instance['ss_badge_widget_style'];
        } else {
		   $ss_badge_widget_style = 4;	
		}

		?>
		
		<div class="ss-widget-container" id="<?php echo $this->get_field_id('ss_badge_conatiner'); ?>">		
			<div class="control-group">
				<label class="control-label" for="<?php echo $this->get_field_name('ss_badge_widget_title'); ?>"><?php echo __('Widget Title', 'netnovate-salessurvey');?></label>
				<div class="controls">
					<input type="text" name="<?php echo $this->get_field_name('ss_badge_widget_title'); ?>" id="<?php echo $this->get_field_id('ss_badge_widget_title'); ?>" value="<?php echo $ss_badge_widget_title;?>" />
				</div>
			</div>			
			<div class="control-group">
				<label class="control-label" for="<?php echo $this->get_field_name('ss_badge_widget_sellerId'); ?>"><?php echo __('eBay Username', 'netnovate-salessurvey');?></label>
				<div class="controls">
					<input type="text" data-target="<?php echo $this->get_field_id('ss_badge_conatiner'); ?>" class="ss-widget-sellerId" name="<?php echo $this->get_field_name('ss_badge_widget_sellerId'); ?>" id="<?php echo $this->get_field_id('ss_badge_widget_sellerId'); ?>" value="<?php echo $ss_badge_widget_sellerId;?>" />
				</div>
			</div>			
			<div class="control-group">
				<label class="control-label" for="<?php echo $this->get_field_name('ss_badge_widget_style'); ?>"><?php echo __('Style', 'netnovate-salessurvey');?></label>
				<div class="controls">
					<select class="ss-widget-style" data-target="<?php echo $this->get_field_id('ss_badge_conatiner'); ?>" name="<?php echo $this->get_field_name('ss_badge_widget_style'); ?>" id="<?php echo $this->get_field_id('ss_badge_widget_style'); ?>">
					<?php 
					$styleArray = array(
						1 => '250x40',
						2 => '200x200',
						3 => '468x75',
						4 => '250x250',
						5 => '220x220',
						6 => '155x30',
						7 => '155x155',
						8 => '160x230',
						9 => '190x150',
						10 => '250x250',
						11 => '300x250',
					);
					foreach($styleArray as $style => $description){
						echo '<option value="'. $style .'" '. (($style == $ss_badge_widget_style) ? 'selected' : '') .'>Style '. $style .' ('. $description .')</option>';
					}
					?>
					</select>
				</div>
			</div>
			<div>
				<label class="control-label"><?php echo __('Preview', 'netnovate-salessurvey');?></label>
				<div class="ss-badge-preview">
					<?php
					if(!empty($ss_badge_widget_sellerId)){
						?>
						<img src="https://www.salessurvey.de/Image/<?php echo $ss_badge_widget_style;?>/<?php echo $ss_badge_widget_sellerId;?>?locale=<?php echo get_locale();?>" style="max-width:100%">
						<?php
					}
					?>
				</div>
			</div>
			<br/>
		</div>
		<?php
	}
	
	public function update($new_instance, $old_instance) {

		$instance = array();
        $instance['ss_badge_widget_title'] = (!empty($new_instance['ss_badge_widget_title'])) ? sanitize_text_field($new_instance['ss_badge_widget_title']) : '';
        $instance['ss_badge_widget_sellerId'] =(!empty($new_instance['ss_badge_widget_sellerId'])) ? sanitize_text_field($new_instance['ss_badge_widget_sellerId']) : '';
        $instance['ss_badge_widget_style'] =(!empty($new_instance['ss_badge_widget_style'])) ? intval($new_instance['ss_badge_widget_style']) : '';

        return $instance;
    
    }
}



add_action('widgets_init', function(){
	
	register_widget('salessurvey_widget_auction');
	register_widget('salessurvey_widget_badge');
	
});