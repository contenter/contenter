<?php
class FinalView_Application_Resources
{
    const ACCESS_MODE_EXPLICIT = 'ACCESS_MODE_EXPLICIT';
    const ACCESS_MODE_SOFT = 'ACCESS_MODE_SOFT';

    private static $_yml_resources;
    private static $_resources = array(
        '_ROOT_'    =>  array(
            'rule'  =>  '_FALSE_'
        )
    );

    protected $_resource;
    protected $_path;
    private $_access_rule;
    
    private static $_access_mode = self::ACCESS_MODE_SOFT;

    public static function setAccessMode($accessMode)
    {
        if (!in_array($accessMode, array(self::ACCESS_MODE_EXPLICIT, self::ACCESS_MODE_SOFT))) {
            throw new FinalView_Application_Exception('access mode ' . $accessMode . 'doesn\'t allowed. Only ACCESS_MODE_EXPLICIT and ACCESS_MODE_SOFT can be used');
        }
        self::$_access_mode = $accessMode;
    }

    public static function setResources(array $resources)
    {
        self::$_yml_resources = $resources;

        foreach (self::$_yml_resources as $resName => $resData) {
            self::_addResource($resName, $resData);
        }
    }
    
    private static function _addResource($key, $data, $context = '_ROOT_')
    {
        if (!isset(self::$_resources[$context])) {
            throw new FinalView_Application_Exception('context ' . $context . ' is not defined');
        }

        if (strpos($key, '.')) {
            $keys = explode('.', $key);
            
            $lastKey = array_pop($keys);
            $evContext = $context;
            while ($pKey = array_shift($keys)) {
                if (!isset(self::$_resources[$evContext . '.' . $pKey])) {
                    self::_addResource($pKey, array(), $evContext);
                }
                $evContext .= '.' . $pKey;
            }
        }else{
            $lastKey = $key;
            $evContext = $context;
        }
        
        switch (true) {
            case !empty($data['rule']):
            break;
            case !empty($data['orRule']):
                $data['rule'] = '(' . self::$_resources[$evContext]['rule'] . ') OR (' . $data['orRule'] . ')';
                unset($data['orRule']);
            break;
            case !empty($data['andRule']):
                $data['rule'] = '(' . self::$_resources[$evContext]['rule'] . ') AND (' . $data['andRule'] . ')';
                unset($data['andRule']);
            break;
            default:
                $data['rule'] = self::$_resources[$evContext]['rule'];
            break;
        }
        

        if (isset(self::$_resources[$evContext . '.' . $lastKey])) {
            throw new FinalView_Application_Exception('resource with key ' . $key . ' already defined in context ' . $context);
        }

        $children = array();
        if (array_key_exists('children', $data)) {
            $children = $data['children'];
            unset($data['children']);
        }

        self::$_resources[$evContext . '.' . $lastKey] = $data;

        foreach ($children as $resName => $resData) {
            if (is_int($resName) && is_string($resData)) {
                self::_addResource($resData, array(), $evContext . '.' . $lastKey);
            }elseif(is_array($resData)){
                self::_addResource($resName, $resData, $evContext . '.' . $lastKey);
            }else{
                throw new FinalView_Application_Exception('incorrect children of resource ' . $key);
            }
        }
    }

    public static function hasResource($resource)
    {
        if (empty($resource)) { return false;}
        $resource = '_ROOT_.' . strtolower($resource);

        if (isset(self::$_resources[$resource])) {
            return true;
        }elseif(self::$_access_mode === self::ACCESS_MODE_EXPLICIT) {
            return false;
        }
        
        $resourceParent = substr($resource, 0, strrpos($resource, '.'));
        while (!isset(self::$_resources[$resourceParent])) {
            $resourceParent = substr($resourceParent, 0, strrpos($resourceParent, '.'));
        }
        
        if ($resourceParent === '_ROOT_') {
            return false;
        }
        
        return true;
    }

    public static function get($resource)
    {
        if (empty($resource)) { return false;}
        $resource = '_ROOT_.' . strtolower($resource);
        
        if (isset(self::$_resources[$resource])) {
            return new self(self::$_resources[$resource], $resource);
        }elseif(self::$_access_mode === self::ACCESS_MODE_EXPLICIT) {
            return null;
        }
        
        $resourceParent = substr($resource, 0, strrpos($resource, '.'));
        while (!isset(self::$_resources[$resourceParent])) {
            $resourceParent = substr($resourceParent, 0, strrpos($resourceParent, '.'));
        }

        if ($resourceParent === '_ROOT_') {
            return null;
        }

        return new self(self::$_resources[$resourceParent], $resource);
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
