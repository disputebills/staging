(function($){

	var cnt = 0;

	// Common functions
	function isNumber(n)
	{
	   return n == parseFloat(n);
	}
	function isOdd(n)
	{
	   return isNumber(n) && (Math.abs(n) % 2 == 1);
	}


	function initialize_field( $el ) {

		cnt++;
		if( isOdd( cnt ) ){
			var content = $el.nextUntil('.field_type-row');

			var col_num = $el.find('.acf-row').data('col_num');

			$(content).wrapAll( '<div class="acf-row-wrap row-col-'+col_num+'"></div>' );
		}

	}


	if( typeof acf.add_action !== 'undefined' ) {

		/*
		*  ready append (ACF5)
		*
		*  These are 2 events which are fired during the page load
		*  ready = on page load similar to $(document).ready()
		*  append = on new DOM elements appended via repeater field
		*
		*  @type	event
		*  @date	20/07/13
		*
		*  @param	$el (jQuery selection) the jQuery element which contains the ACF fields
		*  @return	n/a
		*/

		acf.add_action('ready append', function( $el ){

			// search $el for fields of type 'row'
				console.log($el);
			acf.get_fields({ type : 'row'}, $el).each(function(){

				initialize_field( $(this) );

			});

		});


	} else {


		/*
		*  acf/setup_fields (ACF4)
		*
		*  This event is triggered when ACF adds any new elements to the DOM.
		*
		*  @type	function
		*  @since	1.0.0
		*  @date	01/01/12
		*
		*  @param	event		e: an event object. This can be ignored
		*  @param	Element		postbox: An element which contains the new HTML
		*
		*  @return	n/a
		*/

		$(document).live('acf/setup_fields', function(e, postbox){

			$(postbox).find('.field[data-field_type="row"]').each(function(){

				initialize_field( $(this) );

			});

		});


	}


})(jQuery);
