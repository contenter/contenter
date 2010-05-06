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
    
    public function postInsert(Doctrine_Event $event)
    {
        $invoker = $event->getInvoker();

        Doctrine::getTable('Confirmation')
            ->createHash($invoker->getTable()->getComponentName(), $invoker->getIncremented())
            ->save()
        ;
    }
    
    public function preHydrate(Doctrine_Event $event)
    {        
        $invoker = $event->getInvoker();        
        $data = $event->data;

        $data['Confirmation'] = Doctrine::getTable('Confirmation')->findOneByParams(array(
            'entity' =>  array(
                'model' =>  $invoker->getComponentName(),
                'id'    =>  $data[$invoker->getIdentifier()]
            )
        ));

        $event->data = $data;
    }
}
