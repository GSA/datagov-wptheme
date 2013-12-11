<div class="entry-meta">
	<p class="byline author vcard">
	<a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>" rel="author" class="fn"><?php echo get_the_author(); ?></a>
	</p>
	<?php if (get_the_author()): ?>
	 &bull;  
	<?php endif; ?>
	<time class="published" datetime="<?php echo get_the_time('c'); ?>"><?php echo get_the_date(); ?></time>
</div>