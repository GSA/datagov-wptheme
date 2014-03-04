<?php

// SECURITY : Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

?>
<div class="wrap">

    <div id="icon-options-general" class="icon32"></div>
    <h2><?php _e('WP Sitemap Page', 'wp_sitemap_page'); ?></h2>

    <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">
            <!-- main content -->
            <div id="post-body-content">
                <div class="meta-box-sortables ui-sortable">
                    <div class="postbox">
                        <h3><span><?php _e('Settings', 'wp_sitemap_page'); ?></span></h3>

                        <div class="inside">

                            <p><?php _e('Please choose how you want to display the posts on the sitemap.', 'wp_sitemap_page'); ?></p>
                            <ul>
                                <li><?php echo sprintf(__('%1$s: title of the post.', 'wp_sitemap_page'), '<strong>{title}</strong>'); ?></li>
                                <li><?php echo sprintf(__('%1$s: URL of the post.', 'wp_sitemap_page'), '<strong>{permalink}</strong>'); ?></li>
                                <li><?php echo sprintf(__('%1$s: The year of the post, four digits, for example 2004.', 'wp_sitemap_page'), '<strong>{year}</strong>'); ?></li>
                                <li><?php echo sprintf(__('%1$s: Month of the year, for example 05.', 'wp_sitemap_page'), '<strong>{monthnum}</strong>'); ?></li>
                                <li><?php echo sprintf(__('%1$s: Day of the month, for example 28.', 'wp_sitemap_page'), '<strong>{day}</strong>'); ?></li>
                                <li><?php echo sprintf(__('%1$s: Hour of the day, for example 15.', 'wp_sitemap_page'), '<strong>{hour}</strong>'); ?></li>
                                <li><?php echo sprintf(__('%1$s: Minute of the hour, for example 43.', 'wp_sitemap_page'), '<strong>{minute}</strong>'); ?></li>
                                <li><?php echo sprintf(__('%1$s: Second of the minute, for example 33.', 'wp_sitemap_page'), '<strong>{second}</strong>'); ?></li>
                                <li><?php echo sprintf(__('%1$s: The unique ID # of the post, for example 423.', 'wp_sitemap_page'), '<strong>{post_id}</strong>'); ?></li>
                                <li><?php echo sprintf(__('%1$s: Category name. Nested sub-categories appear as nested directories in the URI.', 'wp_sitemap_page'), '<strong>{category}</strong>'); ?></li>
                            </ul>

                            <form method="post" action="options.php">
                                <?php settings_fields('wp-sitemap-page'); ?>
                                <table class="form-table">
                                    <tbody>
                                    <tr valign="top">
                                        <th scope="row">
                                            <label for="wsp_posts_by_category">
                                                <?php _e('How to display the posts', 'wp_sitemap_page'); ?>
                                            </label>
                                        </th>
                                        <td>
                                            <?php
                                            // determine the code to place in the textarea
                                            $wsp_posts_by_category = get_option('wsp_posts_by_category');
                                            if ($wsp_posts_by_category === false) {
                                                // this option does not exists
                                                $wsp_posts_by_category = '<a href="{permalink}">{title}</a>';

                                                // save this option
                                                add_option('wsp_posts_by_category', $textarea);
                                            } else {
                                                // this option exists, display it in the textarea
                                                $textarea = $wsp_posts_by_category;
                                            }
                                            ?>
                                            <textarea name="wsp_posts_by_category" id="wsp_posts_by_category"
                                                      rows="2" cols="50"
                                                      class="large-text code"><?php
                                                echo $textarea;
                                                ?></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">
                                            <label for="wsp_exclude_pages">
                                                <?php _e('Exclude pages', 'wp_sitemap_page'); ?>
                                            </label>
                                        </th>
                                        <td>
                                            <?php
                                            // Exclude some pages
                                            $wsp_exclude_pages = get_option('wsp_exclude_pages');
                                            ?>
                                            <input type="text" class="large-text code"
                                                   name="wsp_exclude_pages" id="wsp_exclude_pages"
                                                   value="<?php echo $wsp_exclude_pages; ?>"/>

                                            <p class="description"><?php _e('Just add the IDs, separated by a comma, of the pages you want to exclude.', 'wp_sitemap_page'); ?></p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">
                                            <?php _e('Exclude Custom Post Type', 'wp_sitemap_page'); ?>
                                        </th>
                                        <td>
                                            <?php
                                            // Is this CPT already excluded ?
                                            $wsp_exclude_cpt_page = get_option('wsp_exclude_cpt_page');
                                            $wsp_exclude_cpt_post = get_option('wsp_exclude_cpt_post');
                                            $wsp_exclude_cpt_archive = get_option('wsp_exclude_cpt_archive');
                                            $wsp_exclude_cpt_author = get_option('wsp_exclude_cpt_author');
                                            ?>
                                            <div>
                                                <label for="wsp_exclude_cpt_page">
                                                    <input type="checkbox"
                                                           name="wsp_exclude_cpt_page" id="wsp_exclude_cpt_page"
                                                           value="1" <?php echo($wsp_exclude_cpt_page == 1 ? ' checked="checked"' : ''); ?> />
                                                    <?php _e('Page', 'wp_sitemap_page'); ?>
                                                </label>
                                            </div>
                                            <div>
                                                <label for="wsp_exclude_cpt_post">
                                                    <input type="checkbox"
                                                           name="wsp_exclude_cpt_post" id="wsp_exclude_cpt_post"
                                                           value="1" <?php echo($wsp_exclude_cpt_post == 1 ? ' checked="checked"' : ''); ?> />
                                                    <?php _e('Post', 'wp_sitemap_page'); ?>
                                                </label>
                                            </div>
                                            <div>
                                                <label for="wsp_exclude_cpt_archive">
                                                    <input type="checkbox"
                                                           name="wsp_exclude_cpt_archive" id="wsp_exclude_cpt_archive"
                                                           value="1" <?php echo($wsp_exclude_cpt_archive == 1 ? ' checked="checked"' : ''); ?> />
                                                    <?php _e('Archive', 'wp_sitemap_page'); ?>
                                                </label>
                                            </div>
                                            <div>
                                                <label for="wsp_exclude_cpt_author">
                                                    <input type="checkbox"
                                                           name="wsp_exclude_cpt_author" id="wsp_exclude_cpt_author"
                                                           value="1" <?php echo($wsp_exclude_cpt_author == 1 ? ' checked="checked"' : ''); ?> />
                                                    <?php _e('Author', 'wp_sitemap_page'); ?>
                                                </label>
                                            </div>
                                            <?php
                                            // Get the CPT (Custom Post Type)
                                            $args = [
                                                'public'   => true,
                                                '_builtin' => false
                                            ];
                                            $post_types = get_post_types($args, 'names');

                                            // list all the CPT
                                            foreach ($post_types as $post_type) {

                                                // extract CPT object
                                                $cpt = get_post_type_object($post_type);

                                                // Is this CPT already excluded ?
                                                $wsp_exclude_cpt = get_option('wsp_exclude_cpt_' . $cpt->name);
                                                ?>
                                                <div>
                                                    <label for="wsp_exclude_cpt_<?php echo $cpt->name; ?>">
                                                        <input type="checkbox"
                                                               name="wsp_exclude_cpt_<?php echo $cpt->name; ?>"
                                                               id="wsp_exclude_cpt_<?php echo $cpt->name; ?>"
                                                               value="1" <?php echo($wsp_exclude_cpt == '1' ? ' checked="checked"' : ''); ?> />
                                                        <?php echo $cpt->label; ?>
                                                    </label>
                                                </div>
                                            <?php
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                                <?php
                                // idea to evolve : button to restaure initial code
                                ?>
                                <?php submit_button(); ?>
                            </form>

                        </div>
                        <!-- .inside -->
                    </div>
                    <!-- .postbox -->
                </div>
                <!-- .meta-box-sortables .ui-sortable -->
            </div>
            <!-- post-body-content -->
            <!-- sidebar -->
            <div id="postbox-container-1" class="postbox-container">
                <div class="meta-box-sortables">
                    <div class="postbox">
                        <h3><span><?php _e('About', 'wp_sitemap_page'); ?></span></h3>

                        <div style="padding:0 5px;">
                            <?php
                            $fr_lang = ['fr_FR', 'fr_BE', 'fr_CH', 'fr_LU', 'fr_CA'];
                            $is_fr = (in_array(WPLANG, $fr_lang) ? true : false);
                            // Get the URL author depending on the language
                            $url_author = ($is_fr === true ? 'http://tonyarchambeau.com/' : 'http://en.tonyarchambeau.com/');
                            ?>
                            <p><?php _e('To display the sitemap, just use [wp_sitemap_page] on any page or post.', 'wp_sitemap_page'); ?></p>
                            <hr/>
                            <p><?php printf(__('Plugin developed by <a href="%1$s">Tony Archambeau</a>.', 'wp_sitemap_page'), $url_author); ?></p>

                            <p><a href="<?php echo WSP_DONATE_LINK; ?>"><?php _e('Donate', 'wp_sitemap_page'); ?></a>
                            </p>
                            <?php
                            // Display the author for Russian audience
                            if (WPLANG == 'ru_RU') {
                                ?>
                                <p><?php printf(__('Translated in Russian by <a href="%1$s">skesov.ru</a>.', 'wp_sitemap_page'), 'http://skesov.ru/'); ?></p>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                    <!-- .postbox -->
                </div>
                <!-- .meta-box-sortables -->
            </div>
            <!-- #postbox-container-1 .postbox-container -->
        </div>
        <!-- #post-body .metabox-holder .columns-2 -->
        <br class="clear"/>
    </div>
    <!-- #poststuff -->
</div><!-- .wrap -->
