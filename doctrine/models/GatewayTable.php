<?php

class GatewayTable extends FinalView_Doctrine_Table
{
          protected function innerJoinCountrySelector($params)
         {
            $this->innerJoin('Countries', $params);
         }

          protected function statusSelector($status)
          {
            $this->_getQuery()->addWhere($this->getTableName().'.status = ?', $status);
          }

}