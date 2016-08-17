<?php

namespace Syndicate\Bundle\ComponentBundle\Form;

use Setting\Bundle\LocationBundle\Repository\LocationRepository;
use Setting\Bundle\ToolBundle\Repository\SyndicateRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class StudyAbroadType extends AbstractType
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

            ->add('establishment','date', array('attr'=>array('class'=>'m-wrap span12 selectbox'),'years' => range(1850, date('Y'))))
            ->add('address','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter address'),
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

            ->add('contactPerson','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter contact person name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required'))

                )
            ))


            ->add('registrationNo','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter registration no')))
            ->add('postalCode','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter postal code')))
            ->add('weeklyOffDay', 'choice', array(
                'constraints' =>array(new NotBlank(array('message'=>'Please input required'))),
                'attr'=>array('class'=>'selectbox'),
                'choices' => array('Sunday' => 'Sunday',  'Monday' => 'Monday', 'Tuesday' => 'Tuesday', 'Wednesday' => 'Wednesday', 'Thursday' => 'Thursday', 'Friday' => 'Friday', 'Saturday' => 'Saturday'),
            ))
            ->add('startHour','text', array('attr'=>array('class'=>'m-wrap small clockface_1 span10', 'data-format' => 'hh:mm A','placeholder'=>'Enter start hour')))
            ->add('endHour','text', array('attr'=>array('class'=>'m-wrap small clockface_1 span10', 'data-format' => 'hh:mm A', 'placeholder'=>'Enter end hour')))
            ->add('phone','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter phone')))
            ->add('fax','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter fax')))
            ->add('skypeId','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter skype id')))
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
            ))

            ->add('syndicates', 'entity', array(
                'required'    => true,
                'multiple'  =>true,
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
            'data_class' => 'Syndicate\Bundle\ComponentBundle\Entity\StudyAbroad'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'syndicate_bundle_componentbundle_studyabroad';
    }
}
