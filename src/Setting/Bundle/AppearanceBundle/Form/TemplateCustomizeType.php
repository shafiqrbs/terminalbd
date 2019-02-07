<?php

namespace Setting\Bundle\AppearanceBundle\Form;

use Setting\Bundle\ToolBundle\Form\SocialIconType;
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

            ->add('showSocialIcon')
            ->add('mobileLogin')
            ->add('mobileShowLogo')
            ->add('mobileHomeShowLogo')
            ->add('topBar')
            ->add('footerBlock')
            ->add('showCalendar')
            ->add('showNewsLetter')
            ->add('showEmail')
            ->add('showMobile')
            ->add('showSearch')
            ->add('showLogin')
            ->add('showSidebar')
            ->add('sidebarTooltip')
            ->add('menuBold')
            ->add('sidebarTitle','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter sidebar title')))
            ->add('topBarContent','textarea', array('attr'=>array('class'=>'m-wrap span12','rows'=>2,'placeholder'=>'Enter top bar text or button')))
            ->add('sidebarPosition', 'choice', array(
                'attr'=>array('class'=>'span12'),
                'choices' => array('' => '---Select One---','left' => 'left',  'right' => 'right'),
            ))

            ->add('socialIconType', 'choice', array(
                'attr'=>array('class'=>'span12'),
                'choices' => array(
                	'' => '---Select Icon Type ---',
	                'icon-fadein' => 'Icon Fadein',
	                'icon-loop' => 'Icon Loop',
	                'icon-square' => 'Icon Square',
	                'icon-circle' => 'Icon Circle',
                ),
            ))

            ->add('socialIconPosition', 'choice', array(
                'attr'=>array('class'=>'span12'),
                'choices' => array('' => '---Select Icon Position---','menu-right' => 'Menu-Right','top-left' => 'Top-Left','top-center' => 'Top-Center','top-right' => 'Top-Right'),
            ))

            ->add('topIconPosition', 'choice', array(
                'attr'=>array('class'=>'span12'),
                'choices' => array('' => '---Contact Position---','menu-right' => 'Menu-Right','text-left' => 'Left','text-center' => 'Center','text-right' => 'Right'),
            ))

            ->add('topTextPosition', 'choice', array(
                'attr'=>array('class'=>'span12'),
                'choices' => array('' => '---Top Text Position---','menu-right' => 'Menu-Right','text-left' => 'Left','text-center' => 'Center','text-right' => 'Right'),
            ))

            ->add('breadcrumb')
	        ->add('breadcrumbHeight','number', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Breadcrumb height')))

	        ->add('breadcrumbPosition', 'choice', array(
                'attr'=>array('class'=>' span12'),
                'choices' => array('' => 'Select Breadcrumb Position', 'flat-left' => 'Flat-Left','flat-right' => 'Flat-Right',  'left' => 'Left','center' => 'Center'),
            ))
            ->add('breadcrumbFontSize', 'choice', array(
                'attr'=>array('class'=>' span12'),
                'choices' => array('' => 'Font Size', '10px' => '10px',  '12px' => '12px','14px' => '14px', '16px' => '16px','18px' => '18px',  '20px' => '20px'),
            ))
            ->add('breadcrumbBg','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default',
                'placeholder'=>'')
            ))
            ->add('breadcrumbActiveBg','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default',
                'placeholder'=>'')
            ))
            ->add('breadcrumbHome','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default',
                'placeholder'=>'')
            ))
            ->add('breadcrumbColor','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default',
                'placeholder'=>'')
            ))
           /* ->add('breadcrumbBorderColor','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default',
                'placeholder'=>'')
            ))*/
            
            ->add('siteBgColor','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default',
                'placeholder'=>'')
            ))

            ->add('sidebarColor','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default',
                'placeholder'=>'')
            ))

            ->add('siteTitle','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Site page welcome text')))
            ->add('siteTitleSize', 'choice', array(
                'attr'=>array('class'=>'span12'),
                'choices' => array('H1' => 'H1','H2' => 'H2','H3' => 'H3','H4' => 'H4'),
            ))
            ->add('siteTitleColor','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default',
                'placeholder'=>'')
            ))
            ->add('siteSlogan','textarea', array('attr'=>array('class'=>'m-wrap span12','row'=> 4,'placeholder'=>'Site slogan')))
            ->add('homeBgColor','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default',
                'placeholder'=>'')
            ))

            ->add('homeAnchorColor','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default',
                'placeholder'=>'')
            ))

            ->add('homeAnchorColorHover','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default',
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
            ->add('carouselHeight', 'choice', array(
                'attr'=>array('class'=>'span12'),
                'choices' => array(
                    '720px'     => '720px',
                    '628px'     => '628px',
                    '444px'     => '444px',
                    '360px'     => '360px',
                ),
            ))
            ->add('mobileCarouselHeight', 'choice', array(
                'attr'=>array('class'=>'span12'),
                'choices' => array(
                    '280px'     => '280px',
                    '320px'     => '320px',
                    '380px'     => '380px',
                    '420px'     => '420px',
                ),
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
                'class'=>'m-wrap span10 colorpicker-default',
                'placeholder'=>'')
            ))
            ->add('anchorHoverColor','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default',
                'placeholder'=>'')
            ))

            ->add('buttonBgColor','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default',
                'placeholder'=>'')
            ))

            ->add('buttonBgColorHover','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default',
                'placeholder'=>'')
            ))
            ->add('dividerBorderWidth','text', array('attr'=>array('class'=>'m-wrap span12 numeric','placeholder'=>'Divider border width')))
            ->add('dividerBorder','text', array('attr'=>array('class'=>'m-wrap span12 numeric','placeholder'=>'Border size')))

            ->add('dividerAfterColor','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default',
                'placeholder'=>'')
            ))

            ->add('dividerBeforeColor','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default',
                'placeholder'=>'')
            ))

            ->add('dividerFontColor','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default',
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
                'class'=>'m-wrap span10 colorpicker-default',
                'placeholder'=>'')
            ))

            ->add('subPageBgColor','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default',
                'placeholder'=>'')
            ))
            ->add('topBgColor','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default',
                'placeholder'=>'')
            ))
            ->add('headerBgColor','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default',
                'placeholder'=>'')
            ))

            ->add('headerBorderColor','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default',
                'placeholder'=>'')
            ))

            ->add('headerBorderHeight', 'choice', array(
                'attr'=>array('class'=>'span6 m-wrap'),
                'choices' => array('1px' => '1px',  '2px' => '2px', '3px' => '3px', '4px' => '4px',  '5px' => '5px'),
            ))

            ->add('menuBgColor','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default',
                'placeholder'=>'')
            ))

            ->add('menuBgColorHover','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default',
                'placeholder'=>'')
            ))
            ->add('subMenuWidth','text', array('attr'=>array('class'=>'m-wrap span6','placeholder'=>'Enter sub menu with')))
            ->add('menuLiAColor','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default',
                'placeholder'=>'')
            ))

            ->add('menuLiAHoverColor','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default',
                'placeholder'=>'')
            ))

            ->add('subMenuBgColor','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default','placeholder'=>'')
            ))

            ->add('subMenuBgColorHover','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default','placeholder'=>'')
            ))

            ->add('menuFontSize', 'choice', array(
                'attr'=>array('class'=>'span12 m-wrap'),
                'choices' => array('' => '---Select One---','10px' => '10px',  '11px' => '11px',  '12px' => '12px', '13px' => '13px', '14px' => '14px',  '15px' => '15px', '16px' => '16px', '17px' => '17px',  '18px' => '18px', '19px' => '19px', '20px' => '20px'),
            ))

            ->add('menuLetter', 'choice', array(
                'attr'=>array('class'=>'span10 m-wrap'),
                'choices' => array('uppercase' => 'Uppercase',  'capitalize' => 'Capitalize', 'lowercase' => 'Lowercase'),
            ))
            ->add('menuPosition', 'choice', array(
                'attr'=>array('class'=>'span10 m-wrap'),
                'choices' => array('' => 'Navbar-Default','navbar-right' => 'Navbar-Right','navbar-center' => 'Navbar-Center',  'brand-center' => 'Logo-Center','navbar-brand-top' => 'Logo-Center-Top',  'brand-center center-side' => 'Logo-Center Menu-Side'),
            ))

            ->add('menuTopMargin','text', array('attr'=>array(
                'class'=>'m-wrap span12 numeric',
                'placeholder'=>'')
            ))
            ->add('stickyMenuTopMargin','text', array('attr'=>array(
                'class'=>'m-wrap span12 numeric',
                'placeholder'=>'')
            ))

            ->add('bodyColor','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default',
                'placeholder'=>'')
            ))

            ->add('borderColor','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default',
                'placeholder'=>'')
            ))

            ->add('borderColorHover','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default',
                'placeholder'=>'')
            ))

            ->add('footerBgColor','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default',
                'placeholder'=>'')
            ))

            ->add('footerTextColor','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default',
                'placeholder'=>'')
            ))
            ->add('footerAnchorColor','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default',
                'placeholder'=>'')
            ))
            ->add('footerAnchorColorHover','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default',
                'placeholder'=>'')
            ))
            ->add('logoHeight','text', array('attr'=>array(
                'class'=>'m-wrap span10 numeric',
                'placeholder'=>'')
            ))
            ->add('logoWidth','text', array('attr'=>array(
                'class'=>'m-wrap span10 numeric',
                'placeholder'=>'')
            ))
            ->add('metaDescription','textarea', array('attr'=>array('class'=>'m-wrap span12','rows'=>4,'placeholder'=>'Enter meta description')))
            ->add('metaKeyword','textarea', array('attr'=>array('class'=>'m-wrap span12','rows'=>5,'placeholder'=>'Enter meta keywords')))
            ->add('mobileHeaderBgColor','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default',
                'placeholder'=>'')
            ))
            ->add('mobileMenuBgColor','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default',
                'placeholder'=>'')
            ))
            ->add('mobileMenuBgColorHover','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default',
                'placeholder'=>'')
            ))
            ->add('mobileMenuLiAColor','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default',
                'placeholder'=>'')
            ))
            ->add('mobileMenuLiAHoverColor','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default',
                'placeholder'=>'')
            ))
        ;
	    $builder->add('globalOption', new SocialIconType());

    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Setting\Bundle\AppearanceBundle\Entity\TemplateCustomize'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'templatecustomize';
    }
}
