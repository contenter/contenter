<?php
class FinalView_Access_Rules
{
   
    const RULES_CLASSES_PREFIX = 'Application_Rules';

    private static $_schema;
    private static $_rules;
    public static $options = array(
        'default_behavior'  =>  false
    );
    
    protected $_rule;
    protected $_failedRules = array(); 
    
    public static function setSchema(array $schema)
    {
        self::$_schema = $schema;
        
        foreach ($schema as $group => $rules) {
        	foreach ($rules as $rule => $data) {
                self::$_rules[$rule] = array(
                    'group'     =>  $group,
                    'type'      =>  $data['type']
                );
                if (array_key_exists('dependences', $data)) {
                    self::$_rules[$rule]['dependences'] = $data['dependences']; 	
                }
            }   
        }
    }
    
    public static function getRule($rule)
    {
        if ($rule == '_TRUE_' || $rule == '_FALSE_') {
        	return new self($rule);
        }
        if (substr($rule, 0, 1) == '!') {
        	$_rule = substr($rule, 1);
        	if (!array_key_exists($_rule, self::$_rules)) {
                throw new FinalView_Access_Exception('can not find rule' . $_rule . ' in schema ');
            }
            self::$_rules[$rule] = self::$_rules[$_rule];            
        }
        if (!array_key_exists($rule, self::$_rules)) {
        	throw new FinalView_Access_Exception('can not find rule' . $rule . ' in schema ');
        } 
        return new self($rule);
    }
    
    public function isInverted()
    {
        return substr($this->_rule, 0, 1) == '!';
    }
    
    private function __construct($rule)
    {
        $this->_rule = $rule;
    }
    
    public function check(array $params = array())
    {
        $this->_failedRules = array();
        
        switch ($this->_rule) {
            case '_TRUE_':
                return true;
            break;
            case '_FALSE_':
                return false;
            break;
        }
        
        if ($this->_getRule('type') == 'FUNC') {
            $result = $this->_check($params);
            if (false === $result) {
                $this->_failedRules[$this->_rule] = $this;
            }
            
            return $result;        	
        }
        $dependences = $this->_getRule('dependences');
       
        foreach ((array)$dependences as $checking_rule) {
            $checkingRule = self::getRule($checking_rule);
            $result = $checkingRule->check($params);
            switch ($this->_getRule('type')) {
                case 'OR':
                    if (true === $result) {
                    	return true;
                    }else{
                        $this->_failedRules[$checkingRule->getName()] = $checkingRule;
                        $this->_failedRules = $this->_failedRules + $checkingRule->getFailedRules(); 
                    }
                break;
                case 'AND':
                	if (false === $result) {
                        $this->_failedRules[$checkingRule->getName()] = $checkingRule;
                        $this->_failedRules = $this->_failedRules + $checkingRule->getFailedRules(); 
                        return false;                
                    }                
                break;
            }
        }

        $result = $this->_check($params);
        
        if ($result === null) {
            switch ($this->_getRule('type')) {
            	case 'OR':
            	   return false;
            	break;
            	case 'AND':
            	   return true;
            	break;
            	default:
            	   throw new FinalView_Access_Exception('type of rule '.$this->_rule.' is incorrect');
            	break;
            }
        }
        
        if (false === $result) {
            $this->_failedRules[$this->_rule] = $this;
        }        
        
        return $result;
    }
    
    public function isFailedRule($rule = null)
    {
        if (empty($this->_failedRules)) {
        	return false;
        }
        
        if (is_null($rule)) $rule = $this->_rule;
        
        $failedRules = $this->_failedRules;
        return array_key_exists($rule, $failedRules);
    }
    
    public function getName()
    {
        return $this->_rule;
    }
    
    public function getFailedRules()
    {
        return $this->_failedRules;
    }
    
    protected function _check(array $params = array())
    {
        $className = $this->_getClassName();
        
        $rules = new $className();        
        
        $method = $this->_getMethodName();
        
        if (method_exists($rules, $method)) {
            return !$this->isInverted() 
                ? $rules->$method($params)
                : !$rules->$method($params);
        }
        
        return null;
    }
    
    protected function _getRule($key = null)
    {
        return (is_null($key)) ? self::$_rules[$this->_rule] : @self::$_rules[$this->_rule][$key]; 
    }
    
    protected function _getClassName()
    {
        return self::RULES_CLASSES_PREFIX . '_' . ucfirst(self::$_rules[$this->_rule]['group']);
    }
    
    protected function _getMethodName()
    {
        $filter = new Zend_Filter_Word_UnderscoreToCamelCase();
        
        $base_rule = $this->_rule;
        if ($this->isInverted()) {
        	$base_rule = substr($this->_rule, 1); 
        }
        
        return lcfirst($filter->filter($base_rule)) . 'Rule';    
    }
}
