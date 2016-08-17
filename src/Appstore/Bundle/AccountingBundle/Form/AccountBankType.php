<?php

namespace Appstore\Bundle\AccountingBundle\Form;

use Appstore\Bundle\InventoryBundle\Entity\InventoryConfig;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class AccountBankType extends AbstractType
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

            ->add('amount','text', array('attr'=>array('class'=>'m-wrap span12 numeric','placeholder'=>'add payment amount BDT'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please add payment amount BDT'))
                )))

            ->add('accountNo','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'add your account no'),
                 'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required'))
                ),
            ))
            ->add('remark','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'remark')))
            ->add('paymentMethod', 'choice', array(
                'attr'=>array('class'=>'span12 select2'),
                'choices' => array(
                    'Cash' => 'Cash',
                    'Cheque' => 'Cheque',
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
                        ->where("e.parent = 1 ");
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
                        ->where("e.globalOption =".$this->globalOption->getId());
                },
            ))
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
            ));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\AccountingBundle\Entity\AccountBank'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appstore_bundle_accountingbundle_AccountBank';
    }


}
