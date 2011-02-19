<?php
class FinalView_Acl_Assert implements Zend_Acl_Assert_Interface
{

    protected $_params;

    public function assert(Zend_Acl $acl, Zend_Acl_Role_Interface $role = null, Zend_Acl_Resource_Interface $resource = null,
                           $privilege = null)
    {
        $res = FinalView_Application_Resources::get(
            $resource->getResourceName()
        );

        if (is_null($res)) {
            return FinalView_Access_Rules::$options['default_behavior'];
        }

        return $res->getAccessRule()->check($resource->getParams());
    }
}
