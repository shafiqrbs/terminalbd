<?php

namespace Core\UserBundle\Form;

use Core\UserBundle\Entity\User;
use Core\UserBundle\Form\Type\ProfileType;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\LocationBundle\Repository\LocationRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Setting\Bundle\ToolBundle\Form\DesignationType;
use Setting\Bundle\ToolBundle\Form\InitialOptionType;
use Setting\Bundle\ToolBundle\Repository\DesignationRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;


class DomainEditProfileType extends AbstractType
{


    /** @var  GlobalOption */
    private $globalOption;

    /** @var  LocationRepository */
    private $location;

    /** @var  DesignationRepository */
    private $designation;

    function __construct(GlobalOption $globalOption, LocationRepository $location , DesignationRepository $designation)
    {
        $this->globalOption = $globalOption;
        $this->location = $location;
        $this->designation = $designation;
    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter your full name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),
                    new Length(array('max'=>200))
                )
            ))
            ->add('mobile','text', array('attr'=>array('class'=>'m-wrap span12 mobile','placeholder'=>'Enter mobile number', 'data-original-title' =>'Must be use personal mobile number.' , 'data-trigger' => 'hover'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required'))
                )
            ))
            ->add('address','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter address')))
            ->add('permanentAddress','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter permanent address')))
            ->add('designation', 'entity', array(
                'required'    => false,
                'empty_value' => '--- Select Designation ---',
                'attr'=>array('class'=>'select2 span12'),
                'class' => 'Setting\Bundle\ToolBundle\Entity\Designation',
                'constraints' =>array(
                    new NotBlank(array('message'=>'Select user designation'))
                ),
                'property' => 'name',
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('b')
                        ->orderBy("b.name", "ASC");
                },
            ))

            ->add('postalCode','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter your email address')))
            ->add('additionalPhone','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter your email address')))
            ->add('nid','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter national id card no')))
            ->add('dob','birthday', array('attr'=>array('class'=>'m-wrap span6')))
            ->add('about','textarea', array('attr'=>array('class'=>'m-wrap span12','rows'=>'8')))

            ->add('location', 'entity', array(
                'required'    => false,
                'empty_value' => '---Select Location---',
                'attr'=>array('class'=>'select2 span12'),
                'class' => 'Setting\Bundle\LocationBundle\Entity\Location',
                'constraints' =>array(
                    new NotBlank(array('message'=>'Select user location'))
                ),
                'choices'=> $this->LocationChoiceList(),
                'choices_as_values' => true,
                'choice_label' => 'nestedLabel',
            ))
            ->add('bloodGroup', 'choice', array(
                'attr'=>array('class'=>'m-wrap span6'),
                'choices' => array('A+' => 'A+',  'A-' => 'A-','B+' => 'B+',  'B-' => 'B-',  'O+' => 'O+',  'O-' => 'O-',  'AB+' => 'AB+',  'AB-' => 'AB-'),

            ))
            ->add('bank', 'entity', array(
                'required'    => true,
                'class' => 'Setting\Bundle\ToolBundle\Entity\Bank',
                'empty_value' => '---Choose a bank---',
                'property' => 'name',
                'attr'=>array('class'=>'span12 select2'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('b')
                        ->orderBy("b.name", "ASC");
                },
            ))
            ->add('branch','textarea', array('attr'=>array('class'=>'m-wrap span12','rows'=>4 ,'draggable' => 'false' ,'placeholder'=>'Enter your bank branch name')))
            ->add('accountNo','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter your bank account no')))

            ->add('file');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Core\UserBundle\Entity\Profile'
        ));
    }

    public function getName()
    {
        return 'manage_profile';
    }

    protected function LocationChoiceList()
    {
        return $syndicateTree = $this->location->getLocationOptionGroup();

    }

    protected function DesignationChoiceList()
    {
        return $syndicateTree = $this->designation->getDesignationOptionGroup();

    }
}