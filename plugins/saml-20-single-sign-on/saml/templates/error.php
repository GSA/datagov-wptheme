<?php 
	$this->data['header'] = $this->t($this->data['dictTitle']);
	
	$this->data['head'] = '
<meta name="robots" content="noindex, nofollow" />
<meta name="googlebot" content="noarchive, nofollow" />';
	
	$this->includeAtTemplateBase('includes/header.php'); 
?>

	<h2><?php echo $this->t($this->data['dictTitle']); ?></h2>

<?php
echo htmlspecialchars($this->t($this->data['dictDescr'], $this->data['parameters']));

/* Include optional information for error. */
if (isset($this->data['includeTemplate'])) {
	$this->includeAtTemplateBase($this->data['includeTemplate']);
}
?>

	<div class="trackidtext">
		<?php echo $this->t('report_trackid'); ?>
		<span class="trackid"><?php echo $this->data['error']['trackId']; ?></span>
	</div>
		

<?php
/* Print out exception only if the exception is available. */
if ($this->data['showerrors']) {
?>
		<h2><?php echo $this->t('debuginfo_header'); ?></h2>
		<p><?php echo $this->t('debuginfo_text'); ?></p>
		
		<div style="border: 1px solid #eee; padding: 1em; font-size: x-small">
			<p style="margin: 1px"><?php echo htmlspecialchars($this->data['error']['exceptionMsg']); ?></p>
			<pre style=" padding: 1em; font-family: monospace; "><?php echo htmlspecialchars($this->data['error']['exceptionTrace']); ?></pre>
		</div>
<?php
}
?>

<?php
/* Add error report submit section if we have a valid technical contact. 'errorreportaddress' will only be set if
 * the technical contact email address has been set.
 */
if (isset($this->data['errorReportAddress'])) {
?>

	<h2><?php echo $this->t('report_header'); ?></h2>
	<form action="<?php echo htmlspecialchars($this->data['errorReportAddress']); ?>" method="post">
	
		<p><?php echo $this->t('report_text'); ?></p>
		<p><?php echo $this->t('report_email'); ?> <input type="text" size="25" name="email" value="<?php echo htmlspecialchars($this->data['email']); ?>" /></p>
	
		<p>
		<textarea name="text" rows="6" cols="43"><?php echo $this->t('report_explain'); ?></textarea>
		</p><p>
		<input type="hidden" name="reportId" value="<?php echo $this->data['error']['reportId']; ?>" />
		<input type="submit" name="send" value="<?php echo $this->t('report_submit'); ?>" />
		</p>
	</form>
<?php
}
?>

<h2 style="clear: both"><?php echo $this->t('howto_header'); ?></h2>

<p><?php echo $this->t('howto_text'); ?></p>


<?php $this->includeAtTemplateBase('includes/footer.php'); ?>