<?php
namespace App\Form\Course\Assessment\Question;

class Delete extends \Zend_Form
{

    public function init ()
    {
        $this->setMethod('post');
        $this->setOptions(
                array(
                        'class' => 'form-stacked'
                ));
        
        $submit = new \Zend_Form_Element_Submit('submit');
        $submit->setLabel('Yes, I want to delete this question.')->setOptions(
                array(
                        'class' => 'btn btn-large btn-danger'
                ));
        // Attach elements to the form
        $this->addElement($submit);
    }
}