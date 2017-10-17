<?php

namespace Appstore\Bundle\InventoryBundle\Form;

use Appstore\Bundle\InventoryBundle\Entity\InventoryConfig;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class PurchaseItemSimpleType extends AbstractType
{

    public  $inventoryConfig;

    public function __construct(InventoryConfig $inventoryConfig)
    {
        $this->inventoryConfig = $inventoryConfig;

    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

    
            ->add('item', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\InventoryBundle\Entity\Item',
                'empty_value' => '---Choose a item ---',
                'property' => 'name',
                'attr'=>array('class'=>'span12 select2'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required'))
                ),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('p')
                        ->where("p.status = 1")
                        ->andWhere("p.inventoryConfig =".$this->inventoryConfig->getId())
                        ->orderBy("p.name","ASC");
                },
            ))

            ->add('quantity','text', array('attr'=>array('class'=>'m-wrap span8','placeholder'=>'Item quantity'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Add item quantity')))))

            ->add('salesPrice','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Item sales price'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Add sales price')))))

            ->add('purchasePrice','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Item purchase price'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Add purchase price')))))
            ;

    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\InventoryBundle\Entity\PurchaseItem'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appstore_bundle_inventorybundle_purchaseitem';
    }
}
