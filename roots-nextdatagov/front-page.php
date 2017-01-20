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
	'exclude'                  => '112,71,73,79,64,82,65,62,63,70,74,59,67,26880,102,93,69,61,57,60,72,94,56,26881,26879,81,68,75,26882,26883,26877,33884',
	'include'                  => '',
	'number'                   => '',
	'taxonomy'                 => 'category',
	'pad_counts'               => false

);

?>

<ul class="topics">
<?php
// 	$categories = get_categories($args);
// 	foreach ($categories as $category) {
// 		$option = '<li class="topic-' . $category->category_nicename . '"><a href="/'.$category->category_nicename.'">';
// 		$option .= "<i></i><span>{$category->cat_name}</span>";
// 		$option .= '</a></li>';
// 		echo $option;
// 	}
?>


	<li class="topic-food">
		<a href="/food/"><i></i><span>Agriculture</span></a>
	</li>
	<li class="topic-climate">
		<a href="/climate/"><i></i><span>Climate</span></a>
	</li>
    <li class="topic-consumer">
        <a href="/consumer/"><i></i><span>Consumer</span></a>
    </li>
	<li class="topic-ecosystems">
		<a href="/ecosystems/"><i></i><span>Ecosystems</span></a>
	</li>
	<li class="topic-education">
		<a href="/education/"><i></i><span>Education</span></a>
	</li>
	<li class="topic-energy">
		<a href="/energy/"><i></i><span>Energy</span></a>
	</li>
	<li class="topic-finance">
		<a href="/finance/"><i></i><span>Finance</span></a>
	</li>
	<li class="topic-health">
		<a href="/health/"><i></i><span>Health</span></a>
	</li>
	<li class="topic-local">
		<a href="/local/"><i></i><span>Local Government</span></a>
	</li>
	<li class="topic-manufacturing">
		<a href="/manufacturing/"><i></i><span>Manufacturing</span></a>
	</li>
	<li class="topic-maritime">
		<a href="/maritime/"><i></i><span>Maritime</span></a>
	</li>
	<li class="topic-ocean">
		<a href="/ocean/"><i></i><span>Ocean</span></a>
	</li>
	<li class="topic-safety">
		<a href="/safety/"><i></i><span>Public Safety</span></a>
	</li>
	<li class="topic-research">
		<a href="/research/"><i></i><span>Science &amp; Research</span></a>
	</li>
</ul>

</div><!--/.container-->


<?php get_template_part('templates/content','highlights'); ?>

<div class="wrap container">
<?php get_template_part('templates/content','excerpts'); ?>
</div><!--/.container-->
