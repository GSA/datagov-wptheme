<?php
/**
 * Plugin Name: USA search
 * Description: This plugin implements the USA search functionality.
 */

// USA search settings
add_action('admin_menu', 'search_configuration');

function search_configuration() {

    add_menu_page('USA Search Settings', 'USA Search Settings', 'administrator', 'search_config', 'usa_search_settings');

}


function usa_search_settings() {

    $affiliate_name = (get_option('affiliate_name') != '') ? get_option('affiliate_name') : 'datagov';
    $api_key = (get_option('api_key') != '') ? get_option('api_key') : '703cf139fb10f62e63511ff7f7e2b9c5';
    $domain = (get_option('domain') != '') ? get_option('domain') : 'search.usa.gov';



    $html = '<form action="options.php" method="post" name="options">
			<h2>USA Search Settings</h2>' . wp_nonce_field('update-options');

    $html .= '<table class="form-table" width="100%" cellpadding="10">
				<tbody>
					<tr>
						<td scope="row" aligni="left">
							<label>Affiliate name:</label>
							<input type="text" name="affiliate_name" size="60" value="' . $affiliate_name . '">
						</td>
					</tr>
					<tr>
						<td scope="row" aligni="left">
						   <label>API key</label>
						   <input type="text" name="api_key" size="60" value="' . $api_key . '">
						</td>
					</tr>
					<tr>
						<td scope="row" aligni="left">
						   <label>Search Domain</label>
						   <input type="text" name="domain" size="60" value="' . $domain . '">
						</td>
					</tr>

				</tbody>
 			</table>';

    $html .= '<input type="hidden" name="action" value="update" />
			<input type="hidden" name="page_options" value="affiliate_name,api_key,domain" />
			<input type="submit" name="Submit" value="Update" />
			</form>';

    echo $html;
}
?>