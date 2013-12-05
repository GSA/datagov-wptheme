<?php
/**
 * Created by JetBrains PhpStorm.
 * User: pshirodkar
 * Date: 12/5/13
 * Time: 11:12 AM
 * To change this template use File | Settings | File Templates.
 *
 * Template Name: RSS Feed
 */

include_once(ABSPATH.WPINC.'/rss.php'); // path to include script
$feed = fetch_rss('http://opendata.stackexchange.com/feeds/tag/data.gov'); // specify feed url
$items = array_slice($feed->items, 0, 3); // specify first and last item

?>

<?php if (!empty($items)) : ?>
<?php foreach ($items as $item) : ?>

    <h2><a href="<?php echo $item['link']; ?>"><?php echo $item['title']; ?></a></h2>
    <p><?php echo date('F d, Y h:i A',strtotime($item['updated'])); ?></p>

    <?php endforeach; ?>
<?php endif; ?>