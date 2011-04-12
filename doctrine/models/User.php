<?php

/**
 * User
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 5845 2009-06-09 07:36:57Z jwage $
 */
class User extends BaseUser
{

    public function setUp()
    {
        parent::setUp();
        $this->hasMutator('password', 'passwordEncrypt');

    }
    
    /**
    * Encrypt password
    * 
    * @param string $value
    */
    public function passwordEncrypt($value)
    {
        $this->_set('password', FinalView_Auth_Encrypt::encrypt($value));
    }
    
    public function isRole($role)
    {
        return (($this->role & $role) === $role);
    }

    public function getPages(array $selectors, $page = null, $per_page = null)
    {
        $selectors['user_id'] = $this->id;
        if (!is_null($page)) {
            $per_page = is_null($per_page) ? Config::get('user', 'pages_per_page') : $per_page;
            return Doctrine::getTable('Page')->findPageByParams($selectors, $page, $per_page);
        }
        return Doctrine::getTable('Page')->findByParams($selectors);
    }
}
