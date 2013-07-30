
		<?php if ( ! is_singular( 'post' ) ) : ?>
			<div class="sixteen columns footer">
				<div class="twelve columns alpha">
					<p><a href="http://www.data.gov/accessibility">Accessibility</a> <a href="http://www.data.gov/data-policy">Data Policy</a> <a href="http://www.data.gov/privacy-policy">Privacy Policy</a> <a href="https://github.com/GSA/datagov-design/">Improve this site</a></p>
				</div>
				<div class="four columns omega right-align">
					<p>Next.Data.Gov</p>
				</div>
			</div>
		<?php endif; ?>

		</div> <!-- content -->

	</div><!-- container -->

	<?php if (is_home()) : ?>
<ol id="joyRideTipContent" data-joyride>
  <li data-id="next-logo-title">
		<h2>Early Preview</h2>
        <p>This is an early look at the future of Data.gov. The new experience showcases the wide range of information assets that exist within the federal government. We are actively making changes, so pardon the dust.</p>
  </li>
  <li data-id="search-textbox">
  		<h2>Search</h2>
        <p>Explore datasets from agencies across the federal government following the <a href="http://www.whitehouse.gov/the-press-office/2013/05/09/executive-order-making-open-and-machine-readable-new-default-government-">Open Data Executive Order</a>. Agencies that have successfully published their <a href="http://project-open-data.github.io/schema/">common core metadata</a> will appear in the search results. If you can't find what you are looking for, please visit <a href="http://data.gov">http://data.gov</a>.</p>
  </li>
  <li data-id="stop3">
  		<h2>Data Communities</h2>
        <p>You'll find resources on a wide range of topics from education to health. See examples of how companies across the US are using these datasets.</p>
  </li>
  <li data-id="posts" data-options="tipLocation:top;">
  		<h2>Rich Stream</h2>
        <p>Across Next.Data.Gov you will see rich streams that enable the Data.gov community to publish blog posts, feature tweets, highlight quotes in publications, and feature datasets.</p>
  </li>
  <li data-id="stop-disclaimer" data-text="Explore Next.Data.Gov">
  		<h2>Your Voice</h2>
        <p>Help shape the future of Data.Gov. <a href="#">Tell us what you think</a>. If you are a developer, <a href="#">contribute via GitHub</a>.</p>
  </li>
</ol>
<?php endif; ?>

<!-- End Document
================================================== -->
<?php wp_footer(); ?>
</body>
</html>