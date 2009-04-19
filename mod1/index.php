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
				var $mooVersion = '1-2-1';
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
							<script type="text/javascript" src="../'.$this->mooVersion.'/Core/Core.js"></script>
							<script type="text/javascript" src="../'.$this->mooVersion.'/Core/Browser.js"></script>
							<script type="text/javascript" src="../'.$this->mooVersion.'/Native/Array.js"></script>
							<script type="text/javascript" src="../'.$this->mooVersion.'/Native/Function.js"></script>
							<script type="text/javascript" src="../'.$this->mooVersion.'/Native/Number.js"></script>
							<script type="text/javascript" src="../'.$this->mooVersion.'/Native/String.js"></script>
							<script type="text/javascript" src="../'.$this->mooVersion.'/Native/Hash.js"></script>
							<script type="text/javascript" src="../'.$this->mooVersion.'/Native/Event.js"></script>
							<script type="text/javascript" src="../'.$this->mooVersion.'/Element/Element.js"></script>
							<script type="text/javascript" src="../'.$this->mooVersion.'/Element/Element.Event.js"></script>
							<script type="text/javascript"  src="../'.$this->mooVersion.'/Utilities/Domready.js"></script>
							<script type="text/javascript"  src="../'.$this->mooVersion.'/Utilities/Selectors.js"></script>
							<script type="text/javascript" src="../res/mooconfig.js"></script>
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

					$this->mooConfig = $_POST['files'];
					if (!is_array($this->mooConfig) || !isset($this->mooConfig)) {
						$this->mooConfig = array('Core/Core.js');
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
					<table border="0" cellspacing="1" cellpadding="2" id="process">
						<tr class="bgColor5">
							<td>&nbsp;</td>
							<td>'.$LANG->getLL('mootools.extension.title').'</td>
							<td>'.$LANG->getLL('mootools.extension.extkey').'</td>
							<td>'.$LANG->getLL('mootools.extension.version').'</td>
						</tr>
							'.$this->makeCheckboxes().'
						<tr>
							<td colspan="4">
								<table>
									<tr>
										<td><p class="submit"><input type="button" id="select_all" name="select_all" value="'.$LANG->getLL('mootools.button.selectall').'" /></p></td>
										<td><p class="submit"><input type="button" id="select_none" name="select_none" value="'.$LANG->getLL('mootools.button.selectnone').'" /></p></td>
										<td><p class="submit"><input type="submit" value="'.$LANG->getLL('mootools.button.check').'" /></p></td>
									</tr>
								</table>
							</td>
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
						'DomReady' => 'Utilities',
						'Cookie' => 'Utilities',
						'JSON' => 'Utilities',
						'Swiff' => 'Utilities',
						'Hash.Cookie' => 'Utilities',
						'Color' => 'Utilities',
						'Group' => 'Utilities',
						'Assets' => 'Utilities',
						'Fx' => 'Fx',
						'Fx.CSS' => 'Fx',
						'Fx.Tween' => 'Fx',
						'Fx.Morph' => 'Fx',
						'Fx.Transitions' => 'Fx',
						'Fx.Slide' => 'Fx',
						'Fx.Scroll' => 'Fx',
						'Fx.Elements' => 'Fx',
						'Request' => 'Request',
						'Request.HTML' => 'Request',
						'Request.JSON' => 'Request',
						'Drag' => 'Drag',
						'Drag.Move' => 'Drag',
						'Sortables' => 'Interface',
						'Tips' => 'Interface',
						'SmoothScroll' => 'Interface',
						'Slider' => 'Interface',
						'Scroller' => 'Interface',
						'Accordion' => 'Interface',
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
										$opt[]='<tr class="bgColor4" valign="top">
															<td><input name="ext[]" type="checkbox" id="ext'.$c.'" class="extkey" value="'.htmlspecialchars($path.$dirName.'/t3mootools.txt').'"'.$selVal.' /></td>
															<td title="'.htmlspecialchars($extInfo['description']).'" nowrap><label for="ext'.$c.'">'.htmlspecialchars($extInfo['title']).'</label></td>
															<td nowrap>'.htmlspecialchars($dirName).'</td>
															<td nowrap>'.htmlspecialchars($extInfo['version']).'</td>
														</tr>';
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
<div class="">
			<table id="download">
				<tr>
					<th colspan="3"><h3>Core</h3></th>
				</tr>
				<tr class="check">
					<td class="check">
						<input type="checkbox" id="Core" deps="Core" name="files[]" value="Core/Core.js"'.(in_array("Core/Core.js", $formVars)?' checked="1"':'').' />
					</td>
					<td class="name">Core</td>
					<td class="description">
						<p>'.$LANG->getLL('mootools.component.core.core').'</p>
					</td>
				</tr>
				<tr class="check">
					<td class="check">
							<input type="checkbox" id="Browser" deps="Core" name="files[]" value="Core/Browser.js"'.(in_array("Core/Browser.js", $formVars)?' checked="1"':'').' />
					</td>
					<td class="name">Browser</td>
					<td class="description">
						<p>'.$LANG->getLL('mootools.component.core.browser').'</p>
					</td>
				</tr>
				<tr>
					<th colspan="3"><h3>Native</h3></th>
				</tr>
				<tr class="check">
					<td class="check">
							<input type="checkbox" id="Array" deps="Core" name="files[]" value="Native/Array.js"'.(in_array("Native/Array.js", $formVars)?' checked="1"':'').' />
					</td>
					<td class="name">Array</td>
					<td class="description">
						<p>'.$LANG->getLL('mootools.component.native.array').'</p>
					</td>
				</tr>
				<tr class="check">
					<td class="check">
							<input type="checkbox" id="Function" deps="Core" name="files[]" value="Native/Function.js"'.(in_array("Native/Function.js", $formVars)?' checked="1"':'').' />
					</td>
					<td class="name">Function</td>
					<td class="description">
						<p>'.$LANG->getLL('mootools.component.native.function').'</p>
					</td>
				</tr>
				<tr class="check">
					<td class="check">
							<input type="checkbox" id="Number" deps="Core" name="files[]" value="Native/Number.js"'.(in_array("Native/Number.js", $formVars)?' checked="1"':'').' />
					</td>
					<td class="name">Number</td>
					<td class="description">
						<p>'.$LANG->getLL('mootools.component.native.number').'</p>
					</td>
				</tr>
				<tr class="check">
					<td class="check">
							<input type="checkbox" id="String" deps="Core" name="files[]" value="Native/String.js"'.(in_array("Native/String.js", $formVars)?' checked="1"':'').' />
					</td>
					<td class="name">String</td>
					<td class="description">
						<p>'.$LANG->getLL('mootools.component.native.string').'</p>
					</td>
				</tr>
				<tr class="check">
					<td class="check">
							<input type="checkbox" id="Hash" deps="Core" name="files[]" value="Native/Hash.js"'.(in_array("Native/Hash.js", $formVars)?' checked="1"':'').' />
					</td>
					<td class="name">Hash</td>
					<td class="description">
						<p>'.$LANG->getLL('mootools.component.native.hash').'</p>
					</td>
				</tr>
				<tr class="check">
					<td class="check">
							<input type="checkbox" id="Event" deps="Browser,Array,Function,Number,String,Hash" name="files[]" value="Native/Event.js"'.(in_array("Native/Event.js", $formVars)?' checked="1"':'').' />
					</td>
					<td class="name">Event</td>
					<td class="description">
						<p>'.$LANG->getLL('mootools.component.native.event').'</p>
					</td>
				</tr>
				<tr>
					<th colspan="3"><h3>Class</h3></th>
				</tr>
				<tr class="check">
					<td class="check">
							<input type="checkbox" id="Class" deps="Core,Array,String,Function,Number,Hash" name="files[]" value="Class/Class.js"'.(in_array("Class/Class.js", $formVars)?' checked="1"':'').' />
					</td>
					<td class="name">Class</td>
					<td class="description">
						<p>'.$LANG->getLL('mootools.component.class.class').'</p>
					</td>
				</tr>
				<tr class="check">
					<td class="check">
							<input type="checkbox" id="Class.Extras" deps="Class" name="files[]" value="Class/Class.Extras.js"'.(in_array("Class/Class.Extras.js", $formVars)?' checked="1"':'').' />
					</td>
					<td class="name">Class.Extras</td>
					<td class="description">
						<p>'.$LANG->getLL('mootools.component.class.class.extras').'</p>
					</td>
				</tr>
				<tr>
					<th colspan="3"><h3>Element</h3></th>
				</tr>
				<tr class="check">
					<td class="check">
							<input type="checkbox" id="Element" deps="Browser,Array,String,Function,Number,Hash" name="files[]" value="Element/Element.js"'.(in_array("Element/Element.js", $formVars)?' checked="1"':'').' />
					</td>
					<td class="name">Element</td>
					<td class="description">
						<p>'.$LANG->getLL('mootools.component.element.element').'</p>
					</td>
				</tr>
				<tr class="check">
					<td class="check">
							<input type="checkbox" id="Element.Event" deps="Element,Event" name="files[]" value="Element/Element.Event.js"'.(in_array("Element/Element.Event.js", $formVars)?' checked="1"':'').' />
					</td>
					<td class="name">Element.Event</td>
					<td class="description">
						<p>'.$LANG->getLL('mootools.component.element.element.event').'</p>
					</td>
				</tr>
				<tr class="check">
					<td class="check">
							<input type="checkbox" id="Element.Style" deps="Element" name="files[]" value="Element/Element.Style.js"'.(in_array("Element/Element.Style.js", $formVars)?' checked="1"':'').' />
					</td>
					<td class="name">Element.Style</td>
					<td class="description">
						<p>'.$LANG->getLL('mootools.component.element.element.style').'</p>
					</td>
				</tr>
				<tr class="check">
					<td class="check">
							<input type="checkbox" id="Element.Dimensions" deps="Element" name="files[]" value="Element/Element.Dimensions.js"'.(in_array("Element/Element.Dimensions.js", $formVars)?' checked="1"':'').' />
					</td>
					<td class="name">Element.Dimensions</td>
					<td class="description">
						<p>'.$LANG->getLL('mootools.component.element.element.dimensions').'</p>
					</td>
				</tr>
				<tr>
					<th colspan="3"><h3>Utilities</h3></th>
				</tr>
				<tr class="check">
					<td class="check">
							<input type="checkbox" id="Selectors" deps="Element" name="files[]" value="Utilities/Selectors.js"'.(in_array("Utilities/Selectors.js", $formVars)?' checked="1"':'').' />
					</td>
					<td class="name">Selectors</td>
					<td class="description">
						<p>'.$LANG->getLL('mootools.component.utilities.selectors').'</p>
					</td>
				</tr>
				<tr class="check">
					<td class="check">
							<input type="checkbox" id="DomReady" deps="Element.Event" name="files[]" value="Utilities/DomReady.js"'.(in_array("Utilities/DomReady.js", $formVars)?' checked="1"':'').' />
					</td>
					<td class="name">DomReady</td>
					<td class="description">
						<p>'.$LANG->getLL('mootools.component.utilities.domready').'</p>
					</td>
				</tr>
				<tr class="check">
					<td class="check">
							<input type="checkbox" id="JSON" deps="Array,String,Function,Number,Hash" name="files[]" value="Utilities/JSON.js"'.(in_array("Utilities/JSON.js", $formVars)?' checked="1"':'').' />
					</td>
					<td class="name">JSON</td>
					<td class="description">
						<p>'.$LANG->getLL('mootools.component.utilities.json').'</p>
					</td>
				</tr>
				<tr class="check">
					<td class="check">
							<input type="checkbox" id="Cookie" deps="Browser,Class.Extras" name="files[]" value="Utilities/Cookie.js"'.(in_array("Utilities/Cookie.js", $formVars)?' checked="1"':'').' />
					</td>
					<td class="name">Cookie</td>
					<td class="description">
						<p>'.$LANG->getLL('mootools.component.utilities.cookie').'</p>
					</td>
				</tr>
				<tr class="check">
					<td class="check">
							<input type="checkbox" id="Swiff" deps="Class.Extras" name="files[]" value="Utilities/Swiff.js"'.(in_array("Utilities/Swiff.js", $formVars)?' checked="1"':'').' />
					</td>
					<td class="name">Swiff</td>
					<td class="description">
						<p>'.$LANG->getLL('mootools.component.utilities.swiff').'</p>
					</td>
				</tr>
				<tr class="check">
					<td class="check">
							<input type="checkbox" id="Hash.Cookie" deps="JSON,Cookie" name="files[]" value="Utilities/Hash.Cookie.js"'.(in_array("Utilities/Hash.Cookie.js", $formVars)?' checked="1"':'').' />
					</td>
					<td class="name">Hash.Cookie</td>
					<td class="description">
						<p>'.$LANG->getLL('mootools.component.utilities.hash.cookie').'</p>
					</td>
				</tr>
				<tr class="check">
					<td class="check">
							<input type="checkbox" id="Color" deps="Array,Function,Number,String,Hash" name="files[]" value="Utilities/Color.js"'.(in_array("Utilities/Color.js", $formVars)?' checked="1"':'').' />
					</td>
					<td class="name">Color</td>
					<td class="description">
						<p>'.$LANG->getLL('mootools.component.utilities.color').'</p>
					</td>
				</tr>
				<tr class="check">
					<td class="check">
							<input type="checkbox" id="Group" deps="Class.Extra" name="files[]" value="Utilities/Group.js"'.(in_array("Utilities/Group.js", $formVars)?' checked="1"':'').' />
					</td>
					<td class="name">Group</td>
					<td class="description">
						<p>'.$LANG->getLL('mootools.component.utilities.group').'</p>
					</td>
				</tr>
				<tr class="check">
					<td class="check">
							<input type="checkbox" id="Assets" deps="Element.Event" name="files[]" value="Utilities/Assets.js"'.(in_array("Utilities/Assets.js", $formVars)?' checked="1"':'').' />
					</td>
					<td class="name">Assets</td>
					<td class="description">
						<p>'.$LANG->getLL('mootools.component.utilities.assets').'</p>
					</td>
				</tr>
				<tr>
					<th colspan="3"><h3>Fx</h3></th>
				</tr>
				<tr class="check">
					<td class="check">
							<input type="checkbox" id="Fx" deps="Class.Extras" name="files[]" value="Fx/Fx.js"'.(in_array("Fx/Fx.js", $formVars)?' checked="1"':'').' />
					</td>
					<td class="name">Fx</td>
					<td class="description">
						<p>'.$LANG->getLL('mootools.component.fx.fx').'</p>
					</td>
				</tr>
				<tr class="check">
					<td class="check">
							<input type="checkbox" id="Fx.CSS" deps="Fx,Element.Style" name="files[]" value="Fx/Fx.CSS.js"'.(in_array("Fx/Fx.CSS.js", $formVars)?' checked="1"':'').' />
					</td>
					<td class="name">Fx.CSS</td>
					<td class="description">
						<p>'.$LANG->getLL('mootools.component.fx.fx.css').'</p>
					</td>
				</tr>
				<tr class="check">
					<td class="check">
							<input type="checkbox" id="Fx.Tween" deps="Fx.CSS" name="files[]" value="Fx/Fx.Tween.js"'.(in_array("Fx/Fx.Tween.js", $formVars)?' checked="1"':'').' />
					</td>
					<td class="name">Fx.Tween</td>
					<td class="description">
						<p>'.$LANG->getLL('mootools.component.fx.fx.tween').'</p>
					</td>
				</tr>
				<tr class="check">
					<td class="check">
							<input type="checkbox" id="Fx.Morph" deps="Fx.CSS" name="files[]" value="Fx/Fx.Morph.js"'.(in_array("Fx/Fx.Morph.js", $formVars)?' checked="1"':'').' />
					</td>
					<td class="name">Fx.Morph</td>
					<td class="description">
						<p>'.$LANG->getLL('mootools.component.fx.fx.morph').'</p>
					</td>
				</tr>
				<tr class="check">
					<td class="check">
							<input type="checkbox" id="Fx.Transitions" deps="Fx" name="files[]" value="Fx/Fx.Transitions.js"'.(in_array("Fx/Fx.Transitions.js", $formVars)?' checked="1"':'').' />
					</td>
					<td class="name">Fx.Transitions</td>
					<td class="description">
						<p>'.$LANG->getLL('mootools.component.fx.fx.transitions').'</p>
					</td>
				</tr>
				<tr class="check">
					<td class="check">
							<input type="checkbox" id="Fx.Slide" deps="Element.Style,Fx" name="files[]" value="Fx/Fx.Slide.js"'.(in_array("Fx/Fx.Slide.js", $formVars)?' checked="1"':'').' />
					</td>
					<td class="name">Fx.Slide</td>
					<td class="description">
						<p>'.$LANG->getLL('mootools.component.fx.fx.slide').'</p>
					</td>
				</tr>
				<tr class="check">
					<td class="check">
							<input type="checkbox" id="Fx.Scroll" deps="Element.Dimensions,Element.Event,Fx" name="files[]" value="Fx/Fx.Scroll.js"'.(in_array("Fx/Fx.Scroll.js", $formVars)?' checked="1"':'').' />
					</td>
					<td class="name">Fx.Scroll</td>
					<td class="description">
						<p>'.$LANG->getLL('mootools.component.fx.fx.scroll').'</p>
					</td>
				</tr>
				<tr class="check">
					<td class="check">
							<input type="checkbox" id="Fx.Elements" deps="Fx.CSS" name="files[]" value="Fx/Fx.Elements.js"'.(in_array("Fx/Fx.Elements.js", $formVars)?' checked="1"':'').' />
					</td>
					<td class="name">Fx.Elements</td>
					<td class="description">
						<p>'.$LANG->getLL('mootools.component.fx.fx.elements').'</p>
					</td>
				</tr>
				<tr>
					<th colspan="3"><h3>Request</h3></th>
				</tr>
				<tr class="check">
					<td class="check">
							<input type="checkbox" id="Request" deps="Class.Extras,Element" name="files[]" value="Request/Request.js"'.(in_array("Request/Request.js", $formVars)?' checked="1"':'').' />
					</td>
					<td class="name">Request</td>
					<td class="description">
						<p>'.$LANG->getLL('mootools.component.request.request').'</p>
					</td>
				</tr>
				<tr class="check">
					<td class="check">
							<input type="checkbox" id="Request.HTML" deps="Request" name="files[]" value="Request/Request.HTML.js"'.(in_array("Request/Request.HTML.js", $formVars)?' checked="1"':'').' />
					</td>
					<td class="name">Request.HTML</td>
					<td class="description">
						<p>'.$LANG->getLL('mootools.component.request.request.html').'</p>
					</td>
				</tr>
				<tr class="check">
					<td class="check">
							<input type="checkbox" id="Request.JSON" deps="Request,JSON" name="files[]" value="Request/Request.JSON.js"'.(in_array("Request/Request.JSON.js", $formVars)?' checked="1"':'').' />
					</td>
					<td class="name">Request.JSON</td>
					<td class="description">
						<p>'.$LANG->getLL('mootools.component.request.request.json').'</p>
					</td>
				</tr>
				<tr>
					<th colspan="3"><h3>Drag</h3></th>
				</tr>
				<tr class="check">
					<td class="check">
							<input type="checkbox" id="Drag" deps="Core,Class.Extras,Element.Event,Element.Style" name="files[]" value="Drag/Drag.js"'.(in_array("Drag/Drag.js", $formVars)?' checked="1"':'').' />
					</td>
					<td class="name">Drag</td>
					<td class="description">
						<p>'.$LANG->getLL('mootools.component.drag.drag').'</td>
				</tr>
				<tr class="check">
					<td class="check">
							<input type="checkbox" id="Drag.Move" deps="Drag,Element.Dimensions" name="files[]" value="Drag/Drag.Move.js"'.(in_array("Drag/Drag.Move.js", $formVars)?' checked="1"':'').' />
					</td>
					<td class="name">Drag.Move</td>
					<td class="description">
						<p>'.$LANG->getLL('mootools.component.drag.drag.move').'</p>
					</td>
				</tr>
				<tr>
					<th colspan="3"><h3>Interface</h3></th>
				</tr>
				<tr class="check">
					<td class="check">
							<input type="checkbox" id="Sortables" deps="Drag.Move" name="files[]" value="Interface/Sortables.js"'.(in_array("Interface/Sortables.js", $formVars)?' checked="1"':'').' />
					</td>
					<td class="name">Sortables</td>
					<td class="description">
						<p>'.$LANG->getLL('mootools.component.interface.sortables').'</p>
					</td>
				</tr>
				<tr class="check">
					<td class="check">
							<input type="checkbox" id="Tips" deps="Class.Extras,Element.Event,Element.Style,Element.Dimensions" name="files[]" value="Interface/Tips.js"'.(in_array("Interface/Tips.js", $formVars)?' checked="1"':'').' />
					</td>
					<td class="name">Tips</td>
					<td class="description">
						<p>'.$LANG->getLL('mootools.component.interface.tips').'</p>
					</td>
				</tr>
				<tr class="check">
					<td class="check">
							<input type="checkbox" id="SmoothScroll" deps="Fx.Scroll" name="files[]" value="Interface/SmoothScroll.js"'.(in_array("Interface/SmoothScroll.js", $formVars)?' checked="1"':'').' />
					</td>
					<td class="name">SmoothScroll</td>
					<td class="description">
						<p>'.$LANG->getLL('mootools.component.interface.smoothscroll').'</p>
					</td>
				</tr>
				<tr class="check">
					<td class="check">
							<input type="checkbox" id="Slider" deps="Drag,Element.Dimensions" name="files[]" value="Interface/Slider.js"'.(in_array("Interface/Slider.js", $formVars)?' checked="1"':'').' />
					</td>
					<td class="name">Slider</td>
					<td class="description">
						<p>'.$LANG->getLL('mootools.component.interface.slider').'</p>
					</td>
				</tr>
				<tr class="check">
					<td class="check">
							<input type="checkbox" id="Scroller" deps="Class.Extras,Element.Event,Element.Dimensions" name="files[]" value="Interface/Scroller.js"'.(in_array("Interface/Scroller.js", $formVars)?' checked="1"':'').' />
					</td>
					<td class="name">Scroller</td>
					<td class="description">
						<p>'.$LANG->getLL('mootools.component.interface.scroller').'</p>
					</td>
				</tr>
				<tr class="check last">
					<td class="check">
							<input type="checkbox" id="Accordion" deps="Fx.Elements,Element.Event" name="files[]" value="Interface/Accordion.js"'.(in_array("Interface/Accordion.js", $formVars)?' checked="1"':'').' />
					</td>
					<td class="name">Accordion</td>
					<td class="description">
						<p>'.$LANG->getLL('mootools.component.interface.accordion').'</p>
					</td>
				</tr>
			</table>
		</div>
		<h2 class="options compression-options"><a href= "#" id="compression-tog">'.$LANG->getLL('mootools.compression').'</a></h2>
			<table id="download-options">
				<tr class="radio">
					<td class="check">
							<input type="radio" name="compression" value="packer" />
					</td>
					<td class="name">'.$LANG->getLL('mootools.compression.packer.name').'</td>
					<td class="description">'.$LANG->getLL('mootools.compression.packer.description').'</td>
				</tr>
				<tr class="radio">
					<td class="check">
							<input type="radio" name="compression" value="jsmin" />
					</td>
					<td class="name">'.$LANG->getLL('mootools.compression.jsmin.name').'</td>
					<td class="description">'.$LANG->getLL('mootools.compression.jsmin.description').'</td>
				</tr>
				<tr class="radio">
					<td class="check">
							<input type="radio" name="compression" value="nodocs" />	
					</td>
					<td class="name">'.$LANG->getLL('mootools.compression.nodocs.name').'</td>
					<td class="description">'.$LANG->getLL('mootools.compression.nodocs.description').'</td>
				</tr>
				<tr class="radio last">
					<td class="check">
							<input type="radio" name="compression" value="none" />
					</td>
					<td class="name">'.$LANG->getLL('mootools.compression.none.name').'</td>
					<td class="description">'.$LANG->getLL('mootools.compression.none.description').'.</td>
				</tr>
			</table>
		<input type="hidden" name="version" value="1.2" />
		<table>
			<tr>
				<td><p class="submit"><input type="button" id="select_all" name="select_all" value="'.$LANG->getLL('mootools.button.selectall').'" /></p></td>
				<td><p class="submit"><input type="button" id="select_none" name="select_none" value="'.$LANG->getLL('mootools.button.selectnone').'" /></p></td>
				<td><p class="submit"><input type="submit" value="'.$LANG->getLL('mootools.button.create').'" /></p></td>
			</tr>
		</table>';
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