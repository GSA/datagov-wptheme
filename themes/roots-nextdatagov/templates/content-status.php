<article <?php post_class(); ?>>	
  <header>
    <div class="tweet-author">
    	<a class="fa fa-twitter" class="tweet-permalink" href="<?php the_field('link_to_tweet'); ?>">
    		<span class="sr-only">Tweet</a>
    	</a>
    	<a class="author-link" href="https://twitter.com/<?php the_field('twitter_handle'); ?>">
	        <span class="author-image">
	            <img alt="" src="<?php the_field('twitter_photo'); ?>">
	        </span>
	        <div>
	        <span class="author-name">
	            <?php the_field('persons_name'); ?>            
	        </span>
  				<span class="author-handle">
  		            @<?php the_field('twitter_handle'); ?>            
  		        </span>	        
  		    </div>
    	</a>
         
    </div>
  </header>

  <div class="tweet-body">
        <?php the_content('Read the rest of this entry »'); ?>
  </div>

</article>
