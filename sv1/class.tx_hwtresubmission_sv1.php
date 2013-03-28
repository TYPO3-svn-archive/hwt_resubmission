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

require_once(PATH_t3lib . 'class.t3lib_svbase.php');


/**
 * Service "HWT Resubmission" for the "hwt_resubmission" extension. This service will
 * request open resubmissions from db.
 *
 * @author	Heiko Westermann <hwt3@gmx.de>
 * @package	TYPO3
 * @subpackage	tx_hwtresubmission
 */
class tx_hwtresubmission_sv1 extends t3lib_svbase {

    function init() {
        return parent::init(); // initialization of the service (true if available)
    }



	/**
	 * Standard relative path for the service
	 *
	 * @var	string
	 */
	public	$scriptRelPath = 'sv1/class.tx_hwtresubmission_sv1.php';	// Path to this script relative to the extension dir.



	/**
	 * Requests open resubmission from db.
	 *
	 * @param	int	$userId	ID of cruser
     * @param   int $resubmissionTime time after which user has to resubmit
	 * @return	bool/array $rows db records with open resubmission
	 */
	public function getOpenResubmissions($userId, $resubmissionTime, $selectFields='tt_content.*, pages.title', $limit=50, $table='tt_content') {
        $rows = FALSE;

        if((int)$resubmissionTime > 0) {
            $selectConf['selectFields'] = $selectFields;
            $selectConf['fromTable'] = $table;
            $selectConf['where'] = '(' . $selectConf['fromTable'] . '.tstamp+' . (int)$resubmissionTime . ')<=' . time();
            if($userId) {
                $selectConf['where'] .= ' AND ' . $selectConf['fromTable'] . '.cruser_id=' . (int)$userId;
            }
            if($selectConf['fromTable']!='pages') {
                $selectConf['fromTable'] .= ' LEFT JOIN pages ON pages.uid=' . $table . '.pid';
            }
            if($limit) {
                $selectConf['limit'] = (int)$limit;
            }

            $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($selectConf['selectFields'], $selectConf['fromTable'], $selectConf['where'], $selectConf['groupBy'], $selectConf['orderBy'], $selectConf['limit']);
//if($table!='pages') {
//            var_dump($GLOBALS['TYPO3_DB']->SELECTquery($selectConf['selectFields'], $selectConf['fromTable'], $selectConf['where'], $selectConf['groupBy'], $selectConf['orderBy'], $selectConf['limit']));
//    die();
//}
            $rows = array();
            while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
                $rows[] = $row;
            }
        }

        return $rows;
    }
}

if (defined('TYPO3_MODE') && isset($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/hwt_resubmission/sv1/class.tx_hwtresubmission_sv1.php'])) {
	include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/hwt_resubmission/sv1/class.tx_hwtresubmission_sv1.php']);
}
?>