<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2013 Heiko Westermann <hwt3@gmx.de>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * $Id$
 */

require_once(t3lib_extMgm::extPath('hwt_resubmission', 'sv1/class.tx_hwtresubmission_sv1.php'));

/**
 * This class contains a hook to the backend warnings collection. It checks
 * for open resubmissions and creates a warning if the configuration is wrong.
 *
 * @author	Heiko Westermann <hwt3@gmx.de>
 * @package	TYPO3
 * @subpackage	tx_hwtresubmission
 */
class tx_hwtresubmission_backendwarnings {

	/**
	 * Checks for open resubmissions and creates warnings if necessary.
	 *
	 * @param	array	$warnings	Warnings
	 * @see	t3lib_BEfunc::displayWarningMessages()
	 */
	public function displayWarningMessages_postProcess(array &$warnings) {
        $extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['hwt_resubmission']);

        $openResubmissions = tx_hwtresubmission_sv1::getOpenResubmissions($GLOBALS['BE_USER']->user['uid'], $extConf['resubmissionTime'], 'pages.uid', 1, 'pages');
        if(!$openResubmissions) {
            $openResubmissions = tx_hwtresubmission_sv1::getOpenResubmissions($GLOBALS['BE_USER']->user['uid'], $extConf['resubmissionTime'], 'tt_content.uid', 1);
        }

        if($openResubmissions) {
            $warnings['hwtresubmission'] = '[EXT: hwt_resubmission] Open Resubmissions!<br />Please go to ';
            /*foreach($openResubmissions as $value) {
                $warnings['hwtresubmission'] .= '- tt_content[' . $value['uid'] . '] ' . $value['header'] . '<br />';
            }*/
//         var_dump($openResubmissions);
//         die();
            $warnings['hwtresubmission'] .= '<a href="javascript:top.goToModule(\'web_txhwtresubmissionM1\',1)">Resubmission Module</a> to check them.';
        }
	}
}

if (defined('TYPO3_MODE') && isset($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/hwt_resubmission/hooks/class.tx_hwtresubmission_backendwarnings.php'])) {
	include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/hwt_resubmission/hooks/class.tx_hwtresubmission_backendwarnings.php']);
}
?>