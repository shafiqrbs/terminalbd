<?php

namespace Appstore\Bundle\DmsBundle\Form;

use Appstore\Bundle\DomainUserBundle\Form\CustomerForDmsType;
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

          /*  ->add('drugHistory','textarea', array('attr'=>array('class'=>'m-wrap span12','rows'=> 8,'placeholder'=>'Enter patient drug history','autocomplete'=>'off')))
            ->add('diagnosis','textarea', array('attr'=>array('class'=>'m-wrap span12','rows'=> 8,'placeholder'=>'Enter diagnosis details','autocomplete'=>'off')))
          */
          ->add('process', 'choice', array(
                'attr'=>array('class'=>'span4 select-custom'),
                'expanded'      =>false,
                'multiple'      =>false,
                'empty_value' => '---Choose process---',
                'choices' => array(
                    'Created' => 'Created',
                    'Appointment' => 'Appointment',
                    'Visit' => 'Visit',
                    'Done' => 'Done',
                    'Canceled' => 'Canceled',
                ),
            ))

/*
            ->add('investigations', 'entity', array(
                'required'    => false,
                'class' => 'Appstore\Bundle\DmsBundle\Entity\DmsParticular',
                'property' => 'name',
                'multiple'    => true,
                'attr'=>array('class'=>'m-wrap span12 multiselect'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->join("e.service",'s')
                        ->where("e.status = 1")
                        ->andWhere('s.slug IN (:slugs)')
                        ->setParameter('slugs',array('investigation'))
                        ->orderBy("e.name","ASC");
                }
            ))*/

            /*->add('specialAdvises', 'entity', array(
                'required'    => false,
                'class' => 'Appstore\Bundle\DmsBundle\Entity\DmsSpecialAdvise',
                'property' => 'name',
                'multiple'    => true,
                'expanded' => true,
                'attr'=>array('class'=>'m-wrap check-list'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->orderBy("e.name","ASC");
                }
            ))*/
            ->add('assignDoctor', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\DmsBundle\Entity\DmsParticular',
                'property' => 'name',
                'multiple'    => false,
                'expanded' => false,
                'attr'=>array('class'=>'m-wrap span6'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->join("e.service",'s')
                        ->where("e.status = 1")
                        ->andWhere('e.dmsConfig =:dmsConfig')
                        ->setParameter('dmsConfig', $this->globalOption->getDmsConfig()->getId())
                        ->andWhere('s.slug IN (:slugs)')
                        ->setParameter('slugs',array('doctor'))
                        ->orderBy("e.name","ASC");
                }
            ));
           $builder->add('customer', new CustomerForDmsType( $this->location ));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\DmsBundle\Entity\DmsInvoice'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appstore_bundle_dmsbundle_invoice';
    }

}
