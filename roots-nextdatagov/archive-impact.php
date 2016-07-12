<div class="wrap container impact">
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

  <div class="row Impact-wrapper">
    <?php while (have_posts()) : the_post(); ?>
      <div class="col-sm-6 col-md-4">
        <div class="thumbnail">
          <img class="impact-icon" src="<?php echo get_field("thumbnail"); ?>" alt="<?php the_title(); ?>" />
          <div class="caption">
            <h3 class="impact-title"><?php the_title(); ?></h3>
            <div class="impact-content">
              <?php if($agency = get_field("agency_name")): ?>
              <p class="show-on-modal">
                <strong>Agency:</strong>
                <em><?php echo esc_html($agency); ?></em>
              </p>
              <?php endif; ?>

              <?php if ($contact = get_field("contact_email_url")): ?>
              <p class="show-on-modal">
                <strong>Contact:</strong>
                <?php if (is_email($contact)): ?>
                  <a href="mailto:<?php echo sanitize_email($contact) ?>?subject=data.gov Impact: <?php the_title() ?>">
                    <?php echo sanitize_email($contact) ?>
                  </a>
                <?php else: ?>
                  <a target="_blank" href="<?php echo esc_url($contact) ?>"><?php echo esc_url($contact) ?></a>
                <?php endif;?>
              </p>
              <?php endif;?>

              <?php if ($dataset_url = get_field("dataset_url")): ?>
              <p class="show-on-modal">
                <strong>Dataset:</strong>
                <a target="_blank" href="<?php echo esc_url($dataset_url); ?>"><?php echo esc_url($dataset_url);?></a>
              </p>
              <?php endif; ?>

              <?php /* $categs = get_the_category();
              if (sizeof($categs)):?>
              <p>
                <strong>Categories:</strong>
                <ul>
                  <?php
                  foreach($categs as $cat){
                    echo '<li>'.$cat->name.'</li>';
                  }?>
                </ul>
              </p>
              <?php endif; */?>

              <div class="show-on-modal">
                <?php the_content()?>
              </div>

              <div class="hide-on-modal">
                <?php
                remove_filter('get_the_excerpt', 'wp_trim_excerpt');
                add_filter('get_the_excerpt', 'datagov_custom_keep_my_links');
                $more_tag = strpos($post->post_content, '<!--more-->');
                ($more_tag) ? the_content('Continued') : the_excerpt();
                ?>
              </div>

            </div>
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

<!-- Modal -->
<div class="modal fade" id="impactModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="gridSystemModalLabel">Modal title</h4>
      </div>
      <div class="modal-body row">
        <div class="col-md-6 col-lg-6 impact-img"></div>
        <div class="col-md-6 col-lg-6 impact-content"></div>
      </div>
      <div class="modal-footer">
<!--        <a href="" target="_blank" class="go-to-impact btn btn-primary pull-left">Go to impact...</a>-->
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
