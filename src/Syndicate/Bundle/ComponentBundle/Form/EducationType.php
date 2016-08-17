<?php

namespace Syndicate\Bundle\ComponentBundle\Form;

use Doctrine\ORM\EntityRepository;
use Setting\Bundle\LocationBundle\Repository\LocationRepository;
use Setting\Bundle\ToolBundle\Repository\InstituteLevelRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class EducationType extends AbstractType
{


    /** @var  LocationRepository */
    private $em;

    /** @var  InstituteLevelRepository */
    private $ic;

    function __construct(LocationRepository $em, InstituteLevelRepository $ic)
    {
        $this->em = $em;
        $this->ic = $ic;
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
            ->add('establishment','date', array('attr'=>array('class'=>'selectbox'),'years' => range(1850, date('Y'))))
            ->add('instituteCheif','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter Institute Cheif'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),
                    new Length(array('max'=>200))
                )
            ))
            ->add('instituteCheifDesignation','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter institute cheif designation'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),
                    new Length(array('max'=>200))
                )
            ))
            ->add('address','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter address'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),
                    new Length(array('max'=>200))
                )
            ))
            ->add('hotline','text', array('attr'=>array('class'=>'m-wrap span12 mobile','placeholder'=>'Enter hot line', 'data-original-title' =>'Hot line must be use mobile number.' , 'data-trigger' => 'hover'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required'))

                )
            ))
            ->add('registrationNo','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter registration number'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required'))

                )
            ))
            ->add('weeklyOffDay', 'choice', array(
                'attr'=>array('class'=>'selectbox'),
                'choices' => array('Sunday' => 'Sunday',  'Monday' => 'Monday', 'Tuesday' => 'Tuesday', 'Wednesday' => 'Wednesday', 'Thursday' => 'Thursday', 'Friday' => 'Friday', 'Saturday' => 'Saturday'),
            ))
            ->add('courseLevels', 'entity', array(
                    'required'      => true,
                    'multiple'      =>true,
                    'expanded'      =>true,
                    'class'         => 'Setting\Bundle\ToolBundle\Entity\CourseLevel',
                    'property'      => 'name',
                    'attr'          =>array('class'=>'m-wrap span12'),
                    'query_builder' => function(EntityRepository $er){
                            return $er->createQueryBuilder('c')
                                ->andWhere("c.status = 1")
                                ->orderBy('c.id','ASC');
                        }
            ))

            ->add('postalCode','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter postal code')))
            ->add('startHour','text', array('attr'=>array('class'=>'m-wrap small clockface_1 span10', 'data-format' => 'hh:mm A','placeholder'=>'Enter start hour')))
            ->add('endHour','text', array('attr'=>array('class'=>'m-wrap small clockface_1 span10', 'data-format' => 'hh:mm A', 'placeholder'=>'Enter end hour')))
            ->add('phone','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter phone')))
            ->add('fax','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter fax')))
            ->add('skypeId','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter skype id')))
            ->add('email','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter email')))
            ->add('contactPerson','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter contact person')))
            ->add('contactPersonDesignation','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter contact person designation')))
            ->add('location', 'entity', array(
                    'required'    => true,
                    'empty_value' => '---Select location---',
                    'attr'=>array('class'=>'location m-wrap span10 selectbox'),
                    'class' => 'SettingLocationBundle:Location',
                    'property' => 'nestedLabel',
                    'choices'=> $this->LocationChoiceList()
            ))

            /*->add('instituteLevels', 'entity', array(
                    'required'    => true,
                    'empty_value' => '---Select location---',
                    'attr'=>array('class'=>'location m-wrap span10'),
                    'class' => 'SettingToolBundle:InstituteLevel',
                    'property' => 'nestedLabel',
                    'choices'=> $this->InstituteLevelChoiceList()
            ))*/


            ->add('file','file', array('attr'=>array('class'=>'input-sm','placeholder'=>'')))
            ->add('overview','textarea', array('attr'=>array('class'=>'span12 wysihtml5 m-wrap','rows'=>10))
                );

        }else{

            $builder

                ->add('name','text', array('attr'=>array('class'=>'m-wrap tooltips','placeholder'=>'Enter institute name' , 'data-original-title' =>'Tooltip text goes here. Tooltip text goes here.' , 'data-trigger' => 'hover'),
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
            'data_class' => 'Syndicate\Bundle\ComponentBundle\Entity\Education'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'syndicate_bundle_componentbundle_education';
    }

    /**
     * @return mixed
     */
    protected function LocationChoiceList()
    {
        return $locationTree = $this->em->getFlatLocationTree();

    }

    public function InstituteLevelChoiceList()
    {
        return $this->ic->getInstituteLevelList();
    }
}
