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

            ->add('showSidebar')
            ->add('sidebarTooltip')
            ->add('sidebarTitle','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter sidebar title')))
            ->add('sidebarPosition', 'choice', array(
                'attr'=>array('class'=>'span12'),
                'choices' => array('left' => 'left',  'right' => 'right'),
            ))

            ->add('siteBgColor','text', array('attr'=>array(
                'class'=>'m-wrap span12 colorpicker-default',
                'placeholder'=>'')
            ))

            ->add('homeBgColor','text', array('attr'=>array(
                'class'=>'m-wrap span12 colorpicker-default',
                'placeholder'=>'')
            ))

            ->add('homeAnchorColor','text', array('attr'=>array(
                'class'=>'m-wrap span12 colorpicker-default',
                'placeholder'=>'')
            ))

            ->add('homeAnchorColorHover','text', array('attr'=>array(
                'class'=>'m-wrap span12 colorpicker-default',
                'placeholder'=>'')
            ))


            ->add('siteFontSize', 'choice', array(
                'attr'=>array('class'=>'selectbox span12'),
                'choices' => array('10px' => '10px',  '12px' => '12px', '13px' => '13px', '14px' => '14px'),
            ))

            ->add('titleTextAlign', 'choice', array(
                'attr'=>array('class'=>'selectbox span12'),
                'choices' => array('left' => 'Left',  'center' => 'Center', 'right' => 'Right'),
            ))

            ->add('titleHeight','text', array('attr'=>array('class'=>'m-wrap span12 numeric','placeholder'=>'Title height')))
            ->add('titleMarginBottom','text', array('attr'=>array('class'=>'m-wrap span12 numeric','placeholder'=>'Title border margin')))
            ->add('sliderTopPosition','text', array('attr'=>array('class'=>'m-wrap span12 numeric','placeholder'=>'Slider Top')))
            ->add('sliderRightPosition','text', array('attr'=>array('class'=>'m-wrap span12 numeric','placeholder'=>'Slider Right')))
            ->add('titleBgColor','text', array('attr'=>array(
                'class'=>'m-wrap span12 colorpicker-default',
                'placeholder'=>'')
            ))
            ->add('titleFontColor','text', array('attr'=>array(
                'class'=>'m-wrap span12 colorpicker-default',
                'placeholder'=>'')
            ))
            ->add('titleBorderColor','text', array('attr'=>array(
                'class'=>'m-wrap span12 colorpicker-default',
                'placeholder'=>'')
            ))
            ->add('titleFontSize', 'choice', array(
                'attr'=>array('class'=>'selectbox span12'),
                'choices' => array('' => 'Font Size', '10px' => '10px',  '12px' => '12px','14px' => '14px', '16px' => '16px','18px' => '18px',  '20px' => '20px','22px' => '22px', '24px' => '24px', ),
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
