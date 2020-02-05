<?php

namespace Appstore\Bundle\AssetsBundle\Form;


use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class PurchaseType extends AbstractType
{



    /** @var  $option GlobalOption */

    public  $option;


    public function __construct(GlobalOption $option)
    {
        $this->option = $option;

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
                'class' => 'Appstore\Bundle\AccountingBundle\Entity\AccountVendor',
                'empty_value' => '---Choose a vendor ---',
                'property' => 'companyName',
                'attr'=>array('class'=>'span12 m-wrap vendor'),
                'constraints' =>array( new NotBlank(array('message'=>'Please select your vendor name')) ),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->andWhere("e.globalOption =".$this->option->getId());
                },
            ))
            ->add('process', 'choice', array(
                'attr'=>array('class'=>'span6 m-wrap'),
                'expanded'      =>false,
                'required'    => true,
                'multiple'      =>false,
                'choices' => array(
                    'Hold' => 'Hold',
                    'In-progress' => 'In-progress',
                    'Done' => 'Done',
                ),
            ))

            ->add('transactionMethod', 'entity', array(
                'required'    => true,
                'class' => 'Setting\Bundle\ToolBundle\Entity\TransactionMethod',
                'property' => 'name',
                'empty_value' => '---Choose a Transaction---',
                'attr'=>array('class'=>'span12 transactionMethod'),
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
                'attr'=>array('class'=>'span12 m-wrap'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('b')
                        ->where("b.status = 1")
                        ->andWhere("b.globalOption =".$this->option->getId())
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
                        ->andWhere("b.globalOption =".$this->option->getId())
                        ->orderBy("b.name", "ASC");
                },
            ))
            ->add('challanNo','text', array('attr'=>array('class'=>'m-wrap span12 challan inputs','placeholder'=>'Enter challan no')))
            ->add('receiveDate','date', array('attr'=>array('class'=>'m-wrap span12 inputs','placeholder'=>'Enter receive date')))
            ->add('netTotal','text', array('attr'=>array('class'=>'m-wrap span12 inputs netTotal','readonly'=>'readonly','placeholder'=>'Enter total amount')))
            ->add('remark','textarea', array('attr'=>array('class'=>'m-wrap span12','rows'=>3,'placeholder'=>'Enter narration')))
            ->add('discount','text', array('attr'=>array('class'=>'m-wrap span12 inputs discount','placeholder'=>'Enter discount amount')))
            ->add('payment','text', array('attr'=>array('class'=>'m-wrap span12 numeric','placeholder'=>'Payment amount','autocomplete'=>'off')
            ));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\AssetsBundle\Entity\Purchase'
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
