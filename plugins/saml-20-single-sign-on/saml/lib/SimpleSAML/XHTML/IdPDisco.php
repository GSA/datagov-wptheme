<?php

/**
 * This class implements a generic IdP discovery service, for use in various IdP
 * discovery service pages. This should reduce code duplication.
 *
 * Experimental support added for Extended IdP Metadata Discovery Protocol by Andreas 2008-08-28
 * More information: http://rnd.feide.no/content/extended-identity-provider-discovery-service-protocol
 *
 * @author Olav Morken, UNINETT AS.
 * @author Andreas Åkre Solberg <andreas@uninett.no>, UNINETT AS.
 * @package simpleSAMLphp
 * @version $Id$
 */
class SimpleSAML_XHTML_IdPDisco {

	/**
	 * An instance of the configuration class.
	 */
	protected $config;

	/**
	 * The identifier of this discovery service.
	 *
	 * @var string
	 */
	protected $instance;


	/**
	 * An instance of the metadata handler, which will allow us to fetch metadata about IdPs.
	 */
	protected $metadata;


	/**
	 * The users session.
	 */
	protected $session;


	/**
	 * The metadata sets we find allowed entities in, in prioritized order.
	 *
	 * @var array
	 */
	protected $metadataSets;


	/**
	 * The entity id of the SP which accesses this IdP discovery service.
	 */
	protected $spEntityId;
	
	/**
	 * HTTP parameter from the request, indicating whether the discovery service
	 * can interact with the user or not.
	 */
	protected $isPassive;
	
	/**
	 * The SP request to set the IdPentityID... 
	 */
	protected $setIdPentityID = NULL;


	/**
	 * The name of the query parameter which should contain the users choice of IdP.
	 * This option default to 'entityID' for Shibboleth compatibility.
	 */
	protected $returnIdParam;

	/**
	 * The list of scoped idp's. The intersection between the metadata idpList
	 * and scopedIDPList (given as a $_GET IDPList[] parameter) is presented to
	 * the user. If the intersection is empty the metadata idpList is used.
	 */
	protected $scopedIDPList = array();
	
	/**
	 * The URL the user should be redirected to after choosing an IdP.
	 */
	protected $returnURL;


	/**
	 * Initializes this discovery service.
	 *
	 * The constructor does the parsing of the request. If this is an invalid request, it will
	 * throw an exception.
	 *
	 * @param array $metadataSets  Array with metadata sets we find remote entities in.
	 * @param string $instance  The name of this instance of the discovery service.
	 */
	public function __construct(array $metadataSets, $instance) {
		assert('is_string($instance)');

		/* Initialize standard classes. */
		$this->config = SimpleSAML_Configuration::getInstance();
		$this->metadata = SimpleSAML_Metadata_MetaDataStorageHandler::getMetadataHandler();
		$this->session = SimpleSAML_Session::getInstance();
		$this->instance = $instance;
		$this->metadataSets = $metadataSets;

		$this->log('Accessing discovery service.');


		/* Standard discovery service parameters. */

		if(!array_key_exists('entityID', $_GET)) {
			throw new Exception('Missing parameter: entityID');
		} else {
			$this->spEntityId = $_GET['entityID'];
		}

		if(!array_key_exists('returnIDParam', $_GET)) {
			$this->returnIdParam = 'entityID';
		} else {
			$this->returnIdParam = $_GET['returnIDParam'];
		}
		
		$this->log('returnIdParam initially set to [' . $this->returnIdParam . ']');

		if(!array_key_exists('return', $_GET)) {
			throw new Exception('Missing parameter: return');
		} else {
			$this->returnURL = $_GET['return'];
		}
		
		$this->isPassive = FALSE;
		if (array_key_exists('isPassive', $_GET)) {
			if ($_GET['isPassive'] === 'true') $this->isPassive = TRUE;
		}
		$this->log('isPassive initially set to [' . ($this->isPassive ? 'TRUE' : 'FALSE' ) . ']');
		
		if (array_key_exists('IdPentityID', $_GET)) {
			$this->setIdPentityID = $_GET['IdPentityID'];
		} else {
			$this->setIdPentityID = NULL;
		}

		if (array_key_exists('IDPList', $_REQUEST)) {
			$this->scopedIDPList = $_REQUEST['IDPList'];
		}

	}


