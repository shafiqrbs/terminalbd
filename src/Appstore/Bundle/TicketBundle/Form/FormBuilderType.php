<?php

namespace Appstore\Bundle\TicketBundle\Form;


use Appstore\Bundle\TicketBundle\Entity\TicketConfig;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class FormBuilderType extends AbstractType
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

            ->add('userType', 'entity', array(
                'required'    => false,
                'class' => 'Appstore\Bundle\TicketBundle\Entity\Setting',
                'property' => 'name',
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please select required'))
                ),
                'empty_value' => '---Choose a setting type ---',
                'attr'=>array('class'=>'span12 m-wrap'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->join("e.type",'p')
                        ->where("e.status = 1")
                        ->andWhere("p.slug = 'user-type'")
                        ->andWhere('e.config ='.$this->ticketConfig->getId())
                        ->orderBy("e.name","ASC");
                }
            ))
            ->add('module', 'entity', array(
                'required'    => false,
                'class' => 'Appstore\Bundle\TicketBundle\Entity\Setting',
                'property' => 'name',
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please select required'))
                ),
                'empty_value' => '---Choose a setting type ---',
                'attr'=>array('class'=>'span12 m-wrap'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->join("e.type",'p')
                        ->where("e.status = 1")
                        ->andWhere("p.slug = 'module'")
                        ->andWhere('e.config ='.$this->ticketConfig->getId())
                        ->orderBy("e.name","ASC");
                }
            ))

            ->add('process', 'entity', array(
                'required'    => false,
                'class' => 'Appstore\Bundle\TicketBundle\Entity\Setting',
                'property' => 'name',
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please select required'))
                ),
                'empty_value' => '---Choose a setting type ---',
                'attr'=>array('class'=>'span12 m-wrap'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->join("e.type",'p')
                        ->where("e.status = 1")
                        ->andWhere("p.slug = 'process'")
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
            'data_class' => 'Appstore\Bundle\TicketBundle\Entity\TicketFormBuilder'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'formBuilder';
    }


}
