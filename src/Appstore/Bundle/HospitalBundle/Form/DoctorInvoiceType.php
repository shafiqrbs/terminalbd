<?php

namespace Appstore\Bundle\HospitalBundle\Form;

use Doctrine\ORM\EntityRepository;
use Setting\Bundle\LocationBundle\Repository\LocationRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class DoctorInvoiceType extends AbstractType
{

    /** @var  GlobalOption */
    private $globalOption;


    function __construct(GlobalOption $globalOption)
    {
        $this->globalOption     = $globalOption;
    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('paymentMobile','text', array('attr'=>array('class'=>'m-wrap span12 mobile','placeholder'=>'Add payment mobile no','data-original-title'=>'Add payment mobile no','autocomplete'=>'off')))
            ->add('payment','text', array('attr'=>array('class'=>'tooltips payment','data-trigger' => 'hover','placeholder'=>'Receive amount','data-original-title'=>'Enter received amount','autocomplete'=>'off'),
            ))
            ->add('comment','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Add remarks','autocomplete'=>'off')))
            ->add('referredDoctor', 'entity', array(
                  'required'    => true,
                  'property' => 'referred',
                  'empty_value' => '--- Select Referred Doctor/Agent ---',
                  'attr'=>array('class'=>'m-wrap span12 select2'),
                  'class' => 'Appstore\Bundle\HospitalBundle\Entity\Particular',
                  'query_builder' => function(EntityRepository $er){
                      return $er->createQueryBuilder('e')
                          ->where("e.service = 6")
                          ->orderBy("e.name","ASC");
                  }

            ))
            ->add('transactionMethod', 'entity', array(
                'required'    => true,
                'class' => 'Setting\Bundle\ToolBundle\Entity\TransactionMethod',
                'property' => 'name',
                'attr'=>array('class'=>'span12 m-wrap transactionMethod'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->orderBy("e.id","ASC");
                }
            ))
            ->add('accountBank', 'entity', array(
                'required'    => false,
                'class' => 'Appstore\Bundle\AccountingBundle\Entity\AccountBank',
                'property' => 'name',
                'attr'=>array('class'=>'span12 select2'),
                'empty_value' => '---Choose receive bank account---',
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('b')
                        ->where("b.status = 1")
                        ->andWhere("b.globalOption =".$this->globalOption->getId())
                        ->orderBy("b.name", "ASC");
                }
            ))

            ->add('accountMobileBank', 'entity', array(
                'required'    => false,
                'class' => 'Appstore\Bundle\AccountingBundle\Entity\AccountMobileBank',
                'property' => 'name',
                'attr'=>array('class'=>'span12 select2'),
                'empty_value' => '---Choose receive mobile bank account---',
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('b')
                        ->where("b.status = 1")
                        ->andWhere("b.globalOption =".$this->globalOption->getId())
                        ->orderBy("b.name", "ASC");
                }
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\HospitalBundle\Entity\DoctorInvoice'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appstore_bundle_hospitalbundle_doctor_invoice';
    }
}
