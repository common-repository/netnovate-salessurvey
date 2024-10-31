	
function ss_updatePreview(){
	
	var ss_widget = jQuery('.ss_template_active').attr("data-widget");
	
	if(ss_widget == 'shop'){
		jQuery('#ss_setting_theme').show();	
		jQuery('#ss_setting_tilesize').hide();	
	} else {
		jQuery('#ss_setting_theme').hide();	
		jQuery('#ss_setting_tilesize').show();	
	}
	
	if(ss_widget == 'shop' || ss_widget == 'auctionsgallery'){
		jQuery('#ss_setting_frame').hide();	
	} else {
		jQuery('#ss_setting_frame').show();		
	}
	
	if(ss_widget == 'auctionsgallery'){
		jQuery('#ss_setting_frame_color').hide();	
	} else {
		jQuery('#ss_setting_frame_color').show();
	}
	
	
	var apiUri = "https://www.salessurvey.de/##SCRIPT##.js?sellerId=##SELLERID##&globalId=##GLOBALID##&locale=##LOCALE##&theme=##THEME##&sortOrder=##SORTORDER##&tabs=##TABS##&tileSize=##TILESIZE##&bgColor=##BGCOLOR##&responsive=1&height=##HEIGHT##&items=##ITEMS##&fields%5Bimage%5D=1&fields%5Bbids%5D=1&fields%5Bprice%5D=1&fields%5BremainingTime%5D=1&fields%5BfeedbackIcon%5D=1&fields%5BfeedbackSeller%5D=1&fields%5BfeedbackTime%5D=1&responseType=json&source=wp-admin";
	var targetScript = jQuery('.ss_template_active').attr('data-widget');
	
	apiUri = apiUri.replace('##SCRIPT##', encodeURIComponent(targetScript) + '-widget');
	apiUri = apiUri.replace('##SELLERID##', encodeURIComponent(jQuery('#ss_ebay_sellerId').val()));
	apiUri = apiUri.replace('##GLOBALID##', encodeURIComponent(jQuery('#ss_ebay_globalId').val()));
	apiUri = apiUri.replace('##LOCALE##', backend.locale);
	apiUri = apiUri.replace('##SORTORDER##', encodeURIComponent(jQuery('#ss_ebay_sort_order').val()));
	apiUri = apiUri.replace('##TABS##', encodeURIComponent(jQuery('#ss_ebay_shop_theme').val()));
	apiUri = apiUri.replace('##TILESIZE##', encodeURIComponent(jQuery('#ss_ebay_tile_size').val()));
	apiUri = apiUri.replace('##BGCOLOR##', encodeURIComponent(jQuery('#ss_ebay_frame_color').val()));
	apiUri = apiUri.replace('##HEIGHT##', encodeURIComponent(jQuery('#ss_ebay_shop_height').val()));
	apiUri = apiUri.replace('##ITEMS##', encodeURIComponent(jQuery('#ss_ebay_num_items').val()));
	apiUri = apiUri.replace('##THEME##', ((jQuery('#ss_ebay_frame').prop('checked')) ? 'doubleborder' : 'noframe'));
	
	if(jQuery('#ss_ebay_sellerId').val().length > 1){
	
		jQuery.ajax({
			type: "GET",
			url: apiUri,
			dataType: "json",
			success: function(data){				
				
				jQuery("#ss_preview").html(data.html);
				
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
			}
		});
	
	} else {
		jQuery("#ss_preview").html(backend.noEbayNameSet);
	}
	
}
	