	/**
	 * Log a message.
	 *
	 * This is an helper function for logging messages. It will prefix the messages with our
	 * discovery service type.
	 *
	 * @param $message  The message which should be logged.
	 */
	protected function log($message) {
		SimpleSAML_Logger::info('idpDisco.' . $this->instance . ': ' . $message);
	}


	/**
	 * Retrieve cookie with the given name.
	 *
	 * This function will retrieve a cookie with the given name for the current discovery
	 * service type.
	 *
	 * @param $name  The name of the cookie.
	 * @return  The value of the cookie with the given name, or NULL if no cookie with that name exists.
	 */
	protected function getCookie($name) {
		$prefixedName = 'idpdisco_' . $this->instance . '_' . $name;
		if(array_key_exists($prefixedName, $_COOKIE)) {
			return $_COOKIE[$prefixedName];
		} else {
			return NULL;
		}
	}


	/**
	 * Save cookie with the given name and value.
	 *
	 * This function will save a cookie with the given name and value for the current discovery
	 * service type.
	 *
	 * @param $name  The name of the cookie.
	 * @param $value  The value of the cookie.
	 */
	protected function setCookie($name, $value) {
		$prefixedName = 'idpdisco_' . $this->instance . '_' . $name;

		/* We save the cookies for 90 days. */
		$saveUntil = time() + 60*60*24*90;

		/* The base path for cookies. This should be the installation directory for simpleSAMLphp. */
		$cookiePath = '/' . $this->config->getBaseUrl();

		setcookie($prefixedName, $value, $saveUntil, $cookiePath);
	}


	/**
	 * Validates the given IdP entity id.
	 *
	 * Takes a string with the IdP entity id, and returns the entity id if it is valid, or
	 * NULL if not.
	 *
	 * @param $idp  The entity id we want to validate. This can be NULL, in which case we will return NULL.
	 * @return  The entity id if it is valid, NULL if not.
	 */
	protected function validateIdP($idp) {
		if($idp === NULL) {
			return NULL;
		}

		if(!$this->config->getBoolean('idpdisco.validate', TRUE)) {
			return $idp;
		}

		foreach ($this->metadataSets AS $metadataSet) {
			try {
				$this->metadata->getMetaData($idp, $metadataSet);
				return $idp;
			} catch(Exception $e) { }
		}

		$this->log('Unable to validate IdP entity id [' . $idp . '].');
		/* The entity id wasn't valid. */
		return NULL;
	}


	/**
	 * Retrieve the users choice of IdP.
	 *
	 * This function finds out which IdP the user has manually chosen, if any.
	 *
	 * @return  The entity id of the IdP the user has chosen, or NULL if the user has made no choice.
	 */
	protected function getSelectedIdP() {


		/* Parameter set from the Extended IdP Metadata Discovery Service Protocol,
		 * indicating that the user prefers this IdP.
		 */
		if ($this->setIdPentityID) {
			return $this->validateIdP($this->setIdPentityID);
		}

		/* User has clicked on a link, or selected the IdP from a dropdown list. */
		if(array_key_exists('idpentityid', $_GET)) {
			return $this->validateIdP($_GET['idpentityid']);
		}

		/* Search for the IdP selection from the form used by the links view.
		 * This form uses a name which equals idp_<entityid>, so we search for that.
		 *
		 * Unfortunately, php replaces periods in the name with underscores, and there
		 * is no reliable way to get them back. Therefore we do some quick and dirty
		 * parsing of the query string.
		 */
		$qstr = $_SERVER['QUERY_STRING'];
		$matches = array();
		if(preg_match('/(?:^|&)idp_([^=]+)=/', $qstr, $matches)) {
			return $this->validateIdP(urldecode($matches[1]));
		}

		/* No IdP chosen. */
		return NULL;
	}


