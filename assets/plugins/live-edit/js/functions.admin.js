(function($){

	$(document).on('click', '.button-close', function( e ){
		
		e.preventDefault();
		
		
		// validate parent
		if( !parent || !parent.live_edit ) {
		
			return;
			
		}
		
		
		// update the div
		parent.live_edit.close_panel();
		
	});
	
	$(document).on('submit', '#post', function( e ){
		
		$('.form-title .spinner').show();
		
	});	
	
	$( document ).ajaxComplete(function( event, xhr, settings ) {
		
		$('.form-title .spinner').hide();
		
	});
	

})(jQuery);
