<?php
namespace App\Form\Course\Assessment\Question;

class TrueFalse extends \Zend_Form
{

    public function init ()
    {
        $this->setMethod('post');
        $this->setOptions(
                array(
                        'class' => 'form-stacked'
                ));
        $question = new \Zend_Form_Element_Text('question');
        $question->setLabel('Your question...')
            ->setRequired(true)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities')
            ->setOptions(array(
                'class' => 'span8'
        ));
        $answer = new \Zend_Form_Element_Radio('answer');
        $answer->setLabel("Answer")
            ->addMultiOptions(
                array(
                        0 => "False",
                        1 => "True"
                ))
            ->setRequired(true);
        
        $submit = new \Zend_Form_Element_Submit('submit');
        $submit->setLabel('Save Question')->setOptions(
                array(
                        'class' => 'btn btn-primary'
                ));
        // Attach elements to the form
        $this->addElement($question)
            ->addElement($answer)
            ->addElement($submit);
    }
}