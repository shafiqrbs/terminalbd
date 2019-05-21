<?php

namespace Appstore\Bundle\HumanResourceBundle\Form;


use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class EmployeePayrollType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder

            ->add('effectedMonth','date', array('attr'=>array('class'=>'m-wrap span12 datePicker','placeholder'=>'Enter effected month'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input effected month')),
                )
            ))
            ->add('basicAmount','text', array('attr'=>array('class'=>'m-wrap span12 datePicker','placeholder'=>'Enter basic amount'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input basic amount')),
                )
            ))
            ->add('loanAdjustMonth', 'date', array(
                'widget' => 'choice',
            ))
            ->add('bonusApplicable')
            ->add('accountNo','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter Account No')))
            ->add('bankAccountName','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter Bank Account Name')))
            ->add('branch','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Tax Number')))
            ->add('taxNumber','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Tax Number')))
            ->add('loanInstallment','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter Loan Installment')))
            ->add('bonusPercentage','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter bonus percentage')))
            ->add('bank', 'entity', array(
                'required'    => false,
                'class' => 'Setting\Bundle\ToolBundle\Entity\Bank',
                'property' => 'name',
                'attr'=>array('class'=>'span12 m-wrap'),
                'empty_value' => '---Select bank name---',
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->orderBy("e.name", "ASC");
                }
            ))
            ->add('remark','textarea', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter user remark')))
            ->add('mobileAccount','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter Mobile  Account')))
            ->add('mobileBanking', 'choice', array(
                'attr'=>array('class'=>'m-wrap span12'),
                'expanded'      =>false,
                'multiple'      =>false,
                'choices' => array(
                    'Bkash' => 'Bkash',
                    'Rocket' => 'Rocket',
                    'Nagod' => 'Nagod',
                ),
            ))
             ->add('salaryType', 'choice', array(
                'attr'=>array('class'=>'m-wrap span12'),
                'expanded'      =>false,
                'multiple'      =>false,
                'choices' => array(
                    'fixed' => 'Fixed',
                    'honourable' => 'Honourable',
                    'day' => 'Day',
                    'hour' => 'Hour',
                ),
            ))
            ->add('paymentMethod', 'choice', array(
                'attr'=>array('class'=>'m-wrap span12'),
                'expanded'      =>false,
                'multiple'      =>false,
                'choices' => array(
                    'Cash' => 'Cash in Hand',
                    'Bank' => 'Bank',
                    'Mobile Banking' => 'Mobile Banking',

                ),
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\HumanResourceBundle\Entity\EmployeePayroll'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'employeePayroll';
    }
}
