<?php

/**
* A void transaction cancels the transaction. Only transactions in the current 
* batch (that have not been sent for settlement) can be voided. 
* 
*/
class FinalView_Payment_FirstData_Void 
    extends FinalView_Payment_FirstData_Post
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