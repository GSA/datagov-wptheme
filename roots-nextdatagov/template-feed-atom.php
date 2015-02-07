<?php
/**
 * Atom Feed Template for displaying Atom Posts feed.
 *
 * @package WordPress
 */
header('Content-Type: ' . feed_content_type('atom') . '; charset=' . get_option('blog_charset'), true);
$more = 1;
echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>';
/** This action is documented in wp-includes/feed-rss2.php */
do_action( 'rss_tag_pre', 'atom' );
?>
<feed xmlns="http://www.w3.org/2005/Atom" xml:lang="en" >
    <?php
    do_action( 'atom_ns' );
?>
<title type="text"><?php bloginfo_rss('name'); wp_title_rss(); ?></title> <subtitle type="text"><?php bloginfo_rss("description") ?></subtitle> <updated><?php echo mysql2date('Y-m-d\TH:i:s\Z', get_lastpostmodified('GMT'), false); ?></updated> <link rel="alternate" type="<?php bloginfo_rss('html_type'); ?>" href="<?php bloginfo_rss('url') ?>" /> <id><?php bloginfo_rss('url') ?><?php bloginfo('atom_url'); ?></id><link rel="self" type="application/atom+xml" href="<?php self_link(); ?>" />
	<?php
    $category_name= "";
    if(!empty($wp_query->query['category_name']))
        $category_name = "/".$wp_query->query['category_name'];
    $count_posts =  $wp_query->found_posts;
    echo '<link rel="first" href="'.get_bloginfo('url').$category_name.'/feed/atom/" ></link>'."\n";
    $postsperpage = get_option('posts_per_rss');
    $total_pages = ceil($count_posts/$postsperpage);
    $currentpage = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
    if ($currentpage > $total_pages)
        $currentpage = $total_pages;
    if ($currentpage < 1)
        $currentpage = 1;
    if ($currentpage > 1) {
        $prevpage = $currentpage - 1;
        echo '<link rel="previous" href="'.get_bloginfo('url').$category_name.'/feed/atom?paged='.$prevpage.'" ></link>'."\n";

    }
    if ($currentpage != $total_pages) {
        $nextpage = $currentpage + 1;
        echo '<link rel ="next" href="'.get_bloginfo('url').$category_name.'/feed/atom?paged='.$nextpage.'" ></link>'."\n";
        echo '<link rel ="last" href="'.get_bloginfo('url').$category_name.'/feed/atom?paged='.$total_pages.'" ></link>'."\n";
    }
	do_action( 'atom_head' );
	while ( have_posts() ) : the_post();
	?>
	<entry>
		<author>
			<name><?php the_author() ?></name>
			<?php $author_url = get_the_author_meta('url'); if ( !empty($author_url) ) : ?>
			<uri><?php the_author_meta('url')?></uri>
			<?php endif;
			do_action( 'atom_author' );
		?>
		</author>
		<title type="<?php html_type_rss(); ?>"><![CDATA[<?php the_title_rss() ?>]]></title>
		<link rel="alternate" type="<?php bloginfo_rss('html_type'); ?>" href="<?php the_permalink_rss() ?>" />
		<id><?php the_guid() ; ?></id>
		<updated><?php echo get_post_modified_time('Y-m-d\TH:i:s\Z', true); ?></updated>
		<published><?php echo get_post_time('Y-m-d\TH:i:s\Z', true); ?></published>
		<?php the_category_rss('atom') ?>
		<summary type="<?php html_type_rss(); ?>"><![CDATA[<?php the_excerpt_rss(); ?>]]></summary>
<?php if ( !get_option('rss_use_excerpt') ) : ?>
		<content type="<?php html_type_rss(); ?>" xml:base="<?php the_permalink_rss() ?>"><![CDATA[<?php the_content_feed('atom') ?>]]></content>
<?php endif; ?>
	<?php atom_enclosure();
	do_action( 'atom_entry' );
		?>
	</entry>
	<?php endwhile ; ?>
</feed>
