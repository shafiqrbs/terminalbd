<?php

namespace Appstore\Bundle\AccountingBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class AccountHeadSubType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12 tooltips','placeholder'=>'Enter account head name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),
                )
            ))
            ->add('parent', 'entity', [
                'class'     => 'Appstore\Bundle\AccountingBundle\Entity\AccountHead',
                'group_by'  => 'parent.name',
                'property'  => 'name',
                'attr'=>array('class'=>'span12 m-wrap'),
                'choice_translation_domain' => true,
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->join("e.parent",'c')
                        ->where("e.status = 1")
                        ->andWhere("c.isParent =1")
                        ->orderBy("e.name", "ASC");
                }
            ])
            ->add('status');
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\AccountingBundle\Entity\AccountHead'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appstore_bundle_accountingbundle_accounthead';
    }
}
