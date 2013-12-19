<div class="ccf-popover" id="ccf-usage-popover" title="<?php _e('Plugin Usage Popover', 'custom-contact-forms'); ?>">
  <div class="popover-body">
    <!--<ul id="popover-tof">
      <li><a href="#pop-forms">
        <?php _e('Forms', 'custom-contact-forms'); ?>
        </a></li>
      <li><a href="#pop-fields">
        <?php _e('Fields', 'custom-contact-forms'); ?>
        </a></li>
	  <li><a href="#pop-fixed-fields">
        <?php _e('Fixed Fields', 'custom-contact-forms'); ?>
        </a></li>
      <li><a href="#pop-field-options">
        <?php _e('Field Options', 'custom-contact-forms'); ?>
        </a></li>
      <li><a href="#pop-styles">
        <?php _e('Styles', 'custom-contact-forms'); ?>
        </a></li>
      <li><a href="#pop-custom-html">
        <?php _e('Custom HTML Forms', 'custom-contact-forms'); ?>
        </a></li>
      <li><a href="#pop-import-export">
        <?php _e('Import/Export', 'custom-contact-forms'); ?>
        </a></li>
      <li><a href="#pop-form-submissions">
        <?php _e('Form Submissions', 'custom-contact-forms'); ?>
        </a></li>
    </ul>-->
    <h3>
      <?php _e('Introduction', 'custom-contact-forms'); ?>
    </h3>
    <p>
      <?php _e("CCF is an extremely intuitive plugin allowing you to create any type of contact form you can imagine. CCF is very user friendly but with possibilities comes complexity. It is recommend that you click the button below to create default fields, field options, and forms.
                    The default content will help you get a feel for the amazing things you can accomplish with this plugin. This popover only shows automatically the first time you visit the admin page;", 'custom-contact-forms'); ?>
      <b>
      <?php _e("if you want to view this popover again, click the 'Show Plugin Usage Popover'", 'custom-contact-forms'); ?>
      </b>
      <?php _e("in the instruction area of the admin page.", 'custom-contact-forms'); ?>
    </p>
    <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
      <input type="submit" class="insert-default-content-button" value="<?php _e("Insert Default Content", 'custom-contact-forms'); ?>" name="insert_default_content" />
    </form>
    <p>
      <?php _e("Below is a basic usage outline of the four pillars of this plugin: fields, field options, styles, and forms. Another useful part of the plugin is the 'Custom HTML Feature' which allows you to write the form HTML yourself using the plugin simply as a form processor; this is great if you are a web developer with HTML experience. Also explained below is the 'Saved Form Submissions' feature which displays all form submissions in the admin panel.", 'custom-contact-forms'); ?>
    </p>
    <ul>
      <li>
        <h3>
          <?php _e("Fields", 'custom-contact-forms'); ?>
          <a name="pop-fields"></a></h3>
        <p>
          <?php _e("Fields are the actual input boxes in which users enter their information. There are six types of fields that you can attach to your forms.!", 'custom-contact-forms'); ?>
        </p>
        <ul>
          <li><span>
            <?php _e("Text:", 'custom-contact-forms'); ?>
            </span>
            <div>
              <input type="text" class="ccf-width200" value="<?php _e("This is a text field", 'custom-contact-forms'); ?>" />
            </div>
          </li>
          <li><span>
            <?php _e("Date:", 'custom-contact-forms'); ?>
            </span>
            <div>
              <?php _e("A Date field looks exactly the same as a Text field. But when a user clicks a Date field, a calender popover is displayed allowing a user to easily insert a date in to your form.", 'custom-contact-forms'); ?>
            </div>
          </li>
          <li><span>
            <?php _e("File:", 'custom-contact-forms'); ?>
            </span>
            <div>
              <input type="file" class="ccf-width200" /> <?php _e("This is a file field. Attaching on of these to your forms allows users to send files to you (files are also stored on your server). You can set a max upload size as well as limit the allowed file types.", 'custom-contact-forms'); ?>
            </div>
          </li>
          <li><span>
            <?php _e("Textarea:", 'custom-contact-forms'); ?>
            </span>
            <div>
              <textarea class="ccf-width2000"><?php _e("This is a text field", 'custom-contact-forms'); ?></textarea>
            </div>
          </li>
          <li><span>
            <?php _e("Dropdown:", 'custom-contact-forms'); ?>
            </span>
            <div>
              <select>
                <option>
                <?php _e("This is a dropdown field", 'custom-contact-forms'); ?>
                </option>
                <option>
                <?php _e("Field Option 2!", 'custom-contact-forms'); ?>
                </option>
                <option>
                <?php _e("Field Option 3!", 'custom-contact-forms'); ?>
                </option>
                <option>
                <?php _e("Field Option 4!", 'custom-contact-forms'); ?>
                </option>
                <option>
                <?php _e("Unlimited # of options allowed", 'custom-contact-forms'); ?>
                </option>
              </select>
            </div>
          </li>
          <li><span>
            <?php _e("Radio:", 'custom-contact-forms'); ?>
            </span>
            <div>
              <input type="radio" selected="selected" />
              <?php _e("A radio field", 'custom-contact-forms'); ?>
              <input type="radio" selected="selected" />
              <?php _e("Field Option 2", 'custom-contact-forms'); ?>
              <input type="radio" selected="selected" />
              <?php _e("Field Option 3", 'custom-contact-forms'); ?>
            </div>
          </li>
          <li><span>
            <?php _e("Checkbox:", 'custom-contact-forms'); ?>
            </span>
            <div>
              <input type="checkbox" value="1" />
              <?php _e("This is a checkbox field", 'custom-contact-forms'); ?>
            </div>
          </li>
          <li><span>
            <?php _e("(advanced) Hidden:", 'custom-contact-forms'); ?>
            </span>
            <?php _e("These fields are hidden (obviously), they allow you to pass hidden information within your forms. Great for using other form processors like Aweber or InfusionSoft.", 'custom-contact-forms'); ?>
          </li>
        </ul>
        <p>
          <?php _e("There are a variety of different options that you can use when creating a field,", 'custom-contact-forms'); ?>
          <span class="red">*</span>
          <?php _e("denotes something required:", 'custom-contact-forms'); ?>
        </p>
        <ul>
          <li><span class="red">*</span> <span>
            <?php _e("Slug:", 'custom-contact-forms'); ?>
            </span>
            <?php _e("A slug is simply a way to identify your field. It can only contain underscores, letters, and numbers and must be unique.", 'custom-contact-forms'); ?>
          </li>
          <li><span>
            <?php _e("Field Label:", 'custom-contact-forms'); ?>
            </span>
            <?php _e("The field label is displayed next to the field and is visible to the user.", 'custom-contact-forms'); ?>
          </li>
          <li><span class="red">*</span> <span>
            <?php _e("Field Type:", 'custom-contact-forms'); ?>
            </span>
            <?php _e("The six field types you can choose from are explained above.", 'custom-contact-forms'); ?>
          </li>
          <li><span>
            <?php _e("Initial Value:", 'custom-contact-forms'); ?>
            </span>
            <?php _e("This is the initial value of the field. If you set the type as checkbox, it is recommend you set this to what the checkbox is implying. For example if I were creating the checkbox 'Are you human?', I would set the initial value to 'Yes'. If you set the field type as 'Dropdown' or 'Radio', you should enter the slug of the field option you would like initially selected (or just leave it blank and the first option attached will be selected).", 'custom-contact-forms'); ?>
          </li>
          <li><span>
            <?php _e("Max Length:", 'custom-contact-forms'); ?>
            </span>
            <?php _e("This allows you to limit the amount of characters a user can enter in a field (does not apply to textareas as of version 3.5.5)", 'custom-contact-forms'); ?>
          </li>
          <li><span>
            <?php _e("Required Field:", 'custom-contact-forms'); ?>
            </span>
            <?php _e("If a field is required and a user leaves it blank, the plugin will display an error message explaining the problem. The user will then have to go back and fill in the field properly.", 'custom-contact-forms'); ?>
          </li>
          <li><span>
            <?php _e("Field Instructions:", 'custom-contact-forms'); ?>
            </span>
            <?php _e("If this is filled out, a stylish tooltip popover displaying this text will show when the field is selected. This will only work if JQuery is enabled in general options.", 'custom-contact-forms'); ?>
          </li>
		  <li><span>
            <?php _e("Field Error:", 'custom-contact-forms'); ?>
            </span>
            <?php _e("If a user leaves this field blank and the field is required, this error message will be shown. A generic default will show if left blank.", 'custom-contact-forms'); ?>
          </li>
		  <li><span>
            <?php _e("(advanced) Field Class:", 'custom-contact-forms'); ?>
            </span>
            <?php _e("This allows you to assign a CSS class to an individual field without affecting anything else. This is a great way to take the customization of your form to the next level.", 'custom-contact-forms'); ?>
          </li>
          <li><span>
            <?php _e("Field Options:", 'custom-contact-forms'); ?>
            </span>
            <?php _e("After you create a field, if it's field type is radio or dropdown, you can attach field options to the field. Field options are explained in the next section.", 'custom-contact-forms'); ?>
          </li>
        </ul>
	  </li>
	  <li>
		<h3><?php _e('Fixed Fields', 'custom-contact-forms'); ?>
          <a name="pop-fixed-fields"></a></h3>
        <p>
          <?php _e("The last important thing related to fields are <span>Fixed Fields</span>. Fixed Fields are special fields that come already created within the plugin such as the captcha spam blocker and email field. Fixed Fields do special things that you wouldn't be able to accomplish with normal fields; they cannot be deleted or created. For example, if you use the fixedEmail field, as opposed to creating your own email field. the users email will be checked to make sure it is valid, if it isn't a form error will be displayed. Below is a list of fixed fields and their descriptions.", 'custom-contact-forms'); ?>
        </p>
		<ul>
		  <li><span><?php _e('fixedEmail:', 'custom-contact-forms'); ?></span> <?php _e("When attached to a form and set to required, this field will validate a user's email addresses. If a user's email is not valid, an error will be shown.", 'custom-contact-forms'); ?></li>
		  <li><span><?php _e('captcha:', 'custom-contact-forms'); ?></span> <?php _e('This field helps prevent spam by prompting users to copy numbers displayed on an image. If a user copies the numbers incorrectly, the form will show an error.', 'custom-contact-forms'); ?></li>
		  <li><span><?php _e('fixedWebsite:', 'custom-contact-forms'); ?></span> <?php _e("When attached to a form and set to required, this field will validate a user's website. If a user's website is not valid, an error will be shown.", 'custom-contact-forms'); ?></li>
		  <li><span><?php _e('emailSubject:', 'custom-contact-forms'); ?></span> <?php _e("This lets a user enter in an email subject that will carry over as the subject of the email sent to you on form completion", 'custom-contact-forms'); ?></li>
		  <li><span><?php _e('usaStates:', 'custom-contact-forms'); ?></span> <?php _e("This field displays a dropdown of all the states in the US.", 'custom-contact-forms'); ?></li>
		  <li><span><?php _e('allCountries:', 'custom-contact-forms'); ?></span> <?php _e("This field displays a dropdown of all the countries in the world.", 'custom-contact-forms'); ?></li>
		  <li><span><?php _e('resetButton:', 'custom-contact-forms'); ?></span> <?php _e("Attaching this field adds a reset button right next to the submit button in your form.", 'custom-contact-forms'); ?></li>
		  <li><span><?php _e('ishuman:', 'custom-contact-forms'); ?></span> <?php _e("This field helps prevent spam by prompting users to check a box to verify that they are human. If the box is left unchecked, an error will be displayed and the user will have to go back.", ''); ?></li>
		</ul>
      </li>
      <li>
        <h3>
          <?php _e('Field Options', 'custom-contact-forms'); ?>
          <a name="pop-field-options"></a></h3>
        <p>
          <?php _e("In the field section above, look at the radio or dropdown fields. See how they have multiple options within the field? Those are called Field Options. Field Options have their own manager. There are only three things you must fill in to create a field option.", 'custom-contact-forms'); ?>
        </p>
        <ul>
          <li><span class="red">*</span> <span>
            <?php _e("Slug:", 'custom-contact-forms'); ?>
            </span>
            <?php _e("Used to identify the field option, solely for admin purposes; must be unique, and contain only letters, numbers, and underscores. Example: 'slug_one'.", 'custom-contact-forms'); ?>
          </li>
          <li><span class="red">*</span> <span>
            <?php _e("Option Label:", 'custom-contact-forms'); ?>
            </span>
            <?php _e("This is what is shown to the user in the dropdown or radio field.", 'custom-contact-forms'); ?>
          </li>
          <li><span>
            <?php _e("Option Value:", 'custom-contact-forms'); ?>
            </span>
            <?php _e("This is the actual value of the option which isn't shown to the user. This can be the same thing as the label. An example pairing of label => value is: 'The color green' => 'green' or 'Yes' => '1'. The option value is behind the scences; unseen by the user, but when a user fills out the form, the option value is what is actually emailed to you and stored in the database. For dropdown fields the option value is optional, <span>for radio fields it is required</span>.", 'custom-contact-forms'); ?>
          </li>
		  <li><span>
            <?php _e("Is Dead Option:", 'custom-contact-forms'); ?>
            </span> <select><option><?php _e('Please Select:', 'custom-contact-forms'); ?></option><option><?php _e('Option 1', 'custom-contact-forms'); ?></option><option><?php _e('Option 2', 'custom-contact-forms'); ?></option></select><br />
            <?php _e('Dead options are only useful for required dropdown fields. The first field option "Please Select" would be a useful way to encorporate a dead option. Assume the field is required; if a user submitted that field and left it as "Please Select:", then the form would throw an error and the user would have to go back.', 'custom-contact-forms'); ?>
		  </li>
        </ul>
        <p>
          <?php _e("Once you create field options, you can attach them (in the field manager) to radio and dropdown fields (that are already created). It is important to remember that after you create a dropdown or radio field, they will not work until you attach one or more field options.", 'custom-contact-forms'); ?>
        </p>
      </li>
      <li>
        <h3>
          <?php _e('Forms', 'custom-contact-forms'); ?>
          <a name="pop-forms"></a></h3>
        <p>
          <?php _e("Forms bring everything together. Each form you create in the form manager shows a code to display that form in posts/pages as well as theme files. The post/page form display code looks like: [customcontact id=FORMID]. There are a number of parameters that you can fill out when creating and managing each of your forms.", 'custom-contact-forms'); ?>
        </p>
        <ul>
          <li><span class="red">*</span> <span>
            <?php _e("Slug:", 'custom-contact-forms'); ?>
            </span>
            <?php _e("A slug is simply a way to identify your form. It can only contain underscores, letters, and numbers and must be unique. Example 'my_contact_form'", 'custom-contact-forms'); ?>
          </li>
          <li><span>
            <?php _e('Form Title:', 'custom-contact-forms'); ?>
            </span>
            <?php _e("The form title is heading text shown at the top of the form to users. Here's an example: 'My Contact Form'.", 'custom-contact-forms'); ?>
          </li>
          <li><span class="red">*</span> <span>
            <?php _e('Form Method:', 'custom-contact-forms'); ?>
            </span>
            <?php _e("If you don't know what this is leave it as 'Post'. This allows you to change the way a form sends user information.", 'custom-contact-forms'); ?>
          </li>
          <li><span>
            <?php _e('Form Action:', 'custom-contact-forms'); ?>
            </span>
            <?php _e("This allows you to process your forms using 3rd party services or your own scripts. If you don't know what this is, then leave it blank. This is useful if you use a service like Aweber or InfusionSoft.", 'custom-contact-forms'); ?>
          </li>
          <li><span>
            <?php _e('Form Style:', 'custom-contact-forms'); ?>
            </span>
            <?php _e("This allows you to apply styles you create in the style manager to your forms. If you haven't created a custom style yet, just choose 'Default'.", 'custom-contact-forms'); ?>
          </li>
          <li><span>
            <?php _e('Submit Button Text:', 'custom-contact-forms'); ?>
            </span>
            <?php _e("Here, you can specify the text that shows on the submit button.", 'custom-contact-forms'); ?>
          </li>
          <li><span>
            <?php _e("Custom Code:", 'custom-contact-forms'); ?>
            </span>
            <?php _e("If unsure, leave blank. This field allows you to insert custom HTML directly after the starting form tag.", 'custom-contact-forms'); ?>
          </li>
          <li><span>
            <?php _e('Form Destination Email:', 'custom-contact-forms'); ?>
            </span>
            <?php _e("Specify the email address(es) that should receive all form submissions. Seperate multiple email addresses with semi-colons (ex: email1@gmail.com;email2@gmail.com;email3@gmail.com). If you leave this blank it will revert to the default specified in general settings. You can set forms not to send email in General Settings.", 'custom-contact-forms'); ?>
          </li>
          <li><span>
            <?php _e('Form Success Message:', 'custom-contact-forms'); ?>
            </span>
            <?php _e("Will be displayed in a popover after the form is filled out successfully when no custom success page is specified; if left blank it will use the default specified in general settings.", 'custom-contact-forms'); ?>
          </li>
          <li><span>
            <?php _e('Form Success Message Title:', 'custom-contact-forms'); ?>
            </span>
            <?php _e("Will be displayed in a popover when the form is filled out successfully when no custom success page is specified; if left blank it will use the default specified in general settings.", 'custom-contact-forms'); ?>
          </li>
          <li><span>
            <?php _e('Custom Success URL:', 'custom-contact-forms'); ?>
            </span>
            <?php _e("If this is filled out, users will be sent to this page when they successfully fill out the form. If it is left blank, a popover showing the form's 'success message' will be displayed on successful form submission.", 'custom-contact-forms'); ?>
          </li>
		  <li><span>
            <?php _e('Email From Name:', 'custom-contact-forms'); ?>
            </span>
            <?php _e("The email sent to you on form completion will be from this name. If this is left blank, it will default to the 'Default From Name' provided in General Settings.", 'custom-contact-forms'); ?>
          </li>
		  <li><span>
            <?php _e('Email Subject:', 'custom-contact-forms'); ?>
            </span>
            <?php _e("This is the subject of the email that will be sent to you on form completion. If this is left blank, it will default to the 'Default Email Subject' provided in General Settings.", 'custom-contact-forms'); ?>
          </li>
		  <li><span>
            <?php _e('Can View Form:', 'custom-contact-forms'); ?>
            </span>
            <?php _e("This lets you choose which types of users can view your form. If you want everyone to see the form, check all the boxes. If a user doesn't have access to the form, they will not be able to see it.", 'custom-contact-forms'); ?>
          </li>
          <li><span>
            <?php _e('Attach Fields:', 'custom-contact-forms'); ?>
            </span>
            <?php _e("After creating a form you are given the option to attach (and detach) fields to that specific form. Forms are useless until you attach fields.", 'custom-contact-forms'); ?>
          </li>
        </ul>
        <p>
          <?php _e("The form success message and success title apply to a popover that fades in after someone successfully completes a form (that does not have a custom success URL provided). The image below will help to give you a feel to how the popover will look and where the title and message actually show up.", 'custom-contact-forms'); ?>
        </p>
        <div class="ccf-success-popover-example"></div>
      </li>
      <li>
        <h3>
          <?php _e('Style Manager', 'custom-contact-forms'); ?>
          <a name="pop-styles"></a></h3>
        <p>
          <?php _e("The style manager allows you to customize the appearance of forms without any knowledge of CSS. There are a ton of parameters you can fill out with each style and all of them are pretty self-explanitory. After you create a style, you need to go to the form manager and set the form style to the new style you created (the slug will be what shows in the 'Form Style' dropdown).", 'custom-contact-forms'); ?>
        </p>
        <p>
          <?php _e("The image below will help you better understand how each style option will change your forms.", 'custom-contact-forms'); ?>
        </p>
        <div class="ccf-style-example"></div>
      </li>
      <li>
        <h3>
          <?php _e('Custom HTML Forms Feature (advanced)', 'custom-contact-forms'); ?>
          <a name="pop-custom-html"></a></h3>
        <p>
          <?php _e("If you know HTML and simply want to use this plugin to process form requests, this feature is for you. The following HTML is a the framework to which you must adhere. In order for your form to work you MUST do the following:", 'custom-contact-forms'); ?>
        </p>
        <ul>
          <li>
            <?php _e("Keep the form action/method the same (yes the action is supposed to be empty).", 'custom-contact-forms'); ?>
          </li>
          <li>
            <?php _e("Include all the hidden fields shown below.", 'custom-contact-forms'); ?>
          </li>
          <li>
            <?php _e("Provide a hidden field with a success message or thank you page (both hidden fields are included below, you must choose one or the other and fill in the value part of the input field appropriately).", 'custom-contact-forms'); ?>
          </li>
        </ul>
        <p>
          <?php _e("Just to be clear, you don't edit the code in the Custom HTML Forms feature within the admin panel. Instead, you copy the code in to the page, post, or theme file you want to display a form, then edit the code to look how you want following the guidelines provided above.", 'custom-contact-forms'); ?>
        </p>
      </li>
      <li>
        <h3>
          <?php _e('Saved Form Submissions', 'custom-contact-forms'); ?>
          <a name="pop-form-submissions"></a></h3>
        <p>
          <?php _e('This features saves each user form submission. All the fields attached to the form along with the time of submission and form URL are saved in the database and displayed in a stylish format in the admin panel.', 'custom-contact-forms'); ?>
        </p>
      </li>
      <li>
        <h3>
          <?php _e('Import / Export', 'custom-contact-forms'); ?>
          <a name="pop-import-export"></a></h3>
        <p>
          <?php _e('Import/export is a new feature that allows you to transfer forms, fields, field options, styles and everything else saved by the plugin (except file uploads) between Wordpress installations. Clicking the Export All button will create a .SQL file for download. With the .SQL export file you can use the importer within the CCF plugin admin page to import the .SQL file. The built-in importer is completely safe as long as you only import files that have been generated by the CCF exporter. 
                            You can also use PHPMyAdmin or any other MySQL database administration tool to run the import file. Importing a .SQL file will never overwrite any existing data. 
                            It is strongly recommended that you import CCF .SQL files using the built-in importer with in the admin panel due to the added complexity of importing using alterative methods.
                            If you are importing without using the built-in importer (such as PHPMyAdmin), then note the following: You should only run the import file on Wordpress installations that already have Custom Contact Forms installed; also you will need to change the table prefix for each query within the .SQL file.', 'custom-contact-forms'); ?>
        </p>
      <p><?php _e('Custom Contact Forms allows you to import data in different ways.', 'custom-contact-forms'); ?></p>
      <ul>
      	<li><b><?php _e('Clear and Import:', 'custom-contact-forms'); ?></b> <?php _e("This is the safest way to import because it deletes all current content before importing. This is important because it prevents occurences of conflicting data ID's or slugs.", 'custom-contact-forms'); ?></li>
      </ul>
      <p><?php _e('You can also export data in different ways.', 'custom-contact-forms'); ?></p>
      <ul>
      	<li><b><?php _e('Export All:', 'custom-contact-forms'); ?></b> <?php _e("This exports all custom contact form data including your general settings in SQL format. This is allows you to easily create backups.", 'custom-contact-forms'); ?></li>
      	<li><b><?php _e('Export All Saved Form Submission to CSV:', 'custom-contact-forms'); ?></b> <?php _e("This exports all your saved form submissions into a .CSV file. Since this export contains multiple forms that are assumed to have different fields, this export will only contain the value of each field and not the name of that field.", 'custom-contact-forms'); ?></li>
        <li><b><?php _e("Export Specific Form's: Submissions to CSV:", 'custom-contact-forms'); ?></b> <?php _e("This exports a specific form's saved form submissions. This CSV export will probably more useful to you because it will contain the name of fields as well as the values. This export works best on forms that have fields that have remained completely constant throughout submission.", 'custom-contact-forms'); ?></li>
      </ul>
      <p><?php _e('More import/export methods will be added in the future. Always create a backup before attempting an import! * Note: If you are having problems getting the importer/exporter to work, try CHMODing the import/ and export/ directories to 0777.', 'custom-contact-forms'); ?></p>
      </li>
    </ul>
  </div>
</div>
