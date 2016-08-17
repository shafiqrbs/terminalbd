<?php

namespace Setting\Bundle\ContentBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class BlogType extends AbstractType
{


    private $globalOption;

    public function __construct($globalOption)
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
            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter blogger name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),

                )
            ))
             ->add('title','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter blog title'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),

                )
            ))
            ->add('file','file', array('attr'=>array('class'=>'default')))
            ->add('content','textarea', array('attr'=>array('class'=>'span12 wysihtml5 m-wrap','rows'=>15)))
            ->add('photo_gallery', 'entity', array(
                'required'    => false,
                'class' => 'Setting\Bundle\MediaBundle\Entity\PhotoGallery',
                'empty_value' => '---Select Photo Gallery---',
                'property' => 'name',
                'attr'=>array('class'=>'m-wrap span12'),
                'query_builder' => function(EntityRepository $er){
                        return $er->createQueryBuilder('o')
                            ->andWhere("o.status = 1")
                            ->andWhere("o.globalOption = $this->globalOption ")
                            ->orderBy('o.name','ASC');
                    },
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
            'data_class' => 'Setting\Bundle\ContentBundle\Entity\Blog'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'setting_bundle_contentbundle_blog';
    }
}
