<?php

namespace CompanyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SurveyController extends Controller {

    public function addSurveyAction(Request $request) {
        $survey = new \CompanyBundle\Entity\Survey;
        $form = $this->get('form.factory')->create(new \CompanyBundle\Form\SurveyType(), $survey);

        if ($form->handleRequest($request)->isValid()) {
            $survey->setType(($_POST['submit'] == "average") ? true : false);
            $survey->setUserCompany($this->getUser());
            $survey->setCreationDate(new \DateTime());
            $survey->setStatus(0); 

            $em = $this->getDoctrine()->getManager();
            $em->persist($survey);
            $em->flush();
            return $this->redirect($this->generateUrl('company_survey_proposition_add', array('id' => $survey->getId())));
        }
        return $this->render('CompanyBundle:FormSurvey:add_survey.html.twig', array('form' => $form->createView()));
    }

    public function updateSurveyAction(Request $request, $id) {
        $survey = $this->isGarented($id);
        if ($survey == null)
            return $this->denyAccessUnlessGranted('ROLE_EDIT', 'You cannot edit this item.');
        $tmp = clone $survey;
        $form = $this->get('form.factory')->create(new \CompanyBundle\Form\SurveyType(), $survey);

        if ($form->handleRequest($request)->isValid()) {
            if($survey->getPassword() == "")
                $survey->setPassword($tmp->getPassword());
            $em = $this->getDoctrine()->getManager();
            $em->persist($survey);
            $em->flush();
            return $this->redirect($this->generateUrl('company_survey', array('id' => $survey->getId())));
        }
        return $this->render('CompanyBundle:FormSurvey:update_survey.html.twig', array('form' => $form->createView(), 'survey' => $survey));
    }

