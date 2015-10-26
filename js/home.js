$( document ).ready( function() {

	$( "#login" ).click( function() {
		loginForm();
	} );

} );

function loginForm() {
	bootbox.dialog( {
		title: "Login",
		message: '<div class="row">  ' +
			'<div class="col-md-12"> ' +
			'<form class="form-horizontal"> ' +
			'<div class="form-group"> ' +
			'<label class="col-md-4 control-label" for="name">Username</label> ' +
			'<div id="username-box" class="col-md-4"> ' +
			'<input id="username" name="name" type="text" class="form-control input-md"> ' +
			'</div> ' +
			'<label class="col-md-4" control-label" for="password">Password</label>' +
			'<div id="password-box" class="col-md-4">' +
			'<input id="password" name="password" type="password" class="form-control form input-md">' +
			'</div> ' +
			'</form> ' +
			'</div> ' +
			'</div>',
		buttons: {
			register: {
				label: "Register",
				className: "btn-default",
				callback: function() {
					registerForm();
				}
			},
			login: {
				label: "Login",
				className: "btn-primary",
				callback: function() {
					var username = $( "#username" ).val();
					var password = $( "#password" ).val();
					console.log( username + " " + password );
				}
			}
		}
	} );
}

function registerForm() {
	bootbox.dialog( {
		title: "Register",
		message: '<div class="row">  ' +
			'<div class="col-md-12"> ' +
			'<form class="form-horizontal"> ' +
			'<div class="form-group"> ' +
			'<label class="col-md-4 control-label" for="name">Username</label> ' +
			'<div id="username-box" class="col-md-4"> ' +
			'<input id="username" name="name" type="text" class="form-control input-md"> ' +
			'</div> ' +
			'<label class="col-md-4" control-label" for="password">Password</label>' +
			'<div id="password-box" class="col-md-4">' +
			'<input id="password" name="password" type="password" class="form-control form input-md">' +
			'</div> ' +
			'<label class="col-md-4" control-label" for="password-confirm">Confirm Password</label>' +
			'<div id="password-confirm-box" class="col-md-4">' +
			'<input id="password-confirm" name="password-confirm" type="password" class="form-control form input-md">' +
			'</div> ' +
			'<label class="col-md-4 control-label" for="email">Email Address</label> ' +
			'<div id="email-box" class="col-md-4"> ' +
			'<input id="email" name="email" type="text" class="form-control input-md"> ' +
			'</div> ' +
			'</form> ' +
			'</div> ' +
			'</div>',
		buttons: {
			register: {
				label: "Register",
				className: "btn-default",
				callback: function() {

				}
			},
		}
	} );

}