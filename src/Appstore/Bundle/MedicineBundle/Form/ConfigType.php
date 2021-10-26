<?php

namespace Appstore\Bundle\MedicineBundle\Form;


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

            ->add('invoicePrefix','text', array('attr'=>array('class'=>'m-wrap span8','maxlength'=> 4,'placeholder'=>'max 4 char')))
            ->add('instantVendorPercentage','text', array('attr'=>array('class'=>'m-wrap span8 ','maxlength'=> 4,'placeholder'=>'Instant vendor item percentage')))
            ->add('tpPercent','text', array('attr'=>array('class'=>'m-wrap span8 ','maxlength'=> 4,'placeholder'=>'TP Percent')))
            ->add('tpVatPercent','text', array('attr'=>array('class'=>'m-wrap span8 ','maxlength'=> 4,'placeholder'=>'TP vat Percent')))
            ->add('instantVendorPercentage','text', array('attr'=>array('class'=>'m-wrap span8 ','maxlength'=> 4,'placeholder'=>'Instant vendor item percentage')))
            ->add('currency', 'choice', array(
                'attr'=>array('class'=>'span8'),
                'choices' => array(
                    '৳'       => 'Taka(৳)',
                    '$'       => 'Dollar($)'
                ),
            ))
            ->add('invoiceActualPrice')
            ->add('printPreviousDue')
            ->add('printDiscountPercent')
            ->add('customPrint')
            ->add('printOutstanding')
            ->add('posPrint')
            ->add('searchSlug')
            ->add('openingQuantity')
            ->add('regularPosPrint')
            ->add('isBarcode')
            ->add('isPrint')
            ->add('invoicePrintLogo')
            ->add('isPrintHeader')
            ->add('isPrintFooter')
            ->add('expiryDate', 'choice', array(
                'attr' => array(
                    'class'=>'m-wrap span12'),
                    'expanded'      =>false,
                    'empty_value' => '---Default expiry date---',
                    'multiple'      =>false,
                    'choices' => array(
                        '1' => '1 Month',
                        '2' => '2 Month',
                        '3' => '3 Month',
                        '4' => '4 Month',
                        '5' => '5 Month',
                        '6' => '6 Month',
                        '12' => '1 Year',
                        '18' => '1.5 Year',
                        '24' => '2 Years',

                    ),
            ))
            ->add('printLeftMargin','text',array('attr'=>array('class'=>'m-wrap numeric span12')))
            ->add('printTopMargin','text',array('attr'=>array('class'=>'m-wrap numeric span12')))
            ->add('address','textarea',array('attr'=>array('class'=>'m-wrap span12','rows' => 4,'placeholder'=>'Enter vendor address')))
            ->add('printFooterText','textarea',array('attr'=>array('class'=>'m-wrap span12','rows' => 4,'placeholder'=>'Enter print footer')))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\MedicineBundle\Entity\MedicineConfig'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appstore_bundle_medicine_config';
    }



}
