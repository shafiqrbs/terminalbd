<?php

namespace Appstore\Bundle\DoctorPrescriptionBundle\Form;

use Appstore\Bundle\DoctorPrescriptionBundle\Entity\DpsConfig;
use Appstore\Bundle\HospitalBundle\Entity\Category;
use Appstore\Bundle\HospitalBundle\Repository\CategoryRepository;
use Appstore\Bundle\HospitalBundle\Repository\HmsCategoryRepository;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class ParticularType extends AbstractType
{



    /** @var  DpsConfig */
    private $dmsConfig;


    function __construct(DpsConfig  $dmsConfig)
    {

        $this->dmsConfig = $dmsConfig;
    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12','autocomplete'=>'off','placeholder'=>'Enter service name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please enter particular name'))
                ))
            )
            ->add('service', 'entity', array(
                'required'    => false,
                'class' => 'Appstore\Bundle\DoctorPrescriptionBundle\Entity\DpsService',
                'property' => 'name',
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please select required'))
                ),
                'empty_value' => '---Choose a service ---',
                'attr'=>array('class'=>'span12 m-wrap'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                    ->where("e.status = 1")
                    ->andWhere('e.dentalService is null')
                    ->andWhere('e.dmsConfig ='.$this->dmsConfig->getId())
                    ->orderBy("e.sorting","ASC");
                }
            ))

        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\DoctorPrescriptionBundle\Entity\DpsParticular'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appstore_bundle_hospitalbundle_particular';
    }


}
