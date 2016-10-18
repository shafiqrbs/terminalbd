<?php

namespace Appstore\Bundle\AccountingBundle\Form;

use Appstore\Bundle\InventoryBundle\Entity\InventoryConfig;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class AccountCashType extends AbstractType
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

            ->add('payment','text', array('attr'=>array('class'=>'m-wrap span12 numeric','placeholder'=>'add payment amount BDT'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please add payment amount BDT'))
                )))

            ->add('accountNo','text', array('attr'=>array('class'=>'m-wrap span12 numeric','placeholder'=>'add your account no')))
            ->add('paymentMethod', 'choice', array(
                'attr'=>array('class'=>'span12 select2'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please choose required'))
                ),
                'choices' => array(
                    'Cash' => 'Cash',
                    'Cheque' => 'Cheque',
                    'Gift card' => 'Gift card',
                    'Bkash' => 'Bkash',
                    'Payment Card' => 'Payment Card',
                    'Other' => 'Other'
                ),
            ))
            ->add('vendor', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\InventoryBundle\Entity\Vendor',
                'empty_value' => '---Choose a vendor---',
                'property' => 'companyName',
                'attr'=>array('class'=>'span12 select2'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required'))
                ),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('wt')
                        ->where("wt.status = 1")
                        ->andWhere("wt.inventoryConfig =".$this->inventoryConfig->getId());
                },
            ))
           /* ->add('purchase', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\InventoryBundle\Entity\Purchase',
                'empty_value' => '---Choose a purchase---',
                'property' => 'grn',
                'attr'=>array('class'=>'span12 select2'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('wt')
                          ->where("wt.inventoryConfig =".$this->inventoryConfig->getId());
                },
            ))
             */
            ->add('bank', 'entity', array(
                'required'    => true,
                'class' => 'Setting\Bundle\ToolBundle\Entity\Bank',
                'empty_value' => '---Choose a bank---',
                'property' => 'name',
                'attr'=>array('class'=>'span12 select2'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('b')
                        ->orderBy("b.name", "ASC");
                },
            ))


        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\AccountingBundle\Entity\AccountPurchase'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appstore_bundle_accountingbundle_accountpurchase';
    }


}
