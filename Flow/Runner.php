<?php
/**
 * 
 * @copyright   Copyright (C) 2008 Rodin Shih
 * @author      Rodin Shih <schludern@gmail.com>
 * @license     http://www.opensource.org/licenses/bsd-license.php
 * @package     ROPH
 */
 
class RO_Flow_Runner{
	/**
	 * current work
	 *
	 * @var string
	 */
	protected $_cur;
	/**
	 * works
	 *
	 * @var array
	 */
	protected $_works;
	/**
	 * shared data
	 *
	 * @var ArrayObject
	 */
	protected $_data;
	
	public function __construct(array $data) {
		$this->_cur = '';
		$this->_works = array();
		$this->_data = new ArrayObject($data);
	}
	
	public function addWork(RO_Flow_Work $work, array $targets, $workName = null) {
		if(!$workName || !is_string($workName)){
			$workName = strval($work);
		}
		
		$defined = $work->getTargetsDefine();
		$key = array_keys($targets);
		
		if(($defines = array_diff($defined, $key)) || ($keys = array_diff($key, $defined))){
			throw new Exception("Defined targets is not match the demands of \"{$workName}\", " . ($defines ? "\"" . implode(',', $defines) . "\" should been given " : "") . ($keys ? "but \"" . implode(',', $keys) . "\" should not been given"  : ""));
		}
		$this->_works[$workName] = array('work' => $work, 'targets' => $targets);
	}

	public function setStart($workName) {
		if(array_key_exists($workName, $this->_works)){
			$this->_cur = $workName;
		}else{
			throw new Exception("{$workName} must already added to flow");
		}
	}
	
	protected function _checkTargets() {
		foreach ($this->_works as $name => $work){
			foreach ($work['targets'] as $tname => $target){
				if(array_key_exists($target, $this->_works)){
				}else{
					throw new Exception("Target \"{$target}\" mapped to \"{$tname}\" of work \"{$name}\" not added in flow");
				}
			}
		}
	}
	
	public function getData() {
		return $this->_data;
	}
	
	
	public function run() {
		if(!$this->_cur){
			throw new Exception('start work not set');
		}
		$this->_checkTargets();
		while ($this->_cur) {
			$work = $this->_works[$this->_cur];
			try{
				$w = $work['work'];
				$t = $w->run($this->_data);
				if(array_key_exists($t, $work['targets'])){
					$this->_cur = $work['targets'][$t];
				}else{
					if ($w instanceof RO_Flow_EndWork) {
						$this->_cur = '';
					}else{
						throw new Exception('Target ' . $t . ' not in defined targets[' . implode(',', $work['targets']) . ']');
					}
				}
			}catch (RO_Flow_NeedResume $ex){
				return serialize($this);
			}
		}
		return "";
	}
	
	static public function resume($freezedFlow) {
		return unserialize($freezedFlow);
	}
}
