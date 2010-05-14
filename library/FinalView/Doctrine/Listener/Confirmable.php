<?php
class FinalView_Doctrine_Listener_Confirmable extends Doctrine_Record_Listener
{        
    public function preDelete(Doctrine_Event $event)
    {
        $invoker = $event->getInvoker();
        
    	Doctrine::getTable('Confirmation')->findByParams(array(
            'entity' =>  array(
                'model'     =>  $invoker->getTable()->getComponentName(),
                'id'        =>  $invoker->getIncremented()
            )
        ))->delete();
    }
}
