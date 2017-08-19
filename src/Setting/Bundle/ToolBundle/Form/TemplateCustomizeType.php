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

            ->add('topBar')
            ->add('footerBlock')
            ->add('showCalendar')
            ->add('showEmail')
            ->add('showMobile')
            ->add('showSearch')
            ->add('showSidebar')
            ->add('sidebarTooltip')
            ->add('sidebarTitle','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter sidebar title')))
            ->add('sidebarPosition', 'choice', array(
                'attr'=>array('class'=>'span12'),
                'choices' => array('' => '---Select One---','left' => 'left',  'right' => 'right'),
            ))


            ->add('siteBgColor','text', array('attr'=>array(
                'class'=>'m-wrap span12 colorpicker-default',
                'placeholder'=>'')
            ))

            ->add('siteTitle','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Site page welcome text')))
            ->add('siteTitleSize', 'choice', array(
                'attr'=>array('class'=>'span12'),
                'choices' => array('H1' => 'H1','H2' => 'H2','H3' => 'H3','H4' => 'H4'),
            ))
            ->add('siteSlogan','textarea', array('attr'=>array('class'=>'m-wrap span12','row'=> 4,'placeholder'=>'Site slogan')))
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
                'attr'=>array('class'=>'span12'),
                'choices' => array('' => '---Select One---','10px' => '10px',  '12px' => '12px', '13px' => '13px', '14px' => '14px'),
            ))
            ->add('pagination', 'choice', array(
                'attr'=>array('class'=>'span12'),
                'choices' => array('bootstrap' => 'Bootstrap',  'nextPrev' => 'Next Previous', 'nextPrevDropDown' => 'Next Previous with drop down'),
            ))
            ->add('sliderPosition', 'choice', array(
                'attr'=>array('class'=>'span12'),
                'choices' => array(
                    'top-left'      => 'Top-Left',
                    'top-center'    => 'Top-Center',
                    'top-right'     => 'Top-Right',
                    'vertical'      => 'Vertical',
                    'bottom-left'   => 'Bottom-Left',
                    'bottom-center' => 'Bottom-Center',
                    'bottom-right'  => 'Bottom-Right'
                ),
            ))
            ->add('sliderLeftRightPosition','text', array('attr'=>array('class'=>'m-wrap span12 numeric','placeholder'=>'Slider Top/Bottom')))
            ->add('sliderTopBottomPosition','text', array('attr'=>array('class'=>'m-wrap span12 numeric','placeholder'=>'Slider Left/Right')))
            ->add('siteFontFamily', 'choice', array(
                'attr'=>array('class'=>'span12'),
                'choices' => array(
                    '' => '---Select font family---',
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
            ->add('dividerBorder','text', array('attr'=>array('class'=>'m-wrap span12 numeric','placeholder'=>'Border size')))

            ->add('dividerAfterColor','text', array('attr'=>array(
                'class'=>'m-wrap span12 colorpicker-default',
                'placeholder'=>'')
            ))

            ->add('dividerBeforeColor','text', array('attr'=>array(
                'class'=>'m-wrap span12 colorpicker-default',
                'placeholder'=>'')
            ))

            ->add('dividerFontColor','text', array('attr'=>array(
                'class'=>'m-wrap span12 colorpicker-default',
                'placeholder'=>'')
            ))
            ->add('dividerFontSize', 'choice', array(
                'attr'=>array('class'=>' span12'),
                'choices' => array('' => 'Font Size', '10px' => '10px',  '12px' => '12px','14px' => '14px', '16px' => '16px','18px' => '18px',  '20px' => '20px','22px' => '22px', '24px' => '24px', ),
            ))

            ->add('siteH1TextSize', 'choice', array(
                'attr'=>array('class'=>' span12'),
                'choices' => array('' => '---Select One---','30px' => '30px', '32px' => '32px','34px' => '34px', '36px' => '36px','38px' => '38px', '40px' => '40px','42px' => '42px', '44px' => '44px','46px' => '46px','48px' => '48px', '50px' => '50px'),
            ))

            ->add('siteH2TextSize', 'choice', array(
                'attr'=>array('class'=>' span12'),
                'choices' => array('' => '---Select One---','24px' => '24px',  '26px' => '26px','28px' => '28px', '30px' => '30px','32px' => '32px',  '34px' => '34px','36px' => '36px'),
            ))
            ->add('siteH3TextSize', 'choice', array(
                'attr'=>array('class'=>' span12'),
                'choices' => array('' => '---Select One---','18px' => '18px',  '20px' => '20px','22px' => '22px', '24px' => '24px', '26px' => '26px','28px' => '28px', '30px' => '30px'),
            ))
            ->add('siteH4TextSize', 'choice', array(
                'attr'=>array('class'=>' span12'),
                'choices' => array('' => '---Select One---','10px' => '10px',  '12px' => '12px','14px' => '14px', '16px' => '16px','18px' => '18px',  '20px' => '20px','22px' => '22px', '24px' => '24px'),
            ))

            ->add('siteTitleBgColor','text', array('attr'=>array(
                'class'=>'m-wrap span12 colorpicker-default',
                'placeholder'=>'')
            ))

            ->add('subPageBgColor','text', array('attr'=>array(
                'class'=>'m-wrap span12 colorpicker-default',
                'placeholder'=>'')
            ))
            ->add('topBgColor','text', array('attr'=>array(
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

            ->add('menuBgColorHover','text', array('attr'=>array(
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
                'attr'=>array('class'=>'span12'),
                'choices' => array('' => '---Select One---','10px' => '10px',  '12px' => '12px', '13px' => '13px', '14px' => '14px'),
            ))

            ->add('bodyColor','text', array('attr'=>array(
                'class'=>'m-wrap span12 colorpicker-default',
                'placeholder'=>'')
            ))

            ->add('borderColor','text', array('attr'=>array(
                'class'=>'m-wrap span12 colorpicker-default',
                'placeholder'=>'')
            ))

            ->add('borderColorHover','text', array('attr'=>array(
                'class'=>'m-wrap span12 colorpicker-default',
                'placeholder'=>'')
            ))

            ->add('footerBgColor','text', array('attr'=>array(
                'class'=>'m-wrap span12 colorpicker-default',
                'placeholder'=>'')
            ))

            ->add('footerTextColor','text', array('attr'=>array(
                'class'=>'m-wrap span12 colorpicker-default',
                'placeholder'=>'')
            ))
            ->add('footerAnchorColor','text', array('attr'=>array(
                'class'=>'m-wrap span12 colorpicker-default',
                'placeholder'=>'')
            ))
            ->add('footerAnchorColorHover','text', array('attr'=>array(
                'class'=>'m-wrap span12 colorpicker-default',
                'placeholder'=>'')
            ))

        ;
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
