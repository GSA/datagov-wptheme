<?php

require_once('_include.php');

$config = SimpleSAML_Configuration::getInstance();

/* This page will redirect to itself after processing a POST request and sending the email. */
if($_SERVER['REQUEST_METHOD'] !== 'POST') {
	/* The message has been sent. Show error report page. */

	$t = new SimpleSAML_XHTML_Template($config, 'errorreport.php', 'errors');
	$t->show();
	exit;
}

$reportId = (string)$_REQUEST['reportId'];
$email = (string)$_REQUEST['email'];
$text = htmlspecialchars((string)$_REQUEST['text']);

try {
	$session = SimpleSAML_Session::getInstance();
	$data = $session->getData('core:errorreport', $reportId);
} catch (Exception $e) {
	SimpleSAML_Logger::error('Error loading error report data: ' . var_export($e->getMessage(), TRUE));
}

if ($data === NULL) {
	$data = array(
		'exceptionMsg' => 'not set',
		'exceptionTrace' => 'not set',
		'reportId' => $reportId,
		'trackId' => 'not set',
		'url' => 'not set',
		'version' => $config->getVersion(),
		'referer' => 'not set',
	);

	if (isset($session)) {
		$data['trackId'] = $session->getTrackId();
	}
}

foreach ($data as $k => $v) {
	$data[$k] = htmlspecialchars($v);
}

/* Build the email message. */

$message = '<h1>SimpleSAMLphp Error Report</h1>

<p>Message from user:</p>
<div class="box" style="background: yellow; color: #888; border: 1px solid #999900; padding: .4em; margin: .5em">' . htmlspecialchars($text) . '</div>

<p>Exception: <strong>' . $data['exceptionMsg'] . '</strong></p>
<pre>' . $data['exceptionTrace'] . '</pre>

<p>URL:</p>
<pre><a href="' . $data['url'] . '">' . $data['url'] . '</a></pre>

<p>Host:</p>
<pre>' . htmlspecialchars(php_uname('n')) . '</pre>

<p>Directory:</p>
<pre>' . dirname(dirname(__FILE__)) . '</pre>

<p>Track ID:</p>
<pre>' . $data['trackId'] . '</pre>

<p>Version: <tt>' . $data['version'] . '</tt></p>

<p>Report ID: <tt>' . $data['reportId'] . '</tt></p>

<p>Referer: <tt>' . htmlspecialchars($data['referer']) . '</tt></p>

<hr />
<div class="footer">This message was sent using simpleSAMLphp. Visit <a href="http://rnd.feide.no/simplesamlphp">simpleSAMLphp homepage</a>.</div>

';


/* Add the email address of the submitter as the Reply-To address. */
$email = trim($email);
/* Check that it looks like a valid email address. */
if (!preg_match('/\s/', $email) && strpos($email, '@') !== FALSE) {
	$replyto = $email;
	$from = $email;
} else {
	$replyto = NULL;
	$from = 'no-reply@simplesamlphp.org';
}

/* Send the email. */
$toAddress = $config->getString('technicalcontact_email', 'na@example.org');
if ($toAddress !== 'na@example.org') {
	$email = new SimpleSAML_XHTML_EMail($toAddress, 'simpleSAMLphp error report', $from);
	$email->setBody($message);
	$email->send();
	SimpleSAML_Logger::error('Report with id ' . $reportId . ' sent to <' . $toAddress . '>.');
}

/* Redirect the user back to this page to clear the POST request. */
SimpleSAML_Utilities::redirect(SimpleSAML_Utilities::selfURLNoQuery());
