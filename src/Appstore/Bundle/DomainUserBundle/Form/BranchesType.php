<?php

namespace Appstore\Bundle\DomainUserBundle\Form;

use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class BranchesType extends AbstractType
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
            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Branch name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please enter branch name '))
            )))
            ->add('address','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Branch address'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please enter  branch address'))
            )))
            ->add('mobile','text', array('attr'=>array('class'=>'m-wrap span12 mobile','placeholder'=>'Add  mobile no'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please enter  mobile no'))
            )))
            ->add('email','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Add  email address')))
            ->add('branchManager', 'entity', array(
                'expanded'      =>false,
                'multiple'      =>false,
                'required'    => true,
                'class' => 'Core\UserBundle\Entity\User',
                'property' => 'username',
                'attr'=>array('class'=>'span12 select2'),
                'empty_value' => '---Choose assign manager ---',
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required'))
                ),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('u')
                        ->where("u.isDelete IS NULL")
                        ->andWhere("u.globalOption =".$this->globalOption->getId())
                        ->orderBy("u.username", "ASC");
                }
            ))
            ->add('status');
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\DomainUserBundle\Entity\Branches'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appstore_bundle_domainUserbundle_branches';
    }
}
