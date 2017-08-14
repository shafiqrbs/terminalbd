<?php

namespace Setting\Bundle\ToolBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * TemplateCustomize
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Setting\Bundle\ToolBundle\Repository\TemplateCustomizeRepository")
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
     **/

    protected $globalOption;

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
     * @var string
     *
     * @ORM\Column(name="sidebarPosition", type="string", nullable=true)
     */
    private $sidebarPosition = 'left';

    /**
     * @var boolean
     *
     * @ORM\Column(name="showSearch", type="boolean", nullable=true)
     */
    private $showSearch = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="showMobile", type="boolean", nullable=true)
     */
    private $showMobile = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="showEmail", type="boolean", nullable=true)
     */
    private $showEmail = true;

    /**
     * @var string
     *
     * @ORM\Column(name="carouselPosition", type="string", length=20, nullable=true)
     */
    private $carouselPosition='top-right';


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
     * @var boolean
     *
     * @ORM\Column(name="logoDisplayWebsite", type="boolean")
     */
    private $logoDisplayWebsite=true;

    /**
     * @var string
     *
     * @ORM\Column(name="homeBgColor", type="string", length=20, nullable=true)
     */
    private $homeBgColor;

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
     * @var integer
     *
     * @ORM\Column(name="titleHeight", type="integer", length=3, nullable=true)
     */
    private $titleHeight;

    /**
     * @var integer
     *
     * @ORM\Column(name="titleMarginBottom", type="integer", length=3, nullable=true)
     */
    private $titleMarginBottom;

    /**
     * @var integer
     *
     * @ORM\Column(name="sliderTopPosition", type="integer", length=3, nullable=true)
     */
    private $sliderTopPosition;

    /**
     * @var integer
     *
     * @ORM\Column(name="sliderRightPosition", type="integer", length=3, nullable=true)
     */
    private $sliderRightPosition;


    /**
     * @var string
     *
     * @ORM\Column(name="titleFontSize", type="string", length=10, nullable=true)
     */
    private $titleFontSize;

    /**
     * @var string
     *
     * @ORM\Column(name="titleTextAlign", type="string", length=10, nullable=true)
     */
    private $titleTextAlign;

    /**
     * @var string
     *
     * @ORM\Column(name="pagination", type="string", length=25, nullable=true)
     */
    private $pagination ='bootstrap';


    /**
     * @var string
     *
     * @ORM\Column(name="titleBgColor", type="string", length=20 ,nullable=true)
     */
    private $titleBgColor;

    /**
     * @var string
     *
     * @ORM\Column(name="titleFontColor", type="string", length=20, nullable=true)
     */
    private $titleFontColor;

    /**
     * @var string
     *
     * @ORM\Column(name="titleBorderColor", type="string", length=20 ,nullable=true)
     */
    private $titleBorderColor;


    /**
     * @var string
     *
     * @ORM\Column(name="bgImage", type="string", length=50, nullable=true)
     */
    private $bgImage;

    /**
     * @Assert\File(maxSize="8388608")
     */
    protected $bgImageFile;

    /**
     * @var string
     *
     * @ORM\Column(name="siteFontFamily", type="text", nullable=true)
     */
    private $siteFontFamily;

    /**
     * @var integer
     *
     * @ORM\Column(name="siteFontSize", type="smallint", nullable=true)
     */
    private $siteFontSize;

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
     * @ORM\Column(name="siteH1TextSize", type="string", length=50, nullable=true)
     */
    private $siteH1TextSize;

    /**
     * @var string
     *
     * @ORM\Column(name="siteH2TextSize", type="string", length=50, nullable=true)
     */
    private $siteH2TextSize;

    /**
     * @var string
     *
     * @ORM\Column(name="siteH3TextSize", type="string", length=50, nullable=true)
     */
    private $siteH3TextSize;

    /**
     * @var string
     *
     * @ORM\Column(name="siteH4TextSize", type="string", length=50, nullable=true)
     */
    private $siteH4TextSize;


    /**
     * @var string
     *
     * @ORM\Column(name="headerBgColor", type="string", length=20, nullable=true)
     */
    private $headerBgColor;

    /**
     * @var string
     *
     * @ORM\Column(name="headerBgImage", type="string", length=20, nullable=true)
     */
    private $headerBgImage;

    /**
     * @var string
     *
     * @ORM\Column(name="dividerBgColor", type="string", length=20, nullable=true)
     */
    private $dividerBgColor;

    /**
     * @var string
     *
     * @ORM\Column(name="dividerColor", type="string", length=20, nullable=true)
     */
    private $dividerColor;

    /**
     * @var string
     *
     * @ORM\Column(name="borderColor", type="string", length=20, nullable=true)
     */
    private $borderColor;

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
     * @ORM\Column(name="dividerTitleColor", type="string", length=20, nullable=true)
     */
    private $dividerTitleColor;


    /**
     * @Assert\File(maxSize="8388608")
     */
    protected $headerBgImageFile;



    /**
     * @var string
     *
     * @ORM\Column(name="menuBgColor", type="string", length=50, nullable=true)
     */
    private $menuBgColor;

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
     * @Assert\File(maxSize="8388608")
     */
    protected $file;


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
     * @return int
     */
    public function getSiteFontSize()
    {
        return $this->siteFontSize;
    }

    /**
     * @param int $siteFontSize
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
     * @param mixed $logoFile
     */
    public function setLogoFile(UploadedFile $logoFile)
    {
        $this->logoFile = $logoFile;
    }

    /**
     * @return mixed
     */
    public function getHeaderBgImageFile()
    {
        return $this->headerBgImageFile;
    }

    /**
     * @param mixed $headerBgImageFile
     */
    public function setHeaderBgImageFile(UploadedFile $headerBgImageFile)
    {
        $this->headerBgImageFile = $headerBgImageFile;
    }

    /**
     * @return mixed
     */
    public function getBgImageFile()
    {
        return $this->bgImageFile;
    }

    /**
     * @param mixed $bgImageFile
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
     * @return mixed
     */
    public function getDividerBgColor()
    {
        return $this->dividerBgColor;
    }

    /**
     * @param mixed $dividerBgColor
     */
    public function setDividerBgColor($dividerBgColor)
    {
        $this->dividerBgColor = $dividerBgColor;
    }

    /**
     * @return string
     */
    public function getDividerColor()
    {
        return $this->dividerColor;
    }

    /**
     * @param string $dividerColor
     */
    public function setDividerColor($dividerColor)
    {
        $this->dividerColor = $dividerColor;
    }

    /**
     * @return string
     */
    public function getDividerTitleColor()
    {
        return $this->dividerTitleColor;
    }

    /**
     * @param string $dividerTitleColor
     */
    public function setDividerTitleColor($dividerTitleColor)
    {
        $this->dividerTitleColor = $dividerTitleColor;
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
     * @return int
     */
    public function getTitleHeight()
    {
        return $this->titleHeight;
    }

    /**
     * @param int $titleHeight
     */
    public function setTitleHeight($titleHeight)
    {
        $this->titleHeight = $titleHeight;
    }

    /**
     * @return int
     */
    public function getTitleFontSize()
    {
        return $this->titleFontSize;
    }

    /**
     * @param int $titleFontSize
     */
    public function setTitleFontSize($titleFontSize)
    {
        $this->titleFontSize = $titleFontSize;
    }

    /**
     * @return string
     */
    public function getTitleBgColor()
    {
        return $this->titleBgColor;
    }

    /**
     * @param string $titleBgColor
     */
    public function setTitleBgColor($titleBgColor)
    {
        $this->titleBgColor = $titleBgColor;
    }

    /**
     * @return string
     */
    public function getTitleFontColor()
    {
        return $this->titleFontColor;
    }

    /**
     * @param string $titleFontColor
     */
    public function setTitleFontColor($titleFontColor)
    {
        $this->titleFontColor = $titleFontColor;
    }

    /**
     * @return string
     */
    public function getTitleBorderColor()
    {
        return $this->titleBorderColor;
    }

    /**
     * @param string $titleBorderColor
     */
    public function setTitleBorderColor($titleBorderColor)
    {
        $this->titleBorderColor = $titleBorderColor;
    }

    /**
     * @return int
     */
    public function getTitleMarginBottom()
    {
        return $this->titleMarginBottom;
    }

    /**
     * @param int $titleMarginBottom
     */
    public function setTitleMarginBottom($titleMarginBottom)
    {
        $this->titleMarginBottom = $titleMarginBottom;
    }

    /**
     * @return int
     */
    public function getSliderTopPosition()
    {
        return $this->sliderTopPosition;
    }

    /**
     * @param int $sliderTopPosition
     */
    public function setSliderTopPosition($sliderTopPosition)
    {
        $this->sliderTopPosition = $sliderTopPosition;
    }

    /**
     * @return int
     */
    public function getSliderRightPosition()
    {
        return $this->sliderRightPosition;
    }

    /**
     * @param int $sliderRightPosition
     */
    public function setSliderRightPosition($sliderRightPosition)
    {
        $this->sliderRightPosition = $sliderRightPosition;
    }

    /**
     * @return string
     */
    public function getTitleTextAlign()
    {
        return $this->titleTextAlign;
    }

    /**
     * @param string $titleTextAlign
     */
    public function setTitleTextAlign($titleTextAlign)
    {
        $this->titleTextAlign = $titleTextAlign;
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
     * @return string
     */
    public function getCarouselPosition()
    {
        return $this->carouselPosition;
    }

    /**
     * @param string $carouselPosition
     */
    public function setCarouselPosition($carouselPosition)
    {
        $this->carouselPosition = $carouselPosition;
    }


}

