<?php

namespace CompanyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;

/**
 * Question
 *
 * @ORM\Table(name="question")
 * @ORM\Entity(repositoryClass="CompanyBundle\Repository\QuestionRepository")
 */
class Question {

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
     * @OneToMany(targetEntity="Answer", mappedBy="question", cascade={"persist"})
     */
    private $answers;

    /**
     * @ManyToOne(targetEntity="Survey", inversedBy="questions")
     * @JoinColumn(name="survey_id", referencedColumnName="id")
     */
    private $survey;

    public function __construct() {
        $this->answers = new \Doctrine\Common\Collections\ArrayCollection();
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

    function getAnswers() {
        return $this->answers;
    }

    function getAnswerById($id) {
        foreach ($this->answers as $a) {
            if ($a->getId() == $id) {
                return $a;
            }
        }
        return null;
    }

    function setAnswers($answers) {
        $this->answers = $answers;
    }

    function addAnswer($answer) {
        $this->answers->add($answer);
    }

    function getSurvey() {
        return $this->survey;
    }

    function setSurvey($survey) {
        $this->survey = $survey;
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

    function getStats() {
        return $this->stats;
    }

    function setStats($stats) {
        $this->stats = $stats;
    }


}
