<?php
?>
<div class="lmslider-container">
	<h1> Liste des sliders </h1>
    <br/>
    <div class='addButton'><a href="<?php echo LM_ACTION ?>&action=new"> Creer une nouvelle animation</a></div>
    <br/>

		<table id="lmslider_decks" class="lmslider_list" cellspacing="0">
			<tbody>
    				<tr>
    					<th> Titre </th>
                        <th> Date </th>
                        <th> Actions </th>
    				</tr>
    				<?php foreach( (array) $decks->posts as $deck ): ?>
    				<tr class="lmslider_deck_item" valign="top" style='vertical-align:middle;'>
    					<td><a href="<?php echo LM_ACTION ?>&action=edit&id=<?php echo $deck->ID; ?>"><?php echo $deck->post_title; ?> </a></td>
                        <td><a href="<?php echo LM_ACTION ?>&action=edit&id=<?php echo $deck->ID; ?>"><?php echo $deck->post_modified_gmt; ?> </a></td>
                        <td ><a href="javascript:if(confirm('Supprimer <?php echo $deck->post_title; ?> ?')) window.location.replace('<?php echo LM_ACTION ?>&action=delete&deckid=<?php echo $deck->ID; ?>');">
                            <img width='20px' src='<?php echo LM_PATH."/img/remove.png"; ?>' > </a></td>
    				</tr>
    				<?php endforeach; ?>
    		</tbody>
		</table>
</div>