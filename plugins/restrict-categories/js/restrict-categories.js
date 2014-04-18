jQuery(document).ready(function($) {
	
	$( '.select-all' ).click( function( e ) {
		e.preventDefault();
		
		var active_panel = $( this ).closest( 'div' ).find( '.tabs-panel-active' ).attr( 'id' ),
			items = $( '#' + active_panel + ' input[type="checkbox"]:visible' );
		
		if ( items.length === items.filter( ':checked' ).length )
			items.removeAttr( 'checked' );
		else
			items.prop( 'checked', true );
	});
	
});