<?php
/**
 * Created by PhpStorm.
 * User: shafiq
 * Date: 3/4/15
 * Time: 3:36 PM
 */

namespace Setting\Bundle\AppearanceBundle\Menu;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Setting\Bundle\AppearanceBundle\Entity\EcommerceMenu;
use Setting\Bundle\AppearanceBundle\Entity\MegaMenu;
use Setting\Bundle\ToolBundle\Entity\Branding;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\DependencyInjection\ContainerAware;
use Setting\Bundle\AppearanceBundle\Entity\MenuGrouping;
use Product\Bundle\ProductBundle\Entity\Category;
use Symfony\Component\HttpFoundation\Request;


class Builder extends ContainerAware
{

    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $securityContext = $this->container->get('security.token_storage')->getToken()->getUser();
        $globalOption = $securityContext->getGlobalOption();

        $menu = $factory->createItem('root');
        $menu->setChildrenAttribute('class', 'page-sidebar-menu');
        $menu = $this->dashboardMenu($menu);
        if ($securityContext->getRole() === 'ROLE_SUPER_ADMIN') {

            $menu = $this->toolsMenu($menu);
           // $menu = $this->productCategoryMenu($menu);
            $menu = $this->manageFrontendMenu($menu);
           // $menu = $this->manageVendorMenu($menu);
           // $menu = $this->manageAdvertismentMenu($menu);
            $menu = $this->manageApplicationSettingMenu($menu);
            $menu = $this->manageDomainMenu($menu);
          //  $menu = $this->manageSystemAccountMenu($menu);
            $menu = $this->PayrollMenu($menu);
            $menu = $this->businessMenu($menu);
          //  $menu = $this->reservationMenu($menu);

        }

            $modules = $globalOption->getSiteSetting()->getAppModules();
            $arrSlugs = array();
            $menuName = array();
            if (!empty($globalOption->getSiteSetting()) and !empty($modules)) {
                foreach ($globalOption->getSiteSetting()->getAppModules() as $mod) {
                    if (!empty($mod->getModuleClass())) {
                        $menuName[] = $mod->getModuleClass();
                        $arrSlugs[] = $mod->getSlug();
                    }
                }
            }

            $result = array_intersect($menuName, array('Inventory'));
            if (!empty($result)) {
                if ($securityContext->isGranted('ROLE_INVENTORY')){
                    if ($securityContext->isGranted('ROLE_DOMAIN_INVENTORY_SALES')) {
                        $menu = $this->InventorySalesMenu($menu);
                    }
                    if ($securityContext->isGranted('ROLE_DOMAIN_INVENTORY')) {
                        $menu = $this->InventoryMenu($menu);
                    }
                }
            }

            $result = array_intersect($menuName, array('Hospital'));
            if (!empty($result)) {
                if ($securityContext->isGranted('ROLE_HOSPITAL')){
                    $menu = $this->HospitalMenu($menu);
                }
            }

            $result = array_intersect($menuName, array('Dms'));
            if (!empty($result)) {
                if ($securityContext->isGranted('ROLE_DMS')){
                    $menu = $this->DmsMenu($menu);
                }
            }

            $result = array_intersect($menuName, array('Miss'));
            if (!empty($result)) {
                if ($securityContext->isGranted('ROLE_MEDICINE')){
                    $menu = $this->medicineMenu($menu);
                }
            }

            $result = array_intersect($menuName, array('Dps'));
            if (!empty($result)) {
                if ($securityContext->isGranted('ROLE_DPS')){
                    $menu = $this->DpsMenu($menu);
                }
            }


            $result = array_intersect($menuName, array('Ecommerce'));
            if (!empty($result)) {
                if ($securityContext->isGranted('ROLE_ECOMMERCE')){
                    $menu = $this->EcommerceMenu($menu);
                }
            }

            $result = array_intersect($menuName, array('Website'));
            if (!empty($result)) {
                if ($securityContext->isGranted('ROLE_WEBSITE')){
                    $menu = $this->WebsiteMenu($menu,$menuName);
                }
            }

            $result = array_intersect($menuName, array('Restaurant'));
            if (!empty($result)) {
                if ($securityContext->isGranted('ROLE_RESTAURANT')){
                    $menu = $this->RestaurantMenu($menu);
                }
            }

            $result = array_intersect($menuName, array('Business'));
            if (!empty($result)) {
                if ($securityContext->isGranted('ROLE_BUSINESS') or $securityContext->isGranted('ROLE_DOMAIN')){
                    $menu = $this->BusinessMenu($menu);
                }
            }

            $result = array_intersect($menuName, array('Hotel'));
            if (!empty($result)) {
                if ($securityContext->isGranted('ROLE_HOTEL') or $securityContext->isGranted('ROLE_DOMAIN')){
	                $menu = $this->ReservationMenu($menu);
                }
            }

		    $result = array_intersect($menuName, array('Election'));
		    if (!empty($result)) {
			    if ($securityContext->isGranted('ROLE_ELECTION')){
				    $menu = $this->ElectionMenu($menu);
			    }
		    }

            $result = array_intersect($menuName, array('Accounting'));
            if (!empty($result)) {
                if ($securityContext->isGranted('ROLE_ACCOUNTING')){
                    $menu = $this->AccountingMenu($menu);
                }
            }

            $result = array_intersect($menuName, array('Payroll'));
            if (!empty($result)) {
                if ($securityContext->isGranted('ROLE_HR') || $securityContext->isGranted('ROLE_PAYROLL')){
                    $menu = $this->PayrollMenu($menu);
                }
            }
            if ($securityContext->isGranted('ROLE_DOMAIN') || $securityContext->isGranted('ROLE_SMS')) {
                $menu = $this->manageDomainInvoiceMenu($menu);
            }
            return $menu;
    }

    public function dashboardMenu($menu)
    {
        $menu
            ->addChild('Dashboard', array('route' => 'homepage'))
            ->setAttribute('icon', 'fa fa-home');
        return $menu;
    }

