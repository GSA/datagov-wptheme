<?php

if(!array_key_exists('header', $this->data)) {
	$this->data['header'] = 'selectidp';
}
$this->data['header'] = $this->t($this->data['header']);

$this->data['autofocus'] = 'dropdownlist';

$this->includeAtTemplateBase('includes/header.php');

foreach ($this->data['idplist'] AS $idpentry) {
	if (isset($idpentry['name'])) {
		$this->includeInlineTranslation('idpname_' . $idpentry['entityid'], $idpentry['name']);
	} elseif (isset($idpentry['OrganizationDisplayName'])) {
		$this->includeInlineTranslation('idpname_' . $idpentry['entityid'], $idpentry['OrganizationDisplayName']);
	}
	if (isset($idpentry['description']))
		$this->includeInlineTranslation('idpdesc_' . $idpentry['entityid'], $idpentry['description']);
}


?>

		<h2><?php echo $this->data['header']; ?></h2>
		
		<p><?php echo $this->t('selectidp_full'); ?></p>

		<form method="get" action="<?php echo $this->data['urlpattern']; ?>">
		<input type="hidden" name="entityID" value="<?php echo htmlspecialchars($this->data['entityID']); ?>" />
		<input type="hidden" name="return" value="<?php echo htmlspecialchars($this->data['return']); ?>" />
		<input type="hidden" name="returnIDParam" value="<?php echo htmlspecialchars($this->data['returnIDParam']); ?>" />
		<select id="dropdownlist" name="idpentityid">
		<?php
			
		foreach ($this->data['idplist'] AS $idpentry) {

			echo '<option value="'.htmlspecialchars($idpentry['entityid']).'"';
			if (isset($this->data['preferredidp']) && 
				$idpentry['entityid'] == $this->data['preferredidp']) 
				echo ' selected="selected"';
				
			echo '>'.htmlspecialchars($this->t('idpname_' . $idpentry['entityid'])).'</option>';
		
		}
		?>
		</select>
		<input type="submit" value="<?php echo $this->t('select'); ?>"/>
		<?php
		if($this->data['rememberenabled']) {
			echo('<br/><input type="checkbox" name="remember" value="1" />' . $this->t('remember'));
		}
		?>
		</form>

		
<?php $this->includeAtTemplateBase('includes/footer.php'); ?>
