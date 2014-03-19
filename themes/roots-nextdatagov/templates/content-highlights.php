<?php
$args = array(
    'post_type'           => 'post',
    'ignore_sticky_posts' => 1,
    'tax_query'           => array(
        'relation' => 'AND',
        array(
            'taxonomy' => 'post_format',
            'field'    => 'slug',
            'terms'    => array('post-format-link', 'post-format-status', 'post-format-gallery'),
            'operator' => 'NOT IN'
        ),
        array(
            'taxonomy' => 'featured',
            'field'    => 'slug',
            'terms'    => array('highlights'),
            'operator' => 'IN'
        )
    ),
    'posts_per_page'      => 5);

if (is_category()) $args['cat'] = get_query_var('cat');

$highlight_posts = new WP_Query($args);

if (($highlight_posts->have_posts())):
    ?>

    <section id="highlights" class="wrap wrap-lightblue">
        <div class="container">

            <div class="page-header">
                <h1>Highlights</h1>
            </div>

            <div id="highlightsCarousel" class="carousel highlights slide">
                <?php
                /**
                 * reset counter
                 */
                $checkFirst = 0;
                ?>
                <!-- Carousel items -->
                <div id="highlightsCarouselInner" class="carousel-inner">
                    <?php while ($highlight_posts->have_posts()) : $highlight_posts->the_post(); ?>
                        <div
                            class="highlight item <?php echo(!$checkFirst++ ? 'active' : ''); ?> <?php get_category_by_slug(isset($slug) ? $slug : '') ?>">
                            <header>
                                <?php 
                                    // TODO: Style this! Adding "FALSE" to hide until properly styled
                                    if (FALSE && !is_category() && !is_archive()): ?>
                                    
                                    <h5 class="category">
                                        <?php 
                                            $category = get_the_category();
                                            echo $category[0]->cat_name; 
                                        ?>
                                    </h5>

                                <?php endif; ?>

                                <h2 class="entry-title" style="width:750px;float:left;"><?php the_title(); ?></h2>
                                <?php if (get_post_format() == 'image'): ?>
                                    <div class="dataset-link" style="clear:none;margin:0px; width:180px; float:right;">
                                        <a class="btn btn-default pull-right"
                                           href="<?php the_field('link_to_dataset'); ?>">
                                            <span class="glyphicon glyphicon-download"></span> View this Dataset
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </header>
                            <br clear="all"/>
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="featured-image col-md-4">
                                    <?php the_post_thumbnail('medium'); ?>
                                </div>
                            <?php endif; ?>

                            <article
                                class="<?php if (has_post_thumbnail()) : ?>col-md-8<?php else: ?>no-image<?php endif; ?>">
                                <?php the_content(); ?>
                            </article>
                        </div><!--/.highlight-->
                    <?php endwhile; ?>
                </div>
                <!-- Carousel nav -->
                <!--                <a class="carousel-control left" href="#highlightsCarousel" data-slide="prev">&lsaquo;</a>
                -->
                <!--                <a class="carousel-control right" href="#highlightsCarousel" data-slide="next">&rsaquo;</a>
                -->
                <?php
                /**
                 * reset counter
                 */
                $checkFirst = 0;
                ?>
                <ol class="carousel-indicators">
                    <?php while ($highlight_posts->have_posts()) : $highlight_posts->the_post(); ?>
                        <li data-target="#highlightsCarousel" data-slide-to="<?php echo $checkFirst; ?>"
                            class="<?php echo(!$checkFirst++ ? 'active' : ''); ?>"></li>
                    <?php endwhile; ?>
                </ol>
                <div class="carousel-expand more-link">
                    <a id="carouselExpand" href="javascript:void(0)" alt="Read more" class="collapsed">More<i></i></a>
                    <a id="carouselHide" href="javascript:void(0)" style="display: inline;" alt="hide">Less<i></i></a>
                </div>
            </div>
        </div>
        <!--/.container-->
    </section><!--/.wrap-lightblue-->

    <script type="text/javascript">
        jQuery(function ($) {
            $('.carousel').carousel({
                interval: 5000
            }).on('slid', carouselHeight);

            $('#carouselExpand').on('click', function () {
                $('#highlightsCarouselInner > .item.active').height('auto');
                $(this).hide();
                $('#carouselHide').show();
            })
            $('#carouselHide').on('click', function () {
                $('#highlightsCarouselInner > .item.active').height('300px');
                $(this).hide();
                $('#carouselExpand').show();
            })

            function carouselHeight() {
                $('#highlightsCarousel > .carousel-inner > .item').height(300);
                $('#carouselExpand').hide();
                $('#carouselHide').hide();

                var scrollHeight = $('#highlightsCarouselInner > .item.active')[0].scrollHeight;

                if (scrollHeight > 300) {
                    $('#carouselExpand').show();
                    $('#carouselHide').hide();
                }
            }

            carouselHeight();
        });
    </script>

<?php

endif;
wp_reset_postdata();