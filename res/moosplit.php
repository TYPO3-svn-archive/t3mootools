<?php
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
					
$moofile = 'mootools-1.2.2-core-nc.js';
$savedir = 'Core_components';
full2component($moofile,$savedir,$components);

$moofile = 'mootools-1.2.2.1-more.js';
$savedir = 'More_components';
full2component($moofile,$savedir,$components);


function full2component($moofile,$dir,$comparray) {
	echo '<h3>Processing '.$moofile.'</h3>';
	$moodata = file_get_contents($moofile);
	// Split Mootools library souurce file at comment headers
	$components = preg_split('%(?=/\*\s+Script:)%', $moodata);

	if (!is_dir($dir)) {
		mkdir($dir);
	}
	foreach ($components as $component) {
		if (preg_match('%/\*\s+Script:\s+([^\s]+)%', $component, $regs)) {
			$file = $regs[1];
			$filename = substr($file,0,strrpos($file,"."));
			$savedir = $dir.'/'.$comparray[$filename];
			if (!is_dir($savedir)) {
				mkdir($savedir);
			}
			echo 'Component '.$savedir.'/<strong>'.$file.'</strong> extracted.<br />';
			file_put_contents($savedir.'/'.$file, $component);
		}
	}
}
?>
