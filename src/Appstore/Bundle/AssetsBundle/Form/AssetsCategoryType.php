<?php

namespace Appstore\Bundle\AssetsBundle\Form;

use Appstore\Bundle\AccountingBundle\Repository\AccountHeadRepository;
use Appstore\Bundle\AssetsBundle\Repository\AssetsCategoryRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class AssetsCategoryType extends AbstractType
{

    /** @var AssetsCategoryRepository */
    private $em;

    /** @var  AccountHeadRepository */
    private $accountHead;

    /** @var  GlobalOption */
    private $globalOption;


    function __construct(AssetsCategoryRepository $em , AccountHeadRepository $accountHead, GlobalOption $globalOption)
    {
        $this->em = $em;
        $this->globalOption = $globalOption;
        $this->accountHead = $accountHead;
    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter category name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),

                )
            ))
            ->add('accountHead', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\AccountingBundle\Entity\AccountHead',
                'empty_value' => '---Choose a account head---',
                'property' => 'name',
                'attr'=>array('class'=>'span12  m-wrap'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required'))
                ),
                'choices'=> $this->ExpenseAccountChoiceList()
            ))

            ->add('parent', 'entity', array(
                'required'    => false,
                'empty_value' => '---Select Parent Category---',
                'attr'=>array('class'=>'m-wrap span12 AssetsCategory'),
                'class' => 'Appstore\Bundle\AssetsBundle\Entity\AssetsCategory',
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
            'data_class' => 'Appstore\Bundle\AssetsBundle\Entity\AssetsCategory'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'assetsCategory';
    }

    /**
     * @return mixed
     */
    protected function ExpenseCategoryChoiceList()
    {
        return $this->em->getFlatCategoryTree($this->globalOption);

    }

    /**
     * @return mixed
     */
    protected function ExpenseAccountChoiceList()
    {
        return $this->accountHead->getAccountHeadTrees();

    }


}
