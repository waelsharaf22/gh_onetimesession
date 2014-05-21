<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

// Force 'getUser' service to find the user
$TYPO3_CONF_VARS['SVCONF']['auth']['setup']['FE_alwaysFetchUser'] = 1;

// Force re-auth user with 'getAuth' service
$TYPO3_CONF_VARS['SVCONF']['auth']['setup']['FE_alwaysAuthUser'] = 1;

// Register authentication service 
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addService(
	$_EXTKEY,
	'auth',
	'Gebruederheitz\\GhOnetimesession\\Authentication\\OnetimeAuthentication',
	array(
		'title' => 'Onetime Authentication',
		'description' => 'Onetime Authentication Service',
		
		'subtype' => 'authUserFE,getUserFE',
		
		'available' => 1,
		'priority' => 80,
		'quality' => 80,
		
		'os' => '',
		'exec' => '',
		
		'classFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY).'Classes/Authentication/OnetimeAuthentication.php',
		'className' => 'Gebruederheitz\\GhOnetimesession\\Authentication\\OnetimeAuthentication',
	)
);
