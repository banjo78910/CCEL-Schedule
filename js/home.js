$( document ).ready( function() {

	$( "#login" ).click( function() {
		console.log( "click" );
		$( "#login-form" ).slideToggle();
	} );

} );