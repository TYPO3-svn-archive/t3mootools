<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007-2008 Peter Klein <peter@umloud.dk>
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


	// DEFAULT initialization of a module [BEGIN]
unset($MCONF);
require_once('conf.php');
require_once($BACK_PATH.'init.php');
require_once($BACK_PATH.'template.php');

$LANG->includeLLFile('EXT:t3mootools/mod1/locallang.xml');
require_once(PATH_t3lib.'class.t3lib_scbase.php');
$BE_USER->modAccess($MCONF,1);	// This checks permissions and exits if the users has no permission for entry.
	// DEFAULT initialization of a module [END]

if (intval(PHP_VERSION)<5) require_once('class.JavaScriptPacker.php');
else require_once('class.JavaScriptPacker_php5.php');
define('JSMIN_AS_LIB', true);
require_once('jsmin2.php');
require_once('class.analyzeMooJS.php');
if (t3lib_extMgm::isLoaded('extdeveval'))    {
	require_once(t3lib_extMgm::extPath('extdeveval').'mod1/class.tx_extdeveval_apidisplay.php');
}

/**
 * Module 'Mootools Config' for the 't3mootools' extension.
 *
 * @author	Peter Klein <peter@umloud.dk>
 * @package	TYPO3
 * @subpackage	tx_t3mootools
 */
