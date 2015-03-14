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

<?php
$ckan          = new CKAN_Harvest_Stats;
$organizations = $ckan->getContent();

$totalNumber = 0;
foreach ($organizations as $organization) {
    if ($organization->name && sizeof($organization->harvest_results)) {
        $totalNumber++;
    }
}
?>

<div class="single">
    <div class="container">

        <div>
        <?php the_content(); ?>
        </div>
        <div>
            Total Agencies set-up in Data.gov to harvest from their agency Jsons: <?php echo $totalNumber; ?>
        </div>
        <?php foreach ($organizations as $organization): ?>
            <?php if (!$organization->name) {
                continue;
            } ?>
            <?php
            if (sizeof($organization->harvest_results)): ?>
                <dl id="<?php echo $organization->name ?>" class="harvest-stats <?php echo $organization->name ?>">
                    <dt><?php echo $organization->title ?> </dt>
                    <?php /* echo sizeof($organization->harvest_results) ?> harvest result(s): */ ?>
                    <?php
                    foreach ($organization->harvest_results as $harvest) : ?>
                        <dd style="margin-left:25px;">
                            <strong>Source</strong> <span><?php echo $harvest->title ?></span><br/>
                            <strong>Total datasets</strong> <span><?php echo $harvest->total_datasets ?></span></br >
                            <strong>Url</strong><span> <a href="<?php echo $harvest->url ?>" target="_blank"><?php echo $harvest->url ?></a></span><br/>
                            <br/><em style="font-weight: bold;">Latest Harvest Job</em><br/>
                            <strong>Run on</strong><span><?php echo date("l, d-M-Y H:i:s T", strtotime($harvest->gather_finished)) ?></span><br/>
                            <?php if ($harvest->errored): ?>
                                <strong>Errors</strong> <?php echo $harvest->errored ?><br/>
                            <?php endif; ?>
                            <?php if ($harvest->added): ?>
                                <strong>Additions</strong><span><?php echo $harvest->added ?></span><br/>
                            <?php endif; ?>
                            <?php if ($harvest->deleted): ?>
                                <strong>Deletions</strong><span><?php echo $harvest->deleted ?></span><br/>
                            <?php endif; ?>
                            <?php if ($harvest->updated): ?>
                                <strong>Updates</strong><span><?php echo $harvest->updated ?></span><br/>
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
