<?php

namespace Appstore\Bundle\MedicineBundle\Form;

use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class SalesItemType extends AbstractType
{


    public function __construct(GlobalOption $option)
    {
        $this->option = $option;

    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('barcode', 'choice', array(
                'attr'=>array('class'=>'m-wrap span8 input'),
                'expanded'      =>false,
                'multiple'      =>false,
                'empty_value' => '---Choose barcode---',
                'choices' => array(),
            ))

            ->add('salesPrice','text', array('attr'=>array('class'=>'m-wrap span4 input','placeholder'=>'Sales price')))
            ->add('quantity','number', array('attr'=>array('class'=>'m-wrap span5 form-control input-number input','placeholder'=>'quantity')))
            ->add('medicineStock', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\MedicineBundle\Entity\MedicineStock',
                'empty_value' => '---Choose a medicine ---',
                'property' => 'medicineStockSkuQuantity',
                'attr'=>array('class'=>'span12 select2 input'),
                'constraints' =>array( new NotBlank(array('message'=>'Please select medicine name')) ),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('wt')
                        ->where("wt.status = 1")
                        ->andWhere("wt.medicineConfig =".$this->option->getMedicineConfig()->getId());
                },
            ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\MedicineBundle\Entity\MedicineSalesItem'
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
