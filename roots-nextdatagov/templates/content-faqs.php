<?php
$category = get_the_category();
$term_name = $category[0]->cat_name;
$term_slug = $category[0]->slug;
?>
<?php
$cat_name = $category[0]->cat_name;
$cat_slug = $category[0]->slug;
?>
<?php include('category-subnav.php'); ?>
<div class="single">
    <div class="container">

        <?php if($cat_slug=="ocean"){?>
        <div id="regionsidebar">
            <div class="inner">
                <h2 class="block-title">Community of Practice</h2>
                <div class="panecontent">
                    <div class="item-list">
                        <?php $post = get_post('40645')?>
                        <p><?php echo $post->post_content;?></p>
                    </div>
                </div>
            </div>
        </div>
        <?php }?>

        <div style="float: left; margin-right: 10px; padding: 10px; width: 70%;">
            <div class="inner">
                <h2 class="pane-title block-title">Frequently Asked Questions</h2>
                <div class="pane-content content">

                    <p>Following are some Frequently Asked Questions, we hope to add to this list as we hear from you.</p>
                </div>
            </div>
            <div class="sixteen columns">
                <div class=" view-display-id-ogpl_ocean_faq_block ">
                    <div class="view-header">
                        <a id="faq_top"></a>
                        <p>
                        <h2 style="color: #284A78; font-family: Georgia,Times New Roman,Times,serif;font-size: 142.85%;">Questions</h2>
                    </div>


                    <div class="item-list">
                        <ol>
                            <?php
                            global $cat_name;
                            $category = get_the_category(  );
                            $cat_name=$category[0]->slug;
                            //WordPress loop for custom post type
                            $i=0;
                            $my_query = new WP_Query("post_type=qa_faqs&posts_per_page=-1&faq_category=$cat_name");
                            while ($my_query->have_posts()) : $my_query->the_post(); ?>
                                <li  >
                                    <a href="#faq_<?php echo $i;?>" style="color: #4295B0;font: bold 12px Arial,Helvetica,sans-serif; "><?php the_title(); ?></a></li>
                                <?php $i++;endwhile;  wp_reset_query(); ?>
                        </ol>
                    </div>
                </div>

                <div class="separator-mini-700"> </div>
                <?php
//WordPress loop for custom post type
                $i=0;
                $my_query = new WP_Query("post_type=qa_faqs&posts_per_page=-1&faq_category=$cat_name");
                while ($my_query->have_posts()) : $my_query->the_post(); ?>
                    <a id="faq_<?php echo $i;?>"></a>
                    <div class="views-field views-field-nothing-1">
                <span class="field-content">
                <?php the_title(); ?>
                </span>
                    </div>
                    <span class="field-content">
                <p><?php the_content(); ?></p>
                <p>
                    <a class="faq-top" href="#faq_top">Return to Top</a>
                </p>
            </span>
                    <?php $i++;endwhile;  wp_reset_query(); ?>
            </div>
        </div>
    </div>