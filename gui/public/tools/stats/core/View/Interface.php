<?php
/**
 * Piwik - Open source web analytics
 * 
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @version $Id: Interface.php 5425 2011-11-10 13:17:21Z vipsoft $
 * 
 * @category Piwik
 * @package Piwik
 */

/**
 * Rendering interface for Piwik_View and Piwik_Visualization
 *
 * @package Piwik
 */
interface Piwik_View_Interface
{
	/**
	 * Outputs the data.
	 * @return mixed (image, array, html...)
	 */
	function render();
}
