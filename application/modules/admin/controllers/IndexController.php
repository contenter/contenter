<?php

class Admin_IndexController extends FinalView_Controller_Action
{
    
    public function indexAction() 
    {
        $base_iterator = array(
            array('id' => 1, 'name' => 'name1'),
            array('id' => 2, 'name' => 'name2'),
            array('id' => 3, 'name' => 'name3'),
            array('id' => 4, 'name' => 'name4'),
            array('id' => 5, 'name' => 'name5'),
            array('id' => 6, 'name' => 'name6'),
            array('id' => 7, 'name' => 'name7'),
            array('id' => 8, 'name' => 'name8'),
            array('id' => 9, 'name' => 'name9'),
            array('id' => 10, 'name' => 'name10'),
            array('id' => 11, 'name' => 'name11'),
            array('id' => 12, 'name' => 'name12'),
        );
        
        $iterator = array_slice($base_iterator, ($this->getRequest()->getParam('page', 1) - 1) * 2 + 1, 2);
        
        $grid = new FinalView_Grid();
        
        $grid->setIterator($iterator);
        $grid->setColumnsFromIterator();
        
        $grid->addColumn(
            new FinalView_Grid_Column_Action('act', 'edit', 'www.yandex.ru', array('id'))
        );
        
        $grid->addPlugin(new FinalView_Grid_Plugin_Pager(count($base_iterator), 2, $this->getRequest()->getParam('page', 1)));
        $grid->addPlugin(new FinalView_Grid_Plugin_Gridactions(array(
            array('type'    =>  'link', 'href'  =>  'yandex.ru', 'id' => 'hello', 'label' => 'yandex'),
            array('type'    =>  'button', 'value' => 'BUUUT'),
            array('type'    =>  'submit', 'value' => 'SUUUUUB'),
        )));
        
        $this->view->grid = $grid;  
    }    
}