	/**
	 * Retrieve the users saved choice of IdP.
	 *
	 * @return  The entity id of the IdP the user has saved, or NULL if the user hasn't saved any choice.
	 */
	protected function getSavedIdP() {
		if(!$this->config->getBoolean('idpdisco.enableremember', FALSE)) {
			/* Saving of IdP choices is disabled. */
			return NULL;
		}

		if($this->getCookie('remember') === '1') {
			$this->log('Return previously saved IdP because of remember cookie set to 1');
			return $this->getPreviousIdP();
		}
		
		if( $this->isPassive) {
			$this->log('Return previously saved IdP because of isPassive');
			return $this->getPreviousIdP();
		}
		
		return NULL;
	}


	/**
	 * Retrieve the previous IdP the user used.
	 *
	 * @return  The entity id of the previous IdP the user used, or NULL if this is the first time.
	 */
	protected function getPreviousIdP() {
		return $this->validateIdP($this->getCookie('lastidp'));
	}


	/**
	 * Retrieve a recommended IdP based on the IP address of the client.
	 *
	 * @return string|NULL  The entity ID of the IdP if one is found, or NULL if not.
	 */
	protected function getFromCIDRhint() {

		foreach ($this->metadataSets as $metadataSet) {
			$idp = $this->metadata->getPreferredEntityIdFromCIDRhint($metadataSet, $_SERVER['REMOTE_ADDR']);
			if (!empty($idp)) {
				return $idp;
			}
		}

		return NULL;
	}


	/**
	 * Try to determine which IdP the user should most likely use.
	 *
	 * This function will first look at the previous IdP the user has chosen. If the user
	 * hasn't chosen an IdP before, it will look at the IP address.
	 *
	 * @return  The entity id of the IdP the user should most likely use.
	 */
	protected function getRecommendedIdP() {

		$idp = $this->getPreviousIdP();
		if($idp !== NULL) {
			$this->log('Preferred IdP from previous use [' . $idp . '].');
			return $idp;
		}

		$idp = $this->getFromCIDRhint();

		if(!empty($idp)) {
			$this->log('Preferred IdP from CIDR hint [' . $idp . '].');
			return $idp;
		}

		return NULL;
	}


	/**
	 * Save the current IdP choice to a cookie.
	 *
	 * @param string $idp  The entityID of the IdP.
	 */
	protected function setPreviousIdP($idp) {
		assert('is_string($idp)');

		$this->log('Choice made [' . $idp . '] Setting cookie.');
		$this->setCookie('lastidp', $idp);
	}


	/**
	 * Determine whether the choice of IdP should be saved.
	 *
	 * @return  TRUE if the choice should be saved, FALSE if not.
	 */
	protected function saveIdP() {
		if(!$this->config->getBoolean('idpdisco.enableremember', FALSE)) {
			/* Saving of IdP choices is disabled. */
			return FALSE;
		}

		if(array_key_exists('remember', $_GET)) {
			return TRUE;
		}
	}


	/**
	 * Determine which IdP the user should go to, if any.
	 *
	 * @return  The entity id of the IdP the user should be sent to, or NULL if the user
	 *          should choose.
	 */
	protected function getTargetIdP() {

		/* First, check if the user has chosen an IdP. */
		$idp = $this->getSelectedIdP();
		if($idp !== NULL) {
			/* The user selected this IdP. Save the choice in a cookie. */
			$this->setPreviousIdP($idp);

			if($this->saveIdP()) {
				$this->setCookie('remember', 1);
			} else {
				$this->setCookie('remember', 0);
			}

			return $idp;
		}

		$this->log('getSelectedIdP() returned NULL');

		/* Check if the user has saved an choice earlier. */
		$idp = $this->getSavedIdP();
		if($idp !== NULL) {
			$this->log('Using saved choice [' . $idp . '].');
			return $idp;
		}

		/* The user has made no choice. */
		return NULL;
	}


