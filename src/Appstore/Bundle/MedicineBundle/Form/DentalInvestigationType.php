<?php

namespace Appstore\Bundle\MedicineBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class DentalInvestigationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter particular name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),
                )
            ))
            ->add('parent', 'entity', array(
                'required'    => false,
                'class' => 'Appstore\Bundle\MedicineBundle\Entity\DentalInvestigation',
                'empty_value' => '---Select parent name---',
                'property' => 'name',
                'attr'=>array('class'=>'select2 span12'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('o')
                        ->where("o.status = 1")
                        ->andWhere("o.isParent = 1")
                        ->orderBy('o.name','ASC');
                },
            ))
            ->add('isParent');
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\MedicineBundle\Entity\DentalInvestigation'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appstore_bundle_medicinebundle_investigation';
    }
}
