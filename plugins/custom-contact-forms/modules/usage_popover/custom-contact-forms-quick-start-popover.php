<div class="ccf-popover" id="ccf-quick-start-popover" title="<?php _e('Quick Start Guide', 'custom-contact-forms'); ?>">
  <div class="popover-body">
  	<p><?php _e("If you want to quickly and easily create a form and insert it in to your WordPress site, then follow these simple instructions.", 'custom-contact-forms'); ?></p>
    <ol>
    	<li>
			<?php _e("First insert some default content by clicking the button below. This will create a standard form automatically that, if you wish, can be customized later.", 'custom-contact-forms'); ?>
        	<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
      			<input type="submit" class="insert-default-content-button" value="<?php _e("Insert Default Content", 'custom-contact-forms'); ?>" name="insert_default_content" />
    		</form>
        </li>
        <li><?php _e("Insert the code, [customcontact form=1], in any page or post. If you want to insert a form in to a theme file, locate the form in the form manager in the admin panel where you will find the theme display code.", 'custom-contact-forms'); ?></li>
    	<li><?php _e("Done! Pretty simple, huh? Custom Contact Forms allows you to create extremely customizable forms, you just have to familiarize yourself with the plugin; this guide is a great way to get started quickly but doesn't make use of the myriad of possibilities.", 'custom-contact-forms'); ?></li>
    </ol>
  </div>
</div>
