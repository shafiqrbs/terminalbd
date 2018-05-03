<?php

namespace Appstore\Bundle\MedicineBundle\Form;

use Appstore\Bundle\MedicineBundle\Entity\MedicineConfig;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class MedicineStockItemType extends AbstractType
{


    /** @var  MedicineConfig */

    private $medicineConfig;

    function __construct(MedicineConfig $medicineConfig)
    {
        $this->medicineConfig = $medicineConfig;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12 select2Medicine','placeholder'=>'Enter medicine name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),
                )
            ))
            ->add('purchaseQuantity','number', array('attr'=>array('class'=>'m-wrap span4 form-control input-number input','placeholder'=>'Purchase quantity')))
            ->add('purchasePrice','text', array('attr'=>array('class'=>'m-wrap span4','placeholder'=>'Purchase price')))
            ->add('salesPrice','text', array('attr'=>array('class'=>'m-wrap span4','placeholder'=>'MRP price')))
            ->add('rackNo','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Rack no'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),
                )
            ))
            ->add('unit', 'entity', array(
                'required'    => true,
                'class' => 'Setting\Bundle\ToolBundle\Entity\ProductUnit',
                'property' => 'name',
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please select required'))
                ),
                'empty_value' => '---Choose a unit ---',
                'attr'=>array('class'=>'span12 select2'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('p')
                        ->where("p.status = 1")
                        ->orderBy("p.name","ASC");
                },
            ))
            ->add('rackNo', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\MedicineBundle\Entity\MedicineParticular',
                'empty_value' => '---Rack position ---',
                'property' => 'name',
                'attr'=>array('class'=>'m-wrap span12 inputs'),
                'constraints' =>array( new NotBlank(array('message'=>'Select rack position')) ),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->join("e.particularType","pt")
                        ->where("e.status = 1")
                        ->andWhere("e.medicineConfig =". $this->medicineConfig->getId())
                        ->andWhere("pt.slug = 'medicine-rack'");
                },
            ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\MedicineBundle\Entity\MedicineStock'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'medicineStock';
    }
}
