<?php

namespace Appstore\Bundle\ProcurementBundle\Form;

use Appstore\Bundle\InventoryBundle\Entity\InventoryConfig;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class PurchaseRequisitionType extends AbstractType
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


            ->add('memo','text', array('attr'=>array('class'=>'m-wrap span12 ','required' => true ,'label' => 'form.name','placeholder'=>'Memo no'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please add  memo no'))
            )))
            ->add('file','file',array('attr'=>array('class'=>'default span12')))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\ProcurementBundle\Entity\PurchaseRequisition'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'purchaseRequisition';
    }
}