class  tx_t3mootools_module1 extends t3lib_SCbase {
				var $pageinfo;
				var $extKey = 't3mootools';
				var $mooVersion = '1-2-2';
				/**
				 * Initializes the Module
				 * @return	void
				 */
				function init()	{
					global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

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
							'2' => $LANG->getLL('function2'),
							'3' => $LANG->getLL('function3'),
							'4' => $LANG->getLL('function4'),
						)
					);
					if (t3lib_extMgm::isLoaded('extdeveval')) {
						$this->MOD_MENU['function'][5] = $LANG->getLL('function5');
					}
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
					// Access check!
					// The page will show only if there is a valid page and if this page may be viewed by the user
					$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
					$access = is_array($this->pageinfo) ? 1 : 0;

					if (($this->id && $access) || ($BE_USER->user['admin'] && !$this->id))	{

							// Draw the header.
						$this->doc = t3lib_div::makeInstance('bigDoc');
						$this->doc->backPath = $BACK_PATH;
						$this->doc->form='<form action="'.$_SERVER['SCRIPT_NAME'].'#moobuttons" method="post" enctype="multipart/form-data" name="moo">';

							// JavaScript (Mootools subscripts is used, as no compressed lib exists yet or might not include the supparts needed.)

						$this->doc->JScode = '
							<link rel="stylesheet" type="text/css" media="screen" href="../res/mooconfig.css" />				

							<script type="text/javascript" src="../'.$this->mooVersion.'/Core/Core.js"></script>
							<script type="text/javascript" src="../'.$this->mooVersion.'/Core/Browser.js"></script>
							<script type="text/javascript" src="../'.$this->mooVersion.'/Native/Array.js"></script>
							<script type="text/javascript" src="../'.$this->mooVersion.'/Native/Hash.js"></script>
							<script type="text/javascript" src="../'.$this->mooVersion.'/Native/String.js"></script>
							<script type="text/javascript" src="../'.$this->mooVersion.'/Native/Function.js"></script>
							<script type="text/javascript" src="../'.$this->mooVersion.'/Native/Number.js"></script>
							<script type="text/javascript" src="../'.$this->mooVersion.'/Native/Event.js"></script>
							<script type="text/javascript" src="../'.$this->mooVersion.'/Element/Element.js"></script>
							<script type="text/javascript" src="../'.$this->mooVersion.'/Element/Element.Event.js"></script>
							<script type="text/javascript" src="../'.$this->mooVersion.'/Element/Element.Style.js"></script>
							<script type="text/javascript" src="../'.$this->mooVersion.'/Utilities/Selectors.js"></script>
							<script type="text/javascript" src="../'.$this->mooVersion.'/Utilities/Domready.js"></script>
							<script type="text/javascript" src="../'.$this->mooVersion.'/Class/Class.js"></script>
							<script type="text/javascript" src="../'.$this->mooVersion.'/Class/Class.Extras.js"></script>
							<script type="text/javascript" src="../'.$this->mooVersion.'/Fx/Fx.js"></script>
							<script type="text/javascript" src="../'.$this->mooVersion.'/Fx/Fx.CSS.js"></script>
							<script type="text/javascript" src="../'.$this->mooVersion.'/Fx/Fx.Morph.js"></script>
							<script type="text/javascript" src="../'.$this->mooVersion.'/Fx/Fx.Transitions.js"></script>
							<script type="text/javascript" src="../'.$this->mooVersion.'/Fx/Fx.Slide.js"></script>
							<script type="text/javascript" src="../res/mooconfig1.2.2.js"></script>
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

						$headerSection = $this->doc->getHeader('pages',$this->pageinfo,$this->pageinfo['_thePath']).'<br />'.$LANG->sL('LLL:EXT:lang/locallang_core.xml:labels.path').': '.t3lib_div::fixed_lgd_pre($this->pageinfo['_thePath'],50);

						$this->content.=$this->doc->startPage($LANG->getLL('title'));
						$this->content.=$this->doc->header($LANG->getLL('title'));
						$this->content.=$this->doc->spacer(5);

						// Use button from the Analyzer has been pressed
						if ($_POST['usemoo'] && $_POST['dependencies']) {
							$this->saveMooConf(unserialize(urldecode($_POST['dependencies'])));
							$this->MOD_SETTINGS['function'] = 1;
						}
						// Merge&Use button from the Analyzer has been pressed
						if ($_POST['mergemoo'] && $_POST['dependencies']) {
							$this->saveMooConf($this->mergeMooConf(unserialize(urldecode($_POST['dependencies']))));
							$this->MOD_SETTINGS['function'] = 1;
						}
						$this->compressed = '';
						// Compress button from the Compress own script has been pressed
						if ($_POST['compress'] && $_POST['compressdata']!='') {
							$this->compressed = $this->compressJSFile($_POST['compressdata']);
						}

						$this->content.=$this->doc->section('',$this->doc->funcMenu('',t3lib_BEfunc::getFuncMenu($this->id,'SET[function]',$this->MOD_SETTINGS['function'],$this->MOD_MENU['function'])));
						$this->content.=$this->doc->divider(5);


						// Render content:
						$this->moduleContent();


						// ShortCut
						if ($BE_USER->mayMakeShortcut())	{
							$this->content.=$this->doc->spacer(20).$this->doc->section('',$this->doc->makeShortcutIcon('id',implode(',',array_keys($this->MOD_MENU)),$this->MCONF['name']));
						}

						$this->content.=$this->doc->spacer(10);
					} else {
							// If no access or if ID == zero

						$this->doc = t3lib_div::makeInstance('mediumDoc');
						$this->doc->backPath = $BACK_PATH;

						$this->content.=$this->doc->startPage($LANG->getLL('title'));
						$this->content.=$this->doc->header($LANG->getLL('title'));
						$this->content.=$this->doc->spacer(5);
						$this->content.=$this->doc->spacer(10);
					}
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
				function moduleContent()	{
					global $LANG;

					$this->mooConfig = $_POST['mootools-core_files'];
					$this->mooConfigMore = $_POST['mootools-more_files'];
					
					if (!is_array($this->mooConfig) || !isset($this->mooConfig)) {
						$this->mooConfig = array('Core/Core.js');
					}

					if (is_array($this->mooConfigMore) && isset($this->mooConfigMore)) {
						$this->mooConfig = array_merge($this->mooConfig,$this->mooConfigMore);
					}

					switch((string)$this->MOD_SETTINGS['function'])    {
						case 1:
							if (isset($_POST['compression'])) {
								if ($jsAlertData = $this->createMooFile()) {
									$content.='<script type="text/javascript">
									window.addEvent("load", function() {
										alert("'.t3lib_div::slashJS($jsAlertData).'");
									});
									</script>';
								}
							}
							$content.= $this->makeMooForm();
							$this->content.=$this->doc->section($LANG->getLL('mootools.header1'),$content,0,1);
						break;
						case 2:
							$content = $this->makePackitoForm();
							$this->content.=$this->doc->section($LANG->getLL('mootools.header2'),$content,0,1);
							$file = $_FILES['js_local']['tmp_name'] ? $_FILES['js_local']['tmp_name'] : ($_POST['js_remote'] ? t3lib_div::getFileAbsFileName($_POST['js_remote']) : '');
							// Form has been submitted
							if ($file) {
								$fileName = $_FILES['js_local']['name'] ? $_FILES['js_local']['name'] : ($_POST['js_remote'] ? $_POST['js_remote'] : $file );
								$dep = $this->analyzeJS($file);
								$content = $this->displayDependencies($dep);
								if (count($dep)==0) {
									$content.= '<p>&nbsp;</p><p>'.$LANG->getLL('mootools.analyze.packed').'</p>';
								}
								$this->content.=$this->doc->section($LANG->getLL('mootools.analyze.dependencies').' "'.basename($fileName).'"',$content,0,1);
							}
						break;
						case 3:
							$content = $this->makeProcessForm();
							$this->content.=$this->doc->section($LANG->getLL('mootools.header3'),$content,0,1);
							// Form has been submitted
							$files = $_POST['ext'];
							if ($files) {
								$dep = Array();
								foreach ($files as $file) {
									$dep = $this->processT3mootoolsTxt(t3lib_div::getFileAbsFileName($file),$dep);
								}
								$content = $this->displayDependencies($dep);
								$this->content.=$this->doc->section($LANG->getLL('mootools.extension.dependencies'),$content,0,1);
							}
						break;
						case 4:
							$content = $this->makeCompressForm($this->compressed);
							$this->content.=$this->doc->section($LANG->getLL('mootools.header4'),$content,0,1);
						break;
						case 5:
							// Display APIdocs
							if (t3lib_extMgm::isLoaded('extdeveval')) {
								$inst = t3lib_div::makeInstance('tx_extdeveval_apidisplay');
								$content = '<hr />'.$inst->main(t3lib_div::getUrl('../ext_php_api.dat'),'tx_t3mootools');
								$this->content.=$this->doc->section($LANG->getLL('mootools.header5'),$content,0,1);
							}
						break;
					}
				}

				function analyzeJS($file) {
					$dependencies = array();
					$fileName = $_FILES['js_local']['name'] ? $_FILES['js_local']['name'] : ($_POST['js_remote'] ? $_POST['js_remote'] : $file );
					$path_info = pathinfo(array_shift(explode('?', basename($fileName))));
					if ($path_info['extension']=='js') {
						$fileData = @file_get_contents($file);
						if (substr($fileData,0,23)!= 'eval(function(p,a,c,k,e') {

							$pack = new analyzeMooJS('',$fileData);
							$requires = $pack->dependencies;
							foreach($requires as $file => $lib) {
								$dependencies[$lib][$file] = 1;
							}
						}
					}
					return $dependencies;
				}

				function displayDependencies($requires) {
					global $LANG;
					$dependencies = '';
					$prevlib = '';
					$mooconfig = array();
					foreach($requires as $lib => $files) {
						foreach($files as $file => $flag) {
							if ($flag) {
								if ($lib!=$prevlib) {
									$dependencies.='<dt><h2>'.$lib.'</h2></dt>';
									$prevlib = $lib;
								}
								$dependencies.='<dd>'.$file.'</dd>';
								$mooconfig[]= $lib.'/'.$file.'.js';
							}
						}
					}
					if ($dependencies) {
						$content ='<dl>'. $dependencies.'</dl>';
						$content.=$this->doc->divider(5);
						$content.= '<input type="hidden" name="dependencies" value="'.urlencode(serialize($mooconfig)).'">';
						$content.='<a name="moobuttons"></a><input type="submit" name="usemoo" value="'.$LANG->getLL('mootools.button.usemoo').'"> ';
						$content.='<input type="submit" name="mergemoo" value="'.$LANG->getLL('mootools.button.mergemoo').'">';
					}
					else {
						$content = '<strong>'.$LANG->getLL('mootools.analyze.none').'</strong>';
					}
					return $content;
				}

				function file_put_content($name, $data) {
					$file = @fopen($name, 'w');
					if ($file === false) {
						return 0;
					}
					else {
						$bytes_written = fwrite($file, $data);
						fclose($file);
						return $bytes_written;
					}
				}

				function makePackitoForm() {
					global $LANG;
					return '<br /><p>'.$LANG->getLL('mootools.packito.description').'</p><br />
					<table border="0" cellspacing="1" cellpadding="2">
						<tr>
							<td>'.$LANG->getLL('mootools.packito.remote').'</td>
							<td><input type="text" name="js_remote" value="'.$_POST['js_remote'].'" id="js_remote" size="50" /></td>
						</tr>
						<tr>
							<td>'.$LANG->getLL('mootools.packito.local').'</td>
							<td><input type="file" name="js_local" size="50" /></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td><p>'.$LANG->getLL('mootools.packito.note').'</p></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td><p class="submit"><input type="submit" value="'.$LANG->getLL('mootools.button.check').'" /></p></td>
						</tr>
					</table>';
				}

				function makeProcessForm() {
					global $LANG;
					return '<br /><p>'.$LANG->getLL('mootools.extension.description').'</p><br />
					<table border="0" cellspacing="1" cellpadding="2" id="process" class="download">
						<tr class="folder">
          		<th>&nbsp;</th>
							<th>'.$LANG->getLL('mootools.extension.title').'</th>
							<th>'.$LANG->getLL('mootools.extension.extkey').'</th>
							<th>'.$LANG->getLL('mootools.extension.version').'</th>
						</tr>
							'.$this->makeCheckboxes().'
						<tr>
					</table>
					<table>
						<tr>
							<td><p class="submit"><input type="button" id="select_all" name="select_all" value="'.$LANG->getLL('mootools.button.selectall').'" /></p></td>
							<td><p class="submit"><input type="button" id="select_none" name="select_none" value="'.$LANG->getLL('mootools.button.selectnone').'" /></p></td>
							<td><p class="submit"><input type="submit" value="'.$LANG->getLL('mootools.button.check').'" /></p></td>
						</tr>
					</table>';
				}

				function makeCompressForm($compressed) {
					global $LANG;
					$out = '<br /><p>'.$LANG->getLL('mootools.compress.description').'</p><br />
					<table border="0" cellspacing="1" cellpadding="2">
						<tr>
							<td colspan="3" class="bgColor5">'.$LANG->getLL('mootools.compress.compress').'</td>
						</tr>
						<tr>
							<td colspan="3"><textarea cols="80" rows="12" name="compressdata" id="compressdata">'.stripslashes($_POST['compressdata']).'</textarea></td>
						</tr>
						<tr>
							<td align="right" width: >'.$LANG->getLL('mootools.compress.nomunge').'</td>
							<td><select name="compression">
								<option value="1"'.($_POST['compression']==1?' selected="selected"':'').'>Yes</option>
								<option value="0"'.($_POST['compression']!=1?' selected="selected"':'').'>No</option>
							</select></td>
							<td><p class="submit"><input type="submit" id="compress" name="compress" value="'.$LANG->getLL('mootools.button.compress').'" /></p></td>
						</tr>
						<tr>
							<td colspan="2">&nbsp;</td>
						</tr>
						<tr>
							<td colspan="3" class="bgColor5">'.$LANG->getLL('mootools.compress.decompress').'</td>
						</tr>
						<tr>
							<td colspan="3"><textarea cols="80" rows="12" name="decompressdata" id="decompressdata">'.$compressed.'</textarea></td>
						</tr>
						<tr>
					</table>';
					if ($sizeDiff = strlen(stripslashes($_POST['compressdata']))-strlen($compressed)) {
					 $out .= '<p>Compression reduced the size '.$sizeDiff.' bytes.</p>';
					}
					return $out;
				}
				
				function processT3mootoolsTxt($t3moofile,$dep=array()) {
					global $LANG;

					$components = Array(
						'Core' => 'Core',
						'Browser' => 'Core',
						'Array' => 'Native',
						'Event' => 'Native',
						'Function' => 'Native',
						'Hash' => 'Native',
						'Number' => 'Native',
						'String' => 'Native',
						'Class' => 'Class',
						'Class.Extras' => 'Class',
						'Element' => 'Element',
						'Element.Event' => 'Element',
						'Element.Style' => 'Element',
						'Element.Dimensions' => 'Element',
						'Selectors' => 'Utilities',
						'Domready' => 'Utilities',
						'Cookie' => 'Utilities',
						'JSON' => 'Utilities',
						'Swiff' => 'Utilities',
						'Fx' => 'Fx',
						'Fx.CSS' => 'Fx',
						'Fx.Tween' => 'Fx',
						'Fx.Morph' => 'Fx',
						'Fx.Transitions' => 'Fx',
						'Request' => 'Request',
						'Request.HTML' => 'Request',
						'Request.JSON' => 'Request',
						'More' => 'Core',
						'MooTools.Lang' => 'Core',
						'Log' => 'Core',
						'Class.Refactor' => 'Class',
						'Class.Binds' => 'Class',
						'Class.Occlude' => 'Class',
						'Chain.Wait' => 'Class',
						'Array.Extras' => 'Native',
						'Date' => 'Native',
						'Date.Extras' => 'Native',
						'Hash.Extras' => 'Native',
						'String.Extras' => 'Native',
						'String.QueryString' => 'Native',
						'URI' => 'Native',
						'URI.Relative' => 'Native',
						'Element.Forms' => 'Element',
						'Element.Measure' => 'Element',
						'Element.Pin' => 'Element',
						'Element.Position' => 'Element',
						'Element.Shortcuts' => 'Element',
						'FormValidator' => 'Forms',
						'FormValidator.Inline' => 'Forms',
						'FormValidator.Extras' => 'Forms',
						'OverText' => 'Forms',
						'Fx.Elements' => 'Fx',
						'Fx.Accordion' => 'Fx',
						'Fx.Move' => 'Fx',
						'Fx.Reveal' => 'Fx',
						'Fx.Scroll' => 'Fx',
						'Fx.Slide' => 'Fx',
						'Fx.SmoothScroll' => 'Fx',
						'Fx.Sort' => 'Fx',
						'Drag' => 'Drag',
						'Drag.Move' => 'Drag',
						'Slider' => 'Drag',
						'Sortables' => 'Drag',
						'Request.JSONP' => 'Request',
						'Request.Queue' => 'Request',
						'Request.Periodical' => 'Request',
						'Assets' => 'Utilities',
						'Color' => 'Utilities',
						'Group' => 'Utilities',
						'Hash.Cookie' => 'Utilities',
						'IframeShim' => 'Utilities',
						'Tips' => 'Interface',
						'Scroller' => 'Interface',
						'Date.English.US' => 'Localization',
						'FormValidator.English' => 'Localization'
					);

					$lines = file($t3moofile);
					$path = dirname($t3moofile);

					foreach($lines as $line) {
						$tmp = explode('=',$line);
						$option = strtolower(trim($tmp[0]));
						$params = explode(',',$tmp[1]);
						switch ($option) {
							case 'script':
								foreach($params as $file) {
									if (is_file($path.'/'.trim($file))) {
										$dep = $this->array_merge_recursive_unique($dep,$this->analyzeJS($path.'/'.trim($file)));
									}
								}
							break;
							case 'components':
								foreach($params as $component) {
									if (array_key_exists(trim($component),$components)) {
										$dep[$components[trim($component)]][trim($component)] = 1;
									}
								}
							break;
						}
					}
					return $dep;
				}


				function makeCheckboxes()	{
					$out = $this->makeCheckboxesForLocalExtensions('typo3conf/ext/'); // Local extensions
					$out.= $this->makeCheckboxesForLocalExtensions('typo3/ext/'); // Global extensions
					$out.= $this->makeCheckboxesForLocalExtensions('typo3/sysext/'); // System extensions
					return $out;
				}

				/**
				 * Generates checkboxes with the extension keys locally available for this install.
				 *
				 * @return	string		list of checkboxes for selecting the local extension to work on (or error message)
				 */
				function makeCheckboxesForLocalExtensions($localExtensionDir)	{
					global $LANG;
					$path = PATH_site.$localExtensionDir;
					if (@is_dir($path))	{
						$dirs = $this->extensionList = t3lib_div::get_dirs($path);
						if (is_array($dirs)) {
							sort($dirs);
							$c=0;
							$opt=array();
							foreach($dirs as $dirName) {
								// only display loaded extensions
								if (t3lib_extMgm::isLoaded($dirName)) {
									if (@file_exists($path.$dirName.'/t3mootools.txt')) {
										// Get extension info from ext_emconf.php
										$extInfo = $this->includeEMCONF($path.$dirName.'/ext_emconf.php', $dirName);
										if (is_array($_POST['ext'])) $selVal = in_array($path.$dirName.'/t3mootools.txt',$_POST['ext']) ? ' checked="checked"' : '';
										$c++;
										$opt[]='<tr class="option check"> 
															<td class="check"> 
																<div class="check lib_check" id="ext'.$c.'"> 
																	<input type="checkbox" name="ext[]" value="'.htmlspecialchars($path.$dirName.'/t3mootools.txt').'"'.$selVal.' />
																</div>
															</td>
															<td class="title">'.htmlspecialchars($extInfo['title']).'</td>
															<td class="extkey">'.htmlspecialchars($dirName).'</td>
															<td class="version">'.htmlspecialchars($extInfo['version']).'</td>
														</tr>
														';
										}
									}
								}
							  return implode(' ',$opt);
						}
						//else return '<tr><td>No extensions found in: "'.$path.'"</td></tr>';
					}
					else return '<tr><td>ERROR: Extensions path: "'.$path.'" not found!</td></tr>';
				}

				function compressJSFile($script) {
					switch((integer)$_POST['compression'])	{
						case 0:
							$t1 = microtime(true);

							$packer = new JavaScriptPacker($script, 'Normal', true, false);
							$script = $packer->pack();

							$t2 = microtime(true);
							$time = sprintf('%.4f', ($t2 - $t1) );
							$out = 'Mootools script packed in '.$time.' s.';
						break;
						case 1:
							$t1 = microtime(true);
							$script = get_magic_quotes_gpc() ? stripslashes($script) : $script;
							//$script = JSMin::minify($script);// JSMin v1.1.1 method
							$jsMin = new JSMin($script, false);
							$script = $jsMin->minify();
							
							$t2 = microtime(true);
							$time = sprintf('%.4f', ($t2 - $t1) );
							$out = 'Mootools script minimized in '.$time.' s.';
						break;
					}
					return $script;
				}

				function createMooFile() {
					$script = '';

					foreach($this->mooConfig as $scriptPart) {
						$script.= @file_get_contents(t3lib_extMgm::extPath($this->extKey).$this->mooVersion.'/'.$scriptPart);
					}
					$sizeBefore = strlen($script);

					switch((string)$_POST['compression'])	{
						case 'packer':
							$t1 = microtime(true);

							$packer = new JavaScriptPacker($script, 'Normal', true, false);
							$script = $packer->pack();

							$t2 = microtime(true);
							$time = sprintf('%.4f', ($t2 - $t1) );
							$out = 'Mootools script packed in '.$time.' s.';
						break;
						case 'jsmin':
							$t1 = microtime(true);

							//$script = JSMin::minify($script); JSMin v1.1.0 method
							$jsMin = new JSMin($script, false);
							$script = $jsMin->minify();

							$t2 = microtime(true);
							$time = sprintf('%.4f', ($t2 - $t1) );
							$out = 'Mootools script minimized in '.$time.' s.';
						break;
						case 'nodocs':
							$t1 = microtime(true);

							// Remove comments
							$script = preg_replace('%(/\\*([^*]|[\\r\\n]|(\\*+([^*/]|[\\r\\n])))*\\*+/)|(//.*)%', '', $script);
							// Remove empty lines
							//$script = preg_replace('/\\s+\\r\\n/', chr(10).chr(13), $script);

							$t2 = microtime(true);
							$time = sprintf('%.4f', ($t2 - $t1) );
							$out = 'Mootools script stripped of comments in '.$time.' s.';
						break;
						default:
							$out = 'Mootools script created.\nSize: '.$sizeBefore.' bytes.';
						break;
					}

					if ($_POST['compression']!='none') {
						$sizeAfter = strlen($script);
						$out.= '\nCompression ratio: '.sprintf('%01.2f', $sizeAfter/$sizeBefore).'\nSize reduced '.($sizeBefore - $sizeAfter).' bytes from '.$sizeBefore.' bytes to '.$sizeAfter.' bytes.';
					}
					$this->file_put_content(t3lib_extMgm::extPath($this->extKey).'res/mootools_v'.$_POST['version'].'.js', $script);
					$this->saveMooConf($this->mooConfig);
					return $out;
				}

				function loadMooConf() {
					if ($formVars = @file_get_contents(t3lib_extMgm::extPath($this->extKey).'res/t3mootools.cfg')) {
						return unserialize($formVars);
					}
					else {
						return array();
					}
				}

				function saveMooConf($formVars) {
			  	$this->file_put_content(t3lib_extMgm::extPath($this->extKey).'res/t3mootools.cfg', serialize($formVars));
				}

				function mergeMooConf($formVars) {
					return array_keys(array_count_values(array_merge($this->loadMooConf(),$formVars)));
				}

				function makeMooForm() {
					global $LANG;
					$formVars = $this->loadMooConf();
					$out = '

       <div id="download">
       <table id="table_head_mootools-core" class="download lib_table">
        <tr class="folder library">
          <th>
            <div style="position: relative">
              <div class="lib_check_container">
                <div class="check lib_check" id="include_mootools-core">
                  <input type="checkbox" name="include_mootools-core" value="true" checked />
                </div>
                '.$LANG->getLL('mootools.component.includelibary').'
              </div>
              <h2>Mootools 1.2.2</h2>
            </div>
          </th>
        </tr>
      </table>

      <div id="slider_mootools-core" class="lib_slider">
        <table id="download_mootools-core" class="download">
          <tr class="folder"> 
            <th colspan="3">
              <h3>Core</h3>
            </th>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="Core" deps="Core"> 
                <input type="checkbox" name="mootools-core_files[]" value="Core/Core.js"'.(in_array("Core/Core.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">Core</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.core.core').'</p>
            </td>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="Browser" deps="Core"> 
                <input type="checkbox" name="mootools-core_files[]" value="Core/Browser.js"'.(in_array("Core/Browser.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">Browser</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.core.browser').'</p>
            </td>
          </tr>
          <tr class="folder"> 
            <th colspan="3">
              <h3>Native</h3>
            </th>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="Array" deps="Core"> 
                <input type="checkbox" name="mootools-core_files[]" value="Native/Array.js"'.(in_array("Native/Array.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">Array</td>
            <td class="description"> 
             <p>'.$LANG->getLL('mootools.component.native.array').'</p>
           </td>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="Function" deps="Core"> 
                <input type="checkbox" name="mootools-core_files[]" value="Native/Function.js"'.(in_array("Native/Function.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">Function</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.native.function').'</p>
            </td>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="Number" deps="Core"> 
                <input type="checkbox" name="mootools-core_files[]" value="Native/Number.js"'.(in_array("Native/Number.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">Number</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.native.number').'</p>
           </td>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="String" deps="Core"> 
                <input type="checkbox" name="mootools-core_files[]" value="Native/String.js"'.(in_array("Native/String.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">String</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.native.string').'</p>
            </td>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="Hash" deps="Core"> 
                <input type="checkbox" name="mootools-core_files[]" value="Native/Hash.js"'.(in_array("Native/Hash.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">Hash</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.native.hash').'</p>
            </td>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="Event" deps="Browser,Array,Function,Number,String,Hash"> 
                <input type="checkbox" name="mootools-core_files[]" value="Native/Event.js"'.(in_array("Native/Event.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">Event</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.native.event').'</p>
            </td>
          </tr>
          <tr class="folder"> 
            <th colspan="3">
              <h3>Class</h3>
            </th>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="Class" deps="Core,Array,String,Function,Number,Hash,Browser"> 
                <input type="checkbox" name="mootools-core_files[]" value="Class/Class.js"'.(in_array("Class/Class.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">Class</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.class.class').'</p>
            </td>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="Class.Extras" deps="Class"> 
                <input type="checkbox" name="mootools-core_files[]" value="Class/Class.Extras.js"'.(in_array("Class/Class.Extras.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">Class.Extras</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.class.class.extras').'</p>
            </td>
          </tr>
          <tr class="folder"> 
            <th colspan="3">
              <h3>Element</h3>
            </th>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="Element" deps="Browser,Array,String,Function,Number,Hash"> 
                <input type="checkbox" name="mootools-core_files[]" value="Element/Element.js"'.(in_array("Element/Element.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">Element</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.element.element').'</p>
            </td>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="Element.Event" deps="Element,Event"> 
                <input type="checkbox" name="mootools-core_files[]" value="Element/Element.Event.js"'.(in_array("Element/Element.Event.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">Element.Event</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.element.element.event').'</p>
            </td>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="Element.Style" deps="Element"> 
                <input type="checkbox" name="mootools-core_files[]" value="Element/Element.Style.js"'.(in_array("Element/Element.Style.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">Element.Style</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.element.element.style').'</p>
            </td>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="Element.Dimensions" deps="Element"> 
                <input type="checkbox" name="mootools-core_files[]" value="Element/Element.Dimensions.js"'.(in_array("Element/Element.Dimensions.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">Element.Dimensions</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.element.element.dimensions').'</p>
            </td>
          </tr>
          <tr class="folder"> 
            <th colspan="3">
              <h3>Utilities</h3>
            </th>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="Selectors" deps="Element"> 
                <input type="checkbox" name="mootools-core_files[]" value="Utilities/Selectors.js"'.(in_array("Utilities/Selectors.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">Selectors</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.utilities.selectors').'</p>
            </td>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="Domready" deps="Element.Event"> 
                <input type="checkbox" name="mootools-core_files[]" value="Utilities/Domready.js"'.(in_array("Utilities/Domready.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">Domready</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.utilities.domready').'</p>
            </td>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="JSON" deps="Array,String,Function,Number,Hash"> 
                <input type="checkbox" name="mootools-core_files[]" value="Utilities/JSON.js"'.(in_array("Utilities/JSON.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">JSON</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.utilities.json').'</p>
            </td>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="Cookie" deps="Browser,Class,Class.Extras"> 
                <input type="checkbox" name="mootools-core_files[]" value="Utilities/Cookie.js"'.(in_array("Utilities/Cookie.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">Cookie</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.utilities.cookie').'</p>
            </td>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="Swiff" deps="Class.Extras"> 
                <input type="checkbox" name="mootools-core_files[]" value="Utilities/Swiff.js"'.(in_array("Utilities/Swiff.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">Swiff</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.utilities.swiff').'</p>
            </td>
          </tr>
          <tr class="folder"> 
            <th colspan="3">
              <h3>Fx</h3>
            </th>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="Fx" deps="Class.Extras"> 
                <input type="checkbox" name="mootools-core_files[]" value="Fx/Fx.js"'.(in_array("Fx/Fx.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">Fx</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.fx.fx').'</p>
            </td>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="Fx.CSS" deps="Fx,Element.Style"> 
                <input type="checkbox" name="mootools-core_files[]" value="Fx/Fx.CSS.js"'.(in_array("Fx/Fx.CSS.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">Fx.CSS</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.fx.fx.css').'</p>
            </td>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="Fx.Tween" deps="Fx.CSS"> 
                <input type="checkbox" name="mootools-core_files[]" value="Fx/Fx.Tween.js"'.(in_array("Fx/Fx.Tween.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">Fx.Tween</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.fx.fx.tween').'</p>
            </td>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="Fx.Morph" deps="Fx.CSS"> 
                <input type="checkbox" name="mootools-core_files[]" value="Fx/Fx.Morph.js"'.(in_array("Fx/Fx.Morph.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">Fx.Morph</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.fx.fx.morph').'</p>
            </td>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="Fx.Transitions" deps="Fx"> 
                <input type="checkbox" name="mootools-core_files[]" value="Fx/Fx.Transitions.js"'.(in_array("Fx/Fx.Transitions.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">Fx.Transitions</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.fx.fx.transitions').'</p>
            </td>
          </tr>
          <tr class="folder"> 
            <th colspan="3">
              <h3>Request</h3>
            </th>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="Request" deps="Class.Extras"> 
                <input type="checkbox" name="mootools-core_files[]" value="Request/Request.js"'.(in_array("Request/Request.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">Request</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.request.request').'</p>
            </td>
         </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="Request.HTML" deps="Request,Element"> 
                <input type="checkbox" name="mootools-core_files[]" value="Request/Request.HTML.js"'.(in_array("Request/Request.HTML.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">Request.HTML</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.request.request.html').'</p>
            </td>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="Request.JSON" deps="Request,JSON"> 
                <input type="checkbox" name="mootools-core_files[]" value="Request/Request.JSON.js"'.(in_array("Request/Request.JSON.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">Request.JSON</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.request.request.json').'</p>
            </td>
          </tr>
        </table>
      </div>

      <table id="table_head_mootools-more" class="download lib_table">
        <tr class="folder library">
          <th colspan="5">
            <div style="position: relative">
              <div class="lib_check_container">
                <div class="check lib_check" id="include_mootools-more">
                  <input type="checkbox" name="include_mootools-more" value="true"/>
                </div>
                '.$LANG->getLL('mootools.component.includelibary').'
              </div>
              <h2>Mootools More 1.2.2.2</h2>
            </div>
          </th>
        </tr>
      </table>

      <div id="slider_mootools-more" class="lib_slider">
        <table id="download_mootools-more" class="download">
          <tr class="folder"> 
            <th colspan="3"> 
              <h3>Core</h3>
            </th>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="More" deps="Core"> 
                <input type="checkbox" name="mootools-more_files[]" value="Core/More.js"'.(in_array("Core/More.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">More</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.core.more').'</p>
            </td>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="Lang" deps="More,Class.Extras"> 
                <input type="checkbox" name="mootools-more_files[]" value="Core/Lang.js"'.(in_array("Core/Lang.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">Lang</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.core.lang').'</p>
            </td>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="Log" deps="Class"> 
                <input type="checkbox" name="mootools-more_files[]" value="Core/Log.js"'.(in_array("Core/Log.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">Log</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.core.log').'</p>
            </td>
          </tr>
          <tr class="folder"> 
            <th colspan="3"> 
              <h3>Class</h3>
            </th>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="Class.Refactor" deps="More,Class"> 
                <input type="checkbox" name="mootools-more_files[]" value="Class/Class.Refactor.js"'.(in_array("Class/Class.Refactor.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">Class.Refactor</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.class.refactor').'</p>
            </td>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="Class.Binds" deps="More,Class"> 
                <input type="checkbox" name="mootools-more_files[]" value="Class/Class.Binds.js"'.(in_array("Class/Class.Binds.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">Class.Binds</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.class.binds').'</p>
            </td>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="Class.Occlude" deps="More,Class,Element"> 
                <input type="checkbox" name="mootools-more_files[]" value="Class/Class.Occlude.js"'.(in_array("Class/Class.Occlude.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">Class.Occlude</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.class.occlude').'</p>
            </td>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="Chain.Wait" deps="More,Class.Extras"> 
                <input type="checkbox" name="mootools-more_files[]" value="Class/Chain.Wait.js"'.(in_array("Class/Chain.Wait.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">Chain.Wait</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.class.chain.wait').'</p>
            </td>
          </tr>
          <tr class="folder"> 
            <th colspan="3"> 
              <h3>Native</h3>
            </th>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="Array.Extras" deps="More,Core,Array"> 
                <input type="checkbox" name="mootools-more_files[]" value="Native/Array.Extras.js"'.(in_array("Native/Array.Extras.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">Array.Extras</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.native.array.extras').'</p>
            </td>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="Date" deps="More,Core,String,Number,Array,String.Extras,Lang,Date.English.US"> 
                <input type="checkbox" name="mootools-more_files[]" value="Native/Date.js"'.(in_array("Native/Date.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">Date</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.native.date').'</p>
            </td>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="Date.Extras" deps="More,Date"> 
                <input type="checkbox" name="mootools-more_files[]" value="Native/Date.Extras.js"'.(in_array("Native/Date.Extras.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">Date.Extras</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.native.date.extras').'</p>
            </td>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="Hash.Extras" deps="More,Core"> 
                <input type="checkbox" name="mootools-more_files[]" value="Native/Hash.Extras.js"'.(in_array("Native/Hash.Extras.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">Hash.Extras</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.native.hash.extras').'</p>
            </td>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="String.Extras" deps="More,String,Array,Hash.Extras"> 
                <input type="checkbox" name="mootools-more_files[]" value="Native/String.Extras.js"'.(in_array("Native/String.Extras.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">String.Extras</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.native.string.extras').'</p>
            </td>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="String.QueryString" deps="More,String,Array"> 
                <input type="checkbox" name="mootools-more_files[]" value="Native/String.QueryString.js"'.(in_array("Native/String.QueryString.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">String.QueryString</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.native.string.querystring').'</p>
            </td>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="URI" deps="More,Function,Array,Hash,Class.Refactor"> 
                <input type="checkbox" name="mootools-more_files[]" value="Native/URI.js"'.(in_array("Native/URI.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">URI</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.native.uri').'</p>
            </td>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="URI.Relative" deps="URI"> 
                <input type="checkbox" name="mootools-more_files[]" value="Native/URI.Relative.js"'.(in_array("Native/URI.Relative.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">URI.Relative</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.native.uri.relative').'</p>
            </td>
          </tr>
          <tr class="folder"> 
            <th colspan="3"> 
              <h3>Element</h3>
            </th>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="Element.Forms" deps="More,Element"> 
                <input type="checkbox" name="mootools-more_files[]" value="Element/Element.Forms.js"'.(in_array("Element/Element.Forms.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">Element.Forms</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.element.element.forms').'</p>
            </td>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="Element.Measure" deps="More,Element.Style"> 
                <input type="checkbox" name="mootools-more_files[]" value="Element/Element.Measure.js"'.(in_array("Element/Element.Measure.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">Element.Measure</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.element.element.measure').'</p>
            </td>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="Element.Pin" deps="More,Element.Event,Element.Dimensions,Element.Style"> 
                <input type="checkbox" name="mootools-more_files[]" value="Element/Element.Pin.js"'.(in_array("Element/Element.Pin.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">Element.Pin</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.element.element.pin').'</p>
            </td>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="Element.Position" deps="More,Element.Dimensions,Element.Measure"> 
                <input type="checkbox" name="mootools-more_files[]" value="Element/Element.Position.js"'.(in_array("Element/Element.Position.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">Element.Position</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.element.element.position').'</p>
            </td>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="Element.Shortcuts" deps="More,Element.Style"> 
                <input type="checkbox" name="mootools-more_files[]" value="Element/Element.Shortcuts.js"'.(in_array("Element/Element.Shortcuts.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">Element.Shortcuts</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.element.element.shortcuts').'</p>
            </td>
          </tr>
          <tr class="folder"> 
            <th colspan="3"> 
              <h3>Forms</h3>
            </th>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="FormValidator" deps="More,Lang,Class.Extras,Class.Binds,Selectors,Element.Event,Element.Style,JSON,Date,Element.Forms,FormValidator.English"> 
                <input type="checkbox" name="mootools-more_files[]" value="Forms/FormValidator.js"'.(in_array("Forms/FormValidator.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">FormValidator</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.forms.formvalidator').'</p>
            </td>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="FormValidator.Inline" deps="More,FormValidator"> 
                <input type="checkbox" name="mootools-more_files[]" value="Forms/FormValidator.Inline.js"'.(in_array("Forms/FormValidator.Inline.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">FormValidator.Inline</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.forms.formvalidator.inline').'</p>
            </td>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="FormValidator.Extras" deps="More,FormValidator"> 
                <input type="checkbox" name="mootools-more_files[]" value="Forms/FormValidator.Extras.js"'.(in_array("Forms/FormValidator.Extras.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">FormValidator.Extras</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.forms.formvalidator.extras').'</p>
            </td>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="OverText" deps="More,Class.Extras,Element.Event,Class.Binds,Class.Occlude,Element.Position,Element.Shortcuts"> 
                <input type="checkbox" name="mootools-more_files[]" value="Forms/OverText.js"'.(in_array("Forms/OverText.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">OverText</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.forms.overtext').'</p>
            </td>
          </tr>
          <tr class="folder"> 
            <th colspan="3"> 
              <h3>Fx</h3>
            </th>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="Fx.Elements" deps="More,Fx.CSS"> 
                <input type="checkbox" name="mootools-more_files[]" value="Fx/Fx.Elements.js"'.(in_array("Fx/Fx.Elements.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">Fx.Elements</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.fx.fx.elements').'</p>
            </td>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="Fx.Accordion" deps="More,Fx.Elements,Element.Event"> 
                <input type="checkbox" name="mootools-more_files[]" value="Fx/Fx.Accordion.js"'.(in_array("Fx/Fx.Accordion.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">Fx.Accordion</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.interface.accordion').'</p>
            </td>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="Fx.Move" deps="More,Fx.Morph,Element.Position"> 
                <input type="checkbox" name="mootools-more_files[]" value="Fx/Fx.Move.js"'.(in_array("Fx/Fx.Move.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">Fx.Move</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.fx.move').'</p>
            </td>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="Fx.Reveal" deps="More,Fx.Morph,Element.Shortcuts,Element.Measure"> 
                <input type="checkbox" name="mootools-more_files[]" value="Fx/Fx.Reveal.js"'.(in_array("Fx/Fx.Reveal.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">Fx.Reveal</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.fx.reveal').'</p>
            </td>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="Fx.Scroll" deps="More,Fx,Element.Event,Element.Dimensions"> 
                <input type="checkbox" name="mootools-more_files[]" value="Fx/Fx.Scroll.js"'.(in_array("Fx/Fx.Scroll.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">Fx.Scroll</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.fx.fx.scroll').'</p>
            </td>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="Fx.Slide" deps="More,Fx,Element.Style"> 
                <input type="checkbox" name="mootools-more_files[]" value="Fx/Fx.Slide.js"'.(in_array("Fx/Fx.Slide.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">Fx.Slide</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.fx.fx.slide').'</p>
            </td>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="Fx.SmoothScroll" deps="More,Fx.Scroll,Selectors"> 
                <input type="checkbox" name="mootools-more_files[]" value="Fx/Fx.SmoothScroll.js"'.(in_array("Fx/Fx.SmoothScroll.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">Fx.SmoothScroll</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.interface.smoothscroll').'</p>
            </td>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="Fx.Sort" deps="More,Fx.Elements,Element.Dimensions,Element.Measure"> 
                <input type="checkbox" name="mootools-more_files[]" value="Fx/Fx.Sort.js"'.(in_array("Fx/Fx.Sort.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">Fx.Sort</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.fx.sort').'</p>
            </td>
          </tr>
          <tr class="folder"> 
            <th colspan="3"> 
              <h3>Drag</h3>
            </th>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="Drag" deps="More,Class.Extras,Element.Event,Element.Style"> 
                <input type="checkbox" name="mootools-more_files[]" value="Drag/Drag.js"'.(in_array("Drag/Drag.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">Drag</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.drag.drag').'</p>
            </td>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="Drag.Move" deps="More,Drag,Element.Dimensions"> 
                <input type="checkbox" name="mootools-more_files[]" value="Drag/Drag.Move.js"'.(in_array("Drag/Drag.Move.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">Drag.Move</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.drag.drag.move').'</p>
            </td>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="Slider" deps="More,Class.Binds,Drag,Element.Dimensions"> 
                <input type="checkbox" name="mootools-more_files[]" value="Drag/Slider.js"'.(in_array("Drag/Slider.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">Slider</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.interface.slider').'</p>
            </td>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="Sortables" deps="More,Drag.Move"> 
                <input type="checkbox" name="mootools-more_files[]" value="Drag/Sortables.js"'.(in_array("Drag/Sortables.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">Sortables</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.interface.sortables').'</p>
            </td>
          </tr>
          <tr class="folder"> 
            <th colspan="3"> 
              <h3>Request</h3>
            </th>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="Request.JSONP" deps="More,Log,Browser,Element,Request,Class.Extras"> 
                <input type="checkbox" name="mootools-more_files[]" value="Request/Request.JSONP.js"'.(in_array("Request/Request.JSONP.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">Request.JSONP</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.request.jsonp').'</p>
            </td>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="Request.Queue" deps="More,Request"> 
                <input type="checkbox" name="mootools-more_files[]" value="Request/Request.Queue.js"'.(in_array("Request/Request.Queue.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">Request.Queue</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.request.queue').'</p>
            </td>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="Request.Periodical" deps="More,Request,Class.Refactor"> 
                <input type="checkbox" name="mootools-more_files[]" value="Request/Request.Periodical.js"'.(in_array("Request/Request.Periodical.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">Request.Periodical</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.request.periodical').'</p>
            </td>
          </tr>
          <tr class="folder"> 
            <th colspan="3"> 
              <h3>Utilities</h3>
            </th>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="Assets" deps="More,Element.Event"> 
                <input type="checkbox" name="mootools-more_files[]" value="Utilities/Assets.js"'.(in_array("Utilities/Assets.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">Assets</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.utilities.assets').'</p>
            </td>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="Color" deps="More,Core,Array,String,Function,Number,Hash"> 
                <input type="checkbox" name="mootools-more_files[]" value="Utilities/Color.js"'.(in_array("Utilities/Color.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">Color</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.utilities.color').'</p>
            </td>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="Group" deps="More,Class.Extras"> 
                <input type="checkbox" name="mootools-more_files[]" value="Utilities/Group.js"'.(in_array("Utilities/Group.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">Group</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.utilities.group').'</p>
            </td>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="Hash.Cookie" deps="More,Class.Extras,Cookie,JSON"> 
                <input type="checkbox" name="mootools-more_files[]" value="Utilities/Hash.Cookie.js"'.(in_array("Utilities/Hash.Cookie.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">Hash.Cookie</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.utilities.hash.cookie').'</p>
            </td>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="IframeShim" deps="More,Element.Position,Element.Event,Element.Style,Class.Extras,Class.Occlude"> 
                <input type="checkbox" name="mootools-more_files[]" value="Utilities/IframeShim.js"'.(in_array("Utilities/IframeShim.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">IframeShim</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.utilities.iframshim').'</p>
            </td>
          </tr>
          <tr class="folder"> 
            <th colspan="3"> 
              <h3>Interface</h3>
            </th>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="Scroller" deps="More,Class.Extras,Element.Event,Element.Dimensions"> 
                <input type="checkbox" name="mootools-more_files[]" value="Interface/Scroller.js"'.(in_array("Interface/Scroller.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">Scroller</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.interface.scroller').'</p>
            </td>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="Tips" deps="More,Class.Extras,Element.Event,Element.Style,Element.Dimensions,Element.Measure"> 
                <input type="checkbox" name="mootools-more_files[]" value="Interface/Tips.js"'.(in_array("Interface/Tips.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">Tips</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.interface.tips').'</p>
            </td>
          </tr>
          <tr class="folder"> 
            <th colspan="3"> 
              <h3>Localization</h3>
            </th>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="Date.English.US" deps="More,Lang"> 
                <input type="checkbox" name="mootools-more_files[]" value="Localization/Date.English.US.js"'.(in_array("Localization/Date.English.US.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">Date.English.US</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.localization.date.english.us').'</p>
            </td>
          </tr>
          <tr class="option check"> 
            <td class="check"> 
              <div class="check " id="FormValidator.English" deps="More,Lang"> 
                <input type="checkbox" name="mootools-more_files[]" value="Localization/FormValidator.English.js"'.(in_array("Localization/FormValidator.English.js", $formVars)?' checked="1"':'').' />
              </div>
            </td>
            <td class="name">FormValidator.English</td>
            <td class="description"> 
              <p>'.$LANG->getLL('mootools.component.localization.formvalidator.english').'</p>
            </td>
          </tr>
        </table>
      </div>

		<h2 class="options compression-options"><a href= "#" id="compression-tog">'.$LANG->getLL('mootools.compression').'</a></h2>
		<div id="compression">
			<table id="compression-options" class="compression options">
				<tr class="option">
					<td class="check">
						<div class="check">
							<input type="radio" name="compression" value="packer" />
						</div>
					</td>
					<td class="name">'.$LANG->getLL('mootools.compression.packer.name').'</td>
					<td class="description">'.$LANG->getLL('mootools.compression.packer.description').'</td>
				</tr>
				<tr class="option">
					<td class="check">
						<div class="check">
							<input type="radio" name="compression" value="jsmin" />
						</div>
					</td>
					<td class="name">'.$LANG->getLL('mootools.compression.jsmin.name').'</td>
					<td class="description">'.$LANG->getLL('mootools.compression.jsmin.description').'</td>
				</tr>
				<tr class="option">
					<td class="check">
						<div class="check">
							<input type="radio" name="compression" value="nodocs" />	
						</div>
					</td>
					<td class="name">'.$LANG->getLL('mootools.compression.nodocs.name').'</td>
					<td class="description">'.$LANG->getLL('mootools.compression.nodocs.description').'</td>
				</tr>
				<tr class="option">
					<td class="check">
						<div class="check">
							<input type="radio" name="compression" value="none" />
						</div>
					</td>
					<td class="name">'.$LANG->getLL('mootools.compression.none.name').'</td>
					<td class="description">'.$LANG->getLL('mootools.compression.none.description').'</td>
				</tr>
			</table>
		</div>
		<input type="hidden" name="version" value="1.2.2" />
		<table>
			<tr>
				<td><p class="submit"><input type="button" id="select_all" name="select_all" value="'.$LANG->getLL('mootools.button.selectall').'" /></p></td>
				<td><p class="submit"><input type="button" id="select_none" name="select_none" value="'.$LANG->getLL('mootools.button.selectnone').'" /></p></td>
				<td><p class="submit"><input type="submit" value="'.$LANG->getLL('mootools.button.create').'" /></p></td>
			</tr>
		</table>
		</div>';
					return $out;
				}

				/**
				 * Returns the $EM_CONF array from an extensions ext_emconf.php file
				 *
				 * @param	string		Absolute path to EMCONF file.
				 * @param	string		Extension key.
				 * @return	array		EMconf array values.
				 */
				function includeEMCONF($path,$_EXTKEY)	{
					@include($path);
					if(is_array($EM_CONF[$_EXTKEY])) {
						return $EM_CONF[$_EXTKEY];
					}
					return false;
				}

				function array_merge_recursive_unique($array0, $array1) {
					$arrays = func_get_args();
					$remains = $arrays;

					// We walk through each arrays and put value in the results (without
					// considering previous value).
					$result = array();

					// loop available array
					foreach($arrays as $array) {

						// The first remaining array is $array. We are processing it. So
						// we remove it from remaing arrays.
						array_shift($remains);

						// We don't care non array param, like array_merge since PHP 5.0.
						if (is_array($array)) {
							// Loop values
							foreach($array as $key => $value) {
								if (is_array($value)) {
									// we gather all remaining arrays that have such key available
									$args = array();
									foreach($remains as $remain) {
										if(array_key_exists($key, $remain)) {
											array_push($args, $remain[$key]);
										}
									}
									if (count($args) > 2) {
										// put the recursion
										$result[$key] = call_user_func_array(__FUNCTION__, $args);
									}
									else {
										foreach($value as $vkey => $vval) {
											$result[$key][$vkey] = $vval;
										}
									}
								}
								else {
									// simply put the value
									$result[$key] = $value;
								}
							}
						}
					}
					return $result;
				}

			}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3mootools/mod1/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3mootools/mod1/index.php']);
}

// Make instance:
$SOBE = t3lib_div::makeInstance('tx_t3mootools_module1');
$SOBE->init();

// Include files?
foreach($SOBE->include_once as $INC_FILE)	include_once($INC_FILE);

$SOBE->main();
$SOBE->printContent();

?>