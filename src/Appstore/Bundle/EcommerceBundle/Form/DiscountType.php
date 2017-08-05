<?php

namespace Appstore\Bundle\EcommerceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class DiscountType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Discount title')))
            ->add('discountAmount','text', array('attr'=>array('class'=>'m-wrap span6 numeric','placeholder'=>'Add discount amount percentage/flat'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Add discount amount percentage/flat'))
                )))
            ->add('type', 'choice', array(
                'attr'=>array('class'=>'span12'),
                'choices' => array(
                    'percentage'       => 'Percentage',
                    'flat'       => 'Flat'
                ),
            ))
            ->add('file','file', array('attr'=>array('class'=>'default')));

        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\EcommerceBundle\Entity\Discount'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appstore_bundle_ecommercebundle_discount';
    }
}
