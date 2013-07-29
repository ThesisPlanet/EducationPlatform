<?php
namespace App\Form\Course;

class InviteUser extends \Zend_Form
{

    public function init ()
    {
        $this->setMethod('post');
        // create text input for e-mail address
        
        $email = new \Zend_Form_Element_Text('email');
        $email->setLabel('Your Email:')
            ->setOptions(array(
                'size' => '50'
        ))
            ->setRequired(true)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities')
            ->addValidator('emailAddress');
        
        $id = new \Zend_Form_Element_Hidden('id');
        $id->setOptions(array(
                'size' => '50'
        ))
            ->setRequired(false)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities');
        $submit = new \Zend_Form_Element_Submit('submit');
        $submit->setLabel('Send an invitation email')->setOptions(
                array(
                        'class' => 'btn btn-primary'
                ));
        // Attach elements to the form
        $this->addElement($id)
            ->addElement($email)
            ->addElement($submit);
    }
}