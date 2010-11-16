<?php
class Application_Rules_Confirmation extends FinalView_Access_Rules_Abstract
{
    public function hashExistRule()
    {
        $confirmation = Doctrine::getTable('Confirmation')->findOneByParams(array(
            'hash'  =>  $this->_params['hash']
        ));
        
        return (bool)$confirmation;
    }
}
