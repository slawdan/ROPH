<?php 

/**
 * Commandline options holder
 * 
 * @package   Cli
 * @author    Rodin Shih <schludern@gmail.com>
 */
class RO_Cli_Option {
    const T_STR = 0;
    const T_BOOL = 10; 
    const T_INT = 20;
    const T_FLOAT = 30;
    const T_ENUM = 40;

    protected $_short = '';
    protected $_long = '';
    protected $_prompt = '';
    protected $_desc = '';
    
    protected $_type = self::T_STR;
    protected $_min = 0;
    protected $_max = 1;
    protected $_options = array();
    protected $_found = false;
    
    protected $_value = array();
    protected $_default = null;

    public function __construct($short = '', $long = ''){
        if(empty($short) && empty($long)) {
            throw new Exception('Need at least the short or the long option name');
        }
        $this->short($short);
        $this->long($long);
    }
    
    protected function _type($type, $default, $options = array()){
        $this->_type = $type;
        $this->_default = $default;
        $this->_options = $options;
        return $this;
    }
    
    public function int($default = NULL){
        return $this->_type(self::T_INT, $default);
    }
    
    public function float($default = NULL){
        return $this->_type(self::T_FLOAT, $default);
    }
    
    public function str($default = NULL){
        return $this->_type(self::T_STR, $default);
    }
    
    public function enum($default = NULL, $e0 = ''){
        $args = func_get_args();
        array_shift($args);
        return $this->_type(self::T_ENUM, $default, $args);
    }
    
    public function min($min) {
        return $this->need($min, $this->_max);
    }
    
    public function max($max) {
        return $this->need($this->_min, $max);
    }
    
    public function need($min = 0, $max = 1){
        if(!is_numeric($min) || $min < 0) {
            throw new Exception('Min must greater than 0');
        }
        if(!is_numeric($max) || $max < $min) {
            throw new Exception('Max must greater than min');
        }
        
        $this->_min = $min;
        $this->_max = $max;
        return $this;
    }

    public function toList($min = 0, $max = PHP_INT_MAX){
        return $this->need($min, $max);
    }
    
    public function bool($default = NULL) {
        return $this->_type(self::T_BOOL, $default);
    }
    
    public function long($option) {
        $this->_long = $option;
        return $this;
    }
    
    public function short($option){
        $this->_short = $option;
        return $this;
    }
    
    public function prompt($promp) {
        $this->_prompt = $promp;
        return $this;
    }
    
    public function desc($desc) {
        $this->_desc = $desc;
        return $this;
    }
    
    public function isOk(){
        $len = count($this->_value);
        return ($this->_min <= $len && $len <= $this->_max);
    }
    
    public function getOptionName(){
        return array($this->_short, $this->_long);
    }
    
    public function getPrompt(){
        return $this->_prompt;
    }
    
    public function getOptionStr(){
        return ($this->_short ? "-{$this->_short}" : '') .
                ($this->_short && $this->_long ? ', ' : '') . 
                ($this->_long ? "--{$this->_long}" : '') . 
                ($this->_prompt ? " {$this->_prompt}" : ''); 
    }
    
    public function getDesc(){
        return $this->_desc;
    }
    
    public function get(){
        if($this->_max > 1) {
            if($this->_value) {
                return $this->_value;
            }elseif($this->_min < 1) {
                return $this->_value;
            } else {
                return $this->_default;
            }
        }
        if(isset($this->_value[0])){
            return $this->_value[0];
        } else {
            return $this->_default;
        }
    }
    
    public function setFound(){
        $this->_found = true;
        if($this->_type === self::T_BOOL) {
            $this->_value[0] = true;
        }
        return $this;
    }
    
    public function set($v) {
        switch ($this->_type) {
            case self::T_BOOL:
                // bool's value is depends on $this->setFound
                break;
            case self::T_INT:
                if(strval($v) !== strval(intval($v))){
                    throw new Exception('Type Error');
                }
                $this->_value[] = intval($v);
                break;
            case self::T_FLOAT:
                if(!is_numeric($v)) {
                    throw new Exception('Type error');
                }
                $this->_value[] = floatval($v);
                break;
            case self::T_ENUM:
                if(!in_array($v, $this->_options, true)){
                    var_dump($this->_options);
                    echo $v;
                    throw new Exception('Enum value error');
                }
                $this->_value[] = $v;
                break;
            case self::T_STR:
            default:
                $this->_value[] = strval($v);
        }
    }
    
    /**
     * 
     * 
     * @param string $short
     * @param string $long
     * 
     * @return RO_Cli_Option
     */
    static public function make($short, $long = ''){
        $opt = new self($short, $long);
        return $opt;
    }
}