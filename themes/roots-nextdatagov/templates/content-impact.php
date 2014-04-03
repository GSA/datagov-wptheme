<?php 


$term = get_post_custom_values('industry');
$term = $term[0];

if(empty($headings[$term])) {
	$headings[$term] = true;
	$term = $industries[$term];
	$heading = '<div class="category-header topic-' . $term->slug . '"><a name="' . $term->slug . '" href="#' . $term->slug . '"><i></i><span>' . $term->name . '</span></a></div>';
} else {
	$heading = '';
}

$meta = array();

$meta['anchor_id']			= basename(get_permalink());

$meta['location'] 		= (get_post_custom_values('location')) ? current(get_post_custom_values('location')) : null;
$meta['financing'] 		= (get_post_custom_values('financing')) ? current(get_post_custom_values('financing')) : null;
$meta['jobs'] 			= (get_post_custom_values('jobs')) ? current(get_post_custom_values('jobs')) : null;
$meta['data_sources'] 	= (get_post_custom_values('data_sources')) ? current(get_post_custom_values('data_sources')) : null;
$meta['link'] 			= (get_post_custom_values('link')) ? current(get_post_custom_values('link')) : '#' . $meta['anchor_id'];


?>

<?php echo $heading; ?>

<div class="impact-post clearfix">



<div class="impact-meta-list col-md-3 col-md-push-9">

	<div class="impact-meta impact-title">
		<h3 class="meta-heading">
			<a name="<?php echo $meta['anchor_id']?>" href="<?php echo $meta['link'] ?>">
				<i class="glyphicon glyphicon glyphicon-link"></i>
				<span><?php the_title(); ?></span>
			</a>
		</h3>
	</div>

	<?php if (!empty($meta['location'])): ?>

		<div class="impact-meta">
			
			<h4 class="meta-heading">
				<i class="glyphicon glyphicon-map-marker"></i>
				<span>Location</span>
			</h4>
			 
			<div class="meta-content"> 
			 <?php echo $meta['location']; ?> 
			</div>

		</div>

	<?php endif; ?>


	<?php if (!empty($meta['financing'])): ?>

		<div class="impact-meta">
			
			<h4 class="meta-heading">
				<i class="glyphicon glyphicon-usd"></i>
				<span>Financing</span>
			</h4>
			 
			<div class="meta-content"> 
			 <?php echo $meta['financing']; ?> 
			</div>

		</div>

	<?php endif; ?>



	<?php if (!empty($meta['jobs'])): ?>

		<div class="impact-meta">
			
			<h4 class="meta-heading">
				<i class="glyphicon glyphicon-user"></i>
				<span>Jobs</span>
			</h4>
			 
			<div class="meta-content"> 
			 <?php echo $meta['jobs']; ?> 
			</div>

		</div>

	<?php endif; ?>


</div>


<div class="col-md-9 col-md-pull-3">

	<div class="impact-body">
		<?php the_content(); ?>
	</div>

	<?php if (!empty($meta['data_sources'])): ?>

		<div class="impact-meta impact-data-sources">
			
			<h4 class="meta-heading">
				<i class="glyphicon glyphicon-folder-open"></i>
				<span>Data Sources</span>
			</h4>
			 
			<div class="meta-content"> 
			 <?php echo $meta['data_sources']; ?> 
			</div>

		</div>

	<?php endif; ?>

</div>




</div>