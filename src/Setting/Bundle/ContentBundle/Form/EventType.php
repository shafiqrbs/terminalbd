<?php

namespace Setting\Bundle\ContentBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class EventType extends AbstractType
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
            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter title'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),

                )
            ))
            ->add('contactPerson','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter contact person'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),

                )
            ))
            ->add('mobile','text', array('attr'=>array('class'=>'m-wrap span12 mobile','placeholder'=>'Enter mobile no'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),

                )
            ))
        
            ->add('email','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter email address'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),

                )
            ))
            ->add('content','textarea', array('attr'=>array('class'=>'wysihtml5 m-wrap span12','rows'=>8)))
            ->add('address','text', array('attr'=>array('class'=>'m-wrap span12')))
            ->add('additionalPhone','text', array('attr'=>array('class'=>'m-wrap span12')))
            ->add('file','file', array('attr'=>array('class'=>'default')))
            ->add('startDate','date', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>''),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required'))
                ),
                'years'=> array('2015', '2016', '2017', '2018', '2019', '2020', '2021', '2022', '2023', '2024', '2025'),
                'widget' => 'single_text',
                // this is actually the default format for single_text
                'format' => 'yyyy-MM-dd',

            ))
            ->add('endDate','date', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>''),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required'))
                ),
                'years'=> array('2015', '2016', '2017', '2018', '2019', '2020', '2021', '2022', '2023', '2024', '2025'),
                'widget' => 'single_text',
                // this is actually the default format for single_text
                'format' => 'yyyy-MM-dd',

            ))
            ->add('startHour','text', array('attr'=>array('class'=>'m-wrap small clockface_1 span10')))
            ->add('endHour','text', array('attr'=>array('class'=>'m-wrap small clockface_1 span10')))
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
            'data_class' => 'Setting\Bundle\ContentBundle\Entity\Page'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'setting_bundle_contentbundle_page';
    }
}
