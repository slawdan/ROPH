<?php 

/**
 * Command line option parser
 * 
 * @author Rodin Shih <schludern@gmail.com>
 */
class RO_Cli_Parser extends ArrayObject{
    
    const REST = '...';
    
    protected $_cmdUsage;
    protected $_details;
    protected $_usage;
    protected $_options = array();

    public function __construct($cmdUsage = NULL, $details = NULL){
        $this->program($cmdUsage, $details);
    }
    
    /**
     * 
     * @param string $cmdUsage
     * @param string $details
     * 
     * @return RO_Cli_Parser
     */
    public function program($cmdUsage = NULL, $details = NULL){
        $this->_cmdUsage = $cmdUsage;
        $this->_details = $details;
        return $this;
    }
    
    /**
     * 
     * @param string $usage
     * 
     * @return RO_Cli_Parser
     */
    public function usage($usage) {
        $this->_usage = $usage;
        return $this;
    }
    
    /**
     * 
     * @param array $args
     * 
     * @return RO_Cli_Parser
     */
    public function parse(array $args = NULL){
        if($args === NULL) {
            $args = $_SERVER['argv'];
            array_shift($args);
        }
        
        $cur = self::REST;
        foreach ($args as $arg) {
            if($this->_isName($arg)) {
                $name = $this->_extractName($arg);
                if(isset($this[$name])) {
                    $cur = $name;
                    $this[$name]->setFound();
                } else {
                    throw new Exception('Unrecognized option name:' . $arg);
                }
            } else {
                $this[$cur]->set($this->_trimValue($arg));
                $cur = self::REST;
            }
        }
        
        foreach ($this as $k => $v) {
            if($v->isOk()) {
                continue;
            }
            
            throw new Exception('Parse option error: ' . $k);
        }
        
        return $this;
    }
    
    public function get($name){
        if(isset($this[$name])) {
            return $this[$name]->get();
        }
        throw new Exception('Option undefined');
    }
    
    public function getRest(){
        return $this->get(self::REST);
    }
    
    public function printUsage() {
        echo PHP_EOL;
        if($this->_cmdUsage === NULL) {
            echo "Usage: program [options]";
            if(isset($this[self::REST])) {
                $prompt = $this[self::REST]->getPrompt();
                if(!$prompt) {
                    $prompt = '...';
                }
                echo " " . $prompt;
            }
        } else {
            echo $this->_cmdUsage;
        }
        
        if($this->_details) {
            echo PHP_EOL;
            echo $this->_details;
        }
        
        echo PHP_EOL;
        if($this->_usage) {
            echo $this->_usage;
            return;
        }
        
        $options = $this->_options;
        
        echo PHP_EOL . "Options:" . PHP_EOL;
        $optLength = 0;
        foreach ($options as $v) {
            /* @var $v RO_Cli_Option */
            $len = strlen($v->getOptionStr());
            $optLength = max($len, $optLength);
        }
        
        $spaces = str_repeat(' ', $optLength);
        $pattern = "  %s  %s" . PHP_EOL;
        foreach ($options as $v) {
            /* @var $v RO_Cli_Option */
            $desc = $v->getDesc();
            $descLines = explode("\n", $desc);

            list($s, ) = $v->getOptionName();
            if($s === self::REST){
                printf($pattern, 
                        str_pad($v->getPrompt() ? $v->getPrompt() : '...', $optLength, ' ', STR_PAD_RIGHT), 
                        array_shift($descLines));
            } else {
                printf($pattern, 
                        str_pad($v->getOptionStr(), $optLength, ' ', STR_PAD_RIGHT), 
                        array_shift($descLines));
            }
            
            foreach ($descLines as $d) {
                printf($pattern, $spaces, array_shift($descLines));
            }
        }
        
        echo PHP_EOL;
        return $this;
    }
    
    protected function _trimValue($v) {
        if(isset($v[0]) && isset($v[1])) {
            if($v[0] === $v[1] && ($v[0] === '"' || $v[0] === "'")) {
                return substr($v, 1, strlen($v) - 2);
            }
        }
        return trim($v);
    }
    
    protected function _extractName($arg) {
        $arg = trim($arg);
        if($this->_isLongName($arg)) {
            return substr($arg, 2);
        }
        return substr($arg, 1);
    }

    protected function _isLongName($arg) {
        return $this->_isName($arg) && (isset($arg[1]) && $arg[1] === '-');
    }
    
    protected function _isName($arg){
        return isset($arg[0]) && $arg[0] === '-';
    }
    

    static public function makeRestOption() {
        return RO_Cli_Option::make(self::REST)->toList();
    }
    
    /**
     * 
     * @return RO_Cli_Parser
     */
    static public function make(){
        return new RO_Cli_Parser();
    }
    
    /**
     * 
     * @param RO_Cli_Option $v
     * 
     * @return RO_Cli_Parser
     */
    public function add(RO_Cli_Option $v){
        list($s, $l) = $v->getOptionName();
        if(!empty($s)) {
            if(parent::offsetExists($s)) {
                throw new Exception('Option name duplicates');
            }
            parent::offsetSet($s, $v);
        }
        if(!empty($l)) {
            if(parent::offsetExists($l)) {
                throw new Exception('Option name duplicates');
            }
            parent::offsetSet($l, $v);
        }
        $this->_options[] = $v;
        return $this;
    }
    
    public function append($v){
        /* @var $v RO_Cli_Option */
        $this->add($v);
        return $this;
    }
    
    public function offsetSet($index, $newval){
        $this->add($newval);
        return $this;
    }
}