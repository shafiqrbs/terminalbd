<?php

namespace Appstore\Bundle\TicketBundle\Form;


use Appstore\Bundle\TicketBundle\Entity\TicketConfig;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class SettingType extends AbstractType
{



    /** @var  TicketConfig  */

    private $ticketConfig;

    function __construct(TicketConfig  $ticketConfig)
    {

        $this->ticketConfig = $ticketConfig;
    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12','autocomplete'=>'off','placeholder'=>'Enter setting name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please enter setting name'))
                ))
            )
            ->add('type', 'entity', array(
                'required'    => false,
                'class' => 'Appstore\Bundle\TicketBundle\Entity\SettingType',
                'property' => 'name',
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please select required'))
                ),
                'empty_value' => '---Choose a setting type ---',
                'attr'=>array('class'=>'span12 m-wrap'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                    ->where("e.status = 1")
                    ->andWhere('e.config ='.$this->ticketConfig->getId())
                    ->orderBy("e.name","ASC");
                }
            ))

        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\TicketBundle\Entity\Setting'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'setting';
    }


}
