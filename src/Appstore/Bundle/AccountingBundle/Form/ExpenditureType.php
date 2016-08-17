<?php

namespace Appstore\Bundle\AccountingBundle\Form;

use Appstore\Bundle\AccountingBundle\Repository\ExpenseCategoryRepository;
use Appstore\Bundle\InventoryBundle\Entity\InventoryConfig;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class ExpenditureType extends AbstractType
{

    /** @var  InventoryConfig */

    public  $inventoryConfig;

    /** @var  ExpenseCategoryRepository */

    private $expenseCategoryRepository;

    public function __construct(GlobalOption $globalOption, ExpenseCategoryRepository $expenseCategoryRepository )
    {
        $this->globalOption = $globalOption;
        $this->expenseCategoryRepository = $expenseCategoryRepository;

    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('amount','text', array('attr'=>array('class'=>'m-wrap span12 numeric','placeholder'=>'payment amount'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please add payment amount BDT'))
                )))
            ->add('remark','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Remark...')))
            ->add('bank', 'entity', array(
                'required'    => true,
                'class' => 'Setting\Bundle\ToolBundle\Entity\Bank',
                'empty_value' => '---Choose a bank---',
                'property' => 'name',
                'attr'=>array('class'=>'span12'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('b')
                        ->orderBy("b.name", "ASC");
                },
            ))
            ->add('paymentMethod', 'choice', array(
                'attr'=>array('class'=>'span12 select2 paymentMethod'),
                'choices' => array(
                    'Cash' => 'Cash',
                    'Cheque' => 'Cheque',
                ),
            ))
            ->add('accountNo','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'add your account no')))
            ->add('accountHead', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\AccountingBundle\Entity\AccountHead',
                'empty_value' => '---Choose a account head---',
                'property' => 'name',
                'attr'=>array('class'=>'span12 select2'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required'))
                ),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.parent = 23")
                        ->andWhere("e.status = 1");
                },
            ))
            ->add('expenseCategory', 'entity', array(
                'required'    => true,
                'empty_value' => '---Select expense category---',
                'attr'=>array('class'=>'ExpenseCategory m-wrap span12 select2'),
                'class' => 'Appstore\Bundle\AccountingBundle\Entity\ExpenseCategory',
                'property' => 'nestedLabel',
                'choices'=> $this->ExpenseCategoryChoiceList()
            ))
            ->add('toUser', 'entity', array(
                'required'    => true,
                'class' => 'Core\UserBundle\Entity\User',
                'empty_value' => '---Choose a user---',
                'property' => 'username',
                'attr'=>array('class'=>'span12 select2'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required'))
                ),

                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.globalOption =".$this->globalOption->getId());
                },
            ));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\AccountingBundle\Entity\Expenditure'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appstore_bundle_accountingbundle_expenditure';
    }

    /**
     * @return mixed
     */
    protected function ExpenseCategoryChoiceList()
    {
        return $ExpenseCategoryTree = $this->expenseCategoryRepository->getFlatExpenseCategoryTree($this->globalOption);

    }



}
