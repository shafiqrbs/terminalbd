<?php

namespace Appstore\Bundle\BusinessBundle\Form;

use Setting\Bundle\LocationBundle\Repository\LocationRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class MarketingType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12','autocomplete'=>'off','placeholder'=>'Enter executive name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please enter name'))
                ))
            )
            ->add('designation','text', array('attr'=>array('class'=>'m-wrap span12','autocomplete'=>'off','placeholder'=>'Enter designation'),
                    'constraints' =>array(
                        new NotBlank(array('message'=>'Please enter designation'))
                    ))
            )
            ->add('companyName','text', array('attr'=>array('class'=>'m-wrap span12','autocomplete'=>'off','placeholder'=>'Enter company name'),
                    'constraints' =>array(
                        new NotBlank(array('message'=>'Please enter company name'))
                    ))
            )
            ->add('mobileNo','text', array('attr'=>array('class'=>'m-wrap span12','autocomplete'=>'off','placeholder'=>'Enter mobile no'),
                    'constraints' =>array(
                        new NotBlank(array('message'=>'Please enter mobile no'))
                    ))
            )
            ->add('joiningDate', 'date', array(
                'widget' => 'choice',
                'years' => range(date('Y'), date('Y')-50),
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\BusinessBundle\Entity\Marketing'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'marketing';
    }


}
