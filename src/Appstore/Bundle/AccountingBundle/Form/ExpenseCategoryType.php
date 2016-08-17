<?php

namespace Appstore\Bundle\AccountingBundle\Form;

use Appstore\Bundle\AccountingBundle\Repository\ExpenseCategoryRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class ExpenseCategoryType extends AbstractType
{

    /** @var  ExpenseCategoryRepository */
    private $em;

    /** @var  GlobalOption */
    private $globalOption;


    function __construct(ExpenseCategoryRepository $em, GlobalOption $globalOption)
    {
        $this->em = $em;
        $this->globalOption = $globalOption;
    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter expense category name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),

                )
            ))
            ->add('parent', 'entity', array(
                'required'    => true,
                'empty_value' => '---Select parent expense category---',
                'attr'=>array('class'=>'ExpenseCategory m-wrap span12 select2'),
                'class' => 'Appstore\Bundle\AccountingBundle\Entity\ExpenseCategory',
                'property' => 'nestedLabel',
                'choices'=> $this->ExpenseCategoryChoiceList()
            ))
            ->add('status')


        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\AccountingBundle\Entity\ExpenseCategory'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'product_bundle_accountingbundle_expensecategory';
    }

    /**
     * @return mixed
     */
    protected function ExpenseCategoryChoiceList()
    {
        return $ExpenseCategoryTree = $this->em->getFlatExpenseCategoryTree($this->globalOption);

    }


}
