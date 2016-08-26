<?php

namespace Setting\Bundle\ToolBundle\Entity;
use Appstore\Bundle\AccountingBundle\Entity\Transaction;
use Appstore\Bundle\DomainUserBundle\Entity\DomainUser;
use Appstore\Bundle\EcommerceBundle\Entity\EcommerceConfig;
use Core\UserBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Setting\Bundle\ContentBundle\Entity\Admission;
use Setting\Bundle\ContentBundle\Entity\Blog;
use Setting\Bundle\ContentBundle\Entity\Branch;
use Setting\Bundle\ContentBundle\Entity\ContactPage;
use Setting\Bundle\ContentBundle\Entity\Event;
use Setting\Bundle\ContentBundle\Entity\HomePage;
use Setting\Bundle\ContentBundle\Entity\HomeSlider;
use Setting\Bundle\ContentBundle\Entity\MallConnect;
use Setting\Bundle\ContentBundle\Entity\ModuleCategory;
use Setting\Bundle\ContentBundle\Entity\News;
use Setting\Bundle\ContentBundle\Entity\NoticeBoard;
use Setting\Bundle\ContentBundle\Entity\Page;
use Setting\Bundle\ContentBundle\Entity\Portfolio;
use Setting\Bundle\ContentBundle\Entity\Service;
use Setting\Bundle\ContentBundle\Entity\Team;
use Setting\Bundle\ContentBundle\Entity\Testimonial;
use Setting\Bundle\ContentBundle\Entity\TradeItem;
use Setting\Bundle\LocationBundle\Entity\Location;
use Setting\Bundle\MediaBundle\Entity\PhotoGallery;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * GlobalOption
 * @UniqueEntity(fields="domain",message="This data is already in use.")
 * @UniqueEntity(fields="mobile",message="This mobile no is already in use.")
 * @UniqueEntity(fields="subDomain",message="This data is already in use.")
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Setting\Bundle\ToolBundle\Repository\GlobalOptionRepository")
 */
