<?php

namespace Appstore\Bundle\RestaurantBundle\Repository;
use Appstore\Bundle\InventoryBundle\Entity\SalesItem;
use Appstore\Bundle\RestaurantBundle\Controller\InvoiceController;
use Appstore\Bundle\RestaurantBundle\Entity\Invoice;
use Appstore\Bundle\RestaurantBundle\Entity\InvoiceParticular;
use Appstore\Bundle\RestaurantBundle\Entity\Particular;
use Appstore\Bundle\RestaurantBundle\Entity\RestaurantTableInvoice;
use Appstore\Bundle\RestaurantBundle\Entity\RestaurantTableInvoiceItem;
use Appstore\Bundle\RestaurantBundle\Entity\RestaurantTemporary;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;


/**
 * PathologyRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class InvoiceParticularRepository extends EntityRepository
{

    public function handleDateRangeFind($qb, $data)
    {
        if (empty($data)) {
            $datetime = new \DateTime("now");
            $data['startDate'] = $datetime->format('Y-m-d 00:00:00');
            $data['endDate'] = $datetime->format('Y-m-d 23:59:59');
        } else {
            $data['startDate'] = date('Y-m-d', strtotime($data['startDate']));
            $data['endDate'] = date('Y-m-d', strtotime($data['endDate']));
        }

        if (!empty($data['startDate'])) {
            $qb->andWhere("i.created >= :startDate");
            $qb->setParameter('startDate', "{$data['startDate']} 00:00:00");
        }
        if (!empty($data['endDate'])) {
            $qb->andWhere("i.created <= :endDate");
            $qb->setParameter('endDate', "{$data['endDate']} 23:59:59");
        }
    }

    public function salesStockItemUpdate(Particular $stockItem)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->join('e.invoice', 'mp');
        $qb->select('SUM(e.quantity) AS quantity');
        $qb->where('e.particular = :particular')->setParameter('particular', $stockItem->getId());
        $qb->andWhere('mp.process IN (:process)')->setParameter('process', array('Done','Delivered'));
        $qnt = $qb->getQuery()->getOneOrNullResult();
        return $qnt['quantity'];
    }

    public function findWithCategoryOverview(User $user, $data)
    {

        $config = $user->getGlobalOption()->getRestaurantConfig()->getId();
        $qb = $this->createQueryBuilder('e');
        $qb->leftJoin('e.invoice','i');
        $qb->leftJoin('e.particular','p');
        $qb->leftJoin('p.category','c');
        $qb->select('sum(e.subTotal) as amount');
        $qb->addSelect('SUM(e.quantity) as quantity');
        $qb->addSelect('c.name as categoryName');
        $qb->where('i.restaurantConfig = :config')->setParameter('config', $config);
        $qb->andWhere("i.process IN (:process)");
        $qb->setParameter('process', array('Done','Delivered'));
        $this->handleDateRangeFind($qb,$data);
        $qb->groupBy('c.id');
        $result = $qb->getQuery()->getArrayResult();
        return $result;
    }

    public function findWithProductOverview(User $user, $data)
    {

        $config = $user->getGlobalOption()->getRestaurantConfig()->getId();
        $qb = $this->createQueryBuilder('e');
        $qb->leftJoin('e.invoice','i');
        $qb->leftJoin('e.particular','p');
        $qb->select('sum(e.subTotal) as amount');
        $qb->addSelect('p.name as productName');
        $qb->addSelect('p.price as price');
        $qb->addSelect('SUM(e.quantity) as quantity');
        $qb->where('i.restaurantConfig = :config')->setParameter('config', $config);
        $qb->andWhere("i.process IN (:process)");
        $qb->setParameter('process', array('Done','Delivered'));
        $this->handleDateRangeFind($qb,$data);
        $qb->groupBy('p.id');
        $result = $qb->getQuery()->getArrayResult();
        return $result;
    }


    public function initialInvoiceItems(User $user, Invoice $invoice)
    {
        $em = $this->_em;
        $entities = $user->getRestaurantTemps();
        /* @var $temp RestaurantTemporary */
        foreach ($entities as $temp) {
            $entity = new InvoiceParticular();
            $entity->setQuantity($temp->getQuantity());
            $entity->setSalesPrice($temp->getSalesPrice());
            if($invoice->getRestaurantConfig()->isProduction() == 1 and $temp->getParticular()->getService()->getSlug() == 'product'){
                $entity->setPurchasePrice($temp->getParticular()->getProductionElementAmount());
            }else{
                $entity->setPurchasePrice($temp->getParticular()->getPurchasePrice());
            }
            $entity->setSubTotal($temp->getSubTotal());
            $entity->setInvoice($invoice);
            $entity->setParticular($temp->getParticular());
            $em->persist($entity);
            $em->flush();
            $this->insertSalesAccessories($entity);
        }
        return $invoice;
    }

    public function tableInvoiceItems(Invoice $invoice,RestaurantTableInvoice $tableInvoice)
    {
        $em = $this->_em;
        /* @var $temp RestaurantTableInvoiceItem */
        foreach ($tableInvoice->getInvoiceItems() as $temp) {
            $entity = new InvoiceParticular();
            $entity->setQuantity($temp->getQuantity());
            $entity->setSalesPrice($temp->getSalesPrice());
            if($invoice->getRestaurantConfig()->isProduction() == 1 and $temp->getParticular()->getService()->getSlug() == 'product'){
                $entity->setPurchasePrice($temp->getParticular()->getProductionElementAmount());
            }else{
                $entity->setPurchasePrice($temp->getParticular()->getPurchasePrice());
            }
            $entity->setSubTotal($temp->getSubTotal());
            $entity->setInvoice($invoice);
            $entity->setParticular($temp->getParticular());
            $em->persist($entity);
            $em->flush();
            $this->insertSalesAccessories($entity);
        }
        return $invoice;
    }

    public function insertSalesAccessories(InvoiceParticular $item)
    {
        $em = $this->_em;
        /** @var Particular  $particular */
        $particular = $item->getParticular();
        if( $particular->getService()->getSlug() == 'stockable' ){
            $qnt = ($particular->getSalesQuantity() + $item->getQuantity());
            $particular->setSalesQuantity($qnt);
            $em->persist($particular);
            $em->flush();
        }
    }

    public function insertInvoiceItems(Invoice $invoice, $data)
    {
        $em = $this->_em;
        $particularId = (int)$data['particularId'];
        $particular = $this->_em->getRepository('RestaurantBundle:Particular')->find($particularId);
        $entity = new InvoiceParticular();
        $invoiceParticular = $this->findOneBy(array('invoice' => $invoice ,'particular' => $particular));
        if(!empty($invoiceParticular) and $data['process'] == 'create') {
            $entity = $invoiceParticular;
            $entity->setQuantity($invoiceParticular->getQuantity() + 1);
            $entity->setSubTotal($particular->getPrice() * $entity->getQuantity());
        }elseif(!empty($invoiceParticular) and $data['process'] == 'update') {
            $entity = $invoiceParticular;
            $entity->setQuantity((float)$data['quantity']);
            $entity->setSubTotal($particular->getPrice() * $entity->getQuantity());
        }else{
            $entity->setQuantity($data['quantity']);
            $entity->setSalesPrice($particular->getPrice());
            $entity->setSubTotal($particular->getPrice() * $entity->getQuantity());
        }
        $entity->setPurchasePrice($particular->getPurchasePrice());
        $entity->setInvoice($invoice);
        $entity->setParticular($particular);
        $entity->setEstimatePrice($particular->getPrice());
        $em->persist($entity);
        $em->flush();

    }


    public function getSalesItems(Invoice $sales)
    {
        $entities = $sales->getInvoiceParticulars();
        $data = '';
        $i = 1;
        foreach ($entities as $entity) {
            $data .= '<tr id="remove-'. $entity->getId().'">';
            $data .= "<td>{$i}</td>";
            $data .= '<td class="span4" >'.$i.'. '. $entity->getParticular()->getParticularCode() .' - ' .$entity->getParticular()->getName(). '</td>';
            $data .= '<td class="span1" >' . $entity->getSalesPrice().'</td>';
            $data .= '<td class="span1" >';
            $data .='<div class="input-group input-append">';
            $data .='<span class="input-group-btn">';
            $data .='<button type="button" class="btn yellow btn-number" data-type="minus" data-field="quantity" data-id="'.$entity->getId().'"  data-text="'.$entity->getId().'" data-title=""><i class="icon-minus"></i></button>';
            $data .='</span>';
            $data .='<input type="text" readonly="readonly" style="text-align:center; width:80px; height: 34px !important;" name="quantity" id="quantity-'.$entity->getId().'" class="form-control m-wrap  span4 input-number" value="'.$entity->getQuantity().'" min="1" max="100" >';
            $data .='<span class="input-group-btn">';
            $data .='<button type="button" class="btn green btn-number" data-type="plus" data-field="quantity" data-id="'.$entity->getId().'" data-text="'.$entity->getId().'"   data-title=""><i class="icon-plus"></i></button>';
            $data .='<button type="button" class="btn blue addCart" data-title="'.$entity->getSalesPrice().'"  data-text="'.$entity->getId().'"  id=""  data-id="'.$entity->getParticular()->getId().'"  data-url="/restaurant/invoice/'.$sales->getId().'/particular-add" ><i class="icon-shopping-cart"></i> Add</button>';
            $data .='<div>';
            $data .='</td>';
            $data .= '<td class="span2" id="subTotal-'.$entity->getId().'" >= '.$entity->getSubTotal().'</td>';
            $data .= '<td class="span1" >
            <a id="'.$entity->getId().'" data-id="'.$entity->getId().'"  data-url="/restaurant/invoice/' . $sales->getId() . '/' . $entity->getId() . '/particular-delete" href="javascript:" class="btn red particularDelete" ><i class="icon-trash"></i></a>
            </td>';
            $data .= '</tr>';
            $i++;
        }
        return $data;

    }

    public function invoiceParticularLists(Invoice $sales){

        /* @var $entity InvoiceParticular */

        $entities = $sales->getInvoiceParticulars();
        $data = '';
        $i = 1;
        foreach ($entities as $entity) {
            $data .= "<tr id='remove-{$entity->getId()}'>";
            $data .= "<td>{$i}. {$entity->getParticular()->getName()}</td>";
            $data .= "<td>{$entity->getSalesPrice()}</td>";
            $data .= "<td><div class='input-append' style='margin-bottom: 0!important;'>
                                                    <span class='input-group-btn'>
  <a href='javascript:' data-action='/restaurant/invoice/{$sales->getId()}/{$entity->getParticular()->getId()}/product-update' class='btn yellow btn-number mini' data-type='minus' data-id='{$entity->getId()}'  data-text='{$entity->getId()}' data-title='{{ item.salesPrice }}'  data-field='quantity'>
                                                            <span class='fa fa-minus'></span>
                                                   </a>
                                                                     <input type='text' class='form-control inline-m-wrap updateProduct btn-qnt-particular' data-action='/restaurant/invoice/{$sales->getId()}/{$entity->getParticular()->getId()}/product-update' data-id='{$entity->getId()}' data-title='{$entity->getSalesPrice()}' id='quantity-{$entity->getId()}' name='quantity-{$entity->getId()}' value='{$entity->getQuantity()}'  min='1' max='1000'>
                                                      <a href='javascript:' data-action='/restaurant/invoice/{$sales->getId()}/{$entity->getParticular()->getId()}/product-update' class='btn green btn-number mini'  data-type='plus' data-id='{$entity->getId()}' data-title='{$entity->getSalesPrice()}'  data-text='{$entity->getId()}' data-field='quantity'>
                                                          <span class='fa fa-plus'></span>
                                                  </a>
                                                        </span>

                                            </div></td>";
            $data .= "<td id='subTotal-{$entity->getId()}'>{$entity->getSubTotal()}</td>";
            $data .= "<td><a id='{$entity->getId()}' data-id='{$entity->getId()}'  data-url='/restaurant/invoice/{$sales->getId()}/{$entity->getId()}/particular-delete' href='javascript:' class='btn red mini particularDelete'><i class='icon-trash'></i></a></td>";
            $data .= "</tr>";
            $i++;
        }
        return $data;
    }

    public function invoiceParticularReverse(Invoice $invoice)
    {

        $em = $this->_em;

        /** @var InvoiceParticular $item */

        foreach($invoice->getInvoiceParticulars() as $item ){

            /** @var Particular  $particular */

            $particular = $item->getParticular();
            if( $particular->getService()->getId() == 4 ){
                $qnt = ($particular->getSalesQuantity() - $item->getQuantity());
                $particular->setSalesQuantity($qnt);
                $em->persist($particular);
                $em->flush();
            }
        }

    }

    public function getLastCode($entity,$datetime)
    {

        $today_startdatetime = $datetime->format('Y-m-d 00:00:00');
        $today_enddatetime = $datetime->format('Y-m-d 23:59:59');


        $qb = $this->createQueryBuilder('ip');
        $qb
            ->select('MAX(ip.code)')
            ->join('ip.invoice','s')
            ->where('s.restaurantConfig = :hospital')
            ->andWhere('s.updated >= :today_startdatetime')
            ->andWhere('s.updated <= :today_enddatetime')
            ->setParameter('hospital', $entity->getRestaurantConfig())
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
        $qb->join('ip.invoice','i');
        $qb->select('SUM(ip.quantity * p.purchasePrice ) AS totalPurchaseAmount');
        $qb->where('i.hospitalConfig = :hospital');
        $qb->setParameter('hospital', $option->getRestaurantConfig());
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

    public function serviceParticularDetails(User $user, $data)
    {

        $hospital = $user->getGlobalOption()->getRestaurantConfig()->getId();
        $startDate = isset($data['startDate'])  ? $data['startDate'] : '';
        $endDate =   isset($data['endDate'])  ? $data['endDate'] : '';
        if(!empty($data['service'])){

            $qb = $this->createQueryBuilder('ip');
            $qb->leftJoin('ip.particular','p');
            $qb->leftJoin('ip.invoice','e');
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

    public function reverseInvoiceParticularUpdate(Invoice $invoice)
    {
        $em = $this->_em;

        /** @var InvoiceParticular $item */
        foreach($invoice->getInvoiceParticulars() as $item ){
            /** @var Particular  $particular */
            $particular = $item->getParticular();
            if( $particular->getService()->getSlug() == 'stockable' ){
                $qnt = ($particular->getSalesQuantity() - $item->getQuantity());
                $particular->setSalesQuantity($qnt);
                $em->persist($particular);
                $em->flush();
            }
        }
    }


}
