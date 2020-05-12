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
            ->add('pickupLocation','textarea', array('attr'=>array('class'=>'m-wrap span12','rows'=> 6,'placeholder'=>'Please set address where customer product pickup')))
            ->add('address','textarea', array('attr'=>array('class'=>'m-wrap span12','rows'=> 6 ,'placeholder'=>'Please set address ')))
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
                    '24'       => 'Per page-24',
                    '27'       => 'Per page-27',
                    '28'       => 'Per page-28',
                    '30'       => 'Per page-30',
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
            ->add('menuType', 'choice', array(
                'attr'=>array('class'=>'span12'),
                'choices' => array(
                    'Mega'       => 'Mega',
                    'Dropdown'       => 'Dropdown',
                    'Sidebar'       => 'Sidebar',
                ),
            ))
            ->add('owlProductColumn', 'choice', array(
                'attr'=>array('class'=>'span12'),
                'choices' => array(
                    '3'       => 'Per Column-3',
                    '4'       => 'Per Column-4',
                    '5'       => 'Per Column-5',
                    '6'       => 'Per Column-6',
                ),
            ))
            ->add('titleBar', 'choice', array(
                'attr'=>array('class'=>'span12'),
                'choices' => array(
                    'none'       => 'None',
                    'top'       => 'Top',
                    'bottom'       => 'Bottom',
                    'both'       => 'Both',
                ),
            ))
            ->add('paginationShow', 'choice', array(
                'attr'=>array('class'=>'span12'),
                'choices' => array(
                    'top'       => 'Top',
                    'bottom'    => 'Bottom',
                    'both'      => 'Both',
                ),
            ))
            ->add('file')
            ->add('cartSearch')
            ->add('footerCategory')
            ->add('isAdditionalItem')
            ->add('searchCategory')
            ->add('showSidebar')
            ->add('showBrand')
            ->add('showCategory')
            ->add('sidebarBrand')
            ->add('sidebarCategory')
            ->add('sidebarPrice')
            ->add('sidebarSize')
            ->add('sidebarColor')
            ->add('sidebarPromotion')
            ->add('sidebarDiscount')
            ->add('sidebarTag')
            ->add('isPreorder')
            ->add('isColor')
            ->add('isSize')
            ->add('cart')
            ->add('printBy')
            ->add('uploadFile')
            ->add('isPrintHeader')
            ->add('isPrintFooter')
            ->add('printer',
                'choice', array(
                    'attr'=>array('class'=>'m-wrap span12'),
                    'choices' => array(
                        'save'          => 'Save',
                        'printer'       => 'Printer',
                        'pos'           => 'Pos Printer',
                    ),
                    'required'    => true,
                    'multiple'    => false,
                    'expanded'  => false,
                    'empty_data'  => null,
                ))
            ->add('vatEnable')
            ->add('vat','text',array('attr'=>array('class'=>'m-wrap numeric span8')))
            ->add('vatRegNo','text',array('attr'=>array('class'=>'m-wrap numeric span8')))
            ->add('printLeftMargin','text',array('attr'=>array('class'=>'m-wrap numeric span8')))
            ->add('printTopMargin','text',array('attr'=>array('class'=>'m-wrap numeric span8')))
            ->add('printMarginBottom','text',array('attr'=>array('class'=>'m-wrap numeric span8')))

        ;
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
