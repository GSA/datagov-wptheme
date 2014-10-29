<?php
$category = get_the_category();
$term_name = $category[0]->cat_name;
$term_slug = $category[0]->slug;
?>
<?php
$cat_name = $category[0]->cat_name;
$cat_slug = $category[0]->slug;
$allowed_slug_arrays = array("climate-ecosystems","coastalflooding","energysupply","foodsupply","humanhealth","transportation","water","climate");
?>
<?php include('category-subnav.php'); ?>
<div class="wrap container content-page" style="pading-top:0px;">
    <?php
    while( have_posts() ) {
        the_post();
        ?>
          <div class="Apps-wrapper" style="margin-top:0px;">
          <div class="Apps-post" id="post-<?php the_ID(); ?>">
         <?php the_content();   ?>
         </div></div>
        <?php }?>
  <div class="highlights-listing">
<?php
      $args = array(
          'post_type' => 'post',
          'tax_query' => array(
              'relation' => 'AND',
              array(
                  'taxonomy' => 'post_format',
                  'field' => 'slug',
                  'terms' => array( 'post-format-link', 'post-format-status', 'post-format-gallery'),
                  'operator' => 'NOT IN'
              ),
              array(
                  'taxonomy' => 'featured',
                  'field' => 'slug',
                  'terms' => array( 'highlights'),
                  'operator' => 'IN'
              )
          ),
          'posts_per_page' => - 1,
          'category_name'=> $cat_slug );


      $category_query = new WP_Query($args);
      wp_reset_query();
      $highlights = array();
      $i        = 0;
    while ( $category_query->have_posts() ) {
        $category_query->the_post();
        $highlights[ $i ]['title']                 = get_the_title( $post->ID );
        $highlights[ $i ]['content']                = get_content();
        $highlights[ $i ]['post_thumbnail_id'] = get_post_thumbnail_id( $post_id );
        $highlights[ $i ]['featuredImage'] = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'thumbnail' );
        $highlights[ $i ]['postformat'] = get_post_format();
        $dataseturl  = get_field_object( 'field_5176012cb8098' );
        $highlights[ $i ]['dataseturl'] = $dataseturl['value'];

        $i++;
    }
      $total_highlights    = count( $highlights );
      $highlights_per_page = 5;
      if ( isset( $highlights ) ) {
          $total_pages = ceil( $total_highlights / $highlights_per_page );
      } else {
          $total_pages = 1;
          $total_highlights  = 0;
      }
      if ( isset( $_GET['currentpage'] ) && is_numeric( $_GET['currentpage'] ) ) {
          $currentpage = (int) $_GET['currentpage'];
      } else {
          $currentpage = 1;
      }
      if ( $currentpage > $total_pages ) {
          $currentpage = $total_pages;
      }
      if ( $currentpage < 1 ) {
          $currentpage = 1;
      }
      $start = ( $currentpage - 1 ) * $highlights_per_page + 1;

      if($total_highlights > 0){
            for ( $i = $start - 1; $i < $start - 1 + $highlights_per_page; $i ++ ) {
                if ( isset( $highlights[ $i ] ) ) {
          ?>
          <div class="highlight <?php $cat_name ?> clearfix">
              <header>
                  <h2 class="entry-title"><?php  echo $highlights[ $i ]['title']; ?></h2>
              </header>
              <?php if ( $highlights[ $i ]['post_thumbnail_id'] ) : ?>
              <div class="featured-image col-md-2">
              
                <img src="<?php echo $highlights[ $i ]['featuredImage'][0];?>" />
              </div>
              <?php endif; ?>
              <article class="<?php if ( $highlights[ $i ]['post_thumbnail_id'] ) : ?>col-md-10<?php else: ?>no-image<?php endif;?>">
                  <?php echo $highlights[ $i ]['content']; ?>
              </article>
              <?php if ( $highlights[ $i ]['postformat']  == 'image'): ?>
              <div class="dataset-link">
                  <a class="btn btn-default pull-right" href="<?php echo $highlights[ $i ]['dataseturl']; ?>">
                      <span class="glyphicon glyphicon-download"></span> View this Dataset
                  </a>
              </div>
              <?php endif;?>
          </div>

     <?php }
        }
      }
?>
    </div>
        <div class='pagination'>
            <p class="counter">
                <?php printf( __( 'Page %1$s of %2$s' ), $currentpage, $total_pages ); ?>
            </p>
            <?php
            highlights_customPagination( $query,'highlights', $currentpage, $total_pages, true );
            ?>
        </div>
    </div>
</div>



<?php
function highlights_customPagination( $query,$base_url, $cur_page, $number_of_pages, $prev_next = false ) {
    $ends_count   = 1; //how many items at the ends (before and after [...])
    $middle_count = 2; //how many items before and after current page
    $dots         = false;
    $nextpage     = $cur_page + 1;
    $prevpage     = $cur_page - 1;
    $output       = "<ul class='pagination'>";
    ?>

<?php
    if ( $prev_next && $cur_page && 1 < $cur_page ) { //print previous button?
        $output .= "<li class='pagination-prev'><a class='prev page-numbers pagenav local-link' href='?currentpage=$prevpage'>Previous</a> </li>";
    }
    for ( $i = 1; $i <= $number_of_pages; $i ++ ) {
        if ( $i == $cur_page ) {
            $output .= "<li><span class='page-numbers pagenav current'> $i </span></li>";
            $dots = true;
        } else {
            if ( $i <= $ends_count || ( $cur_page && $i >= $cur_page - $middle_count && $i <= $cur_page + $middle_count ) || $i > $number_of_pages - $ends_count ) {
                $output .= "<li><a class='page-numbers pagenav' href='?currentpage=$i'> $i </a></li>";
                $dots = true;
            } elseif ( $dots ) {
                $output .= '<li><span class="page-numbers dots">' . __( '&hellip;' ) . '</span></li>';
                $dots = false;
            }
        }
    }
    if ( $prev_next && $cur_page && ( $cur_page < $number_of_pages || - 1 == $number_of_pages ) ) { //print next button?
        $output .= " <li class='pagination-next'> <a href='?currentpage=$nextpage'> Next</a></li> ";
    }
    ?>
<?php
    $output .= "</ul>";
    print $output;
}
function get_content($more_link_text = '(more...)', $stripteaser = 0, $more_file = '')
{
    $content = get_the_content($more_link_text, $stripteaser, $more_file);
    $content = apply_filters('the_content', $content);
    $content = str_replace(']]>', ']]&gt;', $content);
    return $content;
}
?>
