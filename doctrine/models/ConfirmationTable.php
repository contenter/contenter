<?php
class ConfirmationTable extends FinalView_Doctrine_Table
{   
    protected function entitySelector($data)
    {
        $this->_getQuery()->addWhere($this->getTableName().'.entity_model = ?', $data['model']);
        $this->_getQuery()->addWhere($this->getTableName().'.entity_id = ?', $data['id']);    
    }
    
    protected function hashSelector($hash)
    {
        $this->_getQuery()->addWhere($this->getTableName().'.hash = ?', $hash);    
    }    
        
    public function createHash($entity_model, $entity_id)
    {
        $data = array(
            'entity_model'      =>  $entity_model,
            'entity_id'         =>  $entity_id,
            'hash'              =>  $this->_generateUniqueHash()
        );
        
        return $this->create($data);
    }
    
    protected function _generateUniqueHash()
    {
        $hash_length = $this->getFieldLength('hash');
        
        do {
            $hash = substr(md5(uniqid(rand())), 0, $hash_length);
            $query = Doctrine_Query::create()
                ->select('COUNT(*) AS num')
                ->from('Confirmation')
                ->where('hash = ?', $hash)
                ;
            if (0 == $query->fetchOne()->num) {
                return $hash;
            }
        } while (true);        
    }
}
