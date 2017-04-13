<?php

namespace Appstore\Bundle\EcommerceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CouponType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Add coupon name')))
            ->add('discountAmount','text', array('attr'=>array('class'=>'m-wrap span12 numeric','placeholder'=>'Add discount amount ')))
            ->add('validAmount','text', array('attr'=>array('class'=>'m-wrap span12 numeric','placeholder'=>'Add valid more then amount ')))
            ->add('startDate', 'date', array(
                'widget' => 'single_text',
                'placeholder' => array(
                    'mm' => 'mm', 'dd' => 'dd','YY' => 'YY'

                ),
                'format' => 'dd-MM-yyyy',
                'attr' => array('class'=>'m-wrap span5 datePicker'),
                'view_timezone' => 'Asia/Dhaka'))
            ->add('endDate', 'date', array(
                'widget' => 'single_text',
                'placeholder' => array(
                    'mm' => 'mm', 'dd' => 'dd','YY' => 'YY'

                ),
                'format' => 'dd-MM-yyyy',
                'attr' => array('class'=>'m-wrap span5 datePicker'),
                'view_timezone' => 'Asia/Dhaka'))
            ->add('quantity','number', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Add no of coupon')))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\EcommerceBundle\Entity\Coupon'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appstore_bundle_ecommercebundle_coupon';
    }
}
