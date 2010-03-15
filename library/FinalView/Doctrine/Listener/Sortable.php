<?php
class FinalView_Doctrine_Listener_Sortable extends Doctrine_Record_Listener
{
    public function postInsert(Doctrine_Event $event)
    {
        $event->getInvoker()->position = $event->getInvoker()->id;
        $event->getInvoker()->save();
    }
}