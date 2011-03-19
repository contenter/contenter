<?php

class User_Form_Settings extends Zend_Form
{

    /**
     * Initialize form (used by extending classes)
     *
     * @return void
     */
     public $gateway;
     public function setGateway($gateway)
    {
        $this->gateway = $gateway;
    }

       public function init()
    {

        $countries_list = Doctrine::getTable('GeoCountry')->getCountriesAsOptionsId();
         
        $country_id = 179; //------------------HARDCODED
        $q = Doctrine_Query::create()
                -> select ('*,g.gateway_title,g.gateway_ident')
                -> from('GatewayCountries x')
                ->innerJoin('x.Gateway g')
                ->innerJoin('x.Country c')
                ->andWhere('g.status = ?', 1)
                ->andWhere('c.id = ?', $country_id);
         $gateways = $q->execute();
        //echo $q->getSqlQuery();
         //print_r($country_id);
         //exit();
         foreach ($gateways as $key=>$value) {
          //   dump($value->toArray());
              $gateway_list[$value->Gateway->id] =$value->Gateway->gateway_ident;
         }


        //dump($gateway_list);
        //exit();
        $element = new Zend_Form_Element_Select('country_id');
        $element
            ->setLabel('Country')
            ->setRequired()
            ->addMultiOptions(array($countries_list))
            ->addFilters(array('StringTrim'))
            ;
        $this->addElement($element);
        
        $element = new Zend_Form_Element_Select('gateway_id');
        $element
            ->setLabel('Payment Gateway')
            ->setRequired()
            ->addMultiOptions(array($gateway_list))
            ->addFilters(array('StringTrim'))
            ;
        $this->addElement($element);

         if (!empty($this->gateway)) {
            $ClassToUse = "User_Form_".$this->gateway;
            $subform = new $ClassToUse;
        } else {
            $ClassToUse = "User_Form_".$gateway_list[1];
            $subform = new $ClassToUse;
            $subform->clearDecorators();
            $subform->addDecorator('DtDdWrapper');
        }
        $this->addSubForm($subform, 'gateway',2);


        $element = new Zend_Form_Element_Radio('banner_count');
        $element->setLabel('Banners count');
        $element->setRequired();
        $element->setMultiOptions(array('0'=>'Few','2'=>'Medium','3'=>'Large')  );
        $element->setOptions(array('separator'=>'')); // Makes the radio buttons sit next to each other
        $this->addElement($element);

        $element = new Zend_Form_Element_Radio('banner_types');
        $element->setLabel('Banners Type');
        $element->setRequired();
        $element->setMultiOptions(array('0'=>'Adult alowed','1'=>'Adult NOT alowed')  );
        $element->setOptions(array('separator'=>'')); // Makes the radio buttons sit next to each other
        $this->addElement($element);

        $element = new Zend_Form_Element_Submit('submit');
        $element
            ->setLabel('Edit user')
            ->setIgnore(true)
            ;
        $this->addElement($element);

    }

}
