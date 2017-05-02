<?php

namespace Appstore\Bundle\HospitalBundle\Form;

use Appstore\Bundle\HospitalBundle\Entity\Category;
use Appstore\Bundle\HospitalBundle\Repository\CategoryRepository;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class DoctorType extends AbstractType
{


    /** @var  CategoryRepository */
    private $emCategory;

    /** @var  GlobalOption */
    private $globalOption;


    function __construct(CategoryRepository $emCategory , GlobalOption $globalOption)
    {
        $this->emCategory = $emCategory;
        $this->globalOption = $globalOption;
    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('room','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter room/cabin name or no')))
            ->add('price','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter fees'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),
                )
            ))
            ->add('commission','text', array('attr'=>array('class'=>'m-wrap span12 numeric','autocomplete'=>'off','placeholder'=>'Commission no')))
            ->add('phoneNo','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter additional phone no')))
            ->add('email','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter valid email')))
            ->add('currentJob','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter current job')))
            ->add('specialist','textarea', array('attr'=>array('class'=>'m-wrap span12','rows'=>3,'placeholder'=>'Enter specialist')))
            ->add('educationalDegree','textarea', array('attr'=>array('class'=>'m-wrap span12','rows'=>3,'placeholder'=>'Enter educational degree')))
            ->add('startHour','text', array('attr'=>array('class'=>'m-wrap small clockface_1 span10', 'data-format' => 'hh:mm A','placeholder'=>'Start hour')))
            ->add('endHour','text', array('attr'=>array('class'=>'m-wrap small clockface_1 span10', 'data-format' => 'hh:mm A', 'placeholder'=>'End hour')))
            ->add('weeklyOffDay', 'choice', array(
                'attr'=>array('class'=>'check-list span12'),
                'expanded'      =>true,
                'multiple'      =>true,
                'choices' => array('Sunday' => 'Sunday',  'Monday' => 'Monday', 'Tuesday' => 'Tuesday', 'Wednesday' => 'Wednesday', 'Thursday' => 'Thursday', 'Friday' => 'Friday', 'Saturday' => 'Saturday'),
            ))
            ->add('room','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter room/cabin name or no')))
            ->add('assignDoctor', 'entity', array(
                'required'    => false,
                'empty_value' => '---Select doctor---',
                'class' => 'Core\UserBundle\Entity\User',
                'property' => 'userFullName',
                'attr'=>array('class'=>'span12 select2'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.enabled = 1")
                        ->andWhere("e.globalOption =".$this->globalOption->getId())
                   /*     ->join('e.profile','p')*/
                   /*     ->join('p.designation','designation')*/
                   /*     ->where("e.enabled = 1")*/
                   /*     ->andWhere("designation.slug = 'doctor'")*/
                        ->orderBy("e.id","ASC");
                }
            ))

            ->add('assignOperator', 'entity', array(
                'required'    => false,
                'empty_value' => '---Select operator---',
                'class' => 'Core\UserBundle\Entity\User',
                'property' => 'userFullName',
                'attr'=>array('class'=>'span12 select2'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.enabled = 1")
                        ->andWhere("e.globalOption =".$this->globalOption->getId())
                 /*       ->join('e.profile','p')*/
                 /*       ->join('p.designation','designation')*/
                 /*       ->where("e.enabled = 1")*/
                 /*       ->andWhere("designation.slug = 'operator'")*/
                        ->orderBy("e.id","ASC");
                }
            ))

            ->add('category', 'entity', array(
                'required'    => true,
                'empty_value' => '---Select pathology---',
                'attr'=>array('class'=>'m-wrap span12 select2'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please select required'))
                ),
                'class' => 'Appstore\Bundle\HospitalBundle\Entity\Category',
                'property' => 'nestedLabel',
                'choices'=> $this->PathologyChoiceList()
            ))

            ->add('department', 'entity', array(
                'required'    => true,
                'empty_value' => '---Select department---',
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please select required'))
                ),
                'attr'=>array('class'=>'m-wrap span12 select2'),
                'class' => 'Appstore\Bundle\HospitalBundle\Entity\Category',
                'property' => 'nestedLabel',
                'choices'=> $this->DepartmentChoiceList()
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\HospitalBundle\Entity\Particular'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appstore_bundle_hospitalbundle_particular';
    }

    /**
     * @return mixed
     */
    protected function PathologyChoiceList()
    {
        return $this->emCategory->getParentCategoryTree($parent = 2 /** Pathology */ );

    }
    /**
     * @return mixed
     */
    protected function DepartmentChoiceList()
    {
        return $this->emCategory->getParentCategoryTree($parent = 7 /** Department */);

    }

}
