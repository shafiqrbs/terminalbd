<?php

namespace Core\UserBundle\Form;

use Core\UserBundle\Form\Type\ProfileType;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\LocationBundle\Repository\LocationRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Setting\Bundle\ToolBundle\Form\InitialOptionType;
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


    function __construct(GlobalOption $globalOption, LocationRepository $location)
    {
        $this->globalOption = $globalOption;
        $this->location = $location;
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
            ->add('designation','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter designation')))
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
            ->add('branches', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\DomainUserBundle\Entity\Branches',
                'empty_value' => '---Choose a branch---',
                'property' => 'name',
                'attr'=>array('class'=>'span12 select2'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->andWhere("e.globalOption =".$this->globalOption->getId())
                        ->orderBy("e.name", "ASC");
                },
            ))
            ->add('bloodGroup', 'choice', array(
                'attr'=>array('class'=>'m-wrap span6'),
                'choices' => array('A+' => 'A+',  'A-' => 'A-','B+' => 'B+',  'B-' => 'B-',  'O+' => 'O+',  'O-' => 'O-',  'AB+' => 'AB+',  'AB-' => 'AB-'),

            ))
            ->add('file')

        ;
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
}