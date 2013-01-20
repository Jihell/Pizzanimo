/* 
 Affiche les overlay.
Le fake overlay sert à afficher un curseur d'attente durant une action ajax
Attention lors de l'utilisation du fake overlay, penser à le détruire en cas
d'échec !

 @author Joseph Lemoine - lemoine.joseph@gmail.com
 @version 1
 */

/* Trigger de fermeture des overlays */
$(document).ready( function () {
    $('div.overlay, div.overlay .closeOV').live('click', function(event) {
		/* Test si la propagation de l'event est bien sur la cible et non un
		 * descendant */
		if(event.target == this) {
			hideOverlay($('div.overlay'));
		}
	});
});

/* Création d'un overlay */
function openOverlay(content)
{
	var overlay_layer = $('<div></div>', {
		'class': 'overlay layer'
	});
	var overlay_box = $('<div></div>', {
		'class': 'overlay_box'
	});
	overlay_box.html(content);
	overlay_layer.append(overlay_box);
	
	$("footer").after(overlay_layer);
	$('.overlay').css({
		width: $(document).width(),
		height: $(document).height()
	});
	$(".overlay").fadeIn("fast");
	
	$('.overlay_box').css({
		'left': (($(document).width() - overlay_box.width()) / 2)+'px',
		'top': (($(document).height() - overlay_box.height()) / 2)+'px'
	});
}
/* Fermeture d'un overlay */
function hideOverlay(overlay) 
{
	overlay.fadeOut("fast", function(){$(this).remove();});
}
/* Création d'un overlay */
function openFakeOverlay() {
	$("footer").after("<div class='fakeOverlay'></div>");
	$(".fakeOverlay").css("top", window.pageYOffset);
}
/* Fermeture d'un overlay */
function hideFakeOverlay() {
	$(".fakeOverlay").remove();
}