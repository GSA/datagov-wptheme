<div class="entry-meta">
	
<?php
	/** Hiding this for now
	
	echo '
	<p class="byline author vcard">
	<a href="' . get_author_posts_url(get_the_author_meta('ID')) . '" rel="author" class="fn">' . get_the_author() . '</a>' . 
	'</p>';
	 if (get_the_author()) {
	 	echo '&bull;';	
	 }
	**/
?>

	<time class="published" datetime="<?php echo get_the_time('c'); ?>"><?php echo get_the_date(); ?></time>
</div>

