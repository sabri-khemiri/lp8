<?php

namespace CompanyBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddAnswerType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('answers', 'collection', array(
        'type'         => new AnswerType(),
        'allow_add'    => true,
        'allow_delete' => true,
        'by_reference' => false
        ))
        ->add('matchs', 'collection', array(
        'type'         => new MatchType(),
        'allow_add'    => true,
        'allow_delete' => true,
        'by_reference' => false
        ))        
    ;
    }
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CompanyBundle\Entity\AddAnswer'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'companybundle_add_answer';
    }
}
