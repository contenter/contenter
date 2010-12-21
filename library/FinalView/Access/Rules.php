<?php
class FinalView_Access_Rules
{

    const RULES_CLASSES_PREFIX = 'Application_Rules';

    private static $_schema;
    private static $_rules;
    public static $options = array(
        'default_behavior'  =>  false
    );

    private static $_rulesClasses = array();
    private static $_params = array();

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
                
                if (array_key_exists('expression', $data)) {
                    self::$_rules[$rule]['expression'] = $data['expression'];
                }
            }
        }
    }
    
    public static function addRuleToSchema($name, $expression)
    {
        if (strpos($expression, ' AND ') !== false && strpos($expression, ' OR ') !== false) {
            throw new FinalView_Access_Exception("Please use only OR and AND for one part of rule. Use brackets (). Expression: " . $expression);
        }

        if (!preg_match('/^[!a-zA-Z0-9][!a-zA-Z0-9\-\_\s]+$/', $expression)) {
            throw new FinalView_Access_Exception("Please use only a-zA-Z - _ in rules names. Expression: " . $expression);
        }
        

        if (strpos($expression, ' AND ') !== false) {
            $arr = explode(' AND ', $expression);
            $type = 'AND';
        }elseif (strpos($expression, ' OR ') !== false) {
            $arr = explode(' OR ', $expression);
            $type = 'OR';
        }elseif(strpos($expression, ' ') === false){
            $type = 'AND';
            $arr = array($expression);
        }else{
            throw new FinalView_Access_Exception("Not correct rule: " . $expression);
        }
        self::$_rules[$name] = array(
            'group'         =>  '_auto_built_rules_',
            'type'          =>  $type,
            'dependences'   =>  $arr
        );
    }
    
    private static function cleanRuleExpression($expression)
    {
        $cleanRule = str_ireplace(' and ', ' AND ', $expression);
        $cleanRule = str_ireplace(' or ', ' OR ', $cleanRule);
        $cleanRule = preg_replace('/\s+/', ' ', $cleanRule);

        return $cleanRule;
    }
    
    public static function replaceRule($matches)
    {
        $match = $matches[0];
        $cleanRule = substr($match, 1, strlen($match) - 2);
        $cleanRule = self::cleanRuleExpression($cleanRule);

        $ruleName = md5($cleanRule);
        
        if (!isset(self::$_rules[$ruleName]) ) {
            self::addRuleToSchema($ruleName, $cleanRule);
        }
        
        return $ruleName;
    }

    private static function buildRule($rule)
    {
        $proccedRule = $rule;
        while (preg_match_all('/\({1}[^\(\)]*\){1}/', $proccedRule, $matches)) {
            $proccedRule = preg_replace_callback('/\({1}[^\(\)]*\){1}/', array('FinalView_Access_Rules', 'replaceRule') , $proccedRule);
        }
        
        $cleanRule = self::cleanRuleExpression($proccedRule);
        $ruleName = md5($cleanRule);
        
        if (!isset(self::$_rules[$ruleName]) ) {
            self::addRuleToSchema($ruleName, $cleanRule);
        }

        return $ruleName;
    }

    public static function getRule($rule)
    {
        if ($rule == '_TRUE_' || $rule == '_FALSE_') {
            return new self($rule);
        }
        
        if (preg_match('/\s/', trim($rule))) {
            return self::getRule(self::buildRule(trim($rule)));
        }elseif (substr($rule, 0, 1) == '!') {
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
        self::$_rulesClasses = array();
        self::$_params = $params;

        return $this->checkInContext();
    }

    public function checkInContext()
    {
        $this->_failedRules = array();

        switch (true) {
            case $this->_rule == '_TRUE_':
                return true;
            break;
            
            case $this->_rule == '_FALSE_':
                return false;
            break;
            
            case !is_null($this->_getRule('expression')):
                $checkingRule = self::getRule($this->_getRule('expression'));
                $result = $checkingRule->checkInContext();

                if (false === $result) {
                    $this->_failedRules = $this->_failedRules + $checkingRule->getFailedRules();
                }
            break;
            
            case $this->_getRule('type') == 'FUNC':
                $result = $this->_check();
            break;
            
            case in_array($this->_getRule('type'), array('OR', 'AND')):
                $result = $this->_checkDependencies();
            break;
            
            default:
                throw new FinalView_Access_Exception('rule' . $this->_rule . 'is incorrect');
            break;
        }
        
        if (false === $result) {
            $this->_failedRules[$this->_rule] = $this;
        }

        return $result;
    }
    
    protected function _checkDependencies()
    {
        $dependences = $this->_getRule('dependences');
        if (!is_array($dependences) || empty($dependences) ) {
            throw new FinalView_Access_Exception("For dependent rule: " . $this->_rule . ' dependences is incorrect (must be non empty array)');
        }
        
        if (!in_array($this->_getRule('type'), array('OR', 'AND'))) {   // do not add any other types. At first look at return value at the end of function
            throw new FinalView_Access_Exception("For dependent rule: " . $this->_rule . ' type is incorrect (must be OR or AND)');
        }

        foreach ($dependences as $checking_rule) {
            $checkingRule = self::getRule($checking_rule);
            $result = $checkingRule->checkInContext();
            $rule_type = $this->_getRule('type');
            if ($this->isInverted()) {
                $result = !$result;
                switch ($this->_getRule('type')) {
                   case 'OR':
                       $rule_type = 'AND';
                   break;
                   case 'AND':
                       $rule_type = 'OR';
                   break;
                }
            }
            switch ($rule_type) {
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
        
        return $this->_getRule('type') == 'AND';  //here we know that only AND expressions can be true. OR expressions are false.
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

    protected function _check()
    {
        $className = $this->_getClassName();

        if (!isset(self::$_rulesClasses[$className])) {
            self::$_rulesClasses[$className] = new $className(self::$_params);
        }

        $rules = self::$_rulesClasses[$className];

        $method = $this->_getMethodName();

        if (!method_exists($rules, $method)) {
            throw new FinalView_Access_Exception('Rule Method ' . $method . ' can not be found in class ' . $className);
        }
        
        return !$this->isInverted()
            ? $rules->$method()
            : !$rules->$method();
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
