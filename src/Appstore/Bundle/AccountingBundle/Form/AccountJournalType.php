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
                'attr'=>array('class'=>'span12 select2'),
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
                'attr'=>array('class'=>'span12 select2 transactionMethod'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required'))
                ),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->andWhere("e.slug != 'cash-on-delivery'")
                        ->orderBy("e.name");
                }
            ))

            ->add('accountHeadDebit', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\AccountingBundle\Entity\AccountHead',
                'empty_value' => '---Choose a debit head---',
                'property' => 'name',
                'attr'=>array('class'=>'span12 select2'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required'))
                ),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.toIncrease ='Debit'")
                        ->andWhere("e.parent > 0")
                        ->orderBy("e.name");
                }
            ))

            ->add('accountHeadCredit', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\AccountingBundle\Entity\AccountHead',
                'empty_value' => '---Choose a credit head---',
                'property' => 'name',
                'attr'=>array('class'=>'span12 select2'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required'))
                ),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.toIncrease = 'Credit'")
                        ->andWhere("e.parent > 0")
                        ->orderBy("e.name");
                }
            ))

            ->add('accountBank', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\AccountingBundle\Entity\AccountBank',
                'empty_value' => '---Choose a bank---',
                'property' => 'name',
                'attr'=>array('class'=>'span12 select2'),
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
                'attr'=>array('class'=>'span12 select2'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->andWhere("e.globalOption =".$this->globalOption->getId());
                },
            ))

            ->add('toUser', 'entity', array(
                'required'    => true,
                'class' => 'Core\UserBundle\Entity\User',
                'empty_value' => '---Choose a user---',
                'property' => 'username',
                'attr'=>array('class'=>'span12 select2'),
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
