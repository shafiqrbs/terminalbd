<?php

namespace Appstore\Bundle\HospitalBundle\Form;


use Appstore\Bundle\HospitalBundle\Entity\AdmissionPatientParticular;
use Appstore\Bundle\HospitalBundle\Entity\HospitalConfig;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;


class InvoiceAdmittedParticularType extends AbstractType
{

    /** @var  HospitalConfig */
    private $hospitalConfig;

    function __construct(HospitalConfig $hospitalConfig)
    {
        $this->hospitalConfig  = $hospitalConfig;
    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('particular', 'entity',array(
                'class'     => 'Appstore\Bundle\HospitalBundle\Entity\Particular',
                'group_by'  => 'service.name',
                'property'  => 'name',
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please choose particular'))
                ),
                'empty_value' => '---Test Name, Accessories, Surgery, Cabin etc.---',
                'attr'=>array('class'=>'span12 m-wrap particular select2'),
                'choice_translation_domain' => true,
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('e')
                        ->join("e.service",'s')
                        ->where('e.hospitalConfig ='.$this->hospitalConfig->getId())
                        ->andWhere('s.id IN(:service)')->setParameter('service',array(1,2,3,4,7))
                        ->andWhere("e.status = 1")
                        ->orderBy("e.name","ASC");
                }
            ))
            ->add('quantity','text', array('attr'=>array('class'=>'m-wrap span5 input-number','placeholder'=>'Quantity','autocomplete'=>'off')))
            ->add('salesPrice','text', array('attr'=>array('class'=>'m-wrap span8 price','placeholder'=>'Price','autocomplete'=>'off')))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\HospitalBundle\Entity\AdmissionPatientParticular'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'admitted_particular';
    }



}