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

            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12 patientName','autocomplete'=>'off','placeholder'=>'Enter patient name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Enter patient name')),
                )
            ))
            ->add('mobile','text', array('attr'=>array('class'=>'m-wrap span12 select2mobile','autocomplete'=>'off','placeholder'=>'Enter mobile no'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Enter patient mobile no')),
                )
            ))
            ->add('customerId','text', array('attr'=>array('class'=>'m-wrap span12 select2CustomerCode','autocomplete'=>'off','placeholder'=>'Enter customer code')))
            ->add('fatherName','text', array('attr'=>array('class'=>'m-wrap span12','autocomplete'=>'off','placeholder'=>'Enter father/spouse name'),

            ))
            ->add('motherName','text', array('attr'=>array('class'=>'m-wrap span12','autocomplete'=>'off','placeholder'=>'Enter mother name'),

            ))
            ->add('religion','text', array('attr'=>array('class'=>'m-wrap span12','autocomplete'=>'off','placeholder'=>'Enter religion'),

            ))
            ->add('profession','text', array('attr'=>array('class'=>'m-wrap span12','autocomplete'=>'off','placeholder'=>'Enter profession'),

            ))
            ->add('nationality','text', array('attr'=>array('class'=>'m-wrap span12','autocomplete'=>'off','placeholder'=>'Enter nationality'),

            ))
            ->add('dob','text', array('attr'=>array('class'=>'m-wrap span12 dateCalendar','placeholder'=>'Enter patient date of birth'),
                ))
            ->add('age','number', array('attr'=>array('class'=>'m-wrap span12 numeric patientAge','placeholder'=>'Enter patient age'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Enter patient age')),
            )))
            ->add('bloodGroup', 'choice', array(
                'attr'=>array('class'=>'m-wrap span12'),
                'empty_value' => '--- Select Blood Group ---',
                'choices' => array('A+' => 'A+',  'A-' => 'A-','B+' => 'B+',  'B-' => 'B-',  'O+' => 'O+',  'O-' => 'O-',  'AB+' => 'AB+',  'AB-' => 'AB-'),
            ))
            ->add('ageType', 'choice', array(
                'attr'=>array('class'=>'span12 select-custom ageType'),
                'expanded'      =>false,
                'multiple'      =>false,
                'constraints' =>array(
                    new NotBlank(array('message'=>'Enter patient age type')),
                ),
                'choices' => array('Years' => 'Years','Months' => 'Months','Day' => 'Day')
            ))
            ->add('gender', 'choice', array(
                'attr'=>array('class'=>'span12 select-custom gender'),
                'expanded'      =>false,
                'multiple'      =>false,
                'choices' => array('Female' => 'Female','Male' => 'Male', 'Others' => 'Others'),
            ))
            ->add('maritalStatus', 'choice', array(
                'attr'=>array('class'=>'span12 select-custom maritalStatus'),
                'expanded'      =>false,
                'multiple'      =>false,
                'choices' => array('Single' => 'Single','Married' => 'Married', 'Divorced' => 'Divorced','Widow'=>'Widow'),
            ))

            ->add('alternativeContactPerson','text', array('attr'=>array('class'=>'m-wrap span12 numeric patientAge','placeholder'=>'Enter guardian name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Enter guardian name')),
            )))

            ->add('alternativeContactPerson','text', array('attr'=>array('class'=>'m-wrap span12 alternativeContactPerson','placeholder'=>'Enter guardian name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Enter guardian name')),
            )))

            ->add('alternativeContactMobile','text', array('attr'=>array('class'=>'m-wrap span12 numeric mobile alternativeContactMobile','placeholder'=>'Enter guardian mobile no'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Enter guardian mobile no')),
            )))

            ->add('alternativeRelation','text', array('attr'=>array('class'=>'m-wrap span12  alternativeRelation','placeholder'=>'Enter Relationship with the Patient'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Enter Relationship with the Patient')),
            )))


            ->add('address','textarea', array('attr'=>array('class'=>'m-wrap span12 resize','rows'=> 4,'autocomplete'=>'off','placeholder'=>'Enter patient address')
            ))
            ->add('location', 'entity', array(
                'required'    => false,
                'empty_value' => '---Select Location---',
                'attr'=>array('class'=>'select2 span12 location'),
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
