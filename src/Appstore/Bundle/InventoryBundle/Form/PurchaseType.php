<?php

namespace Appstore\Bundle\InventoryBundle\Form;

use Appstore\Bundle\InventoryBundle\Entity\InventoryConfig;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class PurchaseType extends AbstractType
{

    public  $inventoryConfig;

    public function __construct(InventoryConfig $inventoryConfig)
    {
        $this->inventoryConfig = $inventoryConfig;

    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('vendor', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\InventoryBundle\Entity\Vendor',
                'empty_value' => '---Choose a vendor ---',
                'property' => 'companyName',
                'attr'=>array('class'=>'span12 select2'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('wt')
                        ->where("wt.status = 1")
                        ->andWhere("wt.inventoryConfig =".$this->inventoryConfig->getId());
                },
            ))

            ->add('invoice','text', array('attr'=>array('class'=>'m-wrap span12','label' => 'form.invoice','required' => false ,'placeholder'=>'Invoice no')
            ))
            ->add('memo','text', array('attr'=>array('class'=>'m-wrap span12','required' => true ,'label' => 'form.name','placeholder'=>'Memo no'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please add  memo no'))
                )))
            /*->add('chalan','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'')))*/
            ->add('receiveDate','date', array('attr'=>array('class'=>'m-wrap span6','placeholder'=>''),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required'))
                ),
                'years'=> array('2015', '2016', '2017', '2018', '2019', '2020', '2021', '2022', '2023', '2024', '2025'),
                'widget' => 'single_text',
                // this is actually the default format for single_text
                'format' => 'yyyy-MM-dd',
                
                ))
            ->add('totalAmount','text', array('attr'=>array('class'=>'m-wrap span12 numeric','placeholder'=>'Net total amount BDT'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please add total amount BDT'))
                )))
            ->add('paymentAmount','text', array('attr'=>array('class'=>'m-wrap span12 numeric','placeholder'=>'Net payment amount BDT'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please add payment amount BDT'))
                )))
            ->add('dueAmount','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Due amount BDT')))
            ->add('commissionAmount','text', array('attr'=>array('class'=>'m-wrap span12 numeric','placeholder'=>'Commission amount BDT')))
            ->add('totalQnt','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'No of Qnt'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please add total qnt'))
                )))
            ->add('totalItem','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'No of item'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please add total item'))
                )))

            /*->add('advanceAmount','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'')))
            ->add('commissionAmount','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'')))
            ->add('vatAmount','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'')))
            ->add('taxAmount','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'')))
            ->add('totalQnt','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'')))
            ->add('totalItem','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'')))
            ->add('paymentType','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'')))
            ->add('paymentMethod','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'')))*/
            ->add('file','file',array('attr'=>array('class'=>'default span12')))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\InventoryBundle\Entity\Purchase'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appstore_bundle_inventorybundle_purchase';
    }
}
