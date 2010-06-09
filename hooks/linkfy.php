<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Linkfy Links Hook - Load All Events
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package	   Ushahidi - http://source.ushahididev.com
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class linkfy {
	/**
	 * Registers the main event add method
	 */
	public function __construct()
	{	
		// Hook into routing
		Event::add('system.pre_controller', array($this, 'add'));
	}
	
	/**
	 * Adds all the events to the main Ushahidi application
	 */
	public function add()
	{
		// Only add the events if we are on that controller
		if (Router::$controller == 'reports' AND Router::$method == 'view')
		{
			Event::add('ushahidi_filter.report_description', array($this, '_make_clickable'));
		}
	}
	
	/**
	 * Put clickable links into description
	 */
	public function _make_clickable()
	{
		// Access the report description
		$report_description = Event::$data;
		
		$report_description = $this->_convert_to_clickable($report_description);
		
		// Return new description
		Event::$data = $report_description;
	}
	
	
	/**
	 * Convert dead anchors into clickable links.
	 *
	 * @param   string   dead anchors
	 * @return  string
	 */
	private function _convert_to_clickable($text)
	{
		// Finds all http/https/ftp/ftps links that are not part of an existing html anchor
		if (preg_match_all('~\b(?<!href="|">)(?:ht|f)tps?://\S+(?:/|\b)~i', $text, $matches))
		{	
			//checking for URLs
			foreach ($matches[0] as $match)
			{
				$clickable_link = "<a href=\" $match \"> $match </a>";
				$text = str_replace($match, $clickable_link, $text);
				
			}
		
		} 
		
		//checking for emails
		if( preg_match_all('/[a-z0-9]+([\\+_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}/i',
			$text, $matches) ) {
			
			foreach( $matches[0] as $match ) {
				$clickable_link = "<a href=\"mailto:$match\"> $match </a>";
				$text = str_replace($match, $clickable_link, $text );
			}
		}
		
		return $text;
	}
	
}

new linkfy;