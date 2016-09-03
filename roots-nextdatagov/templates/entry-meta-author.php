<?php
$additional_author_name = false;
if (function_exists('get_field')) {
  $additional_author_name = get_field('author_name');
}
$author_name = get_the_author();
$display_author_name = (!empty($additional_author_name)) ? $additional_author_name : $author_name;
if(!empty($display_author_name))
    $display_author_name = "By ".$display_author_name;
else
    $display_author_name = "";
?>
<div class="entry-meta" xmlns="//www.w3.org/1999/html">
    <time class="published" datetime="<?php echo get_the_time('c'); ?>"><?php echo get_the_date(); ?>&nbsp;&nbsp;<i><?php echo $display_author_name;?></i></time>
</div>


