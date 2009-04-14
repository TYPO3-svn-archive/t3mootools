<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007-2008 Peter Klein (peter@umloud.dk)
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
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
* Contains a class with Mootools Javascript Loader functions
*
* @author Peter Klein <peter@umloud.dk>
*/

if (file_exists(t3lib_extMgm::siteRelPath('t3mootools').'res/mootools_1.21.js')) {
	define('T3MOOTOOLS', 'mootools_1.21.js');
}

/**
 * Mootools Javascript Loader functions
 *
 * You are encouraged to use this library in your own scripts!
 *
 * USE:
 * The class is intended to be used without creating an instance of it.
 * So: Don't instantiate - call functions with "tx_t3mootools::" prefixed the function name.
 * So use tx_t3mootools::[method-name] to refer to the functions, eg. 'tx_t3mootools::addMooJS()'
 *
 * Example:
 *
 * if (t3lib_extMgm::isLoaded('t3mootools'))    {
 *   require_once(t3lib_extMgm::extPath('t3mootools').'class.tx_t3mootools.php');
 * }
 *
 *
 * if (defined('T3MOOTOOLS')) {
 * 	tx_t3mootools::addMooJS();
 * }
 * else {
 *
 * 	// Here you add your own version of Mootols library, which is used if the
 * 	// "t3mootools" extension is not installed.
 * 	$GLOBALS['TSFE']->additionalHeaderData[] = ..
 * }
 *
 * @author Peter Klein <peter@umloud.dk>
 * @package TYPO3
 * @subpackage t3mootools
 */
class tx_t3mootools {

	var $cObj;

	/**************************************
	*
	* MOOTOOLS JAVASCRIPT LOADER FUNCTIONS
	*
	***************************************/

	/**
	 * Adds the mootools script tag for the page header.
	 * For frontend usage only.
	 *
	 * @return	void
	 */
	function addMooJS() {
		$GLOBALS['TSFE']->additionalHeaderData['mootools'] = tx_t3mootools::getMooJS();
	}

	/**
	 * Get the mootools script tag.
	 * For frontend usage only.
	 *
	 * @param	boolean		If true, only the URL is returned, not a full script tag
	 * @return	string		HTML Script tag to load the Mootools JavaScript library
	 */
	function getMooJS($urlOnly = FALSE) {
		$url = t3lib_extMgm::siteRelPath('t3mootools').'res/'.T3MOOTOOLS;
		return $urlOnly ? $url : '<script type="text/javascript" src="'.$url.'"></script>';
	}

	/**
	 * Get the mootools script tag.
	 * For backend usage only.
	 *
	 * @param	boolean		If true, only the URL is returned, not a full script tag
	 * @return	string		HTML Script tag to load the Mootools JavaScript library
	 */
	function getMooJSBE($urlOnly = FALSE) {
		global $BACK_PATH;
		$url = str_replace('typo3', t3lib_extMgm::siteRelPath('t3mootools'), $BACK_PATH).'res/'.T3MOOTOOLS;
		return $urlOnly ? $url : '<script type="text/javascript" src="'.$url.'"></script>';
	}

	/**
	 * Function to be used from TypoScript to add Javascript after the mootools.js
	 *
	 * This is a small wrapper for adding javascripts script after the Mootools Library.
	 * This is needed in some situations because headerdata added with "page.headerData" 
	 * is placed BEFORE the headerdata which is added using PHP.
	 *
	 * Usage:
	 *
	 *  10 = USER
	 *  10.userFunc = tx_t3mootools->addJS
	 *  10.jsfile = fileadmin/testscript.js
	 *  10.jsdata = alert('Hello World!');
	 *
	 * @param	string		$content: Content input, ignore (just put blank string)
	 * @param	array		$conf: TypoScript configuration of the plugin!
	 * @return	void
	 */
	function addJS($content,$conf) {

		// If the Mootools lib is not added to page yet, add it!
		if ($GLOBALS['TSFE']->additionalHeaderData['mootools']=='') tx_t3mootools::addMooJS();

		// Append additional javascript to existing headerData.
        $jsdata = $this->cObj->stdWrap($conf['jsdata'], $conf['jsdata.']);
        $jsfile = preg_replace('|^'.PATH_site.'|i','',t3lib_div::getFileAbsFileName($this->cObj->stdWrap($conf['jsfile'], $conf['jsfile.'])));

		if ($jsfile!='') $GLOBALS['TSFE']->additionalHeaderData['mootools'] .= chr(10).'<script type="text/javascript" src="'.$jsfile.'"></script>';
//		if ($jsdata!='') $GLOBALS['TSFE']->additionalHeaderData['mootools'] .= chr(10).'<script type="text/javascript">'.$jsdata.'</script>';
		if ($jsdata!='') $GLOBALS['TSFE']->additionalHeaderData['mootools'] .= chr(10).'<script type="text/javascript">'.chr(10).'/*<![CDATA[*/'.chr(10).'<!--'.chr(10).$jsdata.chr(10).'// -->'.chr(10).'/*]]>*/'.chr(10).'</script>';
	}

}
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3mootools/class.tx_t3mootools.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3mootools/class.tx_t3mootools.php']);
}
?>
