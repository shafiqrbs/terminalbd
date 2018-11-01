<?php

namespace Appstore\Bundle\HotelBundle\Form;


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

            ->add('address','textarea', array('attr'=>array('class'=>'m-wrap span12','rows'=> 8,'placeholder'=>'Enter company address')))
            ->add('bodyFontSize', 'choice', array(
                'attr'=>array('class'=>'m-wrap span12'),
                'choices' => array('' => 'Font Size', '10px' => '10px',  '12px' => '12px','14px' => '14px', '16px' => '16px','18px' => '18px',  '20px' => '20px'),
            ))
            ->add('invoiceFontSize', 'choice', array(
                'attr'=>array('class'=>'m-wrap span12'),
                'choices' => array('' => 'Font Size', '10px' => '10px',  '12px' => '12px','14px' => '14px', '16px' => '16px','18px' => '18px',  '20px' => '20px'),
            ))
            ->add('isPowered')
	        ->add('invoicePrintLogo')
            ->add('customInvoicePrint')
            ->add('isInvoiceTitle')
            ->add('showStock')
            ->add('isPrintHeader')
            ->add('isPrintFooter')
            ->add('removeImage')
            ->add('file')
            ->add('invoiceHeight','text',array('attr'=>array('class'=>'m-wrap numeric span12')))
	        ->add('printLeftMargin','text',array('attr'=>array('class'=>'m-wrap numeric span12')))
	        ->add('printTopMargin','text',array('attr'=>array('class'=>'m-wrap numeric span12')))
	        ->add('notification')
	        ->add('invoiceBookingsms','text',array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter booking sms text')))
            ->add('invoiceCheckinsms','text',array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter check in sms text')))
            ->add('invoiceCheckoutsms','text',array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter check out sms')))

        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\HotelBundle\Entity\HotelConfig'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'config';
    }



}
