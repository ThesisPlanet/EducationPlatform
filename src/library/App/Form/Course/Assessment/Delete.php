<?php
namespace App\Form\Course\Assessment;

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
        $submit->setLabel('Yes, I want to delete this assessment.')
            ->setOptions(
                array(
                        'class' => 'btn btn-large btn-danger'
                ))
            ->setDescription(
                "Deleting this assessment will remove the questions, attempts, scores, and everything else related to this assessment.");
        // Attach elements to the form
        $this->addElement($submit);
    }
}