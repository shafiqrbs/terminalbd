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
                'constraints' =>array( new NotBlank(array('message'=>'Please select your vendor name')) ),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('wt')
                        ->where("wt.status = 1")
                        ->andWhere("wt.inventoryConfig =".$this->inventoryConfig->getId());
                },
            ))
            ->add('transactionMethod', 'entity', array(
                'required'    => true,
                'class' => 'Setting\Bundle\ToolBundle\Entity\TransactionMethod',
                'property' => 'name',
                'attr'=>array('class'=>'span12 select2 transactionMethod'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required'))
                ),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->orderBy("e.id","ASC");
                }
            ))

            ->add('accountBank', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\AccountingBundle\Entity\AccountBank',
                'empty_value' => '---Choose a bank---',
                'property' => 'name',
                'attr'=>array('class'=>'span12 select2'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('b')
                        ->where("b.status = 1")
                        ->andWhere("b.globalOption =".$this->inventoryConfig->getGlobalOption()->getId())
                        ->orderBy("b.name", "ASC");
                },
            ))
            ->add('accountMobileBank', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\AccountingBundle\Entity\AccountMobileBank',
                'empty_value' => '---Choose a mobile banking---',
                'property' => 'name',
                'attr'=>array('class'=>'span12 select2'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('b')
                        ->where("b.status = 1")
                        ->andWhere("b.globalOption =".$this->inventoryConfig->getGlobalOption()->getId())
                        ->orderBy("b.name", "ASC");
                },
            ))
            ->add('memo','text', array('attr'=>array('class'=>'m-wrap span12 ','required' => true ,'label' => 'form.name','placeholder'=>'Memo no'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please add  memo no'))
            )))
            /*->add('chalan','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'')))*/

            ->add('receiveDate', 'date', array(
                'widget' => 'single_text',
                'placeholder' => array(
                    'mm' => 'mm', 'dd' => 'dd','YY' => 'YY'

                ),
                'format' => 'dd-MM-yyyy',
                'attr' => array('class'=>'m-wrap span12 datePicker'),
                'view_timezone' => 'Asia/Dhaka'))

            ->add('totalAmount','text', array('attr'=>array('class'=>'m-wrap span12 numeric','placeholder'=>'Net total amount BDT'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please add total amount BDT'))
                )))
            ->add('paymentAmount','text', array('attr'=>array('class'=>'m-wrap span12 numeric','placeholder'=>'Net payment amount BDT'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please add payment amount BDT'))
                )))
            ->add('dueAmount','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Due amount BDT')))
            ->add('totalQnt','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'No of Qnt'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please add total qnt'))
                )))
            ->add('totalItem','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'No of item'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please add total item'))
                )))
            ->add('purchaseTo', 'choice', array(
                'required'    => false,
                'attr'=>array('class'=>'span12'),
                'empty_value' => '---Choose a purchase To ---',
                'choices' => array(
                    'International' => 'International',
                    'National' => 'National',
                    'In House' => 'In House'
                ),
            ))
            ->add('process', 'choice', array(
                'required'    => false,
                'attr'=>array('class'=>'span12'),
                'choices' => array(
                    'created' => 'Created',
                    'complete' => 'Complete',
                    'approved' => 'Approved',
                ),
            ))
            ->add('asInvestment')

            /*->add('advanceAmount','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'')))
            /*->add('advanceAmount','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'')))
            ->add('commissionAmount','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'')))
            ->add('vatAmount','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'')))
            ->add('taxAmount','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'')))
            ->add('totalQnt','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'')))
            ->add('totalItem','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'')))
            ->add('paymentType','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'')))
            ->add('paymentMethod','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'')))*/
            /*->add('file','file',array('attr'=>array('class'=>'default span12')))*/
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
