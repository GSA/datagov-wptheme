<?php include('category-subnav.php'); ?>


<?php
$thumbnail = $agency_name = $contact_email_url = $dataset_url = false;
if (function_exists('get_field')) {
  $thumbnail = get_field('thumbnail');
  $agency_name = get_field('agency_name');
  $contact_email_url = get_field('contact_email_url');
  $dataset_url = get_field('dataset_url');
}
?>

<div class="wrap container content-page">

  <?php while (have_posts()) : the_post(); ?>

    <?php if (has_category() && ($categ = get_the_category()) && ($categ[0]->slug !== 'uncategorized')): ?>

      <h1 class="page-title">
        <?php the_title(); ?>
      </h1>

    <?php endif; ?>

    <div class="content impact-full">
      <div class="col-md-6 col-lg-6">
        <?php if ($thumbnail): ?>
          <img class="thumbnail" src="<?php echo $thumbnail ?>" alt="<?php the_title(); ?>"/>
        <?php endif ?>
      </div>
      <div class="col-md-6 col-lg-6">
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

        <?php the_content(); ?>

      </div>
    </div>

    <?php wp_link_pages(array('before' => '<nav class="pagination">', 'after' => '</nav>')); ?>
  <?php endwhile; ?>
</div>
