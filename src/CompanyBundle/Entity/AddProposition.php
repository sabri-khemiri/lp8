<?php
namespace CompanyBundle\Entity;

class AddProposition
{
    private $propositions;
    
    public function __construct() {
        $this->propositions = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    function getPropositions() {
        return $this->propositions;
    }

    function setPropositions($propositions) {
        $this->propositions = $propositions;
    }

    function addPorpositions($proposition){
       $this->$propositions->add($proposition);
    }
}
