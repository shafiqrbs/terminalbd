<?php

namespace Appstore\Bundle\DomainUserBundle\Form;

use Doctrine\ORM\EntityRepository;
use Setting\Bundle\LocationBundle\Repository\LocationRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class CustomerForHospitalNewAdmissionType extends AbstractType
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

            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12 patientName','autocomplete'=>'off','placeholder'=>'patient name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Enter patient full name')),
                )
            ))
            ->add('mobile','text', array('attr'=>array('class'=>'m-wrap span12 select2mobile','autocomplete'=>'off','placeholder'=>'mobile no'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Enter patient mobile no')),
                )
            ))
            ->add('fatherName','text', array('attr'=>array('class'=>'m-wrap span12','autocomplete'=>'off','placeholder'=>'father/spouse name'),

            ))
            ->add('age','number', array('attr'=>array('class'=>'m-wrap span6 numeric patientAge','placeholder'=>'age'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'patient age')),
                )))
            ->add('ageType', 'choice', array(
                'attr'=>array('class'=>'m-wrap span6 select-custom ageType'),
                'expanded'      =>false,
                'multiple'      =>false,
                'constraints' =>array(
                    new NotBlank(array('message'=>'patient age type')),
                ),
                'choices' => array('Years' => 'Years','Months' => 'Months','Day' => 'Day')
            ))
            ->add('gender', 'choice', array(
                'attr'=>array('class'=>'span6 m-wrap select-custom gender'),
                'expanded'      =>false,
                'multiple'      =>false,
                'choices' => array('Female' => 'Female','Male' => 'Male', 'Others' => 'Others'),
            ))
            ->add('maritalStatus', 'choice', array(
                'attr'=>array('class'=>'span6 m-wrap select-custom maritalStatus'),
                'expanded'      =>false,
                'multiple'      =>false,
                'choices' => array('Single' => 'Single','Married' => 'Married', 'Divorced' => 'Divorced','Widow'=>'Widow'),
            ))

            ->add('bloodGroup', 'choice', array(
                'attr'=>array('class'=>'m-wrap span12'),
                'empty_value' => '--- Select Blood Group ---',
                'choices' => array('A+' => 'A+',  'A-' => 'A-','B+' => 'B+',  'B-' => 'B-',  'O+' => 'O+',  'O-' => 'O-',  'AB+' => 'AB+',  'AB-' => 'AB-'),
            ))

            ->add('nationality','text', array('attr'=>array('class'=>'m-wrap span12','autocomplete'=>'off','placeholder'=>'Nationality'),

            ))
            ->add('religion','text', array('attr'=>array('class'=>'m-wrap span12','autocomplete'=>'off','placeholder'=>'Religion'),

            ))
            ->add('address','text', array('attr'=>array('class'=>'m-wrap span12 resize','rows'=> 4,'autocomplete'=>'off','placeholder'=>'patient address')
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
        return 'customer';
    }

    protected function LocationChoiceList()
    {
        return $this->location->getLocationOptionGroup();

    }

}
