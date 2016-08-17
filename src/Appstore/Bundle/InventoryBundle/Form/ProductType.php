<?php

namespace Appstore\Bundle\InventoryBundle\Form;

use Appstore\Bundle\InventoryBundle\Entity\InventoryConfig;
use Product\Bundle\ProductBundle\Entity\CategoryRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProductType extends AbstractType
{

    /** @var  InventoryConfig */

    private $inventoryConfig;

    /** @var  CategoryRepository */
    private $em;

    function __construct(CategoryRepository $em , InventoryConfig $inventoryConfig)
    {
        $this->em = $em;
        $this->inventoryConfig = $inventoryConfig;
    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder

            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Add  item name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please add your item name')))))
            ->add('unit', 'choice', array(
                'attr'=>array('class'=>'span6 select2'),
                'choices' => array(
                    'Bag'       => 'Bag',
                    'Bottle'    => 'Bottle',
                    'Box'       => 'Box',
                    'Can'       => 'Can',
                    'Cft'       => 'Cft',
                    'Coil'      => 'Coil',
                    'Cylinder'  => 'Cylinder',
                    'Carton'    => 'Carton',
                    'Feet'      => 'Feet',
                    'Gallon'    => 'Gallon',
                    'Jar'       => 'Jar',
                    'Job'       => 'Job',
                    'Kg'        => 'Kg',
                    'Liter'     => 'Liter',
                    'Meter'     => 'Meter',
                    'ML'        => 'ML',
                    'MM'        => 'MM',
                    'Nos'       => 'Nos',
                    'Pail'      => 'Pail',
                    'Pair'      => 'Pair',
                    'Pcs' => 'Pcs',
                    'Packet' => 'Packet',
                    'Pound' => 'Pound',
                    'Prs' => 'Prs',
                    'Refile' => 'Refile',
                    'Rft' => 'Rft',
                    'Rim' => 'Rim',
                    'Roll' => 'Roll',
                    'Set' => 'Set',
                    'Sft' => 'Sft',
                    'Yard' => 'Yard',
                ),
            ))
            ->add('category', 'entity', array(
                'required'    => true,
                'empty_value' => '---Select parent category---',
                'attr'=>array('class'=>'category m-wrap span12 select2'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required'))
                ),
                'class' => 'ProductProductBundle:Category',
                'property' => 'nestedLabel',
                'choices'=> $this->categoryChoiceList()
            ))
            ->add('file')
            ->add('status')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\InventoryBundle\Entity\Product'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appstore_bundle_inventorybundle_product';
    }

    /**
     * @return mixed
     */
    protected function categoryChoiceList()
    {

        return $categoryTree = $this->em->getUseInventoryItemCategory($this->inventoryConfig);

    }
}
