<?php /*
Template Name: In The News
*/
?>

<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!-->
<html lang="en">
<!--<![endif]-->

<?php get_template_part('header'); ?>
<?php
$category = get_the_category();
$term_name = $category[0]->cat_name;
$term_slug = $category[0]->slug;
?>
<?php
$cat_name = $category[0]->cat_name;
$cat_slug = $category[0]->slug;
?>

<body class="announcement">
<div class="banner disclaimer">
    <p>This is a demonstration site exploring the future of Data.gov. <span id="stop-disclaimer"> Give us your feedback on <a href="https://twitter.com/usdatagov">Twitter</a>, <a href="http://quora.com">Quora</a></span>, <a href="https://github.com/GSA/datagov-design/">Github</a>, or <a href="http://www.data.gov/contact-us">contact us</a></p>
</div>
<!-- Header Background Color, Image, or Visualization
================================================== -->
<div class="menu-container">
    <div class="header-next-top" >


        <?php get_template_part('navigation'); ?>



    </div>
</div>
<div class="next-header category <?php foreach( get_the_category() as $cat ) { echo $cat->slug . '  '; } ?>"> </div>

<!-- Navigation & Search
================================================== -->

<div class="container">
  <div class="next-top category energy <?php foreach( get_the_category() as $cat ) { echo $cat->slug . '  '; } ?>">

    <?php get_template_part('category-search'); ?>
  </div>
  <!-- top --> 
  
</div>
<div class="page-nav"> </div>
<div class="container">
  <div class="sixteen columns page-nav-items">
      <?php


      // show Links associated to a community
      // we need to build $args based either term_name or term_slug
      $args = array(
          'category_name'=> $term_slug, 'categorize'=>0, 'title_li'=>0,'orderby'=>'rating');
      wp_list_bookmarks($args);
      if (strcasecmp($term_name,$term_slug)!=0) {
          $args = array(
              'category_name'=> $term_name, 'categorize'=>0, 'title_li'=>0,'orderby'=>'rating');
          wp_list_bookmarks($args);
      }
      ?>
  </div>
  
  <!-- WordPress Content
    ================================================== -->
  <div class="category-content">
    <div class="content" style="margin-top:20px;">
     
      <div class="sixteen columns">
          <div class="content">
              <?php
              while( have_posts() ) {
                  the_post();
                  ?>
          <div class="Apps-wrapper">
          <div class="Apps-post" id="post-<?php the_ID(); ?>">
           <div id="appstitle" class="Appstitle" ><?php the_title();?></div>
                  <?php the_content();   ?>
                  <?php }?>
          </div>
          </div>
          </div>
      <!--  News -->
        <?php $category = get_the_category();
							$cat_slug = $category[0]->slug;
							//echo $cat_name;
				?>
        <?php //query_posts('category_name='.$cat_name ); ?>
        <?php
		/*$args = array(
						 'post_type' => 'posts',
					   'tax_query'=>	array(
						'relation' => 'AND',
					array(
					'taxonomy' => 'announcements_and_news',
					'terms' => 'announcement-10',
					'field' => 'slug',
					),
					array(
					'taxonomy' => 'category',
					'terms' => $cat_slug,
					'field' => 'slug',
					),
				)
			);
	*/
	$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
	$args = array(
			'posts_per_page' => 20,
			'announcements_and_news'=>'in_the_news-10',
			'paged' => $paged
	);	
		$apps = new WP_Query( $args );
		$my_post_count = $apps->post_count;
		
		if( $apps->have_posts() ) {
			
			while( $apps->have_posts() ) {
			
				$apps->the_post();
				?>
                <div id="cat-posts" class="All-cat-post horizontal_dotted_line cat-post">
         <div class="core">
             <div class="title">
                 <a href="<?php echo get_post_meta($post->ID, 'link_to_url', TRUE ); ?>">
                     <?php the_title() ?>
                 </a>
             </div>
             <?php $postdate=strtotime(get_post_meta($post->ID, 'field_original_post_date', TRUE )); ?>
             <span><?php echo date("m/d/y", $postdate); ?></span>
        <div class="body">
         
		
          <?php the_content() ?>
   
       
       
		 </div><br clear="all" />
          </div>
             </div>
		<?php
			}
		}
		
		$big = 999999999; // need an unlikely integer

echo paginate_links( array(
	'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
	'format' => '?paged=%#%',
	'current' => max( 1, get_query_var('paged') ),
	'total' => $apps->max_num_pages
) );
	?>
   
        <br clear="all" />
     
        
        
   
      </div>
      <!-- sixteen columns -->
      
      <?php get_template_part('footer'); ?>
    </div>
    <!-- content --> 
  </div>
</div>
<!-- container --> 

<script src="<?php echo get_bloginfo('template_directory'); ?>/js/jquery.joyride-2.1.js"></script> 
<script type="text/javascript" src="<?php echo get_bloginfo('template_directory'); ?>/js/jquery.cookie.js"></script> 
<script type="text/javascript" src="<?php echo get_bloginfo('template_directory'); ?>/js/modernizr.mq.js"></script> 
<script>
    $(window).load(function(){
        $('#posts').masonry({
            // options
            columnWidth: 287,
            itemSelector : '.post',
            isResizable: true,
            isAnimated: true,
            gutterWidth: 25
        });

        $("#joyRideTipContent").joyride({
            autoStart: true,
            modal: true,
            cookieMonster: true,
            cookieName: 'datagov',
            cookieDomain: 'next.data.gov'
        });
    });
</script> 
<script>
    $(function () {
        var
                $demo = $('#rotate-stats'),
                strings = JSON.parse($demo.attr('data-strings')).targets,
                randomString;

        randomString = function () {
            return strings[Math.floor(Math.random() * strings.length)];
        };

        $demo.fadeTo(randomString());
        setInterval(function () {
            $demo.fadeTo(randomString());
        }, 15000);
    });
</script> 
<script src="<?php echo get_bloginfo('template_directory'); ?>/js/v1.js"></script> 
<script src="<?php echo get_bloginfo('template_directory'); ?>/js/autosize.js"></script> 

<!-- End Document
================================================== -->
</body>
</html>