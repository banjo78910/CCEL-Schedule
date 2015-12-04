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
		'sender': 'UserS',
		'recepient': 'UserR',
		'subject': 'Some Text',
		'message': 'some message text',
		'timestamp': '12-05-23'
	};

	$( "#message-list" ).append( messageBlockGen( data ) );
	$( "#message-list" ).append( messageBlockGen( data ) );
	$( "#message-list" ).append( messageBlockGen( data ) );
	$( "#message-list" ).append( messageBlockGen( data ) );
	$( "#message-list" ).append( messageBlockGen( data ) );
	$( "#message-list" ).append( messageBlockGen( data ) );

	$( ".msg-block" ).on( 'click', function() {
		$( ".list-group-item[class*=active]" ).toggleClass( 'active' );
		$( event.currentTarget ).toggleClass( 'active' );
		$( '#msg-box' ).slideDown();
		displayMessageBody( $( ".list-group-item[class*=active]" ) );
	} );
	$( "#messages" ).html( '<a href="../">Home</a>' );

	$( ".list-group-item" ).on( 'click', function() {

	} );

	$( "#compose" ).on( 'click', function() {
		composeDialogGen( '', '' );
	} );

	$( ".btn-reply" ).on( 'click', function() {
		replyHandler( $( ".list-group-item[class*=active]" ) );
	} );

} );

function composeDialogGen( sendTo, subject ) {
	console.log( sendTo, subject );
	console.log( "called" );
	bootbox.dialog( {
		title: "Compose Message",
		message: '<form> ' +
			'<div class="form-group"> ' +
			'<label class="col-md-4 control-label" for="sendto">Send To:</label> ' +
			'<input id="username" name="sendto" type="text" value="' + sendTo + '" class="form-control input-md"> ' +
			'</div>' +
			'<label class="col-md-4 control-label" for="subject">Subject:</label> ' +
			'<input id="username" name="subject" type="text" value="' + subject + '"class="form-control input-md"> ' +
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
}

function messageBlockGen( messageData ) {
	var msgTrunc = messageData.message.substring( 0, 100 ) + '...';

	var block = '<a href="#" class="list-group-item msg-block">' +
		'<div class="checkbox">' +
		'<label>' +
		'<input type="checkbox">' +
		'</label>' +
		'</div>' +
		'<span class="glyphicon glyphicon-star-empty"></span><span class="msg-sender" style="min-width: 120px;' +
		'display: inline-block;">' + messageData.sender + '</span> <span class="msg-subject">' + messageData.subject + '</span>' +
		'<span class="text-muted msg-body-display" style="font-size: 11px;">- ' + msgTrunc + '</span> <span class="badge msg-timestamp">' + messageData.timestamp + '</span> <span class="pull-right">' +
		'</span> <span class="msg-body-full">' + messageData.message + '</span>' +
		'</a>';

	return block;
}

function replyHandler( target ) {
	var sendTo = target.find( ".msg-sender" ).html();
	var subject = 'RE: ' + target.find( ".msg-subject" ).html();
	console.log( sendTo, subject );
	composeDialogGen( sendTo, subject );
}

function displayMessageBody( msgToDisplay ) {
	var msgBody = msgToDisplay.find( ".msg-body-full" ).html();
	console.log( msgToDisplay.find( ".msg-sender" ).html() );
	$( ".msg-body" ).html( msgBody );
}