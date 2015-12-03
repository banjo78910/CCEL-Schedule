$( document ).ready( function() {
	role = $.cookie( 'role' );
	username = $.cookie( 'username' );
	console.log( "from page load: username " + username + ", role: " + role );
	if ( username ) {
		roleSwitcher( role );
		loginUIUpdate( username, role );
	} else {
		window.location.assign( "../index.html" );
	}

	var data = {
		'sender': 'YOUR FACE',
		'subject': 'RE: YOUR FACE',
		'message': 'YOUR FACE IS DUMB',
		'timestamp': '12-05-23'
	};

	$( "#message-list" ).html( messageBlockGen( data ) );

	$( "#compose" ).on( 'click', function() {
		bootbox.dialog( {
			title: "Compose Message",
			message: '<form> ' +
				'<div class="form-group"> ' +
				'<label class="col-md-4 control-label" for="sendto">Send To:</label> ' +
				'<input id="username" name="sendto" type="text" class="form-control input-md"> ' +
				'</div>' +
				'<label class="col-md-4 control-label" for="subject">Subject:</label> ' +
				'<input id="username" name="subject" type="text" class="form-control input-md"> ' +
				'</div>' +
				'<div class="form-group"> ' +
				'<label class="col-md-4" control-label" for="msg-body">Message:</label>' +
				'<textarea rows="6" name="msg-body" class="form-control col-md-4"></textarea>' +
				'</div> ' +
				'</form> ' +
				'</div> ',
			buttons: {
				register: {
					label: "Close",
					className: "btn-default",
					callback: function() {}
				},
				login: {
					label: "Send",
					className: "btn-primary",
					callback: function() {}
				}
			}
		} );
	} );

} );

function messageBlockGen( messageData ) {
	var block = '<a href="#" class="list-group-item">' +
		'<div class="checkbox">' +
		'<label>' +
		'<input type="checkbox">' +
		'</label>' +
		'</div>' +
		'<span class="glyphicon glyphicon-star-empty"></span><span class="name" style="min-width: 120px;' +
		'display: inline-block;">' + messageData.sender + '</span> <span class="">' + messageData.subject + '</span>' +
		'<span class="text-muted" style="font-size: 11px;">- ' + messageData.message + '</span> <span class="badge">' + messageData.timestamp + '</span> <span class="pull-right">' +
		'</a>';

	return block;
}