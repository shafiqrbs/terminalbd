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

            ->add('expirationDate','text', array('attr'=>array('class'=>'m-wrap span4 dateCalendar','placeholder'=>'Expiration date')))
            ->add('medicineStock', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\MedicineBundle\Entity\MedicineStock',
                'empty_value' => '---Choose a medicine ---',
                'property' => 'name',
                'attr'=>array('class'=>'span12 select2'),
                'constraints' =>array( new NotBlank(array('message'=>'Please select your medicine name')) ),
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
            'data_class' => 'Appstore\Bundle\MedicineBundle\Entity\MedicinePurchaseItem'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appstore_bundle_dmspurchase';
    }
}
