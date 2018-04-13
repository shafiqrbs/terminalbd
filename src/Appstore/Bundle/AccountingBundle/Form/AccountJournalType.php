<?php

namespace Appstore\Bundle\AccountingBundle\Form;

use Appstore\Bundle\InventoryBundle\Entity\InventoryConfig;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class AccountJournalType extends AbstractType
{

    public  $globalOption;

    public function __construct(GlobalOption $globalOption)
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

            ->add('amount','text', array('attr'=>array('class'=>'m-wrap span12 numeric','placeholder'=>'Add amount'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please add amount'))
                )))
            ->add('remark','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'')))
            ->add('transactionType', 'choice', array(
                'attr'=>array('class'=>'span12 m-wrap'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please choose required'))
                ),
                'choices' => array(
                    'Debit' => 'Debit',
                    'Credit' => 'Credit',
                ),
            ))
            ->add('transactionMethod', 'entity', array(
                'required'    => true,
                'class' => 'Setting\Bundle\ToolBundle\Entity\TransactionMethod',
                'empty_value' => '---Choose a transaction method---',
                'property' => 'name',
                'attr'=>array('class'=>'span12 m-wrap transactionMethod'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required'))
                ),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->andWhere("e.slug != 'cash-on-delivery'")
                        ->orderBy("e.id");
                }
            ))

            ->add('accountHeadDebit', 'entity', [
                'class'     => 'Appstore\Bundle\AccountingBundle\Entity\AccountHead',
                'group_by'  => 'parent.name',
                'property'  => 'name',
                'attr'=>array('class'=>'span12 m-wrap'),
                'choice_translation_domain' => true,
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->join("e.parent",'c')
                        ->where("e.toIncrease = 'Debit'")
                        ->orderBy("e.name", "ASC");
                }
            ])

            ->add('accountHeadCredit', 'entity', [
                'class'     => 'Appstore\Bundle\AccountingBundle\Entity\AccountHead',
                'group_by'  => 'parent.name',
                'property'  => 'name',
                'attr'=>array('class'=>'span12 m-wrap'),
                'choice_translation_domain' => true,
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->join("e.parent",'c')
                        ->where("e.toIncrease = 'Credit'")
                        ->orderBy("e.name", "ASC");
                }
            ])

            ->add('accountBank', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\AccountingBundle\Entity\AccountBank',
                'empty_value' => '---Choose a bank---',
                'property' => 'name',
                'attr'=>array('class'=>'span12 m-wrap'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->andWhere("e.globalOption =".$this->globalOption->getId());
                },
            ))

            ->add('accountMobileBank', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\AccountingBundle\Entity\AccountMobileBank',
                'empty_value' => '---Choose a mobile account---',
                'property' => 'name',
                'attr'=>array('class'=>'span12 m-wrap'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->andWhere("e.globalOption =".$this->globalOption->getId());
                },
            ))

            ->add('toUser', 'entity', array(
                'required'    => true,
                'class' => 'Core\UserBundle\Entity\User',
                'empty_value' => '--- Select a user name ---',
                'property' => 'username',
                'attr'=>array('class'=>'span12 m-wrap'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where('e.isDelete !=  :delete')
                        ->setParameter('delete', 1)
                        ->andWhere("e.globalOption =".$this->globalOption->getId());
                },
            ));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\AccountingBundle\Entity\AccountJournal'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appstore_bundle_accountingbundle_AccountJournal';
    }


}
