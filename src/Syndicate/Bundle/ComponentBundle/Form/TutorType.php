<?php

namespace Syndicate\Bundle\ComponentBundle\Form;

use Setting\Bundle\LocationBundle\Repository\LocationRepository;
use Setting\Bundle\ToolBundle\Repository\SyndicateRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class TutorType extends AbstractType
{

    /** @var  LocationRepository */
    private $em;

    /** @var  SyndicateRepository */
    private $syn;


    function __construct(LocationRepository $em , SyndicateRepository $syn)
    {
        $this->em = $em;
        $this->syn = $syn;
    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        parent::buildForm($builder, $options);

        $builder

            ->add('name','textarea', array('attr'=>array('class'=>'span12  m-wrap','rows'=>5),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),

                )
            ))

            ->add('dateOfBirth','date', array('attr'=>array('class'=>'m-wrap span12 selectbox'),'years' => range(1850, date('Y'))))
            ->add('presentAddress','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter address'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),

                )
            ))
            ->add('permanentAddress','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter address'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),

                )
            ))
            ->add('mobile','text', array('attr'=>array('class'=>'m-wrap span12 mobile','placeholder'=>'Enter mobile no'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required'))

                )
            ))

            ->add('email','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter mobile address'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required'))

                )
            ))
            ->add('postalCode','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter postal code')))
            ->add('bloodGroup', 'choice', array(
                'attr'=>array('class'=>'selectbox'),
                'choices' => array('' => 'Select blood group','A+' => 'A+',  'A-' => 'A-', 'B+' => 'B+', 'B-' => 'B-', 'AB+' => 'AB+', 'AB-' => 'AB-', 'O+' => 'O+','O-'=>'O-'),
            ))
            ->add('nationality','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter your nationality')))
            ->add('gender', 'choice', array(
                'attr'=>array('class'=>'selectbox'),
                'choices' => array('Male' => 'Male',  'Female' => 'Female'),
            ))
            ->add('currentPosition','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter current position')))
            ->add('phone','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter phone')))
            ->add('skypeId','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter skype id')))
            ->add('linkedin','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter linkedin id')))
            ->add('twitterId','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter twitter url')))
            ->add('facebookId','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter facebook id')))
            ->add('file','file', array('attr'=>array('class'=>'input-sm','placeholder'=>'Enter contact person designation')))
            ->add('location', 'entity', array(
                'required'    => true,
                'empty_value' => '---Select location---',
                'attr'=>array('class'=>'location m-wrap span10 selectbox'),
                'class' => 'SettingLocationBundle:Location',
                'property' => 'nestedLabel',
                'choices'=> $this->LocationChoiceList()
            ))

            ->add('syndicates', 'entity', array(
                'required'    => true,
                'multiple'  =>true,
                'expanded' => true,
                'attr'=>array('class'=>'m-wrap span10 multiple-select'),
                'class' => 'SettingToolBundle:Syndicate',
                'property' => 'nestedLabel',
                'choices'=> $this->SyndicateChoiceList()
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
     * @return mixed
     */
    protected function SyndicateChoiceList()
    {
        return $syndicateTree = $this->syn->getFlatSyndicateTree();

    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Syndicate\Bundle\ComponentBundle\Entity\Tutor'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'syndicate_bundle_componentbundle_tutor';
    }
}
