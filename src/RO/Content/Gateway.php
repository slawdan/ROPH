<?php 

class RO_Content_Gateway{
	
	/**
	 * @var PDO
	 */
	protected $_db = null;
	
	/**
	 * @var PDOStatement
	 */
	protected $_stmt = null;
	
	protected $_contentTable = 'content';
	protected $_contentClass = 'RO_Content_Entry';
	
	protected $_indexedData = array('id', 'create_time', 'update_time');
	
	/**
	 * 
	 * @param PDO $db
	 * @return RO_Content_Gateway
	 */
	public function setDb(PDO $db){
		$this->_db = $db;
		return $this;
	}
	
	protected function _prepareForPersistent(array $data){
		$ret = array();
		foreach($this->_indexedData as $k){
			$ret[$k] = $data[$k];
			unset($data[$k]);
		}
		
		$ret['bin_data'] = serialize($data);
		return $ret;
	}
	
	/**
	 * 
	 * @param array $sql
	 * @param $data
	 * @return PDOStatement
	 */
	protected function _query($sql, array $data){
		if($sql !== '_'){
			if(!$this->_db){
				throw new Exception('Gateway does not have a Db instance yet');
			}
			$this->_stmt = $this->_db->prepare($sql);
		}elseif(!$this->_stmt){
			throw new Exception('Statement not prepared yet');
		}
		$this->_stmt->execute($data);
		return $this->_stmt;
	}
	
	/**
	 * 
	 * @param unknown_type $where
	 * @param array $data
	 * @return array
	 */
	public function getContents($where = '', array $data = array()){
		$where && $where[0] !== ' ' AND $where = 'WHERE ' . $where;
		$result = $this->_query(
			"SELECT * FROM `{$this->_contentTable}` $where",
			$data
		)->fetchAll(PDO::FETCH_CLASS, $this->_contentClass);
		return $result;
	}
	
	public function count($where = '', array $data = array()){
		$where && $where[0] !== ' ' AND $where = 'WHERE ' . $where;
		$result = $this->_query(
			"SELECT COUNT(*) FROM `{$this->_contentTable}` $where",
			$data
		)->fetchColumn(0);
		return intval($result);
	}
	
	/**
	 * 
	 * @param $contentId
	 * @return RO_Content_Entry
	 */
	public function get($contentId){
		$sql = "SELECT * FROM `{$this->_contentTable}` WHERE `id` = :id";
		$data = $this->_query($sql, array(':id' => $contentId));
		return $data->fetchObject($this->_contentClass);
	}
	
	/**
	 * 
	 * @param RO_Content_Entry $contentObj
	 * @return int
	 */
	public function replace(RO_Content_Entry $contentObj){
		if(!$contentObj instanceof $this->_contentClass){
			throw new Exception('Unsupported object type');
		}
		/* @var $contentObj RO_Content_Record */
		$sql = "REPLACE INTO `{$this->_contentTable}` (%s) VALUES (%s)";
		$updated = $this->_prepareForPersistent($contentObj->getArrayCopy());
		if(!$updated){
			return 1;
		}
		$fields = array_keys($updated);
		$sql = sprintf($sql, '`' . implode('`,`', $fields) . '`', ':' . implode(',:', $fields));
		$data = $this->_query($sql, $updated);
		$id = $this->_db->lastInsertId();
		$contentObj->id = $id;
		return $data->rowCount();
	}
	
	/**
	 * 
	 * @return RO_Content_Entry
	 */
	public function createFrom(array $requestData){
		$record = new $this->_contentClass();
		
		//need some fake to pass the recurvesition
		isset($requestData['id']) || $requestData['id'] = 0;
		$requestData['create_time'] = time();
		$requestData['update_time'] = time();
		//Every thing specified by the indexedData must exist and validated;
		foreach($this->_indexedData as $k){
			$record->$k = $requestData[$k];
			unset($requestData[$k]);
		}
		
		//The other things can be anything and may be without validation.
		foreach($requestData as $k => $v){
			$record->$k = $v;
		}
		
		$recordExist = $this->get($record->id);
		if(!$recordExist){
			return $record;
		}else{
			$record->create_time = $recordExist->create_time;
			return $record;
		}
	}
	
	/**
	 * 
	 * @param RO_Content_Entry $contentObj
	 * @return int
	 */
	public function delete(RO_Content_Entry $contentObj){
		if(!$contentObj instanceof $this->_contentClass){
			throw new Exception('Unsupported object type');
		}
		
		$sql = "DELETE FROM `$this->_contentTable` WHERE `id` = :id";
		
		$data = $this->_query($sql, array(':id' => $contentObj->id));
		return $data->rowCount();
	}
}