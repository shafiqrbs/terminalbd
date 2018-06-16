<?php

namespace Appstore\Bundle\BusinessBundle\Repository;
use Appstore\Bundle\BusinessBundle\Entity\BusinessConfig;
use Appstore\Bundle\BusinessBundle\Entity\BusinessInvoice;
use Appstore\Bundle\BusinessBundle\Entity\BusinessInvoiceParticular;
use Appstore\Bundle\BusinessBundle\Entity\BusinessParticular;
use Appstore\Bundle\BusinessBundle\Entity\BusinessPurchaseItem;
use Appstore\Bundle\HospitalBundle\Entity\InvoiceParticular;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;


/**
 * BusinessInvoiceParticularRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BusinessInvoiceParticularRepository extends EntityRepository
{

    public function handleDateRangeFind($qb,$data)
    {
        if(empty($data)){
            $datetime = new \DateTime("now");
            $data['startDate'] = $datetime->format('Y-m-d 00:00:00');
            $data['endDate'] = $datetime->format('Y-m-d 23:59:59');
        }else{
            $data['startDate'] = date('Y-m-d',strtotime($data['startDate']));
            $data['endDate'] = date('Y-m-d',strtotime($data['endDate']));
        }

        if (!empty($data['startDate']) ) {
            $qb->andWhere("e.created >= :startDate");
            $qb->setParameter('startDate', $data['startDate'].' 00:00:00');
        }
        if (!empty($data['endDate'])) {
            $qb->andWhere("e.created <= :endDate");
            $qb->setParameter('endDate', $data['endDate'].' 23:59:59');
        }
    }


    public function insertInvoiceParticular(BusinessInvoice $invoice, $data)
    {
        $em = $this->_em;
        $entity = new BusinessInvoiceParticular();
        $entity->setBusinessInvoice($invoice);
        $entity->setParticular($data['particular']);
        $entity->setPrice($data['price']);
        $entity->setQuantity($data['quantity']);
        if(!empty($data['quantity'])){
            $entity->setSubTotal($data['price']*$data['quantity']);
        }else{
            $entity->setSubTotal($data['price']);
        }
        if($data['unit']) {
            $unit = $this->_em->getRepository('SettingToolBundle:ProductUnit')->find($data['unit']);
            $entity->setUnit($unit);
        }
        $em->persist($entity);
        $em->flush();

    }

    public function salesStockItemUpdate(BusinessParticular $stockItem)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->select('SUM(e.quantity) AS quantity');
        $qb->where('e.businessParticular = :stock')->setParameter('stock', $stockItem->getId());
        $qnt = $qb->getQuery()->getOneOrNullResult();
        return $qnt['quantity'];
    }

    public function getSalesItems(BusinessInvoice $sales)
    {
        $entities = $sales->getBusinessInvoiceParticulars();
        $data = '';
        $i = 1;

        /* @var $entity BusinessInvoiceParticular */

        foreach ($entities as $entity) {

            $data .= "<tr id='remove-{$entity->getId()}'>";
                $data .= "<td>{$i}.</td>";
                $data .= "<td>{$entity->getParticular()}</td>";
                $data .= "<td>";
                $data .= "<input type='hidden' name='salesItem[]' value='{$entity->getId()}'>";
                $data .= "<input type='text' class='numeric td-inline-input salesPrice' data-id='{$entity->getId()}' autocomplete='off' id='salesPrice-{$entity->getId()}' name='salesPrice' value='{$entity->getPrice()}'>";
                $data .= "</td>";
                $data .= "<td>";
                $data .= "<input type='text' class='numeric td-inline-input-qnt quantity' data-id='{$entity->getId()}' autocomplete='off' min=1  id='quantity-{$entity->getId()}' name='quantity[]' value='{$entity->getQuantity()}' placeholder='Qnt'>";
                $data .= "</td>";
                $data .= "<td id='subTotal-{$entity->getId()}'>{$entity->getSubTotal()}</td>";
                $data .= "<td>";
                $data .= "<a id='{$entity->getId()}' data-id='{$entity->getId()}' data-url='/medicine/sales-temporary/sales-item-update' href='javascript:' class='btn blue mini itemUpdate' ><i class='icon-save'></i></a>";
                $data .= "<a id='{$entity->getId()}' data-id='{$entity->getId()}' data-url='/business/invoice/{$sales->getId()}/{$entity->getId()}/particular-delete' href='javascript:' class='btn red mini particularDelete' ><i class='icon-trash'></i></a>";
                $data .= "</td>";

            $data .= '</tr>';
            $i++;
        }
        return $data;
    }


    public function getLastCode($entity,$datetime)
    {

        $today_startdatetime = $datetime->format('Y-m-d 00:00:00');
        $today_enddatetime = $datetime->format('Y-m-d 23:59:59');


        $qb = $this->createQueryBuilder('ip');
        $qb
            ->select('MAX(ip.code)')
            ->join('ip.dmsInvoice','s')
            ->where('s.hospitalConfig = :hospital')
            ->andWhere('s.updated >= :today_startdatetime')
            ->andWhere('s.updated <= :today_enddatetime')
            ->setParameter('hospital', $entity->getBusinessConfig())
            ->setParameter('today_startdatetime', $today_startdatetime)
            ->setParameter('today_enddatetime', $today_enddatetime);
        $lastCode = $qb->getQuery()->getSingleScalarResult();

        if (empty($lastCode)) {
            return 0;
        }

        return $lastCode;
    }

    public function reportSalesAccessories(GlobalOption $option ,$data)
    {
        $startDate = isset($data['startDate'])  ? $data['startDate'] : '';
        $endDate =   isset($data['endDate'])  ? $data['endDate'] : '';
        $qb = $this->createQueryBuilder('ip');
        $qb->join('ip.particular','p');
        $qb->join('ip.dmsInvoice','i');
        $qb->select('SUM(ip.quantity * p.purchasePrice ) AS totalPurchaseAmount');
        $qb->where('i.hospitalConfig = :hospital');
        $qb->setParameter('hospital', $option->getBusinessConfig());
        $qb->andWhere("i.process IN (:process)");
        $qb->setParameter('process', array('Done','Paid','In-progress','Diagnostic','Admitted','Release','Death','Released','Dead'));
        if (!empty($data['startDate']) ) {
            $qb->andWhere("i.updated >= :startDate");
            $qb->setParameter('startDate', $startDate.' 00:00:00');
        }
        if (!empty($data['endDate'])) {
            $qb->andWhere("i.updated <= :endDate");
            $qb->setParameter('endDate', $endDate.' 23:59:59');
        }
        $res = $qb->getQuery()->getOneOrNullResult();
        return $res['totalPurchaseAmount'];

    }

    public function serviceBusinessParticularDetails(User $user, $data)
    {

        $hospital = $user->getGlobalOption()->getBusinessConfig()->getId();
        $startDate = isset($data['startDate'])  ? $data['startDate'] : '';
        $endDate =   isset($data['endDate'])  ? $data['endDate'] : '';
        if(!empty($data['service'])){

            $qb = $this->createQueryBuilder('ip');
            $qb->leftJoin('ip.particular','p');
            $qb->leftJoin('ip.dmsInvoice','e');
            $qb->select('SUM(ip.quantity) AS totalQuantity');
            $qb->addSelect('SUM(ip.quantity * p.purchasePrice ) AS purchaseAmount');
            $qb->addSelect('SUM(ip.quantity * ip.salesPrice ) AS salesAmount');
            $qb->addSelect('p.name AS serviceName');
            $qb->where('e.hospitalConfig = :hospital');
            $qb->setParameter('hospital', $hospital);
            $qb->andWhere('p.service = :service');
            $qb->setParameter('service', $data['service']);
            $qb->andWhere("e.process IN (:process)");
            $qb->setParameter('process', array('Done','Paid','In-progress','Diagnostic','Admitted','Release','Death'));
            $this->handleDateRangeFind($qb,$data);
            $qb->groupBy('p.id');
            $res = $qb->getQuery()->getArrayResult();
            return $res;

        }else{

            return false;
        }

    }

    public function searchAutoComplete(BusinessConfig $config,$q)
    {
        $query = $this->createQueryBuilder('e');
        $query->join('e.businessInvoice', 'i');
        $query->select('e.particular as id');
        $query->where($query->expr()->like("e.particular", "'$q%'"  ));
        $query->andWhere("i.businessConfig = :config");
        $query->setParameter('config', $config->getId());
        $query->groupBy('e.particular');
        $query->orderBy('e.particular', 'ASC');
        $query->setMaxResults( '10' );
        return $query->getQuery()->getArrayResult();
    }

    public function updateInvoiceItems($data)
    {

        $em = $this->_em;
        $invoiceParticular = $this->find($data['itemId']);
        if(!empty($invoiceParticular)) {
            $entity = $invoiceParticular;
            $entity->setQuantity($data['quantity']);
            $entity->setPrice($data['salesPrice']);
            $entity->setSubTotal($entity->getPrice() * $entity->getQuantity());
        }
        $em->persist($entity);
        $em->flush();
        return $invoiceParticular->getBusinessInvoice();

    }

}
