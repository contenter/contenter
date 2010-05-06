<?php
class Application_Rules_Confirmation
{
    public function hashExistRule($params)
    {
        $confirmation = Doctrine::getTable('Confirmation')->findOneByParams(array(
            'hash'  =>  $params['hash']
        ));
        
        return (bool)$confirmation;
    }
}