jQuery(document).ready(function(){
	
	jQuery('div.ss-tooltip').hover(function(){
		jQuery('<div id="ss-tooltip-active"></div>').text(jQuery(this).data('title')).appendTo('body').fadeIn('slow');
	}, function() {
		jQuery('#ss-tooltip-active').remove();
	})
	.mousemove(function(e) {
		var mousex = e.pageX + 15;
		var mousey = e.pageY ;
		jQuery('#ss-tooltip-active').css({ top: mousey, left: mousex });
	});

	jQuery(".ss-refresh-preview, #ss_meta_content_badges_tab").click(function(){
		
		ss_updatePreview();
		
		var sellerId = jQuery('#ss_ebay_sellerId').val();
		
		if(sellerId.length > 2){		
			
			jQuery('.ss-feedback-badge-image').each(function(){
				jQuery(this).attr('src', 'https://www.salessurvey.de/Image/'+ jQuery(this).attr('data-image') +'/'+ sellerId + '?locale=' + jQuery(this).attr('data-locale'));
			});
			
			jQuery('#ss_meta_content_badges_active').show();
			jQuery('#ss_meta_content_badges_inactive').hide();	
			
		} else {
			jQuery('#ss_meta_content_badges_active').hide();
			jQuery('#ss_meta_content_badges_inactive').show();	
		}
		
	});
	
	jQuery("#ss_meta_options .nav-tab").click(function(){
		
		jQuery("#ss_meta_options .nav-tab").removeClass("nav-tab-active");
		jQuery(this).addClass("nav-tab-active");
		
		jQuery("#ss_meta_options .ss_meta_content").hide();
		
		var ss_target = jQuery(this).attr("data-target");
		jQuery("#" + ss_target).show();
		
		
	});

	jQuery("#ss_meta_content_auctions .ss_template").click(function(){
						
		var ss_widget = jQuery(this).attr("data-widget");
		jQuery('#ss_ebay_template').val(ss_widget);
		jQuery(".ss_template").removeClass("ss_template_active");
		jQuery(this).addClass("ss_template_active");
				
		ss_updatePreview();					
	});
	
	jQuery("#ss_meta_content_auctions .controls input").change(function(){						
		ss_updatePreview();					
	});	
	
	jQuery("#ss_meta_content_auctions .controls select").change(function(){						
		ss_updatePreview();					
	});

	
	jQuery("#ss_ebay_frame_color").spectrum({
		showAlpha: false,
		showPalette: true,
		showInput: true,
		preferredFormat: "hex",
		palette: [
			["#000","#444","#666","#999","#ccc","#eee","#f3f3f3","#fff"],
			["#f00","#f90","#ff0","#0f0","#0ff","#00f","#90f","#f0f"],
			["#f4cccc","#fce5cd","#fff2cc","#d9ead3","#d0e0e3","#cfe2f3","#d9d2e9","#ead1dc"],
			["#ea9999","#f9cb9c","#ffe599","#b6d7a8","#a2c4c9","#9fc5e8","#b4a7d6","#d5a6bd"],
			["#e06666","#f6b26b","#ffd966","#93c47d","#76a5af","#6fa8dc","#8e7cc3","#c27ba0"],
			["#c00","#e69138","#f1c232","#6aa84f","#45818e","#3d85c6","#674ea7","#a64d79"],
			["#900","#b45f06","#bf9000","#38761d","#134f5c","#0b5394","#351c75","#741b47"],
			["#600","#783f04","#7f6000","#274e13","#0c343d","#073763","#20124d","#4c1130"]
		],
		change: function(color) {
			ss_updatePreview();
		},
		move: function(color) {
			ss_updatePreview();
		}
	});
	
	jQuery('body').on('change', '.ss-widget-style, .ss-widget-sellerId', function(){			
	
		var previewUri = 'https://www.salessurvey.de/Image/##STYLE##/##SELLERID##?locale=' + backend.locale;
		
		var ssTarget = jQuery(this).data('target');	
		var ssStyle = jQuery('#'+ ssTarget +' .ss-widget-style').val();	
		var ssSellerId = jQuery('#'+ ssTarget +' .ss-widget-sellerId').val();	

		previewUri = previewUri.replace('##STYLE##', ssStyle).replace('##SELLERID##', ssSellerId);
		jQuery('#'+ ssTarget +' .ss-badge-preview img').attr('src' , previewUri);	
		
	});


	if(jQuery("#ss_preview").length){
		ss_updatePreview();
	}
	
	var clipboard = new ClipboardJS('.btn-clipboard');

});