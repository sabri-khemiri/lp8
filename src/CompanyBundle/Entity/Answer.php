<?php

namespace CompanyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\JoinColumn;

/**
 * Answer
 *
 * @ORM\Table(name="answer")
 * @ORM\Entity(repositoryClass="CompanyBundle\Repository\AnswerRepository")
 */
class Answer {

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="wording", type="string", length=255, nullable=true)
     */
    private $wording;

    /**
     * @ManyToOne(targetEntity="Question", inversedBy="answers")
     * @JoinColumn(name="question_id", referencedColumnName="id")
     */
    private $question;

    /**
     * @var string
     *
     * @ORM\Column(name="annotation", type="string")
     */
    private $annotation;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * @var integer
     *
     * @ORM\Column(name="pass_order", type="integer", nullable=true)
     */
    private $order;
    
    /**
     * @OneToMany(targetEntity="MatchAnswerProposition", mappedBy="answer")
     */
    private $matchs;

    function __construct() {
        $this->matchs = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set wording
     *
     * @param string $wording
     * @return Question
     */
    public function setWording($wording) {
        $this->wording = $wording;

        return $this;
    }

    /**
     * Get wording
     *
     * @return string 
     */
    public function getWording() {
        return $this->wording;
    }

    function getQuestion() {
        return $this->question;
    }

    function setQuestion($question) {
        $this->question = $question;
    }

    function getAnnotation() {
        return $this->annotation;
    }

    function getImage() {
        return $this->image;
    }

    function getOrder() {
        return $this->order;
    }

    function setAnnotation($annotation) {
        $this->annotation = $annotation;
    }

    function setImage($image) {
        $this->image = $image;
    }

    function setOrder($order) {
        $this->order = $order;
    }
    function getMatchs() {
        return $this->matchs;
    }
    
    function getMatchByProposition($id) {
        foreach($this->matchs as $m){
            if($m->getProposition()->getId() == $id){
                return $m;
            }
        }
        return null;
    }

    function setMatchs($matchs) {
        $this->matchs = $matchs;
    }
    
    function addMatch($match){
        $this->matchs->add($matchs);
    }


}
