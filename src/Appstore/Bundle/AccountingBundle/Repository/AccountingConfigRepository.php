<?php

namespace Appstore\Bundle\AccountingBundle\Repository;
use Appstore\Bundle\AccountingBundle\Entity\AccountBank;
use Appstore\Bundle\AccountingBundle\Entity\AccountJournal;
use Appstore\Bundle\AccountingBundle\Entity\AccountPurchase;
use Appstore\Bundle\InventoryBundle\Entity\Sales;
use Appstore\Bundle\InventoryBundle\Entity\SalesReturn;
use Doctrine\ORM\EntityRepository;
use Proxies\__CG__\Appstore\Bundle\DomainUserBundle\Entity\PaymentSalary;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * AccountingConfigRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AccountingConfigRepository extends EntityRepository
{

    public function accountingReset(GlobalOption $option){

        $em = $this->_em;
        $option = $option->getId();

        $transaction = $em->createQuery('DELETE AccountingBundle:Transaction e WHERE e.globalOption = '.$option);
        $transaction->execute();

        $accountCash = $em->createQuery('DELETE AccountingBundle:AccountCash e WHERE e.globalOption = '.$option);
        $accountCash->execute();

        $AccountJournal = $em->createQuery('DELETE AccountingBundle:AccountJournal e WHERE e.globalOption = '.$option);
        $AccountJournal->execute();

        $AccountPurchase = $em->createQuery('DELETE AccountingBundle:AccountPurchase e WHERE e.globalOption = '.$option);
        $AccountPurchase->execute();

        $AccountPurchaseReturn = $em->createQuery('DELETE AccountingBundle:AccountPurchaseReturn e WHERE e.globalOption = '.$option);
        $AccountPurchaseReturn->execute();

        $AccountSales = $em->createQuery('DELETE AccountingBundle:AccountSales e WHERE e.globalOption = '.$option);
        $AccountSales->execute();

        $AccountSalesReturn = $em->createQuery('DELETE AccountingBundle:AccountSalesReturn e WHERE e.globalOption = '.$option);
        $AccountSalesReturn->execute();

        $PaymentSalary = $em->createQuery('DELETE AccountingBundle:PaymentSalary e WHERE e.globalOption = '.$option);
        $PaymentSalary->execute();

        $SalarySetting = $em->createQuery('DELETE AccountingBundle:SalarySetting e WHERE e.globalOption = '.$option);
        $SalarySetting->execute();

        $Expenditure = $em->createQuery('DELETE AccountingBundle:Expenditure e WHERE e.globalOption = '.$option);
        $Expenditure->execute();

        $AccountMobileBank = $em->createQuery('DELETE AccountingBundle:AccountMobileBank e WHERE e.globalOption = '.$option);
        $AccountMobileBank->execute();

        $AccountBank = $em->createQuery('DELETE AccountingBundle:AccountBank e WHERE e.globalOption = '.$option);
        $AccountBank->execute();



    }
}
