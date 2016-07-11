<div class="wrap container">
  <?php if (!have_posts()) : ?>
    <div class="alert alert-warning">
      <?php _e('Sorry, no results were found.', 'roots'); ?>
    </div>
    <?php get_search_form(); ?>
  <?php endif; ?>

  <div class="category">
    <div class="intro">
      <div class="container">

        <div>
          <div>Open data is fuel for innovators. It has the potential to generate more than&nbsp;<a href="http://www.mckinsey.com/insights/business_technology/open_data_unlocking_innovation_and_performance_with_liquid_information" target="_blank">$3 trillion a year</a> in additional value in sectors including finance, consumer products, health, energy and education, according to a recent study. These are just a few examples of companies leveraging open data. While we don’t endorse companies, we’re always interested in new examples: <a href="http://www.twitter.com/usdatagov" target="_blank">Share them on Twitter</a>.</div>
        </div>

        <div style="display: block !important; margin:0 !important; padding: 0 !important" id="wpp_popup_post_end_element"></div>
      </div>
    </div>
  </div>

  <?php while (have_posts()) : the_post(); ?>
    <div class="col-sm-6 col-md-4">
      <div class="thumbnail">
        <img src="<?php echo get_field("thumbnail"); ?>" alt="...">
        <div class="caption">
          <h3><?php the_title(); ?></h3>
          <p>
            <strong>Agency:</strong>
            <?php echo get_field("agency_name"); ?>
          </p>
          <p>
            <strong>Contact:</strong>
            <?php echo get_field("contact_email_url"); ?>
          </p>
          <p>
            <strong>Dataset:</strong>
            <?php echo get_field("dataset_url"); ?>
          </p>
          <p>
            <?php
            remove_filter('get_the_excerpt', 'wp_trim_excerpt');
            add_filter('get_the_excerpt', 'datagov_custom_keep_my_links');
            $more_tag = strpos($post->post_content, '<!--more-->');
            ($more_tag) ? the_content('Continued') : the_excerpt();
            ?>
          </p>
          <!--          <p><a href="#" class="btn btn-primary" role="button">Button</a> <a href="#" class="btn btn-default" role="button">Button</a></p>-->
        </div>
      </div>
    </div>
    <!---->
    <!--    <article --><?php //post_class(); ?><!-- >-->
    <!--      <header>-->
    <!--        <h2 class="entry-title">-->
    <!--          <a id="--><?php //echo 'post-title-' . get_the_ID(); ?><!--" href="--><?php //echo generate_post_url($post->post_name); ?><!--">--><?php //the_title(); ?><!--</a>-->
    <!--        </h2>-->
    <!--        --><?php //get_template_part('templates/entry-meta-author'); ?>
    <!--      </header>-->
    <!--      <div class="entry-summary">-->
    <!--        <p>-->
    <!--          -->
    <!--        </p>-->
    <!--        -->
    <!--      </div>-->
    <!--    </article>-->

  <?php endwhile; ?>
</div>

<!--<div class="row">-->
<!--  <div class="col-sm-12 col-md-4">-->
<!--    <div class="thumbnail">-->
<!--      <img src="https://collegescorecard.ed.gov/img/hero-large.jpg" alt="...">-->
<!--      <div class="caption">-->
<!--        <h3>College Scorecard</h3>-->
<!--        <p>Department of Education</p>-->
<!--        <p><a href="mailto:scorecarddata@rti.org" class="btn btn-primary" role="button">Contact</a> <a href="/impacts/college-scorecard/" class="btn btn-default" role="button">Read more...</a></p>-->
<!--      </div>-->
<!--    </div>-->
<!--  </div>-->
<!--  <div class="col-sm-12 col-md-4">-->
<!--    <div class="thumbnail">-->
<!--      <img src="https://nebula.wsimg.com/76bc7274deb35b88aae0db199c44479a?AccessKeyId=1EC2C512A0E17DC8D90B&disposition=0&alloworigin=1" alt="...">-->
<!--      <div class="caption">-->
<!--        <h3>Open Data Summer Camp</h3>-->
<!--        <p>Department of Agriculture (USDA)</p>-->
<!--        <p><a href="mailto:Cynthia.Larkins@wdc.usda.gov" class="btn btn-primary" role="button">Contact</a> <a href="/impacts/open-data-summer-camp/" class="btn btn-default" role="button">Read more...</a></p>-->
<!--      </div>-->
<!--    </div>-->
<!--  </div>-->
<!--  <div class="col-sm-12 col-md-4">-->
<!--    <div class="thumbnail">-->
<!--      <img src="https://nebula.wsimg.com/76bc7274deb35b88aae0db199c44479a?AccessKeyId=1EC2C512A0E17DC8D90B&disposition=0&alloworigin=1" alt="...">-->
<!--      <div class="caption">-->
<!--        <h3>Open Data Summer Camp</h3>-->
<!--        <p>Department of Agriculture (USDA)</p>-->
<!--        <p><a href="mailto:Cynthia.Larkins@wdc.usda.gov" class="btn btn-primary" role="button">Contact</a> <a href="/impacts/open-data-summer-camp/" class="btn btn-default" role="button">Read more...</a></p>-->
<!--      </div>-->
<!--    </div>-->
<!--  </div>-->
<!--</div>-->

<?php /* if ($wp_query->max_num_pages > 1) : ?>
    <nav class="post-nav">
      <ul class="pager">
        <li class="previous"><?php next_posts_link(__('&larr; Older posts', 'roots')); ?></li>
        <li class="next"><?php previous_posts_link(__('Newer posts &rarr;', 'roots')); ?></li>
      </ul>
    </nav>
  <?php endif; */ ?>
</div>
