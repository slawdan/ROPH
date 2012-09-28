<?php
class RO_Tree_Node implements ArrayAccess,Countable
{
	protected $_name = __CLASS__;
	protected $_info = array();
	protected $_tree = array();
	
	public function __construct(){
		$this->name(uniqid(__CLASS__ . "/"));
	}
	
	public function count(){
		return count($this->_tree);
	}
	
	public function getChildren(){
		return $this->_tree;
	}
	
	public function getIterator(){
		return $this->_tree;
	}
	
	public function getArrayCopy($sort=true){
		$array = array('name' => $this->_name, 'info' => $this->_info, 'nodes' => array());
		foreach ($this->_tree as $k => $node){
			$array['nodes'][$k] = $node->getArrayCopy($sort);
		}
		if($sort)
			uasort($array['nodes'], array($this, '_cbSort'));
		return $array;
	}
	
	private function _cbSort($a, $b){
		$bo = isset($b['info']['order']) ? floatval($b['info']['order']) : 0;
		$ao = isset($a['info']['order']) ? floatval($a['info']['order']) : 0;
		return $ao > $bo ? -1 : ($ao < $bo ? 1 : ($a['name'] > $b['name'] ? 1 : ($a['name'] < $b['name'] ? -1 : 0)));
	}
	
	public function __sleep(){
		return array('_info', '_name' , '_tree');
	}
	
	/**
	 * 
	 * @param $name
	 * @return RO_Tree_Node
	 */
	public static function __new__($name = null){
		$klass = __CLASS__;
		$node = new $klass();
		is_null($name) || $node->name($name);
		return $node;
	}
	
	/**
	 * 
	 * 
	 * @param  RO_Tree_Node $node
	 * @return RO_Tree_Node
	 */
    public function append($node)
    {
    	if(!($node instanceof RO_Tree_Node)) throw new Exception('wrong node type');
    	$this->_tree[] = $node;
    	return $this;
    }
    
    /**
	 * 
	 * @return RO_Tree_Node
     */
    public function offsetGet($offset){
    	return $this->offsetExists($offset) ? $this->_tree[$offset] : null;
    }
    
    /**
	 * @return RO_Tree_Node
     */
    public function offsetSet($offset, $node){
    	if(!($node instanceof RO_Tree_Node)) throw new Exception('wrong node type');
    	if(is_null($offset))
    		$this->_tree[] = $node;
    	else
    		$this->_tree[$offset] = $node;
    	return $this;
    }
    
    public function offsetExists($offset){
    	return isset($this->_tree[$offset]);
    }
    
    public function offsetUnset($offset){
    	unset($this->_tree[$offset]);
    	return $this;
    }

    /**
     * 
     * @param  $index
     * @param RO_Tree_Node $node
     * @return RO_Tree_Node
     */
    public function insertAt($index, RO_Tree_Node $node)
    {
        $temp = $node;
        $autoChar = is_int($index) ? 1 : '_';
        $originIndex = $index; 
        while($this->offsetExists($index))
        {
            $temp = $this->offsetGet($index);
            $this->offsetSet($index, $node);
            $node = $temp;
            $index = is_int($autoChar) ? $index + $autoChar : $index . $autoChar;
        }
        $this->offsetSet($index, $node);
        return $this;
    }

    /**
     * 
     * @param $newname
     * @return RO_Tree_Node
     */
    public function name($newname = null){
    	if(is_null($newname)){
    		return $this->_name;
    	}
    	$this->_name = $newname;
    	return $this;
    }
    
    /**
     * 
     * @param $key
     * @param $value
     * @return RO_Tree_Node
     */
    public function info($key = null, $value = null){
    	if(is_null($key))
    		return $this->_info;
    	if(is_null($value)){
    		return isset($this->_info[$key]) ? $this->_info[$key] : null;
    	}
    	if($value === 'NULL')
    		unset($this->_info[$key]);
    	else
    		$this->_info[$key] = $value;
    	return $this;
    }
}
