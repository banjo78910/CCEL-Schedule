$( document ).ready( function() {

	$( "#login-student" ).click( function() {
		loginForm( "Student" );
	} );
	$( "#login-tutor" ).click( function() {
		loginForm( "Tutor" );
	} );

} );

function loginForm( loginType ) {
	bootbox.dialog( {
		title: loginType + " Login",
		message: '<form class="form-inline"> ' +
			'<div class="form-group"> ' +
			'<label class="col-md-4 control-label" for="name">Username</label> ' +
			'<input id="username" name="name" type="text" class="form-control input-md"> ' +
			'</div>' +
			'<div class="form-group"> ' +
			'<label class="col-md-4" control-label" for="password">Password</label>' +
			'<input id="password" name="password" type="password" class="form-control form input-md">' +
			'</div> ' +
			'</form> ' +
			'</div> ',
		buttons: {
			register: {
				label: "Register",
				className: "btn-default",
				callback: function() {
					registerForm( loginType );
				}
			},
			login: {
				label: "Login",
				className: "btn-primary",
				callback: function() {
					var username = $( "#username" ).val();
					var password = $( "#password" ).val();
					console.log( username + " " + password );
					loginHandler( $( "#username" ).val() );
				}
			}
		}
	} );
}

function registerForm( loginType ) {
	bootbox.dialog( {
		title: "Register",
		message: '<div class="col-md-12">' +
			'<form>' +
			'<div class="form-group">' +
			'<label for="name">Username</label>' +
			'<input id="username" name="name" type="text" class="form-control input-md">' +
			'</div>' +
			'<div class="form-group">' +
			'<label for="password ">Password</label>' +
			'<input id="password" name="password" type="password" class="form-control form input-md ">' +
			'</div>' +
			'<div class="form-group">' +
			'<label for="password-confirm">Confirm Password</label>' +
			'<input id="password-confirm" name="password-confirm" type="password" class="form-control form input-md">' +
			'</div>' +
			'<div class="form-group">' +
			'<label for="email">Email Address</label>' +
			'<input id="email" name="email" type="text" class="form-control input-md">' +
			'</div>' +
			'</form>' +
			'</div>',
		buttons: {
			register: {
				label: "Register",
				className: "btn-default",
				callback: function() {
					console.log( loginType );

				}
			},
		}
	} );

}

function loginHandler( username ) {
	$( "#navbar-right" ).html(
		'<div class="btn-group navbar-btn">' +
		'<button type="button" class="btn btn-danger">' + username + +'</button>' +
		'<button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' +
		'<span class="caret"></span>' +
		'<span class="sr-only">Toggle Dropdown</span>' +
		'</button>' +
		'<ul class="dropdown-menu">' +
		'<li><a href="#">My Account</a></li>' +
		'<li><a href="#">Message Center</a></li>' +
		'</ul>' +
		'</div>'
	);
}