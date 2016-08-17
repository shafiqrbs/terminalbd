<?php

namespace Core\UserBundle\Entity;

use Appstore\Bundle\EcommerceBundle\Entity\Order;
use Appstore\Bundle\EcommerceBundle\Entity\PreOrder;
use Appstore\Bundle\InventoryBundle\Entity\StockItem;
use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table(name="fos_user")
 * @UniqueEntity(fields="username",message="User name already existing,Please try again.")
 * @UniqueEntity(fields="email",message="Email address already existing,Please try again.")
 * @ORM\Entity(repositoryClass="Core\UserBundle\Entity\Repository\UserRepository")
 */
class User extends BaseUser
{


    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     */
    protected $username;

    protected $role;

    protected $enabled=true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isDelete", type="boolean", nullable=true)
     */
    private $isDelete=false;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $avatar;

    /**
     * @ORM\ManyToMany(targetEntity="Group", inversedBy="users")
     * @ORM\JoinTable(name="user_user_group",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     */
    protected $groups;


    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="users" )
     *  * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="globalOption_id", referencedColumnName="id")
     * })
     **/

    protected $globalOption;

    /**
     * This part for system customer payment
     */

     /**
     * @ORM\OneToMany(targetEntity="Setting\Bundle\ContentBundle\Entity\Page", mappedBy="user" , cascade={"persist", "remove"} )
     */
    protected $pages;

    /**
     * @ORM\OneToMany(targetEntity="Setting\Bundle\ContentBundle\Entity\Branch", mappedBy="user" , cascade={"persist", "remove"} )
     */
    protected $branches;

    /**
     * @ORM\OneToMany(targetEntity="Setting\Bundle\ContentBundle\Entity\News", mappedBy="user" , cascade={"persist", "remove"})
     */
    protected $news;

    /**
     * @ORM\OneToMany(targetEntity="Setting\Bundle\ContentBundle\Entity\Admission", mappedBy="user" , cascade={"persist", "remove"})
     */
    protected $admissions;

    /**
     * @ORM\OneToMany(targetEntity="Setting\Bundle\ContentBundle\Entity\Admission", mappedBy="createUser" , cascade={"persist", "remove"})
     */
    protected $admissionPromotions;

    /**
     * @ORM\OneToMany(targetEntity="Setting\Bundle\ContentBundle\Entity\Faq", mappedBy="user" , cascade={"persist", "remove"} )
     */
    protected $faqs;

    /**
     * @ORM\OneToMany(targetEntity="Setting\Bundle\ContentBundle\Entity\Event", mappedBy="user" , cascade={"persist", "remove"})
     */
    protected $events;

    /**
     * @ORM\OneToMany(targetEntity="Setting\Bundle\ContentBundle\Entity\Blog", mappedBy="user" , cascade={"persist", "remove"})
     */
    protected $blogs;

    /**
     * @ORM\OneToMany(targetEntity="Setting\Bundle\ContentBundle\Entity\HomeSlider", mappedBy="user" , cascade={"persist", "remove"})
     */
    protected $homeSliders;

    /**
     * @ORM\OneToOne(targetEntity="Setting\Bundle\ContentBundle\Entity\Blackout", mappedBy="user" , cascade={"persist", "remove"})
     */
    protected $blackout;

    /**
     * @ORM\OneToMany(targetEntity="Setting\Bundle\ContentBundle\Entity\Testimonial", mappedBy="user" , cascade={"persist", "remove"})
     */
    protected $testimonials;

    /**
     * @ORM\OneToMany(targetEntity="Setting\Bundle\ContentBundle\Entity\NoticeBoard", mappedBy="user" , cascade={"persist", "remove"})
     */
    protected $noticeboards;

    /**
     * @ORM\OneToMany(targetEntity="Product\Bundle\ProductBundle\Entity\Product", mappedBy="user" , cascade={"persist", "remove"})
     */
    protected $products;


    /**
     * @ORM\OneToMany(targetEntity="Setting\Bundle\AppearanceBundle\Entity\Menu", mappedBy="user" , cascade={"persist", "remove"})
     * @ORM\OrderBy({"menu" = "ASC"})
     */
    protected $menus;

    /**
     * @ORM\OneToOne(targetEntity="Syndicate\Bundle\ComponentBundle\Entity\Education", mappedBy="user" , cascade={"persist", "remove"})
     */
    protected $education;

    /**
     * @ORM\OneToOne(targetEntity="Syndicate\Bundle\ComponentBundle\Entity\StudyAbroad", mappedBy="user" , cascade={"persist", "remove"})
     */
    protected $studyAbroad;

    /**
     * @ORM\OneToOne(targetEntity="Syndicate\Bundle\ComponentBundle\Entity\Tutor", mappedBy="user" , cascade={"persist", "remove"})
     */
    protected $tutor;

    /**
     * @ORM\OneToOne(targetEntity="Syndicate\Bundle\ComponentBundle\Entity\Vendor", mappedBy="user" , cascade={"persist", "remove"})
     */
    protected $vendor;



    /**
     * @ORM\OneToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\SiteSetting", mappedBy="user" , cascade={"persist", "remove"})
     **/

    private $siteSetting;
    /**
     * @ORM\OneToOne(targetEntity="Setting\Bundle\ContentBundle\Entity\HomePage", mappedBy="user" , cascade={"persist", "remove"})
     **/

    private $homePage;

    /**
     * @ORM\OneToOne(targetEntity="Setting\Bundle\ContentBundle\Entity\ContactPage", mappedBy="user" , cascade={"persist", "remove"})
     **/

    private $contactPage;

    /**
     * @ORM\OneToMany(targetEntity="Setting\Bundle\ContentBundle\Entity\SyndicateContent", mappedBy="user" , cascade={"persist", "remove"})
     */
    protected $syndicateContents;



    /**
     * @ORM\OneToOne(targetEntity="Profile", mappedBy="user", cascade={"persist", "remove"})
     *
     */
    protected $profile;

    /**
     * @ORM\OneToOne(targetEntity="Product\Bundle\ProductBundle\Entity\CategoryGrouping", mappedBy="user", cascade={"persist", "remove"})
     *
     */
    protected $categoryGrouping;


    /**
     * @ORM\OneToMany(targetEntity="Setting\Bundle\ContentBundle\Entity\ContactMessage", mappedBy="user" , cascade={"persist", "remove"})
     */
    protected $contactMessages;


    /**
     * @ORM\OneToMany(targetEntity="Setting\Bundle\ContentBundle\Entity\EmailBox", mappedBy="user" , cascade={"persist", "remove"})
     **/

    protected $emailBox;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\DomainUserBundle\Entity\CustomerInbox", mappedBy="replyUser" , cascade={"persist", "remove"})
     **/

    protected $customerInbox;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\DomainUserBundle\Entity\UserInbox", mappedBy="user" , cascade={"persist", "remove"})
     **/

    protected $userInbox;

    /* ----------------------------------inventory------------------*/



    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\StockItem", mappedBy="createdBy"  , cascade={"persist", "remove"})
     */
    protected $stockItems;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Purchase", mappedBy="createdBy" , cascade={"persist", "remove"})
     */
    protected $purchase;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Purchase", mappedBy="approvedBy" , cascade={"persist", "remove"} )
     */
    protected $purchasesApprovedBy;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\PurchaseReturn", mappedBy="createdBy" , cascade={"persist", "remove"} )
     */
    protected $purchaseReturn;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\PurchaseReturn", mappedBy="approvedBy" , cascade={"persist", "remove"} )
     */
    protected $purchasesReturnApprovedBy;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Sales", mappedBy="salesBy" , cascade={"persist", "remove"} )
     */
    protected $salesUser;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Sales", mappedBy="createdBy" , cascade={"persist", "remove"} )
     */
    protected $sales;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Sales", mappedBy="approvedBy" , cascade={"persist", "remove"} )
     */
    protected $salesApprovedBy;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\SalesReturn", mappedBy="createdBy" , cascade={"persist", "remove"})
     */
    protected $salesReturn;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\SalesImport", mappedBy="createdBy" , cascade={"persist", "remove"} )
     */
    protected $salesImport;

    /* ----------------------------------Accounting------------------*/


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountPurchase", mappedBy="createdBy" , cascade={"persist", "remove"} )
     */
    protected $accountPurchases;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountPurchase", mappedBy="approvedBy" , cascade={"persist", "remove"} )
     */
    protected $purchaseApprove;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountPurchase", mappedBy="toUser" , cascade={"persist", "remove"} )
     */
    protected $purchasesToUser;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountSales", mappedBy="createdBy" , cascade={"persist", "remove"} )
     */
    protected $accountSales;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountSales", mappedBy="approvedBy" , cascade={"persist", "remove"} )
     */
    protected $salesApprove;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\PettyCash", mappedBy="createdBy" , cascade={"persist", "remove"} )
     */
    protected $pettyCash;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\PettyCash", mappedBy="toUser" , cascade={"persist", "remove"} )
     */
    protected $pettyCashToUser;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\PettyCash", mappedBy="approvedBy" , cascade={"persist", "remove"} )
     */
    protected $pettyCashApprove;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\Expenditure", mappedBy="createdBy" , cascade={"persist", "remove"} )
     */
    protected $expenditure;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\Expenditure", mappedBy="toUser" , cascade={"persist", "remove"} )
     */
    protected $expenditureToUser;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\Expenditure", mappedBy="approvedBy" , cascade={"persist", "remove"} )
     */
    protected $expenditureApprove;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountBank", mappedBy="createdBy" , cascade={"persist", "remove"} )
     */
    protected $accountBanks;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountBank", mappedBy="toUser" , cascade={"persist", "remove"} )
     */
    protected $bankToUser;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountBank", mappedBy="approvedBy" , cascade={"persist", "remove"} )
     */
    protected $bankApprove;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\PaymentSalary", mappedBy="createdBy" , cascade={"persist", "remove"} )
     * @ORM\OrderBy({"updated" = "DESC"})
     */
    protected $paymentSalary;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\PaymentSalary", mappedBy="user" , cascade={"persist", "remove"} )
     * @ORM\OrderBy({"updated" = "DESC"})
     */
    protected $paymentSalaries;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\PaymentSalary", mappedBy="approvedBy" , cascade={"persist", "remove"} )
     */
    protected $paymentSalaryApprove;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\SalarySetting", mappedBy="user" , cascade={"persist", "remove"} )
     */
    protected $employeeSalaries;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\SalarySetting", mappedBy="createdBy" , cascade={"persist", "remove"} )
     */
    protected $salarySetting;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\SalarySetting", mappedBy="approvedBy" , cascade={"persist", "remove"} )
     */
    protected $salarySettingApproved;


    /*------------------------------------------------ Domain ---------------------------------*/


    /**
     * @ORM\OneToMany(targetEntity="Setting\Bundle\ToolBundle\Entity\InvoiceSmsEmail", mappedBy="createdBy" , cascade={"persist", "remove"} )
     */
    protected $invoiceSmsEmail;

    /**
     * @ORM\OneToMany(targetEntity="Setting\Bundle\ToolBundle\Entity\InvoiceSmsEmail", mappedBy="receivedBy" , cascade={"persist", "remove"} )
     */
    protected $invoiceSmsEmailReceivedBy;


    /**
     * @ORM\OneToMany(targetEntity="Setting\Bundle\ToolBundle\Entity\InvoiceModule", mappedBy="createdBy" , cascade={"persist", "remove"} )
     */
    protected $invoiceModule;

    /**
     * @ORM\OneToMany(targetEntity="Setting\Bundle\ToolBundle\Entity\InvoiceModule", mappedBy="paymentBy" , cascade={"persist", "remove"} )
     */
    protected $invoiceModulePaymentBy;

    /**
     * @ORM\OneToMany(targetEntity="Setting\Bundle\ToolBundle\Entity\InvoiceModule", mappedBy="receivedBy" , cascade={"persist", "remove"} )
     */
    protected $invoiceModuleReceivedBy;

    /**
    =========================================== E-commerce============================================
     */


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\Order", mappedBy="createdBy"  )
     **/
    private  $orders;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\PreOrder", mappedBy="createdBy"  )
     **/
    private  $preOrders;


    public function isGranted($role)
    {
        return in_array($role, $this->getRoles());
    }

    /**
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    public function toArray($collection)
    {
        $this->setRoles($collection->toArray());
    }

    public function setRole($role)
    {
        $this->getRoles();
        $this->addRole($role);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getRole()
    {
        $role = $this->getRoles();

        return $role[0];
    }


    /**
     * @param mixed $profile
     */
    public function setProfile($profile)
    {
        $profile->setUser($this);
        $this->profile = $profile;
    }

    /**
     * @return mixed
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * get avatar image file name
     *
     * @return string
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * set avatar image file name
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;
    }

    public function isSuperAdmin()
    {
        $groups = $this->getGroups();
        foreach ($groups as $group) {
            if ($group->hasRole('ROLE_SUPER_ADMIN')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return boolean
     */
    public function getNotification()
    {
        return $this->notification;
    }

    /**
     * @param boolean $notification
     *
     * @return $this
     */
    public function setNotification($notification)
    {
        $this->notification = $notification;
        return $this;
    }


    /**
     * @param mixed $education
     */
    public function setEducation($education)
    {
        $education->setUser($this);
        $this->education = $education;
    }

    /**
     * @return mixed
     */
    public function getEducation()
    {
        return $this->education;
    }

    /**
     * @param mixed $pages
     */
    public function setPages($pages)
    {
        $this->pages = $pages;
    }

    /**
     * @return mixed
     */
    public function getPages()
    {
        return $this->pages;
    }

    /**
     * @param mixed $menus
     */
    public function setMenus($menus)
    {
        $this->menus = $menus;
    }

    /**
     * @return mixed
     */
    public function getMenus()
    {
        return $this->menus;
    }


    /**
     * @param mixed $siteSetting
     */
    public function setSiteSetting($siteSetting)
    {
        $siteSetting->setUser($this);
        $this->siteSetting = $siteSetting;
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
    public function getBlackout()
    {
        return $this->blackout;
    }

    /**
     * @param mixed $blackout
     */
    public function setBlackout($blackout)
    {
        $this->blackout = $blackout;
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
     * @return mixed
     */
    public function getGlobalOption()
    {
        return $this->globalOption;
    }

    /**
     * @param mixed $globalOption
     */
    public function setGlobalOption($globalOption)
    {
        $this->globalOption = $globalOption;
    }

    /**
     * @return mixed
     */
    public function getNews()
    {
        return $this->news;
    }


    /**
     * @return mixed
     */
    public function getHomePage()
    {
        return $this->homePage;
    }

    /**
     * @param mixed $homePage
     */
    public function setHomePage($homePage)
    {
        $this->homePage = $homePage;
    }

    /**
     * @return mixed
     */
    public function getContactPage()
    {
        return $this->contactPage;
    }

    /**
     * @param mixed $contactPage
     */
    public function setContactPage($contactPage)
    {
        $this->contactPage = $contactPage;
    }

    /**
     * @return mixed
     */
    public function getBranches()
    {
        return $this->branches;
    }

    /**
     * @return mixed
     */
    public function getSyndicateContents()
    {
        return $this->syndicateContents;
    }

    /**
     * @return mixed
     */
    public function getAdmissions()
    {
        return $this->admissions;
    }

    /**
     * @return mixed
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * @return mixed
     */
    public function getBlogs()
    {
        return $this->blogs;
    }

    /**
     * @param mixed $blogs
     */
    public function setBlogs($blogs)
    {
        $this->blogs = $blogs;
    }

    /**
     * @return mixed
     */
    public function getTestimonials()
    {
        return $this->testimonials;
    }

    /**
     * @return mixed
     */
    public function getNoticeboards()
    {
        return $this->noticeboards;
    }

    /**
     * @return mixed
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @return mixed
     */
    public function getVendor()
    {
        return $this->vendor;
    }

    /**
     * @param mixed $vendor
     */
    public function setVendor($vendor)
    {
        $this->vendor = $vendor;
    }

    /**
     * @return mixed
     */
    public function getCategoryGrouping()
    {
        return $this->categoryGrouping;
    }

    /**
     * @return mixed
     */
    public function getHomeSliders()
    {
        return $this->homeSliders;
    }

    /**
     * @return mixed
     */
    public function getFaqs()
    {
        return $this->faqs;
    }

    /**
     * @return mixed
     */
    public function getStudyAbroad()
    {
        return $this->studyAbroad;
    }

    /**
     * @return mixed
     */
    public function getTutor()
    {
        return $this->tutor;
    }


    /**
     * @return mixed
     */
    public function getSalesUser()
    {
        return $this->salesUser;
    }

    /**
     * @return mixed
     */
    public function getSales()
    {
        return $this->sales;
    }

    /**
     * @return mixed
     */
    public function getPurchaseReturn()
    {
        return $this->purchaseReturn;
    }

    /**
     * @return mixed
     */
    public function getPurchasesReturnApprovedBy()
    {
        return $this->purchasesReturnApprovedBy;
    }


    /**
     * @return boolean
     */
    public function getIsDelete()
    {
        return $this->isDelete;
    }

    /**
     * @param boolean $isDelete
     */
    public function setIsDelete($isDelete)
    {
        $this->isDelete = $isDelete;
    }

    /**
     * @return mixed
     */
    public function getSalesReturn()
    {
        return $this->salesReturn;
    }

    /**
     * @return mixed
     */
    public function getPattyCash()
    {
        return $this->pattyCash;
    }

    /**
     * @return mixed
     */
    public function getPettyCashApprove()
    {
        return $this->pettyCashApprove;
    }

    /**
     * @return mixed
     */
    public function getExpenditure()
    {
        return $this->expenditure;
    }

    /**
     * @return mixed
     */
    public function getExpenditureToUser()
    {
        return $this->expenditureToUser;
    }

    /**
     * @return mixed
     */
    public function getExpenditureApprove()
    {
        return $this->expenditureApprove;
    }

    /**
     * @return mixed
     */
    public function getPaymentSalaries()
    {
        return $this->paymentSalaries;
    }

    /**
     * @return mixed
     */
    public function getSalesApprovedBy()
    {
        return $this->salesApprovedBy;
    }

    /**
     * @return mixed
     */
    public function getInvoiceSmsEmail()
    {
        return $this->invoiceSmsEmail;
    }

    /**
     * @return mixed
     */
    public function getInvoiceSmsEmailReceivedBy()
    {
        return $this->invoiceSmsEmailReceivedBy;
    }

    /**
     * @return mixed
     */
    public function getSalesImport()
    {
        return $this->salesImport;
    }

    /**
     * @return StockItem
     */
    public function getStockItems()
    {
        return $this->stockItems;
    }

    /**
     * @return Order
     */
    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * @return PreOrder
     */
    public function getPreOrders()
    {
        return $this->preOrders;
    }

    public function getCheckRoleEcommercePreorder($role = NULL)
    {

        $roles = array(
            'ROLE_DOMAIN_INVENTORY_ECOMMERCE',
            'ROLE_DOMAIN_INVENTORY_ECOMMERCE_MANAGER',
            'ROLE_DOMAIN_INVENTORY_MANAGER',
            'ROLE_DOMAIN_INVENTORY_APPROVE',
            'ROLE_DOMAIN_MANAGER',
            'ROLE_DOMAIN'
        );

        if(in_array($role,$roles)){
            return true;
        }else{
            return false;
        }

    }



}