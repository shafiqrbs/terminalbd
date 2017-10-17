<?php

namespace Appstore\Bundle\InventoryBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class InventoryConfigType extends AbstractType
{



    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('vatRegNo','text', array('attr'=>array('class'=>'m-wrap span8 ','placeholder'=>'Enter vat registration no.')))
            ->add('salesReturnDayLimit','integer',array('attr'=>array('class'=>'m-wrap numeric')))
            ->add('shopName','text',array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Barcode code print shop name')))
            ->add('vatPercentage','integer',array('attr'=>array('class'=>'m-wrap numeric span4')))
            ->add('deliveryProcess',
                'choice', array(
                'attr'=>array('class'=>'check-list  span12'),
                'choices' => array(
                    'Pos'           => 'Point of Sales(POS)',
                    'OnlineSales'   => 'Online Sales',
                    'GeneralSales'  => 'General Sales',
                    'ManualSales'   => 'Manual Sales',
                    'Order'         => 'Online Order',
                    'BranchInvoice' => 'Branch Invoice',
                ),
                'required'    => true,
                'multiple'    => true,
                'expanded'  => true,
                'empty_data'  => null,
            ))
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

            ->add('onlineSalesPrinter',
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
            ->add('isBranch')
            ->add('isColor')
            ->add('isSize')
            ->add('barcodeColor')
            ->add('barcodePrint')
            ->add('barcodeSize')
            ->add('isPrintFooter')
            ->add('invoicePrintUserMobile')
            ->add('invoicePrintLogo')
            ->add('barcodeBrandVendor',
                'choice', array(
                    'attr'=>array('class'=>'m-wrap span12'),
                    'choices' => array(
                        '' => '--Select one--',
                        '1' => 'Brand',
                        '2' => 'Vendor',
                    ),
                    'required'    => true,
                    'multiple'    => false,
                    'expanded'  => false,
                    'empty_data'  => null,
            ))
            ->add('barcodeThickness',
                'choice', array(
                    'attr'=>array('class'=>'m-wrap span12'),
                    'choices' => array(
                        '20' => '20',
                        '22' => '22',
                        '24' => '24',
                        '26' => '26',
                        '28' => '28',
                        '30' => '30',
                        '32' => '32',
                        '34' => '34',
                        '36' => '36',
                        '38' => '38',
                        '40' => '40',
                    ),
                    'required'    => true,
                    'multiple'    => false,
                    'expanded'  => false,
                    'empty_data'  => 30,
            ))
            ->add('barcodeFontSize',
                'choice', array(
                    'attr'=>array('class'=>'m-wrap span12'),
                    'choices' => array(
                        '7' => '7',
                        '8' => '8',
                        '9' => '9',
                        '10' => '10',
                        '11' => '11',
                        '12' => '12',
                        '13' => '13',
                    ),
                    'required'    => true,
                    'multiple'    => false,
                    'expanded'  => false,
                    'empty_data'  => 8,
            ))
            ->add('barcodeScale',
                'choice', array(
                    'attr'=>array('class'=>'m-wrap span12'),
                    'choices' => array(
                        '1' => '1',
                        '2' => '2',
                        '3' => '3',
                        '4' => '4',
                    ),
                    'required'    => true,
                    'multiple'    => false,
                    'expanded'  => false,
                    'empty_data'  =>1,
            ))
            ->add('printLeftMargin','text',array('attr'=>array('class'=>'m-wrap numeric span8')))
            ->add('printTopMargin','text',array('attr'=>array('class'=>'m-wrap numeric span8')))
            ->add('barcodeText','text',array('attr'=>array('class'=>'m-wrap span12')))
            ->add('barcodeText',
                'choice', array(
                    'attr'=>array('class'=>'m-wrap span12'),
                    'choices' => array(
                        '' => '--Select Barcode Text--',
                        'including vat' => 'including vat',
                        'without vat' => 'without vat',
                        '+ vat' => '+ vat',
                    ),
                    'required'    => true,
                    'multiple'    => false,
                    'expanded'  => false,
                    'empty_data'  => null,
            ))
            ->add('barcodeHeight','integer',array('attr'=>array('class'=>'m-wrap numeric span8')))
            ->add('barcodeWidth','integer',array('attr'=>array('class'=>'m-wrap numeric span8')))
            ->add('barcodeMargin','integer',array('attr'=>array('class'=>'m-wrap numeric span8')))
            ->add('barcodePadding','integer',array('attr'=>array('class'=>'m-wrap numeric span8')))
        ;
    }


    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\InventoryBundle\Entity\InventoryConfig'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appstore_bundle_inventorybundle_inventoryconfig';
    }
}
