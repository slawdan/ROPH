<?php 

class RO_Template_Serpent{
	static protected $_instance = null;
	protected function __construct(){}
	
	/**
	 * 
	 * @return Serpent__constrcut
	 */
	static public function getHandler(){
		if(!self::$_instance){
			$params = require('template.conf.php'); 
			$serpent = new Serpent();
			$serpent->compile_dir   = $params['compile_dir'];
			$serpent->force_compile = $params['force_compile'];
			$serpent->default_resource = 'file';
			$serpent->default_compiler = 'serpent';
			$serpent->setCharset('utf-8');
			
			// init resource
			$serpent->addPluginConfig('resource', 'file', array(
			        'template_dir' => $params['template_dir'],
			        'suffix' => $params['suffix'],
			));
			$serpent->addPluginConfig('compiler', 'serpent', array(
			        'mappings' => array(
			              'tpl' => __METHOD__,
							'quote' => 'rawurlencode',
							'json' => 'json_encode',
							'url' => 'RO_Util_Misc::url',
							'formvar' => 'RO_Util_Misc::formVar',
			        )
			));
			self::$_instance = $serpent;
		}
		return self::$_instance;
	}
}