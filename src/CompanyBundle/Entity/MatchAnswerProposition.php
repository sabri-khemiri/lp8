<?php

namespace CompanyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;

/**
 * MatchAnswerProposition
 *
 * @ORM\Table(name="match_answer_proposition")
 * @ORM\Entity(repositoryClass="CompanyBundle\Repository\MatchAnswerPropositionRepository")
 */
class MatchAnswerProposition
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="weight", type="integer")
     */
    private $weight;

    /**
     * @ManyToOne(targetEntity="Proposition")
     * @JoinColumn(name="proposition_id", referencedColumnName="id")
     */ 
    private $proposition;
    
    /**
     * @ManyToOne(targetEntity="Answer", inversedBy="matchs")
     * @JoinColumn(name="answer_id", referencedColumnName="id")
     */ 
    private $answer;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set weight
     *
     * @param integer $weight
     * @return MatchAnswerProposition
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * Get weight
     *
     * @return integer 
     */
    public function getWeight()
    {
        return $this->weight;
    }
    function getProposition() {
        return $this->proposition;
    }

    function setProposition($proposition) {
        $this->proposition = $proposition;
    }
    
    function getAnswer() {
        return $this->answer;
    }

    function setAnswer($answer) {
        $this->answer = $answer;
    }




}
