<?php

namespace Core\UserBundle\Form;

use Core\UserBundle\Form\Type\ProfileType;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\LocationBundle\Repository\LocationRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Setting\Bundle\ToolBundle\Form\InitialOptionType;
use Setting\Bundle\ToolBundle\Repository\DesignationRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;


class DomainProfileType extends AbstractType
{

    /** @var  GlobalOption */
    private $globalOption;

    /** @var  LocationRepository */
    private $location;

    /** @var  DesignationRepository */
    private $designation;


    function __construct(GlobalOption $globalOption, LocationRepository $location, DesignationRepository $designation)
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
                    new NotBlank(array('message'=>'Please input user full name')),
                    new Length(array('max'=>200))
                )
            ))
            ->add('mobile','text', array('attr'=>array('class'=>'m-wrap span12 mobile','placeholder'=>'Enter mobile number', 'data-original-title' =>'Must be use personal mobile number.' , 'data-trigger' => 'hover'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input user mobile no'))
                )
            ))
            ->add('joiningDate', 'date', array(
                'widget' => 'single_text',
                'placeholder' => array(
                    'mm' => 'mm', 'dd' => 'dd','YY' => 'YY'
                ),
                'format' => 'dd-MM-yyyy',
                'attr' => array('class'=>'m-wrap span12 datePicker'),
                'view_timezone' => 'Asia/Dhaka'
            ))
            ->add('address','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter address')))
            ->add('designation', 'entity', array(
                'required'    => true,
                'class' => 'Setting\Bundle\ToolBundle\Entity\Designation',
                'empty_value' => '---Choose a designation---',
                'property' => 'name',
                'constraints' =>array(
                    new NotBlank(array('message'=>'Select user designation'))
                ),
                'attr'=>array('class'=>'span12 select2'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status =1")
                        ->orderBy("e.name", "ASC");
                },
            ))

            ->add('nid','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter national id card no')))
            ->add('branches', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\DomainUserBundle\Entity\Branches',
                'empty_value' => '---Choose a branch---',
                'property' => 'name',
                'attr'=>array('class'=>'span12 select2'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.globalOption =".$this->globalOption->getId())
                        ->orderBy("e.name", "ASC");
                },
            ))
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
        return $syndicateTree = $this->location->getLocationOptionGroup();

    }
}