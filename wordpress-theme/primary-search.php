			<div class="sixteen columns">
				<div class="next-logo">
					<div id="next-logo-title-bg" class="next-background"></div>
					<div class="next-object"><span id="next-logo-title">Data.Gov</span></div>
				</div>
				<div class="next-stats">
					<div class="next-background"></div>
					<div id="rotate-stats" class="next-object" data-strings='
    { "targets" : ["75,714 datasets", "Search 100 APIs", "75,714 datasets", "CKAN Powered"]}'>&nbsp;</div>
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
								<label for="search-textbox" class="hddn" title="Search Data.gov">Search Data.gov</label>
	  							<input id="search-textbox" role="search" class="next" name="q" type="text" />
								<input id="next-search-submit" type="submit" />
							</form>
	  					</div>
  					</div>
				</div>
				<div class="next-categories">
					<div class="next-background"></div>
					<div class="next-object">
						<ul>
							<li><a href="education">Education</a></li>
							<li><a href="energy">Energy</a></li>
							<li><a href="#" onclick="comingSoonCommunity()">Finance</a></li>
							<li><a href="development">Global Development</a></li>
							<li id="stop3"><a href="health">Health</a></li>
							<li><a href="research">Research</a></li>
							<li><a href="safety">Safety</a></li>
							<li><a href="#" onclick="comingSoon()">all communities</a></li>
						</ul>
					</div>
				</div>
			</div> <!-- sixteen columns -->

				<script>
		function comingSoon(){
			alert("This functionality coming soon.");
		}
		</script>

						<script>
		function comingSoonCommunity(){
			alert("This community is coming soon.");
		}
		</script>
