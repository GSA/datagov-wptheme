<?php $imagefile=get_field_object('field_5240b9c982f41'); ?>
<article <?php post_class( 'col-md-4 col-lg-4' ); ?>>
    <img class="scale-with-grid app-image" src="<?php echo $imagefile['value']['url']; ?>" alt="<?php echo $imagefile['value']['alt']; ?>">

    <header>
        <h4 class="app-title"><?php the_title(); ?></h4>
    </header>

    <div class="app-body">
        <?php the_content('Read the rest of this entry »'); ?>
    </div>
</article>
