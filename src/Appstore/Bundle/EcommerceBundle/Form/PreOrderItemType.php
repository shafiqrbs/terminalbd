<?php

namespace Appstore\Bundle\EcommerceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class PreOrderItemType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please add  your item name'))
                )))
            ->add('url','text', array('attr'=>array('class'=>'m-wrap span12'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please add  your item url'))
                )))
            ->add('quantity','integer', array('attr'=>array('class'=>'m-wrap span12 numeric','min'=>1,'placeholder'=>'No of quantity'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please add  quantity'))
                )))
            ->add('dollar','text', array('attr'=>array('class'=>'m-wrap span12 numeric','placeholder'=>'Dollar price'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please add  item price as dollar'))
                )))
            ->add('price','text', array('attr'=>array('class'=>'m-wrap span12 numeric','placeholder'=>'Unit price convert taka '),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please add item price convert taka'))
                )))
            ->add('shippingCharge','text', array('attr'=>array('class'=>'m-wrap span12 numeric','placeholder'=>'Shipping charge using taka ')))
            ->add('details','textarea', array('attr'=>array('class'=>'m-wrap span12','row'=>3,'placeholder'=>'Notes...')))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\EcommerceBundle\Entity\PreOrderItem'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appstore_bundle_ecommercebundle_preorder_item';
    }
}
