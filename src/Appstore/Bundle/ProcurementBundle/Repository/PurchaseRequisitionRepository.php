<?php
namespace Appstore\Bundle\ProcurementBundle\Repository;
use Appstore\Bundle\AccountingBundle\Entity\Transaction;
use Appstore\Bundle\InventoryBundle\Entity\InventoryConfig;
use Appstore\Bundle\InventoryBundle\Entity\Purchase;
use Appstore\Bundle\InventoryBundle\Entity\PurchaseVendorItem;
use Appstore\Bundle\ProcurementBundle\Entity\PurchaseRequisition;
use Doctrine\ORM\EntityRepository;

/**
 * PurchaseRequisitionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PurchaseRequisitionRepository extends EntityRepository
{

    public function findWithSearch($inventory,$data)
    {

        $receiveDate = isset($data['receiveDate']) ? $data['receiveDate'] :'';
        $memo = isset($data['memo'])? $data['memo'] :'';
        $grn = isset($data['grn'])? $data['grn'] :'';
        $vendor = isset($data['vendor'])? $data['vendor'] :'';
        $qb = $this->createQueryBuilder('purchase');
        $qb->where("purchase.globalOption = :inventory");
        $qb->setParameter('inventory', $inventory);
        if (!empty($receiveDate)) {
            $compareTo = new \DateTime($receiveDate);
            $receiveDate =  $compareTo->format('Y-m-d');
            $qb->andWhere("purchase.receiveDate LIKE :receiveDate");
            $qb->setParameter('receiveDate', $receiveDate.'%');
        }
        if (!empty($memo)) {
            $qb->andWhere("purchase.memo = :memo");
            $qb->setParameter('memo', $memo);
        }
        if (!empty($grn)) {
            $qb->andWhere("purchase.grn LIKE :grn");
            $qb->setParameter('grn', $grn.'%');
        }
        if (!empty($vendor)) {
            $qb->join('purchase.vendor', 'v');
            $qb->andWhere("v.companyName = :companyName");
            $qb->setParameter('companyName', $vendor);
        }
        $qb->orderBy('purchase.updated','DESC');
        $qb->getQuery();
        return  $qb;



    }

    public function purchaseOverview($inventory,$data)
    {
        $receiveDate = isset($data['receiveDate'])? $data['receiveDate'] :'';
        $memo = isset($data['memo'])? $data['memo'] :'';
        $grn = isset($data['grn'])? $data['grn'] :'';
        $vendor = isset($data['vendor'])? $data['vendor'] :'';
        $qb = $this->createQueryBuilder('purchase');
        $qb->select('SUM(purchase.totalQnt) AS quantity ');
        $qb->addSelect('SUM(purchase.totalAmount) AS total ');
        $qb->addSelect('SUM(purchase.paymentAmount) AS payment');
        $qb->addSelect('SUM(purchase.dueAmount) AS due');
        $qb->addSelect('SUM(purchase.commissionAmount) AS discount');
        $qb->where("purchase.globalOption = :inventory");
        $qb->andWhere("purchase.process = 'approved'");
        $qb->setParameter('inventory', $inventory);
        if (!empty($receiveDate)) {
            $compareTo = new \DateTime($receiveDate);
            $receiveDate =  $compareTo->format('Y-m-d');
            $qb->andWhere("purchase.receiveDate LIKE :receiveDate");
            $qb->setParameter('receiveDate', $receiveDate.'%');

        }
        if (!empty($memo)) {
            $qb->andWhere("purchase.memo = :memo");
            $qb->setParameter('memo', $memo);
        }
        if (!empty($grn)) {
            $qb->andWhere("purchase.grn LIKE :grn");
            $qb->setParameter('grn', $grn.'%');
        }
        if (!empty($vendor)) {
            $qb->join('purchase.vendor', 'v');
            $qb->andWhere("v.companyName = :companyName");
            $qb->setParameter('companyName', $vendor);
        }

        $data = $qb->getQuery()->getSingleResult();
        return $data;

    }

    public  function getSumPurchase($user,$inventory){

        $qb = $this->createQueryBuilder('p');
        $qb->join('p.purchaseVendorItems', 'pvi');
        $qb->select('p.id as id');
        $qb->addSelect('SUM(pvi.quantity) AS quantity ');
        $qb->addSelect('COUNT(pvi.id) AS item ');
        $qb->addSelect('SUM(pvi.quantity * pvi.purchasePrice) AS total');
        $qb->where("p.process = 'imported'");
        $qb->andWhere("p.inventoryConfig = :inventory");
        $qb->setParameter('inventory', $inventory);
        $qb->groupBy('p.id');
        $result = $qb->getQuery()->getResult();
        foreach ($result as $row ){

            $entity = $this->find($row['id']);
            $entity->setApprovedBy($user);
            $entity->setTotalQnt($row['quantity']);
            $entity->setTotalItem($row['item']);
            $entity->setTotalAmount($row['total']);
            $entity->setPaymentAmount($row['total']);
            $entity->setTransactionMethod($this->_em->getRepository('SettingToolBundle:TransactionMethod')->find(1));
            $this->_em->persist($entity);

        }

        $this->_em->flush();

        if(!empty($result)){
            return 'imported';
        }
        return false;

   }

    public  function purchaseSimpleUpdate(PurchaseRequisition $purchase){

        $qb = $this->createQueryBuilder('p');
        $qb->join('p.purchaseItems', 'pvi');
        $qb->select('p.id as id');
        $qb->addSelect('SUM(pvi.quantity) AS quantity ');
        $qb->addSelect('COUNT(pvi.id) AS item ');
        $qb->addSelect('SUM(pvi.purchaseSubTotal) AS total');
        $qb->where("p.id = :purchaseId");
        $qb->setParameter('purchaseId', $purchase);
        $row = $qb->getQuery()->getOneOrNullResult();

        $purchase->setTotalQnt($row['quantity']);
        $purchase->setTotalItem($row['item']);
        $purchase->setTotalAmount($row['total']);
        $purchase->setPaymentAmount($row['total']);
        $this->_em->persist($purchase);
        $this->_em->flush($purchase);

    }


    public  function purchaseModifyUpdate(PurchaseRequisition $purchase){

        $qb = $this->createQueryBuilder('p');
        $qb->join('p.purchaseItems', 'pvi');
        $qb->select('p.id as id');
        $qb->addSelect('SUM(pvi.quantity) AS quantity ');
        $qb->addSelect('COUNT(pvi.id) AS item ');
        $qb->addSelect('SUM(pvi.quantity * pvi.purchasePrice) AS total');
        $qb->where("p.id = :purchaseId");
        $qb->setParameter('purchaseId', $purchase);
        $qb->groupBy('p.id');
        $row = $qb->getQuery()->getOneOrNullResult();

        $purchase->setTotalQnt($row['quantity']);
        $purchase->setTotalItem($row['item']);
        $purchase->setTotalAmount($row['total']);
        $purchase->setPaymentAmount($row['total']);
        $this->_em->persist($purchase);
        $this->_em->flush($purchase);
    }



    public  function getPurchaseCount($inventory)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('count(e.id)');
        $qb->from('InventoryBundle:Purchase','e');
        $qb->where("e.inventoryConfig = :inventory");
        $qb->setParameter('inventory', $inventory->getId());
        $count = $qb->getQuery()->getSingleScalarResult();
        if($count > 0 ){
            return $count+1;
        }else{
            return 1;
        }
        return $code;
    }

    public function updateProcess($purchase,$process = 'complete')
    {
        $purchase->setProcess($process);
        $this->_em->persist($purchase);
        $this->_em->flush($purchase);
    }

    public function searchAutoComplete(InventoryConfig $inventory,$q)
    {

        $search = strtolower($q);
        $query = $this->createQueryBuilder('i');
        $query->select('i.id as id');
        $query->addSelect('i.grn as name');
        $query->addSelect('i.grn as text');
        $query->where("i.id = :inventory");
        $query->setParameter('inventory', $inventory->getId());
        $query->andWhere($query->expr()->like("i.grn", "'%$search%'"  ));
        $query->groupBy('i.id');
        $query->orderBy('i.updated', 'DESC');
        $query->setMaxResults( '30' );
        return $query->getQuery()->getResult();

    }
}