    public function addPropositionAction($id, Request $request) {
        $survey = $this->isGarented($id);
        if ($survey == null)
            return $this->denyAccessUnlessGranted('ROLE_EDIT', 'You cannot edit this item.');

        $propositions = new \CompanyBundle\Entity\AddProposition();
        $form = $this->get('form.factory')->create(new \CompanyBundle\Form\AddPropositionType(), $propositions);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            foreach ($propositions->getPropositions() as $proposition) {
                $file = $proposition->getImage();
                if($file != null){
                    $proposition->setImage($file->guessExtension());
                }
                $proposition->setSurvey($survey);
                $em->persist($proposition);
                
                if($survey->getStatus() == 0)
                    $survey->setStatus(1);
                $em->merge($survey);
                $em->flush();

                if($file!=null){
                    $fileName = $proposition->getId() . '.' . $file->guessExtension();
                    $file->move($this->getParameter('image_proposition_directory'), $fileName);
                }

                //On met les questions en etat d'attente d'assignation de note
                $questions = $this->getDoctrine()
                        ->getRepository('CompanyBundle:Question')
                        ->findBySurvey($survey);

                $tmp = 0;
                foreach ($questions as $question) {
                    $tmp++;
                    foreach ($question->getAnswers() as $answer) {
                        $match = new \CompanyBundle\Entity\MatchAnswerProposition();
                        $match->setAnswer($answer);
                        $match->setProposition($proposition);
                        $match->setWeight(0);
                        $em = $this->getDoctrine()->getManager();
                        $em->persist($match);
                        $em->flush();
                    }
                }
            }
            if ($tmp == 0)
                return $this->redirect($this->generateUrl('company_survey_question_add', array('id' => $survey->getId())));

            return $this->redirect($this->generateUrl('company_survey', array('id' => $survey->getId())));
        }
        return $this->render('CompanyBundle:Company:add_propositions.html.twig', array('form' => $form->createView()));
    }

    public function updatePropositionAction($idSurvey, $idProposition, Request $request) {
        $survey = $this->isGarented($idSurvey);
        if ($survey == null)
            return $this->denyAccessUnlessGranted('ROLE_EDIT', 'You cannot edit this item.');

        $proposition = $this->getDoctrine()
                ->getRepository('CompanyBundle:Proposition')
                ->findOneById($idProposition);
        if ($proposition == null)
            return $this->denyAccessUnlessGranted('ROLE_EDIT', 'You cannot edit this item.');

        $tmpImage = $proposition->getImage();
        $proposition->setImage(null);
        $form = $this->get('form.factory')->create(new \CompanyBundle\Form\PropositionType(), $proposition);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if ($proposition->getImage() == null) {
                $proposition->setImage($tmpImage);
                $tmpImage = null;
            } else {
                $file = $proposition->getImage();
                $proposition->setImage($file->guessExtension());
            }
            $em->persist($proposition);
            $em->flush();
            $em = $this->getDoctrine()->getManager();

            if ($tmpImage != null) {
                $fileName = $proposition->getId() . '.' . $file->guessExtension();
                $file->move($this->getParameter('image_proposition_directory'), $fileName);
            }
            return $this->redirect($this->generateUrl('company_survey', array('id' => $survey->getId())));
        }
        return $this->render('CompanyBundle:FormSurvey:update_propositions.html.twig', array('form' => $form->createView()));
    }

    public function addQuestionAction($id, Request $request) {
        $survey = $this->isGarented($id);
        if ($survey == null)
            return $this->denyAccessUnlessGranted('ROLE_EDIT', 'You cannot edit this item.');

        //ON recup les propositon
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository("CompanyBundle:Proposition");
        $propositions = $repository->findBySurvey($id, array('id' => 'ASC'));


        $answers = new \CompanyBundle\Entity\AddQuestion();
        $form = $this->get('form.factory')->create(new \CompanyBundle\Form\AddQuestionType(), $answers);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $answers->getQuestion()->setSurvey($survey);
            $em->persist($answers->getQuestion());
            $em->flush();

            foreach ($answers->getAnswers() as $key => $answer) {
                $answer->setQuestion($answers->getQuestion());
                $em->persist($answer);
                $em->flush();

                for ($i = 1; $i <= count($propositions); $i++) {                    
                    $answers->getMatchs()[$key * 10 + $i]->setAnswer($answer);
                    $answers->getMatchs()[$key * 10 + $i]->setProposition($propositions[$i - 1]);
                    $em->persist($answers->getMatchs()[$key * 10 + $i]);
                    $em->flush();
                }
            }

            $answers = new \CompanyBundle\Entity\AddQuestion();
            $form = $this->get('form.factory')->create(new \CompanyBundle\Form\AddQuestionType(), $answers);

            if ($_POST['submit'] == "new") {
                if($survey->getStatus() == 1){
                    $survey->setStatus(2);
                    $em->merge($survey);
                    $em->flush();
                }
                return $this->render('CompanyBundle:Company:add_question.html.twig', array('form' => $form->createView(), 'propositions' => $propositions));
            }
            else{
                $survey->setStatus(4);
                $em->merge($survey);
                $em->flush();
            }
            return $this->redirect($this->generateUrl('company_homepage', array('id' => $survey->getId())));
        }

        return $this->render('CompanyBundle:Company:add_question.html.twig', array('form' => $form->createView(), 'propositions' => $propositions));
    }

    public function addAnswerAction($idSurvey, $idQuestion, Request $request) {
        $survey = $this->isGarented($idSurvey);
        if ($survey == null)
            return $this->denyAccessUnlessGranted('ROLE_EDIT', 'You cannot edit this item.');

        //ON recup les propositon
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository("CompanyBundle:Proposition");
        $propositions = $repository->findBySurvey($idSurvey, array('id' => 'ASC'));

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository("CompanyBundle:Question");
        $question = $repository->findOneById($idQuestion);


        $answers = new \CompanyBundle\Entity\AddQuestion();
        $answers->setQuestion(clone $repository->findOneById($idQuestion));
        $form = $this->get('form.factory')->create(new \CompanyBundle\Form\AddQuestionType(), $answers);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository("CompanyBundle:Question");
        $question = $repository->findOneById($idQuestion);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($answers->getAnswers() as $key => $answer) {
                $answer->setQuestion($question);
                $em->persist($answer);
                $em->flush();

                for ($i = 1; $i <= count($propositions); $i++) {
                    $answers->getMatchs()[$key * 10 + $i]->setAnswer($answer);
                    $answers->getMatchs()[$key * 10 + $i]->setProposition($propositions[$i - 1]);
                    $em->persist($answers->getMatchs()[$key * 10 + $i]);
                    $em->flush();
                }
            }

            $answers = new \CompanyBundle\Entity\AddQuestion();
            $form = $this->get('form.factory')->create(new \CompanyBundle\Form\AddQuestionType(), $answers);

            if ($_POST['submit'] == "new") {
                return $this->render('CompanyBundle:Company:add_question.html.twig', array('form' => $form->createView(), 'propositions' => $propositions));
            }
            return $this->redirect($this->generateUrl('company_homepage', array('id' => $survey->getId())));
        }

        return $this->render('CompanyBundle:FormSurvey:add_answer.html.twig', array('form' => $form->createView(), 'propositions' => $propositions));
    }

    public function updateQuestionAction() {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository("CompanyBundle:Question");
        $question = $repository->findOneById($_POST['id']);

        $question->setWording($_POST['wording']);
        $question->setAnnotation($_POST['annotation']);
        $question->setOrder($_POST['order']);

        $em->persist($question);
        $em->flush();
    }

    public function updateAnswerAction() {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository("CompanyBundle:Answer");
        $answer = $repository->findOneById($_POST['id']);

        $answer->setWording($_POST['wording']);
        $answer->setAnnotation($_POST['annotation']);
        $answer->setOrder($_POST['order']);


        $em->persist($answer);
        $em->flush();
    }

    public function updateMatchAction() {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository("CompanyBundle:MatchAnswerProposition");
        $match = $repository->findOneById($_POST['id']);

        $match->setWeight($_POST['weight']);

        $em->persist($match);
        $em->flush();
    }

    public function deleteQuestionAction($idSurvey, $idQuestion) {
        $survey = $this->isGarented($idSurvey);
        if ($survey == null)
            return $this->denyAccessUnlessGranted('ROLE_EDIT', 'You cannot edit this item.');

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository("CompanyBundle:Question");
        $question = $repository->findOneById($idQuestion);

        foreach ($question->getAnswers() as $answer) {
            foreach ($answer->getMatchs() as $match) {
                $em->remove($match);
            }
            $em->remove($answer);
        }
        $em->remove($question);
        $em->flush();

        return $this->redirect($this->generateUrl('company_survey', array('id' => $survey->getId())));
    }

    public function deleteAnswerAction($idSurvey, $idAnswer) {
        $survey = $this->isGarented($idSurvey);
        if ($survey == null)
            return $this->denyAccessUnlessGranted('ROLE_EDIT', 'You cannot edit this item.');

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository("CompanyBundle:Answer");
        $answer = $repository->findOneById($idAnswer);

        foreach ($answer->getMatchs() as $match) {
            $em->remove($match);
        }
        $em->remove($answer);
        $em->flush();

        return $this->redirect($this->generateUrl('company_survey', array('id' => $survey->getId())));
    }

    public function deletePropositionAction($idSurvey, $idProposition) {
        $survey = $this->isGarented($idSurvey);
        if ($survey == null)
            return $this->denyAccessUnlessGranted('ROLE_EDIT', 'You cannot edit this item.');

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository("CompanyBundle:Proposition");
        $proposition = $repository->findOneById($idProposition);

        $repository = $em->getRepository("CompanyBundle:MatchAnswerProposition");
        $matchs = $repository->findByProposition($proposition);

        foreach ($matchs as $match) {
            $em->remove($match);
        }

        if (!empty($proposition->getImage())) {
            unlink($this->getParameter('image_proposition_directory'). '/' . $proposition->getId() . '.' . $proposition->getImage());
        }
        $em->remove($proposition);
        $em->flush();

        return $this->redirect($this->generateUrl('company_survey', array('id' => $survey->getId())));
    }

    public function deleteSurveyAction($idSurvey) {
        $survey = $this->isGarented($idSurvey);
        if ($survey == null)
            return $this->denyAccessUnlessGranted('ROLE_EDIT', 'You cannot edit this item.');

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository("CompanyBundle:Question");
        $questions = $repository->findBySurvey($survey);

        foreach ($questions as $question) {
            foreach ($question->getAnswers as $answer) {
                foreach ($answer->getMatchs() as $match) {
                    $em->remove($match);
                }
                $em->remove($answer);
            }
            $em->remove($question);
        }

        $repository = $em->getRepository("CompanyBundle:Proposition");
        $propositions = $repository->findBySurvey($survey);

        foreach ($propositions as $proposition) {
            $em->remove($proposition);
        }

        $em->remove($survey);
        $em->flush();

        return $this->redirect($this->generateUrl('company_homepage'));
    }
    
    public function changeStatusSurveyAction($idSurvey, $status){
        $survey = $this->isGarented($idSurvey);
        if ($survey == null || $survey->getStatus() < 3)
            return $this->denyAccessUnlessGranted('ROLE_EDIT', 'You cannot edit this item.');
        
        switch ($status) {
            case "Running":
                $survey->setStatus(4);
                break;
            case "Pause":
                $survey->setStatus(5);
                break;
            case "Close":
                $survey->setStatus(6);
                break;
            default :
                return $this->denyAccessUnlessGranted('ROLE_EDIT', 'You cannot edit this item.');
                break;
        }
        $em = $this->getDoctrine()->getManager();
        $em->merge($survey);
        $em->flush();
        
        return $this->redirect($this->generateUrl('company_survey', array('id' => $survey->getId())));
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
