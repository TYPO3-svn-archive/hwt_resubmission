<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

// Add the service
t3lib_extMgm::addService($_EXTKEY,  'resub' /* sv type */,  'tx_hwtresubmission_sv1' /* sv key */,
	array(
		'title' => 'HWT Resubmission',
		'description' => 'Checks for open resubmission of db records',
		'subtype' => '',
		'available' => TRUE,
		'priority' => 50,
		'quality' => 50,
		'os' => '',
		'exec' => '',
		'classFile' => t3lib_extMgm::extPath($_EXTKEY) . 'sv1/class.tx_hwtresubmission_sv1.php',
		'className' => 'tx_hwtresubmission_sv1',
	)
);

// Add a hook to show Backend warnings
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_befunc.php']['displayWarningMessages'][$_EXTKEY] = 'EXT:' . $_EXTKEY . '/hooks/class.tx_hwtresubmission_backendwarnings.php:tx_hwtresubmission_backendwarnings';

?>