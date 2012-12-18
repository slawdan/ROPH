<?php
/**
 * 
 * @copyright   Copyright (C) 2008 Rodin Shih
 * @author      Rodin Shih <schludern@gmail.com>
 * @license     http://www.opensource.org/licenses/bsd-license.php
 * @package     ROPH
 */
 
if(!defined('PHPSERPENT_CLASS_FILE'))
	throw new Exception('Must define PHPSERPENT_CLASS_FILE to your Serpent class location');
require_once PHPSERPENT_CLASS_FILE;
class RO_Web_Render_Serpent extends RO_Flow_Work {

	protected $_targets = array('NEXT');

	/**
	 * @see RO_Flow_Work::_run()
	 */
	protected function _run(){
		$this->_setNext('NEXT');
		if(!isset($this->_data['_VIEW_DATA_']) || $this->_data['_VIEW_DATA_'] === false){
			return ;
		}
		$data =  $this->_data['_VIEW_DATA_'];
		$tpl = str_replace('_', DIRECTORY_SEPARATOR, $this->_data['_VIEW_']);
		
		// render template with data
		$serpent = RO_Template_Serpent::getHandler();
		$serpent->pass($data);
		echo $serpent->render($tpl);
	}

}
