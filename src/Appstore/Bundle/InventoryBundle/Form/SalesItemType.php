<?php

namespace Appstore\Bundle\InventoryBundle\Form;

use Appstore\Bundle\InventoryBundle\Entity\InventoryConfig;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class SalesItemType extends AbstractType
{


    /** @var InventoryConfig */

    public  $inventoryConfig;

    function __construct(InventoryConfig $inventoryConfig)
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
                'property' => 'itemName',
                'attr'=>array('class'=>'span12 select2 itemSearch'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('p')
                        ->where("p.status = 1")
                        ->andWhere("p.inventoryConfig =".$this->inventoryConfig->getId())
                        ->orderBy("p.name","ASC");
                },
            ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\InventoryBundle\Entity\SalesItem'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'salesitem';
    }


}
