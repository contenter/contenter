<?php
class FinalView_Application_Resources
{
    private static $_yml_resources;
    private static $_resources;

    protected $_resource;
    protected $_section;
    private $_access_rule;
    private $_context_rules;

    public static function setResources(array $resources)
    {
        self::$_yml_resources = $resources;

        self::$_resources[0] = array();
        foreach (self::$_yml_resources as $resKey => $resEntity) {
            if (is_array($resEntity)) {
                foreach ($resEntity as $resName=>$resData) {
                    self::$_resources[$resKey][$resName] = $resData;
                }
            }else{
                self::$_resources[0][$resKey]['rule'] = $resEntity;
            }
        }
    }

    public static function get($resource, $section = null)
    {
        $resource = strtolower($resource);

        if ($section === null) {
            if (!array_key_exists($resource, self::$_resources[0])) {
                foreach (self::$_resources as $sectionName => $resources) {
                    if($sectionName === 0) continue;
                    if (array_key_exists($resource, self::$_resources[$sectionName])) { $section = $sectionName; break;}
                }
            }else{
                $section = 0;
            }
        }
        if (!array_key_exists($resource, self::$_resources[$section])) {
            return null;
        }
        return new self($resource, $section);
    }

    private function __construct($resource, $section)
    {
        $this->_resource = $resource;
        $this->_section = $section;
    }

    public function getAccessRule($context = null)
    {
        if (!is_null($context)) {
            if (!isset($this->_context_rules[$context]) ) {
                $contexts = $this->getResource('contexts');
                if (!isset($contexts[$context])) {
                    throw new FinalView_Application_Exception('context ' . $context . ' is not defined for resource: ' . $this->_resource);
                }
                
                $this->_context_rules[$context] = FinalView_Access_Rules::getRule($contexts[$context]);
                
                if (is_null($this->_context_rules[$context])) {
                    throw new FinalView_Application_Exception('can not be defined rule for resource: ' . $this->_resource);
                }
                
            }
            
            $rule = $this->_context_rules[$context];
        }else{
            if ($this->_access_rule === null) {
            	$this->_access_rule = FinalView_Access_Rules::getRule($this->getResource('rule'));

                if (is_null($this->_access_rule)) {
                    throw new FinalView_Application_Exception('can not be defined rule for resource: ' . $this->_resource);
                }
            }
            
            $rule = $this->_access_rule;
        }

        return $rule;
    }

    public function getResource($key = null)
    {
        return (is_null($key))
            ? self::$_resources[$this->_section][$this->_resource]
            : @self::$_resources[$this->_section][$this->_resource][$key];
    }

    public function getName()
    {
        return $this->_resource;
    }
}
