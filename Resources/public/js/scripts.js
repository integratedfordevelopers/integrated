//Substitute SVG with PNG for non-svg browsers
if (!Modernizr.svg) {
	$('.svg-img').each(function(){
		$(this).attr('src', ($(this).attr('data-png-src')));
	});
}

$(document).ready(function(){
	
	//Placeholders Fix
	$('input, textarea').placeholder();

	$('.search-form .form-control').focus(function(){
        if ($(window).width() >= 768) {
            $(this).closest('.nav-form-inner').toggleClass('full-width');
        }
    });
    $( ".search-form .form-control" ).blur(function() {
		if ($(window).width() >= 768) {
            $(this).closest('.nav-form-inner').toggleClass('full-width');
        }
	});

	//Expanded hidden watch list items
	$( ".list-watch .watch-item-trigger" ).click(function(event) {
		event.preventDefault();
		if ($(this).hasClass('active')) {
			$(this).closest('.list-watch').find('.hidden-item').css("display" , "none");
			$(this).removeClass('active');
		} else {
			$(this).closest('.list-watch').find('.hidden-item').css("display" , "inline-block");
			$(this).addClass('active');
		}
	});

	//Expanded hidden text holder items
	$( ".hidden-text-holder .link-more" ).click(function(event) {
		event.preventDefault();
		if ($(this).hasClass('active')) {
			$(this).closest('.hidden-text-holder').find('.hidden-item').css("display" , "none");
			$(this).removeClass('active');
		} else {
			$(this).closest('.hidden-text-holder').find('.hidden-item').css("display" , "block");
			$(this).addClass('active');
		}
	});

	//Expanded list action
	$(".list-expanded > li > a").click(function(event){
		event.preventDefault();
		$(this).next('.list-sub-expanded').slideToggle(250);
		$(this).toggleClass('active');
	});

	//Tinymce initial
	tinymce.init({
	    selector: "textarea#tinymce-holder"
	});


	//Select 2 initial
	$(".basic-multiple").select2();



});








$(window).load(function(){
	
});


$(window).resize(function(){
	
});