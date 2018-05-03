<?php

namespace Appstore\Bundle\InventoryBundle\Form;

use Appstore\Bundle\InventoryBundle\Entity\InventoryConfig;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class PurchaseItemType extends AbstractType
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
            ->add('serialNo','textarea', array('attr'=>array('class'=>'m-wrap span12','rows'=>3,'placeholder'=>'Attribute name')))
            ->add('expiredDate', 'date', array(
                'widget' => 'single_text',
                'placeholder' => array(
                    'mm' => 'mm', 'dd' => 'dd','YY' => 'YY'

                ),
                'format' => 'dd-MM-yyyy',
                'attr' => array('class'=>'m-wrap span12 dateCalendar'),
                'view_timezone' => 'Asia/Dhaka'))

            ->add('assuranceFromVendor', 'choice', array(
                'attr'=>array('class'=>'span12 m-wrap'),
                'choices' => array(
                    '1 Month' => '1 Month',
                    '3 Month' => '3 Month',
                    '4 Month' => '4 Month',
                    '6 Month' => '6 Month',
                    '1 Year' => '1 Year',
                    '1 Year 6 Month' => '1 Year 6 Month',
                    '2 Year' => '2 Year',
                    '2 Year 6 Month' => '2 Year 6 Month',
                    '3 Year' => '3 Year',
                    '3 Year 6 Month' => '3 Year 6 Month',
                    '4 Year' => '4 Year',
                    '4 Year 6 Month' => '4 Year 6 Month',
                    '5 Year' => '5 Year',
                    '5 Year 6 Month' => '5 Year 6 Month',
                    'Product Life Time' => 'Product Life Time',
                    'No Warranty' => 'No Warranty',
                ),
            ))
            ->add('assuranceToCustomer', 'choice', array(
                'attr'=>array('class'=>'span12 m-wrap'),
                'choices' => array(
                    '1 Month' => '1 Month',
                    '3 Month' => '3 Month',
                    '4 Month' => '4 Month',
                    '6 Month' => '6 Month',
                    '1 Year' => '1 Year',
                    '1 Year 6 Month' => '1 Year 6 Month',
                    '2 Year' => '2 Year',
                    '2 Year 6 Month' => '2 Year 6 Month',
                    '3 Year' => '3 Year',
                    '3 Year 6 Month' => '3 Year 6 Month',
                    '4 Year' => '4 Year',
                    '4 Year 6 Month' => '4 Year 6 Month',
                    '5 Year' => '5 Year',
                    '5 Year 6 Month' => '5 Year 6 Month',
                    'Product Life Time' => 'Product Life Time',
                    'No Warranty' => 'No Warranty',
                ),
            ))
            ->add('assuranceType', 'choice', array(
                'attr'=>array('class'=>'span12 m-wrap'),
                'choices' => array(
                    'Warranty' => 'Warranty',
                    'Grantee' => 'Grantee',
                    'No Warranty' => 'No Warranty',
                ),
            ));
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
