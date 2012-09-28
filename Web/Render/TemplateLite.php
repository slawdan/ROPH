<?php
/**
 * 
 * @copyright   Copyright (C) 2008 Rodin Shih
 * @author      Rodin Shih <schludern@gmail.com>
 * @license     http://www.opensource.org/licenses/bsd-license.php
 * @package     ROPH
 */
 
if(!defined('RO_TEMPLATELITE_CLASS_FILE'))
	throw new Exception('Must define RO_TEMPLATELITE_CLASS_FILE to your Template_Lite lib location');
require_once RO_TEMPLATELITE_CLASS_FILE;
class RO_Web_Render_TemplateLite extends RO_Flow_Work {

	protected $_targets = array('NEXT');

	/**
	 * @see RO_Flow_Work::_run()
	 *
	 * @return RO_Flow_Work
	 */
	protected function _run(){
		$this->_setNext('NEXT');
		if(!isset($this->_data['_VIEW_DATA_'])){
			return ;
		}
		$data =  $this->_data['_VIEW_DATA_'];
		$tpl = str_replace('_', DIRECTORY_SEPARATOR, $this->_data['_VIEW_']) . $this->_params['suffix'];
		$tplite = new Template_Lite();
		$tplite->template_dir = $this->_params['tpl_path'];
		$tplite->compile_dir = $this->_params['tplc_path'];
		$tplite->cache = false;
		$tplite->force_compile = true;

		$tplite->assign($data);
		$tplite->display($tpl);
	}

}
