<?php

namespace Appstore\Bundle\DomainUserBundle\Form;

use Doctrine\ORM\EntityRepository;
use Setting\Bundle\LocationBundle\Repository\LocationRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class CustomerType extends AbstractType
{

    /** @var  LocationRepository */
    private $location;


    function __construct( LocationRepository $location)
    {
        $this->location = $location;
    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12','autocomplete'=>'off','placeholder'=>'Customer name'),
                    'constraints' =>array(
                        new NotBlank(array('message'=>'Please enter customer name'))
                    ))
            )
             ->add('facebookId','text', array('attr'=>array('class'=>'m-wrap span12','autocomplete'=>'off','placeholder'=>'Customer facebook ID'),
                    'constraints' =>array(
                        new NotBlank(array('message'=>'Please enter customer facebook ID'))
                    ))
            )
            ->add('mobile','text', array('attr'=>array('class'=>'m-wrap span12 mobile','autocomplete'=>'off','placeholder'=>'Mobile no'),
                    'constraints' =>array(
                        new NotBlank(array('message'=>'Please enter mobile no'))
                    ))
            )
            ->add('email','text', array('attr'=>array('class'=>'m-wrap span12 ','placeholder'=>'Email address')))
            ->add('address','textarea', array('attr'=>array('class'=>'m-wrap span12 ','rows'=>8,'placeholder'=>'Enter customer address'),
             'constraints' =>array( new NotBlank(array('message'=>'Please customer address')))
                )
            )
            ->add('location', 'entity', array(
                'required'    => false,
                'empty_value' => '---Select Location---',
                'attr'=>array('class'=>'select2 span12'),
                'class' => 'Setting\Bundle\LocationBundle\Entity\Location',
                'constraints' =>array(
                    new NotBlank(array('message'=>'Select customer location'))
                ),
                'choices'=> $this->LocationChoiceList(),
                'choices_as_values' => true,
                'choice_label' => 'nestedLabel',
            ))
            ->add('ageGroup', 'choice', array(
            'attr'=>array('class'=>'span12 select2'),
            'choices' => array(
                'Adult' => 'Adult',
                'Kids' => 'Kids',
            ),
            'required'    => true,
            'empty_data'  => null,
            ))
            ->add('gender', 'choice', array(
                'attr'=>array('class'=>'span12 select2'),
                'choices' => array(
                    'Male' => 'Male',
                    'Female' => 'Female'
                ),
            ));
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
        return $syndicateTree = $this->location->getLocationOptionGroup();

    }
}
