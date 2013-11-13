<?php
$this->data['header'] = $this->t('metaconv_title');
$this->includeAtTemplateBase('includes/header.php');
?>



<h2><?php echo $this->t('metaconv_title'); ?></h2>

<form action="?" method="post">

<p><?php echo($this->t('{admin:metaconv_xmlmetadata}')); ?></p>
<p>
<textarea rows="20" cols="120" name="xmldata"><?php echo htmlspecialchars($this->data['xmldata']); ?></textarea>
</p>
<p>
<input type="submit" value="<?php echo $this->t('metaconv_parse'); ?>" />
</p>
</form>

<?php

$output = $this->data['output'];

if($output !== NULL) {

	echo('<h2>' . $this->t('metaconv_converted') . '</h2>' . "\n");

	foreach($output as $type => $text) {
		if($text === '') {
			continue;
		}

		echo('<h3>' . htmlspecialchars($type) . '</h3>' . "\n");
		echo('<pre class="metadatabox">' . htmlspecialchars($text) . '</pre>' . "\n");
	}
}

?>

<?php
$this->includeAtTemplateBase('includes/footer.php');
?>