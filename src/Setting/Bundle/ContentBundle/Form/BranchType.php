<?php

namespace Setting\Bundle\ContentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class  BranchType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter location name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),
                )
            ))
            ->add('mobile','text', array('attr'=>array('class'=>'m-wrap span12 mobile','placeholder'=>'Enter mobile no'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),
                ))
            )
            ->add('email','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter email address')

            ))
            ->add('phone','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter phone/mobile no')

            ))
            ->add('fax','text', array('attr'=>array('class'=>'m-wrap span12 phone','placeholder'=>'Enter fax no')

            ))
            ->add('contactPerson','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter contact person')
            ))
            ->add('designation','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter designation')
            ))
            ->add('address','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter address'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),

                )))
            ->add('googleMap','textarea', array('attr'=>array('class'=>'span12 m-wrap','rows'=>5)))
            ->add('status')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Setting\Bundle\ContentBundle\Entity\Branch'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'setting_bundle_contentbundle_branch';
    }
}
