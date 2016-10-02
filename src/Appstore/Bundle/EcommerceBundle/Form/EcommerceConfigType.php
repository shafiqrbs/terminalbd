<?php

namespace Appstore\Bundle\EcommerceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EcommerceConfigType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('pickupLocation','textarea', array('attr'=>array('class'=>'m-wrap span12','row'=>3,'placeholder'=>'Notes...')))
            ->add('currency', 'choice', array(
                'attr'=>array('class'=>'span12'),
                'choices' => array(
                    '৳'       => 'Taka(৳)',
                    '$'       => 'Dollar($)'
                ),
            ))
            ->add('perColumn', 'choice', array(
                'attr'=>array('class'=>'span12'),
                'choices' => array(
                    '4'       => 'Per Column-3',
                    '3'       => 'Per Column-4',
                    '2'       => 'Per Column-6',
                ),
            ))
            ->add('owlProductColumn', 'choice', array(
                'attr'=>array('class'=>'span12'),
                'choices' => array(
                    '3'       => 'Per Column-3',
                    '4'       => 'Per Column-4',
                    '6'       => 'Per Column-6',
                ),
            ))
            ->add('isColor')
            ->add('isPreorder')
            ->add('cart')
            ->add('webProduct')
            ->add('promotion')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\EcommerceBundle\Entity\EcommerceConfig'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appstore_bundle_ecommercebundle_ecommerceconfig';
    }
}
