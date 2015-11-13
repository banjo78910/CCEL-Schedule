var username;
$( document ).ready( function() {

	$.ajax( {
		url: '/php/mediator.php',
		type: 'get',
		data: {
			'function': 'displayAll'
		},
		success: function( data ) {
			$( "#sessions" ).html( data );
			$( "#sessions" ).on( "click", ".btn-add-to-sessions", function() {
				var sessionid = $( event.target ).attr( 'id' );
				console.log( sessionid );
				$.ajax( {
					url: '/php/mediator.php',
					data: {
						'function': 'indicateWillAttend',
						'sessionID': sessionid
					},
					success( data ) {
						console.log( "Will attend succeeded" );
					}

				} );
			} );
		},
		error: function( xhr, desc, err ) {
			console.log( xhr + " " + desc + " " + err );
		}
	} );

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
			'<div class="accountType form-group" >' +
			'<div class="radio radio-inline">' +
			'<label><input type="radio" name="accType" value="Student"> Student </label>' +
			'</div>' +
			'<div class="radio radio-inline">' +
			'<label><input type="radio" name="accType" value="Tutor"> Tutor </label>' +
			'</div>' +
			'<div class="radio radio-inline">' +
			'<label><input type="radio" name="accType" value="Admin"> Admin </label>' +
			'</div>' +
			'</div>' +

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
					username = $( "#username" ).val();
					var password = $( "#password" ).val();
					var accountType = $( "input[name=accType]:checked" ).val();
					console.log( username + password + accountType );
					loginHandler( username, password, accountType );
					return ( username, password, accountType );
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
	return loginType;

}

function loginHandler( username, password, accountType ) {
	var session;
	var dropdown;
	switch ( accountType ) {
		case "Student":
			session = "Sessions I Attend";
			dropdown = "<button id='attending' class='btn dropdown-toggle pull-right' type='button' data-toggle='dropdown'>Attending<span class='caret'></span></button>";
			break;
		case "Tutor":
			session = "Sessions I Tutor";
			dropdown = "<button id='attending' class='btn dropdown-toggle pull-right' type='button' data-toggle='dropdown'>Students Attending<span class='caret'></span></button>";
			break;
		case "Admin":
			session = "Sessions I Manage";
			break;
	}

	$( "#navbar-right" ).html(
		'<div class="btn-group navbar-btn">' +
		'<button type="button" class="btn btn-primary" id="my-sessions"> My Sessions </button>' +
		'</div>' +
		'<div class="btn-group navbar-btn">' +
		'<button type="button" class="btn btn-danger">' + username + '</button>' +
		'<button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' +
		'<span class="caret"></span>' +
		'<span class="sr-only">Toggle Dropdown</span>' +
		'</button>' +
		'<ul class="dropdown-menu">' +
		'<li id="sessions"><a href="#">' + session + '</a></li>' +
		'<li id="messages"><a href="#">Message Center</a></li>' +
		'<li role"separator" class="divider"></li>' +
		'<li id="account"><a href="#">My Account</a></li>' +
		'<li id="logout"><a href="#">Log Out</a></li>' +
		'</ul>' +
		'</div>'
	);
	$( '.btn-group' ).on( 'click', '#sessions', function() {

		userSessionList( username, accountType, dropdown );
	} );

	$( '.btn-group' ).on( 'click', '#logout', function() {

		$.ajax( {
			url: '/php/mediator.php',
			type: 'get',
			data: {
				'function': 'logout'
			},
			success: function( data ) {
				return data;
			},
			error: function( xhr, desc, err ) {
				console.log( xhr + " " + desc + " " + err );
				return xhr;
			}
		} );
	} );

	$.ajax( {
		url: '/php/mediator.php',
		type: 'post',
		data: {
			'username': username,
			'password': password
		},
		success: function( data ) {
			return data;
		},
		error: function( xhr, desc, err ) {
			console.log( xhr + " " + desc + " " + err );
			return xhr;
		}
	} );
}

function userSessionList( username, accountType, dropdown ) {
	var sessionDataString;
	$.ajax( {
		url: '/php/mediator.php',
		type: 'get',
		data: {
			'function': 'retrieveWillAttend'
		},
		success: function( data ) {
			console.log( "data: " + data );
			sessionDataString = data + "";
			bootbox.dialog( {
				title: username + "'s Sessions",
				message: sessionDataString,
				buttons: {
					close: {
						label: "Close",
						className: "btn-primary",
						callback: function() {

						}
					}
				}

			} );
		},
		error: function( xhr, desc, err ) {
			console.log( xhr + " " + desc + " " + err );
			return xhr;
		}
	} );
	console.log( sessionDataString );

	return username;
}