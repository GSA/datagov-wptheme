<div class="container">


<div id="contact-wrapper">
<div  class="contact-post">
<div class="text">


Do you have a question or feedback for Data.Gov?We're here for help. Please let us know how to serve you best:</div>
<div  class="separator"></div>
<div class="contact-question">
<span style="margin-left:10px;font-size:25px;"><img src="<?php echo get_bloginfo('template_directory'); ?>/assets/img/chat.png" >&nbsp;Ask a question</span>

</div>
<div class="contact-question">
<span style="margin-left:10px;font-size:25px;"><img src="<?php echo get_bloginfo('template_directory'); ?>/assets/img/bulb.png" >&nbsp;Suggest or Request<br/>
<span style="margin-left:40px;font-size:25px;"><span style="font-size:15px">share ideas/get idea</span></span>

</div>
<div class="contact-question">
<span style="margin-left:10px;font-size:25px;"><img src="<?php echo get_bloginfo('template_directory'); ?>/assets/img/hazard.png" >&nbsp;Report a Problem</span>

</div>
</div>
<div  class="contact-post">
<div class="contact-question" style="background-color: #ffffff; ">
<span style="margin-left:0px;text-transform:uppercase;font-size:15px;font-weight:bold;margin-bottom:5px;"><img src="<?php echo get_bloginfo('template_directory'); ?>/assets/img/bird.png" >&nbsp;Ask the open data community</span>

<div  class="separator"></div>
<span style="text-transform:uppercase;font-size:15px;">
Have a question about working with open data? Tap the expertise of the<br/> <a href="">Open data stack exchange community.</a></span>
<div  class="separator"></div>
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

    <h2 style="font-size:20px;"><a href="<?php echo $item['link']; ?>"><?php echo $item['title']; ?></a></h2>
    <p style="font-size:20px;"><?php echo date('F d, Y h:i A',strtotime($item['updated'])); ?></p>

    <?php endforeach; ?>
<?php endif; ?>


</div>

</div>
<div  class="contact-post" style="background-color: #ffffff;">
<div class="contact-question">

<span style="margin-left:0px;text-transform:uppercase;font-size:15px;font-weight:bold;"><img src="<?php echo get_bloginfo('template_directory'); ?>/assets/img/twitter.png" >&nbsp;Twitter</span>
<div  class="separator"></div>
<span style="text-transform:uppercase;font-size:15px;">
Engage the Data.gov on twitter or find the account for a specific<br/> <a href="">community or agency.</a></span>
<div  class="separator"></div>
    <?php echo do_shortcode( '[twitter-widget username="usdatagov" before_widget="
<div class="half-box">" after_widget="</div>
" before_title="
<h1>" after_title="</h1>
" errmsg="Uh oh!" hiderss="true" hidereplies="true" targetBlank="true" avatar="1" showXavisysLink="1" items="4" showts="60" title="Recent Tweets"]' ) ?>

</div>
</div>

<br/>
<br/>
<br/>
<br/>

<div style="clear:both; "></div>
<div class="contact-title"><img src="<?php echo get_bloginfo('template_directory'); ?>/assets/img/chat.png" >&nbsp;ASK A QUESTION</div>

<div class="horizontal_dotted_line_all"></div>
<div  class="contact-post2">
<div class="contact-text"><img src="<?php echo get_bloginfo('template_directory'); ?>/assets/img/bird.png" >&nbsp;Ask the community<br>
<span>(Stack Exchange)</span></div>
</div>
<div  class="contact-post2">
<div class="contact-text"><img src="<?php echo get_bloginfo('template_directory'); ?>/assets/img/message.png" >&nbsp;Contact a data Steward<br>
<span>(Private)</span></div>
</div>
<div  class="contact-post2">
<div class="contact-text"><img src="<?php echo get_bloginfo('template_directory'); ?>/assets/img/message.png" >&nbsp;<a href="/contact-us/">Contact Data.gov</a><br>
<span>(Private)</span></div>
</div>
<br>
<div class="contact-post3">
<div class="contact-post4">
</div>

<div class="contact-post5">
You can use this form privately contact the data gov team or feel free to email us directly at <a href="">info@data.gov</a>
</div>
<div class="contact-post6">
<select>
  <option value="volvo">What Kind of Inquiry is this?</option>
 
</select>
</div>
<div class="contact-post7">
Send
</div>
</div>

<br/>
<br/>
<br/>
<br/>
<div class="contact-title"><img src="<?php echo get_bloginfo('template_directory'); ?>/assets/img/bird.png" >&nbsp;Suggest or Request</div>

<div class="horizontal_dotted_line_all"></div>

<div class="contact-post8">
<div class="contact-text"><img src="<?php echo get_bloginfo('template_directory'); ?>/assets/img/github.png" >&nbsp;Suggest new Data.gov features</div></div>
<div class="contact-post8">
<div class="contact-text"><img src="<?php echo get_bloginfo('template_directory'); ?>/assets/img/message.png" >&nbsp;Request new Data</div></div>

<div style="clear:both; "></div>
<div class="contact-title"><img src="<?php echo get_bloginfo('template_directory'); ?>/assets/img/hazard.png" >&nbsp;Report a Problem</div>

<div class="horizontal_dotted_line_all"></div>
<div class="contact-post8">
<div class="contact-text"><img src="<?php echo get_bloginfo('template_directory'); ?>/assets/img/github.png" >&nbsp;Report a problem with Data.gov website</div></div>
<div class="contact-post8">
<div class="contact-text"><img src="<?php echo get_bloginfo('template_directory'); ?>/assets/img/message.png" >&nbsp;Report a problem with Specific Data</div></div>