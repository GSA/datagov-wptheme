<?php
$this->data['header'] = $this->t('metadata_' . $this->data['header']);
$this->includeAtTemplateBase('includes/header.php');
?>


		<h2><?php echo $this->data['header']; ?></h2>
		
		<p><?php echo $this->t('metadata_intro'); ?></p>
		
		<?php if (isset($this->data['metaurl'])) { ?>
			<p><?php echo($this->t('metadata_xmlurl', array('%METAURL%' => htmlspecialchars($this->data['metaurl'])))); ?><br />
			<input type="text" style="width: 90%" value="<?php echo htmlspecialchars($this->data['metaurl']); ?>" /></p>
		<?php } ?>
		<h2><?php echo($this->t('metadata_metadata')); ?></h2>
		
		<p><?php echo($this->t('metadata_xmlformat')); ?></p>
		
		<pre class="metadatabox"><?php echo $this->data['metadata']; ?>
</pre>
		
		
		<p><?php echo($this->t('metadata_simplesamlformat')); ?></p>
		
		<pre class="metadatabox"><?php echo $this->data['metadataflat']; ?>
</pre>
		
		
<?php
if(array_key_exists('available_certs', $this->data)) {	?>
	<h2><?php echo($this->t('metadata_cert')); ?></h2>
	<p><?php echo($this->t('metadata_cert_intro')); ?></p>
	<ul>
	<?php
	foreach(array_keys($this->data['available_certs']) as $certName) {
		echo ('<li><a href="'.
			htmlspecialchars(SimpleSAML_Module::getModuleURL('saml/idp/certs.php').'/'.$certName).'">'.$certName.'</a>');
		if($this->data['available_certs'][$certName]['certFingerprint'][0] == 'afe71c28ef740bc87425be13a2263d37971da1f9') {
			echo ('&nbsp; <img style="display: inline;" src="/' . $this->data['baseurlpath'] .
			'resources/icons/silk/exclamation.png" alt="default certificate" />
			This is the default certificate. Generate a new certificate if this is a production system.');
		}
		echo '</li>';
	}
	echo '</ul>';
}
?>
		


<?php $this->includeAtTemplateBase('includes/footer.php'); ?>