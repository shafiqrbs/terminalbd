<?php

namespace Appstore\Bundle\HospitalBundle\Form;

use Appstore\Bundle\DomainUserBundle\Form\CustomerForHospitalType;
use Appstore\Bundle\DomainUserBundle\Form\CustomerType;
use Appstore\Bundle\HospitalBundle\Entity\Category;
use Appstore\Bundle\HospitalBundle\Entity\HmsCategory;
use Appstore\Bundle\HospitalBundle\Repository\CategoryRepository;
use Appstore\Bundle\HospitalBundle\Repository\HmsCategoryRepository;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\LocationBundle\Repository\LocationRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class InvoiceType extends AbstractType
{

    /** @var  LocationRepository */
    private $location;

    /** @var  HmsCategoryRepository */
    private $emCategory;

    /** @var  GlobalOption */
    private $globalOption;


    function __construct(GlobalOption $globalOption , HmsCategoryRepository $emCategory ,  LocationRepository $location)
    {
        $this->location         = $location;
        $this->emCategory       = $emCategory;
        $this->globalOption     = $globalOption;
    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('cardNo','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Add payment card no','data-original-title'=>'Add payment card no','autocomplete'=>'off')))
            ->add('transactionId','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Add payment transaction id','data-original-title'=>'Add payment transaction id','autocomplete'=>'off')))
            ->add('paymentMobile','text', array('attr'=>array('class'=>'m-wrap span12 mobile','placeholder'=>'Add payment mobile no','data-original-title'=>'Add payment mobile no','autocomplete'=>'off')))
            ->add('payment','text', array('attr'=>array('class'=>'tooltips payment span11 input2 m-wrap','data-trigger' => 'hover','placeholder'=>'Receive','data-original-title'=>'Enter received amount','autocomplete'=>'off'),
            ))
            ->add('discountCalculation','text', array('attr'=>array('class'=>'tooltips initialDiscount span11 input2 m-wrap','data-trigger' => 'hover','placeholder'=>'Discount','data-original-title'=>'Enter discount amount','autocomplete'=>'off'),
            ))
            ->add('discount','hidden')
            ->add('comment','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Add remarks','autocomplete'=>'off')))
            ->add('referredDoctor', 'entity', array(
                  'required'    => true,
                  'property' => 'referred',
                  'empty_value' => '--- Select Referred Doctor/Agent ---',
                  'attr'=>array('class'=>'m-wrap span12 select2'),
                  'class' => 'Appstore\Bundle\HospitalBundle\Entity\Particular',
                  'query_builder' => function(EntityRepository $er){
                      return $er->createQueryBuilder('e')
                          ->where('e.hospitalConfig ='.$this->globalOption->getHospitalConfig()->getId())
                          ->andWhere("e.service = 6")
                          ->andWhere("e.status = 1")
                          ->orderBy("e.name","ASC");
                  }

            ))
            ->add('printFor', 'choice', array(
                'attr'=>array('class'=>'span12 m-wrap'),
                'expanded'      =>false,
                'multiple'      =>false,
                'choices' => array(
                    'diagnostic' => 'Diagnostic',
                    'visit' => 'Visit',
                ),
            ))
            ->add('transactionMethod', 'entity', array(
                'required'    => true,
                'class' => 'Setting\Bundle\ToolBundle\Entity\TransactionMethod',
                'property' => 'name',
                'attr'=>array('class'=>'span12 m-wrap transactionMethod'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->andWhere("e.slug != 'cash-on-delivery'")
                        ->orderBy("e.id","ASC");
                }
            ))
            ->add('paymentCard', 'entity', array(
                'required'    => false,
                'property' => 'name',
                'class' => 'Setting\Bundle\ToolBundle\Entity\PaymentCard',
                'attr'=>array('class'=>'span12 m-wrap'),
                'empty_value' => '---Choose payment card---',
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
                'attr'=>array('class'=>'span12 m-wrap'),
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
                'attr'=>array('class'=>'span12 m-wrap'),
                'empty_value' => '---Choose receive mobile bank account---',
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('b')
                        ->where("b.status = 1")
                        ->andWhere("b.globalOption =".$this->globalOption->getId())
                        ->orderBy("b.name", "ASC");
                }
            ))
        ;
        $builder->add('referredDoctor', new InvoiceReferredDoctorType());
        $builder->add('customer', new CustomerForHospitalType());
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\HospitalBundle\Entity\Invoice'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appstore_bundle_hospitalbundle_invoice';
    }

    /**
     * @return mixed
     */
    protected function PathologyChoiceList()
    {
        return $this->emCategory->getParentCategoryTree($parent = 2 /** Pathology */ );

    }

    /**
     * @return mixed
     */
    protected function DepartmentChoiceList()
    {
        return $this->emCategory->getParentCategoryTree($parent = 7 /** Department */);

    }


}
