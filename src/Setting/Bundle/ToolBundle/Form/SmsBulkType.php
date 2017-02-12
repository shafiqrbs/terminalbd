<?php

namespace Setting\Bundle\ToolBundle\Form;

use Setting\Bundle\LocationBundle\Repository\LocationRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class SmsBulkType extends AbstractType
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
            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter module name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),
                    new Length(array('max'=>200))
                )
            ))

            ->add('smsText','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Create sending sms text'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Create sending sms text')),
                    new Length(array('max'=>200))
                )
            ))

            ->add('bulkMobile','textarea', array('attr'=>array('class'=>'m-wrap span12','rows'=> '6','placeholder'=>'Bulk mobile no')))

            ->add('location', 'entity', array(
                'required'    => false,
                'empty_value' => '---Select Location---',
                'attr'=>array('class'=>'select2 span12'),
                'class' => 'Setting\Bundle\LocationBundle\Entity\Location',
                'constraints' =>array(
                    new NotBlank(array('message'=>'Select Location'))
                ),
                'choices'=> $this->LocationChoiceList(),
                'choices_as_values' => true,
                'choice_label' => 'nestedLabel',
            ))

            ->add('sourceTo', 'choice', array(
                'attr'=>array('class'=>'span12 select2'),
                'multiple' => 'multiple',
                'choices' => array(
                    'Customer' => 'Customer',
                    'File' => 'File',
                    'Bulk Mobile' => 'Bulk Mobile'
                ),
            ))

            ->add('ageGroup', 'choice', array(
                'attr'=>array('class'=>'span12 select2'),
                'multiple' => 'multiple',
                'choices' => array(
                    'Kids' => 'Kids',
                    'Adult' => 'Adult'
                ),
            ))

            ->add('gender', 'choice', array(
                'attr'=>array('class'=>'span12 select2'),
                'multiple' => 'multiple',
                'choices' => array(
                    'Male' => 'Male',
                    'Female' => 'Female'
                ),
            ))
            ->add('file','file',array('attr'=>array('class'=>'default span12')))
            ->add('status');
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Setting\Bundle\ToolBundle\Entity\SmsBulk'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'setting_bundle_toolbundle_bulksms';
    }


    protected function LocationChoiceList()
    {
        return $syndicateTree = $this->location->getLocationOptionGroup();

    }
}
