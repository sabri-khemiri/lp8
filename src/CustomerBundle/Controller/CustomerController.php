<?php

namespace CustomerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session;

class CustomerController extends Controller {

    public function homepageSurveyAction(\Symfony\Component\HttpFoundation\Request $request, $idSurvey) {
          if($this->get('session')->get('step') == 1){
            return $this->redirect($this->generateUrl('customer_survey_form', array('idSurvey' => $idSurvey)));
        }
        elseif ($this->get('session')->get('step') == 2) {
            return $this->redirect($this->generateUrl('customer_survey_proposition', array('idSurvey' => $idSurvey)));
        }
        
        $survey = $this->getDoctrine()
                ->getRepository('CompanyBundle:Survey')
                ->findOneById($idSurvey);

        $customer = new \CustomerBundle\Entity\Customer();
        $form = $this->get('form.factory')->create(new \CustomerBundle\Form\CustomerType(), $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($customer->getPassword() != $survey->getPassword()) {
                $error = new \Symfony\Component\Form\FormError("Mot de passe erroné!");
                $form->get('password')->addError($error);
            } else {
                $customer->setCreationDate(new \DateTime());
                $customer->setSurvey($survey);
                $em = $this->getDoctrine()->getManager();
                $em->persist($customer);
                $em->flush();

                $this->get('session')->set('customer', $customer->getID());
                $this->get('session')->set('step', 1);

                return $this->redirect($this->generateUrl('customer_survey_form', array('idSurvey' => $survey->getId())));
            }
        }
        return $this->render('CustomerBundle:Default:homepage_survey.html.twig', array('survey' => $survey, 'form' => $form->createView()));
    }

    public function formSurveyAction(\Symfony\Component\HttpFoundation\Request $reques, $idSurvey) {
        if (!$this->get('session')->has('step')) {
            return $this->redirect($this->generateUrl('customer_survey_login', array('idSurvey' => $idSurvey)));
        }    
        elseif ($this->get('session')->get('step') == 2) {
            return $this->redirect($this->generateUrl('customer_survey_proposition', array('idSurvey' => $idSurvey)));
        }
        $customer = $this->getDoctrine()
                ->getRepository('CustomerBundle:Customer')
                ->findOneById($this->get('session')->get('customer'));

        if ($customer->getSurvey()->getId() != $idSurvey)
            return $this->denyAccessUnlessGranted('ROLE_EDIT', 'You cannot edit this item.');

        $survey = $customer->getSurvey();

        $questions = $this->getDoctrine()
                ->getRepository('CompanyBundle:Question')
                ->findBySurvey($survey);

        if (!empty(($_POST))) {
            $em = $this->getDoctrine()->getManager();
            $customer->setAnswers(new \Doctrine\Common\Collections\ArrayCollection());
            foreach ($questions as $q) {
                $a = $q->getAnswerById($_POST[strval($q->getId())]);
                $customer->addAnswer($a);
                $em->merge($a);
            }
            $survey->setSurveyedNumber($survey->getSurveyedNumber() + 1);
            $em->merge($survey);
            $em->flush();
            $this->get('session')->set('step', 2);

            return $this->redirect($this->generateUrl('customer_survey_proposition', array('idSurvey' => $survey->getId())));
        }
        return $this->render('CustomerBundle:Default:surveyview.html.twig', array('questions' => $questions, 'survey' => $survey));
    }

