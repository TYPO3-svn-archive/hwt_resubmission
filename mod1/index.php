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
 * Hint: use extdeveval to insert/update function index above.
 */


$LANG->includeLLFile('EXT:hwt_resubmission/mod1/locallang.xml');
require_once(PATH_t3lib . 'class.t3lib_scbase.php');
$BE_USER->modAccess($MCONF,1);	// This checks permissions and exits if the users has no permission for entry.
	// DEFAULT initialization of a module [END]

require_once(t3lib_extMgm::extPath('hwt_resubmission', 'sv1/class.tx_hwtresubmission_sv1.php'));

/**
 * Module 'resubmission' for the 'hwt_resubmission' extension.
 *
 * @author	Heiko Westermann <hwt3@gmx.de>
 * @package	TYPO3
 * @subpackage	tx_hwtresubmission
 */
class  tx_hwtresubmission_module1 extends t3lib_SCbase {
    var $pageinfo;
    var $extConf = array();
    var $debug = 0;

    /**
     * Initializes the Module
     * @return	void
     */
    function init()	{
            global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;
            $this->extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['hwt_resubmission']);

            parent::init();

            /*
            if (t3lib_div::_GP('clear_all_cache'))	{
                    $this->include_once[] = PATH_t3lib.'class.t3lib_tcemain.php';
            }
            */
    }

    /**
     * Adds items to the ->MOD_MENU array. Used for the function menu selector.
     *
     * @return	void
     */
    function menuConfig()	{
            global $LANG;
            $this->MOD_MENU = Array (
                    'function' => Array (
                            '1' => $LANG->getLL('function1'),
                    )
            );
            parent::menuConfig();
    }

    /**
     * Main function of the module. Write the content to $this->content
     * If you chose "web" as main module, you will need to consider the $this->id parameter which will contain the uid-number of the page clicked in the page tree
     *
     * @return	[type]		...
     */
    function main()	{
        global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;
        //var_dump($this->extConf);
        // Access check!
        // The page will show only if there is a valid page and if this page may be viewed by the user
        $this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
        $access = is_array($this->pageinfo) ? 1 : 0;

        // Template markers
        $markers = array(
            'CSH' => '',
            'FUNC_MENU' => '',
            'CONTENT' => '',
            'SAVE' => ''
        );

        // initialize doc
        $this->doc = t3lib_div::makeInstance('template');
        $this->doc->setModuleTemplate(t3lib_extMgm::extPath('hwt_resubmission') . 'mod1/mod_template.html');
        $this->doc->backPath = $BACK_PATH;
        $docHeaderButtons = $this->getButtons();

        // if (($this->id && $access) || ($BE_USER->user['admin'] && !$this->id))
        //if ($access)	{
            $markers['FUNC_MENU'] = t3lib_BEfunc::getFuncMenu(0, 'SET[function]', $this->MOD_SETTINGS['function'], $this->MOD_MENU['function']);
            // Draw the form
            $this->doc->form = '<form action="" method="post" enctype="multipart/form-data">';
            // JavaScript
            $this->doc->JScode = '
                    <script language="javascript" type="text/javascript">
                            script_ended = 0;
                            function jumpToUrl(URL)	{
                                    document.location = URL;
                            }
                    </script>
            ';
            $this->doc->postCode='
                    <script language="javascript" type="text/javascript">
                            script_ended = 1;
                            if (top.fsMod) top.fsMod.recentIds["web"] = 0;
                    </script>
            ';
            // Render content:
            $this->moduleContent();
        /*} else {
            // If no access or if ID == zero
            $docHeaderButtons['save'] = '';
            // If no access or if ID == zero
            $flashMessage = t3lib_div::makeInstance(
                    't3lib_FlashMessage',
                    $LANG->getLL('notice.clickAPage_content'),
                    $LANG->getLL('title'),
                    t3lib_FlashMessage::INFO
            );
            $this->content = $flashMessage->render();
            $this->content.=$this->doc->spacer(10);
        }*/

        $markers['CONTENT'] = $this->content;

        // Build the <body> for the module
        $this->content = $this->doc->startPage($LANG->getLL('title'));
        $this->content.= $this->doc->moduleBody($this->pageinfo, $docHeaderButtons, $markers);
        $this->content.= $this->doc->endPage();
        $this->content = $this->doc->insertStylesAndJS($this->content);

    }

    /**
     * Prints out the module HTML
     *
     * @return	void
     */
    function printContent()	{

            $this->content.=$this->doc->endPage();
            echo $this->content;
    }

    /**
     * Generates the module content
     *
     * @return	void
     */
    function moduleContent() {
        global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

        $this->content.=$this->doc->sectionHeader($LANG->getLL('title'));
        $this->content.=$this->doc->spacer(10);

        if ($this->extConf['resubmissionTime'] == 0) {
            $flashMessage = t3lib_div::makeInstance(
                                't3lib_FlashMessage',
                                $LANG->getLL('error.resubmissionTime'),
                                $LANG->getLL('title'),
                                t3lib_FlashMessage::ERROR
                            );
            $content .= $flashMessage->render();
            $error = true;
        }
//        if ($this->extConf['defaultAccountTree'] == 0) {
//            $flashMessage = t3lib_div::makeInstance(
//                                't3lib_FlashMessage',
//                                $LANG->getLL('notice.defaultAccountTree'),
//                                $LANG->getLL('title'),
//                                t3lib_FlashMessage::INFO
//                            );
//            $content .= $flashMessage->render();
//        }
        if ($content) {
            $this->content.=$this->doc->section($LANG->getLL('section.moduleConfiguration'),$content,0,1);
            $this->content.=$this->doc->spacer(10);
        }

//        if (!$error) {
            switch((string)$this->MOD_SETTINGS['function'])	{
                case 1:
                    $content = '';

                    $openResubmissions = tx_hwtresubmission_sv1::getOpenResubmissions($GLOBALS['BE_USER']->user['uid'], $this->extConf['resubmissionTime'], 'pages.uid, pages.title', '', 'pages');
//                    var_dump($openResubmissions);
//                    die();
                    if($openResubmissions) {
                        $content .= $LANG->getLL('label.openResubmissions') . '<br /><br />';
                        foreach($openResubmissions as $value) {
                            $content .= '- [' . $value['uid'] . '] ' . $value['title'] . '<br />';
                        }
            //         var_dump($openResubmissions);
            //         die();
                    }
                    else {
                        $content .= $LANG->getLL('label.noOpenResubmissions') . '<br />';
                    }
                    $this->content.= $this->doc->section($LANG->getLL('section.openResubmissionsPages'),$content,0,1);
                    $this->content.= $this->doc->spacer(20);

                    // check for open resubmissions in 'tt_content'
                    $content = '';
                    $openResubmissions = tx_hwtresubmission_sv1::getOpenResubmissions($GLOBALS['BE_USER']->user['uid'], $this->extConf['resubmissionTime'], 'tt_content.uid, tt_content.header, tt_content.pid, pages.title');
//                    var_dump($openResubmissions);
//                    die();
                    if($openResubmissions) {
                        $content .= $LANG->getLL('label.openResubmissions') . '<br /><br />';
                        foreach($openResubmissions as $value) {
                            $content .= '- [' . $value['uid'] . '] ' . $value['header'] . ' on page [' . $value['pid'] . '] ' . $value['title'] . '<br />';
                        }
//                     var_dump($openResubmissions);
//                     die();
                    }
                    else {
                        $content .= $LANG->getLL('label.noOpenResubmissions') . '<br />';
                    }
                    $this->content.= $this->doc->section($LANG->getLL('section.openResubmissionsContent'),$content,0,1);
                    $this->content.= $this->doc->spacer(10);

                    if ($this->debug == 1) {
                        $this->content.=$this->doc->spacer(10);
                        $content='
                                <br />This is the GET/POST vars sent to the script:<br />'.
                                'GET:'.t3lib_div::view_array($_GET).'<br />'.
                                'POST:'.t3lib_div::view_array($_POST).'<br />'.
                                '';
                        $this->content.= $this->doc->divider(5);
                        $this->content.= $this->doc->section('DEBUG:',$content,0,1);
                    }
                    break;
                case 2:
                    break;
                case 3:
                    break;
            }
//        }
    }


    /**
     * Create the panel of buttons for submitting the form or otherwise perform operations.
     *
     * @return	array	all available buttons as an assoc. array
     */
    protected function getButtons()	{

            $buttons = array(
                    'csh' => '',
                    'shortcut' => '',
                    'save' => ''
            );
            // CSH
            $buttons['csh'] = t3lib_BEfunc::cshItem('_MOD_web_func', '', $GLOBALS['BACK_PATH']);
            // SAVE button
            //$buttons['save'] = '<input type="image" class="c-inputButton" name="submit" value="Update"' . t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/savedok.gif', '') . ' title="' . $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.php:rm.saveDoc', 1) . '" />';
            // Shortcut
            if ($GLOBALS['BE_USER']->mayMakeShortcut())	{
                    $buttons['shortcut'] = $this->doc->makeShortcutIcon('', 'function', $this->MCONF['name']);
            }

            return $buttons;
    }

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/hwt_resubmission/mod1/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/hwt_resubmission/mod1/index.php']);
}


// Make instance:
$SOBE = t3lib_div::makeInstance('tx_hwtresubmission_module1');
$SOBE->init();

// Include files?
foreach($SOBE->include_once as $INC_FILE)	include_once($INC_FILE);

$SOBE->main();
$SOBE->printContent();
?>