	/**
	 * Retrieve the list of IdPs which are stored in the metadata.
	 *
	 * @return array  Array with entityid=>metadata mappings.
	 */
	protected function getIdPList() {

		$idpList = array();
		foreach ($this->metadataSets AS $metadataSet) {
			$newList = $this->metadata->getList($metadataSet);
			/*
			 * Note that we merge the entities in reverse order. This ensuers
			 * that it is the entity in the first metadata set that "wins" if
			 * two metadata sets have the same entity.
			 */
			$idpList = array_merge($newList, $idpList);
		}

		return $idpList;
	}

	/**
	 * Return the list of scoped idp
	 *
	 * @return array  Array of idp entities
	 */
	protected function getScopedIDPList() {
		return $this->scopedIDPList;
	}
	
	/**
	 * Handles a request to this discovery service.
	 *
	 * The IdP disco parameters should be set before calling this function.
	 */
	public function handleRequest() {

		$idp = $this->getTargetIdp();
		if($idp !== NULL) {
		
			$extDiscoveryStorage = $this->config->getString('idpdisco.extDiscoveryStorage', NULL);
			if ($extDiscoveryStorage !== NULL) {
				$this->log('Choice made [' . $idp . '] (Forwarding to external discovery storage)');
				SimpleSAML_Utilities::redirect($extDiscoveryStorage, array(
//					$this->returnIdParam => $idp,
					'entityID' => $this->spEntityId,
					'IdPentityID' => $idp,
					'returnIDParam' => $this->returnIdParam,
					'isPassive' => 'true',
					'return' => $this->returnURL
				));
				
			} else {
				$this->log('Choice made [' . $idp . '] (Redirecting the user back. returnIDParam=' . $this->returnIdParam . ')');
				SimpleSAML_Utilities::redirect($this->returnURL, array($this->returnIdParam => $idp));
			}
			
			return;
		}
		
		if ($this->isPassive) {
			$this->log('Choice not made. (Redirecting the user back without answer)');
			SimpleSAML_Utilities::redirect($this->returnURL);
			return;
		}

		/* No choice made. Show discovery service page. */

		$idpList = $this->getIdPList();
		$preferredIdP = $this->getRecommendedIdP();

		$idpintersection = array_intersect(array_keys($idpList), $this->getScopedIDPList());
		if (sizeof($idpintersection) > 0) {
			$idpList = array_intersect_key($idpList, array_fill_keys($idpintersection, NULL));
		}

        $idpintersection = array_values($idpintersection); 
        
        if(sizeof($idpintersection)  == 1) {
            $this->log('Choice made [' . $idpintersection[0] . '] (Redirecting the user back. returnIDParam=' . $this->returnIdParam . ')');
            SimpleSAML_Utilities::redirect($this->returnURL, array($this->returnIdParam => $idpintersection[0]));
        }

		/*
		 * Make use of an XHTML template to present the select IdP choice to the user.
		 * Currently the supported options is either a drop down menu or a list view.
		 */
		switch($this->config->getString('idpdisco.layout', 'links')) {
		case 'dropdown':
			$templateFile = 'selectidp-dropdown.php';
			break;
		case 'links':
			$templateFile = 'selectidp-links.php';
			break;
		default:
			throw new Exception('Invalid value for the \'idpdisco.layout\' option.');
		}

		$t = new SimpleSAML_XHTML_Template($this->config, $templateFile, 'disco');
		$t->data['idplist'] = $idpList;
		$t->data['preferredidp'] = $preferredIdP;
		$t->data['return'] = $this->returnURL;
		$t->data['returnIDParam'] = $this->returnIdParam;
		$t->data['entityID'] = $this->spEntityId;
		$t->data['urlpattern'] = htmlspecialchars(SimpleSAML_Utilities::selfURLNoQuery());
		$t->data['rememberenabled'] = $this->config->getBoolean('idpdisco.enableremember', FALSE);
		$t->show();
	}
}

?>
