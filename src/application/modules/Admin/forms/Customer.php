<?php
class Admin_Form_Customer extends Zend_Form
{

    public function init ()
    {
    	
        /* Form Elements & Other Definitions Here ... */
        //initialize form
        $this->setAction('/Admin/customer/edit')->setMethod('post');
        //create text input for e-mail address
        $name = new Zend_Form_Element_Text('name');
        $name->setLabel('Customer Name')
            ->setOptions(array('size' => '50'))
            ->setRequired(true)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities');
        $primaryContact = new Zend_Form_Element_Text('primaryContact');
        $primaryContact->setLabel('Primary Single point of contact')
            ->setOptions(array('size' => '50'))
            ->setRequired(true)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities');
        $phone = new Zend_Form_Element_Text('phone');
        $phone->setLabel('Phone Number')
            ->setOptions(array('size' => '50'))
            ->setRequired(true)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities');
        $address = new Zend_Form_Element_Text('address');
        $address->setLabel('Address')
            ->setOptions(array('size' => '50'))
            ->setRequired(true)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities');
        $city = new Zend_Form_Element_Text('city');
        $city->setLabel('City')
            ->setOptions(array('size' => '50'))
            ->setRequired(true)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities');
        $state = new Zend_Form_Element_Select('state');
        $state->setLabel('State')
            ->addMultiOptions(App_Misc_Location::getStates())
            ->setRequired(true)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities');
        $zipCode = new Zend_Form_Element_Text('zipCode');
        $zipCode->setLabel('Zip Code')
            ->setOptions(array('size' => '50'))
            ->setRequired(true)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities');
        $taxId = new Zend_Form_Element_Text('taxId');
        $taxId->setLabel('Tax Id')
            ->setOptions(array('size' => '50'))
            ->setRequired(true)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities');
        $id = new Zend_Form_Element_Hidden('id');
        $id
        ->addFilter('StringTrim')
        ->addFilter('HtmlEntities');
        
            
            $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Register')->setOptions(array('class' => 'submit'));
        //Attach elements to the form
        $this
        	->addElement($id)
        	->addElement($name)
            ->addElement($primaryContact)
            ->addElement($phone)
            ->addElement($address)
            ->addElement($city)
            ->addElement($state)
            ->addElement($zipCode)
            ->addElement($taxId)
            ->addElement($submit);
    }
}

