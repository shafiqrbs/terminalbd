<?php

namespace Syndicate\Bundle\ComponentBundle\Form;

use Setting\Bundle\LocationBundle\Repository\LocationRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class ScholarshipType extends AbstractType
{
    /** @var  LocationRepository */
    private $em;

    function __construct(LocationRepository $em)
    {
        $this->em = $em;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        parent::buildForm($builder, $options);

        $builder

            ->add('organizationName','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter your organization name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required'))

                )
            ))
            ->add('establishment','date', array('attr'=>array('class'=>'m-wrap span12 selectbox'),'years' => range(1850, date('Y'))))
            ->add('address','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter address'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),

                )
            ))
            ->add('mobile','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter mobile no'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required'))

                )
            ))

            ->add('registrationNo','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter registration no')))
            ->add('postalCode','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter postal code')))
            ->add('phone','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter phone')))
            ->add('fax','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter fax')))
            ->add('skypeId','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter skype id')))
            ->add('email','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter email')))
            ->add('contactPerson','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter contact person')))
            ->add('contactPersonDesignation','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter contact person designation')))
            ->add('file','file', array('attr'=>array('class'=>'input-sm','placeholder'=>'Enter contact person designation')))
            ->add('content','textarea', array('attr'=>array('class'=>'span12 wysihtml5 m-wrap','rows'=>10)))

            ->add('location', 'entity', array(
                'required'    => true,
                'empty_value' => '---Select location---',
                'attr'=>array('class'=>'location m-wrap span10 selectbox'),
                'class' => 'SettingLocationBundle:Location',
                'property' => 'nestedLabel',
                'choices'=> $this->LocationChoiceList()
            ));

    }

    /**
     * @return mixed
     */
    protected function LocationChoiceList()
    {
        return $locationTree = $this->em->getFlatLocationTree();

    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Syndicate\Bundle\ComponentBundle\Entity\Scholarship'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'syndicate_bundle_componentbundle_scholarship';
    }
}
