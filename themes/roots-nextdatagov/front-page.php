

<p>
This is the front-page.php template
</p>

<h1>Browse</h1>

<?php $args = array(
	'show_option_all'    => '',
	'orderby'            => 'name',
	'order'              => 'ASC',
	'style'              => 'list',
	'show_count'         => 0,
	'hide_empty'         => 1,
	'use_desc_for_title' => 1,
	'child_of'           => 0,
	'feed'               => '',
	'feed_type'          => '',
	'feed_image'         => '',
	'exclude'            => '112,71,73,79,64,82,65,62,63,70,74,59,67,26880,102,93,69,61,57,60,72,94,56,26881,26879,81,68,75,26882,26883,26877',
	'exclude_tree'       => '',
	'include'            => '',
	'hierarchical'       => 1,
	'title_li'           => __( '' ),
	'show_option_none'   => __('No categories'),
	'number'             => null,
	'echo'               => 1,
	'depth'              => 0,
	'current_category'   => 0,
	'pad_counts'         => 0,
	'taxonomy'           => 'category',
	'walker'             => null
); ?>

<?php wp_list_categories( $args ); ?> 

<p>
Show more
</p>

<h1>Highlights</h1>
<p>
One featured, but expandable to show multiple
</p>


<?php get_template_part('templates/content','excerpts'); ?>
