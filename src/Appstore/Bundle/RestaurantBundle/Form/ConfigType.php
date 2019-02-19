<?php

namespace Appstore\Bundle\RestaurantBundle\Form;


use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class ConfigType extends AbstractType
{


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('vatRegNo','text', array('attr'=>array('class'=>'m-wrap span8','placeholder'=>'Enter vat registration no')))
            ->add('vatPercentage','integer',array('attr'=>array('class'=>'m-wrap numeric span5','max'=> 100)))
            ->add('vatEnable')
            ->add('invoicePrintLogo')
            ->add('isInvoiceTitle')
            ->add('isPrintHeader')
            ->add('isPrintFooter')
            ->add('printInstruction')
            ->add('discountType', 'choice', array(
                'required'    => false,
                'attr'=>array('class'=>'m-wrap span12'),
                'empty_value' => '---Discount Type---',
                'choices' => array(
                    'percentage' => 'Percentage',
                    'flat' => 'Flat',
                ),
            ))
            ->add('payFor', 'choice', array(
                'required'    => true,
                'attr'=>array('class'=>'m-wrap span12'),
                'choices' => array(
                    'pre-pay' => 'Pre-pay',
                    'post-pay' => 'Post-pay',
                ),
            ))
            ->add('discountPercentage','integer',array('attr'=>array('class'=>'m-wrap numeric span5')))
            ->add('invoiceHeight','text',array('attr'=>array('class'=>'m-wrap numeric span12')))
            ->add('printLeftMargin','text',array('attr'=>array('class'=>'m-wrap numeric span12')))
            ->add('printTopMargin','text',array('attr'=>array('class'=>'m-wrap numeric span12')))

        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\RestaurantBundle\Entity\RestaurantConfig'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appstore_bundle_hospitalbundle_particular';
    }


}
