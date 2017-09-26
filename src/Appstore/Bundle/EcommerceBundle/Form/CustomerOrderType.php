<?php

namespace Appstore\Bundle\EcommerceBundle\Form;

use Doctrine\ORM\EntityRepository;
use Setting\Bundle\LocationBundle\Repository\LocationRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class CustomerOrderType extends AbstractType
{


    /** @var GlobalOption */
    /** @var  LocationRepository */

    public  $globalOption;
    public  $location;

    function __construct(GlobalOption $globalOption , LocationRepository $location)
    {
        $this->globalOption = $globalOption;
        $this->location = $location;
    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('address','text', array('attr'=>array('class'=>'m-wrap span12 tooltips', 'data-trigger' => 'hover','placeholder'=>'Enter  delivery address ie: Unit,Floor,House,Road,Area,Thana,District Etc','data-original-title'=>'Enter  delivery address ie: Unit,Floor,House,Road,Area,Thana,District Etc','autocomplete'=>'off')))
            ->add('deliveryDate','date', array('attr'=>array('class'=>'m-wrap span12 tooltips', 'data-trigger' => 'hover','placeholder'=>'Receive your product date(Approximately)','data-original-title'=>'Please receive your product date(Approximately).',),
                'constraints' =>array(new NotBlank(array('message'=>'Please input required')))
            ))
            ->add('location', 'entity', array(
                'required'    => false,
                'empty_value' => '---Select Location---',
                'attr'=>array('class'=>'select2 span12'),
                'class' => 'Setting\Bundle\LocationBundle\Entity\Location',
                'choices'=> $this->LocationChoiceList(),
                'choices_as_values' => true,
                'choice_label' => 'nestedLabel',
            ))
            ->add('process', 'choice', array(
                'attr'=>array('class'=>'span8 m-wrap'),
                'expanded'      =>false,
                'multiple'      =>false,
                'empty_value' => '---Process Status---',
                'choices' => array(
                    'created'       => 'Created',
                    'in-progress'       => 'In-progress',
                    'wfc'       => 'Wait for Confirm'
                ),
            ))
            ->add('cashOnDelivery');

    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\EcommerceBundle\Entity\Order'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'ecommerce_order';
    }

    protected function LocationChoiceList()
    {
        return  $this->location->getLocationOptionGroup();

    }
}
