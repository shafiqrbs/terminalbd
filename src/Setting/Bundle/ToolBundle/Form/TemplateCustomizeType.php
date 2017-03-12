<?php

namespace Setting\Bundle\ToolBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TemplateCustomizeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('siteBgColor','text', array('attr'=>array(
                'class'=>'m-wrap span12 colorpicker-default',
                'placeholder'=>'')
            ))

            ->add('siteFontSize', 'choice', array(
                'attr'=>array('class'=>'selectbox span12'),
                'choices' => array('10px' => '10px',  '12px' => '12px', '13px' => '13px', '14px' => '14px'),
            ))

            ->add('siteFontFamily', 'choice', array(
                'attr'=>array('class'=>'selectbox span12'),
                'choices' => array(
                    'Open Sans, sans-serif' => 'Open Sans, sans-serif',
                    'Helvetica, sans-serif' => 'Helvetica, sans-serif',
                    'Verdana' => 'Verdana',
                    'Gill Sans' => 'Gill Sans',
                    'Avantgarde' => 'Avantgarde',
                    'Helvetica Narrow' => 'Helvetica Narrow',
                    'Times' => 'Times',
                    'Times New Roman' => 'Times New Roman',
                    'Palatino' => 'Palatino',
                    )
            ))

            ->add('logoDisplayWebsite')


            ->add('anchorColor','text', array('attr'=>array(
                'class'=>'m-wrap span12 colorpicker-default',
                'placeholder'=>'')
            ))

            ->add('anchorHoverColor','text', array('attr'=>array(
                'class'=>'m-wrap span12 colorpicker-default',
                'placeholder'=>'')
            ))

            ->add('buttonBgColor','text', array('attr'=>array(
                'class'=>'m-wrap span12 colorpicker-default',
                'placeholder'=>'')
            ))

            ->add('buttonBgColorHover','text', array('attr'=>array(
                'class'=>'m-wrap span12 colorpicker-default',
                'placeholder'=>'')
            ))

            ->add('dividerBgColor','text', array('attr'=>array(
                'class'=>'m-wrap span12 colorpicker-default',
                'placeholder'=>'')
            ))

            ->add('dividerColor','text', array('attr'=>array(
                'class'=>'m-wrap span12 colorpicker-default',
                'placeholder'=>'')
            ))

            ->add('dividerTitleColor','text', array('attr'=>array(
                'class'=>'m-wrap span12 colorpicker-default',
                'placeholder'=>'')
            ))


            ->add('siteH1TextSize', 'choice', array(
                'attr'=>array('class'=>'selectbox span12'),
                'choices' => array('10px' => '10px',  '12px' => '12px', '13px' => '13px', '14px' => '14px'),
            ))


            ->add('siteH2TextSize', 'choice', array(
                'attr'=>array('class'=>'selectbox span12'),
                'choices' => array('10px' => '10px',  '12px' => '12px', '13px' => '13px', '14px' => '14px'),
            ))
            ->add('siteH3TextSize', 'choice', array(
                'attr'=>array('class'=>'selectbox span12'),
                'choices' => array('10px' => '10px',  '12px' => '12px', '13px' => '13px', '14px' => '14px'),
            ))
            ->add('siteH4TextSize', 'choice', array(
                'attr'=>array('class'=>'selectbox span12'),
                'choices' => array('10px' => '10px',  '12px' => '12px', '13px' => '13px', '14px' => '14px'),
            ))



            ->add('siteTitleBgColor','text', array('attr'=>array(
                'class'=>'m-wrap span12 colorpicker-default',
                'placeholder'=>'')
            ))

            ->add('subPageBgColor','text', array('attr'=>array(
                'class'=>'m-wrap span12 colorpicker-default',
                'placeholder'=>'')
            ))


            ->add('headerBgColor','text', array('attr'=>array(
                'class'=>'m-wrap span12 colorpicker-default',
                'placeholder'=>'')
            ))

            ->add('menuBgColor','text', array('attr'=>array(
                'class'=>'m-wrap span12 colorpicker-default',
                'placeholder'=>'')
            ))

            ->add('menuLiAColor','text', array('attr'=>array(
                'class'=>'m-wrap span12 colorpicker-default',
                'placeholder'=>'')
            ))

            ->add('menuLiAHoverColor','text', array('attr'=>array(
                'class'=>'m-wrap span12 colorpicker-default',
                'placeholder'=>'')
            ))

            ->add('menuFontSize', 'choice', array(
                'attr'=>array('class'=>'selectbox span12'),
                'choices' => array('' => '---Select One---','10px' => '10px',  '12px' => '12px', '13px' => '13px', '14px' => '14px'),
            ))

            ->add('bodyColor','text', array('attr'=>array(
                'class'=>'m-wrap span12 colorpicker-default',
                'placeholder'=>'')
            ))


            ->add('footerBgColor','text', array('attr'=>array(
                'class'=>'m-wrap span12 colorpicker-default',
                'placeholder'=>'')
            ))
            ->add('borderColor','text', array('attr'=>array(
                'class'=>'m-wrap span12 colorpicker-default',
                'placeholder'=>'')
            ))

            ->add('footerTextColor','text', array('attr'=>array(
                'class'=>'m-wrap span12 colorpicker-default',
                'placeholder'=>'')
            ));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Setting\Bundle\ToolBundle\Entity\TemplateCustomize'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'setting_bundle_toolbundle_templatecustomize';
    }
}
