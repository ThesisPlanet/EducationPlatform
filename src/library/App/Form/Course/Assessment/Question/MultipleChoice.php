<?php
namespace App\Form\Course\Assessment\Question;

class MultipleChoice extends \Zend_Form
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
        $answer1IsCorrect = new \Zend_Form_Element_Checkbox('answer_1_correct');
        $answer1String = new \Zend_Form_Element_Text("answer_1");
        $answer1String->setRequired(true)->setLabel("Answer 1");
        $answer2IsCorrect = new \Zend_Form_Element_Checkbox('answer_2_correct');
        $answer2String = new \Zend_Form_Element_Text("answer_2");
        $answer2String->setRequired(true)->setLabel("Answer 2");
        $answer3IsCorrect = new \Zend_Form_Element_Checkbox('answer_3_correct');
        $answer3String = new \Zend_Form_Element_Text("answer_3");
        $answer3String->setRequired(true)->setLabel("Answer 3");
        $answer4IsCorrect = new \Zend_Form_Element_Checkbox('answer_4_correct');
        $answer4String = new \Zend_Form_Element_Text("answer_4");
        $answer4String->setRequired(true)->setLabel("Answer 4");
        
        $submit = new \Zend_Form_Element_Submit('submit');
        $submit->setLabel('Save Question')->setOptions(
                array(
                        'class' => 'btn btn-primary'
                ));
        // Attach elements to the form
        $this->addElement($question)
            ->addElement($answer1IsCorrect)
            ->addElement($answer1String)
            ->addElement($answer2IsCorrect)
            ->addElement($answer2String)
            ->addElement($answer3IsCorrect)
            ->addElement($answer3String)
            ->addElement($answer4IsCorrect)
            ->addElement($answer4String)
            ->addElement($submit);
    }
}