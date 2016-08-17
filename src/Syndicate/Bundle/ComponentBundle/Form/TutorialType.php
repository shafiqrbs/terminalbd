<?php

namespace Syndicate\Bundle\ComponentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TutorialType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('email')
            ->add('mobile')
            ->add('address')
            ->add('facebookPage')
            ->add('status')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Syndicate\Bundle\ComponentBundle\Entity\Tutorial'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'syndicate_bundle_componentbundle_tutorial';
    }
}
