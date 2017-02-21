<?php

namespace CompanyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CompanyController extends Controller {

    public function homepageAction() {
        $surveys = $this->getDoctrine()
            ->getRepository('CompanyBundle:Survey')
            ->findByUserCompany($this->getUser());
        return $this->render('CompanyBundle:Company:homepage.html.twig', array('surveys' => $surveys));
    }
    
    public function viewSurveyAction($id) {
        $survey = $this->isGarented($id);
        if ($survey == null)
            return $this->denyAccessUnlessGranted('ROLE_EDIT', 'You cannot edit this item.');
        
        $questions = $this->getDoctrine()
            ->getRepository('CompanyBundle:Question')
            ->findBySurvey($survey);
        
        $propositions = $this->getDoctrine()
            ->getRepository('CompanyBundle:Proposition')
            ->findBySurvey($survey);

        return $this->render('CompanyBundle:Company:survey.html.twig', array('survey' => $survey, 'questions' => $questions, 'propositions' => $propositions));
    }
    
    public function sendInvitationAction($id) {
        echo $_GET['idSurvey'];
        $survey = $this->getDoctrine()
                ->getRepository('CompanyBundle:Survey')
                ->findOneById($_GET['idSurvey']);

        $textMessage = "Bonjour, je t'invite à faire le questionnaire : " . $survey->getTitle() . ". Voici l'adresse : " . $this->generateUrl('customer_survey_login', array('idSurvey'=>$survey->getId()));
        $message = \Swift_Message::newInstance()
            ->setSubject('New')
            ->setFrom('website@example.com')
            ->setTo($_GET['email'])
            ->setBody(
                $textMessage
            )
        ;
        
        $this->get('mailer')->send($message);

        
        
        echo $textMessage;
        

        return new \Symfony\Component\HttpFoundation\Response(200);
    }


    private function isGarented($id) {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('s')
                ->from('CompanyBundle:Survey', 's')
                ->where('s.userCompany = ?1 AND s.id = ?2');
        $qb->setParameters(array(1 => $this->getUser()->getId(), 2 => $id));
        $query = $qb->getQuery();
        $results = $query->getResult();
        return (count($results) == 0) ? null : $results[0];
    }

}
