$( document ).ready( function() {
	role = $.cookie( 'role' );
	username = $.cookie( 'username' );
	console.log( "from messages page load: username " + username + ", role: " + role );
	if ( username ) {
		roleSwitcher( role );
		loginUIUpdate( username, role );
	} else {
		window.location.assign( "../index.html" );
	}
	console.log( "get messages" );
	var messages = getMessages();

	$( "#messages" ).html( '<a href="../">Home</a>' );

	$( "#compose" ).on( 'click', function() {
		composeDialogGen( 'student1', '' );
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
			'<input id="recipient" name="sendto" type="text" value="' + sendTo + '" class="form-control input-md"> ' +
			'</div>' +
			'<label class="col-md-4 control-label" for="subject">Subject:</label> ' +
			'<input id="subject" name="subject" type="text" value="' + subject + '"class="form-control input-md"> ' +
			'</div>' +
			'<div class="form-group"> ' +
			'<label class="col-md-4" control-label" for="msg-body">Message:</label>' +
			'<textarea id="message-content" rows="6" name="msg-body" class="form-control col-md-4"></textarea>' +
			'</div> ' +
			'</form> ' +
			'</div> ',
		buttons: {
			register: {
				label: "Close",
				className: "btn-default",
				callback: function() {

				}
			},
			login: {
				label: "Send",
				className: "btn-primary",
				callback: function() {
					var recipient = $( "#recipient" ).val();
					var subject = $( "#subject" ).val();
					var messageContent = $( "#message-content" ).val();

					var msgJSON = {
						'recipient': recipient,
						'subject': subject,
						'message': messageContent
					};
					var msgJSONString = JSON.stringify( msgJSON );
					console.log( msgJSONString );
					sendMessage( msgJSONString );
				}
			}
		}
	} );
}

function messageBlockGen( messageData ) {
	var msgTrunc = messageData.message.substring( 0, 30 ) + '...';
	console.log( msgTrunc );

	var block = '<a href="#" class="list-group-item msg-block">' +
		'<span class="msg-sender" style="min-width: 120px;' +
		'display: inline-block;">' + messageData.senderID + '</span> <span class="msg-subject">' + messageData.subject + '</span>' +
		'<span class="text-muted msg-body-display" style="font-size: 11px;">- ' + msgTrunc + '</span>' +
		'<span class="msg-body-full">' + messageData.message + '</span>' + '<span class="msg-id' > +messageData.messageID + '</span>' +
		'</a>';

	return block;
}

function getMessages() {
	var messagesJSON;
	$.ajax( {
		url: '/php/mediator.php',
		type: 'get',
		data: {
			'function': 'getJsonMessages'
		},
		success: function( data ) {
			messagesJSON = JSON.parse( data );
			for ( var i = 0; i < messagesJSON.length; i++ ) {
				console.log( messagesJSON[ i ] );
				$( "#message-list" ).append( messageBlockGen( messagesJSON[ i ] ) );
			}

			$( ".msg-block" ).on( 'click', function() {
				console.log( 'click' );
				$( ".list-group-item[class*=active]" ).toggleClass( 'active' );
				$( event.currentTarget ).toggleClass( 'active' );
				$( '#msg-box' ).slideDown();
				displayMessageBody( $( ".list-group-item[class*=active]" ) );
			} );
		},
		error: function( xhr, desc, err ) {
			console.log( err );
		}
	} );
}

function sendMessage( messageData ) {
	$.ajax( {
		url: '/php/mediator.php',
		type: 'get',
		data: {
			'function': 'sendJsonMessage',
			'jsonString': messageData
		},
		success: function( data ) {
			console.log( data );
		},
		error: function( xhr, desc, err ) {

		}
	} );
}

function deleteMessage( msgToDelete ) {
	var msgID = msgToDisplay.find( ".msg-id" ).html();

	$.ajax( {
		url: '/php/mediator.php',
		type: 'get',
		data: {
			'function': 'deleteMessage',
			'jsonString': messageData
		},
		success: function( data ) {
			console.log( data );
		},
		error: function( xhr, desc, err ) {

		}
	} );
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