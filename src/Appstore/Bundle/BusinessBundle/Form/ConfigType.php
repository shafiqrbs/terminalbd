<?php

namespace Appstore\Bundle\BusinessBundle\Form;


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
            ->add('productionType', 'choice', array(
                'attr'=>array('class'=>'m-wrap span12'),
                'choices' => array('' => '-- Select Production --', 'pre-production' => 'Pre Production','post-production' => 'Post Production','vendor-stock' => 'Vendor Stock'),
            ))
	        ->add('businessModel', 'choice', array(
		        'attr'=>array('class'=>'m-wrap span12'),
		        'choices' => array(
			        '' => '-- Select business model --',
			        'general' => 'General',
			        'sign' => 'Digital-Sign',
			        'electrical' => 'Electrical',
			        'stationary' => 'Stationary',
			        'commission' => 'Commission',
			        'event' => 'Event',
			        'bricks' => 'Bricks',
			        'sawmill' => 'Sawmill',
		        ),
	        ))
	        ->add('stockFormat',
		        'choice', array(
			        'attr'=>array('class'=>'m-wrap  span12'),
			        'choices' => array(
				        'wearhouse'           => 'Wearhouse',
				        'category'           => 'Category'
			        ),
			        'required'    => false,
			        'multiple'    => true,
			        'expanded'  => false,
			        'empty_data'  => null,
		        ))

	        ->add('isPowered')
	        ->add('invoicePrintLogo')
            ->add('customInvoicePrint')
            ->add('customInvoice')
            ->add('isInvoiceTitle')
            ->add('showStock')
            ->add('isPrintHeader')
            ->add('isPrintFooter')
            ->add('removeImage')
            ->add('file')
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
            'data_class' => 'Appstore\Bundle\BusinessBundle\Entity\BusinessConfig'
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
