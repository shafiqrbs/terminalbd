<?php

namespace Appstore\Bundle\DmsBundle\Form;


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
            ->add('customerPrefix','text', array('attr'=>array('class'=>'m-wrap span5','maxlength'=> 4,'placeholder'=>'max 4 char')))
            ->add('invoicePrefix','text', array('attr'=>array('class'=>'m-wrap span5 ','maxlength'=> 4,'placeholder'=>'max 4 char')))
            ->add('vatPercentage','integer',array('attr'=>array('class'=>'m-wrap numeric span5','max'=> 100)))
            ->add('vatEnable')
            ->add('isDefaultMedicine')
            ->add('bodyFontSize', 'choice', array(
                'attr'=>array('class'=>' span12'),
                'choices' => array('' => 'Font Size', '10px' => '10px',  '12px' => '12px','14px' => '14px', '16px' => '16px','18px' => '18px',  '20px' => '20px'),
            ))
            ->add('invoiceFontSize', 'choice', array(
                'attr'=>array('class'=>' span12'),
                'choices' => array('' => 'Font Size', '10px' => '10px',  '12px' => '12px','14px' => '14px', '16px' => '16px','18px' => '18px',  '20px' => '20px'),
            ))
            ->add('sidebarFontSize', 'choice', array(
                'attr'=>array('class'=>' span12'),
                'choices' => array('' => 'Font Size', '10px' => '10px',  '12px' => '12px','14px' => '14px', '16px' => '16px','18px' => '18px',  '20px' => '20px'),
            ))
            /*->add('invoiceProcess',
                'choice', array(
                    'attr'=>array('class'=>'check-list  span12'),
                    'choices' => array(
                        'diagnostic'    => 'Diagnostic',
                        'admission'   => 'Admission',
                        'doctor'  => 'Doctor',
                    ),
                    'required'    => true,
                    'multiple'    => true,
                    'expanded'  => true,
                    'empty_data'  => null,
            ))*/
            ->add('invoicePrintLogo')
            ->add('isInvoiceTitle')
            ->add('isPrintHeader')
            ->add('isPrintFooter')
            ->add('printInstruction')
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
            'data_class' => 'Appstore\Bundle\DmsBundle\Entity\DmsConfig'
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