/*    public function manageCustomerOrderMenu($menu)
    {
        $securityContext = $this->container->get('security.context');
        $menu
            ->addChild('Inbox & Transaction')
            ->setAttribute('dropdown', true)
            ->setAttribute('icon', 'fa fa-user-circle');
        $menu['Inbox & Transaction']->addChild('Client', array('route' => 'agentclient'))->setAttribute('icon', 'icon-users');
        $menu['Inbox & Transaction']->addChild('Receive Invoice', array('route' => 'agentclient_invoice'))->setAttribute('icon', 'icon-money');
        $menu['Inbox & Transaction']->addChild('Manage Inbox')->setAttribute('icon', 'icon-money')->setAttribute('dropdown', true);
        $menu['Inbox & Transaction']['Manage Inbox']->addChild('Email', array('route' => 'invoicesmsemail'))->setAttribute('icon', 'icon-envelope');
        $menu['Inbox & Transaction']['Manage Inbox']->addChild('SMS', array('route' => 'invoicemodule'))->setAttribute('icon', 'icon-phone');
        return $menu;
    }*/


    public function businessMenu($menu)
    {

        $securityContext = $this->container->get('security.token_storage')->getToken()->getUser();

        $menu
            ->addChild('Business Management')
            ->setAttribute('icon', 'icon-briefcase')
            ->setAttribute('dropdown', true);

        if ($securityContext->isGranted('ROLE_BUSINESS_INVOICE')) {
	        $menu['Business Management']->addChild('Sales')->setAttribute('icon', 'icon icon-truck')->setAttribute('dropdown', true);
            $menu['Business Management']['Sales']->addChild('Add Invoice', array('route' => 'business_invoice_new'))
                ->setAttribute('icon', 'icon-plus-sign');
            $menu['Business Management']['Sales']->addChild('Invoice', array('route' => 'business_invoice'))
                ->setAttribute('icon', 'icon-th-list');
	        $menu['Business Management']['Sales']->addChild('Reports')
	                                               ->setAttribute('icon', 'icon icon-bar-chart')
	                                               ->setAttribute('dropdown', true);
	        $menu['Business Management']['Sales']['Reports']->addChild('Sales Summary', array('route' => 'business_report_sales_summary'))
	                                                        ->setAttribute('icon', 'icon-th-list');
	        $menu['Business Management']['Sales']['Reports']->addChild('Sales Details', array('route' => 'business_report_sales_details'))
	                                                        ->setAttribute('icon', 'icon-th-list');
	        $menu['Business Management']['Sales']['Reports']->addChild('Customer Sales', array('route' => 'business_report_customer_sales_item'))
	                                                        ->setAttribute('icon', 'icon-th-list');
	        $menu['Business Management']['Sales']['Reports']->addChild('Product Wise Sales', array('route' => 'business_report_sales_stock'))
	                                                        ->setAttribute('icon', 'icon-th-list');
        }
	    if ($securityContext->isGranted('ROLE_CRM') or $securityContext->isGranted('ROLE_DOMAIN')) {
		    $menu['Business Management']->addChild('Notepad', array('route' => 'domain_notepad'))->setAttribute('icon', 'fa fa-file');
		    $menu['Business Management']->addChild('Customer', array('route' => 'domain_customer'))->setAttribute('icon', 'fa fa-group');
        }
	    if ($securityContext->isGranted('ROLE_BUSINESS_PURCHASE')) {

		    $menu['Business Management']->addChild('Purchase')->setAttribute('icon', 'icon icon-truck')->setAttribute('dropdown', true);
		    $menu['Business Management']['Purchase']->addChild('Purchase', array('route' => 'business_purchase'))
		                                            ->setAttribute('icon', 'icon-th-list');
		    $menu['Business Management']['Purchase']->addChild('Purchase Item', array('route' => 'business_purchase_item'))
		                                            ->setAttribute('icon', 'icon-th-list');
		    $menu['Business Management']['Purchase']->addChild('Vendor', array('route' => 'business_vendor'))->setAttribute('icon', 'icon-tag');
		    $menu['Business Management']['Purchase']->addChild('Reports')
		                                           ->setAttribute('icon', 'icon icon-bar-chart')
		                                           ->setAttribute('dropdown', true);
		    $menu['Business Management']['Purchase']['Reports']->addChild('Purchase Summary', array('route' => 'business_report_purchase_summary'))->setAttribute('icon', 'icon-th-list');
		    $menu['Business Management']['Purchase']['Reports']->addChild('Vendor Ledger', array('route' => 'business_report_purchase_ledger'))->setAttribute('icon', 'icon-th-list');
		    $menu['Business Management']['Purchase']['Reports']->addChild('Vendor Details', array('route' => 'business_report_purchase_vendor_details'))->setAttribute('icon', 'icon-th-list');
		    $menu['Business Management']['Purchase']['Reports']->addChild('Stock Price', array('route' => 'business_report_purchase_stock_price'))->setAttribute('icon', 'icon-th-list');
		    $menu['Business Management']['Purchase']['Reports']->addChild('Stock Item Price', array('route' => 'business_report_item_stock_price'))->setAttribute('icon', 'icon-th-list');
		    $menu['Business Management']['Purchase']['Reports']->addChild('Stock Item', array('route' => 'business_report_item_stock'))->setAttribute('icon', 'icon-th-list');

	    }

        if ($securityContext->isGranted('ROLE_BUSINESS_STOCK')) {
	        $menu['Business Management']->addChild('Manage Stock', array('route' => 'business_stock'))->setAttribute('icon', 'icon-th-list');
	        $menu['Business Management']->addChild('Pre-production', array('route' => 'business_production'))->setAttribute('icon', 'icon-th-list');
	        $menu['Business Management']->addChild('Stock Transfer', array('route' => 'business_stock_transfer'))->setAttribute('icon', 'icon-th-list');
	        $menu['Business Management']->addChild('Manage Damage', array('route' => 'business_damage'))->setAttribute('icon', 'icon-trash');
        }

	    if ($securityContext->isGranted('ROLE_BUSINESS_PURCHASE')) {

		    $menu['Business Management']->addChild('Master Data')
		                                ->setAttribute('icon', 'icon icon-cog')
		                                ->setAttribute('dropdown', true);
		    $menu['Business Management']['Master Data']->addChild('Category', array('route' => 'business_category'))->setAttribute('icon', 'icon-th-list');
		    $menu['Business Management']['Master Data']->addChild('Wear House', array('route' => 'business_wearhouse'))->setAttribute('icon', 'icon-th-list');
		    $menu['Business Management']['Master Data']->addChild('Configuration', array('route' => 'business_config_manage'))->setAttribute('icon', 'icon-cog');
	    }

	    $menu['Business Management']->addChild('Reports')
	                     ->setAttribute('icon', 'icon icon-bar-chart')
	                     ->setAttribute('dropdown', true);

	    $menu['Business Management']['Reports']->addChild('Sales')
	                                ->setAttribute('icon', 'icon icon-bar-chart')
	                                ->setAttribute('dropdown', true);

	    $menu['Business Management']['Reports']['Sales']->addChild('Sales Summary', array('route' => 'business_report_sales_summary'))
	                                         ->setAttribute('icon', 'icon-th-list');
	    $menu['Business Management']['Reports']['Sales']->addChild('Sales Details', array('route' => 'business_report_sales_details'))
	                                         ->setAttribute('icon', 'icon-th-list');
	    $menu['Business Management']['Reports']['Sales']->addChild('Customer Sales', array('route' => 'business_report_customer_sales_item'))
	                                         ->setAttribute('icon', 'icon-th-list');
	    $menu['Business Management']['Reports']['Sales']->addChild('Product Wise Sales', array('route' => 'business_report_sales_stock'))
	                                         ->setAttribute('icon', 'icon-th-list');
	    return $menu;

    }

    public function WebsiteMenu($menu,$menuName)
    {

        $securityContext = $this->container->get('security.token_storage')->getToken()->getUser();
        $option = $securityContext->getGlobalOption();

        if ($securityContext->isGranted('ROLE_DOMAIN_WEBSITE_MANAGER')) {

            $menu
                ->addChild('Manage Content')
                ->setAttribute('icon', 'fa fa-book')
                ->setAttribute('dropdown', true);

            $menu['Manage Content']->addChild('Page', array('route' => 'page'));
            if ($option->getSiteSetting()) {
                $syndicateModules = $option->getSiteSetting()->getSyndicateModules();
                if (!empty($syndicateModules)) {
                    foreach ($option->getSiteSetting()->getSyndicateModules() as $syndmod) {
                        $menu['Manage Content']->addChild($syndmod->getName(), array('route' => strtolower($syndmod->getModuleClass())));
                    }
                }

                $modules = $option->getSiteSetting()->getModules();
                if (!empty($modules)) {
                    foreach ($option->getSiteSetting()->getModules() as $mod) {
                        $menu['Manage Content']->addChild($mod->getName(), array('route' => strtolower($mod->getModuleClass())));
                    }
                }
            }
            $menu
                ->addChild('Media')
                ->setAttribute('icon', 'fa fa-picture-o')
                ->setAttribute('dropdown', true);

            $menu['Manage Content']->addChild('Contact', array('route' => 'contactpage_modify'));
            $menu['Media']->addChild('Galleries', array('route' => 'gallery'));
        }

        if ($securityContext->isGranted('ROLE_DOMAIN_WEBSITE_SETTING')) {

            $result = array_intersect($menuName, array('Ecommerce'));
            $menu
                ->addChild('Manage Appearance')
                ->setAttribute('icon', 'fa fa-cog')
                ->setAttribute('dropdown', true);

	        if (!empty($result)) {
		        $menu['Manage Appearance']->addChild( 'Customize Template', array( 'route'           => 'templatecustomize_ecommerce_edit',
		                                                                           'routeParameters' => array( 'id' => $option->getId() )
		        ) );
	        }else{
		        $menu['Manage Appearance']->addChild( 'Customize Template', array( 'route'           => 'templatecustomize_edit',
		                                                                           'routeParameters' => array( 'id' => $option->getId() )
		        ) );
	        }

            if (!empty($result)) {
                if ($securityContext->isGranted('ROLE_DOMAIN_ECOMMERCE_CONFIG') && $securityContext->isGranted('ROLE_ECOMMERCE')){
                    $menu['Manage Appearance']->addChild('E-commerce')->setAttribute('icon', 'icon-th-list')->setAttribute('dropdown', true);
                    $menu['Manage Appearance']['E-commerce']->addChild('E-commerce Widget', array('route' => 'appearancefeaturewidget'))->setAttribute('icon', 'icon-th-list');
                    $menu['Manage Appearance']['E-commerce']->addChild('Feature', array('route' => 'appearancefeature'))->setAttribute('icon', 'icon-th-list');
                    $menu['Manage Appearance']['E-commerce']->addChild('Feature Category', array('route' => 'featurecategory'))->setAttribute('icon', 'icon-th-list');
                    $menu['Manage Appearance']['E-commerce']->addChild('Feature Brand', array('route' => 'featurebrand'))->setAttribute('icon', 'icon-th-list');


                }
            }
            $menu['Manage Appearance']->addChild('Website')->setAttribute('icon', 'icon-th-list')->setAttribute('dropdown', true);
            $menu['Manage Appearance']['Website']->addChild('Website Widget', array('route' => 'appearancewebsitewidget'))->setAttribute('icon', 'icon-th-list');
            $menu['Manage Appearance']['Website']->addChild('Feature', array('route' => 'appearancefeature'))->setAttribute('icon', 'icon-th-list');
            $menu['Manage Appearance']->addChild('Menu')->setAttribute('icon', 'icon-th-list')->setAttribute('dropdown', true);
            if (!empty($result) and $securityContext->isGranted('ROLE_DOMAIN_ECOMMERCE_CONFIG') && $securityContext->isGranted('ROLE_ECOMMERCE')) {
                $menu['Manage Appearance']['Menu']->addChild('E-commerce Menu', array('route' => 'ecommercemenu'))->setAttribute('icon', 'icon-th-list');
            }
            $menu['Manage Appearance']['Menu']->addChild('Website Menu', array('route' => 'menu_manage'))->setAttribute('icon', 'icon-th-list');
            $menu['Manage Appearance']['Menu']->addChild('Menu Grouping', array('route' => 'menugrouping'))->setAttribute('icon', 'icon-th-list');

        }

        if ($securityContext->isGranted('ROLE_DOMAIN')) {
            $menu['Manage Appearance']->addChild('Settings', array('route' => 'globaloption_modify'));
        }
        return $menu;
    }

    public function AccountingMenu($menu)
    {

        $securityContext = $this->container->get('security.token_storage')->getToken()->getUser();

        /* @var  $globalOption GlobalOption */

        $globalOption = $securityContext->getGlobalOption();

        $modules = $globalOption->getSiteSetting()->getAppModules();
        $arrSlugs = array();
        if (!empty($globalOption->getSiteSetting()) and !empty($modules)) {
            foreach ($globalOption->getSiteSetting()->getAppModules() as $mod) {
                if (!empty($mod->getModuleClass())) {
                    $arrSlugs[] = $mod->getSlug();
                }
            }
        }

        $menu
            ->addChild('Accounting')
            ->setAttribute('icon', 'fa fa-building-o')
            ->setAttribute('dropdown', true);
        if ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_REPORT')) {

            $menu['Accounting']->addChild('Transaction & Report', array('route' => 'account_transaction'))
                ->setAttribute('icon', 'fa fa-money')
                ->setAttribute('dropdown', true);
            $menu['Accounting']['Transaction & Report']->addChild('Transaction Overview', array('route' => 'account_transaction'))->setAttribute('icon', 'icon-th-list');
            $accounting = array('inventory');
            $result = array_intersect($arrSlugs, $accounting);
            if (!empty($result)) {
                $menu['Accounting']['Transaction & Report']->addChild('Income', array('route' => 'report_income'))->setAttribute('icon', 'icon-th-list');
                $menu['Accounting']['Transaction & Report']->addChild('Monthly Income',        array('route' => 'report_monthly_income'))->setAttribute('icon', 'icon-th-list');
                $menu['Accounting']['Transaction & Report']->addChild('Customer Ledger', array('route' => 'account_sales_outstanding'))->setAttribute('icon', 'icon-th-list');
                $menu['Accounting']['Transaction & Report']->addChild('Vendor Ledger',        array('route' => 'account_purchase_outstanding'))->setAttribute('icon', 'icon-th-list');

            }
            $accounting = array('e-commerce');
            $result = array_intersect($arrSlugs, $accounting);
            if (!empty($result)) {
                $menu['Accounting']['Transaction & Report']->addChild('Income', array('route' => 'report_income'))->setAttribute('icon', 'icon-th-list');
                /* $menu['Accounting']['Transaction & Report']->addChild('Monthly Income',        array('route' => 'report_monthly_income'))->setAttribute('icon', 'icon-th-list');*/
            }
            $accounting = array('hms');
            $result = array_intersect($arrSlugs, $accounting);
            if (!empty($result)) {
                $menu['Accounting']['Transaction & Report']->addChild('Income', array('route' => 'hms_report_income'))->setAttribute('icon', 'icon-th-list');
                $menu['Accounting']['Transaction & Report']->addChild('Monthly Income',        array('route' => 'hms_report_monthly_income'))->setAttribute('icon', 'icon-th-list');
                $menu['Accounting']['Transaction & Report']->addChild('Expenditure Summary',        array('route' => 'hms_report_expenditure_summary'))->setAttribute('icon', 'icon-th-list');
                $menu['Accounting']['Transaction & Report']->addChild('Expenditure Details',        array('route' => 'hms_report_expenditure_details'))->setAttribute('icon', 'icon-th-list');
            }
            $accounting = array('miss');
            $result = array_intersect($arrSlugs, $accounting);
            if (!empty($result)) {
                $menu['Accounting']['Transaction & Report']->addChild('Income', array('route' => 'account_medicine_income'))->setAttribute('icon', 'icon-th-list');
                $menu['Accounting']['Transaction & Report']->addChild('Monthly Income',        array('route' => 'account_medicine_income_monthly'))->setAttribute('icon', 'icon-th-list');
                $menu['Accounting']['Transaction & Report']->addChild('Customer Ledger', array('route' => 'account_sales_medicine_outstanding'))->setAttribute('icon', 'icon-th-list');
                $menu['Accounting']['Transaction & Report']->addChild('Vendor Ledger',        array('route' => 'account_purchase_medicine_outstanding'))->setAttribute('icon', 'icon-th-list');

            }
            $accounting = array('business');
            $result = array_intersect($arrSlugs, $accounting);
            if (!empty($result)) {
                $menu['Accounting']['Transaction & Report']->addChild('Income', array('route' => 'account_business_income'))->setAttribute('icon', 'icon-th-list');
                $menu['Accounting']['Transaction & Report']->addChild('Monthly Income',        array('route' => 'account_business_income_monthly'))->setAttribute('icon', 'icon-th-list');
                $menu['Accounting']['Transaction & Report']->addChild('Customer Ledger', array('route' => 'account_sales_business_outstanding'))->setAttribute('icon', 'icon-th-list');
                $menu['Accounting']['Transaction & Report']->addChild('Customer Ledger', array('route' => 'account_sales_business_ledger'))->setAttribute('icon', 'icon-money');
                $menu['Accounting']['Transaction & Report']->addChild('Vendor Ledger',        array('route' => 'account_purchase_business_outstanding'))->setAttribute('icon', 'icon-th-list');

            }
            $accounting = array('hotel');
            $result = array_intersect($arrSlugs, $accounting);
            if (!empty($result)) {
                $menu['Accounting']['Transaction & Report']->addChild('Income', array('route' => 'account_business_income'))->setAttribute('icon', 'icon-th-list');
                $menu['Accounting']['Transaction & Report']->addChild('Monthly Income',        array('route' => 'account_business_income_monthly'))->setAttribute('icon', 'icon-th-list');
                $menu['Accounting']['Transaction & Report']->addChild('Customer Ledger', array('route' => 'account_sales_business_outstanding'))->setAttribute('icon', 'icon-th-list');
                $menu['Accounting']['Transaction & Report']->addChild('Customer Ledger', array('route' => 'account_sales_business_ledger'))->setAttribute('icon', 'icon-money');
                $menu['Accounting']['Transaction & Report']->addChild('Vendor Ledger',        array('route' => 'account_purchase_business_outstanding'))->setAttribute('icon', 'icon-th-list');

            }
            $menu['Accounting']['Transaction & Report']->addChild('Expenditure Summary',        array('route' => 'report_expenditure_summary'))->setAttribute('icon', 'icon-th-list');
            $menu['Accounting']['Transaction & Report']->addChild('Expenditure Category',        array('route' => 'report_expenditure_category'))->setAttribute('icon', 'icon-th-list');
            $menu['Accounting']['Transaction & Report']->addChild('Expenditure Details',        array('route' => 'report_expenditure_details'))->setAttribute('icon', 'icon-th-list');
        }

        if ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_TRANSACTION')) {

            $menu['Accounting']->addChild('Cash', array('route' => ''))
                ->setAttribute('icon', 'fa fa-money')
                ->setAttribute('dropdown', true);
            $menu['Accounting']['Cash']->addChild('Cash Overview', array('route' => 'account_transaction_cash_overview'))->setAttribute('icon', 'icon-th-list');
            $menu['Accounting']['Cash']->addChild('All Cash Flow', array('route' => 'account_transaction_accountcash'))->setAttribute('icon', 'icon-th-list');
            $menu['Accounting']['Cash']->addChild('Cash Transaction', array('route' => 'account_transaction_cash'))->setAttribute('icon', 'icon-th-list');
            $menu['Accounting']['Cash']->addChild('Bank Transaction', array('route' => 'account_transaction_bank'))->setAttribute('icon', 'icon-th-list');
            $menu['Accounting']['Cash']->addChild('Mobile Transaction', array('route' => 'account_transaction_mobilebank'))->setAttribute('icon', 'icon-th-list');
        }
        if ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_EXPENDITURE')){
            $menu['Accounting']->addChild('Expenditure', array('route' => 'account_expenditure'))
                ->setAttribute('icon', 'fa fa-money')
                ->setAttribute('dropdown', true);
            $menu['Accounting']['Expenditure']->addChild('Expense', array('route' => 'account_expenditure'))->setAttribute('icon', 'icon-th-list');
            $menu['Accounting']['Expenditure']->addChild('Expense Category', array('route' => 'expensecategory'))->setAttribute('icon', 'icon-th-list');
        }
        /*$menu['Finance']->addChild('Petty Cash & Expense', array('route' => 'account_pettycash'))
            ->setAttribute('icon','fa fa-money')
            ->setAttribute('dropdown', true);
        $menu['Finance']['Petty Cash & Expense']->addChild('Petty Cash', array('route' => 'account_pettycash'))->setAttribute('icon', 'icon-th-list');
        $menu['Finance']['Petty Cash & Expense']->addChild('Expense',        array('route' => 'account_expenditure'))->setAttribute('icon', 'icon-th-list');
        $menu['Finance']['Petty Cash & Expense']->addChild('Expense Category',        array('route' => 'expensecategory'))->setAttribute('icon', 'icon-th-list');*/
        $inventory = array('inventory');
        $result = array_intersect($arrSlugs, $inventory);
        if (!empty($result)) {
            $menu['Accounting']->addChild('Sales & Purchase', array('route' => 'account_expenditure'))
                ->setAttribute('icon', 'fa fa-money')
                ->setAttribute('dropdown', true);
            if ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_SALES')) {
                $menu['Accounting']['Sales & Purchase']->addChild('Sales', array('route' => 'account_sales'));
            }
            if ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_PURCHASE')) {
                $menu['Accounting']['Sales & Purchase']->addChild('Purchase', array('route' => 'account_purchase'));
            }
        }
        $accounting = array('e-commerce');
        $result = array_intersect($arrSlugs, $accounting);
        if (!empty($result) && $securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_ECOMMERCE')) {
            if ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_SALES')) {
                $menu['Accounting']->addChild('Online Order', array('route' => 'account_onlineorder'));
                $menu['Accounting']->addChild('Online Order Return', array('route' => 'account_onlineorder'));
            }
        }

        $hospital = array('hms');
        $result = array_intersect($arrSlugs, $hospital);
        if (!empty($result)) {
            if ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_SALES')) {
                $menu['Accounting']->addChild('Sales', array('route' => 'account_sales_hospital'))->setAttribute('icon', 'icon-th-list');
            }
            if ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_PURCHASE')) {
                $menu['Accounting']->addChild('Purchase', array('route' => 'account_purchase_hospital'))->setAttribute('icon', 'icon-th-list');
            }
        }
        $medicine = array('miss');
        $result = array_intersect($arrSlugs, $medicine);
        if (!empty($result)) {
            if ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_SALES')) {
                $menu['Accounting']->addChild('Sales', array('route' => 'account_sales_medicine'))->setAttribute('icon', 'icon-th-list');
            }
            if ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_PURCHASE')) {
                $menu['Accounting']->addChild('Purchase', array('route' => 'account_purchase_medicine'))->setAttribute('icon', 'icon-th-list');
            }
        }
        $business = array('business');
        $result = array_intersect($arrSlugs, $business);
        if (!empty($result)) {
            if ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_SALES')) {
                $menu['Accounting']->addChild('Sales', array('route' => 'account_sales_business'))->setAttribute('icon', 'icon-th-list');
            }
            if ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_PURCHASE')) {
                $menu['Accounting']->addChild('Purchase', array('route' => 'account_purchase_business'))->setAttribute('icon', 'icon-th-list');
            }
        }
        $hotel = array('hotel');
        $result = array_intersect($arrSlugs, $hotel);
        if (!empty($result)) {
            if ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_SALES')) {
                $menu['Accounting']->addChild('Sales', array('route' => 'account_sales_hotel'))->setAttribute('icon', 'icon-th-list');
            }
           /* if ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_PURCHASE')) {
                $menu['Accounting']->addChild('Purchase', array('route' => 'account_purchase_business'))->setAttribute('icon', 'icon-th-list');
            }*/
        }
        $restaurant = array('restaurant');
        $result = array_intersect($arrSlugs, $restaurant);
        if (!empty($result)) {
          /*  $menu['Accounting']->addChild('Sales', array('route' => 'account_sales_restaurant'))->setAttribute('icon', 'icon-th-list');
          */
            if ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_PURCHASE')) {
                $menu['Accounting']->addChild('Purchase', array('route' => 'account_purchase_restaurant'))->setAttribute('icon', 'icon-th-list');
            }
        }
        if($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_JOURNAL')){
            $menu['Accounting']->addChild('Balance Transfer', array('route' => 'account_balancetransfer'))->setAttribute('icon', 'icon-retweet');
            $menu['Accounting']->addChild('Journal', array('route' => 'account_journal'))->setAttribute('icon', 'icon-retweet');
        }
        if ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_CONFIG')) {

            $menu['Accounting']->addChild('Master Data', array('route' => ''))
                ->setAttribute('icon', 'fa fa-building-o')
                ->setAttribute('dropdown', true);
            $menu['Accounting']['Master Data']->addChild('Bank Account', array('route' => 'accountbank'));
            $menu['Accounting']['Master Data']->addChild('Mobile Account', array('route' => 'accountmobilebank'));
            $menu['Accounting']['Master Data']->addChild('Account Head', array('route' => 'accounthead'));

        }

        return $menu;

    }

    public function InventorySalesMenu($menu)
    {
        $securityContext = $this->container->get('security.token_storage')->getToken()->getUser();
        $inventory = $securityContext->getGlobalOption()->getInventoryConfig();
        if ($securityContext->isGranted('ROLE_DOMAIN_INVENTORY_SALES')) {

            $menu
            ->addChild('Sales')
            ->setAttribute('icon', 'fa fa-shopping-bag')
            ->setAttribute('dropdown', true);


            $deliveryProcess = $inventory->getDeliveryProcess();
            if (!empty($deliveryProcess)) {

                if ('pos' == $deliveryProcess) {
                    $menu['Sales']->addChild('Pos', array('route' => 'inventory_sales_new'))->setAttribute('icon', 'icon icon-shopping-cart');
                    $menu['Sales']->addChild('Sales', array('route' => 'inventory_sales'))->setAttribute('icon', 'icon icon-th-list');
                    $menu['Sales']->addChild('Sales Return', array('route' => 'inventory_salesreturn'))->setAttribute('icon', 'icon icon-share-alt');
                    $menu['Sales']->addChild('Sales Import', array('route' => 'inventory_salesimport'))->setAttribute('icon', 'icon icon-upload');

                }
                if ('general-sales' == $deliveryProcess) {
                    $menu['Sales']->addChild('Add Sales', array('route' => 'inventory_salesonline_new'))->setAttribute('icon', 'icon icon-plus');
                    $menu['Sales']->addChild('Sales', array('route' => 'inventory_salesonline'))->setAttribute('icon', 'icon icon-th-list');
                    $menu['Sales']->addChild('Sales Import', array('route' => 'inventory_salesimport'))->setAttribute('icon', 'icon icon-upload');
                    $menu['Sales']->addChild('Sales Return', array('route' => 'inventory_salesreturn'))->setAttribute('icon', 'icon icon-share-alt');
                }
                if ('general-x-sales' == $deliveryProcess) {
                    $menu['Sales']->addChild('Add Sales', array('route' => 'inventory_salesgeneral_new'))->setAttribute('icon', 'icon icon-plus');
                    $menu['Sales']->addChild('Sales', array('route' => 'inventory_salesgeneral'))->setAttribute('icon', 'icon icon-th-list');
                    $menu['Sales']->addChild('Sales Return', array('route' => 'inventory_salesreturn'))->setAttribute('icon', 'icon icon-share-alt');
                    $menu['Sales']->addChild('Sales Import', array('route' => 'inventory_salesimport'))->setAttribute('icon', 'icon icon-upload');

                }
                if ('manual-sales' == $deliveryProcess) {
                    $menu['Sales']->addChild('Add Sales', array('route' => 'inventory_salesmanual_new'))->setAttribute('icon', 'fa fa-cart-plus');
                    $menu['Sales']->addChild('Sales', array('route' => 'inventory_salesmanual'))->setAttribute('icon', 'fa icon-th-list');
                    $menu['Sales']->addChild('Sales Return', array('route' => 'inventory_salesreturn'))->setAttribute('icon', 'icon-exchange');
                }

                if ('order' == $deliveryProcess) {
                    if ($securityContext->isGranted('ROLE_DOMAIN_INVENTORY_SALES_ORDER')){
                        $menu['Sales']
                            ->addChild('Online Order')
                            ->setAttribute('icon', 'icon icon-truck')
                            ->setAttribute('dropdown', true);
                        $menu['Sales']['Online Order']->addChild('Online Customer', array('route' => 'inventory_customer'))->setAttribute('icon', 'icon icon-user');
                        $menu['Sales']['Online Order']->addChild('Order', array('route' => 'inventory_sales'))->setAttribute('icon', ' icon-th-list');
                        if ($securityContext->isGranted('ROLE_DOMAIN_INVENTORY_MANAGER')) {
                            $menu['Sales']['Online Order']->addChild('Order Return', array('route' => 'inventory_salesreturn'))->setAttribute('icon', 'icon-share-alt');
                        }
                    }
                }
            }
            if ($securityContext->isGranted('ROLE_CRM') or $securityContext->isGranted('ROLE_DOMAIN')) {
                $menu['Sales']->addChild('Customer', array('route' => 'domain_customer'))->setAttribute('icon', 'fa fa-group');
            }
            if ($securityContext->isGranted('ROLE_DOMAIN_INVENTORY_REPORT')) {
                $menu['Sales']->addChild('Reports')
                    ->setAttribute('icon', 'icon-bar-chart')
                    ->setAttribute('dropdown', true);
                $menu['Sales']['Reports']->addChild('Sales Overview', array('route' => 'inventory_report_sales_overview'))->setAttribute('icon', 'icon-bar-chart');
                $menu['Sales']['Reports']->addChild('Periodic Sales Item', array('route' => 'inventory_report_sales_item'))->setAttribute('icon', 'icon-bar-chart');
                $menu['Sales']['Reports']->addChild('Sales Item Details', array('route' => 'inventory_report_sales_item_details'))->setAttribute('icon', 'icon-bar-chart');
                $menu['Sales']['Reports']->addChild('Sales with price', array('route' => 'inventory_report_sales'))->setAttribute('icon', 'icon-bar-chart');
                $menu['Sales']['Reports']->addChild('Sales by User', array('route' => 'inventory_report_sales_user'))->setAttribute('icon', 'icon-bar-chart');
                $menu['Sales']['Reports']->addChild('User Sales Target', array('route' => 'inventory_report_sales_user_target'))->setAttribute('icon', 'icon-bar-chart');
            }


        }
        return $menu;

    }

    public function InventoryMenu($menu)
    {

        $securityContext = $this->container->get('security.token_storage')->getToken()->getUser();
        $inventory = $securityContext->getGlobalOption()->getInventoryConfig();
        $menu
            ->addChild('Inventory')
            ->setAttribute('icon', 'icon-archive')
            ->setAttribute('dropdown', true);
        if ($securityContext->isGranted('ROLE_DOMAIN_INVENTORY_PURCHASE')) {

            $menu['Inventory']->addChild('Manage Purchase', array('route' => 'purchase'))
                ->setAttribute('icon', 'icon icon-list-alt')
                ->setAttribute('dropdown', true);
            $menu['Inventory']['Manage Purchase']->addChild('Purchase', array('route' =>'inventory_purchasesimple'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Inventory']['Manage Purchase']->addChild('Purchase Return', array('route' => 'inventory_purchasereturn'))
                ->setAttribute('icon', ' icon-reply');
            $menu['Inventory']['Manage Purchase']->addChild('Purchase Import', array('route' => 'inventory_excelimproter'))
                ->setAttribute('icon', 'icon-upload');
	        $menu['Inventory']['Manage Purchase']->addChild('Pre-purchase', array('route' => 'prepurchaseitem'))->setAttribute('icon', 'icon icon-archive');
	        if ($securityContext->isGranted('ROLE_DOMAIN_INVENTORY_REPORT')) {
            $menu['Inventory']['Manage Purchase']->addChild('Reports')
                ->setAttribute('icon', 'icon-bar-chart')
                ->setAttribute('dropdown', true);
            $menu['Inventory']['Manage Purchase']['Reports']->addChild('Purchase with price', array('route' => 'inventory_report_purchase'))->setAttribute('icon', 'icon-bar-chart');
            }
        }
/*
        if ($securityContext->isGranted('ROLE_DOMAIN_INVENTORY_PURCHASE')) {
            $menu['Inventory']->addChild('Purchase Item for Web', array('route' => 'inventory_purchasevendoritem'))
                ->setAttribute('icon', 'icon-info-sign');
        }*/

        if ($securityContext->isGranted('ROLE_DOMAIN_INVENTORY_STOCK')) {

            $menu['Inventory']->addChild('Manage Stock')->setAttribute('icon', 'icon icon-archive')->setAttribute('dropdown', true);
            $menu['Inventory']['Manage Stock']->addChild('Stock Item', array('route' => 'inventory_item'))
                ->setAttribute('icon', 'icon-hdd');
            $menu['Inventory']['Manage Stock']->addChild('Purchase Item', array('route' => 'inventory_purchaseitem'))->setAttribute('icon', 'icon-hdd');
            $menu['Inventory']['Manage Stock']->addChild('Barcode wise Stock', array('route' => 'inventory_barcode_branch_stock'))->setAttribute('icon', 'icon-bar-chart');
            $menu['Inventory']['Manage Stock']->addChild('Barcode Stock Details', array('route' => 'inventory_barcode_stock'))->setAttribute('icon', 'icon-bar-chart');
            $menu['Inventory']['Manage Stock']->addChild('Stock Item Details', array('route' => 'inventory_stockitem'))->setAttribute('icon', 'icon-hdd');
            $menu['Inventory']['Manage Stock']->addChild('Stock Short List', array('route' => 'inventory_stockitem_short_list'))->setAttribute('icon', 'icon-hdd');
        }
        if ($securityContext->isGranted('ROLE_DOMAIN_INVENTORY_MANAGER')) {
            if ($inventory->getBarcodePrint() == 1) {
                $menu['Inventory']['Manage Stock']->addChild('Barcode Print', array('route' => 'inventory_barcode'))
                    ->setAttribute('icon', 'icon-barcode');
            }
            $menu['Inventory']['Manage Stock']->addChild('Damage', array('route' => 'inventory_damage'))
                ->setAttribute('icon', ' icon-trash');

            $menu['Inventory']->addChild('Master Data', array('route' => ''))
                ->setAttribute('icon', 'fa fa-archive')
                ->setAttribute('dropdown', true);
            $menu['Inventory']['Master Data']->addChild('Master Item', array('route' => 'inventory_product'));
            $menu['Inventory']['Master Data']->addChild('Item category', array('route' => 'itemtypegrouping_edit', 'routeParameters' => array('id' => $inventory->getId())));
            $menu['Inventory']['Master Data']->addChild('Custom category', array('route' => 'inventory_category'));
            $menu['Inventory']['Master Data']->addChild('Vendor', array('route' => 'inventory_vendor'));
            $menu['Inventory']['Master Data']->addChild('Brand', array('route' => 'itembrand'));
            $menu['Inventory']['Master Data']->addChild('Size Group', array('route' => 'itemsize_group'));
            if ($inventory->getIsBranch() == 1) {
                $menu['Inventory']['Master Data']->addChild('Branches')->setAttribute('icon', 'icon-building')->setAttribute('dropdown', true);
                $menu['Inventory']['Master Data']['Branches']->addChild('Branch Shop', array('route' => 'appsetting_branchshop'))->setAttribute('icon', 'icon-building');
            }
        }


        if ($securityContext->isGranted('ROLE_DOMAIN_INVENTORY_BRANCH')) {

            if ($inventory->getIsBranch() == 1) {
                $menu['Inventory']
                    ->addChild('Branch Delivery')
                    ->setAttribute('icon', 'icon icon-truck')
                    ->setAttribute('dropdown', true);
                $menu['Inventory']['Branch Delivery']->addChild('Delivery Invoice', array('route' => 'inventory_delivery'))->setAttribute('icon', 'icon-shopping-cart');
                $menu['Inventory']['Branch Delivery']->addChild('Return Invoice', array('route' => 'inventory_deliveryreturn'))->setAttribute('icon', 'icon-retweet');

            }
        }
        if ($securityContext->isGranted('ROLE_DOMAIN_INVENTORY_CONFIG')) {

            $menu['Inventory']->addChild('Configuration', array('route' => 'inventoryconfig_edit'))
                ->setAttribute('icon', 'icon icon-cogs');
            $menu['Inventory']->addChild('User Sales Setup', array('route' => 'inventory_sales_user'))
                ->setAttribute('icon', 'fa fa-group');
        }
        if ($securityContext->isGranted('ROLE_DOMAIN_INVENTORY_REPORT')) {

            $menu['Inventory']->addChild('Reports')
                ->setAttribute('icon', 'icon-bar-chart')
                ->setAttribute('dropdown', true);
            $menu['Inventory']['Reports']->addChild('Summary Overview', array('route' => 'inventory_report_overview'))->setAttribute('icon', 'icon-bar-chart');
            $menu['Inventory']['Reports']->addChild('Item Overview', array('route' => 'inventory_report_stock_item'))->setAttribute('icon', 'icon-bar-chart');
            $menu['Inventory']['Reports']->addChild('Till Stock', array('route' => 'inventory_report_till_stock'))->setAttribute('icon', 'icon-bar-chart');
            $menu['Inventory']['Reports']->addChild('Periodic Stock', array('route' => 'inventory_report_periodic_stock'))->setAttribute('icon', 'icon-bar-chart');
            $menu['Inventory']['Reports']->addChild('Operational Stock', array('route' => 'inventory_report_operational_stock'))->setAttribute('icon', 'icon-bar-chart');
            $menu['Inventory']['Reports']->addChild('Group Stock', array('route' => 'inventory_report_group_stock'))->setAttribute('icon', 'icon-bar-chart');
            $menu['Inventory']['Reports']->addChild('Purchase with price', array('route' => 'inventory_report_purchase'))->setAttribute('icon', 'icon-bar-chart');
        }
        if ($securityContext->isGranted('ROLE_DOMAIN_INVENTORY_BRANCH_MANAGER')) {
            if ($inventory->getIsBranch() == 1) {
                $menu['Inventory']->addChild('Branch Reports')
                    ->setAttribute('icon', 'icon-bar-chart')
                    ->setAttribute('dropdown', true);
                $menu['Inventory']['Branch Reports']->addChild('Stock Overview', array('route' => 'inventory_branch_report_overview'))->setAttribute('icon', 'icon-bar-chart');
                $menu['Inventory']['Branch Reports']->addChild('Item wise Stock', array('route' => 'inventory_branch_report_stock'))->setAttribute('icon', 'icon-bar-chart');
                $menu['Inventory']['Branch Reports']->addChild('Barcode wise Stock', array('route' => 'inventory_branch_report_barcode_item'))->setAttribute('icon', 'icon-bar-chart');
                $menu['Inventory']['Branch Reports']->addChild('Item Stock', array('route' => 'inventory_branch_report_item'))->setAttribute('icon', 'icon-bar-chart');
                $menu['Inventory']['Branch Reports']->addChild('Sales Item', array('route' => 'inventory_branch_report_sales'))->setAttribute('icon', 'icon-bar-chart');
            }
        }

        return $menu;

    }

    public function EcommerceMenu($menu)
    {
        $securityContext = $this->container->get('security.token_storage')->getToken()->getUser();
        $menu
            ->addChild('E-commerce')
            ->setAttribute('icon', 'icon  icon-shopping-cart')
            ->setAttribute('dropdown', true);

        if ($securityContext->isGranted('ROLE_DOMAIN_ECOMMERCE_PRODUCT')) {

            $menu['E-commerce']->addChild('Product', array('route' => ''))
                ->setAttribute('icon', 'fa fa-bookmark')
                ->setAttribute('dropdown', true);
            $menu['E-commerce']['Product']->addChild('Product', array('route' => 'inventory_goods'))->setAttribute('icon', 'icon-th-list');
            $menu['E-commerce']['Product']->addChild('Promotion', array('route' => 'ecommerce_promotion'))->setAttribute('icon', 'icon-th-list');
            $menu['E-commerce']['Product']->addChild('Discount', array('route' => 'ecommerce_discount'))->setAttribute('icon', 'icon-th-list');
            $menu['E-commerce']['Product']->addChild('Item Attribute', array('route' => 'itemattribute'))->setAttribute('icon', 'icon-th-list');

        }

        /*$menu['E-commerce']->addChild('Transaction', array('route' => ''))
            ->setAttribute('icon','fa fa-bookmark')
            ->setAttribute('dropdown', true);
        $menu['E-commerce']['Transaction']->addChild('Order',        array('route' => 'customer_order'))->setAttribute('icon', 'icon-th-list');
        $menu['E-commerce']['Transaction']->addChild('Pre-order',    array('route' => 'customer_preorder'))->setAttribute('icon', 'icon-th-list');
        */

        if ($securityContext->isGranted('ROLE_DOMAIN_ECOMMERCE_ORDER')) {

            $menu['E-commerce']->addChild('Order', array('route' => ''))
                ->setAttribute('icon', 'fa fa-bookmark')
                ->setAttribute('dropdown', true);
            $menu['E-commerce']['Order']->addChild('Order', array('route' => 'customer_order'))->setAttribute('icon', 'icon-truck');
            $menu['E-commerce']['Order']->addChild('Order Return', array('route' => 'customer_order'))->setAttribute('icon', 'icon-truck');
            $menu['E-commerce']['Order']->addChild('Pre-order', array('route' => 'customer_preorder'))->setAttribute('icon', 'icon-truck');

        }
        $menu['E-commerce']->addChild('Coupon', array('route' => 'ecommerce_coupon'))->setAttribute('icon', 'icon-tags');
        if ($securityContext->isGranted('ROLE_DOMAIN_ECOMMERCE_CONFIG')) {
            $menu['E-commerce']->addChild('Configuration', array('route' => 'ecommerce_config_modify'))->setAttribute('icon', 'fa fa-cog');
            $menu['E-commerce']->addChild('Master Data', array('route' => ''))
                ->setAttribute('icon', 'fa fa-shopping-cart')
                ->setAttribute('dropdown', true);
            if (empty($resInventory)) {
                $menu['E-commerce']['Master Data']->addChild('Master Item', array('route' => 'inventory_product'))->setAttribute('icon', 'icon-th-list');
               // $menu['E-commerce']['Master Data']->addChild('Item category', array('route' => 'itemtypegrouping_edit', 'routeParameters' => array('id' => $inventory->getId())))->setAttribute('icon', 'icon-th-list');
                $menu['E-commerce']['Master Data']->addChild('Custom category', array('route' => 'inventory_category'))->setAttribute('icon', 'icon-th-list');
                $menu['E-commerce']['Master Data']->addChild('Brand', array('route' => 'itembrand'))->setAttribute('icon', 'icon-th-list');
            }
        }

        return $menu;
    }

    public function AnonymousProductSalesMenu($menu)
    {

        $securityContext = $this->container->get('security.context');
        $user = $securityContext->getToken()->getUser();
        $inventory = $user->getGlobalOption()->getInventoryConfig();

        $menu
            ->addChild('Service & Sales')
            ->setAttribute('icon', 'icon icon-th-large')
            ->setAttribute('dropdown', true);

        $menu['Service & Sales']->addChild('Manage Service')
            ->setAttribute('icon', 'icon icon-reorder')
            ->setAttribute('dropdown', true);
        $menu['Service & Sales']['Manage Service']->addChild('Add Item', array('route' => 'inventory_serviceitem_new'))
            ->setAttribute('icon', 'icon-plus');
        $menu['Service & Sales']['Manage Service']->addChild('Service Item', array('route' => 'inventory_serviceitem'))
            ->setAttribute('icon', 'icon-th-list');
        $menu['Service & Sales']->addChild('Manage Sales')
            ->setAttribute('icon', 'icon icon-reorder')
            ->setAttribute('dropdown', true);
        $menu['Service & Sales']['Manage Sales']->addChild('Create Invoice', array('route' => 'inventory_servicesales_new'))
            ->setAttribute('icon', 'fa fa-files-o');
        $menu['Service & Sales']['Manage Sales']->addChild('Sales', array('route' => 'inventory_servicesales'))
            ->setAttribute('icon', 'icon-th-list');
        $menu['Service & Sales']->addChild('System Setting')
            ->setAttribute('icon', 'icon icon-cogs')
            ->setAttribute('dropdown', true);

        /*
                if($securityContext->isGranted('ROLE_DOMAIN_INVENTORY_CONFIG')) {
                    $menu['Service & Sales']['System Setting']->addChild('Configuration', array('route' => 'inventoryconfig_edit'))
                        ->setAttribute('icon', 'icon-hdd');
                }
                $menu['Inventory']['System Setting']->addChild('Variant', array('route' => 'colorsize'))
                    ->setAttribute('icon', 'icon-th-list');
                $menu['Inventory']['System Setting']->addChild('Ware House', array('route' => 'inventory_warehouse'))->setAttribute('icon', 'icon-th-list');*/
        return $menu;

    }

    public function HospitalMenu($menu)
    {

        $securityContext = $this->container->get('security.token_storage')->getToken()->getUser();
        $config = $securityContext->getGlobalOption()->getHospitalConfig()->getInvoiceProcess();
        $menu
            ->addChild('Hospital & Diagnostic')
            ->setAttribute('icon', 'fa fa-hospital-o')
            ->setAttribute('dropdown', true);

        $menu['Hospital & Diagnostic']->addChild('Manage Invoice')
            ->setAttribute('icon', 'icon icon-medkit')
            ->setAttribute('dropdown', true);
        if ($securityContext->isGranted('ROLE_DOMAIN_HOSPITAL_OPERATOR')) {
            if (!empty($config) and in_array('diagnostic', $config)) {
                $menu['Hospital & Diagnostic']['Manage Invoice']->addChild('Diagnostic', array('route' => 'hms_invoice'))
                    ->setAttribute('icon', 'fa fa-hospital-o');
            }
            if (!empty($config) and in_array('admission', $config)) {
            $menu['Hospital & Diagnostic']['Manage Invoice']->addChild('Admission', array('route' => 'hms_invoice_admission'))
                ->setAttribute('icon', 'fa fa-ambulance');
            }
        }
        if ($securityContext->isGranted('ROLE_DOMAIN_HOSPITAL_MANAGER')) {
            if (!empty($config) and in_array('doctor', $config)) {
                $menu['Hospital & Diagnostic']['Manage Invoice']->addChild('Commission', array('route' => 'hms_doctor_commission_invoice'))
                    ->setAttribute('icon', 'fa fa-user-md');
            }
        }
        if ($securityContext->isGranted('ROLE_DOMAIN_HOSPITAL_OPERATOR')) {
            if (!empty($config)) {
                $menu['Hospital & Diagnostic']['Manage Invoice']->addChild('Patient', array('route' => 'hms_customer'))
                    ->setAttribute('icon', 'fa fa-user');
            }
        }
        if ($securityContext->isGranted('ROLE_DOMAIN_HOSPITAL_LAB') || $securityContext->isGranted('ROLE_DOMAIN_HOSPITAL_DOCTOR')) {
            $menu['Hospital & Diagnostic']->addChild('Diagnostic Report')
                ->setAttribute('icon', 'fa fa-stethoscope')
                ->setAttribute('dropdown', true);
            $menu['Hospital & Diagnostic']['Diagnostic Report']->addChild('Collection & Process', array('route' => 'hms_invoice_particular'))
                ->setAttribute('icon', 'fa fa-stethoscope');
        }

        if ($securityContext->isGranted('ROLE_DOMAIN_HOSPITAL_MANAGER')) {

            $menu['Hospital & Diagnostic']->addChild('Master Data')
                ->setAttribute('icon', 'icon icon-cog')
                ->setAttribute('dropdown', true);
            $menu['Hospital & Diagnostic']['Master Data']->addChild('Diagnostic Test', array('route' => 'hms_pathology'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Hospital & Diagnostic']['Master Data']->addChild('Doctor', array('route' => 'hms_doctor'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Hospital & Diagnostic']['Master Data']->addChild('Referred Doctor', array('route' => 'hms_referreddoctor'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Hospital & Diagnostic']['Master Data']->addChild('Cabin/Ward', array('route' => 'hms_cabin'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Hospital & Diagnostic']['Master Data']->addChild('Surgery', array('route' => 'hms_surgery'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Hospital & Diagnostic']['Master Data']->addChild('Other Service', array('route' => 'hms_other_service'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Hospital & Diagnostic']['Master Data']->addChild('Service Group', array('route' => 'hms_service_group'))->setAttribute('icon', 'icon-tag');
           /* $menu['Hospital & Diagnostic']['Master Data']->addChild('Category', array('route' => 'hms_category'))->setAttribute('icon', 'icon-tag');
           */
            $menu['Hospital & Diagnostic']['Master Data']->addChild('Commission', array('route' => 'hms_commission'))->setAttribute('icon', 'icon-tag');
            if ($securityContext->isGranted('ROLE_DOMAIN_HOSPITAL_CONFIG')) {
                $menu['Hospital & Diagnostic']['Master Data']->addChild('Configuration', array('route' => 'hms_config_manage'))
                    ->setAttribute('icon', 'icon-cog');
            }
            $menu['Hospital & Diagnostic']->addChild('Manage Stock')
                ->setAttribute('icon', 'icon icon-truck')
                ->setAttribute('dropdown', true);
            $menu['Hospital & Diagnostic']['Manage Stock']->addChild('Item Issue', array('route' => 'hms_stockout'))
                ->setAttribute('icon', 'icon-th-list');
             $menu['Hospital & Diagnostic']['Manage Stock']->addChild('Medicine', array('route' => 'hms_medicine'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Hospital & Diagnostic']['Manage Stock']->addChild('Accessories', array('route' => 'hms_accessories'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Hospital & Diagnostic']->addChild('Purchase')
                ->setAttribute('icon', 'icon icon-truck')
                ->setAttribute('dropdown', true);
            $menu['Hospital & Diagnostic']['Purchase']->addChild('Medicine Receive', array('route' => 'hms_purchase'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Hospital & Diagnostic']['Purchase']->addChild('Accessories Receive', array('route' => 'hms_accessories_purchase'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Hospital & Diagnostic']['Purchase']->addChild('Vendor', array('route' => 'hms_vendor'))->setAttribute('icon', 'icon-tag');

            $menu['Hospital & Diagnostic']->addChild('Reports')
                ->setAttribute('icon', 'icon icon-cog')
                ->setAttribute('dropdown', true);
            $menu['Hospital & Diagnostic']['Reports']->addChild('Sales Summary', array('route' => 'hms_report_sales_summary'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Hospital & Diagnostic']['Reports']->addChild('Sales Details', array('route' => 'hms_report_sales_details'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Hospital & Diagnostic']['Reports']->addChild('Service Wise Sales', array('route' => 'hms_report_sales_service'))
                ->setAttribute('icon', 'icon-th-list');
        }
        return $menu;

    }

    public function DmsMenu($menu)
    {

        $securityContext = $this->container->get('security.token_storage')->getToken()->getUser();

        $config = $securityContext->getGlobalOption()->getHospitalConfig()->getInvoiceProcess();
        $menu
            ->addChild('Dental & Diagnosis')
            ->setAttribute('icon', 'fa fa-hospital-o')
            ->setAttribute('dropdown', true);

        $menu['Dental & Diagnosis']->addChild('Patient', array('route' => 'dms_invoice'))
            ->setAttribute('icon', 'fa fa-medkit');
        $menu['Dental & Diagnosis']->addChild('Treatment Schedule', array('route' => 'dms_treatment_plan'))
            ->setAttribute('icon', 'fa fa-calendar');
        if ($securityContext->isGranted('ROLE_DOMAIN_DMS_OPERATOR')) {
            if (!empty($config) and in_array('admission', $config)) {
            $menu['Dental & Diagnosis']->addChild('Patient', array('route' => 'dms_invoice'))
                ->setAttribute('icon', 'fa fa-stethoscope');
            }
        }
        if ($securityContext->isGranted('ROLE_DOMAIN_DMS_MANAGER')) {
            if (!empty($config) and in_array('doctor', $config)) {
                $menu['Dental & Diagnosis']->addChild('Commission', array('route' => 'dms_doctor_commission_invoice'))
                    ->setAttribute('icon', 'fa fa-user-md');
            }
        }

        $menu['Dental & Diagnosis']->addChild('Expense')
            ->setAttribute('icon', 'icon icon-money')
            ->setAttribute('dropdown', true);
        $menu['Dental & Diagnosis']['Expense']->addChild('Expenditure', array('route' => 'dms_account_expenditure'))
            ->setAttribute('icon', 'fa fa-indent');
        $menu['Dental & Diagnosis']['Expense']->addChild('Expense Category', array('route' => 'dms_expensecategory'))
            ->setAttribute('icon', 'icon-tags');

        if ($securityContext->isGranted('ROLE_DOMAIN_DMS_MANAGER')) {

            $menu['Dental & Diagnosis']->addChild('Master Data')
                ->setAttribute('icon', 'icon icon-cog')
                ->setAttribute('dropdown', true);
            $menu['Dental & Diagnosis']['Master Data']->addChild('Treatment Plan', array('route' => 'dms_treatment'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Dental & Diagnosis']['Master Data']->addChild('Particular', array('route' => 'dms_particular'))
                ->setAttribute('icon', 'icon-th-list');
             $menu['Dental & Diagnosis']['Master Data']->addChild('Service', array('route' => 'dms_service'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Dental & Diagnosis']['Master Data']->addChild('Doctor', array('route' => 'dms_doctor'))
                ->setAttribute('icon', 'icon-th-list');
            if ($securityContext->isGranted('ROLE_DOMAIN_DMS_CONFIG')) {
                $menu['Dental & Diagnosis']['Master Data']->addChild('Configuration', array('route' => 'dms_config_manage'))
                    ->setAttribute('icon', 'icon-cog');
            }
            $menu['Dental & Diagnosis']->addChild('Manage Stock')
                ->setAttribute('icon', 'icon icon-truck')
                ->setAttribute('dropdown', true);
            $menu['Dental & Diagnosis']['Manage Stock']->addChild('Accessories Out', array('route' => 'dms_treatment_accessories'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Dental & Diagnosis']['Manage Stock']->addChild('Accessories', array('route' => 'dms_medicine'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Dental & Diagnosis']->addChild('Purchase')
                ->setAttribute('icon', 'icon icon-truck')
                ->setAttribute('dropdown', true);
            $menu['Dental & Diagnosis']['Purchase']->addChild('Receive', array('route' => 'dms_purchase'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Dental & Diagnosis']['Purchase']->addChild('Vendor', array('route' => 'dms_vendor'))->setAttribute('icon', 'icon-tag');

            $menu['Dental & Diagnosis']->addChild('Sales Reports')
                ->setAttribute('icon', 'icon-bar-chart')
                ->setAttribute('dropdown', true);
            $menu['Dental & Diagnosis']['Sales Reports']->addChild('Sales Summary', array('route' => 'dms_report_sales_summary'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Dental & Diagnosis']['Sales Reports']->addChild('Sales Details', array('route' => 'dms_report_sales'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Dental & Diagnosis']['Sales Reports']->addChild('Sales Monthly', array('route' => 'dms_report_sales_monthly'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Dental & Diagnosis']['Sales Reports']->addChild('Sales Yearly', array('route' => 'dms_report_sales_yearly'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Dental & Diagnosis']['Sales Reports']->addChild('All Sales Yearly', array('route' => 'dms_report_sales_all_yearly'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Dental & Diagnosis']['Sales Reports']->addChild('Treatment Base Sales', array('route' => 'dms_report_sales_treatment'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Dental & Diagnosis']['Sales Reports']->addChild('Purchase', array('route' => 'dms_report_purchase'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Dental & Diagnosis']->addChild('Accounting Report')
                ->setAttribute('icon', 'icon-bar-chart')
                ->setAttribute('dropdown', true);
            $menu['Dental & Diagnosis']['Accounting Report']->addChild('Cash in Hand', array('route' => 'dms_report_cash'))
                ->setAttribute('icon', 'icon-money');
            $menu['Dental & Diagnosis']['Accounting Report']->addChild('Expenditure', array('route' => 'dms_report_expenditure'))
                ->setAttribute('icon', 'fa fa-indent');
            $menu['Dental & Diagnosis']['Accounting Report']->addChild('Income', array('route' => 'dms_report_income'))
                ->setAttribute('icon', 'icon-credit-card');
            $menu['Dental & Diagnosis']['Accounting Report']->addChild('Balance Sheet', array('route' => 'dms_report_balance_sheet'))
                ->setAttribute('icon', 'icon-table');
            $menu['Dental & Diagnosis']->addChild('Stock Report')
                ->setAttribute('icon', 'icon-bar-chart')
                ->setAttribute('dropdown', true);
            $menu['Dental & Diagnosis']['Stock Report']->addChild('Accessories Stock', array('route' => 'dms_report_stock'))
                ->setAttribute('icon', ' icon-inbox');
            $menu['Dental & Diagnosis']['Stock Report']->addChild('Accessories Out', array('route' => 'dms_report_stock_out'))
                ->setAttribute('icon', 'icon-hdd');

        }
        return $menu;

    }

    public function DpsMenu($menu)
    {

        $securityContext = $this->container->get('security.token_storage')->getToken()->getUser();
        $menu
            ->addChild('Doctor Prescription')
            ->setAttribute('icon', 'fa fa-hospital-o')
            ->setAttribute('dropdown', true);
        $menu['Doctor Prescription']->addChild('Patient', array('route' => 'dps_invoice'))
            ->setAttribute('icon', 'fa fa-medkit');
        $menu['Doctor Prescription']->addChild('Expense')
            ->setAttribute('icon', 'icon icon-money')
            ->setAttribute('dropdown', true);
        $menu['Doctor Prescription']['Expense']->addChild('Expenditure', array('route' => 'dps_account_expenditure'))
            ->setAttribute('icon', 'fa fa-indent');
        $menu['Doctor Prescription']['Expense']->addChild('Expense Category', array('route' => 'dps_expensecategory'))
            ->setAttribute('icon', 'icon-tags');
        if ($securityContext->isGranted('ROLE_DOMAIN_DMS_MANAGER')) {

            $menu['Doctor Prescription']->addChild('Master Data')->setAttribute('icon', 'icon icon-cog')
                ->setAttribute('dropdown', true);
            $menu['Doctor Prescription']['Master Data']->addChild('Particular', array('route' => 'dps_particular'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Doctor Prescription']['Master Data']->addChild('Service', array('route' => 'dps_service'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Doctor Prescription']['Master Data']->addChild('Doctor', array('route' => 'dps_doctor'))
                ->setAttribute('icon', 'icon-th-list');
            if ($securityContext->isGranted('ROLE_DOMAIN_DMS_CONFIG')) {
                $menu['Doctor Prescription']['Master Data']->addChild('Configuration', array('route' => 'dps_config_manage'))
                    ->setAttribute('icon', 'icon-cog');
            }

        }
        return $menu;

    }

    public function DrugMenu($menu)
    {
        $securityContext = $this->container->get('security.token_storage')->getToken()->getUser();

        $menu
            ->addChild('Drug')
            ->setAttribute('icon', 'fa fa-stethoscope')
            ->setAttribute('dropdown', true);
        $menu['Drug']->addChild('Add Drug', array('route' => 'medicinebrand_new'))->setAttribute('icon', 'icon-medkit');
        $menu['Drug']->addChild('Add Drug', array('route' => 'medicine_user'))->setAttribute('icon', 'icon-medkit');
        if ($securityContext->isGranted('ROLE_ADMIN') OR $securityContext->isGranted('ROLE_SUPER_ADMIN')) {
        $menu['Drug']->addChild('Drug', array('route' => 'medicine'))->setAttribute('icon', 'icon-medkit');
        }
        return $menu;
    }

    public function medicineMenu($menu)
    {
        $securityContext = $this->container->get('security.token_storage')->getToken()->getUser();

        $menu
            ->addChild('Medicine')
            ->setAttribute('icon', 'icon icon-th-large')
            ->setAttribute('dropdown', true);
        if ($securityContext->isGranted('ROLE_MEDICINE_SALES')) {
                $menu['Medicine']->addChild('Manage Sales', array('route' => 'medicine_sales'))
                    ->setAttribute('icon', 'icon-list');
                $menu['Medicine']->addChild('Sales Return', array('route' => 'medicine_sales_return'))
                    ->setAttribute('icon', 'icon-list');
                if ($securityContext->isGranted('ROLE_CRM') or $securityContext->isGranted('ROLE_DOMAIN')) {
	                $menu['Medicine']->addChild('Notepad', array('route' => 'domain_notepad'))->setAttribute('icon', 'fa fa-file');
	                $menu['Medicine']->addChild('Customer', array('route' => 'domain_customer'))->setAttribute('icon', 'fa fa-group');
                }
            }
	    if ($securityContext->isGranted('ROLE_MEDICINE_PURCHASE')) {

		    $menu['Medicine']->addChild('Manage Purchase')
		                     ->setAttribute('icon', 'icon icon-truck')
		                     ->setAttribute('dropdown', true);

		    $menu['Medicine']['Manage Purchase']->addChild('Purchase', array('route' => 'medicine_purchase'))
		                                        ->setAttribute('icon', 'icon-th-list');
		    $menu['Medicine']['Manage Purchase']->addChild('Instant Purchase', array('route' => 'medicine_instantpurchase'))
		                                        ->setAttribute('icon', 'icon-th-list');
		    $menu['Medicine']['Manage Purchase']->addChild('Purchase Return', array('route' => 'medicine_purchase_return'))
		                                        ->setAttribute('icon', 'icon-th-list');
		    $menu['Medicine']['Manage Purchase']->addChild('Pre-purchase', array('route' => 'medicine_prepurchase'))
		                                        ->setAttribute('icon', 'icon-th-list');
		    $menu['Medicine']['Manage Purchase']->addChild('Vendor', array('route' => 'medicine_vendor'))->setAttribute('icon', 'icon-tag');

	    }
        if ($securityContext->isGranted('ROLE_MEDICINE_STOCK')) {
	            $menu['Medicine']->addChild('Manage Stock')
	                             ->setAttribute('icon', 'icon icon-truck')
	                             ->setAttribute('dropdown', true);
                $menu['Medicine']['Manage Stock']->addChild('Stock Item', array('route' => 'medicine_stock'))
                    ->setAttribute('icon', 'icon-th-list');
                $menu['Medicine']['Manage Stock']->addChild('Stock Item Details', array('route' => 'medicine_purchase_item'))
                    ->setAttribute('icon', 'icon-th-list');
                $menu['Medicine']['Manage Stock']->addChild('Stock Item History', array('route' => 'medicine_stock_item_history'))
                    ->setAttribute('icon', 'icon-th-list');
                $menu['Medicine']['Manage Stock']->addChild('Medicine Expiration', array('route' => 'medicine_expiry_item'))
                    ->setAttribute('icon', 'icon-th-list');
                $menu['Medicine']['Manage Stock']->addChild('Medicine Short List', array('route' => 'medicine_stock_short_item'))
                    ->setAttribute('icon', 'icon-th-list');
	            if ($securityContext->isGranted('ROLE_MEDICINE_MANAGER')) {
		            $menu['Medicine']['Manage Stock']->addChild( 'Damage', array( 'route' => 'medicine_damage' ) )
		                                             ->setAttribute( 'icon', 'icon-th-list' );
	            }

            }
        if ($securityContext->isGranted('ROLE_MEDICINE_ADMIN')) {
            $menu['Medicine']->addChild('Master Data')
                ->setAttribute('icon', 'icon icon-cog')
                ->setAttribute('dropdown', true);
            $menu['Medicine']['Master Data']->addChild('Setting', array('route' => 'medicine_particular'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Medicine']['Master Data']->addChild('Minimum Stock Setup', array('route' => 'medicine_minimum'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Medicine']['Master Data']->addChild('User Sales Setup', array('route' => 'medicine_sales_user', 'routeParameters' => array('source' => 'medicine')))
                ->setAttribute('icon', 'icon icon-cog');
            $menu['Medicine']['Master Data']->addChild('Configuration', array('route' => 'medicine_config_manage'))
                ->setAttribute('icon', 'icon icon-cog');
            $menu['Medicine']['Master Data']->addChild('New Medicine', array('route' => 'medicine_user'))
                ->setAttribute('icon', 'icon icon-cog');
        }
        if ($securityContext->isGranted('ROLE_MEDICINE_ADMIN')) {

                $menu['Medicine']->addChild('Reports')
                    ->setAttribute('icon', 'icon icon-bar-chart')
                    ->setAttribute('dropdown', true);
	        $menu['Medicine']['Reports']->addChild('System Overview', array('route' => 'medicine_system_overview'))
	                                    ->setAttribute('icon', 'icon-th-list');
	        $menu['Medicine']['Reports']->addChild('Sales')
                    ->setAttribute('icon', 'icon icon-bar-chart')
                    ->setAttribute('dropdown', true);
                $menu['Medicine']['Reports']['Sales']->addChild('Sales Summary', array('route' => 'medicine_report_sales_summary'))
                    ->setAttribute('icon', 'icon-th-list');
	            $menu['Medicine']['Reports']['Sales']->addChild('Sales Details', array('route' => 'medicine_report_sales_details'))
                    ->setAttribute('icon', 'icon-th-list');
	            $menu['Medicine']['Reports']['Sales']->addChild('Vendor base Sales', array('route' => 'medicine_report_sales_vendor_customer'))->setAttribute('icon', 'icon-th-list');
	            $menu['Medicine']['Reports']['Sales']->addChild('Product Wise Sales', array('route' => 'medicine_report_sales_stock'))
                    ->setAttribute('icon', 'icon-th-list');
	            $menu['Medicine']['Reports']['Sales']->addChild('User Wise Sales', array('route' => 'medicine_report_sales_user'))
                    ->setAttribute('icon', 'icon-th-list');
	            $menu['Medicine']['Reports']['Sales']->addChild('User Monthly Sales', array('route' => 'medicine_report_sales_user_monthly'))
                    ->setAttribute('icon', 'icon-th-list');
	            $menu['Medicine']['Reports']->addChild('Purchase')
	                                        ->setAttribute('icon', 'icon icon-bar-chart')
	                                        ->setAttribute('dropdown', true);
                $menu['Medicine']['Reports']['Purchase']->addChild('Purchase Summary', array('route' => 'medicine_report_purchase_summary'))
                    ->setAttribute('icon', 'icon-th-list');
                $menu['Medicine']['Reports']['Purchase']->addChild('Vendor Ledger', array('route' => 'medicine_report_purchase_vendor'))->setAttribute('icon', 'icon-th-list');
                $menu['Medicine']['Reports']['Purchase']->addChild('Vendor Details', array('route' => 'medicine_report_purchase_vendor_details'))->setAttribute('icon', 'icon-th-list');
	            $menu['Medicine']['Reports']['Purchase']->addChild('Vendor Stock', array('route' => 'medicine_report_product_stock_sales'))->setAttribute('icon', 'icon-th-list');
	            /*$menu['Medicine']['Reports']['Purchase']->addChild('Purchase Wise Sales', array('route' => 'medicine_report_purchase_stock'))->setAttribute('icon', 'icon-th-list');*/
	            $menu['Medicine']['Reports']->addChild('Brand', array('route' => 'medicine_report_purchase_brand_sales'))->setAttribute('icon', 'icon-th-list');
	            $menu['Medicine']['Reports']->addChild('Brand Details', array('route' => 'medicine_report_purchase_brand_sales_details'))->setAttribute('icon', 'icon-th-list');

         }

            return $menu;

    }

    public function RestaurantMenu($menu)
    {

        $securityContext = $this->container->get('security.token_storage')->getToken()->getUser();

        $menu
            ->addChild('Restaurant')
            ->setAttribute('icon', 'icon icon-th-large')
            ->setAttribute('dropdown', true);

        $menu['Restaurant']->addChild('Point of Sales ', array('route' => 'restaurant_invoice_new'))
            ->setAttribute('icon', 'icon-th-large');
        $menu['Restaurant']->addChild('Manage Sales', array('route' => 'restaurant_invoice'))
            ->setAttribute('icon', 'icon-list');
        $menu['Restaurant']->addChild('Customer', array('route' => 'restaurant_customer'))->setAttribute('icon', 'icon icon-user');
        if ($securityContext->isGranted('ROLE_DOMAIN_RESTAURANT_MANAGER')) {
        $menu['Restaurant']->addChild('Master Data')
            ->setAttribute('icon', 'icon icon-cog')
            ->setAttribute('dropdown', true);
        $menu['Restaurant']['Master Data']->addChild('Product', array('route' => 'restaurant_product'))
            ->setAttribute('icon', 'icon-th-list');
        $menu['Restaurant']['Master Data']->addChild('Product Sorting', array('route' => 'restaurant_product_sorting'))
            ->setAttribute('icon', 'icon-th-list');
        $menu['Restaurant']['Master Data']->addChild('Category', array('route' => 'restaurant_category'))
            ->setAttribute('icon', 'icon-th-list');
        $menu['Restaurant']['Master Data']->addChild('Particular', array('route' => 'restaurant_particular'))
            ->setAttribute('icon', 'icon-th-list');
        $menu['Restaurant']['Master Data']->addChild('Configuration', array('route' => 'restaurant_config_manage'))
            ->setAttribute('icon', 'icon-cog');
        $menu['Restaurant']->addChild('Manage Stock')
            ->setAttribute('icon', 'icon icon-truck')
            ->setAttribute('dropdown', true);
        $menu['Restaurant']['Manage Stock']->addChild('Stock Product', array('route' => 'restaurant_stock'))
            ->setAttribute('icon', 'icon-th-list');
        $menu['Restaurant']['Manage Stock']->addChild('Purchase', array('route' => 'restaurant_purchase'))
            ->setAttribute('icon', 'icon-th-list');
        $menu['Restaurant']['Manage Stock']->addChild('Vendor', array('route' => 'restaurant_vendor'))->setAttribute('icon', 'icon-tag');
        $menu['Restaurant']->addChild('Reports')
            ->setAttribute('icon', 'icon icon-cog')
            ->setAttribute('dropdown', true);
        $menu['Restaurant']['Reports']->addChild('Sales Summary', array('route' => 'restaurant_report_sales_summary'))
            ->setAttribute('icon', 'icon-th-list');
        $menu['Restaurant']['Reports']->addChild('Sales Details', array('route' => 'restaurant_report_sales_details'))
            ->setAttribute('icon', 'icon-th-list');
        $menu['Restaurant']['Reports']->addChild('Product Wise Sales', array('route' => 'restaurant_report_sales_service'))
            ->setAttribute('icon', 'icon-th-list');
        $menu['Restaurant']['Reports']->addChild('Stock Wise Sales', array('route' => 'restaurant_report_sales_service'))
            ->setAttribute('icon', 'icon-th-list');
        $menu['Restaurant']['Reports']->addChild('Stock Summary', array('route' => 'restaurant_report_stock'))
            ->setAttribute('icon', 'icon-th-list');
        }
        return $menu;

    }

    public function ClientRelationManagementMenu($menu)
    {
        $menu
            ->addChild('CRM')
            ->setAttribute('dropdown', true);
        $menu['CRM']->addChild('People')->setAttribute('icon', 'icon-group')->setAttribute('dropdown', true);
        $menu['CRM']['People']->addChild('Sms', array('route' => 'domain_customer_sms'))->setAttribute('icon', 'icon-phone');
        $menu['CRM']['People']->addChild('Email', array('route' => 'domain_customer_email'))->setAttribute('icon', 'icon-envelope-alt');
        $menu['CRM']->addChild('Promotion')->setAttribute('icon', 'icon-trello')->setAttribute('dropdown', true);
        $menu['CRM']['Promotion']->addChild('SMS', array('route' => 'domain_customer'))->setAttribute('icon', 'icon-phone');
        $menu['CRM']['Promotion']->addChild('Email', array('route' => 'domain_customer'))->setAttribute('icon', 'icon-envelope-alt');
        $menu['CRM']->addChild('Staff')->setAttribute('icon', 'icon-foursquare')->setAttribute('dropdown', true);
        $menu['CRM']['Staff']->addChild('SMS', array('route' => 'domain_customer'))->setAttribute('icon', 'icon-phone');
        $menu['CRM']['Staff']->addChild('Email', array('route' => 'domain_customer'))->setAttribute('icon', 'icon-envelope-alt');
        $menu['CRM']->addChild('Inbox')->setAttribute('icon', 'icon-envelope')->setAttribute('dropdown', true);
        $menu['CRM']['Inbox']->addChild('SMS', array('route' => 'domain_customer'))->setAttribute('icon', 'icon-phone');
        $menu['CRM']['Inbox']->addChild('Email', array('route' => 'domain_customer'))->setAttribute('icon', 'icon-envelope-alt');
        return $menu;

    }

	public function ReservationMenu($menu)
	{

		$securityContext = $this->container->get('security.token_storage')->getToken()->getUser();

		$menu
			->addChild('Hotel & Restaurant')
			->setAttribute('icon', 'icon-briefcase')
			->setAttribute('dropdown', true);

		if ($securityContext->isGranted('ROLE_HOTEL_INVOICE')) {

			$menu['Hotel & Restaurant']->addChild('Add Invoice', array('route' => 'hotel_invoice_new'))
			                            ->setAttribute('icon', 'icon-plus-sign');
			$menu['Hotel & Restaurant']->addChild('Invoice', array('route' => 'hotel_invoice'))
			                            ->setAttribute('icon', 'icon-th-list');
		}
		if ($securityContext->isGranted('ROLE_CRM') or $securityContext->isGranted('ROLE_DOMAIN')) {
			$menu['Hotel & Restaurant']->addChild('Notepad', array('route' => 'domain_notepad'))->setAttribute('icon', 'fa fa-file');
			$menu['Hotel & Restaurant']->addChild('Customer', array('route' => 'domain_customer'))->setAttribute('icon', 'fa fa-group');
		}
		if ($securityContext->isGranted('ROLE_HOTEL_PURCHASE')) {

			$menu['Hotel & Restaurant']->addChild('Purchase')->setAttribute('icon', 'icon icon-truck')->setAttribute('dropdown', true);
			$menu['Hotel & Restaurant']['Purchase']->addChild('Purchase', array('route' => 'hotel_purchase'))
			                                        ->setAttribute('icon', 'icon-th-list');
			$menu['Hotel & Restaurant']['Purchase']->addChild('Vendor', array('route' => 'hotel_vendor'))->setAttribute('icon', 'icon-tag');
		}

		if ($securityContext->isGranted('ROLE_HOTEL_STOCK')) {
			$menu['Hotel & Restaurant']->addChild('Manage Stock', array('route' => 'hotel_stock'))->setAttribute('icon', 'icon-th-list');
			$menu['Hotel & Restaurant']->addChild('Manage Damage', array('route' => 'hotel_damage'))->setAttribute('icon', 'icon-trash');
		}

		if ($securityContext->isGranted('ROLE_HOTEL_PURCHASE')) {

			$menu['Hotel & Restaurant']->addChild('Master Data')
			                            ->setAttribute('icon', 'icon icon-cog')
			                            ->setAttribute('dropdown', true);
			$menu['Hotel & Restaurant']['Master Data']->addChild('Category', array('route' => 'hotel_category'))->setAttribute('icon', 'icon-th-list');
			$menu['Hotel & Restaurant']['Master Data']->addChild('Configuration', array('route' => 'hotel_config_manage'))->setAttribute('icon', 'icon-cog');
		}

		$menu['Hotel & Restaurant']->addChild('Reports')
		                            ->setAttribute('icon', 'icon icon-bar-chart')
		                            ->setAttribute('dropdown', true);

		$menu['Hotel & Restaurant']['Reports']->addChild('Sales')
		                                       ->setAttribute('icon', 'icon icon-bar-chart')
		                                       ->setAttribute('dropdown', true);

		$menu['Hotel & Restaurant']['Reports']['Sales']->addChild('Sales Summary', array('route' => 'hotel_report_sales_summary'))
		                                                ->setAttribute('icon', 'icon-th-list');
		$menu['Hotel & Restaurant']['Reports']['Sales']->addChild('Sales Details', array('route' => 'hotel_report_sales_details'))
		                                                ->setAttribute('icon', 'icon-th-list');
		$menu['Hotel & Restaurant']['Reports']['Sales']->addChild('Customer Sales', array('route' => 'hotel_report_customer_sales_item'))
		                                                ->setAttribute('icon', 'icon-th-list');
		$menu['Hotel & Restaurant']['Reports']['Sales']->addChild('Product Wise Sales', array('route' => 'hotel_report_sales_stock'))
		                                                ->setAttribute('icon', 'icon-th-list');
		$menu['Hotel & Restaurant']['Reports']->addChild('Purchase')
		                                       ->setAttribute('icon', 'icon icon-bar-chart')
		                                       ->setAttribute('dropdown', true);
		$menu['Hotel & Restaurant']['Reports']['Purchase']->addChild('Purchase Summary', array('route' => 'hotel_report_purchase_summary'))
		                                                   ->setAttribute('icon', 'icon-th-list');
		$menu['Hotel & Restaurant']['Reports']['Purchase']->addChild('Vendor Ledger', array('route' => 'hotel_report_purchase_vendor'))->setAttribute('icon', 'icon-th-list');
		return $menu;

	}

	public function ElectionMenu($menu)
	{

		$securityContext = $this->container->get('security.token_storage')->getToken()->getUser();

		$menu
			->addChild('Election & Committee')
			->setAttribute('icon', 'icon-briefcase')
			->setAttribute('dropdown', true);
		$menu['Election & Committee']->addChild('Manage Election')
		                           ->setAttribute('icon', 'icon-th-list')
		                           ->setAttribute('dropdown', true);
		if ($securityContext->isGranted('ROLE_ELECTION_OPERATOR')) {
			$menu['Election & Committee']['Manage Election']->addChild( 'Election Setup', array( 'route' => 'election_setup' ) )->setAttribute( 'icon', 'icon-th-list' );
			$menu['Election & Committee']['Manage Election']->addChild( 'Candidate', array( 'route' => 'election_candidate' ) )->setAttribute( 'icon', 'icon-user' );
			$menu['Election & Committee']['Manage Election']->addChild( 'Committee', array( 'route' => 'election_committee' ) )->setAttribute( 'icon', 'icon-th-list' );
			$menu['Election & Committee']['Manage Election']->addChild( 'Vote Center', array( 'route' => 'election_votecenter' ) )->setAttribute( 'icon', 'icon-th-list' );
			/*		$menu['Election & Committee']->addChild('Organization')
												 ->setAttribute('icon', 'icon-th-list')
												 ->setAttribute('dropdown', true);
					$menu['Election & Committee']['Committee']->addChild('Committee Setup', array('route' => 'election_organizationcommittee'))->setAttribute('icon', 'icon-th-list');*/
			$menu['Election & Committee']->addChild( 'Members', array( 'route' => 'election_member' ) )->setAttribute( 'icon', 'icon-th-list' );
			$menu['Election & Committee']->addChild( 'Voters', array( 'route' => 'election_voter' ) )->setAttribute( 'icon', 'icon-th-list' );
			$menu['Election & Committee']->addChild( 'Campaign', array( 'route' => 'election_event' ) )->setAttribute( 'icon', 'icon-calendar' );
			$menu['Election & Committee']->addChild( 'Campaign Analysis', array( 'route' => 'election_campaign' ) )->setAttribute( 'icon', 'icon-refresh' );
		}
		if ($securityContext->isGranted('ROLE_ELECTION_MANAGER')) {
			$menu['Election & Committee']->addChild( 'Manage SMS', array( 'route' => 'election_sms' ) )->setAttribute( 'icon', 'icon-th-list' );
		}
		if ($securityContext->isGranted('ROLE_ELECTION_ADMIN')) {
			$menu['Election & Committee']->addChild( 'Master Data' )
			                             ->setAttribute( 'icon', 'icon icon-cog' )
			                             ->setAttribute( 'dropdown', true );
			$menu['Election & Committee']['Master Data']->addChild( 'Setting Option', array( 'route' => 'election_particular' ) )->setAttribute( 'icon', 'icon-th-list' );
			$menu['Election & Committee']['Master Data']->addChild( 'Setting Location', array( 'route' => 'election_location' ) )->setAttribute( 'icon', 'icon-th-list' );
			$menu['Election & Committee']['Master Data']->addChild( 'Member Import', array( 'route' => 'election_member_import' ) )->setAttribute( 'icon', 'icon-th-list' );
			$menu['Election & Committee']['Master Data']->addChild( 'Configuration', array( 'route' => 'election_config_manage' ) )->setAttribute( 'icon', 'icon-cog' );
		}
		return $menu;

	}

    public function InstituteSystemMenu($menu)
    {
        $menu
            ->addChild('IMS')
            ->setAttribute('icon', 'fa fa-cog')
            ->setAttribute('dropdown', true);
        return $menu;

    }

    public function BillingSystemMenu($menu)
    {
        $menu
            ->addChild('Billing System')
            ->setAttribute('icon', 'fa fa-cog')
            ->setAttribute('dropdown', true);
        return $menu;

    }

    public function PayrollMenu($menu)
    {

        $securityContext = $this->container->get('security.token_storage')->getToken()->getUser();
        $global = $securityContext->getGlobalOption();
        $menu
            ->addChild('HR & Payroll')
            ->setAttribute('icon', 'fa fa-group')
            ->setAttribute('dropdown', true);
        if($global->getIsBranch() == 1) {
            $menu['HR & Payroll']->addChild('Branch', array('route' => 'domain_branches'))->setAttribute('icon', 'icon-th-list');
        }
        if ($securityContext->isGranted('ROLE_HR_EMPLOYEE')) {
            $menu['HR & Payroll']->addChild('Human Resource')->setAttribute('icon', 'icon-group')->setAttribute('dropdown', true);
            $menu['HR & Payroll']['Human Resource']->addChild('Employee', array('route' => 'domain_user'))->setAttribute('icon', 'icon-user');
        }

        if ($securityContext->isGranted('ROLE_HR_ATTENDANCE')) {
            $menu['HR & Payroll']->addChild('Attendance')->setAttribute('icon', 'icon-group')->setAttribute('dropdown', true);
            $menu['HR & Payroll']['Attendance']->addChild('Employee', array('route' => 'attendance'))->setAttribute('icon', 'icon-user');
            $menu['HR & Payroll']['Attendance']->addChild('Leave Setup', array('route' => 'leave_setup'))->setAttribute('icon', 'icon-user');
            $menu['HR & Payroll']['Attendance']->addChild('Daily Attendance', array('route' => 'daily_attendance'))->setAttribute('icon', 'icon-user');
            $menu['HR & Payroll']['Attendance']->addChild('Calendar Weekend', array('route' => 'weekend'))->setAttribute('icon', 'icon-user');
        }
        if ($securityContext->isGranted('ROLE_PAYROLL_SALARY')) {

            $menu['HR & Payroll']->addChild('Payroll')->setAttribute('icon', 'icon-group')->setAttribute('dropdown', true);
            $menu['HR & Payroll']['Payroll']->addChild('Salary Transaction', array('route' => 'account_paymentsalary'))->setAttribute('icon', 'icon-th-list');
            $menu['HR & Payroll']['Payroll']->addChild('Payment Salary', array('route' => 'account_paymentsalary_employee'))->setAttribute('icon', 'icon-th-list');
            $menu['HR & Payroll']['Payroll']->addChild('Salary Invoice', array('route' => 'account_salarysetting'))->setAttribute('icon', 'icon-th-list');
        }

        if ($securityContext->isGranted('ROLE_ADMIN')) {

            $menu['HR & Payroll']->addChild('Manage Agent')->setAttribute('icon', 'icon-group')->setAttribute('dropdown', true);
            $menu['HR & Payroll']['Manage Agent']->addChild('Agent New', array('route' => 'agent_new'))->setAttribute('icon', 'icon-user');
            $menu['HR & Payroll']['Manage Agent']->addChild('Agent', array('route' => 'agent'))->setAttribute('icon', 'icon-user');
            $menu['HR & Payroll']->addChild('Agent Payroll')->setAttribute('icon', 'icon-group')->setAttribute('dropdown', true);
            $menu['HR & Payroll']['Agent Payroll']->addChild('Agent Transaction', array('route' => 'agentpayment'))->setAttribute('icon', 'icon-th-list');
            $menu['HR & Payroll']['Agent Payroll']->addChild('Agent Invoice', array('route' => 'agentpayment_invoice'))->setAttribute('icon', 'icon-th-list');
        }
        return $menu;

    }

    public function manageDomainInvoiceMenu($menu)
    {
        $securityContext = $this->container->get('security.token_storage')->getToken()->getUser();

        $menu
            ->addChild('Invoice Sms & Email')
            ->setAttribute('icon', 'fa fa-files-o')
            ->setAttribute('dropdown', true);
        if ($securityContext->isGranted('ROLE_SMS_MANAGER')) {
            $menu['Invoice Sms & Email']->addChild('Manage Sms')->setAttribute('icon', 'icon-phone')->setAttribute('dropdown', true);
            $menu['Invoice Sms & Email']['Manage Sms']->addChild('Sms Logs', array('route' => 'smssender'))->setAttribute('icon', 'icon-phone');
            $menu['Invoice Sms & Email']['Manage Sms']->addChild('Sms Bundle', array('route' => 'invoicesmsemail'))->setAttribute('icon', 'icon-money');
            $menu['Invoice Sms & Email']->addChild('Invoice Application', array('route' => 'invoicemodule_domain'))->setAttribute('icon', 'fa fa-files-o');
        }
        if ($securityContext->isGranted('ROLE_SMS_BULK')) {
            $menu['Invoice Sms & Email']['Manage Sms']->addChild('Bulk Sms', array('route' => 'smsbulk'))->setAttribute('icon', 'icon-envelope');
        }
        if ($securityContext->isGranted('ROLE_SMS_CONFIG')) {
            $menu['Invoice Sms & Email']['Manage Sms']->addChild('Notification Setup', array('route' => 'domain_notificationconfig'))->setAttribute('icon', 'fa fa-bell ');
        }
        return $menu;
    }

    public function manageSystemAccountMenu($menu)
    {
        $menu
            ->addChild('System Transaction')
            ->setAttribute('icon', 'fa fa-money')
            ->setAttribute('dropdown', true);
        $menu['System Transaction']->addChild('Bank', array('route' => 'bankaccount'))->setAttribute('icon', 'icon-money');
        $menu['System Transaction']->addChild('Mobile Bank', array('route' => 'mobilebankaccount'))->setAttribute('icon', 'icon-money');
        return $menu;
    }

    public function manageDomainMenu($menu)
    {
        $menu
            ->addChild('Manage Domain')
            ->setAttribute('icon', 'fa fa-cogs')
            ->setAttribute('dropdown', true);
        $menu['Manage Domain']->addChild('Setting Package')->setAttribute('icon', ' icon-cogs')->setAttribute('dropdown', true);
        $menu['Manage Domain']['Setting Package']->addChild('Application', array('route' => 'applicationpricing'))->setAttribute('icon', 'icon-briefcase');
        $menu['Manage Domain']['Setting Package']->addChild('SMS/Email', array('route' => 'smspricing'))->setAttribute('icon', 'icon-envelope');
        $menu['Manage Domain']->addChild('Manage Operation')->setAttribute('icon', 'icon-cog')->setAttribute('dropdown', true);
        $menu['Manage Domain']['Manage Operation']->addChild('Domain', array('route' => 'tools_domain'))->setAttribute('icon', 'fa fa-server');
        $menu['Manage Domain']->addChild('Manage Invoice')->setAttribute('icon', 'icon-money')->setAttribute('dropdown', true);
        $menu['Manage Domain']['Manage Invoice']->addChild('Customer Invoice', array('route' => 'invoicemodule'))->setAttribute('icon', 'icon-money');
        $menu['Manage Domain']['Manage Invoice']->addChild('Sms Bundle', array('route' => 'invoicesmsemail'))->setAttribute('icon', 'icon-money');

        return $menu;
    }

    public function manageFrontendMenu($menu)
    {
        $menu
            ->addChild('Manage Frontend')
            ->setAttribute('icon', 'fa fa-sitemap')
            ->setAttribute('dropdown', true);
        $menu['Manage Frontend']->addChild('Site Slider', array('route' => 'siteslider'));
        $menu['Manage Frontend']->addChild('Site Content', array('route' => 'sitecontent'));
        $menu['Manage Frontend']->addChild('Testimonial', array('route' => 'sitetestimonial'));
        $menu['Manage Frontend']->addChild('Team', array('route' => 'siteteam'));
        $menu['Manage Frontend']->addChild('Manage Mega Menu', array('route' => 'megamenu'));
        $menu['Manage Frontend']->addChild('Feature Category', array('route' => 'category_sorting'));
        $menu['Manage Frontend']->addChild('Collection', array('route' => 'collection'));
        return $menu;

    }

    public function toolsMenu($menu)
    {
        $menu
            ->addChild('Tools')
            ->setAttribute('icon', 'fa fa-cogs')
            ->setAttribute('dropdown', true);

        $menu['Tools']->addChild('Manage Option', array('route' => 'globaloption'));
        $menu['Tools']->addChild('Manage Setting', array('route' => 'sitesetting'));
        $menu['Tools']->addChild('Location', array('route' => 'location'));
        $menu['Tools']->addChild('Business Sector', array('route' => 'syndicate'));
        $menu['Tools']->addChild('Designation', array('route' => 'designation'));
        $menu['Tools']->addChild('Medicine Import', array('route' => 'medicine_import'));
        $menu['Tools']->addChild('Course', array('route' => 'course'));
        $menu['Tools']->addChild('Institute Level', array('route' => 'institutelevel'));
        $menu['Tools']->addChild('Syndicate Module', array('route' => 'syndicatemodule'));
        $menu['Tools']->addChild('Application Module', array('route' => 'appmodule'));
        $menu['Tools']->addChild('Application Testimonial', array('route' => 'applicationtestimonial'));
        $menu['Tools']->addChild('Module', array('route' => 'module'));
        $menu['Tools']->addChild('Theme', array('route' => 'theme'));
        $menu['Tools']->addChild('Menu Custom', array('route' => 'menucustom'));
        $menu['Tools']->addChild('Menu Group', array('route' => 'menugroup'));
        $menu['Tools']->addChild('Manage Brand', array('route' => 'branding'));
        /*    $menu['Tools']->addChild('Inventory&Accounting')
                ->setAttribute('icon','icon icon-reorder')
                ->setAttribute('dropdown', true);
            $menu['Tools']['Inventory&Accounting']->addChild('Color', array('route' => 'color'))->setAttribute('icon', 'icon-th-list');
            $menu['Tools']['Inventory&Accounting']->addChild('Size', array('route' => 'size'))->setAttribute('icon', 'icon-th-list');
            $menu['Tools']['Inventory&Accounting']->addChild('Account Head', array('route' => 'accounthead'))->setAttribute('icon','fa fa-money');*/

        return $menu;
    }

    public function syndicateMenu($menu)
    {
        $menu
            ->addChild('Syndicate')
            ->setAttribute('icon', 'fa fa-bookmark')
            ->setAttribute('dropdown', true);

        $menu['Syndicate']->addChild('Education', array('route' => 'education'));
        $menu['Syndicate']->addChild('Vendor', array('route' => 'vendor'));
        return $menu;
    }

    public function productCategoryMenu($menu)
    {
        $menu
            ->addChild('Product Category')
            ->setAttribute('icon', 'fa fa-bookmark')
            ->setAttribute('dropdown', true);

        $menu['Product Category']->addChild('Add Category', array('route' => 'category_new'));
        $menu['Product Category']->addChild('Listing', array('route' => 'category'));
        return $menu;
    }

    public function appearanceMenu($menu)
    {


    }

    public function manageAdvertismentMenu($menu)
    {
        $menu
            ->addChild('Manage Advertisment')
            ->setAttribute('icon', 'fa fa-bookmark')
            ->setAttribute('dropdown', true);

        $menu['Manage Advertisment']->addChild('Advertisment', array('route' => 'advertisment'));

        return $menu;

    }

    public function footerMenu(FactoryInterface $factory, array $options)
    {

        $menu = $factory->createItem('root');
        $menu->setChildrenAttribute('class', '');
        $grouping = $this->container->get('doctrine')->getRepository('SettingAppearanceBundle:MenuGrouping')->getFooterMenu();
        if ($grouping) {
            foreach ($grouping as $row) {

                $menu
                    ->addChild($row->getMenu()->getMenu(), array(
                        'route' => 'frontend_page',
                        'routeParameters' => array('slug' => $row->getMenu()->getMenuSlug())
                    ));

            }
        }
        return $menu;
    }

    public function categoryMenu(FactoryInterface $factory, array $options)
    {

        $menu = $factory->createItem('root');
        $menu->setChildrenAttribute('class', 'list-group margin-bottom-25 sidebar-menu');

        $this->buildChildMenus($menu, $this->getCategoryList());

        return $menu;

    }

    public function megaMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');
        $menus = $this->container->get('doctrine')->getRepository('SettingAppearanceBundle:MegaMenu')->getActiveMenus();
        $categoryRepository = $this->container->get('doctrine')->getRepository('ProductProductBundle:Category');
        foreach ($menus as $item) {
            /** @var MegaMenu $item */
            $menuName = $item->getName();
            $menu
                ->addChild($menuName)
                ->setAttribute('dropdown', true);
            $this->buildChildMenus($menu[$menuName], $categoryRepository->buildCategoryGroup($item->getCategories()));
            $this->buildCollectionMenu($menu[$menuName], $item->getCollections());
            $this->buildBrandMenu($menu[$menuName], $item->getBrands());
        }

        return $menu;
    }

    public function frontendEommerceMenu(FactoryInterface $factory, array $options)
    {

        $subdomain = $this->container->get('router')->getContext()->getParameter('subdomain');
        $menu = $factory->createItem('root');
        $menus = $this->container->get('doctrine')->getRepository('SettingAppearanceBundle:EcommerceMenu')->getActiveMenus($subdomain);

        $categoryRepository = $this->container->get('doctrine')->getRepository('ProductProductBundle:Category');
        foreach ($menus as $item) {

            /** @var EcommerceMenu $item */

            $menuName = $item->getName();
            $menu
                ->addChild($menuName)
                ->setAttribute('dropdown', true);
            $this->buildDomainCategoryMenus($menu[$menuName], $categoryRepository->buildCategoryGroup($item->getCategories()));
            $this->buildDomainBrandMenu($menu[$menuName], $item->getBrands());
            $this->buildDomainPromotionMenu($menu[$menuName], $item->getPromotions());
            $this->buildDomainTagMenu($menu[$menuName], $item->getTags());
            $this->buildDomainDiscountMenu($menu[$menuName], $item->getDiscounts());
            $this->buildDomainFeatureMenu($menu[$menuName], $item->getFeatures());
            // $this->buildDomainPromotionMenu($menu[$menuName], $item->getCollections($subdomain));
            //$this->buildDomainTagMenu($menu[$menuName], $item->getCollections($subdomain));
            // $this->buildBrandMenu($menu[$menuName], $item->getBrands());
        }

        return $menu;
    }

    private function buildDomainCategoryMenus(ItemInterface $menu, $categories)
    {

        foreach ($categories as $category) {

            /** var Category $category */
            $categoryName = $category['name'];

            if (!empty($categoryName)) {

                $menu
                    ->addChild($categoryName, array('route' => 'webservice_product_category',
                        'routeParameters' => array('id' => $category['id'])
                    ))
                    ->setAttribute('icon', 'fa fa-angle-right');

                if (!empty($category['__children'])) {
                    $menu->setAttribute('dropdown', true);
                    $menu[$categoryName]->setChildrenAttribute('class', 'dropdown-menu');
                    $this->buildDomainCategoryMenus($menu[$categoryName], $category['__children']);
                }
            }
        }
    }

    private function buildDomainBrandMenu(ItemInterface $menu, $brands)
    {
        $menu
            ->addChild('brands')
            ->setAttribute('brands', true)
            ->setAttribute('class', 'col-md-12 nav-brands');
        foreach ($brands as $brand) {
            /** @var Branding $brand */
            $menu['brands']->addChild($brand->getName(), array('route' => 'webservice_product_brand',
                'routeParameters' => array('id' => $brand->getId())
            ))
                ->setAttribute('brand', true)
                ->setAttribute('icon', 'fa fa-angle-right');

        }
    }

    private function buildDomainPromotionMenu(ItemInterface $menu, $collections)
    {

        if ($collections->count() > 0) {

            $menu
                ->addChild('collection');

            foreach ($collections as $collection) {
                /** @var Branding $brand */
                $menu['collection']->addChild($collection->getName(), array('route' => 'frontend_collection',
                    'routeParameters' => array('slug' => $collection->getSlug())
                ));
            }
        }

    }

    private function buildDomainTagMenu(ItemInterface $menu, $collections){
        if ($collections->count() > 0) {

            $menu
                ->addChild('collection');

            foreach ($collections as $collection) {
                /** @var Branding $brand */
                $menu['collection']->addChild($collection->getName(), array('route' => 'frontend_collection',
                    'routeParameters' => array('slug' => $collection->getSlug())
                ));
            }
        }
    }

    private function buildDomainDiscountMenu(ItemInterface $menu, $collections)
    {

        if ($collections->count() > 0) {

            $menu
                ->addChild('collection');

            foreach ($collections as $collection) {
                /** @var Branding $brand */
                $menu['collection']->addChild($collection->getName(), array('route' => 'frontend_collection',
                    'routeParameters' => array('slug' => $collection->getId())
                ));
            }
        }

    }

    private function buildDomainFeatureMenu(ItemInterface $menu, $collections)
    {

        if ($collections->count() > 0) {

            $menu
                ->addChild('collection');

            foreach ($collections as $collection) {
                /** @var Branding $brand */
                $menu['collection']->addChild($collection->getName(), array('route' => 'frontend_collection',
                    'routeParameters' => array('slug' => $collection->getId())
                ));
            }
        }

    }

    public function manageApplicationSettingMenu($menu)
    {
        $menu
            ->addChild('Application Setting')
            ->setAttribute('icon', 'fa fa-cog')
            ->setAttribute('dropdown', true);
        $menu['Application Setting']->addChild('Account Head', array('route' => 'accounthead'))->setAttribute('icon', 'icon-th-list');
        $menu['Application Setting']->addChild('Transaction Method', array('route' => 'transactionmethod_new'))->setAttribute('icon', 'icon-th-list');
        $menu['Application Setting']->addChild('Color', array('route' => 'color'))->setAttribute('icon', 'icon-th-list');
        $menu['Application Setting']->addChild('Size', array('route' => 'size'))->setAttribute('icon', 'icon-th-list');
        $menu['Application Setting']->addChild('Hospital Category', array('route' => 'hms_category'))->setAttribute('icon', 'icon-th-list');
        return $menu;

    }

    private function buildChildMenus(ItemInterface $menu, $categories)
    {

        foreach ($categories as $category) {

            /** var Category $category */
            $categoryName = $category['name'];

            if (!empty($categoryName)) {

                $menu
                    ->addChild($categoryName, array('route' => 'frontend_category',
                        'routeParameters' => array('slug' => $category['slug'])
                    ))
                    ->setAttribute('icon', 'fa fa-angle-right');

                if (!empty($category['__children'])) {
                    $menu->setAttribute('dropdown', true);
                    $menu[$categoryName]->setChildrenAttribute('class', 'dropdown-menu');
                    $this->buildChildMenus($menu[$categoryName], $category['__children']);
                }
            }
        }
    }

    private function buildBrandMenu(ItemInterface $menu, $brands)
    {
        $menu
            ->addChild('brands')
            ->setAttribute('brands', true)
            ->setAttribute('class', 'col-md-12 nav-brands');
        foreach ($brands as $brand) {
            /** @var Branding $brand */
            $menu['brands']->addChild($brand->getName(), array('route' => 'frontend_brand',
                'routeParameters' => array('slug' => $brand->getSlug())
            ))
                ->setAttribute('brand', true)
                ->setAttribute('icon', $brand->getAbsolutePath());;
        }
    }

    private function buildCollectionMenu(ItemInterface $menu, $collections)
    {

        if ($collections->count() > 0) {

            $menu
                ->addChild('collection');

            foreach ($collections as $collection) {
                /** @var Branding $brand */
                $menu['collection']->addChild($collection->getName(), array('route' => 'frontend_collection',
                    'routeParameters' => array('slug' => $collection->getSlug())
                ));
            }
        }

    }

    public function manageVendorMenu($menu)
    {
        $securityContext = $this->container->get('security.context');

        $menu
            ->addChild('Manage Vendor')
            ->setAttribute('icon', 'fa fa-bookmark')
            ->setAttribute('dropdown', true);
        if ($securityContext->isGranted('ROLE_SUPER_ADMIN')) {
            $menu['Manage Vendor']->addChild('Vendor', array('route' => 'vendor_user'));
            $menu['Manage Vendor']->addChild('Education', array('route' => 'education'));
            $menu['Manage Vendor']->addChild('Scholarship', array('route' => 'scholarship'));
        }
        return $menu;
    }

    protected function getCategoryList()
    {
        $repo = $this->container->get('doctrine')->getRepository('ProductProductBundle:Category');
        $options = array(
            'decorate' => false,
            'representationField' => 'slug',
            'html' => false
        );

        return $repo->childrenHierarchy(
            null, /* starting from root nodes */
            false, /* true: load all children, false: only direct */
            $options
        );
    }

}
