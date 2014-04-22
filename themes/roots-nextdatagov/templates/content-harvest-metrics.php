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

        <div style="">
            <?php the_content(); ?>
        </div>
        <?php
        $ckan          = new CKAN_Harvest_Stats;
        $organizations = $ckan->getContent();
        ?>

        <?php foreach ($organizations as $organization): ?>
            <?php if (!$organization->name) {
                continue;
            } ?>
            <?php
            if (sizeof($organization->harvest_results)): ?>
                <dl class="harvest-stats <?php echo $organization->name ?>">
                    <dt><?php echo $organization->title ?> </dt>
                    <?php /* echo sizeof($organization->harvest_results) ?> harvest result(s): */ ?>
                    <?php
                    foreach ($organization->harvest_results as $harvest) : ?>
                        <dd style="margin-left:25px;">
                            <strong>Source</strong> <?php echo $harvest->title ?><br/>
                            <strong>Total datasets</strong> <?php echo $harvest->total_datasets ?></br >
                            <strong>Url</strong> <a href="<?php echo $harvest->url ?>"
                                                    target="_blank"><?php echo $harvest->url ?></a><br/>
                            <br/><em>Latest Harvest Job</em><br/>
                            <strong>Run on</strong> <?php echo $harvest->gather_finished ?><br/>
                            <?php if ($harvest->errored): ?>
                                <strong>Errors</strong> <?php echo $harvest->errored ?><br/>
                            <?php endif; ?>
                            <?php if ($harvest->added): ?>
                                <strong>Additions</strong> <?php echo $harvest->added ?><br/>
                            <?php endif; ?>
                            <?php if ($harvest->deleted): ?>
                                <strong>Deletions</strong> <?php echo $harvest->deleted ?><br/>
                            <?php endif; ?>
                            <?php if ($harvest->updated): ?>
                                <strong>Updates</strong> <?php echo $harvest->updated ?><br/>
                            <?php endif; ?>
                            <?php /*
                            <strong>Status:</strong> <?php echo $harvest->status ?><br/>
                            <strong>Job count:</strong> <?php echo $harvest->job_count ?><br/>
                            <strong>Total Datasets:</strong> <?php echo $harvest->total_datasets ?><br/>
                            <strong>Gather started:</strong> <?php echo $harvest->gather_started ?><br/>
                            <strong>Gather finished:</strong> <?php echo $harvest->gather_finished ?><br/>
                            <?php if (sizeof($harvest->metas)): ?>
                                <strong>Metas:</strong>
                                <ul>
                                    <?php foreach ($harvest->metas as $meta): ?>
                                        <li>
                                            <strong><?php echo $meta->key ?>:</strong>&nbsp;
                                            <?php echo $meta->value ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                                <br/>
                            <?php endif; ?>
 */
                            ?>
                        </dd>
                    <?php endforeach; ?>
                </dl>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>