#!/usr/bin/env php
<?php
define('RO_BASE_PATH', dirname(dirname(__FILE__)));
require RO_BASE_PATH . '/Loader.php';

RO_Loader::register(RO_BASE_PATH, 'RO_');

if(RO_Util_CLI::isCLI() && RO_Util_CLI::isMain()){
	exit(RO_Util_ActionCreator::__main__());
}

class RO_Util_ActionCreator {
	
	static public function showUsage(){
		echo <<<EOF
		
Usage: php ActionCreator.php ActionName BasePath
	ActionName		Action's name
	BasePath		Action's base path
	
The template action class file will be created just under the basepath.

EOF;
		return ;
	}
	
	static public function checkArgs(){
		if($GLOBALS['argc'] <= 2){
			return  false;
		}
		return true;
	}
	
	static public function parseArgs(){
		$args = $GLOBALS['argv'];
		$actionName = $args[1];
		$actionBasePath = $args[2] ? $args[2] : dirname(RO_BASE_PATH); 
		return array(
			'name' => $actionName,
			'path' => $actionBasePath,
		);
	}
	
	static public function createAction($args) {
		$classFile = $args['path'] . DIRECTORY_SEPARATOR . str_replace('_', DIRECTORY_SEPARATOR, $args['name']) . '.php';
		
		if(file_exists($classFile)){
			throw new Exception('class file already exists, plz delete it first.');
		}
		
		if(!file_exists(dirname($classFile))){
			if(!@mkdir(dirname($classFile), 0775, 1)){
				throw new Exception('mkdir failed');
			}
		}
		
		file_put_contents($classFile, <<<EOF
<?php

class {$args['name']} implements RO_Web_IAction{

	public function run(\$runtimeData){
		//FIXME fill code here
		
		//FIXME template things
		return false;
		/*return array(
			'' => ''
		);*/
	}
}
EOF
		);
		return $classFile;
	}
	
	static public function __main__() {
		if(!self::checkArgs()){
			self::showUsage();
			return 1;
		}
		
		try{
			$file = self::createAction(self::parseArgs());
			echo "Action created at $file\n";
			return 0;
		}catch (Exception $ex){
			echo 'Error:' . $ex->getMessage() . "\n";
			self::showUsage();
			return 1;
		}
	}
}