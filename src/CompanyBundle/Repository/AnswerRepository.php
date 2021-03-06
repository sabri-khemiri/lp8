<?php

namespace CompanyBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * AnswerRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AnswerRepository extends EntityRepository
{

  public function getAnswerBySurveyIdQueryBuilder($id)
  {
    return $this
      ->createQueryBuilder('a')
      ->where('a.question = :question')
      ->setParameter('question', $id)
    ;
  }    
}
