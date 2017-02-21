<?php

namespace CustomerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SurveyType extends AbstractType
{
    private $id;
    
    function __construct($id) {
        $this->id = $id;
    }

    
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('answers', 'entity', array(
        'class'         => 'CompanyBundle:Answer',
        'choices' => 'wording',
        'multiple' => false,
        'expanded' => true,
        'query_builder' => function(\CompanyBundle\Repository\AnswerRepository $repo) {
        return $repo->getAnswerBySurveyIdQueryBuilder($this->id);}))
    ;
    }
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CompanyBundle\Entity\Question'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'companybundle_question2';
    }
}
