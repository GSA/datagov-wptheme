<div class="container">
    <div id="contact-wrapper">
        <div  class="contact-post">
            <div class="text"> How can we help?</div>
            <div  class="separator"></div>
            <div class="contact-question"> <span style="margin-left:10px;font-size:25px;"><i class="bluetext fa fa-comment"></i>&nbsp;<a class="question" href="#question">Ask a question</a></span> </div>
            <div class="contact-question"> <span style="margin-left:10px;font-size:25px;"><i class="bluetext fa fa-lightbulb-o"></i>&nbsp;<a class="question" href="#suggest">Suggest or Request</a></div>
            <div class="contact-question"> <span style="margin-left:10px;font-size:25px;"><img src="<?php echo get_bloginfo('template_directory'); ?>/assets/img/hazard.png" alt="Report a problem" >&nbsp;<a class="question" href="#report">Report a Problem</a></span> </div>
        </div>
        <div  class="contact-post">
            <div class="contact-question" style="background-color: #ffffff; "> <span style="margin-left:0px;text-transform:uppercase;font-size:15px;font-weight:bold;margin-bottom:5px;"><i class="bluetext fa fa-stack-exchange"></i>&nbsp;Open Data Stack Exchange</span>
                <div  class="separator"></div>
                <span style="font-size:15px;line-height: 29px">Ask the community.</span>
                <div  class="separator"></div>
                <?php


                include_once(ABSPATH.WPINC.'/rss.php'); // path to include script
                $feed = fetch_rss('http://opendata.stackexchange.com/feeds/tag/data.gov'); // specify feed url
                $items = array_slice($feed->items, 0, 3); // specify first and last item

                ?>
                <?php if (!empty($items)) : ?>
                    <?php foreach ($items as $item) : ?>
                        <h2 style="font-size:16px;"><a href="<?php echo $item['link']; ?>"><?php echo $item['title']; ?></a></h2>
                        <p style="font-size:16px;"><?php echo date('F d, Y h:i A',strtotime($item['updated'])); ?></p>
                        <?php endforeach; ?>
                    <?php endif; ?>
            </div>
        </div>
        <div  class="contact-post" style="background-color: #ffffff;">
            <div class="contact-question"> <span style="margin-left:0px;text-transform:uppercase;font-size:15px;font-weight:bold;"><i class="bluetext fa fa-twitter"></i>&nbsp;Twitter</span>
                <div  class="separator"></div>
                <span style="font-size:15px;line-height: 29px"> Follow us at @usdatagov</span>
                <div  class="separator"></div>
                <?php echo do_shortcode( '[twitter-widget username="usdatagov" before_widget="<div class="half-box">" after_widget="</div>" hiderss="true" hidereplies="true" targetBlank="true" avatar="1" items="1" showXavisysLink="0" showts="60"]' ) ?> </div>
        </div>
        <div style="clear:both; "></div>
        <div class="contact-title"><i class="bluetext fa fa-comment"></i>&nbsp;<a name="question">ASK A QUESTION</a></div>
        <div class="horizontal_dotted_line_all"></div>
        <!--  <div  class="contact-post2">
                     <div class="contact-text"><i class="fa fa-stack-exchange"></i>&nbsp;<a href="http://opendata.stackexchange.com/">Ask the community</a><br>
                         <span class="greytext">(Stack Exchange)</span></div>
                 </div>
                 <div  class="contact-post2">
                     <div class="contact-text"><i class="fa fa-envelope"></i>&nbsp;Contact a data Steward<br>
                         <span class="greytext">(Private)</span></div>
                 </div> -->


        <br>
        <div class="contact-post3">
            <div class="contact-post4">
                <div class="contact-post5"> You can use this form to contact the data gov team or feel free to email us directly at <a style="color:#ffffff;text-decoration:underline;" href="mailto:data.gov.contactus@reisys.com">data.gov.contactus@reisys.com</a> </div>
                <?php
                query_posts( array( 'page_id' => 126647 ) ); // ID of the page including the form

                if ( have_posts() ) : while ( have_posts() ) : the_post();
                    the_content();
                endwhile; endif;

                wp_reset_query();
                ?>
            </div>
        </div>
        <br/>
        <br/>
        <br/>
        <br/>
        <div class="contact-title" style="text-transform:uppercase; "><i class="bluetext fa fa-lightbulb-o"></i>&nbsp;<a name="suggest">Suggest or Request</a></div>
        <div class="horizontal_dotted_line_all"></div>
        <div class="contact-post8">
            <div class="contact-text"><i class="fa fa-github"></i>&nbsp;<a href="https://github.com/GSA/data.gov/">Suggest new Data.gov features</a></div>
        </div>
        <div class="contact-post8">
            <div class="contact-text"><i class="fa fa-envelope"></i>&nbsp;<a href="https://explore.data.gov/nominate">Request new Data</a></div>
        </div>
        <div style="clear:both; "></div>
        <div class="contact-title" style="text-transform:uppercase; "><img src="<?php echo get_bloginfo('template_directory'); ?>/assets/img/hazard.png" alt="Report a problem" >&nbsp;<a name="report" >Report a Problem</a></div>
        <div class="horizontal_dotted_line_all"></div>
        <div class="contact-post8">
            <div class="contact-text"><i class="fa fa-github"></i>&nbsp;<a href="https://github.com/GSA/data.gov/">Report a problem with website</a></div>
        </div>
