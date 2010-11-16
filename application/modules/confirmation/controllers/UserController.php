<?php
class Confirmation_UserController extends FinalView_Controller_Action
{
    public function registrationAction()
    {
        $confirmation = $this->_helper->confirmation();

        switch ($this->getRequest()->getParam('reply')) {
            case Confirmation::ACTION_ACCEPT:
                $confirmation->Entity->confirmed = 1;
                $confirmation->Entity->replied_at = new Doctrine_Expression('NOW()');

                $confirmation->Entity->save();
            break;
            case Confirmation::ACTION_DECLINE:

            break;
        }

        $confirmation->delete();

        $this->view->reply_type = $this->getRequest()->getParam('reply');
        $this->view->entity = $confirmation->Entity;
    }
}
