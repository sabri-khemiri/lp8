<?php

namespace CustomerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;

/**
 * Customer
 *
 * @ORM\Table(name="customer")
 * @ORM\Entity(repositoryClass="CustomerBundle\Repository\CustomerRepository")
 */
class Customer {

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
     * @ORM\Column(name="first_name", type="string", length=255, nullable=true)
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=255, nullable=true)
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     *
     * @ORM\Column(name="creation_date", type="datetime")
     */
    private $creationDate;

    /**
     * @ManyToMany(targetEntity="CompanyBundle\Entity\Answer")
     * @JoinTable(name="customer_answer",
     *      joinColumns={@JoinColumn(name="customer_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="answer_id", referencedColumnName="id")}
     *      )
     */
    private $answers;

    /**
     * @ManyToOne(targetEntity="CompanyBundle\Entity\Survey")
     * @JoinColumn(name="survey_id", referencedColumnName="id")
     */
    private $survey;
    
    private $password;

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
     * Set firstName
     *
     * @param string $firstName
     * @return Customer
     */
    public function setFirstName($firstName) {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string 
     */
    public function getFirstName() {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     * @return Customer
     */
    public function setLastName($lastName) {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string 
     */
    public function getLastName() {
        return $this->lastName;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Customer
     */
    public function setEmail($email) {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail() {
        return $this->email;
    }

    function getAnswers() {
        return $this->answers;
    }

    function setAnswers($answers) {
        $this->answers = $answers;
    }
    
    function addAnswer($answer) {
        $this->answers->add($answer);
    }

    function getCreationDate() {
        return $this->creationDate;
    }

    function setCreationDate($creationDate) {
        $this->creationDate = $creationDate;
    }

    function getPassword() {
        return $this->password;
    }

    function setPassword($password) {
        $this->password = $password;
    }

    function getSurvey() {
        return $this->survey;
    }

    function setSurvey($survey) {
        $this->survey = $survey;
    }


}
