<?php
$this->data['header'] = $this->t('{modinfo:modinfo:modlist_header}');
$this->includeAtTemplateBase('includes/header.php');

#$icon_enabled  = '<img src="/' . $this->data['baseurlpath'] . 'resources/icons/silk/accept.png" alt="' .
#htmlspecialchars($this->t(...)" />';
#$icon_disabled = '<img src="/' . $this->data['baseurlpath'] . 'resources/icons/silk/delete.png" alt="disabled" />';

?>

<h2><?php echo($this->data['header']); ?></h2>

<table class="modules" style="width: 100%">
<tr>
<th colspan="2"><?php echo($this->t('{modinfo:modinfo:modlist_name}')); ?></th>
<th ><?php echo($this->t('{modinfo:modinfo:modlist_status}')); ?></th>
<th colspan="2"><?php echo($this->t('{modinfo:modinfo:version}')); ?></th>
</tr>
<?php

$i = 0;
foreach($this->data['modules'] as $id => $info) {
	echo('<tr class="' . ($i++ % 2 == 0 ? 'odd' : 'even') . '">');
	
	
	if (isset($info['def'])) {
		echo('<td><a href="http://simplesamlphp.org/modules/' . htmlspecialchars($id) . '">' . htmlspecialchars($info['def']->def['name']) . '</a></td>');		
	} else {
		echo('<td> </td>');
	}
	
	
	echo('<td><tt>' . htmlspecialchars($id) . '</tt></td>');
	
	
	if($info['enabled']) {
		echo('<td><img src="/' . $this->data['baseurlpath'] . 'resources/icons/silk/accept.png" alt="' .
			htmlspecialchars($this->t('{modinfo:modinfo:modlist_enabled}')) . '" /></td>');
	} else {
		echo('<td><img src="/' . $this->data['baseurlpath'] . 'resources/icons/silk/delete.png" alt="' .
			htmlspecialchars($this->t('{modinfo:modinfo:modlist_disabled}')) . '" /></td>');
	}
	
	if (isset($info['def'])) {
		echo('<td>' . htmlspecialchars($info['def']->getVersion()) . ' (' .htmlspecialchars($info['def']->getBranch()) . ')</td>');	
		if ($info['def']->updateExists()) {
			echo('<td><img style="display: inline" src="/' . $this->data['baseurlpath'] . 'resources/icons/silk/delete.png" alt="' .
				htmlspecialchars($this->t('{modinfo:modinfo:update_exists}')) . '" /> ' . 
				htmlspecialchars($this->t('{modinfo:modinfo:update_exists}')) . '</td>');		
		} else {
			echo('<td><img style="display: inline" src="/' . $this->data['baseurlpath'] . 'resources/icons/silk/accept.png" alt="' .
				htmlspecialchars($this->t('{modinfo:modinfo:latest_version}')) . '" /> ' . 
				htmlspecialchars($this->t('{modinfo:modinfo:latest_version}')) . '</td>');
		}
	} else {
		echo('<td colspan="2"> </td>');
	}
	
	echo('</tr>');
}
?>
</table>
<?php $this->includeAtTemplateBase('includes/footer.php'); ?>