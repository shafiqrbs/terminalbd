<?php

namespace Appstore\Bundle\RestaurantBundle\Form;

use Appstore\Bundle\DomainUserBundle\Form\CustomerForHospitalType;
use Appstore\Bundle\DomainUserBundle\Form\CustomerType;
use Appstore\Bundle\DomainUserBundle\Form\RestaurantCustomerType;
use Appstore\Bundle\HospitalBundle\Entity\Category;
use Appstore\Bundle\HospitalBundle\Entity\HmsCategory;
use Appstore\Bundle\HospitalBundle\Repository\CategoryRepository;
use Appstore\Bundle\HospitalBundle\Repository\HmsCategoryRepository;
use Appstore\Bundle\RestaurantBundle\Repository\ParticularRepository;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\LocationBundle\Repository\LocationRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class TableInvoiceType extends AbstractType
{

    /** @var  GlobalOption */
    private $globalOption;


    function __construct(GlobalOption $globalOption )
    {
        $this->globalOption  = $globalOption;
        $this->config  = $globalOption->getRestaurantConfig()->getId();
    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('payment','text', array('attr'=>array('class'=>'m-wrap span12 payment tooltips','data-trigger' => 'hover','placeholder'=>'Receive BDT','data-original-title'=>'Add receive amount','autocomplete'=>'off')))
            ->add('cardNo','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Add payment card no','data-original-title'=>'Add payment card no','autocomplete'=>'off')))
            ->add('transactionId','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Add payment transaction id','data-original-title'=>'Add payment transaction id','autocomplete'=>'off')))
            ->add('paymentMobile','text', array('attr'=>array('class'=>'m-wrap span12 mobile','placeholder'=>'Add payment mobile no','data-original-title'=>'Add payment mobile no','autocomplete'=>'off')))

            ->add('discount','hidden',array('attr'=>array('class'=>'discount')))
            ->add('discountType', 'choice', array(
                'attr'=>array('class'=>'span12 m-wrap invoice-input'),
                'choices'   => array('flat' => 'Flat', 'percentage' => 'Percentage'),
                'required'  => true,
            ))
            ->add('discountCalculation','text', array('attr'=>array('class'=>'tooltips span12 m-wrap invoice-input ','data-trigger' => 'hover','placeholder'=>'Discount','data-original-title'=>'Enter discount amount','autocomplete'=>'off')))
            ->add('discountCoupon','text', array('attr'=>array('class'=>'tooltips span12 m-wrap discountCoupon invoice-input','data-trigger' => 'hover','placeholder'=>'Coupon No','data-original-title'=>'Enter Discount Coupon No','autocomplete'=>'off')))
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

            ->add('invoiceMode', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\RestaurantBundle\Entity\Particular',
                'property' => 'name',
                'attr'=>array('class'=>'span12 m-wrap invoice-input'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->join("e.service","s")
                        ->where("e.status = 1")
                        ->andWhere("s.slug = 'invoice-mode'")
                        ->orderBy("e.id","ASC");
                }
            ))

            ->add('accountBank', 'entity', array(
                'required'    => false,
                'class' => 'Appstore\Bundle\AccountingBundle\Entity\AccountBank',
                'property' => 'name',
                'attr'=>array('class'=>'span12 m-wrap'),
                'empty_value' => '---Choose Bank Account---',
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('b')
                        ->where("b.status = 1")
                        ->andWhere("b.globalOption =".$this->globalOption->getId())
                        ->orderBy("b.name", "ASC");
                }
            ))

            ->add('salesBy', 'entity', array(
                'required'    => true,
                'class' => 'Core\UserBundle\Entity\User',
                'property' => 'userFullName',
                'attr'=>array('class'=>'span12 m-wrap invoice-input'),
                'empty_value' => '---Order By---',
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('u')
                        ->where("u.isDelete != 1")
                        ->andWhere("u.enabled = 1")
                        ->andWhere("u.domainOwner = 2")
                        ->andWhere("u.globalOption =".$this->globalOption->getId())
                        ->orderBy("u.username", "ASC");
                }
            ))

            ->add('accountMobileBank', 'entity', array(
                'required'    => false,
                'class' => 'Appstore\Bundle\AccountingBundle\Entity\AccountMobileBank',
                'property' => 'name',
                'attr'=>array('class'=>'span12 m-wrap'),
                'empty_value' => '---Choose Mobile Account---',
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('b')
                        ->where("b.status = 1")
                        ->andWhere("b.globalOption =".$this->globalOption->getId())
                        ->orderBy("b.name", "ASC");
                }
            ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\RestaurantBundle\Entity\RestaurantTableInvoice'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'restaurant_invoice';
    }

}
