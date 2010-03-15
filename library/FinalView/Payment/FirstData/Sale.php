<?php

/**
* A Sale transaction is a credit card transaction that immediately charges a 
* customer's credit card. 
* 
*/
class FinalView_Payment_FirstData_Sale 
    extends FinalView_Payment_FirstData_Preauth
{
    
    /**
    * Return current transaction type
    * 
    * @return string
    */
    protected function _getTransactionType() 
    {
        return self::SALE;
    }
    
}