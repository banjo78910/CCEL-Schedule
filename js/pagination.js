function displayButtons(pageNum) {
    $(".pagebutton").hide();
    $(".pagebutton").removeClass("pagebuttonactive");
    
    $("#pagebutton" + (pageNum - 5)).css("display", "inline-block");
    $("#pagebutton" + (pageNum - 4)).css("display", "inline-block");
    $("#pagebutton" + (pageNum - 3)).css("display", "inline-block");
    $("#pagebutton" + (pageNum - 2)).css("display", "inline-block");
    $("#pagebutton" + (pageNum - 1)).css("display", "inline-block");
    
    $("#pagebutton" + pageNum).addClass("pagebuttonactive");
    $("#pagebutton" + pageNum).show();
    
    $("#pagebutton" + (pageNum + 1)).css("display", "inline-block");
    $("#pagebutton" + (pageNum + 2)).css("display", "inline-block");
    $("#pagebutton" + (pageNum + 3)).css("display", "inline-block");
    $("#pagebutton" + (pageNum + 4)).css("display", "inline-block");
    $("#pagebutton" + (pageNum + 5)).css("display", "inline-block");
}

function displayPage(pageNum) {
    $("#searchresults").hide();
    $(".page").hide();
    $("#page" + pageNum).show();
    displayButtons(pageNum);
}

$(document).ready(function() {
    var numPages = $("#buttonholder .pagebutton").length;
    $(".page").hide();
    var currentPage = 1;
    displayPage(currentPage);
    
    $("#pagejumpform").on("submit", function(e) {
        e.preventDefault();
        currentPage = parseInt($("#pagejump").val());
        if (currentPage >=1 && currentPage <= numPages) {
            displayPage(currentPage);
        }
    });
    
    $(".pagebutton").click(function() {
        var id = this.id;
        currentPage = parseInt(id.substring(10));
        displayPage(currentPage);
    });
    
    $(".pagebutton").mouseenter(function() {
        $(this).addClass("pagebuttonhover");
        $(this).removeClass("pagebutton");
    });
    
    $(".pagebutton").mouseleave(function() {
        $(this).addClass("pagebutton");
        $(this).removeClass("pagebuttonhover");
    });
});
