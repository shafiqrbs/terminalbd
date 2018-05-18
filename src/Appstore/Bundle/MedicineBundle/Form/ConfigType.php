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

            ->add('invoicePrefix','text', array('attr'=>array('class'=>'m-wrap span5 ','maxlength'=> 4,'placeholder'=>'max 4 char')))
            ->add('instantVendorPercentage','text', array('attr'=>array('class'=>'m-wrap span8 ','maxlength'=> 4,'placeholder'=>'Instant vendor item percentage')))
            ->add('vendorPercentage','text', array('attr'=>array('class'=>'m-wrap span8','maxlength'=> 4,'placeholder'=>'Vendor item percentage')))
            ->add('customPrint')
            ->add('customPrint')
            ->add('invoicePrintLogo')
            ->add('isPrintHeader')
            ->add('isPrintFooter')
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
