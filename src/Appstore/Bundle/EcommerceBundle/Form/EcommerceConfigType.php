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

            ->add('shippingCharge','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Please set delivery charge')))
            ->add('pickupLocation','textarea', array('attr'=>array('class'=>'m-wrap span12','rows'=>8,'placeholder'=>'Please set address where customer product pickup')))
            ->add('currency', 'choice', array(
                'attr'=>array('class'=>'span12'),
                'choices' => array(
                    '৳'       => 'Taka(৳)',
                    '$'       => 'Dollar($)'
                ),
            ))
            ->add('perPage', 'choice', array(
                'attr'=>array('class'=>'span12'),
                'choices' => array(
                    '15'       => 'Per page-15',
                    '16'       => 'Per page-16',
                    '20'       => 'Per page-20',
                    '21'       => 'Per page-21',
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
            ->add('showMasterName')
/*            ->add('isColor')*/
            ->add('isPreorder')
            ->add('cart')
            ->add('promotion');
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
