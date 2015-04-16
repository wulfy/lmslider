<?php
?>
<div class="lmslider-container">
	<h1> Liste des sliders </h1>
		<table id="lmslider_decks" class="lmslider_list" cellspacing="0">
			<tbody>
    				<tr>
    					<a href="<?php echo LM_ACTION ?>&action=new"> Nouveau slider </a>
    				</tr>
    				<?php foreach( (array) $decks as $deck ): ?>
    				<tr class="lmslider_deck_item" valign="top">
    					<a href="<?php echo LM_ACTION ?>&action=edit&id=<?php echo $deck['id']; ?>"><?php echo $deck['name']; ?> </a>
    				</tr>
    				<?php endforeach; ?>
    		</tbody>
		</table>
</div>