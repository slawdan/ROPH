<?php 

class RO_Tree_TraceNode extends RO_Tree_Node{
	protected $_trace = array(null, null);
	
	/**
	 * (non-PHPdoc)
	 * @see RO/Tree/RO_Tree_Node#append($node)
	 */
	public function append($node){
		parent::append($node);
		$node->setTrace($this, $this->count() - 1);
		return $this;
	}
	
	public function __sleep(){
		$p = parent::__sleep();
		// append the protected property $_trace to the serializition array
		$p[] = '_trace';
		return $p;
	}
	
	/**
	 * @param int	$offset
	 * @param RO_Tree_TraceNode $node
	 * @see RO/Tree/RO_Tree_Node#offsetSet($offset, $node)
	 */
	public function offsetSet($offset, $node){
		parent::offsetSet($offset, $node);
		$node->setTrace($this, is_null($offset) ? $this->count() - 1 : $offset);
		return $this;
	}
	
	/**
	 * 
	 * @param RO_Tree_TraceNode $parentNode
	 * @param $index
	 * @return RO_Tree_TraceNode
	 */
	public function setTrace(RO_Tree_TraceNode $parentNode, $index){
		$this->_trace = array($parentNode, $index);
		return $this;
	}
	
	/**
	 * 
	 * @return RO_Tree_TraceNode
	 */
	public function getParent(){
		return $this->_trace[0];
	}
	
	public function getIndex(){
		return $this->_trace[1];
	}
	
	/**
	 * 
	 * @param RO_Tree_TraceNode $node
	 * @param array	            $path
	 * @return RO_Tree_TraceNode
	 */
	static public function route(RO_Tree_TraceNode $node, array $routes){
		for($n = 0, $nCount = count($routes); $n < $nCount; $n ++){
			if($routes[$n] === '') continue;
			if($node->offsetExists($routes[$n])){
				$node = $node->offsetGet($routes[$n]);
			}else{
				$walked = self::routeArrayToString(array_slice($routes, 0, $n, true));
				$routePath = self::routeArrayToString($routes);
				throw new Exception("Can't found node $routePath under node $walked.");
			}
		}
		return $node;
	}
	
	static public function trace(RO_Tree_TraceNode $node){
		$route = array();
		do{
			$offset = $node->getIndex();
			$route[] = $offset;
			$node = $node->getParent();
		}while($node);
		return array_reverse($route);
	}
	
	static public function routeStringToArray($route, $delimiter = '/'){
		return explode($delimiter, preg_replace("/\\{$delimiter}+/", $delimiter, $delimiter . $route));
	}
	
	static public function routeArrayToString(array $route, $delimiter = '/'){
		return preg_replace("/\\{$delimiter}+/", $delimiter, $delimiter . implode($delimiter, $route));
	}
	
	static public function normalizeRouteString($route, $delimiter = '/'){
		return preg_replace("/\\{$delimiter}+/", $delimiter, $delimiter . $route);
	}
	
	/**
	 * 
	 * @param $name
	 * @return RO_Tree_TraceNode
	 */
	static public function __new__($name = null){
		$klass = __CLASS__;
		$node = new $klass();
		is_null($name) || $node->name($name);
		return $node;
	}
}

