<?php

namespace Appstore\Bundle\MedicineBundle\Form;

use Doctrine\ORM\EntityRepository;
use Setting\Bundle\LocationBundle\Repository\LocationRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class SalesTemporaryItemType extends AbstractType
{


    /** @var  GlobalOption */
    private $globalOption;


    function __construct(GlobalOption $globalOption)
    {
        $this->globalOption     = $globalOption;
        $this->config     = $globalOption->getMedicineConfig();
    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('stockName','text', array('attr'=>array('class'=>'m-wrap span12 select2StockMedicine input','placeholder'=>'Enter stock medicine name')))
            ->add('itemPercent', 'choice', array(
                'attr'=>array('class'=>'m-wrap span3 input'),
                'expanded'      =>false,
                'empty_value' => '-Disc(%)-',
                'mapped' => false,
                'required'    => false,
                'multiple'      =>false,
                'choices' => array(
                    1=>1,
                    2=>2,
                    3=>3,
                    4=>4,
                    5=>5,
                    6=>6,
                    7=>7,
                    8=>8,
                    9=>9,
                    10=>10,
                    11=>11,
                    12=>12,
                    13=>13,
                    14=>14,
                    15=>15,
                    16=>16,
                    17=>17,
                    18=>18,
                    19=>19,
                    20=>20,
                ),
            ))
            ->add('salesPrice','text', array('attr'=>array('class'=>'m-wrap span4 input','autocomplete'=>'off','placeholder'=>'MRP')))
            ->add('quantity','number', array('attr'=>array('class'=>'m-wrap span3 form-control input-number input','autocomplete'=>'off','placeholder'=>'quantity')))
           ;

            if($this->config->isPurchaseItem() == 1) {
                $builder->add('purchaseItem', 'choice', array(
                    'attr' => array('class' => 'm-wrap span12 input'),
                    'expanded' => false,
                    'multiple' => false,
                    'empty_value' => '---Expiry Date---',
                    'choices' => array(),
                ));
            }
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
        return 'salesTemporaryItem';
    }
}
