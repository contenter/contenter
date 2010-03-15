<?php

class FinalView_Payment_AuthorizeNet_Void extends 
    FinalView_Payment_AuthorizeNet_Capture
{
    
    /**
    * Return current transaction type
    * 
    * @return string
    */
    protected function _getTransactionType() 
    {
        return self::VOID;
    }
    
}