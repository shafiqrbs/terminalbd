<?php

namespace Appstore\Bundle\MedicineBundle\Form;

use Appstore\Bundle\HospitalBundle\Entity\HospitalConfig;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class PurchaseItemType extends AbstractType
{
    /** @var  HospitalConfig */
    public  $option;

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

            ->add('stockName','text', array('attr'=>array('class'=>'m-wrap span12 select2StockMedicine input','placeholder'=>'Enter stock medicine name')))
            ->add('expirationStartDate','text', array('attr'=>array('class'=>'m-wrap span3 dateCalendar input','placeholder'=>'Expiry start date')))
            ->add('expirationEndDate','text', array('attr'=>array('class'=>'m-wrap span3 dateCalendar input','placeholder'=>'Expiry end date')))
            ->add('salesPrice','text', array('attr'=>array('class'=>'m-wrap span3 input','placeholder'=>'MRP')))
            ->add('purchasePrice','text', array('attr'=>array('class'=>'m-wrap span3 input','placeholder'=>'Purchase price')))
            ->add('quantity','number', array('attr'=>array('class'=>'m-wrap span6 form-control input-number input','placeholder'=>'quantity')))
            ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\MedicineBundle\Entity\MedicinePurchaseItem'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'purchaseItem';
    }
}
