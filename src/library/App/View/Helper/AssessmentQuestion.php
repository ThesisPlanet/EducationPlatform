<?php
namespace App\View\Helper;

class AssessmentQuestion extends \Zend_View_Helper_Abstract
{

    public $questionObjAsArray = null;

    public $submittedAnswer = null;

    public $questionList = null;

    public $questionNumber = null;

    public function AssessmentQuestion ($attemptId, $questionList, 
            $submittedAnswers, $questionNumber)
    
    {
        $this->questionNumber = $questionNumber;
        if (is_array($submittedAnswers))
            if (array_key_exists($questionNumber, $submittedAnswers))
                $this->submittedAnswer = $submittedAnswers[$questionNumber];
        if (is_array($questionList))
            if (array_key_exists($questionNumber, $questionList))
                $this->questionObjAsArray = $questionList[$questionNumber];
        $this->attemptId = $attemptId;
        return $this;
    }

    protected function renderTrueFalse ()
    {
        $output = "<h2>" . $this->view->escape(
                $this->questionObjAsArray['title']) . "</h2>\n";
        $output .= "<form>\n";
        $output .= $this->view->formRadio("answer", $this->submittedAnswer, 
                $attribs = null, 
                array(
                        1 => "True",
                        0 => "False"
                ));
        $output .= $this->view->formHidden("questionNumber", 
                $this->questionNumber, 
                array(
                        'id' => "course-assessment-question-id"
                ));
        
        $output .= "</form>\n";
        
        $questionNumber = $this->questionNumber;
        $attemptID = $this->attemptId;
        $nextQuestionNumber = $questionNumber + 1;
        $previousQuestionNumber = $questionNumber - 1;
        
        $output .= <<<EOT
<script type="text/javascript">
$(document).ready(function(){
    $("a#assessment_question_save_next").click(function(){
        var answer = $('input[name=answer]:checked').val();;
        var result = api.course_assessment.acl_answerQuestion($attemptID,$questionNumber,answer);
        if (result) {
         window.location.href = "/assessment/view-question/$attemptID/$nextQuestionNumber";
        }
    });
    
    $("a#assessment_question_save_previous").click(function(){
    var answer = $('input[name=answer]:checked').val();;
        var result = api.course_assessment.acl_answerQuestion($attemptID,$questionNumber,answer);
        if (result) {
         window.location.href = "/assessment/view-question/$attemptID/$previousQuestionNumber";
        }
    });
    $("a#assessment_question_save_and_finish").click(function() {
    var answer = $('input[name=answer]:checked').val();;
        var result = api.course_assessment.acl_answerQuestion($attemptID,$questionNumber,answer);
        var finishOK = api.course_assessment.acl_finishAttempt($attemptID);     
                if (finishOK)
                {
                    window.location.href="/assessment/view-results/$attemptID";
                }
    });
                 
                 
 });
        </script>
EOT;
        
        return $output;
    }

    protected function renderMultipleChoice ()
    {
        $output = "<h2>" . $this->view->escape(
                $this->questionObjAsArray['title']) . "</h2>\n";
        $output .= "<form>\n";
        
        if (is_array($this->submittedAnswer)) {
            $submittedAnswer = array();
            foreach ($this->submittedAnswer as $questionId => $answer) {
                if ($answer == true) {
                    array_push($submittedAnswer, $questionId);
                }
            }
        } else {
            $submittedAnswer = array();
        }
        
        if (is_array($this->questionObjAsArray['possibleAnswers']))
            $output .= $this->view->formMultiCheckbox('answer', 
                    $submittedAnswer, 
                    array(
                            'class' => 'baz'
                    ), 
                    array(
                            0 => $this->view->escape(
                                    $this->questionObjAsArray['possibleAnswers'][0]),
                            1 => $this->view->escape(
                                    $this->questionObjAsArray['possibleAnswers'][1]),
                            2 => $this->view->escape(
                                    $this->questionObjAsArray['possibleAnswers'][2]),
                            3 => $this->view->escape(
                                    $this->questionObjAsArray['possibleAnswers'][3])
                    ));
        
        $output .= $this->view->formHidden("questionNumber", 
                $this->questionNumber, 
                array(
                        'id' => "course-assessment-question-id"
                ));
        
        $output .= "</form>\n";
        
        $questionNumber = $this->questionNumber;
        $attemptID = $this->attemptId;
        $nextQuestionNumber = $questionNumber + 1;
        $previousQuestionNumber = $questionNumber - 1;
        
        $output .= <<<EOT
       
<script type="text/javascript">
$(document).ready(function(){
    $("a#assessment_question_save_next").click(function(){
        var answers = {0:"0", 1:"0", 2:"0", 3:"0"};
        $('input[name=answer\\[\\]]:checked').each(function(event){
            answers[$(this).val()] = "1";
        });
        var result = api.course_assessment.acl_answerQuestion($attemptID,$questionNumber,answers);
        if (result) {
         window.location.href = "/assessment/view-question/$attemptID/$nextQuestionNumber";
        }
    });
    
    $("a#assessment_question_save_previous").click(function(){
        var answers = {0:"0", 1:"0", 2:"0", 3:"0"};
        $('input[name=answer\\[\\]]:checked').each(function(event){
            answers[$(this).val()] = "1";
        });
        var result = api.course_assessment.acl_answerQuestion($attemptID,$questionNumber,answers);
        if (result) {
         window.location.href = "/assessment/view-question/$attemptID/$previousQuestionNumber";
        }
    });
                 
                 
    $("a#assessment_question_save_and_finish").click(function() {
        var answers = {0:"0", 1:"0", 2:"0", 3:"0"};
        $('input[name=answer\\[\\]]:checked').each(function(event){
            answers[$(this).val()] = "1";
        });
        var result = api.course_assessment.acl_answerQuestion($attemptID,$questionNumber,answers);
        var finishOK = api.course_assessment.acl_finishAttempt($attemptID);     
                if (finishOK)
                {
                    window.location.href="/assessment/view-results/$attemptID";
                }
    });              
 });
        </script>
EOT;
        
        return $output;
    }

