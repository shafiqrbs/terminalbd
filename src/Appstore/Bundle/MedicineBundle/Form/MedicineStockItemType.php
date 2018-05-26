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
            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12 autoComplete2Medicine','placeholder'=>'Enter medicine name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),
                )
            ))
            ->add('accessoriesBrand', 'entity', [
                'required'    => false,
                'group_by'  => 'particularType.name',
                'class' => 'Appstore\Bundle\MedicineBundle\Entity\MedicineParticular',
                'empty_value' => '---Select medicine & others brand ---',
                'property' => 'name',
                'choice_translation_domain' => true,
                'attr'=>array('class'=>'m-wrap span12 inputs'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->join("e.particularType","pt")
                        ->where("e.status = 1")
                        ->andWhere("e.medicineConfig =". $this->medicineConfig->getId())
                        ->andWhere('pt.slug IN (:slugs)')->setParameter('slugs',array('herbal','accessories','medicine','non-medicine','surgery','medical-device'));
                },
            ])
            ->add('purchaseQuantity','text', array('attr'=>array('class'=>'m-wrap span3 stockInput','placeholder'=>'Qnt')))
            ->add('purchasePrice','text', array('attr'=>array('class'=>'m-wrap span5 stockInput','placeholder'=>'Purchase price')))
            ->add('salesPrice','text', array('attr'=>array('class'=>'m-wrap span4 stockInput','placeholder'=>'MRP')))
            ->add('rackNo','text', array('attr'=>array('class'=>'m-wrap span12 stockInput','placeholder'=>'Rack no'),
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
                'attr'=>array('class'=>'span12 stockInput'),
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
                'attr'=>array('class'=>'m-wrap span12 stockInput'),
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
