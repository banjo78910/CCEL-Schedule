var username;
var role;
var roleData = {
	'addSession': '',
	'deleteSession': '',
	'listSessions': '',
	'listSessionsManage': '',
	'sessionString': '',
	'sessionStringManage': ''

};

$( document ).ready( function() {
	role = $.cookie( 'role' );
	username = $.cookie( 'username' );
	console.log( "from home page load: username " + username + ", role: " + role );
	if ( username ) {
		roleSwitcher( role );
		loginUIUpdate( username, role );
	}

	$.ajax( {
		url: '/php/mediator.php',
		type: 'get',
		data: {
			'function': 'displayAllSessions'
		},
		success: function( data ) {

			console.log( "get sessions" );
			$( "#sessions" ).html( data );
			$( '.btn-search' ).on( 'click', function() {
				var searchResultsString = '';
				console.log( "search clicked" );
				$( ".list-group-item" ).each( function() {
					if ( $( this ).html().indexOf( $( ".search-box" ).val() ) > -1 ) {
						searchResultsString += "<br>" + $( this ).html() + "<br>";
						console.log( searchResultsString );
					}
				} );
				bootbox.dialog( {
					title: "Search Results",
					message: searchResultsString,
					buttons: {
						close: {
							label: "Close",
							className: "btn-primary",
							callback: function() {

							}
						}
					}

				} );

			} );
			$( ".btn-session" ).on( "click", function() {
				var func;

				var e = $( event.currentTarget );

				var status = e.hasClass( 'btn-success' );
				var sessionid = e.attr( 'id' );

				console.log( sessionid );
				func = status ? roleData.addSession : roleData.deleteSession;

				$.ajax( {
					url: '/php/mediator.php',
					data: {
						'function': func,
						'sessionID': sessionid
					},
					success( data ) {
						console.log( e );
						console.log( func + " succeeded" );

						e.addClass( status ? 'btn-danger' : 'btn-success' );
						e.removeClass( status ? 'btn-success' : 'btn-danger' );

						e.removeClass( status ? 'delete-from-sessions' : 'add-to-sessions' );
						e.addClass( status ? 'add-to-sessions' : 'remove-from-sessions' );

						e.find( 'span' ).removeClass( status ? 'glyphicon-plus' : 'glyphicon-minus' );
						e.find( 'span' ).addClass( status ? 'glyphicon-minus' : 'glyphicon-plus' );
					}

				} );
			} );

			pageDivider();

			$( ".btn-cancel" ).on( "click", function() {
				var e = $( event.currentTarget );
				var sessionid = e.attr( 'id' );
				console.log( sessionid );
				cancelSessions( sessionid );
			} );

		},
		error: function( xhr, desc, err ) {
			console.log( xhr + " " + desc + " " + err );
		}
	} );

	$( "#login-student" ).click( function() {
		loginForm( "Student" );
	} );

} );

function roleSwitcher( role ) {
	switch ( role ) {
		case "attender":
			roleData.sessionString = "Sessions I Attend";
			roleData.addSession = 'willAttend';
			roleData.deleteSession = 'cancelAttend';
			roleData.listSessions = 'displayAttendingSessions';
			break;
		case "tutor":
			roleData.sessionString = "Sessions I Tutor";
			roleData.addSession = 'signUpToTutor';
			roleData.deleteSession = 'cancelTutor';
			roleData.listSessions = 'displaySessions';
			break;
		case "siteLeader":
			roleData.sessionString = "Sessions I Tutor";
			roleData.sessionStringManage = "Sessions I Manage"
			roleData.addSession = 'signUpToTutor';
			roleData.deleteSession = 'cancelTutor';
			roleData.listSessions = 'displaySessions';
			roleData.listSessionsManage = 'displaySiteSessions';

			break;
	}
}

