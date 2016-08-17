<?php

namespace Syndicate\Bundle\ComponentBundle\Form;

use Setting\Bundle\LocationBundle\Repository\LocationRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class VendorType extends AbstractType
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

        $userID = (!empty($options['data'])) ? $options['data']->getId():0;

        if($userID > 0){

            parent::buildForm($builder, $options);

            $builder

            ->add('establishment','date', array('attr'=>array('class'=>'m-wrap span12 selectbox'),'years' => range(1850, date('Y'))))
            ->add('registrationNo','text', array('attr'=>array('class'=>'m-wrap span12')))

            ->add('address','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter address'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),
                    new Length(array('max'=>200))
                )
            ))
            ->add('hotline','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter hot line'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required'))

                )
            ))

            ->add('postalCode','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter postal code')))
            ->add('weeklyOffDay', 'choice', array(
                    'attr'=>array('class'=>'selectbox'),
                    'choices' => array('Sunday' => 'Sunday',  'Monday' => 'Monday', 'Tuesday' => 'Tuesday', 'Wednesday' => 'Wednesday', 'Thursday' => 'Thursday', 'Friday' => 'Friday', 'Saturday' => 'Saturday'),
            ))
            ->add('startHour','text', array('attr'=>array('class'=>'m-wrap small clockface_1 span10', 'data-format' => 'hh:mm A','placeholder'=>'Enter start hour')))
            ->add('endHour','text', array('attr'=>array('class'=>'m-wrap small clockface_1 span10', 'data-format' => 'hh:mm A', 'placeholder'=>'Enter end hour')))
            ->add('phone','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter phone')))
            ->add('fax','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter fax')))
            ->add('skypeId','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter skype id')))
            ->add('email','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter email')))
            ->add('contactPerson','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter contact person')))
            ->add('contactPersonDesignation','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter contact person designation')))
            ->add('file','file', array('attr'=>array('class'=>'input-sm','placeholder'=>'Enter contact person designation')))
            ->add('overview','textarea', array('attr'=>array('class'=>'span12 wysihtml5 m-wrap','rows'=>10)))
            ->add('location', 'entity', array(
                    'required'    => true,
                    'empty_value' => '---Select location---',
                    'attr'=>array('class'=>'location m-wrap span10 selectbox'),
                    'class' => 'SettingLocationBundle:Location',
                    'property' => 'nestedLabel',
                    'choices'=> $this->LocationChoiceList()
            ));
        }else{

            $builder

                ->add('name','text', array('attr'=>array('class'=>'m-wrap tooltips','placeholder'=>'Enter organization name' , 'data-original-title' =>'Please enter your organization name' , 'data-trigger' => 'hover'),
                    'constraints' =>array(
                        new NotBlank(array('message'=>'Please input required')),
                        new Length(array('max'=>200))
                    )
                ));

        }
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Syndicate\Bundle\ComponentBundle\Entity\Vendor'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'syndicate_bundle_componentbundle_vendor';
    }

    /**
     * @return mixed
     */
    protected function LocationChoiceList()
    {
        return $locationTree = $this->em->getFlatLocationTree();

    }

}
