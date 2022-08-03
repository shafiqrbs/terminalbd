<?php

namespace Appstore\Bundle\AssetsBundle\Form;


use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class ReceiveType extends AbstractType
{



    /** @var  $option GlobalOption */

    public  $option;


    public function __construct(GlobalOption $option)
    {
        $this->option = $option;
        $this->config = $option->getAssetsConfig()->getId();

    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('receiveDate', DateType::class, array(
                'widget' => 'single_text',
                'attr' => array('class'=>'m-wrap span12'),
                'view_timezone' => 'Asia/Dhaka'))
            ->add('challanNo','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter challan no')))
            ->add('gatepass','text', array('attr'=>array('class'=>'m-wrap span12 inputs','placeholder'=>'Enter gate pass')))
            ->add('lcNo','text', array('attr'=>array('class'=>'m-wrap span12 inputs','placeholder'=>'Enter lc no')))
            ->add('vatPercent','text', array('attr'=>array('class'=>'m-wrap span12 inputs vatPercent','placeholder'=>'Enter VAT Percent')))
            ->add('remark','textarea', array('attr'=>array('class'=>'m-wrap span12 inputs','rows'=>3,'placeholder'=>'Enter remark')))
            ->add('file');
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\AssetsBundle\Entity\Receive'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'receive';
    }


}
