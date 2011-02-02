<?php
class FinalView_Acl extends Zend_Acl
{
    protected static $_instance = null;

    protected $_assertion = null;

    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    private function __construct()
    {
        $this->allow(null, null, null, $this->getAssertion());
    }

    public function addResource($resource, $params = array())
    {
        if (is_string($resource)) {
            $resource = new FinalView_Acl_Resource($resource, $params);
        }

        return parent::addResource($resource);
    }

    public function getResource($resource, array $params = array())
    {
        return $this->get($resource.'_'.base64_encode(serialize($params)));
    }

    public function getAssertion()
    {
        if (is_null($this->_assertion) ) {
            $this->_assertion = new FinalView_Acl_Assert();
        }

        return $this->_assertion;
    }

    public function isAllowed($role = null, $resource = null, $privilege = null)
    {
        if (!$resource instanceof FinalView_Acl_Resource) {
            throw new FinalView_Acl_Exception('resource must be an instance of FinalView_Acl_Resource');
        }

        if (!$this->has($resource) ) {
            $this->addResource($resource);
        }

        return parent::isAllowed($role, $resource, $privilege);
    }
}
