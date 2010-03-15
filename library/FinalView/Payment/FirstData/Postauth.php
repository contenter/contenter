<?php

/**
* A Ticket Only transaction is a post-authorization transaction that captures 
* the funds from an Authorize Only transaction, reserving funds on the 
* customer’s card for the amount specified. Funds are transferred when your 
* batch of transactions is settled. 
* 
* If you enter a larger total in the Ticket Only transaction than was specified 
* for the Authorize Only transaction, the Ticket Only transaction may be declined. 
* If you enter a smaller amount than was authorized, an adjustment is made to 
* the authorization to reserve only the smaller amount of funds on the customer’s 
* card for the transaction. Ticket Only transactions must be completed within 
* 30 days of the Authorization being obtained.
* 
*/
class FinalView_Payment_FirstData_Postauth 
    extends FinalView_Payment_FirstData_Post
{
    
    /**
    * Return current transaction type
    * 
    * @return string
    */
    protected function _getTransactionType() 
    {
        return self::POSTAUTH;
    }
    
}