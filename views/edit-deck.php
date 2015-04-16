<?php

?>

<script type="text/javascript" >
	function addSlide() {
		
		jQuery(function($) {
			var count = $('.postbox.slide').length;
			var data = {
				'action': 'lmslider_add_slide',
				'count' : count
			};

			// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
			$.post(ajaxurl, data, function(response) {
				$('#list_slides').append(response);
				var id = 'slide_'+(count+1)+'_content';
				var str = et_tinyMCEPreInit.replace(/editor_id/gi, id);
var ajax_tinymce_init = JSON.parse(str);

tinymce.init( ajax_tinymce_init.mceInit[id] );
				
			});
		});
	};

	</script>

<div class="lmslider-container">
	<?php if($currentAction == 'edit'): ?>
	<h1> Edition du slider </h1>
	<?php else: ?>
	<h1> Nouveau slider </h1>
	<?php endif; ?>

		<form action="" method="post" id="lmslider_save_form">
		<?php if(!empty($lmslider_deck_id)): ?>
		<input type='hidden' name='deckid' value='<?php echo $lmslider_deck_id ?>' />
		<?php endif; ?>
		<input type="hidden" name="action" value="edit" id="form_action">
	<div class="editor-wrapper">
				<div class="editor-body">
					<div id="titlediv">
						<div id="titlewrap">
							<label for="name">Nom du slider</label>
							<input type="text" name="title" size="40" maxlength="255" value="<?php echo !empty( $slidedeck['title'] ) ? $slidedeck['title'] : 'My SlideDeck'; ?>" id="title" />
						</div>
					</div>
				 
					<div class="slides" id="list_slides">
						<?php $count = 1; ?>
						<?php foreach( (array) $slides as $slide ): ?>
							<?php include( lmslider_dir( '/views/_edit-slide.php' ) ); ?>
							<?php $count++; ?>
						<?php endforeach; ?>
						<?php $slide = null ?>
						
					</div>
		
				</div>
	</div>
	<hr>
	
		<input type="submit" class="button-primary" value="Sauvegarder" style="float:right;" />
	</form>
	<br style='clear:both'/>
	<div id="re-order-slides" class="postbox">
					<h3 class="hndle">Re-order Slides</h3>
					<div class="inside">
						<p>Re-order the slides in this SlideDeck<br /><em>Slide editor order will change after saving.</em></p>
						<ul class="ui-sortable slide-order">
							<?php $count = 1; ?>
							<?php foreach( (array) $slides as $slide ): ?>
								<li><a href="#slide_editor_<?php echo $count; ?>" class="hndle" id="hndle_for_slide_editor_<?php echo $count; ?>"><?php echo empty( $slide->title ) ? "Slide " . $count : $slide->title; ?></a></li>
								<?php $count++; ?>
							<?php endforeach; ?>
						</ul>
						<div id="add-another-slide"><div class="ajax-masker"></div><a href="#" onClick="addSlide()" id="btn_add-another-slide" class="preview button">Add Another Slide</a></div>
					</div>
	</div>



</div>