<footer>
	<em>2012 - Développé par <a target="_blank" href="http://www.joseph-lemoine.fr">Joseph Lemoine</a> - Sources libres de droit.</em> | 
	<span id="rules_trigger"><strong>Règles du jeu</strong></span> | 
	<span id="aPropos_trigger">A propos</span>
	<a href="https://twitter.com/share" class="twitter-share-button" data-url="http://pjc.joseph-lemoine.fr/" data-via="LemoineJoseph" data-lang="fr" data-count="none">Tweeter</a>
	<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
	<div class="fb-like" data-href="http://pjc.joseph-lemoine.fr/" data-send="false" data-layout="button_count" data-width="90" data-show-faces="false" data-font="arial"></div>
</footer>
<script>
	$(document).ready(function(){
		$('#rules_trigger').click(function(){
			openOverlay($('#rules').html());
		});
		$('#aPropos_trigger').click(function(){
			openOverlay($('#aPropos').html());
		});
	});
</script>
