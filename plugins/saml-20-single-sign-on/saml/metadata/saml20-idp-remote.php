<?php

$ini = parse_ini_file(constant('SAMLAUTH_CONF') . '/config/saml20-idp-remote.ini',true);
foreach($ini as $key => $array)
{
  
  $metadata[$key] = array(
          'name' => array(
                  'en' => $array['name']
          ),
          'SingleSignOnService'  => $array['SingleSignOnService'],
          'certFingerprint'      => $array['certFingerprint']
  );
  
  if( trim($array['SingleLogoutService']) != '' )
  {
    $metadata[$key]['SingleLogoutService'] = $array['SingleLogoutService'];
  }

}

