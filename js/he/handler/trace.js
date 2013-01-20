/*
 * Affiche ou masque les différentes catégories de trace
 * 
 * @require jQuery-1.7.1.min.js
 */

$(document).ready(function(){
	$('.HeCallstackCat li').live('click', function(){
		/* On masque toute les traces */
		$('.HeCallStackSequence').hide();
		
		/* On affiche la nouvelle */
		var div = $(this).attr('rel');
		$('#'+div).show();
	})
	
	$('#HeSuTrace').on('click', function(){
		$("#HeCallstack").fadeIn('fast');
	});
	
	$('#HeCallStackClose').live('click', function(){
		$("#HeCallstack").fadeOut('fast');
	});
});

function replaceTrace(html)
{
	$('#HeCallstack').remove();
	$('#HeSuBar').after(html);
	$('#HeCallstack').show();
}