function loginForm() {
	bootbox.dialog( {
		title: "Log in to your Account",
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
					registerForm();
				}
			},
			login: {
				label: "Login",
				className: "btn-primary",
				callback: function() {
					username = $( "#username" ).val();
					var password = $( "#password" ).val();
					console.log( username + password );
					loginHandler( username, password );
					window.location.reload();
				}
			}
		}
	} );
}

function registerForm() {
	bootbox.dialog( {
		title: "Register",
		message: '<div class="col-md-12">' +
			'<form>' +
			'<div class="form-group">' +
			'<label for="name">Username</label>' +
			'<input id="username" name="name" type="text" class="form-control input-md">' +
			'</div>' +
			'<div class="form-group">' +
			'<label for="fname">First Name</label>' +
			'<input id="fname" name="fname" type="text" class="form-control input-md">' +
			'</div>' +
			'<div class="form-group">' +
			'<label for="lname">Last Name</label>' +
			'<input idl="lname" name="lname" type="text" class="form-control input-md">' +
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
					var username = $( "#username" ).val();
					var fname = $( "#fname" ).val();
					var lname = $( "lname" ).val();
					var password = $( "#password" ).val();
					var email = $( "#email" ).val();

					var regData = {
						'username': username,
						'firstName': fname,
						'lastName': lname,
						'password': password,
						'email': email
					};
					$.ajax( {
						url: '/php/mediator.php',
						data: {
							'function': 'registerJsonUser',
							'jsonString': JSON.stringify( regData )
						},
						success( data ) {
							window.location.reload();
						}

					} );

				}
			},
		}
	} );
	return null;

}

function loginHandler( username, password ) {
	var session;

	$.ajax( {
		url: '/php/mediator.php',
		type: 'post',
		data: {
			'username': username,
			'password': password
		},
		success: function( data ) {
			role = $.cookie( 'role' );
			console.log( role );

			return role;
		},
		error: function( xhr, desc, err ) {
			console.log( xhr + " " + desc + " " + err );
			return xhr;
		}
	} );

}