class GlobalOption
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
     * @ORM\OneToMany(targetEntity="Core\UserBundle\Entity\User", mappedBy="globalOption" , cascade={"persist", "remove"} )
     **/
    protected $users;


    /**
     * @ORM\OneToMany(targetEntity="Setting\Bundle\MediaBundle\Entity\PhotoGallery", mappedBy="globalOption" , cascade={"persist", "remove"})
     */
    protected $photoGalleries;

    /**
     * @ORM\OneToMany(targetEntity="Setting\Bundle\AppearanceBundle\Entity\Menu", mappedBy="globalOption" , cascade={"persist", "remove"} )
     * @ORM\OrderBy({"menu" = "ASC"})
     */
    protected $menus;

    /**
     * @ORM\OneToMany(targetEntity="Setting\Bundle\AppearanceBundle\Entity\MenuGrouping", mappedBy="globalOption" , cascade={"persist", "remove"} )
     */
    protected $menuGroupings;


    /**
     * @ORM\OneToMany(targetEntity="Setting\Bundle\ContentBundle\Entity\Page", mappedBy="globalOption" , cascade={"persist", "remove"} )
     **/
    protected $pages;


    /**
     * @ORM\OneToMany(targetEntity="Setting\Bundle\ContentBundle\Entity\ModuleCategory", mappedBy="globalOption" , cascade={"persist", "remove"} )
     **/
    protected $moduleCategories;


    /**
     * @ORM\OneToMany(targetEntity="Setting\Bundle\ContentBundle\Entity\Blackout", mappedBy="globalOption" , cascade={"persist", "remove"} )
     **/
    protected $blackout;


    /**
     * @ORM\OneToMany(targetEntity="Setting\Bundle\ContentBundle\Entity\HomeSlider", mappedBy="globalOption" , cascade={"persist", "remove"} )
     * @ORM\OrderBy({"updated" = "DESC"})
     **/
    protected $homeSliders;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\DomainUserBundle\Entity\DomainUser", mappedBy="globalOption" , cascade={"persist", "remove"} )
     **/
    protected $domainUser;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\DomainUserBundle\Entity\Customer", mappedBy="globalOption" , cascade={"persist", "remove"} )
     **/
    protected $customers;


    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\LocationBundle\Entity\Location", inversedBy="globalOptions" , cascade={"persist", "remove"} )
     **/

    protected $location;

    /**/

    /**
     * @var string
     *
     * @ORM\Column(name="mobile", type="string", length=15, nullable = true )
     */
    private $mobile;


    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=100, nullable = true )
     */
    private $email;


    /**
     * @ORM\ManyToMany(targetEntity="Setting\Bundle\AppearanceBundle\Entity\MegaMenu", mappedBy="globalOptions" , cascade={"persist", "remove"} )
     **/

    private $megaMenu;

    /**
     * @ORM\OneToMany(targetEntity="Setting\Bundle\AdvertismentBundle\Entity\Advertisment", mappedBy="globalOption" , cascade={"persist", "remove"} )
     */
    protected $advertisment;


    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255  , nullable=true )
     */
    private $name;

    /**
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(length=255, unique=true)
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="domain", type="string", length=255 , unique=true , nullable=true)
     */
    private $domain;


    /**
     * @var string
     *
     * @ORM\Column(name="subDomain", type="string", length=255 , unique=true, nullable=true)
     */
    private $subDomain;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isMobile", type="boolean" , nullable=true)
     */
    private $isMobile;


    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\Syndicate", inversedBy="globalOption" , cascade={"persist", "remove"} )
     **/

    private $syndicate;

    /**
     * @ORM\OneToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\SiteSetting", mappedBy="globalOption" , cascade={"persist", "remove"} )
     **/

    private $siteSetting;

    /**
     * @ORM\OneToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\AdsTool", mappedBy="globalOption" ,  cascade={"remove"} )
     **/

    private $adsTool;

    /**
     * @ORM\OneToOne(targetEntity="Setting\Bundle\ContentBundle\Entity\Homepage", mappedBy="globalOption" , cascade={"persist", "remove"} )
     **/

    private $homePage;

    /**
     * @ORM\OneToOne(targetEntity="Setting\Bundle\ContentBundle\Entity\ContactPage", mappedBy="globalOption" , cascade={"remove"})
     **/

    private $contactPage;


    /**
     * @ORM\OneToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\TemplateCustomize", mappedBy="globalOption" , cascade={"remove"})
     **/

    private $templateCustomize;

    /**
     * @ORM\OneToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\MobileIcon", mappedBy="globalOption" , cascade={"persist", "remove"})
     **/

    private $mobileIcon;

    /**
     * @ORM\OneToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\FooterSetting", mappedBy="globalOption" , cascade={"persist", "remove"})
     **/

    private $footerSetting;

    /* This part using for Appstore bundle under Accounting module */


    /**
     * @var boolean
     *
     * @ORM\Column(name="customizeDesign", type="boolean" , nullable=true)
     */
    private $customizeDesign;

    /**
     * @var boolean
     *
     * @ORM\Column(name="facebookAds", type="boolean" , nullable=true)
     */
    private $facebookAds;

    /**
     * @var boolean
     *
     * @ORM\Column(name="facebookApps", type="boolean" , nullable=true)
     */
    private $facebookApps;

    /**
     * @var string
     *
     * @ORM\Column(name="facebookPageUrl", type="string", length=255 , nullable=true)
     */
    private $facebookPageUrl;

     /**
     * @var string
     *
     * @ORM\Column(name="twitterUrl", type="string", length=255 , nullable=true)
     */
    private $twitterUrl;

     /**
     * @var string
     *
     * @ORM\Column(name="googlePlus", type="string", length=255 , nullable=true)
     */
    private $googlePlus;

    /**
     * @var boolean
     *
     * @ORM\Column(name="promotion", type="boolean" , nullable=true)
     */
    private $promotion;

    /**
     * @var boolean
     *
     * @ORM\Column(name="googleAds", type="boolean" , nullable=true)
     */
    private $googleAds;

    /**
     * @var boolean
     *
     * @ORM\Column(name="smsIntegration", type="boolean" , nullable=true)
     */
    private $smsIntegration;

    /**
     * @var boolean
     *
     * @ORM\Column(name="emailIntegration", type="boolean" , nullable=true)
     */
    private $emailIntegration;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isIntro", type="boolean" , nullable=true)
     */
    private $isIntro;


    /**
     * @var string
     *
     * @ORM\Column(name="callBackEmail", type="string", length=255 , nullable=true)
     */
    private $callBackEmail;

    /**
     * @var text
     *
     * @ORM\Column(name="callBackContent", type="text", nullable=true)
     */
    private $callBackContent;

    /**
     * @var boolean
     *
     * @ORM\Column(name="callBackNotify", type="boolean", nullable=true)
     */
    private $callBackNotify;

    /**
     * @var boolean
     *
     * @ORM\Column(name="primaryNumber", type="boolean", nullable=true)
     */
    private $primaryNumber = true;

    /**
     * @var string
     *
     * @ORM\Column(name="leaveEmail", type="string", length=255 , nullable=true)
     */
    private $leaveEmail;

    /**
     * @var string
     *
     * @ORM\Column(name="webMail", type="string", length=255 , nullable=true)
     */
    private $webMail;

    /**
     * @var text
     *
     * @ORM\Column(name="leaveContent", type="text", nullable=true)
     */
    private $leaveContent;


    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="smallint", nullable=true)
     */
    private $status = 0;

    /* App store relation for application work this domain */

    /**
     * @ORM\OneToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\InventoryConfig", mappedBy="globalOption" , cascade={"persist", "remove"})
     **/

    private $inventoryConfig;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\Transaction", mappedBy="globalOption" , cascade={"persist", "remove"})
     */
    protected $transactions;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountPurchase", mappedBy="globalOption" , cascade={"persist", "remove"})
     */
    protected $accountPurchase;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountSales", mappedBy="globalOption" , cascade={"persist", "remove"})
     */
    protected $accountSales;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\PettyCash", mappedBy="globalOption" , cascade={"persist", "remove"})
     */
    protected $pettyCash;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\Expenditure", mappedBy="globalOption" , cascade={"persist", "remove"})
     */
    protected $expenditure;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\ExpenseCategory", mappedBy="globalOption" , cascade={"persist", "remove"})
     */
    protected $expenseCategory;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountBank", mappedBy="globalOption" , cascade={"persist", "remove"})
     */
    protected $accountBank;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\PaymentSalary", mappedBy="globalOption" , cascade={"persist", "remove"})
     */
    protected $paymentSalary;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\SalarySetting", mappedBy="globalOption" , cascade={"persist", "remove"})
     */
    protected $salarySetting;

    /*---------------------- Manage Domain Pricing-------------------------------------*/

    /**
     * @ORM\OneToMany(targetEntity="Setting\Bundle\ToolBundle\Entity\InvoiceSmsEmail", mappedBy="globalOption" , cascade={"persist", "remove"})
     * @ORM\OrderBy({"updated" = "DESC"})
     */
    protected $invoiceSmsEmails;

    /**
     * @ORM\OneToMany(targetEntity="Setting\Bundle\ToolBundle\Entity\InvoiceModule", mappedBy="globalOption" , cascade={"persist", "remove"})
     * @ORM\OrderBy({"updated" = "DESC"})
     */
    protected $invoiceModules;

    /*---------------------- Manage Education Portal-------------------------------------*/
    /**
     * @ORM\ManyToMany(targetEntity="Setting\Bundle\ToolBundle\Entity\InstituteLevel", inversedBy="globalOptions" , cascade={"persist", "remove"})
     */
    protected $instituteLevels;

    /*========================= Ecommerce & Payment Method Integration ================================*/

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\PaymentMethod", mappedBy="globalOption" , cascade={"persist", "remove"})
     */
    protected $paymentMethods;

    /**
     * @ORM\OneToOne(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\EcommerceConfig", mappedBy="globalOption" , cascade={"persist", "remove"})
     */
    protected $ecommerceConfig;

    /*===================================Mall Connect =====================================*/

    /**
     * @ORM\OneToMany(targetEntity="Setting\Bundle\ContentBundle\Entity\MallConnect", mappedBy="mall" , cascade={"persist", "remove"})
     */
    protected $shops;

    /**
     * @ORM\OneToMany(targetEntity="Setting\Bundle\ContentBundle\Entity\MallConnect", mappedBy="globalOption" , cascade={"persist", "remove"})
     */
    protected $mallConnects;



    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated", type="datetime")
     */
    private $updated;

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
     * @param mixed $syndicate
     */
    public function setSyndicate($syndicate)
    {
        $this->syndicate = $syndicate;
    }

    /**
     * @return mixed
     */
    public function getSyndicate()
    {
        return $this->syndicate;
    }


    /**
     * Set domain
     *
     * @param string $domain
     * @return GlobalOption
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * Get domain
     *
     * @return string 
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Set subDomain
     *
     * @param string $subDomain
     * @return GlobalOption
     */
    public function setSubDomain($subDomain)
    {
        $this->subDomain = $subDomain;

        return $this;
    }

    /**
     * Get subDomain
     *
     * @return string 
     */
    public function getSubDomain()
    {
        return $this->subDomain;
    }

    /**
     * Set isMobile
     *
     * @param boolean $isMobile
     * @return GlobalOption
     */
    public function setIsMobile($isMobile)
    {
        $this->isMobile = $isMobile;

        return $this;
    }

    /**
     * Get isMobile
     *
     * @return boolean 
     */
    public function getIsMobile()
    {
        return $this->isMobile;
    }

    /**
     * @param mixed $mobileTheme
     */
    public function setMobileTheme($mobileTheme)
    {
        $this->mobileTheme = $mobileTheme;

        return $this;
    }

    /**
     * @param mixed $mobileTheme
     */
    public function getMobileTheme()
    {
        return $this->mobileTheme;
    }


    /**
     * @param mixed $webTheme
     */
    public function setWebTheme($webTheme)
    {
        $this->webTheme = $webTheme;
    }

    /**
     * @return mixed
     */
    public function getWebTheme()
    {
        return $this->webTheme;
    }



    /**
     * Set customizeDesign
     *
     * @param boolean $customizeDesign
     * @return GlobalOption
     */
    public function setCustomizeDesign($customizeDesign)
    {
        $this->customizeDesign = $customizeDesign;

        return $this;
    }

    /**
     * Get customizeDesign
     *
     * @return boolean 
     */
    public function getCustomizeDesign()
    {
        return $this->customizeDesign;
    }

    /**
     * Set facebookAds
     *
     * @param boolean $facebookAds
     * @return GlobalOption
     */
    public function setFacebookAds($facebookAds)
    {
        $this->facebookAds = $facebookAds;

        return $this;
    }

    /**
     * Get facebookAds
     *
     * @return boolean 
     */
    public function getFacebookAds()
    {
        return $this->facebookAds;
    }

    /**
     * Set facebookApps
     *
     * @param boolean $facebookApps
     * @return GlobalOption
     */
    public function setFacebookApps($facebookApps)
    {
        $this->facebookApps = $facebookApps;

        return $this;
    }

    /**
     * Get facebookApps
     *
     * @return boolean 
     */
    public function getFacebookApps()
    {
        return $this->facebookApps;
    }

    /**
     * Set facebookPageUrl
     *
     * @param string $facebookPageUrl
     * @return GlobalOption
     */
    public function setFacebookPageUrl($facebookPageUrl)
    {
        $this->facebookPageUrl = $facebookPageUrl;

        return $this;
    }

    /**
     * Get facebookPageUrl
     *
     * @return string 
     */
    public function getFacebookPageUrl()
    {
        return $this->facebookPageUrl;
    }

    /**
     * Set promotion
     *
     * @param boolean $promotion
     * @return GlobalOption
     */
    public function setPromotion($promotion)
    {
        $this->promotion = $promotion;

        return $this;
    }

    /**
     * Get promotion
     *
     * @return boolean 
     */
    public function getPromotion()
    {
        return $this->promotion;
    }



    /**
     * @return smallint
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param smallint $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return boolean
     */
    public function getIsIntro()
    {
        return $this->isIntro;
    }

    /**
     * @param boolean $isIntro
     */
    public function setIsIntro($isIntro)
    {
        $this->isIntro = $isIntro;
    }


    /**
     * @return boolean
     */
    public function getGoogleAds()
    {
        return $this->googleAds;
    }

    /**
     * @param boolean $googleAds
     */
    public function setGoogleAds($googleAds)
    {
        $this->googleAds = $googleAds;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getSiteSetting()
    {
        return $this->siteSetting;
    }

    /**
     * @return mixed
     */
    public function getAdvertisment()
    {
        return $this->advertisment;
    }

    /**
     * @return mixed
     */
    public function getMegaMenu()
    {
        return $this->megaMenu;
    }

    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param mixed $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * @return string
     */
    public function getTwitterUrl()
    {
        return $this->twitterUrl;
    }

    /**
     * @param string $twitterUrl
     */
    public function setTwitterUrl($twitterUrl)
    {
        $this->twitterUrl = $twitterUrl;
    }

    /**
     * @return string
     */
    public function getGooglePlus()
    {
        return $this->googlePlus;
    }

    /**
     * @param string $googlePlus
     */
    public function setGooglePlus($googlePlus)
    {
        $this->googlePlus = $googlePlus;
    }

    /**
     * @return boolean
     */
    public function isSmsIntegration()
    {
        return $this->smsIntegration;
    }

    /**
     * @param boolean $smsIntegration
     */
    public function setSmsIntegration($smsIntegration)
    {
        $this->smsIntegration = $smsIntegration;
    }

    /**
     * @return boolean
     */
    public function isEmailIntegration()
    {
        return $this->emailIntegration;
    }

    /**
     * @param boolean $emailIntegration
     */
    public function setEmailIntegration($emailIntegration)
    {
        $this->emailIntegration = $emailIntegration;
    }


    /**
     * @return Location
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param mixed $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @return string
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * @param string $mobile
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;
    }

    /**
     * @return TemplateCustomize
     */
    public function getTemplateCustomize()
    {
        return $this->templateCustomize;
    }

    /**
     * @return HomePage
     */
    public function getHomePage()
    {
        return $this->homePage;
    }

    /**
     * @return ContactPage
     */
    public function getContactPage()
    {
        return $this->contactPage;
    }

    /**
     * @return mixed
     */
    public function getFooterSetting()
    {
        return $this->footerSetting;
    }

    /**
     * @return mixed
     */
    public function getMobileIcon()
    {
        return $this->mobileIcon;
    }

    /**
     * @return string
     */
    public function getCallBackEmail()
    {
        return $this->callBackEmail;
    }

    /**
     * @param string $callBackEmail
     */
    public function setCallBackEmail($callBackEmail)
    {
        $this->callBackEmail = $callBackEmail;
    }

    /**
     * @return text
     */
    public function getCallBackContent()
    {
        return $this->callBackContent;
    }

    /**
     * @param text $callBackContent
     */
    public function setCallBackContent($callBackContent)
    {
        $this->callBackContent = $callBackContent;
    }

    /**
     * @return string
     */
    public function getLeaveEmail()
    {
        return $this->leaveEmail;
    }

    /**
     * @param string $leaveEmail
     */
    public function setLeaveEmail($leaveEmail)
    {
        $this->leaveEmail = $leaveEmail;
    }

    /**
     * @return text
     */
    public function getLeaveContent()
    {
        return $this->leaveContent;
    }

    /**
     * @param text $leaveContent
     */
    public function setLeaveContent($leaveContent)
    {
        $this->leaveContent = $leaveContent;
    }

    /**
     * @return boolean
     */
    public function isCallBackNotify()
    {
        return $this->callBackNotify;
    }

    /**
     * @param boolean $callBackNotify
     */
    public function setCallBackNotify($callBackNotify)
    {
        $this->callBackNotify = $callBackNotify;
    }

    /**
     * @return boolean
     */
    public function isPrimaryNumber()
    {
        return $this->primaryNumber;
    }

    /**
     * @param boolean $primaryNumber
     */
    public function setPrimaryNumber($primaryNumber)
    {
        $this->primaryNumber = $primaryNumber;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return DomainUser
     */
    public function getDomainUser()
    {
        return $this->domainUser;
    }

    /**
     * @return mixed
     */
    public function getAdsTool()
    {
        return $this->adsTool;
    }

    /**
     * @return mixed
     */
    public function getItemTypeGrouping()
    {
        return $this->itemTypeGrouping;
    }

    /**
     * @return InventorConfig
     */
    public function getInventoryConfig()
    {
        return $this->inventoryConfig;
    }

    /**
     * @return User[]
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @return mixed
     */
    public function getMenuGrouping()
    {
        return $this->menuGrouping;
    }

    /**
     * @param mixed $menuGrouping
     */
    public function setMenuGrouping($menuGrouping)
    {
        $this->menuGrouping = $menuGrouping;
    }

    /**
     * @return Page
     */
    public function getPages()
    {
        return $this->pages;
    }

    /**
     * @return Blog
     */
    public function getBlogs()
    {
        return $this->blogs;
    }

    /**
     * @return Testimonial
     */
    public function getTestimonials()
    {
        return $this->testimonials;
    }


    /**
     * @return mixed
     */
    public function getFaqs()
    {
        return $this->faqs;
    }

    /**
     * @return News
     */
    public function getNewses()
    {
        return $this->newses;
    }

    /**
     * @return Branch
     */
    public function getBranches()
    {
        return $this->branches;
    }

    /**
     * @return Admission
     */
    public function getAdmissions()
    {
        return $this->admissions;
    }

    /**
     * @return mixed
     */
    public function getAccountPurchase()
    {
        return $this->accountPurchase;
    }

    /**
     * @return mixed
     */
    public function getPettyCash()
    {
        return $this->pettyCash;
    }

    /**
     * @return HomeSlider
     */
    public function getHomeSliders()
    {
        return $this->homeSliders;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param \DateTime $updated
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }

    public function getCurrentStatus()
    {
        $status = '';
        if($this->status == 1){
            $status = 'Active';
        }else if($this->status == 2){
            $status = 'Hold';
        }else if($this->status == 3){
            $status = 'Suspended';
        }
        return $status;
    }

    /**
     * @return PortalBankAccount
     */
    public function getPortalBankAccount()
    {
        return $this->portalBankAccount;
    }

    /**
     * @return mixed
     */
    public function getInvoiceSmsEmails()
    {
        return $this->invoiceSmsEmails;
    }

    /**
     * @return mixed
     */
    public function getInvoiceModules()
    {
        return $this->invoiceModules;
    }

    /**
     * @return string
     */
    public function getWebMail()
    {
        return $this->webMail;
    }

    /**
     * @param string $webMail
     */
    public function setWebMail($webMail)
    {
        $this->webMail = $webMail;
    }

    /**
     * @return mixed
     */
    public function getExpenseCategory()
    {
        return $this->expenseCategory;
    }

    /**
     * @return Transaction
     */
    public function getTransactions()
    {
        return $this->transactions;
    }

    /**
     * @return EcommerceConfig
     */
    public function getEcommerceConfig()
    {
        return $this->ecommerceConfig;
    }

    /**
     * @return NoticeBoard
     */
    public function getNoticeBoards()
    {
        return $this->noticeBoards;
    }

    /**
     * @return Event
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * @return MallConnect
     */
    public function getShops()
    {
        return $this->shops;
    }

    /**
     * @return MallConnect
     */
    public function getMallConnects()
    {
        return $this->mallConnects;
    }

    /**
     * @return PhotoGallery
     */
    public function getPhotoGalleries()
    {
        return $this->photoGalleries;
    }

    /**
     * @return Team
     */
    public function getTeams()
    {
        return $this->teams;
    }

    /**
     * @return ModuleCategory
     */
    public function getModuleCategories()
    {
        return $this->moduleCategories;
    }

    /**
     * @return TradeItem
     */
    public function getTradeItems()
    {
        return $this->tradeItems;
    }

    /**
     * @return Portfolio
     */
    public function getPortfolios()
    {
        return $this->portfolios;
    }

    /**
     * @return Service
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * @return mixed
     */
    public function getClients()
    {
        return $this->clients;
    }


}
