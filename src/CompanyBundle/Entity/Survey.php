<?php

namespace CompanyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;

/**
 * Survey
 *
 * @ORM\Entity
 * @ORM\Table(name="survey")
 */
class Survey {

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
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=true)
     * Crée : 0, Etape n°1 : 1, Etape n°2 : 2, Etape n°3 : 3, 
     * En cours : 4, En pause : 5, Clôturé : 6
     */
    private $status;

    /**
     * @var float
     *
     * @ORM\Column(name="rating", type="float", nullable=true)
     */
    private $rating;

    /**
     * @var integer
     *
     * @ORM\Column(name="surveyedNumber", type="integer", nullable=true)
     */
    private $surveyedNumber;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="ratingNumber", type="integer", nullable=true)
     */
    private $ratingNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255, nullable=true)
     */
    private $password;

    /**
     *
     * @ORM\Column(name="creation_date", type="datetime")
     */
    private $creationDate;
    
    /**
     * @var string
     *
     * @ORM\Column(name="annotation", type="string", nullable=true)
     */
    private $annotation;

    /**
     * @OneToMany(targetEntity="Question", mappedBy="survey")
     */
    private $questions;

    /**
     * @ManyToOne(targetEntity="UserCompanyBundle\Entity\UserCompany")
     * @JoinColumn(name="user_company_id", referencedColumnName="id")
     */
    private $userCompany;

    public function __construct() {
        $this->questions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->surveyedNumber = 0;
        $this->rating = 0;
        $this->ratingNumber = 0;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    function getQuestions() {
        return $this->questions;
    }

    function setQuestions($questions) {
        $this->questions = $questions;
    }

    function getForm() {
        return $this->form;
    }

    function setForm($form) {
        $this->form = $form;
    }

    function getStatus() {
        return $this->status;
    }

    function setStatus($status) {
        $this->status = $status;
    }

    function getTitle() {
        return $this->title;
    }

    function getRating() {
        return $this->rating;
    }

    function getSurveyedNumber() {
        return $this->surveyedNumber;
    }

    function getType() {
        return $this->type;
    }

    function getPassword() {
        return $this->password;
    }

    function getCreationDate() {
        return $this->creationDate;
    }

    function setTitle($title) {
        $this->title = $title;
    }

    function setRating($rating) {
        $this->rating = $rating;
    }

    function setSurveyedNumber($surveyedNumber) {
        $this->surveyedNumber = $surveyedNumber;
    }

    function setType($type) {
        $this->type = $type;
    }

    function setPassword($password) {
        $this->password = $password;
    }

    function setCreationDate($creationDate) {
        $this->creationDate = $creationDate;
    }
    function getUserCompany() {
        return $this->userCompany;
    }

    function setUserCompany($userCompany) {
        $this->userCompany = $userCompany;
    }
    
    function getAnnotation() {
        return $this->annotation;
    }

    function setAnnotation($annotation) {
        $this->annotation = $annotation;
    }

    function getRatingNumber() {
        return $this->ratingNumber;
    }

    function setRatingNumber($ratingNumber) {
        $this->ratingNumber = $ratingNumber;
    }

}