    public function propositionSurveyAction(\Symfony\Component\HttpFoundation\Request $reques, $idSurvey) {
        if (!$this->get('session')->has('step')) {
            return $this->redirect($this->generateUrl('customer_survey_login', array('idSurvey' => $idSurvey)));
        } elseif ($this->get('session')->get('step') != 2) {
            return $this->redirect($this->generateUrl('customer_survey_form', array('idSurvey' => $idSurvey)));
        }
        $customer = $this->getDoctrine()
                ->getRepository('CustomerBundle:Customer')
                ->findOneById($this->get('session')->get('customer'));

        if ($customer->getSurvey()->getId() != $idSurvey || empty($customer->getAnswers()))
            return $this->denyAccessUnlessGranted('ROLE_EDIT', 'You cannot edit this item.');

        $propositions = $this->getDoctrine()
                ->getRepository('CompanyBundle:Proposition')
                ->findBySurvey($customer->getSurvey());

        $tmp;
        $tmpProposition;
        foreach ($propositions as $proposition) {
            $tmp[$proposition->getId()] = 0;
            $tmpProposition[$proposition->getId()] = $proposition;
        }

        foreach ($propositions as $p) {
            foreach ($customer->getAnswers() as $a) {
                $tmp[$p->getId()] += $a->getMatchByProposition($p->getId())->getWeight();
            }
        }
        arsort($tmp);
        $tmp = array_chunk($tmp, 3, true)[0];
        $this->get('session')->set('rank', $tmp);

        $rating = $this->get('session')->has('rating')?$this->get('session')->get('rating'):false;

        return $this->render('CustomerBundle:Default:survey_proposition.html.twig', array('survey' => $customer->getSurvey(), 'ranks' => $tmp, 'propositions' => $tmpProposition, 'rating' => $rating));
    }

    public function ratingSurveyAction($idSurvey) {
        if (!$this->get('session')->has('step')) {
            return $this->redirect($this->generateUrl('customer_survey_login', array('idSurvey' => $idSurvey)));
        } elseif ($this->get('session')->get('step') != 2) {
            return $this->redirect($this->generateUrl('customer_survey_form', array('idSurvey' => $idSurvey)));
        }
        $customer = $this->getDoctrine()
                ->getRepository('CustomerBundle:Customer')
                ->findOneById($this->get('session')->get('customer'));

        if ($customer->getSurvey()->getId() != $idSurvey || !isset($_POST['rating']) || empty($_POST['rating']))
            return $this->denyAccessUnlessGranted('ROLE_EDIT', 'You cannot edit this item.');

        $s = $customer->getSurvey();
        $s->setRating(($s->getRating() * $s->getRatingNumber() + $_POST['rating']) / ($s->getRatingNumber() + 1));
        $s->setRatingNumber($s->getRatingNumber() + 1);
        $em = $this->getDoctrine()->getManager();
        $em->merge($s);
        $em->flush();
        $this->get('session')->set('rating', true);

        return $this->redirect($this->generateUrl('customer_survey_proposition', array('idSurvey' => $s->getId())));
    }

    public function selectPropositionSurveyAction($idSurvey, $idProposition) {
        if (!$this->get('session')->has('step')) {
            return $this->redirect($this->generateUrl('customer_survey_login', array('idSurvey' => $idSurvey)));
        } elseif ($this->get('session')->get('step') != 2) {
            return $this->redirect($this->generateUrl('customer_survey_form', array('idSurvey' => $idSurvey)));
        }
        $customer = $this->getDoctrine()
                ->getRepository('CustomerBundle:Customer')
                ->findOneById($this->get('session')->get('customer'));

        $tmp = $this->get('session')->get('rank');
        if (isset($tmp[$idProposition])) {
            return $this->denyAccessUnlessGranted('ROLE_EDIT', 'You cannot edit this item.');
        }
        $proposition = $this->getDoctrine()
                ->getRepository('CompanyBundle:Proposition')
                ->findOneById($idProposition);
        $proposition->setSelected($proposition->getSelected() + 1);
        $em = $this->getDoctrine()->getManager();
        $em->merge($proposition);
        $em->flush();

        $textMessage = "Bonjour, l'utilisateur " . $customer->getEmail() ." à choisi la proposition : " . $proposition->getTitle();
        if($_POST['message'] != "")
            $textMessage = $textMessage . "Il vous a laissé un message : " . $_POST['message'];
        $message = \Swift_Message::newInstance()
            ->setSubject('New')
            ->setFrom('website@example.com')
            ->setTo($proposition->getSurvey()->getUserCompany()->getEmail())
            ->setBody(
                $textMessage
            )
        ;
        
        $this->get('mailer')->send($message);

        
        $this->get('session')->clear();
        
        

        return $this->redirect($this->generateUrl('customer_survey_end'));
    }
    
    public function endSurveyAction() {
        return $this->render('CustomerBundle:Default:survey_end.html.twig');
    }
}
