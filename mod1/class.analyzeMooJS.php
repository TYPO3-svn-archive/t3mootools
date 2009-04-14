<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007-2008 Peter Klein (peter@umloud.dk)
*  All rights reserved
*
*  This script is free software; you can redistribute it and/or modify
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

/*
Script: class.analyzeMooJS.php
    Analyze javascript file for Mootools v1.2 dependencies.

Example:
    (start code)
    $pack = new analyzeMooJS('js.js');
    print_r($pack->dependencies());
    (end)
*/

class analyzeMooJS {
    var $version = '0.2';
	var $dependencies = array();
	var $moo = array(
		'Core' => array(
			'Core' => array(
				'Deps' => array(),
				'Source' => array('$chk(','$clear(','$defined(','$arguments(','$empty(','$lambda(','$extend(','$merge(','$each(','$pick(','$random(','$splat(','$time(','$try(','$type(')
			),
			'Browser' => array(
				'Deps' => array('Core' => 'Core'),
				'Source' => array('Browser.Features','Browser.Engine','Browser.Platform')
			)
		),
		'Native' => array(
			'Array' => array(
				'Deps' => array('Core' => 'Core'),
				'Source' => array('.each(','.every(','.filter(','.clean(','.indexOf(','.map(','.some(','.associate(','.link(','.contains(','.extend(','.getLast(','.getRandom(','.include(','.combine(','.erase(','.empty(','.flatten(','.rgbToHex(','$A(')
			),
			'Function' => array(
				'Deps' => array('Core' => 'Core'),
				'Source' => array('.create(','.pass(','.attempt(','.bind(','.bindWithEvent(','.delay(','.periodical(','.run(')
			),
			'Hash' => array(
				'Deps' => array('Core' => 'Core'),
				'Source' => array('new Hash(','.each(','.has(','.keyOf(','.hasValue(','.extend(','.combine(','.erase(','.get(','.set(','.empty(','.include(','.map(','.filter(','.every(','.some(','.getClean(','.getKeys(','.getValues(','.toQueryString(','$H(')
			),
			'String' => array(
				'Deps' => array('Core' => 'Core'),
				'Source' => array('.test(','.contains(','.trim(','.clean(','.camelCase(','.hyphenate(','.capitalize(','.escapeRegExp(','.toInt(','.toFloat(','.hexToRgb(','.rgbToHex(','.stripScripts(','.substitute(')
			),
			'Number' => array(
				'Deps' => array('Core' => 'Core'),
				'Source' => array('.limit(','.round(','.times(','.toFloat(','.toInt(')
			),
			'Event' => array(
				'Deps' => array('Core' => 'Core','Browser' => 'Core','Array' => 'Native', 'String' => 'Native', 'Function' => 'Native', 'Number' => 'Native', 'Hash' => 'Native'),
				'Source' => array('new Event(','Event.Keys.','.stop(','.stopPropagation(','.preventDefault(')
			),
		),
		'Class' => array(
			'Class' => array(
				'Deps' => array('Core' => 'Core','Array' => 'Native', 'String' => 'Native', 'Function' => 'Native', 'Number' => 'Native', 'Hash' => 'Native'),
				'Source' => array('new Class(','.implement(')
			),
			'Class.Extras' => array(
				'Deps' => array('Core' => 'Core','Array' => 'Native', 'String' => 'Native', 'Function' => 'Native', 'Number' => 'Native', 'Hash' => 'Native','Class' => 'Class'),
				'Source' => array('.chain(','.callChain(','.clearChain(','.setOptions(')
			)
		),
		'Element' => array(
			'Element' => array(
				'Deps' => array('Core' => 'Core','Browser' => 'Core', 'Array' => 'Native', 'String' => 'Native', 'Function' => 'Native', 'Number' => 'Native', 'Hash' => 'Native'),
				'Source' => array('$(','$$(','new Element(','new IFrame(','new Elements(','.getElement(','.getElements(','.getElementById(','.set(','.get(','.erase(','.match(','.inject(','.grab(','.adopt(','.wraps(','.appendText(','.dispose(','.clone(','.replaces(','.hasClass(','.addClass(','.removeClass(','.toggleClass(','.getPrevious(','.getAllPrevious(','.getNext(','.getAllNext(','.getFirst(','.getLast(','.getParent(','.getParents(','.getChildren(','.hasChild(','.empty(','.destroy(','.toQueryString(','.getSelected(','.getProperty(','.getProperties(','.setProperty(','.setProperties(','.removeProperty(','.removeProperties(','.store(','.retrieve(','.filter(')
			),
			'Element.Dimensions' => array(
				'Deps' => array('Core' => 'Core','Browser' => 'Core', 'Array' => 'Native', 'String' => 'Native', 'Function' => 'Native', 'Number' => 'Native', 'Hash' => 'Native', 'Element' => 'Element'),
				'Source' => array('.scrollTo(','.getSize(','.getScrollSize(','.getScroll(','.getPosition(','.getCoordinates(')
			),
			'Element.Event' => array(
				'Deps' => array('Core' => 'Core','Browser' => 'Core', 'Array' => 'Native', 'String' => 'Native', 'Function' => 'Native', 'Number' => 'Native', 'Hash' => 'Native', 'Event' => 'Native', 'Element' => 'Element'),
				'Source' => array('.addEvent(','.removeEvent(','.addEvents(','.removeEvents(','.fireEvent(','.cloneEvents(')
			),
			'Element.Style' => array(
				'Deps' => array('Core' => 'Core','Browser' => 'Core', 'Array' => 'Native', 'String' => 'Native', 'Function' => 'Native', 'Number' => 'Native', 'Hash' => 'Native', 'Element' => 'Element'),
				'Source' => array('.setStyle(','.getStyle(','.setStyles(','.getStyles(')
			)
		),
		'Utilities' => array(
			'Selectors' => array(
				'Deps' => array('Core' => 'Core','Browser' => 'Core', 'Array' => 'Native', 'String' => 'Native', 'Function' => 'Native', 'Number' => 'Native', 'Hash' => 'Native', 'Element' => 'Element'),
				'Source' => array('.getElements(','.getElement(','$E(','.match(',':enabled(',':empty(',':contains((',':nth.child((',':even(',':odd(',':first.child(',':last.child(',':only.child(')
			),
			'DomReady' => array(
				'Deps' => array('Core' => 'Core','Browser' => 'Core', 'Array' => 'Native', 'String' => 'Native', 'Function' => 'Native', 'Number' => 'Native', 'Hash' => 'Native', 'Event' => 'Native', 'Element' => 'Element', 'Element.Event' => 'Element'),
				'Source' => array('domready')
			),
			'JSON' => array(
				'Deps' => array('Core' => 'Core', 'Array' => 'Native', 'String' => 'Native', 'Function' => 'Native', 'Number' => 'Native', 'Hash' => 'Native'),
				'Source' => array('JSON.encode(','JSON.decode(')
			),
			'Cookie' => array(
				'Deps' => array('Core' => 'Core','Browser' => 'Core', 'Array' => 'Native', 'String' => 'Native', 'Function' => 'Native', 'Number' => 'Native', 'Hash' => 'Native', 'Class' => 'Class', 'Class.Extras' => 'Class'),
				'Source' => array('Cookie.read(','Cookie.write(','Cookie.dispose(')
			),
			'Color' => array(
				'Deps' => array('Core' => 'Core'),
				'Source' => array('new Color(','.mix(','.invert(','.setHue(','.setSaturation(','.setBrightness(','$RGB(','$HSB(','.rgbToHsb(','.hsbToRgb(')
			),
			'Swiff' => array(
				'Deps' => array('Core' => 'Core','Browser' => 'Core', 'Array' => 'Native', 'String' => 'Native', 'Function' => 'Native', 'Number' => 'Native', 'Hash' => 'Native', 'Event' => 'Native', 'Element' => 'Element', 'Element.Event' => 'Element'),
				'Source' => array('new Swiff(','Swiff.remote(')
			),
			'Group' => array(
				'Deps' => array('Core' => 'Core'),
				'Source' => array('new Group(')
			),
			'Hash.Cookie' => array(
				'Deps' => array('Core' => 'Core'),
				'Source' => array('new Hash.Cookie(','.save(','.load(')
			),
			'Assets' => array(
				'Deps' => array('Core' => 'Core'),
				'Source' => array('Asset.javascript(','Asset.css(','Asset.image(','Asset.images(')
			)
		),
		'Fx' => array(
			'Fx' => array(
				'Deps' => array('Core' => 'Core', 'Array' => 'Native', 'String' => 'Native', 'Function' => 'Native', 'Number' => 'Native', 'Hash' => 'Native', 'Class' => 'Class', 'Class.Extras' => 'Class'),
				'Source' => array('new Fx(','.start(','.set(','.cancel(','.pause(','.resume(')
			),
			'Fx.CSS' => array(
				'Deps' => array('Core' => 'Core', 'Browser' => 'Core', 'Array' => 'Native', 'String' => 'Native', 'Function' => 'Native', 'Number' => 'Native', 'Hash' => 'Native', 'Class' => 'Class', 'Class.Extras' => 'Class','Element' => 'Element','Element.Style' => 'Element', 'Fx' => 'Fx'),
				'Source' => array()
			),
			'Fx.Morph' => array(
				'Deps' => array('Core' => 'Core', 'Browser' => 'Core', 'Array' => 'Native', 'String' => 'Native', 'Function' => 'Native', 'Number' => 'Native', 'Hash' => 'Native', 'Class' => 'Class', 'Class.Extras' => 'Class','Element' => 'Element','Element.Style' => 'Element', 'Fx' => 'Fx', 'Fx.CSS' => 'Fx'),
				'Source' => array('new Fx.Morph(','.morph(')
			),
			'Fx.Scroll' => array(
				'Deps' => array('Core' => 'Core'),
				'Source' => array('new Fx.Scroll(','.toTop(','.toBottom(','.toLeft(','.toRight(','.toElement(')
			),
			'Fx.Slide' => array(
				'Deps' => array('Core' => 'Core'),
				'Source' => array('new Fx.Slide(','.slideIn(','.slideOut(','.toggle(','.hide(','.show(','.slide(')
			),
			'Fx.Transitions' => array(
				'Deps' => array('Core' => 'Core', 'Array' => 'Native', 'String' => 'Native', 'Function' => 'Native', 'Number' => 'Native', 'Hash' => 'Native', 'Class' => 'Class', 'Class.Extras' => 'Class', 'Fx' => 'Fx'),
				'Source' => array('new Fx.Transition(','Fx.Transitions')
			),
			'Fx.Tween' => array(
				'Deps' => array('Core' => 'Core', 'Browser' => 'Core', 'Array' => 'Native', 'String' => 'Native', 'Function' => 'Native', 'Number' => 'Native', 'Hash' => 'Native', 'Class' => 'Class', 'Class.Extras' => 'Class','Element' => 'Element','Element.Style' => 'Element', 'Fx' => 'Fx', 'Fx.CSS' => 'Fx'),
				'Source' => array('new Fx.Tween(','.tween(','.fade(','.highlight(')
			),
			'Fx.Elements' => array(
				'Deps' => array('Core' => 'Core'),
				'Source' => array('new Fx.Elements(')
			)
		),
		'Request' => array(
			'Request' => array(
				'Deps' => array('Core' => 'Core', 'Array' => 'Native', 'String' => 'Native', 'Function' => 'Native', 'Number' => 'Native', 'Hash' => 'Native', 'Class' => 'Class', 'Class.Extras' => 'Class'),
				'Source' => array('new Request(','.events(','.setHeader(','.getHeader(','.send(','.cancel(')
			),
			'Request.JSON' => array(
				'Deps' => array('Core' => 'Core', 'Array' => 'Native', 'String' => 'Native', 'Function' => 'Native', 'Number' => 'Native', 'Hash' => 'Native', 'Class' => 'Class', 'Class.Extras' => 'Class', 'JSON' => 'Utilities','Request' => 'Request'),
				'Source' => array('new Request.JSON(')
			),
			'Request.HTML' => array(
				'Deps' => array('Core' => 'Core', 'Browser' => 'Core', 'Array' => 'Native', 'String' => 'Native', 'Function' => 'Native', 'Number' => 'Native', 'Hash' => 'Native', 'Class' => 'Class', 'Class.Extras' => 'Class', 'Element' => 'Element','Request' => 'Request'),
				'Source' => array('new Request.HTML(','.load(')
			)
		),
		'Drag' => array(
			'Drag' => array(
				'Deps' => array('Core' => 'Core'),
				'Source' => array('new Drag(','.attach(','.detach(','.stop(','.makeResizable(')
			),
			'Drag.Move' => array(
				'Deps' => array('Core' => 'Core','Drag' => 'Drag'),
				'Source' => array('new Drag.Move(','.makeDraggable(')
			)
		),
		'Interface' => array(
			'Sortables' => array(
				'Deps' => array('Core' => 'Core','Drag' => 'Drag', 'Drag.Move' => 'Drag'),
				'Source' => array('new Sortables(','.addItems(','.removeItems(','.addLists(','.removeLists(','.serialize(')
			),
			'Tips' => array(
				'Deps' => array('Core' => 'Core'),
				'Source' => array('new Tips(')
			),
			'SmoothScroll' => array(
				'Deps' => array('Core' => 'Core', 'Fx.Scroll' => 'Fx'),
				'Source' => array('new SmoothScroll(')
			),
			'Slider' => array(
				'Deps' => array('Core' => 'Core','Drag' => 'Drag'),
				'Source' => array('new Slider(')
			),
			'Scroller' => array(
				'Deps' => array('Core' => 'Core'),
				'Source' => array('new Scroller(')
			),
			'Accordion' => array(
				'Deps' => array('Core' => 'Core','Fx.Elements' => 'Fx'),
				'Source' => array('new Accordion(','.addSection(','.display(')
			),
		)
	);

	function analyzeMooJS($file = '', $string = false) {
		if ($string || $string = @file_get_contents($file)) {
			$result = array();
			foreach ($this->moo as $dir => $files) {
				foreach ($files as $file => $info) {
					$match = $this->contains($string,$info['Source']);
					if ($match) {
						$result = array_merge($result,$info['Deps']);
						$result = array_merge($result,array($file => $dir));
					}
				}
			}
			$this->dependencies = $result;
		}
	}	

	function contains($fileData,$array = array()) {
		if (!is_array($array)) return false;
		foreach($array as $item) {
			if (strpos($fileData,$item) !== false) return true;
		}
		return false;
	}	
}
?>