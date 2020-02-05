<?php

namespace Setting\Bundle\AppearanceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * TemplateCustomize
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Setting\Bundle\AppearanceBundle\Repository\TemplateCustomizeRepository")
 */
class TemplateCustomize
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="templateCustomize")
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/

    protected $globalOption;

    /**
     * @ORM\OneToMany(targetEntity="Setting\Bundle\AppearanceBundle\Entity\SocialIcon", mappedBy="templateCustomize")
     **/
    protected $socialIcons;

    /**
     * @var boolean
     *
     * @ORM\Column(name="topBar", type="boolean", nullable=true)
     */
    private $topBar = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="footerBlock", type="boolean", nullable=true)
     */
    private $footerBlock = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="showCalendar", type="boolean", nullable=true)
     */
    private $showCalendar = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="showSidebar", type="boolean", nullable=true)
     */
    private $showSidebar = false;

    /**
     * @var string
     *
     * @ORM\Column(name="sidebarTitle", type="string", nullable=true)
     */
    private $sidebarTitle = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="showSidebarTooltip", type="boolean", nullable=true)
     */
    private $sidebarTooltip = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="showLogin", type="boolean", nullable=true)
     */
    private $showLogin = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="menuBold", type="boolean", nullable=true)
     */
    private $menuBold = false;


    /**
     * @var string
     *
     * @ORM\Column(name="sidebarPosition", type="string", nullable=true)
     */
    private $sidebarPosition = 'left';

    /**
     * @var string
     *
     * @ORM\Column(name="pricePrefix", type="string", nullable=true)
     */
    private $pricePrefix = 'Tk.';

    /**
     * @var boolean
     *
     * @ORM\Column(name="buyButton", type="boolean", nullable=true)
     */
    private $buyButton = false;


    /**
     * @var boolean
     *
     * @ORM\Column(name="showSearch", type="boolean", nullable=true)
     */
    private $showSearch = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="contactButton", type="boolean", nullable=true)
     */
    private $contactButton = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="showMobile", type="boolean", nullable=true)
     */
    private $showMobile = true;


    /**
     * @var boolean
     *
     * @ORM\Column(name="contactForm", type="boolean", nullable=true)
     */
    private $contactForm = false;


    /**
     * @var boolean
     *
     * @ORM\Column(name="registrationForm", type="boolean", nullable=true)
     */
    private $registrationForm = true;


    /**
     * @var boolean
     *
     * @ORM\Column(name="mobileShowLogo", type="boolean", nullable=true)
     */
    private $mobileShowLogo = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="mobileHomeShowLogo", type="boolean", nullable=true)
     */
    private $mobileHomeShowLogo = false;


    /**
     * @var boolean
     *
     * @ORM\Column(name="showEmail", type="boolean", nullable=true)
     */
    private $showEmail = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="sendSms", type="boolean", nullable=true)
     */
    private $sendSms = false;


    /**
     * @var boolean
     *
     * @ORM\Column(name="showSocialIcon", type="boolean", nullable=true)
     */
    private $showSocialIcon = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="logoDisplayWebsite", type="boolean")
     */
    private $logoDisplayWebsite=true;

	/**
     * @var boolean
     *
     * @ORM\Column(name="showNewsLetter", type="boolean")
     */
    private $showNewsLetter = true;

    /**
     * @var string
     *
     * @ORM\Column(name="siteTitle", type="string", length=255, nullable=true)
     */
    private $siteTitle;

    /**
     * @var string
     *
     * @ORM\Column(name="messengerScript", type="text", nullable=true)
     */
    private $messengerScript;

    /**
     * @var string
     *
     * @ORM\Column(name="siteTitleColor", type="string", length=15, nullable=true)
     */
    private $siteTitleColor;

    /**
     * @var string
     *
     * @ORM\Column(name="breadcrumbPosition", type="string", length=20, nullable=true)
     */
    private $breadcrumbPosition;

    /**
     * @var boolean
     *
     * @ORM\Column(name="breadcrumb", type="boolean")
     */
    private $breadcrumb = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="mobileLogin", type="boolean")
     */
    private $mobileLogin = true;

    /**
     * @var string
     *
     * @ORM\Column(name="breadcrumbHome", type="string", length=20, nullable=true)
     */
    private $breadcrumbHome;

    /**
     * @var string
     *
     * @ORM\Column(name="breadcrumbBg", type="string", length=20, nullable=true)
     */
    private $breadcrumbBg;

    /**
     * @var string
     *
     * @ORM\Column(name="breadcrumbActiveBg", type="string", length=20, nullable=true)
     */
     private $breadcrumbActiveBg;

    /**
     * @var string
     *
     * @ORM\Column(name="breadcrumbColor", type="string", length=20, nullable=true)
     */
     private $breadcrumbColor;

    /**
     * @var string
     *
     * @ORM\Column(name="breadcrumbFontSize", type="string", length=10, nullable=true)
     */
     private $breadcrumbFontSize;

    /**
     * @var string
     *
     * @ORM\Column(name="breadcrumbBorderColor",type="string", length=20, nullable=true)
     */
     private $breadcrumbBorderColor;

	/**
     * @var int
     *
     * @ORM\Column(name="breadcrumbHeight",type="smallint", length=3, nullable=true)
     */
     private $breadcrumbHeight;


      /**
     * @var string
     *
     * @ORM\Column(name="siteTitleSize", type="string", length=20, nullable=true)
     */
    private $siteTitleSize;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="topBarContent", type="text" , nullable=true)
	 */
	private $topBarContent;


	/**
     * @var string
     *
     * @ORM\Column(name="siteSlogan", type="text" , nullable=true)
     */
    private $siteSlogan;


    /**
     * @var string
     *
     * @ORM\Column(name="siteCssStyle", type="text" , nullable=true)
     */
    private $siteCssStyle;


    /**
     * @var string
     *
     * @ORM\Column(name="homeTitle", type="string", length=20, nullable=true)
     */
    private $homeTitle;


    /**
     * @var string
     *
     * @ORM\Column(name="homeBgColor", type="string", length=20, nullable=true)
     */
    private $homeBgColor;


    /**
     * @var string
     *
     * @ORM\Column(name="placeholderColor", type="string", length=20, nullable=true)
     */
    private $placeholderColor;

    /**
     * @var string
     *
     * @ORM\Column(name="homeAnchorColor", type="string", length=20, nullable=true)
     */
    private $homeAnchorColor;


    /**
     * @var string
     *
     * @ORM\Column(name="homeAnchorColorHover", type="string", length=20, nullable=true)
     */
    private $homeAnchorColorHover;


    /**
     * @var string
     *
     * @ORM\Column(name="siteNameColor", type="string", length=20, nullable=true)
     */
    private $siteNameColor;

    /**
     * @var string
     *
     * @ORM\Column(name="siteBgColor", type="string", length=20, nullable=true)
     */
    private $siteBgColor;

    /**
     * @var string
     *
     * @ORM\Column(name="headerBorderColor", type="string", length=20, nullable=true)
     */
    private $headerBorderColor;

    /**
     * @var string
     *
     * @ORM\Column(name="headerBorderHeight", type="string", length=5, nullable=true)
     */
    private $headerBorderHeight;


    /**
     * @var string
     *
     * @ORM\Column(name="pagination", type="string", length=25, nullable=true)
     */
    private $pagination ='bootstrap';

    /**
     * @var string
     *
     * @ORM\Column(name="siteLanguage", type="string", length=25, nullable=true)
     */
    private $siteLanguage ='english';

    /**
     * @var string
     *
     * @ORM\Column(name="sliderPosition", type="string", length=20, nullable=true)
     */
    private $sliderPosition='top-right';

    /**
     * @var string
     *
     * @ORM\Column(name="menuLetter", type="string", length=20, nullable=true)
     */
    private $menuLetter = 'uppercase';


    /**
     * @var string
     *
     * @ORM\Column(name="boxShadow", type="string", length=20, nullable=true)
     */
     private $boxShadow;


    /**
     * @var string
     *
     * @ORM\Column(name="boxShadowColor", type="string", length=20, nullable=true)
     */
     private $boxShadowColor;


    /**
     * @var integer
     *
     * @ORM\Column(name="menuTopMargin", type="integer", length=3, nullable=true)
     */
    private $menuTopMargin;

    /**
     * @var integer
     *
     * @ORM\Column(name="stickyMenuTopMargin", type="integer", length=3, nullable=true)
     */
    private $stickyMenuTopMargin;



    /**
     * @var integer
     *
     * @ORM\Column(name="dividerBorderWidth", type="integer", length=3, nullable=true)
     */
    private $dividerBorderWidth;


    /**
     * @var string
     *
     * @ORM\Column(name="menuPosition", type="string", length=100, nullable=true)
     */
    private $menuPosition;

    /**
     * @var int
     *
     * @ORM\Column(name="subMenuWidth", type="smallint", length=4, nullable=true)
     */
    private $subMenuWidth;


    /**
     * @var float
     *
     * @ORM\Column(name="sliderTopBottomPosition", type="float", nullable=true)
     */
    private $sliderTopBottomPosition;

    /**
     * @var float
     *
     * @ORM\Column(name="sliderLeftRightPosition", type="float", nullable=true)
     */
    private $sliderLeftRightPosition;

     /**
     * @var string
     *
     * @ORM\Column(name="carouselHeight", type="string", length=10, nullable=true)
     */
    private $carouselHeight = '720px';

    /**
     * @var string
     *
     * @ORM\Column(name="mobileCarouselHeight", type="string", length=10, nullable=true)
     */
    private $mobileCarouselHeight = '320px';


    /**
     * @var string
     *
     * @ORM\Column(name="siteFontFamily", type="text", nullable=true)
     */
    private $siteFontFamily;

    /**
     * @var string
     *
     * @ORM\Column(name="siteFontSize", type="string", length = 5, nullable=true)
     */
    private $siteFontSize;

    /**
     * @var string
     *
     * @ORM\Column(name="contentFontSize", type="string", length = 5, nullable=true)
     */
    private $contentFontSize;

    /**
     * @var string
     *
     * @ORM\Column(name="contentAlign", type="string", length = 20, nullable=true)
     */
    private $contentAlign;

    /**
     * @var string
     *
     * @ORM\Column(name="contentLineHeight", type="string", length = 5, nullable=true)
     */
    private $contentLineHeight;

    /**
     * @var string
     *
     * @ORM\Column(name="anchorColor", type="string", length=50, nullable=true)
     */
    private $anchorColor;

    /**
     * @var string
     *
     * @ORM\Column(name="anchorHoverColor", type="string", length=50, nullable=true)
     */
    private $anchorHoverColor;

    /**
     * @var string
     *
     * @ORM\Column(name="buttonBgColor", type="string", length=255, nullable=true, nullable=true)
     */
    private $buttonBgColor;

    /**
     * @var string
     *
     * @ORM\Column(name="buttonBgColorHover", type="string", length=50, nullable=true)
     */
    private $buttonBgColorHover;

    /**
     * @var string
     *
     * @ORM\Column(name="siteH1TextSize", type="string", length=10, nullable=true)
     */
    private $siteH1TextSize = '36px';

    /**
     * @var string
     *
     * @ORM\Column(name="siteH2TextSize", type="string", length=10, nullable=true)
     */
    private $siteH2TextSize = '30px';

    /**
     * @var string
     *
     * @ORM\Column(name="siteH3TextSize", type="string", length=10, nullable=true)
     */
    private $siteH3TextSize='24px';

    /**
     * @var string
     *
     * @ORM\Column(name="siteH4TextSize", type="string", length=10, nullable=true)
     */
    private $siteH4TextSize ='18px';

    /**
     * @var string
     *
     * @ORM\Column(name="topBgColor", type="string", length=20, nullable=true)
     */
    private $topBgColor;


    /**
     * @var string
     *
     * @ORM\Column(name="headerBgColor", type="string", length=20, nullable=true)
     */
    private $headerBgColor;


    /**
     * @var string
     *
     * @ORM\Column(name="dividerFontSize", type="string", length=10, nullable=true)
     */
    private $dividerFontSize;


    /**
     * @var string
     *
     * @ORM\Column(name="dividerFontColor", type="string", length=20, nullable=true)
     */
    private $dividerFontColor;

    /**
     * @var string
     *
     * @ORM\Column(name="borderColor", type="string", length=20, nullable=true)
     */
    private $borderColor;

    /**
     * @var string
     *
     * @ORM\Column(name="borderColorHover", type="string", length=20, nullable=true)
     */
    private $borderColorHover;



    /**
     * @var float
     *
     * @ORM\Column(name="dividerBorder", type="float", nullable=true)
     */
    private $dividerBorder;


    /**
     * @var string
     *
     * @ORM\Column(name="dividerBeforeColor", type="string", length=20, nullable=true)
     */
    private $dividerBeforeColor;


    /**
     * @var string
     *
     * @ORM\Column(name="dividerAfterColor", type="string", length=20, nullable=true)
     */
    private $dividerAfterColor;

     /**
     * @var string
     *
     * @ORM\Column(name="socialIconPosition", type="string", length=20, nullable=true)
     */
    private $socialIconPosition;

    /**
     * @var string
     *
     * @ORM\Column(name="topTextPosition", type="string", length=20, nullable=true)
     */
    private $topTextPosition;

    /**
     * @var string
     *
     * @ORM\Column(name="topIconPosition", type="string", length=20, nullable=true)
     */
    private $topIconPosition;


    /**
     * @var string
     *
     * @ORM\Column(name="socialIconType", type="string", length=100, nullable=true)
     */
    private $socialIconType;


    /**
     * @var integer
     *
     * @ORM\Column(name="logoWidth", type="integer", length=10, nullable=true)
     */
    private $logoWidth;

    /**
     * @var integer
     *
     * @ORM\Column(name="logoHeight", type="integer", length=10, nullable=true)
     */
    private $logoHeight;


    /**
     * @var string
     *
     *
     * @ORM\Column(name="logo", type="string", length=255, nullable=true)
     */
    private $logo;


    /**
     * @Assert\File(maxSize="8388608")
     */
    protected $logoFile;


    /**
     * @var string
     *
     *
     * @ORM\Column(name="favicon", type="string", length=255, nullable=true)
     */
    private $favicon;


    /**
     * @Assert\File(maxSize="1024")
     */
    protected $faviconFile;

    /**
     * @var string
     *
     * @ORM\Column(name="headerBgImage", type="string", length=255, nullable=true)
     */
    private $headerBgImage;


    /**
     * @Assert\File(maxSize="8388608")
     */
    protected $headerBgImageFile;


    /**
     * @var string
     *
     * @ORM\Column(name="bgImage", type="string", length=255, nullable=true)
     */
    private $bgImage;

    /**
     * @Assert\File(maxSize="8388608")
     */
    protected $bgImageFile;


    /**
     * @var string
     *
     * @ORM\Column(name="menuBgColor", type="string", length=20, nullable=true)
     */
    private $menuBgColor;

    /**
     * @var string
     *
     * @ORM\Column(name="menuBgColorHover", type="string", length=20, nullable=true)
     */
    private $menuBgColorHover;

     /**
     * @var string
     *
     * @ORM\Column(name="subMenuBgColor", type="string", length=20, nullable=true)
     */
    private $subMenuBgColor;

    /**
     * @var string
     *
     * @ORM\Column(name="subMenuBgColorHover", type="string", length=20, nullable=true)
     */
    private $subMenuBgColorHover;


    /**
     * @var string
     *
     * @ORM\Column(name="menuLia", type="string", length=50, nullable=true)
     */
    private $menuLia;

    /**
     * @var string
     *
     * @ORM\Column(name="menuLiHovera", type="string", length=50, nullable=true)
     */
    private $menuLiHovera;

    /**
     * @var string
     *
     * @ORM\Column(name="menuLiAColor", type="string", length=50, nullable=true)
     */
    private $menuLiAColor;

    /**
     * @var string
     *
     * @ORM\Column(name="menuLiHoverAColor", type="string", length=50, nullable=true)
     */
    private $menuLiAHoverColor;

    /**
     * @var string
     *
     * @ORM\Column(name="mobileHeaderBgColor", type="string", length=20, nullable=true)
     */
    private $mobileHeaderBgColor;

    /**
     * @var string
     *
     * @ORM\Column(name="mobileMenuBgColor", type="string", length=20, nullable=true)
     */
    private $mobileMenuBgColor;

    /**
     * @var string
     *
     * @ORM\Column(name="mobileMenuBgColorHover", type="string", length=20, nullable=true)
     */
    private $mobileMenuBgColorHover;

    /**
     * @var string
     *
     * @ORM\Column(name="mobileMenuLiAColor", type="string", length=20, nullable=true)
     */
    private $mobileMenuLiAColor;

    /**
     * @var string
     *
     * @ORM\Column(name="mobileMenuLiAHoverColor", type="string", length=20, nullable=true)
     */
    private $mobileMenuLiAHoverColor;


    /**
     * @var string
     *
     * @ORM\Column(name="menuFontSize", type="string", length=255, nullable=true)
     */
    private $menuFontSize;


    /**
     * @var string
     *
     * @ORM\Column(name="bodyColor", type="string", length=50, nullable=true)
     */
    private $bodyColor;


     /**
     * @var string
     *
     * @ORM\Column(name="sidebarColor", type="string", length=50, nullable=true)
     */
    private $sidebarColor;


    /**
     * @var string
     *
     * @ORM\Column(name="siteTitleBgColor", type="string", length=255, nullable=true)
     */
    private $siteTitleBgColor;

    /**
     * @var string
     *
     * @ORM\Column(name="subPageBgColor", type="string", length=255, nullable=true)
     */
    private $subPageBgColor;

    /**
     * @var string
     *
     * @ORM\Column(name="footerBgColor", type="string", length=255, nullable=true)
     */
    private $footerBgColor;

    /**
     * @var string
     *
     * @ORM\Column(name="footerTextColor", type="string", length=50, nullable=true)
     */
    private $footerTextColor;

     /**
     * @var string
     *
     * @ORM\Column(name="footerAnchorColor", type="string", length=15, nullable=true)
     */
    private $footerAnchorColor;

    /**
     * @var string
     *
     * @ORM\Column(name="footerAnchorColorHover", type="string", length=15, nullable=true)
     */
    private $footerAnchorColorHover;


    /**
     * @var text
     *
     * @ORM\Column(name="metaKeyword", type="text", nullable=true)
     */
    private $metaKeyword;

    /**
     * @var text
     *
     * @ORM\Column(name="metaDescription", type="text", nullable=true)
     */
    private $metaDescription;


    /**
     * @Assert\File(maxSize="8388608")
     */
    protected $file;


    /**
     * @var string
     *
     * @ORM\Column(name="androidHeaderBg", type="string", length=30, nullable=true)
     */
    private $androidHeaderBg;


    /**
     * @var string
     *
     * @ORM\Column(name="androidMenuBg", type="string", length=30, nullable=true)
     */
    private $androidMenuBg;

    /**
     * @var string
     *
     * @ORM\Column(name="androidIconColor", type="string", length=30, nullable=true)
     */
    private $androidIconColor;

    /**
     * @var string
     *
     * @ORM\Column(name="androidMenuBgHover", type="string", length=30, nullable=true)
     */
    private $androidMenuBgHover;

    /**
     * @var string
     *
     * @ORM\Column(name="androidAnchorColor", type="string", length=30, nullable=true)
     */
     private $androidAnchorColor;

    /**
     * @var string
     *
     * @ORM\Column(name="androidAnchorHoverColor", type="string", length=30, nullable=true)
     */
     private $androidAnchorHoverColor;


    /**
     * @var string
     *
     *
     * @ORM\Column(name="androidLogo", type="string", length=255, nullable=true)
     */
    private $androidLogo;


    /**
     * @Assert\File(maxSize="8388608")
     */
    protected $androidLogoFile;




    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }



    /**
     * @param GlobalOption $globalOption
     */
    public function setGlobalOption($globalOption)
    {
        $this->globalOption = $globalOption;
    }

    /**
     * @return GlobalOption
     */
    public function getGlobalOption()
    {
        return $this->globalOption;
    }



    /**
     * @return string
     */
    public function getSiteBgColor()
    {
        return $this->siteBgColor;
    }

    /**
     * @param string $siteBgColor
     */
    public function setSiteBgColor($siteBgColor)
    {
        $this->siteBgColor = $siteBgColor;
    }



    /**
     * @return string
     */
    public function getSiteFontFamily()
    {
        return $this->siteFontFamily;
    }

    /**
     * @param string $siteFontFamily
     */
    public function setSiteFontFamily($siteFontFamily)
    {
        $this->siteFontFamily = $siteFontFamily;
    }

    /**
     * @return string
     */
    public function getSiteFontSize()
    {
        return $this->siteFontSize;
    }

    /**
     * @param string $siteFontSize
     */
    public function setSiteFontSize($siteFontSize)
    {
        $this->siteFontSize = $siteFontSize;
    }

    /**
     * @return string
     */
    public function getAnchorColor()
    {
        return $this->anchorColor;
    }

    /**
     * @param string $anchorColor
     */
    public function setAnchorColor($anchorColor)
    {
        $this->anchorColor = $anchorColor;
    }

    /**
     * @return string
     */
    public function getAnchorHoverColor()
    {
        return $this->anchorHoverColor;
    }

    /**
     * @param string $anchorHoverColor
     */
    public function setAnchorHoverColor($anchorHoverColor)
    {
        $this->anchorHoverColor = $anchorHoverColor;
    }

    /**
     * @return string
     */
    public function getButtonBgColor()
    {
        return $this->buttonBgColor;
    }

    /**
     * @param string $buttonBgColor
     */
    public function setButtonBgColor($buttonBgColor)
    {
        $this->buttonBgColor = $buttonBgColor;
    }

    /**
     * @return string
     */
    public function getButtonBgColorHover()
    {
        return $this->buttonBgColorHover;
    }

    /**
     * @param string $buttonBgColorHover
     */
    public function setButtonBgColorHover($buttonBgColorHover)
    {
        $this->buttonBgColorHover = $buttonBgColorHover;
    }

    /**
     * @return string
     */
    public function getSiteH1TextSize()
    {
        return $this->siteH1TextSize;
    }

    /**
     * @param string $siteH1TextSize
     */
    public function setSiteH1TextSize($siteH1TextSize)
    {
        $this->siteH1TextSize = $siteH1TextSize;
    }

    /**
     * @return string
     */
    public function getSiteH2TextSize()
    {
        return $this->siteH2TextSize;
    }

    /**
     * @param string $siteH2TextSize
     */
    public function setSiteH2TextSize($siteH2TextSize)
    {
        $this->siteH2TextSize = $siteH2TextSize;
    }

    /**
     * @return string
     */
    public function getSiteH3TextSize()
    {
        return $this->siteH3TextSize;
    }

    /**
     * @param string $siteH3TextSize
     */
    public function setSiteH3TextSize($siteH3TextSize)
    {
        $this->siteH3TextSize = $siteH3TextSize;
    }

    /**
     * @return string
     */
    public function getSiteH4TextSize()
    {
        return $this->siteH4TextSize;
    }

    /**
     * @param string $siteH4TextSize
     */
    public function setSiteH4TextSize($siteH4TextSize)
    {
        $this->siteH4TextSize = $siteH4TextSize;
    }

    /**
     * @return string
     */
    public function getHeaderBgColor()
    {
        return $this->headerBgColor;
    }

    /**
     * @param string $headerBgColor
     */
    public function setHeaderBgColor($headerBgColor)
    {
        $this->headerBgColor = $headerBgColor;
    }





    /**
     * @return string
     */
    public function getMenuBgColor()
    {
        return $this->menuBgColor;
    }

    /**
     * @param string $menuBgColor
     */
    public function setMenuBgColor($menuBgColor)
    {
        $this->menuBgColor = $menuBgColor;
    }

    /**
     * @return string
     */
    public function getMenuLiAColor()
    {
        return $this->menuLiAColor;
    }

    /**
     * @param string $menuLiAColor
     */
    public function setMenuLiAColor($menuLiAColor)
    {
        $this->menuLiAColor = $menuLiAColor;
    }

    /**
     * @return string
     */
    public function getMenuLiAHoverColor()
    {
        return $this->menuLiAHoverColor;
    }

    /**
     * @param string $menuLiAHoverColor
     */
    public function setMenuLiAHoverColor($menuLiAHoverColor)
    {
        $this->menuLiAHoverColor = $menuLiAHoverColor;
    }

    /**
     * @return string
     */
    public function getMenuFontSize()
    {
        return $this->menuFontSize;
    }

    /**
     * @param string $menuFontSize
     */
    public function setMenuFontSize($menuFontSize)
    {
        $this->menuFontSize = $menuFontSize;
    }

    /**
     * @return string
     */
    public function getBodyColor()
    {
        return $this->bodyColor;
    }

    /**
     * @param string $bodyColor
     */
    public function setBodyColor($bodyColor)
    {
        $this->bodyColor = $bodyColor;
    }

    /**
     * @return string
     */
    public function getSiteTitleBgColor()
    {
        return $this->siteTitleBgColor;
    }

    /**
     * @param string $siteTitleBgColor
     */
    public function setSiteTitleBgColor($siteTitleBgColor)
    {
        $this->siteTitleBgColor = $siteTitleBgColor;
    }

    /**
     * @return string
     */
    public function getSubPageBgColor()
    {
        return $this->subPageBgColor;
    }

    /**
     * @param string $subPageBgColor
     */
    public function setSubPageBgColor($subPageBgColor)
    {
        $this->subPageBgColor = $subPageBgColor;
    }

    /**
     * @return string
     */
    public function getFooterBgColor()
    {
        return $this->footerBgColor;
    }

    /**
     * @param string $footerBgColor
     */
    public function setFooterBgColor($footerBgColor)
    {
        $this->footerBgColor = $footerBgColor;
    }

    /**
     * @return string
     */
    public function getFooterTextColor()
    {
        return $this->footerTextColor;
    }

    /**
     * @param string $footerTextColor
     */
    public function setFooterTextColor($footerTextColor)
    {
        $this->footerTextColor = $footerTextColor;
    }

    /**
     * Sets file.
     *
     * @param TemplateCustomize $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    /**
     * Get file.
     *
     * @return TemplateCustomize
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @ORM\PostRemove()
     */

    public function removeLogo()
    {

        if ($file = $this->getAbsolutePath()) {
            unlink($file);
        }
    }

    public function removeFavicon()
    {

        $path = null === $this->faviconFile
        ? null
        : $this->getUploadRootDir().'/'.$this->faviconFile;

        if ($file = $path) {
            unlink($file);
        }
    }

    public function removeHeaderImage()
    {
        $path = null === $this->headerBgImage
            ? null
            : $this->getUploadRootDir().'/'.$this->headerBgImage;

        if ($file = $path) {
            unlink($file);
        }
    }

    public function removeBodyImage()
    {
        $path = null === $this->bgImage
            ? null
            : $this->getUploadRootDir().'/'.$this->bgImage;

        if ($file = $path) {
            unlink($file);
        }
    }


    public function removeAndroidLogo()
    {
        $path = null === $this->androidLogo
            ? null
            : $this->getUploadRootDir().'/'.$this->androidLogo;

        if ($file = $path) {
            unlink($file);
        }
    }


    public function getAbsolutePath()
    {
        return null === $this->logo
            ? null
            : $this->getUploadRootDir().'/'.$this->logo;
    }

    public function getWebPath( $fileName = '' )
    {
        return null === $this-> $fileName
            ? null
            : $this->getUploadDir().'/'.$this-> $fileName;
    }

    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../../../web/'.$this->getUploadDir();
    }

    public function getUploadDir()
    {
        return 'uploads/domain/'.$this->getGlobalOption()->getId().'/customizeTemplate';
    }

    public function upload()
    {
        // the file property can be empty if the field is not required
        if (null === $this->getFile()) {
            return;
        }

        // use the original file name here but you should
        // sanitize it at least to avoid any security issues

        // move takes the target directory and then the
        // target filename to move to

        $filename = date('YmdHmi') . "_" . $this->getFile()->getClientOriginalName();
        $this->getFile()->move(
            $this->getUploadRootDir(),
            $filename
        );
        // set the path property to the filename where you've saved the file
        $this->setLogo($filename);

        // clean up the file property as you won't need it anymore
        $this->file = null;
    }


    /**
     * @return boolean
     */
    public function isLogoDisplayWebsite()
    {
        return $this->logoDisplayWebsite;
    }

    /**
     * @param boolean $logoDisplayWebsite
     */
    public function setLogoDisplayWebsite($logoDisplayWebsite)
    {
        $this->logoDisplayWebsite = $logoDisplayWebsite;
    }

    /**
     * @return mixed
     */
    public function getLogoFile()
    {
        return $this->logoFile;
    }

    /**
     * @param UploadedFile $logoFile
     */
    public function setLogoFile(UploadedFile $logoFile)
    {
        $this->logoFile = $logoFile;
    }


    /**
     * @return string
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * @param string $logo
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;
    }



    /**
     * @return string
     */
    public function getFavicon()
    {
        return $this->favicon;
    }

    /**
     * @param string $favicon
     */
    public function setFavicon($favicon)
    {
        $this->favicon = $favicon;
    }

    /**
     * @return UploadedFile
     */
    public function getFaviconFile()
    {
        return $this->faviconFile;
    }

    /**
     * @param UploadedFile $faviconFile
     */
    public function setFaviconFile(UploadedFile $faviconFile)
    {
        $this->faviconFile = $faviconFile;
    }


    /**
     * @return string
     */
    public function getHeaderBgImage()
    {
        return $this->headerBgImage;
    }

    /**
     * @param string $headerBgImage
     */
    public function setHeaderBgImage($headerBgImage)
    {
        $this->headerBgImage = $headerBgImage;
    }


    /**
     * @return UploadedFile
     */
    public function getHeaderBgImageFile()
    {
        return $this->headerBgImageFile;
    }

    /**
     * @param UploadedFile $headerBgImageFile
     */
    public function setHeaderBgImageFile(UploadedFile $headerBgImageFile)
    {
        $this->headerBgImageFile = $headerBgImageFile;
    }

    /**
     * @return string
     */
    public function getBgImage()
    {
        return $this->bgImage;
    }

    /**
     * @param string $bgImage
     */
    public function setBgImage($bgImage)
    {
        $this->bgImage = $bgImage;
    }

    /**
     * @return UploadedFile
     */
    public function getBgImageFile()
    {
        return $this->bgImageFile;
    }

    /**
     * @param UploadedFile $bgImageFile
     */
    public function setBgImageFile(UploadedFile $bgImageFile)
    {
        $this->bgImageFile = $bgImageFile;
    }



    /**
     * @return boolean
     */
    public function getShowEmail()
    {
        return $this->showEmail;
    }

    /**
     * @param boolean $showEmail
     */
    public function setShowEmail($showEmail)
    {
        $this->showEmail = $showEmail;
    }

    /**
     * @return boolean
     */
    public function isShowCalendar()
    {
        return $this->showCalendar;
    }

    /**
     * @param boolean $showCalendar
     */
    public function setShowCalendar($showCalendar)
    {
        $this->showCalendar = $showCalendar;
    }

    /**
     * @return boolean
     */
    public function isShowSidebar()
    {
        return $this->showSidebar;
    }

    /**
     * @param boolean $showSidebar
     */
    public function setShowSidebar($showSidebar)
    {
        $this->showSidebar = $showSidebar;
    }

    /**
     * @return boolean
     */
    public function isShowSearch()
    {
        return $this->showSearch;
    }

    /**
     * @param boolean $showSearch
     */
    public function setShowSearch($showSearch)
    {
        $this->showSearch = $showSearch;
    }

    /**
     * @return boolean
     */
    public function isShowMobile()
    {
        return $this->showMobile;
    }

    /**
     * @param boolean $showMobile
     */
    public function setShowMobile($showMobile)
    {
        $this->showMobile = $showMobile;
    }

    /**
     * @return string
     */
    public function getSiteNameColor()
    {
        return $this->siteNameColor;
    }

    /**
     * @param string $siteNameColor
     */
    public function setSiteNameColor($siteNameColor)
    {
        $this->siteNameColor = $siteNameColor;
    }

    /**
     * @return string
     */
    public function getMenuLia()
    {
        return $this->menuLia;
    }

    /**
     * @param string $menuLia
     */
    public function setMenuLia($menuLia)
    {
        $this->menuLia = $menuLia;
    }

    /**
     * @return string
     */
    public function getMenuLiHovera()
    {
        return $this->menuLiHovera;
    }

    /**
     * @param string $menuLiHovera
     */
    public function setMenuLiHovera($menuLiHovera)
    {
        $this->menuLiHovera = $menuLiHovera;
    }

    /**
     * @return string
     */
    public function getBorderColor()
    {
        return $this->borderColor;
    }

    /**
     * @param string $borderColor
     */
    public function setBorderColor($borderColor)
    {
        $this->borderColor = $borderColor;
    }

    /**
     * @return string
     */
    public function getSidebarPosition()
    {
        return $this->sidebarPosition;
    }

    /**
     * @param string $sidebarPosition
     */
    public function setSidebarPosition($sidebarPosition)
    {
        $this->sidebarPosition = $sidebarPosition;
    }



    /**
     * @return bool
     */
    public function getSidebarTooltip()
    {
        return $this->sidebarTooltip;
    }

    /**
     * @param bool $sidebarTooltip
     */
    public function setSidebarTooltip($sidebarTooltip)
    {
        $this->sidebarTooltip = $sidebarTooltip;
    }

    /**
     * @return string
     */
    public function getSidebarTitle()
    {
        return $this->sidebarTitle;
    }

    /**
     * @param string $sidebarTitle
     */
    public function setSidebarTitle($sidebarTitle)
    {
        $this->sidebarTitle = $sidebarTitle;
    }

    /**
     * @return string
     */
    public function getHomeBgColor()
    {
        return $this->homeBgColor;
    }

    /**
     * @param string $homeBgColor
     */
    public function setHomeBgColor($homeBgColor)
    {
        $this->homeBgColor = $homeBgColor;
    }

    /**
     * @return string
     */
    public function getHomeAnchorColor()
    {
        return $this->homeAnchorColor;
    }

    /**
     * @param string $homeAnchorColor
     */
    public function setHomeAnchorColor($homeAnchorColor)
    {
        $this->homeAnchorColor = $homeAnchorColor;
    }

    /**
     * @return string
     */
    public function getHomeAnchorColorHover()
    {
        return $this->homeAnchorColorHover;
    }

    /**
     * @param string $homeAnchorColorHover
     */
    public function setHomeAnchorColorHover($homeAnchorColorHover)
    {
        $this->homeAnchorColorHover = $homeAnchorColorHover;
    }

    /**
     * @return string
     */
    public function getPagination()
    {
        return $this->pagination;
    }

    /**
     * @param string $pagination
     */
    public function setPagination($pagination)
    {
        $this->pagination = $pagination;
    }

    /**
     * @return boolean
     */
    public function getTopBar()
    {
        return $this->topBar;
    }

    /**
     * @param boolean $topBar
     */
    public function setTopBar($topBar)
    {
        $this->topBar = $topBar;
    }

    /**
     * @return boolean
     */
    public function getFooterBlock()
    {
        return $this->footerBlock;
    }

    /**
     * @param boolean $footerBlock
     */
    public function setFooterBlock($footerBlock)
    {
        $this->footerBlock = $footerBlock;
    }

    /**
     * @return string
     */
    public function getDividerBeforeColor()
    {
        return $this->dividerBeforeColor;
    }

    /**
     * @param string $dividerBeforeColor
     */
    public function setDividerBeforeColor($dividerBeforeColor)
    {
        $this->dividerBeforeColor = $dividerBeforeColor;
    }

    /**
     * @return mixed
     */
    public function getDividerAfterColor()
    {
        return $this->dividerAfterColor;
    }

    /**
     * @param mixed $dividerAfterColor
     */
    public function setDividerAfterColor($dividerAfterColor)
    {
        $this->dividerAfterColor = $dividerAfterColor;
    }

    /**
     * @return mixed
     */
    public function getFooterAnchorColor()
    {
        return $this->footerAnchorColor;
    }

    /**
     * @param mixed $footerAnchorColor
     */
    public function setFooterAnchorColor($footerAnchorColor)
    {
        $this->footerAnchorColor = $footerAnchorColor;
    }

    /**
     * @return mixed
     */
    public function getFooterAnchorColorHover()
    {
        return $this->footerAnchorColorHover;
    }

    /**
     * @param mixed $footerAnchorColorHover
     */
    public function setFooterAnchorColorHover($footerAnchorColorHover)
    {
        $this->footerAnchorColorHover = $footerAnchorColorHover;
    }

    /**
     * @return string
     */
    public function getDividerFontSize()
    {
        return $this->dividerFontSize;
    }

    /**
     * @param string $dividerFontSize
     */
    public function setDividerFontSize($dividerFontSize)
    {
        $this->dividerFontSize = $dividerFontSize;
    }

    /**
     * @return string
     */
    public function getDividerFontColor()
    {
        return $this->dividerFontColor;
    }

    /**
     * @param string $dividerFontColor
     */
    public function setDividerFontColor($dividerFontColor)
    {
        $this->dividerFontColor = $dividerFontColor;
    }

    /**
     * @return string
     */
    public function getSliderPosition()
    {
        return $this->sliderPosition;
    }

    /**
     * @param string $sliderPosition
     */
    public function setSliderPosition($sliderPosition)
    {
        $this->sliderPosition = $sliderPosition;
    }

    /**
     * @return float
     */
    public function getSliderTopBottomPosition()
    {
        return $this->sliderTopBottomPosition;
    }

    /**
     * @param float $sliderTopBottomPosition
     */
    public function setSliderTopBottomPosition($sliderTopBottomPosition)
    {
        $this->sliderTopBottomPosition = $sliderTopBottomPosition;
    }

    /**
     * @return float
     */
    public function getSliderLeftRightPosition()
    {
        return $this->sliderLeftRightPosition;
    }

    /**
     * @param float $sliderLeftRightPosition
     */
    public function setSliderLeftRightPosition($sliderLeftRightPosition)
    {
        $this->sliderLeftRightPosition = $sliderLeftRightPosition;
    }

    /**
     * @return float
     */
    public function getDividerBorder()
    {
        return $this->dividerBorder;
    }

    /**
     * @param float $dividerBorder
     */
    public function setDividerBorder($dividerBorder)
    {
        $this->dividerBorder = $dividerBorder;
    }

    /**
     * @return string
     */
    public function getTopBgColor()
    {
        return $this->topBgColor;
    }

    /**
     * @param string $topBgColor
     */
    public function setTopBgColor($topBgColor)
    {
        $this->topBgColor = $topBgColor;
    }

    /**
     * @return string
     */
    public function getBorderColorHover()
    {
        return $this->borderColorHover;
    }

    /**
     * @param string $borderColorHover
     */
    public function setBorderColorHover($borderColorHover)
    {
        $this->borderColorHover = $borderColorHover;
    }

    /**
     * @return string
     */
    public function getSiteTitle()
    {
        return $this->siteTitle;
    }

    /**
     * @param string $siteTitle
     */
    public function setSiteTitle($siteTitle)
    {
        $this->siteTitle = $siteTitle;
    }

    /**
     * @return string
     */
    public function getSiteTitleSize()
    {
        return $this->siteTitleSize;
    }

    /**
     * @param string $siteTitleSize
     */
    public function setSiteTitleSize($siteTitleSize)
    {
        $this->siteTitleSize = $siteTitleSize;
    }

    /**
     * @return string
     */
    public function getSiteSlogan()
    {
        return $this->siteSlogan;
    }

    /**
     * @param string $siteSlogan
     */
    public function setSiteSlogan($siteSlogan)
    {
        $this->siteSlogan = $siteSlogan;
    }

    /**
     * @return string
     */
    public function getMenuBgColorHover()
    {
        return $this->menuBgColorHover;
    }

    /**
     * @param string $menuBgColorHover
     */
    public function setMenuBgColorHover($menuBgColorHover)
    {
        $this->menuBgColorHover = $menuBgColorHover;
    }

    /**
     * @return boolean
     */
    public function getBreadcrumb()
    {
        return $this->breadcrumb;
    }

    /**
     * @param boolean $breadcrumb
     */
    public function setBreadcrumb($breadcrumb)
    {
        $this->breadcrumb = $breadcrumb;
    }

    /**
     * @return string
     */
    public function getBreadcrumbBg()
    {
        return $this->breadcrumbBg;
    }

    /**
     * @param string $breadcrumbBg
     */
    public function setBreadcrumbBg($breadcrumbBg)
    {
        $this->breadcrumbBg = $breadcrumbBg;
    }

    /**
     * @return string
     */
    public function getBreadcrumbActiveBg()
    {
        return $this->breadcrumbActiveBg;
    }

    /**
     * @param string $breadcrumbActiveBg
     */
    public function setBreadcrumbActiveBg($breadcrumbActiveBg)
    {
        $this->breadcrumbActiveBg = $breadcrumbActiveBg;
    }

    /**
     * @return string
     */
    public function getBreadcrumbFontSize()
    {
        return $this->breadcrumbFontSize;
    }

    /**
     * @param string $breadcrumbFontSize
     */
    public function setBreadcrumbFontSize($breadcrumbFontSize)
    {
        $this->breadcrumbFontSize = $breadcrumbFontSize;
    }

    /**
     * @return string
     */
    public function getBreadcrumbBorderColor()
    {
        return $this->breadcrumbBorderColor;
    }

    /**
     * @param string $breadcrumbBorderColor
     */
    public function setBreadcrumbBorderColor($breadcrumbBorderColor)
    {
        $this->breadcrumbBorderColor = $breadcrumbBorderColor;
    }

    /**
     * @return string
     */
    public function getBreadcrumbHome()
    {
        return $this->breadcrumbHome;
    }

    /**
     * @param string $breadcrumbHome
     */
    public function setBreadcrumbHome($breadcrumbHome)
    {
        $this->breadcrumbHome = $breadcrumbHome;
    }

    /**
     * @return string
     */
    public function getBreadcrumbColor()
    {
        return $this->breadcrumbColor;
    }

    /**
     * @param string $breadcrumbColor
     */
    public function setBreadcrumbColor($breadcrumbColor)
    {
        $this->breadcrumbColor = $breadcrumbColor;
    }

    /**
     * @return string
     */
    public function getSidebarColor()
    {
        return $this->sidebarColor;
    }

    /**
     * @param string $sidebarColor
     */
    public function setSidebarColor($sidebarColor)
    {
        $this->sidebarColor = $sidebarColor;
    }


    /**
     * @return string
     */
    public function getCarouselHeight()
    {
        return $this->carouselHeight;
    }

    /**
     * @param string $carouselHeight
     */
    public function setCarouselHeight($carouselHeight)
    {
        $this->carouselHeight = $carouselHeight;
    }

    /**
     * @return string
     */
    public function getMobileCarouselHeight()
    {
        return $this->mobileCarouselHeight;
    }

    /**
     * @param string $mobileCarouselHeight
     */
    public function setMobileCarouselHeight($mobileCarouselHeight)
    {
        $this->mobileCarouselHeight = $mobileCarouselHeight;
    }

    /**
     * @return string
     */
    public function getHomeTitle()
    {
        return $this->homeTitle;
    }

    /**
     * @param string $homeTitle
     */
    public function setHomeTitle($homeTitle)
    {
        $this->homeTitle = $homeTitle;
    }

    /**
     * @return int
     */
    public function getLogoWidth()
    {
        return $this->logoWidth;
    }

    /**
     * @param int $logoWidth
     */
    public function setLogoWidth($logoWidth)
    {
        $this->logoWidth = $logoWidth;
    }

    /**
     * @return int
     */
    public function getLogoHeight()
    {
        return $this->logoHeight;
    }

    /**
     * @param int $logoHeight
     */
    public function setLogoHeight($logoHeight)
    {
        $this->logoHeight = $logoHeight;
    }

    /**
     * @return string
     */
    public function getMenuLetter()
    {
        return $this->menuLetter;
    }

    /**
     * @param string $menuLetter
     * uppercase
     * capitalize
     * lowercase
     */
    public function setMenuLetter($menuLetter)
    {
        $this->menuLetter = $menuLetter;
    }

    /**
     * @return string
     */
    public function getMenuPosition()
    {
        return $this->menuPosition;
    }

    /**
     * @param string $menuPosition
     */
    public function setMenuPosition($menuPosition)
    {
        $this->menuPosition = $menuPosition;
    }

    /**
     * @return int
     */
    public function getMenuTopMargin()
    {
        return $this->menuTopMargin;
    }

    /**
     * @param int $menuTopMargin
     */
    public function setMenuTopMargin($menuTopMargin)
    {
        $this->menuTopMargin = $menuTopMargin;
    }

    /**
     * @return string
     */
    public function getHeaderBorderColor()
    {
        return $this->headerBorderColor;
    }

    /**
     * @param string $headerBorderColor
     */
    public function setHeaderBorderColor($headerBorderColor)
    {
        $this->headerBorderColor = $headerBorderColor;
    }

    /**
     * @return string
     */
    public function getHeaderBorderHeight()
    {
        return $this->headerBorderHeight;
    }

    /**
     * @param string $headerBorderHeight
     */
    public function setHeaderBorderHeight($headerBorderHeight)
    {
        $this->headerBorderHeight = $headerBorderHeight;
    }

    /**
     * @return int
     */
    public function getStickyMenuTopMargin()
    {
        return $this->stickyMenuTopMargin;
    }

    /**
     * @param int $stickyMenuTopMargin
     */
    public function setStickyMenuTopMargin($stickyMenuTopMargin)
    {
        $this->stickyMenuTopMargin = $stickyMenuTopMargin;
    }

    /**
     * @return string
     */
    public function getSubMenuBgColor()
    {
        return $this->subMenuBgColor;
    }

    /**
     * @param string $subMenuBgColor
     */
    public function setSubMenuBgColor($subMenuBgColor)
    {
        $this->subMenuBgColor = $subMenuBgColor;
    }

    /**
     * @return string
     */
    public function getSubMenuBgColorHover()
    {
        return $this->subMenuBgColorHover;
    }

    /**
     * @param string $subMenuBgColorHover
     */
    public function setSubMenuBgColorHover($subMenuBgColorHover)
    {
        $this->subMenuBgColorHover = $subMenuBgColorHover;
    }

    /**
     * @return bool
     */
    public function getMenuBold()
    {
        return $this->menuBold;
    }

    /**
     * @param bool $menuBold
     */
    public function setMenuBold($menuBold)
    {
        $this->menuBold = $menuBold;
    }

    /**
     * @return string
     */
    public function getMetaKeyword()
    {
        return $this->metaKeyword;
    }

    /**
     * @param string $metaKeyword
     */
    public function setMetaKeyword($metaKeyword)
    {
        $this->metaKeyword = $metaKeyword;
    }

    /**
     * @return string
     */
    public function getMetaDescription()
    {
        return $this->metaDescription;
    }

    /**
     * @param string $metaDescription
     */
    public function setMetaDescription($metaDescription)
    {
        $this->metaDescription = $metaDescription;
    }

    /**
     * @return bool
     */
    public function isShowSocialIcon()
    {
        return $this->showSocialIcon;
    }

    /**
     * @param bool $showSocialIcon
     */
    public function setShowSocialIcon($showSocialIcon)
    {
        $this->showSocialIcon = $showSocialIcon;
    }

    /**
     * @return string
     */
    public function getMobileHeaderBgColor()
    {
        return $this->mobileHeaderBgColor;
    }

    /**
     * @param string $mobileHeaderBgColor
     */
    public function setMobileHeaderBgColor($mobileHeaderBgColor)
    {
        $this->mobileHeaderBgColor = $mobileHeaderBgColor;
    }

    /**
     * @return string
     */
    public function getMobileMenuBgColor()
    {
        return $this->mobileMenuBgColor;
    }

    /**
     * @param string $mobileMenuBgColor
     */
    public function setMobileMenuBgColor($mobileMenuBgColor)
    {
        $this->mobileMenuBgColor = $mobileMenuBgColor;
    }

    /**
     * @return string
     */
    public function getMobileMenuBgColorHover()
    {
        return $this->mobileMenuBgColorHover;
    }

    /**
     * @param string $mobileMenuBgColorHover
     */
    public function setMobileMenuBgColorHover($mobileMenuBgColorHover)
    {
        $this->mobileMenuBgColorHover = $mobileMenuBgColorHover;
    }

    /**
     * @return string
     */
    public function getMobileMenuLiAColor()
    {
        return $this->mobileMenuLiAColor;
    }

    /**
     * @param string $mobileMenuLiAColor
     */
    public function setMobileMenuLiAColor($mobileMenuLiAColor)
    {
        $this->mobileMenuLiAColor = $mobileMenuLiAColor;
    }

    /**
     * @return string
     */
    public function getMobileMenuLiAHoverColor()
    {
        return $this->mobileMenuLiAHoverColor;
    }

    /**
     * @param string $mobileMenuLiAHoverColor
     */
    public function setMobileMenuLiAHoverColor($mobileMenuLiAHoverColor)
    {
        $this->mobileMenuLiAHoverColor = $mobileMenuLiAHoverColor;
    }

    /**
     * @return bool
     */
    public function isMobileLogin()
    {
        return $this->mobileLogin;
    }

    /**
     * @param bool $mobileLogin
     */
    public function setMobileLogin($mobileLogin)
    {
        $this->mobileLogin = $mobileLogin;
    }

    /**
     * @return int
     */
    public function getSubMenuWidth()
    {
        return $this->subMenuWidth;
    }

    /**
     * @param int $subMenuWidth
     */
    public function setSubMenuWidth($subMenuWidth)
    {
        $this->subMenuWidth = $subMenuWidth;
    }

    /**
     * @return int
     */
    public function getDividerBorderWidth()
    {
        return $this->dividerBorderWidth;
    }

    /**
     * @param int $dividerBorderWidth
     */
    public function setDividerBorderWidth($dividerBorderWidth)
    {
        $this->dividerBorderWidth = $dividerBorderWidth;
    }

    /**
     * @return bool
     */
    public function isMobileShowLogo()
    {
        return $this->mobileShowLogo;
    }

    /**
     * @param bool $mobileShowLogo
     */
    public function setMobileShowLogo($mobileShowLogo)
    {
        $this->mobileShowLogo = $mobileShowLogo;
    }

    /**
     * @return bool
     */
    public function isMobileHomeShowLogo()
    {
        return $this->mobileHomeShowLogo;
    }

    /**
     * @param bool $mobileHomeShowLogo
     */
    public function setMobileHomeShowLogo($mobileHomeShowLogo)
    {
        $this->mobileHomeShowLogo = $mobileHomeShowLogo;
    }

	/**
	 * @return bool
	 */
	public function isShowNewsLetter(){
		return $this->showNewsLetter;
	}

	/**
	 * @param bool $showNewsLetter
	 */
	public function setShowNewsLetter( bool $showNewsLetter ) {
		$this->showNewsLetter = $showNewsLetter;
	}

	/**
	 * @return SocialIcon
	 */
	public function getSocialIcons() {
		return $this->socialIcons;
	}

	/**
	 * @return string
	 */
	public function getSocialIconType(){
		return $this->socialIconType;
	}

	/**
	 * @param string $socialIconType
	 */
	public function setSocialIconType( string $socialIconType ) {
		$this->socialIconType = $socialIconType;
	}

	/**
	 * @return string
	 */
	public function getSocialIconPosition(){
		return $this->socialIconPosition;
	}

	/**
	 * @param string $socialIconPosition
	 */
	public function setSocialIconPosition( string $socialIconPosition ) {
		$this->socialIconPosition = $socialIconPosition;
	}

	/**
	 * @return string
	 */
	public function getTopBarContent(){
		return $this->topBarContent;
	}

	/**
	 * @param string $topBarContent
	 */
	public function setTopBarContent( $topBarContent ) {
		$this->topBarContent = $topBarContent;
	}

	/**
	 * @return string
	 */
	public function getTopTextPosition(){
		return $this->topTextPosition;
	}

	/**
	 * @param string $topTextPosition
	 */
	public function setTopTextPosition( string $topTextPosition ) {
		$this->topTextPosition = $topTextPosition;
	}

	/**
	 * @return string
	 */
	public function getTopIconPosition(){
		return $this->topIconPosition;
	}

	/**
	 * @param string $topIconPosition
	 */
	public function setTopIconPosition( string $topIconPosition ) {
		$this->topIconPosition = $topIconPosition;
	}

	/**
	 * @return bool
	 */
	public function isShowLogin(){
		return $this->showLogin;
	}

	/**
	 * @param bool $showLogin
	 */
	public function setShowLogin( bool $showLogin ) {
		$this->showLogin = $showLogin;
	}

	/**
	 * @return string
	 */
	public function getBreadcrumbPosition(){
		return $this->breadcrumbPosition;
	}

	/**
	 * @param string $breadcrumbPosition
	 */
	public function setBreadcrumbPosition( string $breadcrumbPosition ) {
		$this->breadcrumbPosition = $breadcrumbPosition;
	}

	/**
	 * @return int
	 */
	public function getBreadcrumbHeight(){
		return $this->breadcrumbHeight;
	}

	/**
	 * @param int $breadcrumbHeight
	 */
	public function setBreadcrumbHeight( int $breadcrumbHeight ) {
		$this->breadcrumbHeight = $breadcrumbHeight;
	}

    /**
     * @return string
     */
    public function getSiteTitleColor()
    {
        return $this->siteTitleColor;
    }

    /**
     * @param string $siteTitleColor
     */
    public function setSiteTitleColor(string $siteTitleColor)
    {
        $this->siteTitleColor = $siteTitleColor;
    }

    /**
     * @return string
     */
    public function getSiteLanguage()
    {
        return $this->siteLanguage;
    }

    /**
     * @param string $siteLanguage
     */
    public function setSiteLanguage($siteLanguage)
    {
        $this->siteLanguage = $siteLanguage;
    }

    /**
     * @return string
     */
    public function getContentFontSize()
    {
        return $this->contentFontSize;
    }

    /**
     * @param string $contentFontSize
     */
    public function setContentFontSize($contentFontSize)
    {
        $this->contentFontSize = $contentFontSize;
    }

    /**
     * @return string
     */
    public function getContentLineHeight()
    {
        return $this->contentLineHeight;
    }

    /**
     * @param string $contentLineHeight
     */
    public function setContentLineHeight($contentLineHeight)
    {
        $this->contentLineHeight = $contentLineHeight;
    }

    /**
     * @return string
     */
    public function getBoxShadow()
    {
        return $this->boxShadow;
    }

    /**
     * @param string $boxShadow
     */
    public function setBoxShadow($boxShadow)
    {
        $this->boxShadow = $boxShadow;
    }

    /**
     * @return string
     */
    public function getBoxShadowColor()
    {
        return $this->boxShadowColor;
    }

    /**
     * @param string $boxShadowColor
     */
    public function setBoxShadowColor($boxShadowColor)
    {
        $this->boxShadowColor = $boxShadowColor;
    }

    /**
     * @return string
     */
    public function getContentAlign()
    {
        return $this->contentAlign;
    }

    /**
     * @param string $contentAlign
     */
    public function setContentAlign($contentAlign)
    {
        $this->contentAlign = $contentAlign;
    }

    /**
     * @return bool
     */
    public function isContactForm()
    {
        return $this->contactForm;
    }

    /**
     * @param bool $contactForm
     */
    public function setContactForm($contactForm)
    {
        $this->contactForm = $contactForm;
    }

    /**
     * @return bool
     */
    public function isContactButton()
    {
        return $this->contactButton;
    }

    /**
     * @param bool $contactButton
     */
    public function setContactButton($contactButton)
    {
        $this->contactButton = $contactButton;
    }

    /**
     * @return string
     */
    public function getSiteCssStyle()
    {
        return $this->siteCssStyle;
    }

    /**
     * @param string $siteCssStyle
     */
    public function setSiteCssStyle($siteCssStyle)
    {
        $this->siteCssStyle = $siteCssStyle;
    }

    /**
     * @return string
     */
    public function getAndroidLogo()
    {
        return $this->androidLogo;
    }

    /**
     * @param string $androidLogo
     */
    public function setAndroidLogo($androidLogo)
    {
        $this->androidLogo = $androidLogo;
    }

    /**
     * @return string
     */
    public function getAndroidHeaderBg()
    {
        return $this->androidHeaderBg;
    }

    /**
     * @param string $androidHeaderBg
     */
    public function setAndroidHeaderBg($androidHeaderBg)
    {
        $this->androidHeaderBg = $androidHeaderBg;
    }

    /**
     * @return string
     */
    public function getAndroidMenuBg()
    {
        return $this->androidMenuBg;
    }

    /**
     * @param string $androidMenuBg
     */
    public function setAndroidMenuBg($androidMenuBg)
    {
        $this->androidMenuBg = $androidMenuBg;
    }

    /**
     * @return string
     */
    public function getAndroidIconColor()
    {
        return $this->androidIconColor;
    }

    /**
     * @param string $androidIconColor
     */
    public function setAndroidIconColor($androidIconColor)
    {
        $this->androidIconColor = $androidIconColor;
    }

    /**
     * @return string
     */
    public function getAndroidMenuBgHover()
    {
        return $this->androidMenuBgHover;
    }

    /**
     * @param string $androidMenuBgHover
     */
    public function setAndroidMenuBgHover($androidMenuBgHover)
    {
        $this->androidMenuBgHover = $androidMenuBgHover;
    }

    /**
     * @return string
     */
    public function getAndroidAnchorColor()
    {
        return $this->androidAnchorColor;
    }

    /**
     * @param string $androidAnchorColor
     */
    public function setAndroidAnchorColor($androidAnchorColor)
    {
        $this->androidAnchorColor = $androidAnchorColor;
    }

    /**
     * @return string
     */
    public function getAndroidAnchorHoverColor()
    {
        return $this->androidAnchorHoverColor;
    }

    /**
     * @param string $androidAnchorHoverColor
     */
    public function setAndroidAnchorHoverColor($androidAnchorHoverColor)
    {
        $this->androidAnchorHoverColor = $androidAnchorHoverColor;
    }

    /**
     * @return bool
     */
    public function isRegistrationForm()
    {
        return $this->registrationForm;
    }

    /**
     * @param bool $registrationForm
     */
    public function setRegistrationForm($registrationForm)
    {
        $this->registrationForm = $registrationForm;
    }

    /**
     * @return string
     */
    public function getMessengerScript()
    {
        return $this->messengerScript;
    }

    /**
     * @param string $messengerScript
     */
    public function setMessengerScript($messengerScript)
    {
        $this->messengerScript = $messengerScript;
    }

    /**
     * @return string
     */
    public function getPlaceholderColor()
    {
        return $this->placeholderColor;
    }

    /**
     * @param string $placeholderColor
     */
    public function setPlaceholderColor($placeholderColor)
    {
        $this->placeholderColor = $placeholderColor;
    }

    /**
     * @return bool
     */
    public function isSendSms()
    {
        return $this->sendSms;
    }

    /**
     * @param bool $sendSms
     */
    public function setSendSms($sendSms)
    {
        $this->sendSms = $sendSms;
    }


}

