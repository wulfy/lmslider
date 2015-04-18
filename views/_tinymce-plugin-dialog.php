<?php
/**
 * TinyMCE plugin dialog markup
 * 
 */
?>
<div id="lmslider_tinymce_dialog">
	<p>Sélectonner une animation:</p>
	<?php if( isset( $decks ) && !empty( $decks ) ): ?>
		<table class="widefat post fixed" cellspacing="0">
			<thead>
				<tr>
					<th class="manage-column column-title" scope="col">Titre</th>
					<th width="90" class="manage-column column-date" scope="col">Date</th>
				</tr>
			</thead>
			<tbody>
				
				<?php foreach( (array) $decks->posts as $deck ): ?>
					<tr id="deck_id_<?php echo $deck->ID; ?>" class="author-self status-publish iedit" valign="top">
						<td class="post-title column-title">
							<?php echo $deck->post_title ?>
						</td>
						<td clsss="date column-date"><?php echo date( "d/m/Y", strtotime( $deck->post_modified ) ); ?></td>
					</tr>
					
				<?php endforeach; ?>
			</tbody>
		</table>
		<div class="dialog-other-options">
			Dimensions: <input type="text" size="5" value="100%" id="lmslider_tinymce_dimension_w" /> x <input type="text" size="5" value="300px" id="lmslider_tinymce_dimension_h" />
		</div>
	<?php else: ?>
	<div class="message">
		<p>Pas d'animations trouvées! <a href="<?php echo LM_ACTION.'/lmslider_add_new'; ?>">Créer une animation</a></p>
	</div>
	<?php endif; ?>
</div>
