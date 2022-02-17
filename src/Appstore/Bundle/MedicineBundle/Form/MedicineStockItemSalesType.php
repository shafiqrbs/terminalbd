<?php

namespace Appstore\Bundle\MedicineBundle\Form;

use Appstore\Bundle\MedicineBundle\Entity\MedicineConfig;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class MedicineStockItemSalesType extends AbstractType
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
            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12 stockInput autoComplete2Medicine','placeholder'=>'Enter medicine name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),
                )
            ))
            ->add('openingQuantity','text', array('attr'=>array('class'=>'m-wrap span3 stockInput','placeholder'=>'Opening QTY','autoComplete'=>'off')))
            ->add('salesQuantity','text', array('attr'=>array('class'=>'m-wrap span3 stockInput','placeholder'=>'Sales QTY','autoComplete'=>'off')))
            ->add('salesPrice','text', array('attr'=>array('class'=>'m-wrap span3 stockInput','placeholder'=>'MRP','autoComplete'=>'off')))
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
