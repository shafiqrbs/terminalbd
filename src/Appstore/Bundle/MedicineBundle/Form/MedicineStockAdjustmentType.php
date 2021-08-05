<?php

namespace Appstore\Bundle\MedicineBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class MedicineStockAdjustmentType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('medicineStock','text', array('attr'=>array('class'=>'m-wrap span12 select2StockMedicine input','placeholder'=>'Enter stock medicine name')
            ,'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),
            )
            ))
            ->add('adjustment', 'entity', array(
                'required'    => false,
                'mapped'    => false,
                'class' => 'Appstore\Bundle\MedicineBundle\Entity\MedicineParticular',
                'empty_value' => '---Select Print Message ---',
                'property' => 'name',
                'attr'=>array('class'=>' span12 m-wrap'),
                'constraints' =>array( new NotBlank(array('message'=>'Select print message')) ),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->join("e.particularType","pt")
                        ->where("e.status = 1")
                        ->andWhere("e.medicineConfig ={$this->globalOption->getMedicineConfig()->getId()}")
                        ->andWhere("pt.slug = 'stock-adjustment'");
                },
            ))
            ->add('quantity','number', array('attr'=>array('class'=>'m-wrap span12 form-control input-number input','placeholder'=>'quantity')))
            ->add('notes','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter notes ')))

        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\MedicineBundle\Entity\MedicineStockAdjustment'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'adjustment';
    }
}
