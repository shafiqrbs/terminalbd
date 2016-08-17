<?php

namespace Appstore\Bundle\InventoryBundle\Form;

use Appstore\Bundle\InventoryBundle\Entity\SalesItem;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class SalesType extends AbstractType
{



    /** @var GlobalOption */

    public  $globalOption;

    function __construct(GlobalOption $globalOption)
    {
        $this->globalOption = $globalOption;
    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('mobile','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Add your customer mobile no','data-original-title'=>'Please start typing code/name for suggestions or just scan barcode','autocomplete'=>'off'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')))
            ))
            ->add('paymentMethod', 'choice', array(
                'attr'=>array('class'=>'span12 select2'),
                'choices' => array(
                    'Cash' => 'Cash',
                    'Cheque' => 'Cheque',
                    'Gift card' => 'Gift card',
                    'Bkash' => 'Bkash',
                    'Payment Card' => 'Payment Card',
                    'Other' => 'Other'
                ),
            ))
        ;

    }

}
