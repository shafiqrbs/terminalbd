<?php

namespace Appstore\Bundle\HospitalBundle\Form;

use Appstore\Bundle\HospitalBundle\Entity\Category;
use Appstore\Bundle\HospitalBundle\Repository\CategoryRepository;
use Appstore\Bundle\HospitalBundle\Repository\HmsCategoryRepository;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class SurgeryType extends AbstractType
{


    /** @var  HmsCategoryRepository */
    private $emCategory;

    /** @var  GlobalOption */
    private $globalOption;


    function __construct(HmsCategoryRepository $emCategory , GlobalOption $globalOption)
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

            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12','autocomplete'=>'off','placeholder'=>'Enter surgery name'),
                    'constraints' =>array(
                        new NotBlank(array('message'=>'Please enter cabin name'))
                    ))
            )
            ->add('room','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter room/cabin name or no')))
            ->add('content','textarea', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter content')))
            ->add('price','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter fees'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),
                )
            ))
            ->add('minimumPrice','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter minimum price')))
            ->add('commission','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter commission')))


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
