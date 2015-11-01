QUnit.test( "Basic Unit Tests", function( assert ) {
	assert.ok( loginHandler( "", "" ), "loginHandler" );
	assert.ok( loginHandler() );
	assert.ok( loginForm( "student" ) );
	assert.ok( loginForm( "" ) );
	assert.ok( registerForm( "student" ) );
	assert.ok( registerForm( "" ) );
} );