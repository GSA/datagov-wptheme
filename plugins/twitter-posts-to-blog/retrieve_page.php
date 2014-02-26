<div class="wrap">
	<div id="icon-options-general" class="icon32"><br /></div>
	<h2>Twitter To WordPress Autopost</h2>
	
	<?php
		if(empty($dg_tw_queryes)) {
			echo "Nothing to show here";
			exit;
		}
		
		if($tokens_error) {
			echo "You need to configure tokens in the plugin settings page";
			exit;
		}
		
		foreach($dg_tw_queryes as $query) {
			$parameters = array(
				'q' => $query['value'],
				'since_id' => $query['last_id'],
				'include_entities' => true,
				'count' => $dg_tw_ft['ipp']
			);
			
			$dg_tw_data = $connection->get('search/tweets', $parameters);
			
			if(isset($dg_tw_data->errors) && count($dg_tw_data->errors)) {
				$error = current($dg_tw_data->errors);
				
				echo "<h1>ERROR! Check your twitter app configuration</h1>";
				echo "<h2>Info: <i>".$error->message."</i></h2>";
				break;
			}
			
			
			if(isset($dg_tw_data->statuses)) {
				echo "<h3>".$query['value']."</h3>";
				
				?>
				<table class="widefat" cellspacing="0">
					<thead>
						<th style="width:55px;text-align:center;" class="manage-column column-cb check-column">
							Pic
						</th>
						<th scope="col" style="width: 20%;" id="title" class="manage-column sortable desc" style="">
							<span>Author</span>
						</th>
						<th scope="col">
							<span>Filtered Tweet</span>
						</th>
						<th scope="col">
							<span>Original Tweet</span>
						</th>
						<th scope="col"style="width: 10%;">
							<span>Tweet Time</span>
						</th>
						<th scope="col" style="width: 10%;">
							<span>Publish</span>
						</th>
					</thead>
					
					<tbody id="the-list">
						<?php
							foreach($dg_tw_data->statuses as $item) {
								if( isset($dg_tw_ft['exclude_retweets']) && $dg_tw_ft['exclude_retweets'] && isset($item->retweeted_status))
									continue;
									
								if( isset( $dg_tw_ft['exclude_no_images'] ) && $dg_tw_ft['exclude_no_images'] && !count($item->entities->media))
									continue;
								
								if(dg_tw_iswhite($item)) {
									$content = dg_tw_regexText( $item->text );
									?>
									<tr id="post-190" class="post-190 type-post status-publish format-standard hentry alternate iedit author-self" valign="top">
										<td scope="row" style="width:55px;text-align:center;" align="center">
											<?php
												if(isset($item->entities->media)) {
													foreach($item->entities->media as $media) {
														if($media->type=="photo") {
															echo '<img style="max-height:50px;max-width:50px;" src="'.$media->media_url.'" alt=""/>';
															break;
														}
													}
												}
											?>
										</td>
										<td scope="row">
											<b><?php echo $item->user->name; ?></b>
										</td>
										<td scope="row" style="word-break:break-all;">
											<?php echo $content; ?>
										</td>
										<td scope="row" style="word-break:break-all;">
											<?php echo $item->text; ?>
										</td>
										<td scope="row">
											<?php echo $item->created_at; ?>
										</td>
										<td scope="row">
											<button type="button" class="manual_publish" data-query="<?php echo $query['value']; ?>" data-pid="<?php echo $item->id_str; ?>">Publish</button>
										</td>
									</tr>
									<?php
								}
							}
						?>
					</tbody>
					
					<tfoot>
						<th style="width:55px;text-align:center;" class="manage-column column-cb check-column">
							Pic
						</th>
						<th scope="col" style="width: 20%;" id="title" class="manage-column sortable desc" style="">
							<span>Author</span>
						</th>
						<th scope="col" style="">
							<span>Filtered Tweet</span>
						</th>
						<th scope="col" style="">
							<span>Original Tweet</span>
						</th>
						<th scope="col" style="width: 10%;" style="">
							<span>Tweet Time</span>
						</th>
						<th scope="col" valign="middle" style="width: 10%;" style="">
							<span>Publish</span>
						</th>
					</tfoot>
				</table>
				<br><br><br>
				<?php
			}
		}
	?>
</div>
<script type="text/javascript">
jQuery(document).ready(function($) {
	jQuery('.manual_publish').on('click',function () {
		var selected = jQuery(this);
		var pid = selected.data('pid');
		var query = selected.data('query');
		var parent = selected.parent();
		var parent_parent = selected.parent().parent();

		selected.remove();
		parent.append('<span>Publishing...</span>');
		
		var data = {
			action: 'dg_tw_manual_publish',
			id: pid,
			query: query
		};
		
		jQuery.post(ajaxurl, data, function(response) {
			if(response == 'true') {
				parent_parent.remove();
			}
			if(response == 'already') {
				alert('This tweet is already published!');
			}
			if(response == 'nofound') {
				alert('Tweet nowt found, retry later!');
			}
		});
	});
});
</script>