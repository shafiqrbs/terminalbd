<?php

namespace Setting\Bundle\ContentBundle\Form;

use Doctrine\ORM\EntityRepository;
use Setting\Bundle\LocationBundle\Repository\LocationRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContactPageType extends AbstractType
{

    /** @var  LocationRepository */

    private $location;

    function __construct(LocationRepository $location)
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
            ->add('address1','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter address'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),

                )
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
            ->add('latitude','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter latitude')))
            ->add('longitude','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter longitude')))
            ->add('address2','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter address 2')))
            ->add('postalCode','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter postal code')))
            ->add('fax','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter fax no')))
            ->add('additionalPhone','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter additional phoe/mobile no')))
            ->add('additionalEmail','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter additional email')))
            ->add('contactPerson','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter contact person name')))
            ->add('designation','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter designation')))
            ->add('startHour','text', array('attr'=>array('class'=>'m-wrap small clockface_1 span10', 'data-format' => 'hh:mm A','placeholder'=>'Start hour')))
            ->add('endHour','text', array('attr'=>array('class'=>'m-wrap small clockface_1 span10', 'data-format' => 'hh:mm A', 'placeholder'=>'End hour')))
            ->add('weeklyOffDay', 'choice', array(
                'attr'=>array('class'=>'check-list span12'),
                'expanded'      =>true,
                'multiple'      =>true,
                'choices' => array('Sunday' => 'Sunday',  'Monday' => 'Monday', 'Tuesday' => 'Tuesday', 'Wednesday' => 'Wednesday', 'Thursday' => 'Thursday', 'Friday' => 'Friday', 'Saturday' => 'Saturday'),
            ))
            ->add('isContactForm')
            ->add('isBranch')
            ->add('isBaseInformation')

        ;

    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Setting\Bundle\ContentBundle\Entity\ContactPage'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'setting_bundle_contentbundle_contactpage';
    }

    protected function LocationChoiceList()
    {
        return $syndicateTree = $this->location->getLocationOptionGroup();

    }

}
