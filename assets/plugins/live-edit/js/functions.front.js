var live_edit;


(function($){
	
	live_edit = {
		
		o : {},
		
		$el : null,
		
		init : function(){
			
			// reference
			var _this = this;
			
			
			// add buttons
			this.add_buttons();
			
			
			// resizable
			$('#live-edit-panel').resizable({
				handles : 'e',
				start: function(event, ui){
					$('#live-edit-iframe-cover').css({'display':'block'});
				},
				resize: function(event, ui){
					$('body').css({
						'left' : $('#live-edit-panel').width()
					}, 250);
				},
				stop: function(event, ui){
					$('#live-edit-iframe-cover').css({'display':'none'});
					
					var data = {
						action	: 'live_edit_update_width',
						width	: $('#live-edit-panel').width(),
						nonce	: _this.o.nonce
					};
			
					
					$.post(_this.o.ajaxurl, data, function() {
						// do nothing
					});
					
					
					// update local variable
					_this.o.panel_width = data.panel_width;
				}
			});
			
			
			// panel height
			$('#live-edit-panel').css({
				top : $('html').css('margin-top')
			})
			
			// disable resizable
			$('#live-edit-panel').resizable("option", "disabled", true);
			
			
			// events
			$(document).on('click', '.live-edit-button', function( e ){
				
				e.preventDefault();
				
				_this.click( $(this) );
				
			});
			
		},
		
		add_buttons : function(){
			
			$('[data-live-edit-id]').each(function(){
			
				//vars
				var $el = $(this);
				
				
				// ignore if already setup
				if( $el.hasClass('live-edit-area') ) {
				
					return;
					
				}
				
				
				// add class
				$el.addClass('live-edit-area');
				
				
				// add button
				$el.prepend('<a href="#" class="live-edit-button">Edit</a>');
				
			});
			
		},
		
		click : function( $a ){
			
			// update $el
			this.$el = $a.closest('.live-edit-area');
			
			
			// update iframe
			this.update_panel();
			
			
			// open panel
			this.open_panel();
		},
		
		update_panel : function(){
			
			// vars
			var fields = this.$el.attr('data-live-edit-fields'),
				post_id = this.$el.attr('data-live-edit-post_id')
			
			
			// update iframe
			$('#live-edit-iframe').attr('src', this.o.panel_url + '&fields=' + fields + '&post_id=' + post_id + '&nonce=' + this.o.nonce);
			
		},
		
		open_panel : function(){
			
			// vars
			var width = this.o.panel_width,
				$panel = $('#live-edit-panel'),
				$iframe = $('#live-edit-iframe');
				
				
			// set ifram width
			$iframe.css({
				width : width
			});
			
			
			// animate
			$panel.animate({
				width : width
			}, 250, function(){
				
				$iframe.css({
					width : '100%'
				});
				
			});
			
			
			// move across body
			$('html').addClass('live-edit-active');
			$('body').animate({
				'left' : width
			}, 250);
			
			
			// enable resizable
			$panel.resizable("option", "disabled", false);
		
		},
		
		close_panel : function(){
			
			// vars
			var width = this.o.panel_width,
				$panel = $('#live-edit-panel'),
				$iframe = $('#live-edit-iframe');
				
				
			// set ifram width
			$iframe.css({
				width : width
			});
			
			
			// animate
			$panel.animate({
				width : 0
			}, 250 );
			
			
			// move across body
			$('body').animate({
				'left' : 0
			}, 250, function(){
				
				$('html').removeClass('live-edit-active');
				
				$iframe.attr('src', '');
				
			});
			
			
			// disable resizable
			$panel.resizable("option", "disabled", true);
			
		},
		
		sync : function(){
			
			// reference
			var _this = this;
			
			
			// add css to div
			this.$el.append('<div class="live-edit-loading"></div>');
			
			
			// vars
			var $loader = $('<div></div>');
			
			
			// load
			$loader.load( window.location + ' [data-live-edit-id="' + this.$el.attr('data-live-edit-id') + '"]', function(){
				
				_this.$el.removeClass('live-edit-area');
				
				_this.$el.html( $loader.children().html() );
				
				_this.add_buttons();
				
			});
			
							
		}
		
		
	};
	
	$(document).ready(function(){
		
		live_edit.init();
		
	});
	

})(jQuery);
