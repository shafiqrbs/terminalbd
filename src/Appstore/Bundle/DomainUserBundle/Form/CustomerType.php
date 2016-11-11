<?php

namespace Appstore\Bundle\DomainUserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CustomerType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12 ','placeholder'=>'customer name')))
            ->add('mobile','text', array('attr'=>array('class'=>'m-wrap span12 mobile numeric ','placeholder'=>'mobile no')))
            ->add('email','text', array('attr'=>array('class'=>'m-wrap span12 ','placeholder'=>'email address')))
            ->add('ageGroup', 'choice', array(
            'attr'=>array('class'=>'span12 '),
            'choices' => array(
                'Kids' => 'Kids',
                'Adult' => 'Adult'
            ),
            'required'    => true,
            'empty_data'  => null,
            ))
            ->add('gender', 'choice', array(
                'attr'=>array('class'=>'span12'),
                'choices' => array(
                    'Male' => 'Male',
                    'Female' => 'Female',
                    'Others' => 'Others'
                ),
            ));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\DomainUserBundle\Entity\Customer'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appstore_bundle_domainuserbundle_customer';
    }
}
