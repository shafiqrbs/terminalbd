<?php

namespace Setting\Bundle\ToolBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class SidebarWidgetType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required'))

                )
            ))
            ->add('page', 'entity', array(
                'required'      => true,
                'multiple'      =>true,
                'expanded'      =>true,
                'class'         => 'Setting\Bundle\ContentBundle\Entity\Page',
                'property'      => 'name',
                'attr'          =>array('class'=>'m-wrap span12'),
                'query_builder' => function(EntityRepository $er){
                                            return $er->createQueryBuilder('e')
                                            ->where("e.status = 1")
                                            ->andWhere("e.module = 'page'")
                                            ->orderBy('e.name','ASC');
                }
            ))
            ->add('module', 'entity', array(
                'required'      => true,
                'multiple'      =>true,
                'expanded'      =>true,
                'class'         => 'Setting\Bundle\ToolBundle\Entity\Module',
                'property'      => 'name',
                'attr'          =>array('class'=>'m-wrap span12'),
                'query_builder' => function(EntityRepository $er){
                                            return $er->createQueryBuilder('c')
                                            ->andWhere("c.status = 1")
                                            ->orderBy('c.id','ASC');
                }
            ))
            ->add('status')

        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Setting\Bundle\ToolBundle\Entity\SidebarWidget'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'setting_bundle_toolbundle_sidebarwidget';
    }
}
