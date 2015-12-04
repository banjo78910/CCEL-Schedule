function displayButtons( pageNum ) {
    $( ".pagebutton" ).hide();
    $( ".pagebutton" ).removeClass( "pagebuttonactive" );

    $( "#pagebutton" + ( pageNum - 5 ) ).css( "display", "inline-block" );
    $( "#pagebutton" + ( pageNum - 4 ) ).css( "display", "inline-block" );
    $( "#pagebutton" + ( pageNum - 3 ) ).css( "display", "inline-block" );
    $( "#pagebutton" + ( pageNum - 2 ) ).css( "display", "inline-block" );
    $( "#pagebutton" + ( pageNum - 1 ) ).css( "display", "inline-block" );

    $( "#pagebutton" + pageNum ).addClass( "pagebuttonactive" );
    $( "#pagebutton" + pageNum ).show();

    $( "#pagebutton" + ( pageNum + 1 ) ).css( "display", "inline-block" );
    $( "#pagebutton" + ( pageNum + 2 ) ).css( "display", "inline-block" );
    $( "#pagebutton" + ( pageNum + 3 ) ).css( "display", "inline-block" );
    $( "#pagebutton" + ( pageNum + 4 ) ).css( "display", "inline-block" );
    $( "#pagebutton" + ( pageNum + 5 ) ).css( "display", "inline-block" );
}

function displayPage( pageNum ) {
    $( ".page" ).hide();
    console.log( "displayPage" );
    $( "#page" + pageNum ).slideDown();
    displayButtons( pageNum );
}