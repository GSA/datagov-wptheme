<div class="help-contents">
  <ul>
    <li><a href="#intro">Introduction to SAML</a></li>
    <li><a href="#getting-started">How to get started</a></li>
  </ul>
</div>

<a name="intro"></a>
<h3>Introduction to SAML</h3>
<p>There are a number of great SAML documents out there (Wikipedia included), but here's a quick run-through if this is your first time working with the protocol:</p>
<p>There are two basic entities involved in a SAML relationship: the <strong>Identity Provider (IdP)</strong> and the <strong>Service Provider (SP)</strong>.  This WordPress website is the service provider, and your company's SSO portal (or vendor's, if you outsource it) is the Identity Provider. The Identity Provider takes care of everything to do with authentication, or the process of gathering a username/password and verifying that they are correct. If a user successfully logs in to the Identity Provider, it will redirect them to your website, along with an &ldquo;assertion,&rdquo; which is the IdP's way of saying &ldquo;You should allow User X to log in, because the username and password he gave me were correct.&rdquo; The IdP can also provide some other helpful information, such as the user's email address or the security groups they belong to. What the IdP <strong>doesn&rsquo;t</strong> do is authorization, which is the process of determining what a given user is allowed to do. This plugin provides some basic tools to do that authorization, so that some users can be administrators, while others are just editors, and some people may not even be allowed to access your site at all, even if they successfully logged in to the IdP.</p>
<p>There are two different flows that a SAML 2.0 login can use, and this plugin can handle either one, as long as you provide a valid certificate and private key on the <a href="?page=sso_sp.php">Service Provider</a> tab.</p>
<h4>SP-initiated Login (more common)</h4>
<ol>
	<li>The user visits an area of your website that requires login, such as /wp-admin.</li>
	<li>This plugin redirects the user to your IdP, along with a <strong>SAML Request</strong> that tells the IdP when the request was made, and where it should redirect the user back to if they log in successfully. It also &ldquo;signs&rdquo; the request so the IdP can be sure that the request is not being faked.</li>
	<li>The user arrives at the IdP website and provides the IdP with their login information (usually username/password)</li>
	<li>If the IdP determines that the user has logged in correctly, it redirects them <em>back</em> to your website, this time with a <strong>SAML Assertion</strong> that provides some information about the user and tells the SP how long the user's login is valid for.</li>
	<li>Using the information from the IdP, this plugin determines which permissions the user should have and logs them in to WordPress accordingly.</li>
</ol>
<h4><a name="idp-first-flow"></a>IdP-initiated Login (more straightforward)</h4>
<ol>
	<li>The user arrives at the IdP website and provides the IdP with their login information (usually username/password). The user must also indicate to the IdP which SP they want to log in to. (One IdP typically serves many SP's)</li>
	<li>If the IdP determines that the user has logged in correctly, it redirects them <em>back</em> to your website, this time with a <strong>SAML Assertion</strong> that provides some information about the user and tells the SP how long the user's login is valid for.</li>
	<li>Using the information from the IdP, this plugin determines which permissions the user should have and logs them in to WordPress accordingly.</li>
</ol>
<br/>
<a name="getting-started"></a>
<h3>How to Get Started</h3>
<ol class="helpList">
	<li><strong>Start with the Identity Provider Tab.</strong> You only need a few pieces of information about your Identity Provider:</li>
		<ul>
			<li><strong>IdP Name: </strong>this can be anything you want. To make life easier, this will be how the IdP is displayed elsewhere in the plugin.</li>
			<li><strong>URL Identifier: </strong> this is sometimes called an Entity ID. This is a very specific URL, and the administrator of your IdP system should know what it is. If this is not entered correctly, there is absolutely no way the plugin will work.</li>
			<li><strong>Single Sign-On URL: </strong>When the plugin redirects your users to the IdP, it must send them to a script that is capable of processing the request. For ADFS 2.0 IdP's, the default location for this is <code>/adfs/ls</code>. For SimpleSAMLPHP IdP's, the default location is <code>/simplesaml/saml2/idp/SSOService.php</code>.</li>
			<li><strong>Single Logout URL: </strong>This URL uses the same concept as the Single Sign-On URL, except it is used when your users log out of WordPress. For ADFS 2.0 IdP's, the default location for this is <code>/adfs/ls/?wa=wsignout1.0</code>. For SimpleSAMLPHP IdP's, the default location is <code>/simplesaml/saml2/idp/SingleLogoutService.php</code>.</li>
			<li><strong>Certificate Fingerprint: </strong>Every IdP uses a certificate as a way to prove that its assertions are not being faked. Every certificate has 40 numbers and letters that make up a fingerprint, which is a shorthand way of validating a certificate. The octet pairs of a fingerprint are sometimes separated by colons for readability; this format is accepted by the plugin, or you can leave the colons out altogether.</li>
		</ul>
	<li><strong>Configure your Service Provider Settings. </strong> These settings determine how this plugin will process incoming assertions from your IdP.</li>
	<ul>
		<li>To be continued &hellip;</li>
	</ul>
</ol>