function loginUIUpdate( username, role ) {

	$( "#navbar-right" ).html(
		'<div class="btn-group navbar-btn">' +
		( ( role == 'siteLeader' ) ? '<button type="button" class = "btn btn-create btn-default">Create Session</button>' : '' ) +
		'<button type="button" class="btn btn-danger">' + username + '</button>' +
		'<button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' +
		'<span class="caret"></span>' +
		'<span class="sr-only">Toggle Dropdown</span>' +
		'</button>' +
		'<ul class="dropdown-menu">' +
		'<li id="list-sessions"><a href="#">' + roleData.sessionString + '</a></li>' +
		'<li id="list-sessions-manage"><a href="#">' + roleData.sessionStringManage + '</a></li>' +
		'<li id="messages"><a href="/html/messagecenter.html">Message Center</a></li>' +
		'<li role"separator" class="divider"></li>' +
		'<li id="account"><a href="#">My Account</a></li>' +
		'<li id="logout"><a href="#">Log Out</a></li>' +
		'</ul>' +
		'</div>'
	);

	if ( roleData.sessionStringManage.length > 0 ) {
		$( "#list-sessions-manage" ).slideDown();
	}

	$( '.btn-group' ).on( 'click', '#list-sessions', function() {

		userSessionList( username, role );
	} );

	$( '.btn-group' ).on( 'click', '#list-sessions-manage', function() {

		siteLeaderSessions( username, role );
	} );

	$( '.btn-group' ).on( 'click', '#logout', function() {
		console.log( "logout clicked" );
		$.ajax( {
			url: '/php/mediator.php',
			type: 'get',
			data: {
				'function': 'logout'
			},
			success: function( data ) {
				console.log( data );
				console.log( "Logout" );
				window.location.reload();
			},
			error: function( xhr, desc, err ) {
				console.log( xhr + " " + desc + " " + err );
				return xhr;
			}
		} );

	} );

	$( '.btn-group' ).on( 'click', '.btn-create', function() {
		bootbox.dialog( {
			title: 'Create Session',
			message: '<form> ' +
				'<div class="form-group"> ' +
				'<label class="col-md-4 control-label" for="date">Date</label> ' +
				'<input id="date" name="date" type="text" value="Nov 10" class="form-control input-md"> ' +
				'</div>' +
				'<div class="form-group"> ' +
				'<label class="col-md-4" control-label" for="time">Time</label>' +
				'<input id="time" name="time" type="text" value="8:00" class="form-control form input-md">' +
				'</div> ' +
				'<div class="form-group"> ' +
				'<label class="col-md-4 control-label" for="subject">Subject</label> ' +
				'<input id="new-subject" name="subject" type="text" value="History" class="form-control input-md"> ' +
				'</div>' +
				'<div class="form-group"> ' +
				'<label class="col-md-4" control-label" for="grade">Grade Level</label>' +
				'<input id="grade" name="grade" type="text" value="9" class="form-control form input-md">' +
				'</div> ' +
				'</form> ' +
				'</div> ',
			buttons: {
				close: {
					label: "Create",
					className: "btn-primary",
					callback: function() {
						var date = $( "#date" ).val();
						var time = $( "#time" ).val();
						var newSubject = $( "#new-subject" ).val();
						var grade = $( "#grade" ).val();
						var newSessionInfo = {
							'date': date,
							'time': time,
							'subject': newSubject,
							'gradeLevel': grade
						};

						var newSessionInfoJSON = JSON.stringify( newSessionInfo );
						console.log( "json: " + newSessionInfoJSON );
						$.ajax( {
							url: '/php/mediator.php',
							type: 'get',
							data: {
								'function': 'createJsonSession',
								'jsonString': newSessionInfoJSON
							},
							success: function( data ) {
								console.log( data );
								console.log( "new session created" );
								window.location.reload();
							},
							error: function( xhr, desc, err ) {
								console.log( xhr + " " + desc + " " + err );
								return xhr;
							}
						} );

					}
				}
			}

		} );
	} )
}

function userSessionList( username, role ) {

	var sessionDataString;
	$.ajax( {
		url: '/php/mediator.php',
		type: 'get',
		data: {
			'function': roleData.listSessions
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

}

function siteLeaderSessions( username, role ) {
	$.ajax( {
		url: '/php/mediator.php',
		type: 'get',
		data: {
			'function': roleData.listSessionsManage
		},
		success: function( data ) {
			console.log( "data: " + data );
			sessionDataString = data + "";
			bootbox.dialog( {
				title: "Sessions at " + username + "'s Site",
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
}

function cancelSessions( sessionID ) {
	console.log( "call function" );
	$.ajax( {
		url: '/php/mediator.php',
		type: 'get',
		data: {
			'function': 'cancelSession',
			'sessionID': sessionID
		},
		success: function( data ) {
			console.log( "cancel session: " + data );
		},
		error: function( xhr, desc, err ) {
			console.log( xhr + " " + desc + " " + err );
			return xhr;
		}
	} );
}

function pageDivider() {
	var numPages = $( "#buttonholder .pagebutton" ).length;
	// $( ".page" ).hide();
	var currentPage = 1;
	displayPage( currentPage );

	$( ".pagebutton" ).click( function() {
		var id = this.id;
		currentPage = parseInt( id.substring( 10 ) );
		displayPage( currentPage );
	} );

	$( ".pagebutton" ).mouseenter( function() {
		$( this ).addClass( "pagebuttonhover" );
		$( this ).removeClass( "pagebutton" );
	} );

	$( ".pagebutton" ).mouseleave( function() {
		$( this ).addClass( "pagebutton" );
		$( this ).removeClass( "pagebuttonhover" );
	} );
}

function searchSessions() {
	$( "#searchform" ).on( "submit", function( e ) {

	} );
}