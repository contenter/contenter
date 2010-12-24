<?php
class FinalView_Application_Resources
{
    private static $_yml_resources;
    private static $_resources;

    protected $_resource;
    protected $_path;
    private $_access_rule;

    public static function setResources(array $resources)
    {
        self::$_yml_resources = $resources;

        foreach (self::$_yml_resources as $resName => $resData) {
            self::_addResource($resName, $resData);
        }
        
//         dump(self::$_resources);
//         exit;
    }

    private static function _addResource($key, $data, $context = null)
    {
        if (!array_key_exists('rule', $data)) {
             throw new FinalView_Application_Exception('incorrect resource with key ' . $key);
        }

        $contextResources = &self::$_resources;
        if (!is_null($context)) {
            $resourceParts = explode('.', $context);
            while ($part = array_shift($resourceParts)) {
                if (!isset($contextResources[$part])) {
                    $contextResources[$part] = array();
                }
                $contextResources = &$contextResources[$part];
            }
        }

        if (isset($contextResources[$key]['rule'])) {
            throw new FinalView_Application_Exception('resource with key ' . $key . ' already defined in context ' . $context);
        }

        $children = array();
        if (array_key_exists('children', $data)) {
            $children = $data['children'];
            unset($data['children']);
        }

        $contexts = array();
        if (array_key_exists('contexts', $data)) {
            $contexts = $data['contexts'];
            unset($data['contexts']);
        }

        if (isset($contextResources[$key]) ) {
            $contextResources[$key] = array_merge($contextResources[$key], $data);
        } else {
            $contextResources[$key] = $data;
        }

        foreach ($children as $resName => $resData) {
            if (!is_array($resData) || !array_key_exists('rule', $resData)) {
                throw new FinalView_Application_Exception('resource with key ' . $resName . ' must have rule');
            }

            $resData['rule'] = '(' . $data['rule'] . ') AND (' . $resData['rule'] . ')';
            self::_addResource($resName, $resData, !is_null($context) ? $context . '.' . $key : $key);
        }
        
        foreach ($contexts as $contextName => $resData) {
            if (!is_array($resData) || !array_key_exists('rule', $resData)) {
                throw new FinalView_Application_Exception('resource with key ' . $contextName . ' must have rule');
            }

            self::_addResource($key, $resData, $contextName);
        }
    }
    
    public static function hasResource($resource)
    {
        if (empty($resource)) { return false;}
        $resource = strtolower($resource);
        
        $resourceParts = explode('.', $resource);
        $currentResources = &self::$_resources;
        while ($part = array_shift($resourceParts)) {
            if (!isset($currentResources[$part])) {
                return false;
            }
            $currentResources = &$currentResources[$part];
        }
        
        return true;
    }

    public static function get($resource)
    {
        if (empty($resource)) { return false;}
        $resource = strtolower($resource);
        
        $resourceParts = explode('.', $resource);
        
        $currentResources = &self::$_resources;

        while ($part = array_shift($resourceParts)) {
            if (!isset($currentResources[$part])) {
                throw new FinalView_Application_Exception('resource ' . $resource . ' not found');
            }
            $currentResources = &$currentResources[$part];
        }
        
        return new self($currentResources, $resource);
    }

    private function __construct($resource, $path)
    {
        $this->_resource = $resource;
        $this->_path = $path;
    }

    public function getAccessRule($context = null)
    {
        if ($this->_access_rule === null) {
            $this->_access_rule = FinalView_Access_Rules::getRule($this->getResource('rule'));

            if (is_null($this->_access_rule)) {
                throw new FinalView_Application_Exception('can not be defined rule for resource: ' . $this->_resource);
            }
        }

        return $this->_access_rule;
    }

    public function getResource($key = null)
    {
        return (is_null($key))
            ? $this->_resource
            : @$this->_resource[$key];
    }

    public function getName()
    {
        if (is_null($this->_name)) {
            $this->_name = array_pop(explode('.', $this->_path) );
        }
        return $this->_name;
    }
    
    public function getPath()
    {
        return $this->_path;
    }
}
