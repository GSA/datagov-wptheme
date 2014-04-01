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

?>

<?php echo $heading; ?>

<div class="impact-post clearfix">

<div class="col-md-9">

	<div class="impact-body">
		<?php the_content(); ?>
	</div>

	<?php if ($meta = get_post_custom_values('data_sources')): ?>

		<div class="impact-meta impact-data-sources">
			
			<h4 class="meta-heading">
				<i class="glyphicon glyphicon-folder-open"></i>
				<span>Data Sources</span>
			</h4>
			 
			<div class="meta-content"> 
			 <?php echo $meta[0]; ?> 
			</div>

		</div>

	<?php endif; ?>

</div>



<div class="impact-meta-list col-md-3">

	<div class="impact-meta impact-title">
		<h3 class="meta-heading">
			<?php the_title(); ?>
		</h3>
	</div>

	<?php if ($meta = get_post_custom_values('location')): ?>

		<div class="impact-meta">
			
			<h4 class="meta-heading">
				<i class="glyphicon glyphicon-map-marker"></i>
				<span>Location</span>
			</h4>
			 
			<div class="meta-content"> 
			 <?php echo $meta[0]; ?> 
			</div>

		</div>

	<?php endif; ?>


	<?php if ($meta = get_post_custom_values('financing')): ?>

		<div class="impact-meta">
			
			<h4 class="meta-heading">
				<i class="glyphicon glyphicon-usd"></i>
				<span>Financing</span>
			</h4>
			 
			<div class="meta-content"> 
			 <?php echo $meta[0]; ?> 
			</div>

		</div>

	<?php endif; ?>



	<?php if ($meta = get_post_custom_values('jobs')): ?>

		<div class="impact-meta">
			
			<h4 class="meta-heading">
				<i class="glyphicon glyphicon-user"></i>
				<span>Jobs</span>
			</h4>
			 
			<div class="meta-content"> 
			 <?php echo $meta[0]; ?> 
			</div>

		</div>

	<?php endif; ?>


</div>





</div>