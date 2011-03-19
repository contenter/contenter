<?php

class GatewayCountriesTable extends FinalView_Doctrine_Table
{
         protected function innerJoinZcountrySelector($params)
         {
            $this->innerJoin('Country', $params);
         }
         protected function innerJoinZgatewaySelector($params)
         {
            $this->innerJoin('Gateway', $params);
         }
         protected function statusSelector($status)
         {
            $this->_getQuery()->addWhere('Country.status = ?', $status);
         }
         protected function ccountrySelector($id)
        {
           $this->_getQuery()->addWhere('Country.id = ?', $id);
        }

}