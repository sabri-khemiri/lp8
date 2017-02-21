<?php
namespace CompanyBundle\Entity;

class AddAnswer
{
    private $answers;
    private $matchs;
    
    public function __construct() {
        $this->answers = new \Doctrine\Common\Collections\ArrayCollection();
        $this->matchs = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    function getMatchs() {
        return $this->matchs;
    }

    function setMatchs($matchs) {
        $this->matchs = $matchs;
    }
    
    function addMatch($match){
       $this->matchs->add($match);
    }

    function getAnswers() {
        return $this->answers;
    }

    function setAnswers($answers) {
        $this->answers = $answers;
    }

    function addAnswer($answer){
       $this->answers->add($answer);
    }

    function getQuestion() {
        return $this->question;
    }

    function setQuestion($question) {
        $this->question = $question;
    }


}
