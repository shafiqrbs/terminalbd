<?php

namespace Appstore\Bundle\DmsBundle\Form;

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

            ->add('comment','textarea', array('attr'=>array('class'=>'m-wrap span12','rows'=>3,'placeholder'=>'Add patient advise','autocomplete'=>'off')))
            ->add('chiefComplains','textarea', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Add remarks','autocomplete'=>'off')))
            ->add('presentingComplains','textarea', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Add remarks','autocomplete'=>'off')))
            ->add('drugHistory','textarea', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Add remarks','autocomplete'=>'off')))
            ->add('diagnosis','textarea', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Add remarks','autocomplete'=>'off')))
            ->add('process', 'choice', array(
                'attr'=>array('class'=>'span6 select-custom'),
                'expanded'      =>false,
                'multiple'      =>false,
                'empty_value' => '---Choose process---',
                'choices' => array(
                    'Created' => 'Created',
                    'Appointment' => 'Appointment',
                    'Visit' => 'Visit',
                    'Done' => 'Done',
                ),
            ))

            ->add('investigations', 'entity', array(
                'required'    => false,
                'class' => 'Appstore\Bundle\DmsBundle\Entity\DmsParticular',
                'property' => 'name',
                'multiple'    => true,
                'expanded' => true,
                'attr'=>array('class'=>'m-wrap check-list'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->join("e.service",'s')
                        ->where("e.status = 1")
                        ->andWhere('s.slug IN (:slugs)')
                        ->setParameter('slugs',array('investigation'))
                        ->orderBy("e.name","ASC");
                }
            ));
           $builder->add('customer', new CustomerForHospitalType( $this->location ));
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
