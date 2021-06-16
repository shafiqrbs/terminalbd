<?php

namespace Appstore\Bundle\InventoryBundle\Form;

use Appstore\Bundle\InventoryBundle\Entity\InventoryConfig;
use Appstore\Bundle\InventoryBundle\Entity\ItemTypeGrouping;
use Appstore\Bundle\InventoryBundle\Repository\ItemTypeGroupingRepository;
use Doctrine\ORM\EntityRepository;
use Product\Bundle\ProductBundle\Entity\Category;
use Product\Bundle\ProductBundle\Entity\CategoryRepository;
use Product\Bundle\ProductBundle\Entity\ItemGroup;
use Product\Bundle\ProductBundle\Entity\ItemGroupRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\NotNull;

class ItemType extends AbstractType
{

    /** @var  ItemGroupRepository */
    private $groupRep;

    /** @var  CategoryRepository */
    private $catRep;

    /** @var InventoryConfig */
    public  $inventoryConfig;

    function __construct(InventoryConfig $inventoryConfig, ItemGroupRepository $groupRep, CategoryRepository $catRep )
    {
        $this->inventoryConfig = $inventoryConfig;
        $this->category = $catRep;
        $this->itemGroup = $groupRep;
    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder

            ->add('category', 'entity', array(
                'required'    => false,
                'empty_value' => '---Select item category---',
                'attr'=>array('class'=>'category m-wrap span12 select2'),
                'class' => 'ProductProductBundle:Category',
                'property' => 'nestedLabel',
                'choices'=> $this->categoryChoiceList()
            ))

            ->add('itemGroup', 'entity', array(
                'required'    => false,
                'empty_value' => '---Select item group---',
                'attr'=>array('class'=>'category m-wrap span12 select2'),
                'class' => 'ProductProductBundle:ItemGroup',
                'property' => 'nestedLabel',
                'choices'=> $this->ItemGroupChoiceList()
            ))

            ->add('name', 'text', array(
                'required'    => true,
                'attr'=>array('class'=>'span12 select2','list'=>'masterItem'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required'))
                )
            ))
            ->add('itemUnit', 'entity', array(
                'required'    => true,
                'class' => 'Setting\Bundle\ToolBundle\Entity\ProductUnit',
                'property' => 'name',
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please select required'))
                ),
                'empty_value' => '---Choose a item unit ---',
                'attr'=>array('class'=>'span12 select2'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('p')
                        ->where("p.status = 1")
                        ->orderBy("p.name","ASC");
                },
            ))
            ->add('salesPrice', 'text', array(
                'required'    => false,
                'attr'=>array('class'=>'span12')
            ))

            ->add('purchasePrice', 'text', array(
                'required'    => false,
                'attr'=>array('class'=>'span12')
            ))
            ->add('file');
            if($this->inventoryConfig->getIsVendor() == 1 ){

                $builder
                ->add('vendor', 'entity', array(
                    'required'    => true,
                    'class' => 'Appstore\Bundle\InventoryBundle\Entity\Vendor',
                    'empty_value' => '---Choose a vendor ---',
                    'property' => 'companyName',
                    'attr'=>array('class'=>'span12 select2'),
                    'constraints' =>array(
                        new NotBlank(array('message'=>'Please input required'))
                    ),
                    'query_builder' => function(EntityRepository $er){
                        return $er->createQueryBuilder('v')
                            ->where("v.status = 1")
                            ->andWhere("v.inventoryConfig =".$this->inventoryConfig->getId());

                    },
                )) ;

            }
            if($this->inventoryConfig->getIsColor() == 1 ) {

                $builder
                ->add('color', 'entity', array(
                    'required' => true,
                    'class' => 'Appstore\Bundle\InventoryBundle\Entity\ItemColor',
                    'empty_value' => '---Choose a color ---',
                    'property' => 'name',
                    'attr' => array('class' => 'span12 select2'),
                    'constraints' => array(
                        new NotBlank(array('message' => 'Please input required'))
                    ),
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('v')
                            ->where("v.status = 1");

                    },
                ));

            }
            if($this->inventoryConfig->getIsSize() == 1 ) {

                $builder
                    ->add('size', 'entity', array(
                        'required'    => true,
                        'class' => 'Appstore\Bundle\InventoryBundle\Entity\ItemSize',
                        'empty_value' => '---Choose a size/weight ---',
                        'property' => 'name',
                        'attr'=>array('class'=>'span12 select2'),
                        'constraints' =>array(
                            new NotBlank(array('message'=>'Please input required'))
                        ),
                        'query_builder' => function(EntityRepository $er){
                            return $er->createQueryBuilder('v')
                                ->where("v.status = 1");
                        },
                    ));

            }

            if($this->inventoryConfig->getIsBrand() == 1 ) {

                $builder
                    ->add('brand', 'entity', array(
                        'required'    => true,
                        'class' => 'Appstore\Bundle\InventoryBundle\Entity\ItemBrand',
                        'empty_value' => '---Choose a brand ---',
                        'property' => 'name',
                        'attr'=>array('class'=>'span12 select2'),
                        'constraints' =>array(
                            new NotBlank(array('message'=>'Please input required'))
                        ),
                        'query_builder' => function(EntityRepository $er){
                            return $er->createQueryBuilder('v')
                                ->where("v.status = 1")
                                ->andWhere("v.inventoryConfig =".$this->inventoryConfig->getId());
                        },
                    ));

            }


    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\InventoryBundle\Entity\Item'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'item';
    }

    /**
     * @return mixed
     */
    protected function ItemGroupChoiceList()
    {
        return $itemTypeTree = $this->itemGroup->getFlatInventoryItemGroupTree($this->inventoryConfig);

    }

    /**
     * @return mixed
     */
    protected function categoryChoiceList()
    {

        return $categoryTree = $this->category->getFlatInventoryCategoryTree($this->inventoryConfig);

    }
}
