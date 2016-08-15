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
          <div>Open data is fuel for innovators. It has the potential to generate more than&nbsp;<a
              href="http://www.mckinsey.com/insights/business_technology/open_data_unlocking_innovation_and_performance_with_liquid_information"
              target="_blank">$3 trillion a year</a> in additional value in sectors including finance, consumer
            products, health, energy and education, according to a recent study. These are just a few examples of
            companies leveraging open data. While we don’t endorse companies, we’re always interested in new examples:
            <a href="http://www.twitter.com/usdatagov" target="_blank">Share them on Twitter</a>.
          </div>
        </div>

      </div>
    </div>
  </div>

  <?php
    add_filter( 'the_content_more_link', 'excerpt_more_impact' );
  ?>

  <div class="row Impact-wrapper">
    <?php while (have_posts()) : the_post(); ?>
      <div class="col-sm-6 col-md-4">
        <div class="thumbnail">
          <img class="impact-icon" src="<?php echo get_field("thumbnail"); ?>" alt="<?php the_title(); ?>"/>
          <div class="caption">
            <h3 class="impact-title"><?php the_title(); ?></h3>
            <div class="impact-content">
              <?php if ($agency = get_field("agency_name")): ?>
                <p class="show-on-modal">
                  <strong>Agency:</strong>
                  <em><?php echo esc_html($agency); ?></em>
                </p>
              <?php endif; ?>

              <?php if ($contact = get_field("contact_email_url")): ?>
                <p class="show-on-modal">
                  <strong>Contact:</strong>
                  <?php if (is_email($contact)): ?>
                    <a
                      href="mailto:<?php echo sanitize_email($contact) ?>?subject=data.gov Impact: <?php the_title() ?>">
                      <?php echo sanitize_email($contact) ?>
                    </a>
                  <?php else: ?>
                    <a target="_blank" href="<?php echo esc_url($contact) ?>"><?php echo esc_url($contact) ?></a>
                  <?php endif; ?>
                </p>
              <?php endif; ?>

              <?php if ($dataset_url = get_field("dataset_url")): ?>
                <p class="show-on-modal">
                  <strong>Dataset:</strong>
                  <a target="_blank"
                     href="<?php echo esc_url($dataset_url); ?>"><?php echo esc_url($dataset_url); ?></a>
                </p>
              <?php endif; ?>

              <p class="show-on-modal">
                <strong>Permanent:</strong>
                <a href="<?php echo get_permalink(); ?>">
                  <?php echo get_permalink(); ?>
                </a>
              </p>

              <div class="show-on-modal">
                <?php $post = get_post();
                echo $post->post_content; ?>
              </div>

              <div class="hide-on-modal">
                <?php
                $more_tag = strpos($post->post_content, '<!--more-->');
                ($more_tag) ? the_content('[Read more...]') : the_excerpt();
                ?>
              </div>

            </div>
          </div>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="impactModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">&times;</span></button>
        <h4 class="modal-title text-right" id="gridSystemModalLabel">Modal title</h4>
      </div>
      <div class="modal-body row">
        <div class="col-md-6 col-lg-6 impact-img"></div>
        <div class="col-md-6 col-lg-6 impact-content"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
