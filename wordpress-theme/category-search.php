			<div class="sixteen columns">
				<div class="next-logo">
					<div class="next-background"></div>
					<div class="next-object"><?php $category = get_the_category(); echo $category[0]->cat_name; ?></div>
				</div>
				<div class="next-search">
					<div class="next-background"></div>
					<div class="next-object">
						<div class="next-search-label">
							<label class="next" for="next-search-box">Search</label>
						</div>
						<div class="next-search-icon">
							<img src="<?php echo get_bloginfo('template_directory'); ?>/assets/search.png">
						</div>
						<div class="next-search-input">
							<form method="get" action="http://54.225.111.163/dataset">
	  							<input class="next" name="q" type="text" />
								<input type="submit" style="position: absolute; left: -9999px; width: 1px; height: 1px;"/>
							</form>
	  					</div>
  					</div>
				</div>
			</div> <!-- sixteen columns -->

			