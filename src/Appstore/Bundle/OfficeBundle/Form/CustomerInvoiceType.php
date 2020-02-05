<?php

namespace Appstore\Bundle\OfficeBundle\Form;

use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Setting\Bundle\ToolBundle\Event\Glo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class CustomerInvoiceType extends AbstractType
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
            ->add('discount','text', array('attr'=>array('class'=>'m-wrap span12 numeric','placeholder'=>'Add Received Amount BDT'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Add payment amount'))
                )))
            ->add('discount','text', array('attr'=>array('class'=>'m-wrap span12 numeric','placeholder'=>'Add Received Amount BDT'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Add payment amount'))
                )))
            ->add('remark','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Add Remark')))
            ->add('transactionMethod', 'entity', array(
                'required'    => true,
                'class' => 'Setting\Bundle\ToolBundle\Entity\TransactionMethod',
                'empty_value' => '---Choose a Transaction Method---',
                'property' => 'name',
                'attr'=>array('class'=>'span12 select2'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required'))
                ),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->orderBy("e.id");
                }
            ))
            ->add('customerName','text', array('attr'=>array('class'=>'m-wrap span12 numeric','placeholder'=>'Add Received Amount BDT'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Add payment amount'))
                )))

            ->add('accountBank', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\AccountingBundle\Entity\AccountBank',
                'empty_value' => '---Choose a bank---',
                'property' => 'name',
                'attr'=>array('class'=>'span12 select2'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('b')
                        ->where("b.globalOption =".$this->globalOption->getId())
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
                        ->where("b.globalOption =".$this->globalOption->getId())
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
            'data_class' => 'Appstore\Bundle\OfficeBundle\Entity\CustomerInvoice'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appstore_bundle_officebundle_customerinvoice';
    }
}
