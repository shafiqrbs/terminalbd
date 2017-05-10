<?php

namespace Appstore\Bundle\DomainUserBundle\Form;

use Doctrine\ORM\EntityRepository;
use Setting\Bundle\LocationBundle\Repository\LocationRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class CustomerForHospitalAdmissionType extends AbstractType
{

    /** @var  LocationRepository */
    private $location;

    function __construct(LocationRepository $location)
    {
        $this->location         = $location;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12','autocomplete'=>'off','placeholder'=>'Enter patient name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Enter patient name')),
                )
            ))
             ->add('fatherName','text', array('attr'=>array('class'=>'m-wrap span12','autocomplete'=>'off','placeholder'=>'Enter father name'),

            ))
            ->add('motherName','text', array('attr'=>array('class'=>'m-wrap span12','autocomplete'=>'off','placeholder'=>'Enter mother name'),

            ))
            ->add('religion','text', array('attr'=>array('class'=>'m-wrap span12','autocomplete'=>'off','placeholder'=>'Enter religion'),

            ))
            ->add('profession','text', array('attr'=>array('class'=>'m-wrap span12','autocomplete'=>'off','placeholder'=>'Enter profession'),

            ))
            ->add('nationality','text', array('attr'=>array('class'=>'m-wrap span12','autocomplete'=>'off','placeholder'=>'Enter nationality'),

            ))
            ->add('mobile','text', array('attr'=>array('class'=>'m-wrap span12 mobile','autocomplete'=>'off','placeholder'=>'Enter patient mobile no'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Enter patient mobile no')),
                )
            ))
            ->add('age','number', array('attr'=>array('class'=>'m-wrap span12 numeric','placeholder'=>'Enter patient age'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Enter patient age')),
            )))
            ->add('bloodGroup', 'choice', array(
                'attr'=>array('class'=>'m-wrap span12'),
                'empty_value' => '--- Select Blood Group ---',
                'choices' => array('A+' => 'A+',  'A-' => 'A-','B+' => 'B+',  'B-' => 'B-',  'O+' => 'O+',  'O-' => 'O-',  'AB+' => 'AB+',  'AB-' => 'AB-'),
            ))
            ->add('ageType', 'choice', array(
                'attr'=>array('class'=>'span12 select-custom'),
                'expanded'      =>false,
                'multiple'      =>false,
                'constraints' =>array(
                    new NotBlank(array('message'=>'Enter patient age type')),
                ),
                'choices' => array('Years' => 'Years','Months' => 'Months','Day' => 'Day')
            ))
            ->add('gender', 'choice', array(
                'attr'=>array('class'=>'span12 select-custom'),
                'expanded'      =>false,
                'multiple'      =>false,
                'choices' => array('Female' => 'Female','Male' => 'Male', 'Others' => 'Others'),
            ))
            ->add('address','textarea', array('attr'=>array('class'=>'m-wrap span12 resize','rows'=> 4,'autocomplete'=>'off','placeholder'=>'Enter patient address')
            ))
            ->add('permanentAddress','textarea', array('attr'=>array('class'=>'m-wrap span12 resize','rows'=> 5,'autocomplete'=>'off','placeholder'=>'Enter patient permanent address')
            ))
            ->add('location', 'entity', array(
                'required'    => false,
                'empty_value' => '---Select Location---',
                'attr'=>array('class'=>'select2 span12'),
                'class' => 'Setting\Bundle\LocationBundle\Entity\Location',
                'choices'=> $this->LocationChoiceList(),
                'choices_as_values' => true,
                'choice_label' => 'nestedLabel',
            ))

        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\DomainUserBundle\Entity\Customer'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appstore_bundle_domainuserbundle_customer';
    }

    protected function LocationChoiceList()
    {
        return $this->location->getLocationOptionGroup();

    }

}
