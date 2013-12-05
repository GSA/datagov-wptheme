<?php
                    while( have_posts() ) {
                        the_post();
                        ?>


      <?php the_title();?>

    <?php the_content();   ?>
    <?php }?>
                <?php $post = get_post('35665')?>
                <?php $post1 = get_post('40636')?>
                <?php $post2 = get_post('115892')?>
                <?php $post3 = get_post('115902')?>
                <div class="technical-wrapper">
                    <div class="inner">
                        <h2 class="pane-title block-title"><?php echo $post->post_title;?></h2>


                        <p><?php echo $post->post_content;?></p>


                    </div>



                    <p><?php echo $post1->post_content;?></p>
                    <p><?php echo $post2->post_content;?></p>
                    <p><?php echo $post3->post_content;?></p>


                </div>


