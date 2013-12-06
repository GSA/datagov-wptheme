<div class="wrap container">
<div class="page-header">
  <h1>Browse Topics</h1>
</div>
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
	$option .= "<i></i><span>{$category->cat_name}</span>";
	$option .= '</a></li>';
	echo $option;
}
?>
</ul> 

<div class="more-link">
    <h5>More Topics</h5>
</div>

</div><!--/.container-->


<?php get_template_part('templates/content','highlights'); ?>

<div class="wrap container">
<?php get_template_part('templates/content','excerpts'); ?>
</div><!--/.container-->