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

            ->add('item','text', array(
                'attr'=>array('class'=>'itemInput m-wrap span12 selectAllItem','placeholder'=>'Select Item Name'),
                'mapped'=>'false'
            ))

            ->add('quantity','text', array('attr'=>array('class'=>'itemInput m-wrap span12','placeholder'=>'Quantity'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Add item quantity')))))

            ->add('salesPrice','text', array('attr'=>array('class'=>'itemInput m-wrap span6','placeholder'=>'Sales price'),
                ))

            ->add('purchaseSubTotal','text', array('attr'=>array('class'=>'itemInput m-wrap span12','placeholder'=>'Purchase price'),
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
        return 'purchaseitem';
    }
}