    protected function renderFillInTheBlank ()
    {
        $output = "<h2>Fill in the Blank(s)</h2>\n";
        $questionObjAsArray = $this->questionObjAsArray;
        $answersArr = $this->submittedAnswer;
        $view = $this->view;
        $output .= preg_replace_callback('/__[a-zA-Z0-9]+__/', 
                function  ($input) use( $questionObjAsArray, $view, $answersArr)
                {
                    static $count = 0;
                    if (! is_array($answersArr)) {
                        $answersArr = array();
                    }
                    $textInput = $view->formText("answer-" . $count, 
                            (array_key_exists($count, $answersArr) ? $answersArr[$count] : null), 
                            array(
                                    'data-question_number' => $count
                            ));
                    $count ++;
                    return $textInput;
                }, $questionObjAsArray['title']);
        $output .= "<br />\n";
        $questionNumber = $this->questionNumber;
        $attemptID = $this->attemptId;
        $nextQuestionNumber = $questionNumber + 1;
        $previousQuestionNumber = $questionNumber - 1;
        $output .= <<<EOT
   
                <script type="text/javascript">
$(document).ready(function(){
    $("a#assessment_question_save_next").click(function(){
        var answers = {};
$("input[type=text]").each(function(event){
var fibNumber = $(this).data("question_number");
var fibText = $(this).val();
answers[fibNumber] = fibText;
});
answers;    
        var result = api.course_assessment.acl_answerQuestion($attemptID,$questionNumber,answers);
        if (result) {
         window.location.href = "/assessment/view-question/$attemptID/$nextQuestionNumber";
        }
    });
    
    $("a#assessment_question_save_previous").click(function(){
       var answers = {};
$("input[type=text]").each(function(event){
var fibNumber = $(this).data("question_number");
var fibText = $(this).val();
answers[fibNumber] = fibText;
});
answers;    
        var result = api.course_assessment.acl_answerQuestion($attemptID,$questionNumber,answers);
        if (result) {
         window.location.href = "/assessment/view-question/$attemptID/$previousQuestionNumber";
        }
    });
                 
                 
    $("a#assessment_question_save_and_finish").click(function() {
        var answers = {};
$("input[type=text]").each(function(event){
var fibNumber = $(this).data("question_number");
var fibText = $(this).val();
answers[fibNumber] = fibText;
});
answers;    
        var result = api.course_assessment.acl_answerQuestion($attemptID,$questionNumber,answers);
        var finishOK = api.course_assessment.acl_finishAttempt($attemptID);     
                if (finishOK)
                {
                    window.location.href="/assessment/view-results/$attemptID";
                }
    });              
 });
        </script> 
EOT;
        
        return $output;
    }

    function __toString ()
    {
        try {
            switch ($this->questionObjAsArray['class']) {
                case 'App\Entity\Course\Assessment\Question\MultipleChoice':
                    return $this->renderMultipleChoice();
                    break;
                
                case 'App\Entity\Course\Assessment\Question\TrueFalse':
                    return $this->renderTrueFalse();
                    break;
                
                case 'App\Entity\Course\Assessment\Question\FillInTheBlank':
                    return $this->renderFillInTheBlank();
                    break;
                default:
                    return "";
            }
        } catch (\exception $e) {
            return "Exception caught: " . $e->getMessage();
        }
    }
}