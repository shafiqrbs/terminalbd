<?php

namespace Appstore\Bundle\AccountingBundle\Form;

use Core\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class PaymentSalaryType extends AbstractType
{

    public  $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('paidAmount','text', array('attr'=>array('class'=>'m-wrap span12 numeric','placeholder'=>'Pay amount')))
            ->add('otherAmount','text', array('attr'=>array('class'=>'m-wrap span12 numeric','placeholder'=>'Other amount')))
            ->add('totalAmount','hidden')
            ->add('sendBank')
            ->add('remark','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Remark')))
            ->add('salaryMonth', 'choice', array(
                'attr'=>array('class'=>'span12'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please choose required'))
                ),
                'choices' => array(
                    'January'       => 'January',
                    'February'      => 'February',
                    'March'         => 'March',
                    'April'         => 'April',
                    'May'           => 'May',
                    'June'          => 'June',
                    'July'          => 'July',
                    'August'        => 'August',
                    'September'     => 'September',
                    'October'       => 'October',
                    'November'      => 'November',
                    'December'      => 'December',
                ),
            ))
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
                'attr'=>array('class'=>'span12'),
                'choices' => array(
                    'Cash' => 'Cash',
                    'Cheque' => 'Cheque',
                ),
            ))
            ->add('accountNo','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'add your account no')))
            ->add('salarySetting', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\AccountingBundle\Entity\SalarySetting',
                'empty_value' => '---Choose a payment amount---',
                'property' => 'salaryInfo',
                'attr'=>array('class'=>'span12'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required'))
                ),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.user  =".$this->user->getId());
                },
            ));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\AccountingBundle\Entity\PaymentSalary'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appstore_bundle_accountingBundle_paymentsalary';
    }
}
