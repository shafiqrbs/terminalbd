<?php

namespace Appstore\Bundle\InventoryBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class InventoryConfigType extends AbstractType
{



    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('salesReturnDayLimit','integer',array('attr'=>array('class'=>'m-wrap span4 numeric')))
            ->add('vatPercentage','integer',array('attr'=>array('class'=>'m-wrap span4 numeric')))
            ->add('deliveryProcess', 'choice', array(
                'choices' => array(
                    'Pos' => 'Point of sales(POS)',
                    'ManualPos' => 'Manual Sales System',
                    'Delivery' => 'Delivery',
                    'TemporaryDelivery' => 'Temporary Delivery',
                ),
                'required'    => true,
                'multiple'    => true,
                'expanded'  => true,
                'empty_data'  => null,
            ))
            ->add('vatEnable')
            ->add('isColor')
            ->add('isSize')
        ;
    }


    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\InventoryBundle\Entity\InventoryConfig'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appstore_bundle_inventorybundle_inventoryconfig';
    }
}
