<?php

namespace Appstore\Bundle\AccountingBundle\Form;

use Appstore\Bundle\InventoryBundle\Entity\globalOption;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class AccountSalesType extends AbstractType
{

    public  $globalOption;

    public function __construct(\Setting\Bundle\ToolBundle\Entity\GlobalOption $globalOption)
    {
        $this->globalOption = $globalOption;

    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('amount','text', array('attr'=>array('class'=>'m-wrap span12 numeric','placeholder'=>'add payment amount BDT'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please add payment amount BDT'))
                )))

            ->add('accountNo','text', array('attr'=>array('class'=>'m-wrap span12 numeric','placeholder'=>'add your account no')))
            ->add('paymentMethod', 'choice', array(
                'attr'=>array('class'=>'span12 select2'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please chose required'))
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
            ->add('accountHead', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\AccountingBundle\Entity\AccountHead',
                'empty_value' => '---Choose a acount head---',
                'property' => 'name',
                'attr'=>array('class'=>'span12 select2'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required'))
                ),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.parent = 20 ");
                },
            ))
            ->add('customer', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\DomainUserBundle\Entity\Customer',
                'empty_value' => '---Choose a customer---',
                'property' => 'name',
                'attr'=>array('class'=>'span12 select2'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required'))
                ),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.globalOption =".$this->globalOption->getId());
                },
            ))
           /* ->add('sales', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\InventoryBundle\Entity\Purchase',
                'empty_value' => '---Choose a sales---',
                'property' => 'grn',
                'attr'=>array('class'=>'span12 select2'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('wt')
                          ->where("wt.globalOption =".$this->globalOption->getId());
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
            'data_class' => 'Appstore\Bundle\AccountingBundle\Entity\AccountSales'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appstore_bundle_accountingbundle_AccountSales';
    }


}
