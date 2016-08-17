<?php

namespace Core\UserBundle\Form;

use Core\UserBundle\Form\Type\ProfileType;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Form\InitialOptionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;


class DomainProfileType extends AbstractType
{


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
            ->add('joiningDate','date', array('attr'=>array('class'=>'m-wrap span6')))
            ->add('address','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter address')))
            ->add('permanentAddress','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter permanent address')))
            ->add('designation','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter designation')))
            ->add('postalCode','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter your email address')))
            ->add('additionalPhone','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter your email address')))
            ->add('nid','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter national id card no')))
            ->add('dob','birthday', array('attr'=>array('class'=>'m-wrap span6')))
            ->add('about','textarea', array('attr'=>array('class'=>'m-wrap span12','rows'=>'8')))
            ->add('bank', 'entity', array(
                'required'    => true,
                'class' => 'Setting\Bundle\ToolBundle\Entity\Bank',
                'empty_value' => '---Choose a bank---',
                'property' => 'name',
                'attr'=>array('class'=>'span12 select2'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('b')
                        ->orderBy("b.name", "ASC");
                },
            ))
            ->add('branch','textarea', array('attr'=>array('class'=>'m-wrap span12','rows'=>4 ,'draggable' => 'false' ,'placeholder'=>'Enter your bank branch name')))
            ->add('accountNo','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter your bank account no')))
            ->add('district', 'entity', array(
                'required'      => true,
                'multiple'      =>false,
                'expanded'      =>false,
                'class'         => 'SettingLocationBundle:Location',
                'property'      => 'name',
                'attr'          =>array('class'=>'m-wrap span12 select2'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('d')
                        ->where("d.level = 2")
                        ->orderBy('d.name','ASC');
                }
            ))

            ->add('thana', 'entity', array(
                'required'      =>  true,
                'multiple'      =>  false,
                'expanded'      =>  false,
                'class'         => 'SettingLocationBundle:Location',
                'property'      => 'name',
                'attr'          =>  array('class'=>'m-wrap span12 select2'),
                'query_builder' =>  function(EntityRepository $er){
                    return $er->createQueryBuilder('t')
                        ->where("t.level = 3")
                        ->orderBy('t.name','ASC');
                }
            ))
            ->add('bloodGroup', 'choice', array(
                'attr'=>array('class'=>'m-wrap span6'),
                'choices' => array('A+' => 'A+',  'A-' => 'A-'),
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
}