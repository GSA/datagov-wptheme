

<p>
This is the front-page.php template
</p>

<h1>Browse Topics</h1>

<?php  

$args = array(
	'type'                     => 'post',
	'child_of'                 => 0,
	'parent'                   => '',
	'orderby'                  => 'name',
	'order'                    => 'ASC',
	'hide_empty'               => 1,
	'hierarchical'             => 1,
	'exclude'                  => '112,71,73,79,64,82,65,62,63,70,74,59,67,26880,102,93,69,61,57,60,72,94,56,26881,26879,81,68,75,26882,26883,26877',
	'include'                  => '',
	'number'                   => '',
	'taxonomy'                 => 'category',
	'pad_counts'               => false 

);

?>

<ul class="topics">
<?php 
$categories = get_categories($args); 
foreach ($categories as $category) {
	$option = '<li class="topic-' . $category->category_nicename . '"><a href="/'.$category->category_nicename.'">';
	$option .= $category->cat_name;
	$option .= '</a></li>';
	echo $option;
}
?>
</ul> 

<p>
Show more
</p>

<section class="highlights">
	<h1 class="label">Highlights</h1>
<div class="highlight">
	<h2>Lorem Ipsum Dolor Sit Amet</h2>
	<div class="col-md-8">
		Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam quam ante, fermentum ac varius eu, imperdiet eu nisl. Vivamus consequat sem at est cursus condimentum. Phasellus consequat sagittis nunc, facilisis bibendum nisl eleifend in. Duis quis diam diam. Ut eu quam id arcu eleifend cursus. Morbi fringilla enim non placerat gravida. Morbi nec neque leo. Integer massa urna, malesuada nec risus sed, eleifend sagittis libero. Phasellus eget lobortis tortor, non vulputate magna. Fusce faucibus tristique feugiat. Aenean in faucibus dui. Aliquam sem massa, dapibus et nibh non, congue condimentum turpis.
		</p>
	</div>
	<div class="col-md-4">
		<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam viverra metus vitae iaculis mattis. Nam gravida dictum dui, sit amet congue odio pulvinar in. Quisque vitae dictum elit. In non leo quis tellus</p>
		<button>Button One</button>
		<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam viverra metus vitae iaculis mattis. Nam gravida dictum dui, sit amet congue odio pulvinar in. Quisque vitae dictum elit. In non leo quis tellus</p>
		<button>Button Two</button>
	</div>
</div><!--/highlight-->
</section>

<section class="updates">
<?php get_template_part('templates/content','excerpts'); ?>
</section>