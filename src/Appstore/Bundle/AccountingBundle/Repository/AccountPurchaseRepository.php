<?php

namespace Appstore\Bundle\AccountingBundle\Repository;
use Appstore\Bundle\AccountingBundle\Entity\AccountJournal;
use Appstore\Bundle\AccountingBundle\Entity\AccountPurchase;
use Appstore\Bundle\DmsBundle\Entity\DmsPurchase;
use Appstore\Bundle\HospitalBundle\Entity\HmsPurchase;
use Appstore\Bundle\InventoryBundle\Entity\Purchase;
use Appstore\Bundle\InventoryBundle\Entity\PurchaseReturn;
use Appstore\Bundle\MedicineBundle\Entity\MedicinePurchaseReturn;
use Doctrine\ORM\EntityRepository;
use Appstore\Bundle\MedicineBundle\Entity\MedicinePurchase;


/**
 * AccountPurchaseRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AccountPurchaseRepository extends EntityRepository
{

    public function findWithSearch($globalOption,$data = '')
    {
        $qb = $this->createQueryBuilder('e');
        $qb->where("e.globalOption = :globalOption");
        $qb->setParameter('globalOption', $globalOption);
        $this->handleSearchBetween($qb,$data);
        $qb->orderBy('e.updated','DESC');
        $result = $qb->getQuery();
        return $result;

    }

    public function updateVendorBalance(AccountPurchase $accountPurchase){

        $qb = $this->createQueryBuilder('e');
        $qb->select('SUM(e.purchaseAmount) AS purchaseAmount, SUM(e.payment) AS payment');
        $qb->where("e.globalOption = :globalOption");
        $qb->setParameter('globalOption', $accountPurchase->getGlobalOption()->getId());
        $qb->andWhere("e.process = 'approved'");
        if(!empty($accountPurchase->getVendor())){
            $vendor = $accountPurchase->getVendor()->getId();
            $qb->andWhere("e.vendor = :vendor")->setParameter('vendor', $vendor);
        }elseif(!empty($accountPurchase->getHmsVendor())){
            $vendor = $accountPurchase->getHmsVendor()->getId();
            $qb->andWhere("e.hmsVendor = :vendor")->setParameter('vendor', $vendor);
        }elseif(!empty($accountPurchase->getMedicineVendor())){
            $vendor = $accountPurchase->getMedicineVendor()->getId();
            $qb->andWhere("e.medicineVendor = :vendor")->setParameter('vendor', $vendor);
        }
        $result = $qb->getQuery()->getSingleResult();
        $balance = ($result['purchaseAmount'] -  $result['payment']);
        $accountPurchase->setBalance($balance);
        $this->_em->flush();
        return $accountPurchase;

    }


    public function accountPurchaseOverview($globalOption,$data)
    {

        $qb = $this->createQueryBuilder('e');
        $qb->select('SUM(e.purchaseAmount) AS purchaseAmount, SUM(e.payment) AS payment');
        $qb->where("e.globalOption = :globalOption");
        $qb->setParameter('globalOption', $globalOption);
        $qb->andWhere("e.process = :process");
        $qb->setParameter('process', 'approved');
        $this->handleSearchBetween($qb,$data);
        $result = $qb->getQuery()->getOneOrNullResult();
        $data =  array('purchaseAmount'=> $result['purchaseAmount'],'payment'=> $result['payment']);
        return $data;

    }

    public function vendorOutstanding($globalOption,$head)
    {

        $qb = $this->createQueryBuilder('e');
        $qb->select('SUM(e.purchaseAmount) AS purchaseAmount, SUM(e.payment) AS payment');
        $qb->where("e.globalOption = :globalOption");
        $qb->setParameter('globalOption', $globalOption);
        $qb->andWhere("e.process = :process");
        $qb->setParameter('process', 'approved');
        $qb->andWhere("e.processHead = :head");
        $qb->setParameter('head', $head);
        if($head == 'inventory'){
            $qb->join('e.vendor','iv');
            $qb->addSelect('iv.companyName as vendorName');
            $qb->groupBy('e.vendor');
        }
        if($head == 'medicine'){
            $qb->join('e.medicineVendor','iv');
            $qb->addSelect('iv.companyName as vendorName');
            $qb->groupBy('e.medicineVendor');
        }
        $result = $qb->getQuery()->getArrayResult();
        return $result;

    }

    /**
     * @param $qb
     * @param $data
     */

    protected function handleSearchBetween($qb,$data)
    {
        if(empty($data))
        {
                $datetime = new \DateTime("now");
                $startDate = $datetime->format('Y-m-d 00:00:00');
                $endDate = $datetime->format('Y-m-d 23:59:59');

                /*
                $qb->andWhere("e.updated >= :startDate");
                $qb->setParameter('startDate', $startDate);
                $qb->andWhere("e.updated <= :endDate");
                $qb->setParameter('endDate', $endDate);*/

        }else{

                $grn = isset($data['grn'])  ? $data['grn'] : '';
                $startDate = isset($data['startDate'])  ? $data['startDate'] : '';
                $endDate =   isset($data['endDate'])  ? $data['endDate'] : '';
                $inventoryVendor =    isset($data['vendor'])? $data['vendor'] :'';
                $hmsVendor =    isset($data['hmsVendor'])? $data['hmsVendor'] :'';
                $medicineVendor =    isset($data['medicineVendor'])? $data['medicineVendor'] :'';
                $transactionMethod =    isset($data['transactionMethod'])? $data['transactionMethod'] :'';

                if (!empty($data['startDate']) and !empty($data['endDate']) ) {

                    $qb->andWhere("e.updated >= :startDate");
                    $qb->setParameter('startDate', $startDate.' 00:00:00');
                }
                if (!empty($data['endDate']) and !empty($data['startDate'])) {
                    $qb->andWhere("e.updated <= :endDate");
                    $qb->setParameter('endDate', $endDate.' 00:00:00');
                }
               if (!empty($inventoryVendor)) {
                    $qb->leftJoin('e.vendor','v');
                    $qb->andWhere("v.companyName = :vendor");
                    $qb->setParameter('vendor', $inventoryVendor);
                }

                if (!empty($hmsVendor)) {
                    $qb->leftJoin('e.hmsVendor','v');
                    $qb->andWhere("v.companyName = :vendor");
                    $qb->setParameter('vendor', $hmsVendor);
                }

                if (!empty($medicineVendor)) {
                    $qb->leftJoin('e.medicineVendor','v');
                    $qb->andWhere("v.companyName = :vendor");
                    $qb->setParameter('vendor', $medicineVendor);
                }

                if (!empty($transactionMethod)) {
                    $qb->andWhere("e.transactionMethod = :transactionMethod");
                    $qb->setParameter('transactionMethod', $transactionMethod);
                }

                if (!empty($grn)) {
                    $qb->leftJoin("e.purchase",'p');
                    $qb->andWhere("p.grn = :grn");
                    $qb->setParameter('grn', $grn);
                }
        }

    }

    public function lastInsertPurchase($globalOption,$vendor)
    {
        $em = $this->_em;
        $entity = $em->getRepository('AccountingBundle:AccountPurchase')->findOneBy(
            array('globalOption' => $globalOption,'trader' => $vendor,'process'=>'approved'),
            array('id' => 'DESC')
        );

        if (empty($entity)) {
            return 0;
        }
        return $entity->getBalance();
    }

    public function lastInsertHmsPurchase($globalOption,$vendor)
    {
        $em = $this->_em;
        $entity = $em->getRepository('AccountingBundle:AccountPurchase')->findOneBy(
            array('globalOption' => $globalOption,'hmsVendor' => $vendor,'process'=>'approved'),
            array('id' => 'DESC')
        );

        if (empty($entity)) {
            return 0;
        }
        return $entity->getBalance();
    }

    public function insertAccountPurchase(Purchase $entity)
    {

        $em = $this->_em;
        $accountPurchase = new AccountPurchase();
        $accountPurchase->setGlobalOption($entity->getInventoryConfig()->getGlobalOption());
        $accountPurchase->setPurchase($entity);
        $accountPurchase->setVendor($entity->getVendor());
        $accountPurchase->setTransactionMethod($entity->getTransactionMethod());
        $accountPurchase->setPurchaseAmount($entity->getTotalAmount());
        $accountPurchase->setPayment($entity->getPaymentAmount());
        $accountPurchase->setProcessHead('inventory');
        $accountPurchase->setProcessType('Purchase');
        $accountPurchase->setReceiveDate($entity->getReceiveDate());
        $accountPurchase->setProcess('approved');
        $accountPurchase->setApprovedBy($entity->getApprovedBy());
        $em->persist($accountPurchase);
        $em->flush();
        $this->updateVendorBalance($accountPurchase);
        $this->_em->getRepository('AccountingBundle:AccountCash')->insertPurchaseCash($accountPurchase);
        return $accountPurchase;

    }

    public function insertDmsAccountPurchase(DmsPurchase $entity)
    {

        $data = array('dmsVendor' => $entity->getDmsVendor()->getCompanyName());
        $result = $this->accountPurchaseOverview($entity->getDmsConfig()->getGlobalOption(),$data);
        $balance = ( $result['purchaseAmount'] - $result['payment']);

        $em = $this->_em;
        $accountPurchase = new AccountPurchase();
        $accountPurchase->setGlobalOption($entity->getDmsConfig()->getGlobalOption());
        $accountPurchase->setDmsPurchase($entity);
        $accountPurchase->setDmsVendor($entity->getDmsVendor());
        $accountPurchase->setTransactionMethod($entity->getTransactionMethod());
        $accountPurchase->setPurchaseAmount($entity->getNetTotal());
        $accountPurchase->setPayment($entity->getPayment());
        $accountPurchase->setProcessHead('dms');
        $accountPurchase->setProcessType('Purchase');
        $accountPurchase->setReceiveDate($entity->getReceiveDate());
        $accountPurchase->setBalance(($balance + $entity->getNetTotal()) - $accountPurchase->getPayment() );
        $accountPurchase->setProcess('approved');
        $accountPurchase->setApprovedBy($entity->getApprovedBy());
        $em->persist($accountPurchase);
        $em->flush();
        if($accountPurchase->getPayment() > 0 ){
            $this->_em->getRepository('AccountingBundle:AccountCash')->insertPurchaseCash($accountPurchase);
        }
        return $accountPurchase;

    }

    public function insertMedicineAccountPurchase(MedicinePurchase $entity)
    {

        $global = $entity->getMedicineConfig()->getGlobalOption();
        $em = $this->_em;
        $accountPurchase = new AccountPurchase();
        $accountPurchase->setGlobalOption($global);
        $accountPurchase->setMedicinePurchase($entity);
        $accountPurchase->setMedicineVendor($entity->getMedicineVendor());
        $accountPurchase->setTransactionMethod($entity->getTransactionMethod());
        $accountPurchase->setPurchaseAmount($entity->getNetTotal());
        $accountPurchase->setPayment($entity->getPayment());
        $accountPurchase->setProcessHead('medicine');
        $accountPurchase->setProcessType('Purchase');
        $accountPurchase->setReceiveDate($entity->getReceiveDate());
        $accountPurchase->setProcess('approved');
        $accountPurchase->setApprovedBy($entity->getApprovedBy());
        $em->persist($accountPurchase);
        $em->flush();
        $this->updateVendorBalance($accountPurchase);
        if($accountPurchase->getPayment() > 0 ){
            $this->_em->getRepository('AccountingBundle:AccountCash')->insertPurchaseCash($accountPurchase);
        }
        return $accountPurchase;

    }

    public function insertMedicineAccountPurchaseReturn(MedicinePurchaseReturn $entity)
    {
        $global = $entity->getMedicineConfig()->getGlobalOption();
        $em = $this->_em;
        $accountPurchase = new AccountPurchase();
        $accountPurchase->setGlobalOption($global);
        $accountPurchase->setMedicineVendor($entity->getMedicineVendor());
        $accountPurchase->setPayment($entity->getSubTotal());
        $accountPurchase->setSourceInvoice('Pr-'.$entity->getInvoice());
        $accountPurchase->setProcess('medicine');
        $accountPurchase->setProcessType('Purchase-return');
        $accountPurchase->setProcess('approved');
        $accountPurchase->setApprovedBy($entity->getApprovedBy());
        $em->persist($accountPurchase);
        $em->flush();
        $this->updateVendorBalance($accountPurchase);
        return $accountPurchase;

    }

    public function insertHmsAccountPurchase(HmsPurchase $entity)
    {

        $em = $this->_em;
        $accountPurchase = new AccountPurchase();
        $accountPurchase->setGlobalOption($entity->getHospitalConfig()->getGlobalOption());
        $accountPurchase->setHmsPurchase($entity);
        $accountPurchase->setHmsVendor($entity->getVendor());
        $accountPurchase->setTransactionMethod($entity->getTransactionMethod());
        $accountPurchase->setPurchaseAmount($entity->getNetTotal());
        $accountPurchase->setPayment($entity->getPayment());
        $accountPurchase->setProcess('hms');
        $accountPurchase->setProcessType('Purchase');
        $accountPurchase->setReceiveDate($entity->getReceiveDate());
        $accountPurchase->setProcess('approved');
        $accountPurchase->setApprovedBy($entity->getApprovedBy());
        $em->persist($accountPurchase);
        $em->flush();
        if($accountPurchase->getPayment() > 0 ){
            $this->_em->getRepository('AccountingBundle:AccountCash')->insertPurchaseCash($accountPurchase);
        }
        return $accountPurchase;

    }

    public function insertRestaurantAccountPurchase(\Appstore\Bundle\RestaurantBundle\Entity\Purchase $entity)
    {


        $em = $this->_em;
        $accountPurchase = new AccountPurchase();
        $accountPurchase->setGlobalOption($entity->getRestaurantConfig()->getGlobalOption());
        $accountPurchase->setRestaurantPurchase($entity);
        $accountPurchase->setRestaurantVendor($entity->getVendor());
        $accountPurchase->setTransactionMethod($entity->getTransactionMethod());
        $accountPurchase->setPurchaseAmount($entity->getNetTotal());
        $accountPurchase->setPayment($entity->getPayment());
        $accountPurchase->setProcess('restaurant');
        $accountPurchase->setProcessType('Purchase');
        $accountPurchase->setReceiveDate($entity->getReceiveDate());
        $accountPurchase->setProcess('approved');
        $accountPurchase->setApprovedBy($entity->getApprovedBy());
        $em->persist($accountPurchase);
        $em->flush();
        if($accountPurchase->getPayment() > 0 ){
            $this->_em->getRepository('AccountingBundle:AccountCash')->insertPurchaseCash($accountPurchase);
        }
        return $accountPurchase;

    }


    public function removeApprovedAccountPurchase(Purchase $purchase)
    {

        $accountPurchase = $purchase->getAccountPurchase();
        if(!empty($accountPurchase)) {
            $accountCash = $this->_em->getRepository('AccountingBundle:AccountCash')->findOneBy(array('processHead' => 'Purchase', 'globalOption' => $accountPurchase->getGlobalOption(), 'accountRefNo' => $accountPurchase->getAccountRefNo()));
            if ($accountCash) {
                $this->_em->remove($accountCash);
                $this->_em->flush();
            }
            $transactions = $this->_em->getRepository('AccountingBundle:Transaction')->findBy(array('processHead' => 'Purchase', 'globalOption' => $accountPurchase->getGlobalOption(), 'accountRefNo' => $accountPurchase->getAccountRefNo()));
            foreach ($transactions as $transaction) {
                if ($transaction) {
                    $this->_em->remove($transaction);
                    $this->_em->flush();
                }
            }

        }

    }

    public function accountPurchaseReverse(Purchase $entity)
    {
        $em = $this->_em;
        if(!empty($entity->getAccountPurchase())){
            /* @var AccountPurchase $purchase */
            foreach ($entity->getAccountPurchase() as $purchase ){
                $globalOption = $purchase->getGlobalOption()->getId();
                $accountRefNo = $purchase->getAccountRefNo();
                $transaction = $em->createQuery("DELETE AccountingBundle:Transaction e WHERE e.globalOption = ".$globalOption ." AND e.accountRefNo =".$accountRefNo." AND e.processHead = 'Purchase'");
                $transaction->execute();
                $accountCash = $em->createQuery("DELETE AccountingBundle:AccountCash e WHERE e.globalOption = ".$globalOption ." AND e.accountRefNo =".$accountRefNo." AND e.processHead = 'Purchase'");
                $accountCash->execute();
            }
        }
        $accountCash = $em->createQuery('DELETE AccountingBundle:AccountPurchase e WHERE e.purchase = '.$entity->getId());
        if(!empty($accountCash)){
            $accountCash->execute();
        }
    }

    public function accountMedicinePurchaseReverse(MedicinePurchase $entity)
    {
        $em = $this->_em;
        if(!empty($entity->getAccountPurchase())){
            /* @var AccountPurchase $purchase */
            foreach ($entity->getAccountPurchase() as $purchase ){
                $globalOption = $purchase->getGlobalOption()->getId();
                $accountRefNo = $purchase->getAccountRefNo();
                $transaction = $em->createQuery("DELETE AccountingBundle:Transaction e WHERE e.globalOption = ".$globalOption ." AND e.accountRefNo =".$accountRefNo." AND e.processHead = 'Purchase'");
                $transaction->execute();
                $accountCash = $em->createQuery("DELETE AccountingBundle:AccountCash e WHERE e.globalOption = ".$globalOption ." AND e.accountRefNo =".$accountRefNo." AND e.processHead = 'Purchase'");
                $accountCash->execute();
            }
        }
        $accountCash = $em->createQuery('DELETE AccountingBundle:AccountPurchase e WHERE e.medicinePurchase = '.$entity->getId());
        if(!empty($accountCash)){
            $accountCash->execute();
        }
    }

}
