<?php
namespace App\Form\Course\Assessment\Question;

class FillInTheBlank extends \Zend_Form
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
            ->setDescription(
                "Example: \" The quick brown __fox__ jumped over the __lazy__ dog\"")
            ->setRequired(true)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities')
            ->setOptions(array(
                'class' => 'span8'
        ));
        
        $submit = new \Zend_Form_Element_Submit('submit');
        $submit->setLabel('Save Question')->setOptions(
                array(
                        'class' => 'btn btn-primary'
                ));
        // Attach elements to the form
        $this->addElement($question)->addElement($submit);
    }
}