<?php

namespace Appstore\Bundle\BusinessBundle\Form;

use Appstore\Bundle\DomainUserBundle\Form\CustomerForBusinessType;
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

    /** @var  GlobalOption */
    private $globalOption;


    function __construct(GlobalOption $globalOption ,  LocationRepository $location)
    {
        $this->location         = $location;
        $this->globalOption     = $globalOption;
    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('discount','text', array('attr'=>array('class'=>'m-wrap span12 discount','placeholder'=>'Discount amount')))
            ->add('payment','text', array('attr'=>array('class'=>'m-wrap span12 discount','placeholder'=>'Payment amount')))
            ->add('process', 'choice', array(
                'attr'=>array('class'=>'m-wrap invoiceProcess select-custom'),
                'expanded'      =>false,
                'multiple'      =>false,
                'empty_value' => '---Choose process---',
                'choices' => array(
                    'Created' => 'Created',
                    'In-progress' => 'In-progress',
                    'Delivered' => 'Delivered',
                    'Done' => 'Done',
                    'Canceled' => 'Canceled',
                ),
            ));
           $builder->add('customer', new CustomerForBusinessType( $this->location ));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\BusinessBundle\Entity\BusinessInvoice'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appstore_bundle_business_invoice';
    }

}
