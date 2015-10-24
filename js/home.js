$( document ).ready( function() {

	$( "#login" ).click( function() {
		bootbox.dialog( {
			title: "Login",
			message: '<div class="row">  ' +
				'<div class="col-md-12"> ' +
				'<form class="form-horizontal"> ' +
				'<div class="form-group"> ' +
				'<label class="col-md-4 control-label" for="name">Username</label> ' +
				'<div class="col-md-4"> ' +
				'<input id="name" name="name" type="text" class="form-control input-md"> ' +
				'</div> ' +
				'<label class="col-md-4" control-label" for="password">Password</label>' +
				'<div class="col-md-4">' +
				'<input id="password" name="password" type="password" class="form-control form input-md">' +
				'</div> ' +
				'</div> </div>' +
				'</form> </div>  </div>',
			buttons: {
				login: {
					label: "Login",
					className: "btn-primary",
					callback: function() {

					}
				}
			}
		} );
	} );

} );