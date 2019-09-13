<?php

namespace Appstore\Bundle\DomainUserBundle\Form;

use Appstore\Bundle\DomainUserBundle\Repository\CustomerRepository;
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
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;


class MemberEditProfileType extends AbstractType
{


    /** @var $em CustomerRepository */

    private $em;



    function __construct(CustomerRepository $em)
    {
        $this->em = $em;

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
            ->add('fatherName','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter your father name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input father name')),
                )
            ))
            ->add('motherName','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter your mother name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input mother name')),
                )
            ))
            ->add('spouseName','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter your spouse name')))
            ->add('spouseOccupation','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter your spouse occupation')))
            ->add('spouseDesignation','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter your spouse designation')))
            ->add('additionalPhone','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter your email address')))
            ->add('email','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter national id card no')))
            ->add('nid','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter national id card no','autoComplete'=>false)))

            ->add('memberDesignation','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter designation')))
            ->add('dob','birthday', array('attr'=>array('class'=>'m-wrap span6')))
            ->add('about','textarea', array('attr'=>array('class'=>'m-wrap span12','rows'=>'8')))
            ->add('profession','text', array('attr'=>array('class'=>'m-wrap span12','autocomplete'=>'off','placeholder'=>'Enter member occupation'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Enter member occupation')),
                )
            ))
            ->add('religion', 'choice', array(
                'attr'=>array('class'=>'span12 m-wrap'),
                'empty_value' => '---Choose a relation---',
                'choices' => array(
                    'Muslim' => 'Muslim',
                    'Hinduism' => 'Hinduism',
                    'Buddhism' => 'Buddhism',
                    'Christianity' => 'Christianity',
                    'Other religions' => 'Other religions',
                ),
            ))
            ->add('batchYear', 'choice', array(
                'attr'=>array('class'=>'span8 m-wrap'),
                'empty_value' => '---Choose a study year---',
                'choices' => $this->batchYearChoiceList(),
            ))
            ->add('studentBatch', 'choice', array(
                'attr'=>array('class'=>'m-wrap span8'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Select student batch'))),
                'empty_value' => '---Choose a Batch---',
                'choices' => $this->studentBatchChoiceList(),
            ))

            ->add('maritalStatus', 'choice', array(
                'attr'=>array('class'=>'m-wrap span6'),
                'empty_value' => '---Choose a Marital Status---',
                'choices' => array('Married' => 'Married','Single' => 'Single'),

            ))
            ->add('bloodGroup', 'choice', array(
                'attr'=>array('class'=>'m-wrap span6'),
                'empty_value' => '---Choose a Blood Group---',
                'choices' => array('A+' => 'A+',  'A-' => 'A-','B+' => 'B+',  'B-' => 'B-',  'O+' => 'O+',  'O-' => 'O-',  'AB+' => 'AB+',  'AB-' => 'AB-'),

            ))
            ->add('file', 'file',array(
                'required' => true,
                'constraints' =>array(
                    new File(array(
                        'maxSize' => '1M',
                        'mimeTypes' => array(
                            'image/png',
                            'image/jpg',
                            'image/jpeg',
                            'image/gif',
                        ),
                        'mimeTypesMessage' => 'Please upload a valid png,jpg,jpeg,gif extension',
                    ))
                )
            ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\DomainUserBundle\Entity\Customer'
        ));
    }

    public function getName()
    {
        return 'manage_profile';
    }

    public function studentBatchChoiceList()
    {
        return $syndicateTree = $this->em->studentBatchChoiceList();

    }

    public function batchYearChoiceList()
    {
        return $syndicateTree = $this->em->batchYearChoiceList();

    }


}