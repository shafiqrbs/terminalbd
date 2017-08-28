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
        $securityContext = $this->container->get('security.context');
        $user = $securityContext->getToken()->getUser();
        $globalOption = $securityContext->getToken()->getUser()->getGlobalOption();

        $menu = $factory->createItem('root');
        $menu->setChildrenAttribute('class', 'page-sidebar-menu');
        $menu = $this->dashboardMenu($menu);
        if ($securityContext->isGranted('ROLE_SUPER_ADMIN')) {

            $menu = $this->toolsMenu($menu);
            // $menu = $this->syndicateMenu($menu);
            $menu = $this->productCategoryMenu($menu);
            $menu = $this->manageFrontendMenu($menu);
            $menu = $this->manageVendorMenu($menu);
            $menu = $this->manageAdvertismentMenu($menu);
            $menu = $this->manageApplicationSettingMenu($menu);
            $menu = $this->manageDomainMenu($menu);
            $menu = $this->manageSystemAccountMenu($menu);
            $menu = $this->PayrollMenu($menu);
            $menu = $this->manageCustomerOrderMenu($menu);


        } else {

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

            $result = array_intersect($menuName, array('Accounting'));
            if (!empty($result)) {
                if ($securityContext->isGranted('ROLE_ACCOUNTING')){
                    $menu = $this->AccountingMenu($menu);
                }
            }

            $result = array_intersect($menuName, array('Inventory'));
            if (!empty($result)) {
                if ($securityContext->isGranted('ROLE_INVENTORY')){
                    $menu = $this->InventoryMenu($menu);
                }
            }

            $result = array_intersect($menuName, array('Ecommerce'));
            if (!empty($result)) {
                if ($securityContext->isGranted('ROLE_ECOMMERCE')){
                    $menu = $this->EcommerceMenu($menu);
                }
            }

            $result = array_intersect($menuName, array('Payroll'));
            if (!empty($result)) {
                if ($securityContext->isGranted('ROLE_HR') || $securityContext->isGranted('ROLE_PAYROLL')){
                    $menu = $this->PayrollMenu($menu);
                }
            }

            $result = array_intersect($menuName, array('Website'));
            if (!empty($result)) {
                if ($securityContext->isGranted('ROLE_WEBSITE')){
                    $menu = $this->WebsiteMenu($menu,$menuName);
                }
            }

            $result = array_intersect($menuName, array('Hospital'));
            if (!empty($result)) {
               if ($securityContext->isGranted('ROLE_DOMAIN')){
                    $menu = $this->HospitalSalesMenu($menu);
               }
            }

            $result = array_intersect($menuName, array('FoodProduct'));
            if (!empty($result)) {
                if ($securityContext->isGranted('ROLE_DOMAIN')){
                    $menu = $this->FoodProductMenu($menu);
                }
            }

            $applications = array('accounting', 'e-commerce', 'inventory');
            $result = array_intersect($arrSlugs, $applications);
            if (!empty($result)) {
                if ($securityContext->isGranted('ROLE_DOMAIN_INVENTORY_CONFIG')){
                    $menu = $this->applicationSettingMenu($menu);
                }
            }

            if ($securityContext->isGranted('ROLE_DOMAIN') || $securityContext->isGranted('ROLE_DOMAIN_MANAGER')) {
                $menu = $this->manageDomainInvoiceMenu($menu);
                $menu = $this->manageCustomerOrderMenu($menu);
            }

            if ($securityContext->isGranted('ROLE_CUSTOMER')) {
                $menu = $this->manageCustomerOrderMenu($menu);
            }



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

    public function manageCustomerOrderMenu($menu)
    {
        $securityContext = $this->container->get('security.context');
        $menu
            ->addChild('My Account & Transaction')
            ->setAttribute('dropdown', true);
        $menu['My Account & Transaction']->addChild('Client', array('route' => 'agentclient'))->setAttribute('icon', 'icon-user');
        $menu['My Account & Transaction']->addChild('Receive Invoice', array('route' => 'agentclient_invoice'))->setAttribute('icon', 'icon-money');
        $menu['My Account & Transaction']->addChild('Manage Inbox')->setAttribute('icon', 'icon-money')->setAttribute('dropdown', true);
        $menu['My Account & Transaction']['Manage Inbox']->addChild('Email', array('route' => 'invoicesmsemail'))->setAttribute('icon', 'icon-envelope');
        $menu['My Account & Transaction']['Manage Inbox']->addChild('SMS', array('route' => 'invoicemodule'))->setAttribute('icon', 'icon-phone');
        return $menu;
    }

    public function WebsiteMenu($menu,$menuName)
    {

        $securityContext = $this->container->get('security.context');

        if ($securityContext->isGranted('ROLE_DOMAIN_WEBSITE_MANAGER')) {

            $option = $this->container->get('security.context')->getToken()->getUser()->getGlobalOption();
            $menu
                ->addChild('Manage Content')
                ->setAttribute('icon', 'fa fa-bookmark')
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
                ->setAttribute('icon', 'fa fa-bookmark')
                ->setAttribute('dropdown', true);

            $menu['Manage Content']->addChild('Contact', array('route' => 'contactpage_modify'));
            $menu['Media']->addChild('Galleries', array('route' => 'gallery'));
        }
        if ($securityContext->isGranted('ROLE_DOMAIN_WEBSITE_SETTING')) {

            $result = array_intersect($menuName, array('e-commerce'));
            $securityContext = $this->container->get('security.context');
            $globalOption = $securityContext->getToken()->getUser()->getGlobalOption();
            $menu
                ->addChild('Manage Appearance')
                ->setAttribute('icon', 'fa fa-bookmark')
                ->setAttribute('dropdown', true);

            $menu['Manage Appearance']->addChild('Customize Template', array('route' => 'templatecustomize_edit', 'routeParameters' => array('id' => $globalOption->getId())));
            $menu['Manage Appearance']->addChild('Feature & Widget')->setAttribute('icon', 'icon-money')->setAttribute('dropdown', true);
            if (!empty($result)) {
                if ($securityContext->isGranted('ROLE_DOMAIN_ECOMMERCE_CONFIG') && $securityContext->isGranted('ROLE_ECOMMERCE')){
                    $menu['Manage Appearance']['Feature & Widget']->addChild('E-commerce Widget', array('route' => 'appearancefeaturewidget'));
                }
            }
            $menu['Manage Appearance']['Feature & Widget']->addChild('Website Widget', array('route' => 'appearancewebsitewidget'));
            $menu['Manage Appearance']['Feature & Widget']->addChild('Create Feature', array('route' => 'appearancefeature'));
            if (!empty($result)) {
                if ($securityContext->isGranted('ROLE_DOMAIN_ECOMMERCE_CONFIG') && $securityContext->isGranted('ROLE_ECOMMERCE')) {
                    $menu['Manage Appearance']->addChild('E-commerce Menu', array('route' => 'ecommercemenu'));
                }
            }
            $menu['Manage Appearance']->addChild('Menu', array('route' => 'menu_manage'));
            $menu['Manage Appearance']->addChild('Menu Grouping', array('route' => 'menugrouping'));

        }

        if ($securityContext->isGranted('ROLE_DOMAIN')) {
            $menu['Manage Appearance']->addChild('Settings', array('route' => 'globaloption_modify'));
        }
        return $menu;
    }

    public function AccountingMenu($menu)
    {
        $securityContext = $this->container->get('security.context');
        $user = $securityContext->getToken()->getUser();
        $globalOption = $user->getGlobalOption();
        $modules = $user->getGlobalOption()->getSiteSetting()->getAppModules();
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
            ->setAttribute('icon', 'fa fa-money')
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
            }
            $accounting = array('e-commerce');
            $result = array_intersect($arrSlugs, $accounting);
            if (!empty($result)) {
                $menu['Accounting']['Transaction & Report']->addChild('Income', array('route' => 'report_income'))->setAttribute('icon', 'icon-th-list');
                /* $menu['Accounting']['Transaction & Report']->addChild('Monthly Income',        array('route' => 'report_monthly_income'))->setAttribute('icon', 'icon-th-list');*/
            }
            $accounting = array('hospital');
            $result = array_intersect($arrSlugs, $accounting);
            if (!empty($result)) {
                $menu['Accounting']['Transaction & Report']->addChild('Income', array('route' => 'report_income_hospital'))->setAttribute('icon', 'icon-th-list');
                /* $menu['Accounting']['Transaction & Report']->addChild('Monthly Income',        array('route' => 'report_monthly_income'))->setAttribute('icon', 'icon-th-list');*/
            }

        }

        if ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_TRANSACTION')) {

            $menu['Accounting']->addChild('Cash', array('route' => ''))
                ->setAttribute('icon', 'fa fa-money')
                ->setAttribute('dropdown', true);
            $menu['Accounting']['Cash']->addChild('Cash Overview', array('route' => 'account_transaction_cash_overview'))->setAttribute('icon', 'icon-th-list');
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
        $accounting = array('inventory');
        $result = array_intersect($arrSlugs, $accounting);
        if (!empty($result)) {

            if ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_SALES')) {
                $menu['Accounting']->addChild('Sales', array('route' => 'account_sales'));
                $menu['Accounting']->addChild('Sales Return', array('route' => 'account_sales_return'));
            }

            if ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_PURCHASE')) {
                $menu['Accounting']->addChild('Purchase', array('route' => 'account_purchase'));
                $menu['Accounting']->addChild('Purchase Return', array('route' => 'account_purchase_return'));
            }
        }
        $accounting = array('e-commerce');
        $result = array_intersect($arrSlugs, $accounting);
        if (!empty($result) && $securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_ECOMMERCE')) {

            $menu['Accounting']->addChild('Online Order', array('route' => 'account_onlineorder'));
            $menu['Accounting']->addChild('Online Order Return', array('route' => 'account_onlineorder'));
        }

        $accounting = array('hospital');
        $result = array_intersect($arrSlugs, $accounting);
        if (!empty($result)) {
            $menu['Accounting']->addChild('Sales', array('route' => 'account_sales_hospital'))->setAttribute('icon', 'icon-th-list');
        }

        if($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_JOURNAL')){

            $menu['Accounting']->addChild('Journal', array('route' => 'account_journal'))->setAttribute('icon', 'icon-retweet');
        }


        return $menu;

    }

    public function InventoryMenu($menu)
    {

        $securityContext = $this->container->get('security.context');
        $user = $securityContext->getToken()->getUser();
        $userRoles = $user->getRoles();
        $inventory = $user->getGlobalOption()->getInventoryConfig();
        $menu
            ->addChild('Inventory')
            ->setAttribute('icon', 'icon icon-th-large')
            ->setAttribute('dropdown', true);

        if ($securityContext->isGranted('ROLE_DOMAIN_INVENTORY_SALES')) {


            $deliveryProcess = $inventory->getDeliveryProcess();
            if (!empty($deliveryProcess)) {

                if (in_array('Pos', $deliveryProcess)) {

                        if ($this->container->get('security.authorization_checker')->isGranted('ROLE_DOMAIN_INVENTORY_SALES_POS')){

                            $menu['Inventory']->addChild('Point of Sales')
                                ->setAttribute('icon', 'icon icon-truck')
                                ->setAttribute('dropdown', true);
                            $menu['Inventory']['Point of Sales']->addChild('Pos', array('route' => 'inventory_sales_new'))->setAttribute('icon', 'icon-shopping-cart');
                            $menu['Inventory']['Point of Sales']->addChild('Sales', array('route' => 'inventory_sales'))->setAttribute('icon', ' icon-th-list');
                            if ($securityContext->isGranted('ROLE_DOMAIN_INVENTORY_MANAGER') or $securityContext->isGranted('ROLE_DOMAIN_INVENTORY_BRANCH_MANAGER')) {
                                $menu['Inventory']['Point of Sales']->addChild('Sales Return', array('route' => 'inventory_salesreturn'))->setAttribute('icon', 'icon-share-alt');
                            }
                            $menu['Inventory']['Point of Sales']->addChild('Customer', array('route' => 'inventory_customer'))->setAttribute('icon', 'icon icon-user');
                            $menu['Inventory']['Point of Sales']->addChild('Sales Import', array('route' => 'inventory_salesimport'))->setAttribute('icon', 'icon-upload');
                        }
                }

                if (in_array('OnlineSales', $deliveryProcess)) {

                    if ($this->container->get('security.authorization_checker')->isGranted('ROLE_DOMAIN_INVENTORY_SALES_ONLINE')){
                        $menu['Inventory']
                            ->addChild('Online Sales')
                            ->setAttribute('icon', 'icon icon-truck')
                            ->setAttribute('dropdown', true);
                        $menu['Inventory']['Online Sales']->addChild('Customers', array('route' => 'inventory_salesonline_customer'))->setAttribute('icon', 'icon icon-user');
                        $menu['Inventory']['Online Sales']->addChild('Add Sales', array('route' => 'inventory_salesonline_new'))->setAttribute('icon', ' icon-plus');
                        $menu['Inventory']['Online Sales']->addChild('Sales', array('route' => 'inventory_salesonline'))->setAttribute('icon', ' icon-th-list');
                    }

                }

                if (in_array('GeneralSales', $deliveryProcess)) {
                    if (in_array('ROLE_DOMAIN_INVENTORY_SALES_ONLINE',$userRoles)) {
                        if ($securityContext->isGranted('ROLE_DOMAIN_INVENTORY_SALES_GENERAL')) {
                            $menu['Inventory']
                                ->addChild('General Sales')
                                ->setAttribute('icon', 'icon icon-truck')
                                ->setAttribute('dropdown', true);
                            $menu['Inventory']['General Sales']->addChild('Customers', array('route' => 'inventory_salesgeneral_customer'))->setAttribute('icon', 'icon icon-user');
                            $menu['Inventory']['General Sales']->addChild('Add Sales', array('route' => 'inventory_salesgeneral_new'))->setAttribute('icon', ' icon-plus');
                            $menu['Inventory']['General Sales']->addChild('Sales', array('route' => 'inventory_salesgeneral'))->setAttribute('icon', ' icon-th-list');
                            if ($securityContext->isGranted('ROLE_DOMAIN_INVENTORY_MANAGER') or $securityContext->isGranted('ROLE_DOMAIN_INVENTORY_BRANCH_MANAGER') ) {
                                $menu['Inventory']['General Sales']->addChild('Sales Return', array('route' => 'inventory_salesreturn'))->setAttribute('icon', 'icon-share-alt');
                            }
                        }
                    }
                }

                if (in_array('ManualSales', $deliveryProcess)) {
                    if (in_array('ROLE_DOMAIN_INVENTORY_SALES_MANUAL',$userRoles)) {
                        if ($securityContext->isGranted('ROLE_DOMAIN_INVENTORY_SALES_MANUAL')) {

                            $menu['Inventory']
                                ->addChild('Manual Sales')
                                ->setAttribute('icon', 'icon icon-truck')
                                ->setAttribute('dropdown', true);
                            $menu['Inventory']['Manual Sales']->addChild('Customers', array('route' => 'inventory_salesmanual_customer'))->setAttribute('icon', 'icon icon-user');
                            $menu['Inventory']['Manual Sales']->addChild('Add Sales', array('route' => 'inventory_salesmanual_add'))->setAttribute('icon', ' icon-plus');
                            $menu['Inventory']['Manual Sales']->addChild('Sales', array('route' => 'inventory_salesmanual'))->setAttribute('icon', ' icon-th-list');
                            if ($securityContext->isGranted('ROLE_DOMAIN_INVENTORY_MANAGER') or $securityContext->isGranted('ROLE_DOMAIN_INVENTORY_BRANCH_MANAGER')) {
                                $menu['Inventory']['Manual Sales']->addChild('Sales Return', array('route' => 'inventory_salesreturn'))->setAttribute('icon', 'icon-share-alt');
                            }
                        }
                    }
                }

                if (in_array('Order', $deliveryProcess)) {
                    if (in_array('ROLE_DOMAIN_INVENTORY_SALES_ORDER',$userRoles)) {
                        if ($securityContext->isGranted('ROLE_DOMAIN_INVENTORY_SALES_ORDER')) {
                            $menu['Inventory']
                                ->addChild('Online Order')
                                ->setAttribute('icon', 'icon icon-truck')
                                ->setAttribute('dropdown', true);
                            $menu['Inventory']['Online Order']->addChild('Online Customer', array('route' => 'inventory_customer'))->setAttribute('icon', 'icon icon-user');
                            $menu['Inventory']['Online Order']->addChild('Order', array('route' => 'inventory_sales'))->setAttribute('icon', ' icon-th-list');
                            if ($securityContext->isGranted('ROLE_DOMAIN_INVENTORY_MANAGER')) {
                                $menu['Inventory']['Online Order']->addChild('Order Return', array('route' => 'inventory_salesreturn'))->setAttribute('icon', 'icon-share-alt');
                            }
                        }
                    }
                }


            }
        }
        if ($securityContext->isGranted('ROLE_DOMAIN_INVENTORY_PURCHASE')) {

            $menu['Inventory']->addChild('Manage Purchase', array('route' => 'purchase'))
                ->setAttribute('icon', 'icon icon-shopping-cart')
                ->setAttribute('dropdown', true);
            $menu['Inventory']['Manage Purchase']->addChild('Purchase', array('route' => 'purchase'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Inventory']['Manage Purchase']->addChild('Purchase Return', array('route' => 'inventory_purchasereturn'))
                ->setAttribute('icon', ' icon-reply');
            $menu['Inventory']['Manage Purchase']->addChild('Purchase Import', array('route' => 'inventory_excelimproter'))
                ->setAttribute('icon', 'icon-upload');

        }


        if ($securityContext->isGranted('ROLE_DOMAIN_INVENTORY_STOCK')) {

            $menu['Inventory']->addChild('Manage Stock')->setAttribute('icon', 'icon icon-reorder')->setAttribute('dropdown', true);
            $menu['Inventory']['Manage Stock']->addChild('Stock Item', array('route' => 'item'))
                ->setAttribute('icon', 'icon-hdd');
            $menu['Inventory']['Manage Stock']->addChild('Barcode wise Stock', array('route' => 'inventory_barcode_branch_stock'))->setAttribute('icon', 'icon-bar-chart');
            $menu['Inventory']['Manage Stock']->addChild('Barcode Stock Details', array('route' => 'inventory_barcode_stock'))->setAttribute('icon', 'icon-bar-chart');
            $menu['Inventory']['Manage Stock']->addChild('Stock Item Details', array('route' => 'inventory_stockitem'))
                ->setAttribute('icon', 'icon-hdd');
        }
        if ($securityContext->isGranted('ROLE_DOMAIN_INVENTORY_MANAGER')) {
            if ($inventory->getBarcodePrint() == 1) {
                $menu['Inventory']['Manage Stock']->addChild('Barcode Print', array('route' => 'inventory_barcode'))
                    ->setAttribute('icon', 'icon-barcode');
            }
            $menu['Inventory']['Manage Stock']->addChild('Damage', array('route' => 'inventory_damage'))
                ->setAttribute('icon', ' icon-trash');
        }

        if ($securityContext->isGranted('ROLE_DOMAIN_INVENTORY_PURCHASE')) {
            $menu['Inventory']['Manage Stock']->addChild('Vendor Item', array('route' => 'inventory_purchasevendoritem'))
                ->setAttribute('icon', 'icon-info-sign');
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
            $menu['Inventory']['Reports']->addChild('Sales Overview', array('route' => 'inventory_report_sales_overview'))->setAttribute('icon', 'icon-bar-chart');
            $menu['Inventory']['Reports']->addChild('Periodic Sales Item', array('route' => 'inventory_report_sales_item'))->setAttribute('icon', 'icon-bar-chart');
            $menu['Inventory']['Reports']->addChild('Sales with price', array('route' => 'inventory_report_sales'))->setAttribute('icon', 'icon-bar-chart');

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
        $securityContext = $this->container->get('security.context');
        $menu
            ->addChild('E-commerce')
            ->setAttribute('icon', 'icon  icon-shopping-cart')
            ->setAttribute('dropdown', true);

        if ($securityContext->isGranted('ROLE_DOMAIN_ECOMMERCE_PRODUCT')) {

            $menu['E-commerce']->addChild('Product', array('route' => ''))
                ->setAttribute('icon', 'fa fa-bookmark')
                ->setAttribute('dropdown', true);
            $menu['E-commerce']['Product']->addChild('Add Product', array('route' => 'inventory_goods_new'))->setAttribute('icon', 'icon-th-list');
            $menu['E-commerce']['Product']->addChild('Product', array('route' => 'inventory_goods'))->setAttribute('icon', 'icon-th-list');
            $menu['E-commerce']['Product']->addChild('Promotion', array('route' => 'ecommerce_promotion'))->setAttribute('icon', 'icon-th-list');
            $menu['E-commerce']['Product']->addChild('Discount', array('route' => 'ecommerce_discount'))->setAttribute('icon', 'icon-th-list');
            $menu['E-commerce']['Product']->addChild('Coupon', array('route' => 'ecommerce_coupon'))->setAttribute('icon', 'icon-tags');
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
        if ($securityContext->isGranted('ROLE_DOMAIN_ECOMMERCE_CONFIG')) {

            /*
             $menu['E-commerce']->addChild('E-commerce Template', array('route' => ''))
                ->setAttribute('icon', 'fa fa-bookmark')
                ->setAttribute('dropdown', true);
            $menu['E-commerce']['E-commerce Template']->addChild('Home View', array('route' => 'ecommercehome'))->setAttribute('icon', 'fa fa-th-list');
            $menu['E-commerce']['E-commerce Template']->addChild('Mange Template', array('route' => 'ecommercetemplate'))->setAttribute('icon', 'fa fa-th-list');*/
            $menu['E-commerce']->addChild('Setting', array('route' => ''))
                ->setAttribute('icon', 'fa fa-bookmark')
                ->setAttribute('dropdown', true);
            $menu['E-commerce']['Setting']->addChild('E-commerce Config', array('route' => 'ecommerce_config_modify'))->setAttribute('icon', 'fa fa-cog');
            $menu['E-commerce']->addChild('Feature & Widget')->setAttribute('icon', 'icon-th-list')->setAttribute('dropdown', true);
            $menu['E-commerce']['Feature & Widget']->addChild('Feature Category', array('route' => 'featurecategory'));
            $menu['E-commerce']['Feature & Widget']->addChild('Feature Brand', array('route' => 'featurebrand'));
        }
        return $menu;
    }

    public function applicationSettingMenu($menu)
    {
        $securityContext = $this->container->get('security.context');
        $user = $securityContext->getToken()->getUser();
        $inventory = $user->getGlobalOption()->getInventoryConfig();

        $menu
            ->addChild('Application Setting')
            ->setAttribute('icon', 'icon icon-cogs')
            ->setAttribute('dropdown', true);

        $globalOption = $user->getGlobalOption();
        $modules = $user->getGlobalOption()->getSiteSetting()->getAppModules();
        $arrSlugs = array();
        if (!empty($globalOption->getSiteSetting()) and !empty($modules)) {
            foreach ($globalOption->getSiteSetting()->getAppModules() as $mod) {
                if (!empty($mod->getModuleClass())) {
                    $arrSlugs[] = $mod->getSlug();
                }
            }
        }
        $accounting = array('accounting');
        $result = array_intersect($arrSlugs, $accounting);
        if (!empty($result)) {
            if ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_CONFIG')) {

                $menu['Application Setting']->addChild('Accounting', array('route' => ''))
                    ->setAttribute('icon', 'fa fa-money')
                    ->setAttribute('dropdown', true);
                $menu['Application Setting']['Accounting']->addChild('Bank Account', array('route' => 'appsetting_bank'))->setAttribute('icon', 'fa fa-money');
                $menu['Application Setting']['Accounting']->addChild('Mobile Account', array('route' => 'appsetting_mobile_bank'))->setAttribute('icon', 'fa fa-money');
                $menu['Application Setting']['Accounting']->addChild('Account Head', array('route' => 'accounthead'))->setAttribute('icon', 'fa fa-money');

            }
        }

        $inventories = array('inventory');
        $result = array_intersect($arrSlugs, $inventories);
        if (!empty($result)) {

            if ($securityContext->isGranted('ROLE_DOMAIN_INVENTORY_PURCHASE')) {

                $menu['Application Setting']->addChild('Inventory', array('route' => ''))
                    ->setAttribute('icon', 'fa fa-bookmark')
                    ->setAttribute('dropdown', true);
                $menu['Application Setting']['Inventory']->addChild('Master Item', array('route' => 'inventory_product'))->setAttribute('icon', 'icon-th-list');
                $menu['Application Setting']['Inventory']->addChild('Item category', array('route' => 'itemtypegrouping_edit', 'routeParameters' => array('id' => $inventory->getId())))->setAttribute('icon', 'icon-th-list');
                $menu['Application Setting']['Inventory']->addChild('Custom category', array('route' => 'inventory_category'))->setAttribute('icon', 'icon-th-list');
                $menu['Application Setting']['Inventory']->addChild('Vendor', array('route' => 'inventory_vendor'))->setAttribute('icon', 'icon-th-list');
                $menu['Application Setting']['Inventory']->addChild('Brand', array('route' => 'itembrand'))->setAttribute('icon', 'icon-th-list');
                //$menu['Application Setting']['Inventory Setting']->addChild('Color', array('route' => 'itemcolor'))->setAttribute('icon', 'icon-th-list');
                //$menu['Application Setting']['Inventory Setting']->addChild('Size', array('route' => 'itemsize'))->setAttribute('icon', 'icon-th-list');
                //$menu['Application Setting']['Inventory Setting']->addChild('Ware House', array('route' => 'inventory_warehouse'))->setAttribute('icon', 'icon-th-list');
                if ($inventory->getIsBranch() == 1) {
                    $menu['Application Setting']->addChild('Branches')->setAttribute('icon', 'icon-building')->setAttribute('dropdown', true);
                    $menu['Application Setting']['Branches']->addChild('Branch Shop', array('route' => 'appsetting_branchshop'))->setAttribute('icon', 'icon-building');
                }
            }
        }

        $ecommerce = array('e-commerce');
        $result = array_intersect($arrSlugs, $ecommerce);
        $resInventory = array_intersect($arrSlugs, $inventories);
        if (!empty($result)) {

            if ($securityContext->isGranted('ROLE_DOMAIN_ECOMMERCE')) {
                $menu['Application Setting']->addChild('E-commerce', array('route' => ''))
                    ->setAttribute('icon', 'fa fa-bookmark')
                    ->setAttribute('dropdown', true);
                if (empty($resInventory)) {
                    $menu['Application Setting']['E-commerce']->addChild('Master Item', array('route' => 'inventory_product'))->setAttribute('icon', 'icon-th-list');
                    $menu['Application Setting']['E-commerce']->addChild('Item category', array('route' => 'itemtypegrouping_edit', 'routeParameters' => array('id' => $inventory->getId())))->setAttribute('icon', 'icon-th-list');
                    $menu['Application Setting']['E-commerce']->addChild('Custom category', array('route' => 'inventory_category'))->setAttribute('icon', 'icon-th-list');
                    $menu['Application Setting']['E-commerce']->addChild('Brand', array('route' => 'itembrand'))->setAttribute('icon', 'icon-th-list');
                }
                $menu['Application Setting']['E-commerce']->addChild('Item Attribute', array('route' => 'itemattribute'))->setAttribute('icon', 'icon-th-list');

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
            ->setAttribute('icon', 'icon-plus');
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

    public function HospitalSalesMenu($menu)
    {

        $securityContext = $this->container->get('security.context');
        $user = $securityContext->getToken()->getUser();
        $menu
            ->addChild('Hospital & Diagnostic')
            ->setAttribute('icon', 'icon icon-medkit')
            ->setAttribute('dropdown', true);

        $menu['Hospital & Diagnostic']->addChild('Manage Invoice')
            ->setAttribute('icon', 'icon icon-reorder')
            ->setAttribute('dropdown', true);
        $menu['Hospital & Diagnostic']['Manage Invoice']->addChild('Pathological New', array('route' => 'hms_invoice_new'))
            ->setAttribute('icon', 'icon-plus');
        $menu['Hospital & Diagnostic']['Manage Invoice']->addChild('Pathological', array('route' => 'hms_invoice'))
            ->setAttribute('icon', 'icon-th-list');
        $menu['Hospital & Diagnostic']['Manage Invoice']->addChild('Admission New', array('route' => 'hms_invoice_admission_new'))
            ->setAttribute('icon', 'icon-plus');
        $menu['Hospital & Diagnostic']['Manage Invoice']->addChild('Admission', array('route' => 'hms_invoice_admission'))
            ->setAttribute('icon', 'icon-th-list');
        $menu['Hospital & Diagnostic']['Manage Invoice']->addChild('Doctor New Commission', array('route' => 'hms_doctor_commission_invoice'))
            ->setAttribute('icon', 'icon-th-list');
        $menu['Hospital & Diagnostic']['Manage Invoice']->addChild('Doctor Invoice', array('route' => 'hms_doctor_invoice'))
            ->setAttribute('icon', 'icon-th-list');
        $menu['Hospital & Diagnostic']->addChild('Pathological Report')
            ->setAttribute('icon', 'icon icon-reorder')
            ->setAttribute('dropdown', true);
        $menu['Hospital & Diagnostic']['Pathological Report']->addChild('Pathological', array('route' => 'hms_invoice_particular'))
            ->setAttribute('icon', 'icon-plus');
        $menu['Hospital & Diagnostic']->addChild('Manage System')
            ->setAttribute('icon', 'icon icon-cogs')
            ->setAttribute('dropdown', true);
        $menu['Hospital & Diagnostic']['Manage System']->addChild('Purchase New', array('route' => 'hms_purchase_new'))
            ->setAttribute('icon', 'icon-th-list');
        $menu['Hospital & Diagnostic']['Manage System']->addChild('Purchase', array('route' => 'hms_purchase'))
            ->setAttribute('icon', 'icon-th-list');
        $menu['Hospital & Diagnostic']['Manage System']->addChild('Pathology', array('route' => 'hms_pathology'))
            ->setAttribute('icon', 'icon-th-list');
        $menu['Hospital & Diagnostic']['Manage System']->addChild('Doctor', array('route' => 'hms_doctor'))
            ->setAttribute('icon', 'icon-th-list');
        $menu['Hospital & Diagnostic']['Manage System']->addChild('Referred Doctor', array('route' => 'hms_referreddoctor'))
            ->setAttribute('icon', 'icon-th-list');
        $menu['Hospital & Diagnostic']['Manage System']->addChild('Cabin/Ward', array('route' => 'hms_cabin'))
            ->setAttribute('icon', 'icon-th-list');
        $menu['Hospital & Diagnostic']['Manage System']->addChild('Medicine', array('route' => 'hms_medicine'))
            ->setAttribute('icon', 'icon-th-list');
        $menu['Hospital & Diagnostic']['Manage System']->addChild('Surgery', array('route' => 'hms_surgery'))
            ->setAttribute('icon', 'icon-th-list');
        $menu['Hospital & Diagnostic']['Manage System']->addChild('Category', array('route' => 'hms_category'))->setAttribute('icon', 'icon-tag');
        $menu['Hospital & Diagnostic']['Manage System']->addChild('Vendor', array('route' => 'hms_vendor'))->setAttribute('icon', 'icon-tag');
        $menu['Hospital & Diagnostic']->addChild('Configuration', array('route' => 'hms_config_manage'))
            ->setAttribute('icon', 'icon-cog');

        return $menu;

    }

    public function FoodProductMenu($menu)
    {

        $securityContext = $this->container->get('security.context');
        $user = $securityContext->getToken()->getUser();
        $inventory = $user->getGlobalOption()->getInventoryConfig();

        $menu
            ->addChild('Food Product')
            ->setAttribute('icon', 'icon icon-th-large')
            ->setAttribute('dropdown', true);

        $menu['Food Product']->addChild('Manage Food Product')
            ->setAttribute('icon', 'icon icon-reorder')
            ->setAttribute('dropdown', true);
        $menu['Food Product']['Manage Food Product']->addChild('Add food product', array('route' => 'inventory_foodproduct_new'))
            ->setAttribute('icon', 'icon-plus');
        $menu['Food Product']['Manage Food Product']->addChild('Food Product', array('route' => 'inventory_foodproduct'))
            ->setAttribute('icon', 'icon-th-list');
        $menu['Food Product']->addChild('System Setting')
            ->setAttribute('icon', 'icon icon-cogs')
            ->setAttribute('dropdown', true);
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
        $menu
            ->addChild('Reservation')
            ->setAttribute('icon', 'fa fa-cog')
            ->setAttribute('dropdown', true);
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
        $securityContext = $this->container->get('security.context');
        $menu
            ->addChild('HR & Payroll')
            ->setAttribute('icon', 'fa fa-group')
            ->setAttribute('dropdown', true);
        if ($securityContext->isGranted('ROLE_HR_USER')) {

            $menu['HR & Payroll']->addChild('Human Resource')->setAttribute('icon', 'icon-group')->setAttribute('dropdown', true);
            $menu['HR & Payroll']['Human Resource']->addChild('Employee', array('route' => 'domain_user'))->setAttribute('icon', 'icon-user');
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

    public function manageSystemAccountMenu($menu)
    {
        $menu
            ->addChild('System Transaction')
            ->setAttribute('dropdown', true);
        $menu['System Transaction']->addChild('Bank', array('route' => 'bankaccount'))->setAttribute('icon', 'icon-money');
        $menu['System Transaction']->addChild('Mobile Bank', array('route' => 'mobilebankaccount'))->setAttribute('icon', 'icon-money');
        return $menu;
    }

    public function manageDomainMenu($menu)
    {
        $menu
            ->addChild('Manage Domain')
            ->setAttribute('dropdown', true);
        $menu['Manage Domain']->addChild('Setting Package')->setAttribute('icon', ' icon-cogs')->setAttribute('dropdown', true);
        $menu['Manage Domain']['Setting Package']->addChild('Application', array('route' => 'applicationpricing'))->setAttribute('icon', 'icon-briefcase');
        $menu['Manage Domain']['Setting Package']->addChild('SMS/Email', array('route' => 'smspricing'))->setAttribute('icon', 'icon-envelope');
        $menu['Manage Domain']->addChild('Manage Operation')->setAttribute('icon', 'icon-money')->setAttribute('dropdown', true);
        $menu['Manage Domain']['Manage Operation']->addChild('Domain', array('route' => 'tools_domain'))->setAttribute('icon', 'icon-money');
        $menu['Manage Domain']->addChild('Manage Invoice')->setAttribute('icon', 'icon-money')->setAttribute('dropdown', true);
        $menu['Manage Domain']['Manage Invoice']->addChild('Sms Bundle', array('route' => 'invoicesmsemail'))->setAttribute('icon', 'icon-money');
        $menu['Manage Domain']['Manage Invoice']->addChild('Module Invoice', array('route' => 'invoicemodule'))->setAttribute('icon', 'icon-money');

        return $menu;
    }

    public function manageDomainInvoiceMenu($menu)
    {
        $securityContext = $this->container->get('security.context');
        $globalOption = $securityContext->getToken()->getUser()->getGlobalOption();
        $menu
            ->addChild('Invoice Sms & Email')
            ->setAttribute('icon', 'info-sign')
            ->setAttribute('dropdown', true);
        $menu['Invoice Sms & Email']->addChild('Manage Sms')->setAttribute('icon', 'icon-money')->setAttribute('dropdown', true);
        $menu['Invoice Sms & Email']['Manage Sms']->addChild('Sms Logs', array('route' => 'smssender'))->setAttribute('icon', 'icon-phone');
        $menu['Invoice Sms & Email']['Manage Sms']->addChild('Bulk Sms', array('route' => 'smsbulk'))->setAttribute('icon', 'icon-envelope');
        $menu['Invoice Sms & Email']['Manage Sms']->addChild('Sms Bundle', array('route' => 'invoicesmsemail'))->setAttribute('icon', 'icon-money');
        $menu['Invoice Sms & Email']['Manage Sms']->addChild('Notification Setup', array('route' => 'domain_notificationconfig'))->setAttribute('icon', 'icon-info-sign');
        $menu['Invoice Sms & Email']->addChild('Invoice Application', array('route' => 'invoicemodule_domain'))->setAttribute('icon', 'icon-money');

        return $menu;
    }

    public function manageFrontendMenu($menu)
    {
        $menu
            ->addChild('Manage Frontend')
            ->setAttribute('icon', 'fa fa-bookmark')
            ->setAttribute('dropdown', true);

        $menu['Manage Frontend']->addChild('Site Slider', array('route' => 'siteslider'));
        $menu['Manage Frontend']->addChild('Site Content', array('route' => 'sitecontent'));
        $menu['Manage Frontend']->addChild('Manage Mega Menu', array('route' => 'megamenu'));
        $menu['Manage Frontend']->addChild('Feature Category', array('route' => 'category_sorting'));
        $menu['Manage Frontend']->addChild('Collection', array('route' => 'collection'));

        return $menu;

    }

    public function toolsMenu($menu)
    {
        $menu
            ->addChild('Tools')
            ->setAttribute('icon', 'fa fa-bookmark')
            ->setAttribute('dropdown', true);

        $menu['Tools']->addChild('Manage Option', array('route' => 'globaloption'));
        $menu['Tools']->addChild('Manage Setting', array('route' => 'sitesetting'));
        $menu['Tools']->addChild('Location', array('route' => 'location'));
        $menu['Tools']->addChild('Business Sector', array('route' => 'syndicate'));
        $menu['Tools']->addChild('Course', array('route' => 'course'));
        $menu['Tools']->addChild('Institute Level', array('route' => 'institutelevel'));
        $menu['Tools']->addChild('Syndicate Module', array('route' => 'syndicatemodule'));
        $menu['Tools']->addChild('Application Module', array('route' => 'appmodule'));
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
        $menu['Application Setting']->addChild('Color', array('route' => 'itemcolor'))->setAttribute('icon', 'icon-th-list');
        $menu['Application Setting']->addChild('Size', array('route' => 'itemsize'))->setAttribute('icon', 'icon-th-list');

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
