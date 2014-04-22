<div class="subnav banner">
    <div class="container">
        <nav role="navigation" class="topic-subnav">
            <ul class="nav navbar-nav">
                <?php
                // show Links associated to a community
                // we need to build $args based either term_name or term_slug
                if (!empty($term_slug)) {
                    $args = array(
                        'category_name' => $term_slug,
                        'categorize'    => 0,
                        'title_li'      => 0,
                        'orderby'       => 'rating'
                    );
                    wp_list_bookmarks($args);
                    if (strcasecmp($term_name, $term_slug) != 0) {
                        $args = array(
                            'category_name' => $term_name,
                            'categorize'    => 0,
                            'title_li'      => 0,
                            'orderby'       => 'rating'
                        );
                        wp_list_bookmarks($args);
                    }
                }
                ?>
            </ul>
        </nav>
    </div>
</div>

<div class="single">
    <div class="container">
        <?php
        while (have_posts()) {
            the_post();
            ?>

            <div id="appstitle" class="Appstitle" style="margin-left:-20px;"><?php the_title(); ?></div>

        <?php } ?>

        <div style=""> <?php the_content(); ?>    </div>
        <?php
        $ckan          = new CKAN_Harvest_Stats;
        $organizations = $ckan->getContent();
        ?>

        <?php foreach ($organizations as $organization): ?>
            <?php if (!$organization->name) {
                continue;
            } ?>
            <dl class="harvest-stats <?php echo $organization->name ?>">
                <dt><?php echo $organization->title ?></dt>

                <?php
                if (sizeof($organization->harvest_results)): ?>
                    <?php echo sizeof($organization->harvest_results) ?> harvest result(s):
                    <?php
                    foreach ($organization->harvest_results as $harvest) : ?>
                        <dd style="margin-left:25px;">
                            <em>Status:</em> <?php echo $harvest->status ?><br/>
                            <em>Job count:</em> <?php echo $harvest->job_count ?><br/>
                            <em>Total Datasets:</em> <?php echo $harvest->total_datasets ?><br/>
                            <em>Gather started:</em> <?php echo $harvest->gather_started ?><br/>
                            <em>Gather finished:</em> <?php echo $harvest->gather_finished ?><br/>
                            <?php if (sizeof($harvest->metas)): ?>
                                <em>Metas:</em>
                                <ul>
                                    <?php foreach ($harvest->metas as $meta): ?>
                                        <li>
                                            <em><?php echo $meta->key ?>:</em>&nbsp;
                                            <?php echo $meta->value ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                                <br/>
                            <?php endif; ?>
                        </dd>
                    <?php
                    endforeach;
                endif;?>
            </dl>
        <?php endforeach; ?>
    </div>
</div>