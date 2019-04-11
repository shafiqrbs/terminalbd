<?php

namespace Appstore\Bundle\BusinessBundle\Repository;
use Appstore\Bundle\AccountingBundle\Entity\AccountVendor;
use Appstore\Bundle\BusinessBundle\Entity\BusinessInvoice;
use Appstore\Bundle\BusinessBundle\Entity\BusinessInvoiceParticular;
use Appstore\Bundle\BusinessBundle\Entity\BusinessParticular;
use Appstore\Bundle\BusinessBundle\Entity\BusinessVendorStock;
use Appstore\Bundle\BusinessBundle\Entity\BusinessVendorStockItem;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;


/**
 * BusinessVendorStockItemRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BusinessVendorStockItemRepository extends EntityRepository
{


    protected function handleSearchBetween($qb,$data)
    {
        $vendorId = isset($data['vendorId'])? $data['vendorId'] :'';
        $name = isset($data['name'])? $data['name'] :'';

        if(!empty($vendorId)){
            $qb->join('e.vendor','v');
            $qb->andWhere("v.id = :vendorId")->setParameter('vendorId', $vendorId);
        }
        if(!empty($name)){
            $qb->andWhere($qb->expr()->like("p.name", "'%$name%'"  ));
        }

    }

    public function findWithSearch(User $user, $data)
    {
        $config = $user->getGlobalOption()->getBusinessConfig()->getId();
        $qb = $this->createQueryBuilder('pi');
        $qb->join('pi.particular','p');
        $qb->join('pi.businessVendorStock','e');
        $qb->select('p.name as name ,p.id as id , COALESCE(SUM(pi.quantity),0) as quantity');
        $qb->where('e.businessConfig = :config')->setParameter('config', $config) ;
        $qb->andWhere('e.process = :process')->setParameter('process', 'approved');
        $this->handleSearchBetween($qb,$data);
        $qb->groupBy('p.name');
        $qb->orderBy('p.name','ASC');
        $result =  $qb->getQuery()->getArrayResult();
        return  $result;
    }


    public function insertVendorStockItems($invoice, $data)
    {

    	$particular = $this->_em->getRepository('BusinessBundle:BusinessParticular')->find($data['particularId']);
        $em = $this->_em;
	    $price = (isset($data['price']) and !empty($data['price']))? $data['price']:0;
        $entity = new BusinessVendorStockItem();
        $entity->setBusinessVendorStock($invoice);
        $entity->setParticular($particular);
        $entity->setPrice($price);
        $entity->setQuantity($data['quantity']);
        $entity->setSubTotal($data['quantity'] * $entity->getPrice());
        $em->persist($entity);
        $em->flush();

    }


    public function getVendorStockItems(BusinessVendorStock $sales)
    {
        $entities = $sales->getBusinessVendorStockItems();
        $data = '';
        $i = 1;

        /* @var $entity BusinessVendorStockItem */

        foreach ($entities as $entity) {

	        $unit = !empty($entity->getParticular()->getUnit()) ? $entity->getParticular()->getUnit()->getName():'';
            $data .= "<tr id='remove-{$entity->getId()}'>";
            $data .= "<td>{$i}</td>";
            $data .= "<td>{$entity->getParticular()->getName()}</td>";
            $data .= "<td>{$entity->getPrice()}</td>";
            $data .= "<td>{$entity->getQuantity()}</td>";
            $data .= "<td>{$unit}</td>";
            $data .= "<td>{$entity->getSubTotal()}</td>";
            $data .= "<td><a id='{$entity->getId()}'  data-url='/business/vendor-stock/{$sales->getId()}/{$entity->getId()}/particular-delete' href='javascript:' class='btn red mini delete' ><i class='icon-trash'></i></a></td>";
            $data .= '</tr>';
            $i++;
        }
        return $data;
    }

    public function vendorStockItemUpdate(BusinessVendorStock $sales)
    {
        $entities = $sales->getBusinessVendorStockItems();

        /* @var $entity BusinessVendorStockItem */

        foreach ($entities as $entity) {

            $stockItem = $entity->getParticular();
            $qb = $this->createQueryBuilder('e');
            $qb->join('e.businessVendorStock', 'mp');
            $qb->select('SUM(e.quantity) AS quantity');
            $qb->where('e.particular = :particular')->setParameter('particular', $stockItem->getId());
            $qb->andWhere('mp.process = :process')->setParameter('process', 'Approved');
            $qnt = $qb->getQuery()->getOneOrNullResult();
            $qnt = $qnt['quantity'];
            $this->updateStockVendorStock($stockItem, $qnt);
        }

    }

    private function updateStockVendorStock(BusinessParticular $stock , $qnt ){

        $em = $this->_em;
        $stock->setStockIn($qnt);
        $em->persist($stock);
        $em->flush();

    }


    public function insertStockSalesItems(BusinessInvoiceParticular $invoiceParticular ){

        $em = $this->_em;

        /* @var $vendorStock BusinessVendorStockItem */

        $vendorStock = $this->find($invoiceParticular->getVendorStockItem());
        $current = $vendorStock->getSalesQuantity();
        $totalQnt = $current + $invoiceParticular->getTotalQuantity();
        $vendorStock->setSalesQuantity($totalQnt);
        $em->persist($vendorStock);
        $em->flush();

    }

    public function getDropdownList($vendor, $particular)
    {

        $qb = $this->createQueryBuilder('pi');
        $qb->join('pi.particular','p');
        $qb->join('pi.businessVendorStock','e');
        $qb->select('pi.id as id , e.grn as grn , ( COALESCE(SUM(pi.quantity),0) - COALESCE(SUM(pi.salesQuantity),0)) as quantity');
        $qb->where('e.vendor = :vendor')->setParameter('vendor', $vendor) ;
        $qb->andWhere('pi.particular = :particular')->setParameter('particular', $particular) ;
        $qb->groupBy('p.name');
        $qb->orderBy('p.name','ASC');
        $result =  $qb->getQuery()->getArrayResult();

        /* @var  $item BusinessVendorStockItem  */

        $purchaseItems = '';
        $purchaseItems .='<option value="">--Select Stock Quantity--</option>';
        if(!empty($result)){
            foreach ($result as $item){
                if($item['quantity'] > 0) {
                    $purchaseItems .= "<option value='{$item["id"]}'>GRN {$item['grn']} - [ {$item['quantity']} ]</option>";
                }
            }
        }
        return $purchaseItems;
    }




}