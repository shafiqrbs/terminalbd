<?php

namespace Appstore\Bundle\AccountingBundle\Form;

use Appstore\Bundle\InventoryBundle\Entity\InventoryConfig;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class AccountPurchaseType extends AbstractType
{

    public  $global;

    public function __construct(GlobalOption $global)
    {
        $this->global = $global;

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
	        ->add('processType', 'choice', array(
		        'attr'=>array('class'=>'span12 m-wrap'),
		        'expanded'      =>false,
		        'multiple'      =>false,
		        'choices' => array(
			        'Due' => 'Due',
			        'Advance' => 'Advance',
			        'Discount' => 'Discount',
			        'Outstanding' => 'Outstanding',
		        ),
	        ))

	        ->add('companyName','text', array('attr'=>array('class'=>'m-wrap span12 select2Vendor leftMargin','placeholder'=>'Enter vendor company name'),
              'constraints' =>array(
                  new NotBlank(array('message'=>'Please enter vendor company name'))
              )
	        ))
            ->add('accountBank', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\AccountingBundle\Entity\AccountBank',
                'empty_value' => '---Choose a bank---',
                'property' => 'name',
                'attr'=>array('class'=>'span12 m-wrap'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('b')
                        ->where("b.status = 1")
                        ->andWhere("b.globalOption =".$this->global->getId())
                        ->orderBy("b.name", "ASC");
                },
            ))
            ->add('accountMobileBank', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\AccountingBundle\Entity\AccountMobileBank',
                'empty_value' => '---Choose a mobile banking---',
                'property' => 'name',
                'attr'=>array('class'=>'span12 m-wrap'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('b')
	                    ->where("b.status = 1")
	                    ->andWhere("b.globalOption =".$this->global->getId())
                        ->orderBy("b.name", "ASC");
                },
            ))
            ->add('remark','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'')))

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
        return 'purchase';
    }


}
