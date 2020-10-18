<?php

namespace Appstore\Bundle\RestaurantBundle\Repository;
use Appstore\Bundle\RestaurantBundle\Entity\RestaurantConfig;
use Appstore\Bundle\RestaurantBundle\Entity\RestaurantTableInvoice;
use Appstore\Bundle\RestaurantBundle\Entity\RestaurantTableInvoiceItem;
use Appstore\Bundle\RestaurantBundle\Entity\RestaurantTemporary;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * RestaurantTemporary
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */

class RestaurantTableInvoiceRepository extends EntityRepository
{

    public function resetData(RestaurantTableInvoice $invoice)
    {
        $em = $this->_em;
        $invoice->setProcess('Created');
        $invoice->setVat(0);
        $invoice->setSd(0);
        $invoice->setDiscount(0);
        $invoice->setDiscountCalculation(0);
        $invoice->setDiscountType(NULL);
        $invoice->setDiscountCoupon(0);
        $invoice->setSubTotal(0);
        $invoice->setTotal(0);
        $invoice->setPayment(0);
        $invoice->setTransactionMethod(NULL);
        $invoice->setAccountMobileBank(NULL);
        $invoice->setAccountBank(NULL);
        $invoice->setSalesBy(NULL);
        $invoice->setServeBy(array());
        $invoice->setSubTable(array());
        $em->persist($invoice);
        $em->flush();
        $history = $em->createQuery("DELETE RestaurantBundle:RestaurantTableInvoiceItem e WHERE e.tableInvoice ={$invoice->getId()}");
        $history->execute();

    }

    public function fastTableInvoice(RestaurantConfig $config){

        $id = $config->getId();
        $qb = $this->createQueryBuilder('e');
        $qb->select('e.id');
        $qb->where('e.restaurantConfig = :config')->setParameter('config', $id);
        $qb->setMaxResults(1);
        $qb->orderBy('e.id','ASC');
        $table = $qb->getQuery()->getOneOrNullResult()['id'];
        $entity = $this->find($table);
        return $entity;
    }

    public function generateTableInvoice(RestaurantConfig $config,$tables)
    {
        $em = $this->_em;
        foreach ($tables as $table):
            $exist = $this->findOneBy(array('restaurantConfig'=>$config,'table'=>$table));
            if(empty($exist)){
                $entity = new RestaurantTableInvoice();
                $entity->setRestaurantConfig($config);
                $entity->setTable($table);
                $em->persist($entity);
                $em->flush();
            }
        endforeach;
    }

    public function updateKitchenPrint(RestaurantTableInvoice $invoice,$tables)
    {
        $em = $this->_em;

        /* @var $entity RestaurantTableInvoiceItem */

        $i = 0;
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->update('RestaurantBundle:RestaurantTableInvoiceItem', 'mg')
            ->set('mg.isPrint',0)
            ->where('mg.tableInvoice = :id')
            ->setParameter('id', $invoice->getId())
            ->getQuery()
            ->execute();

        foreach ($tables as $key => $value):
            $entity = $em->getRepository('RestaurantBundle:RestaurantTableInvoiceItem')->find($value);
            $entity->setIsPrint(true);
            $em->persist($entity);
            $em->flush();
        endforeach;
    }

    public function updateInvoiceTotalPrice(RestaurantTableInvoice $invoice)
    {
        $em = $this->_em;
        $total = $em->createQueryBuilder()
            ->from('RestaurantBundle:RestaurantTableInvoiceItem','si')
            ->select('sum(si.subTotal) as subTotal')
            ->where('si.tableInvoice = :invoice')
            ->setParameter('invoice', $invoice ->getId())
            ->getQuery()->getOneOrNullResult();
        $subTotal = !empty($total['subTotal']) ? $total['subTotal'] :0;
        if($subTotal > 0){
            if ($invoice->getRestaurantConfig()->getVatEnable() == 1 && $invoice->getRestaurantConfig()->getVatPercentage() > 0) {
                $vat = $this->getCalculationVat($invoice,$subTotal);
                $invoice->setVat($vat);
            }
            $invoice->setSubTotal($subTotal);
            if($invoice->getDiscountCalculation()){
                $discount = $this->discountCalculation($invoice);
                $invoice->setDiscount($discount);
            }
            if($invoice->getDiscountCoupon()){
                $discount = $this->couponDiscount($invoice);
                $invoice->setDiscount($discount);
            }
            $total = ($invoice->getSubTotal() + $invoice->getVat() - $invoice->getDiscount());
            $invoice->setTotal($total);

        }else{

            $invoice->setSubTotal(0);
            $invoice->setTotal(0);
            $invoice->setDiscount(0);
            $invoice->setVat(0);
        }
        $em->persist($invoice);
        $em->flush();
        return $invoice;

    }

    public function getCalculationVat(RestaurantTableInvoice $sales,$totalAmount)
    {
        $vat = ( ($totalAmount * (int)$sales->getRestaurantConfig()->getVatPercentage())/100 );
        return round($vat);
    }


    public function discountCalculation(RestaurantTableInvoice $sales)
    {
        $discount = 0;
        if($sales->getDiscountType() == 'flat' and !empty($sales->getDiscountCalculation())){
            $discount =  $sales->getDiscountCalculation();
        }elseif($sales->getDiscountType() == 'percentage' and !empty($sales->getDiscountCalculation())){
            $discount = ($sales->getSubTotal() * $sales->getDiscountCalculation())/100;
        }
        return $discount;
    }

    public function couponDiscount(RestaurantTableInvoice $sales)
    {
        $config = $sales->getRestaurantConfig();
        $discount = 0;
        if($config->getDiscountType() == 'flat' and !empty($discount)){
            $discount = $config->getDiscountPercentage();
        }elseif($config->getDiscountType() == 'percentage' and !empty($discount)){
            $discount = ($sales->getSubTotal() * $config->getDiscountPercentage())/100;
        }
        return $discount;
    }


    public function getSubTotalAmount(User $user)
    {
        $config = $user->getGlobalOption()->getRestaurantConfig()->getId();
        $qb = $this->createQueryBuilder('e');
        $qb->select('SUM(e.subTotal) AS subTotal');
        $qb->addSelect('SUM(e.purchasePrice * e.quantity) AS purchasePrice');
        $qb->where('e.restaurantConfig = :config');
        $qb->setParameter('config', $config);
        $qb->andWhere("e.user =".$user->getId());
        $res = $qb->getQuery()->getOneOrNullResult();
        return $res;
    }

    public function generateVat(User $user,$total)
    {
        $config = $user->getGlobalOption()->getRestaurantConfig();
        $vat = 0;
        if($config->getVatEnable() == 1){
            $vat = (($total * $config->getVatPercentage())/100);
        }
        return $vat;

    }

    public function insertInvoiceItems(RestaurantTableInvoice $invoice, $data)
    {
        $particular = $this->_em->getRepository('RestaurantBundle:Particular')->find($data['particularId']);
        $em = $this->_em;
        $entity = new RestaurantTableInvoiceItem();
        $invoiceParticular = $em->getRepository('RestaurantBundle:RestaurantTableInvoiceItem')->findOneBy(array('tableInvoice' => $invoice ,'particular' => $particular));
        if(!empty($invoiceParticular) and $data['process'] == 'update') {
            $entity = $invoiceParticular;
            $entity->setQuantity((float)$data['quantity']);
            $entity->setSubTotal($data['price'] * $entity->getQuantity());
        }elseif(!empty($invoiceParticular) and $data['process'] == "create") {
            $entity = $invoiceParticular;
            $entity->setQuantity($invoiceParticular->getQuantity() + 1);
            $entity->setSubTotal($data['price'] * $entity->getQuantity());
        }else{
            $entity->setQuantity(1);
            $entity->setSalesPrice($data['price']);
            $entity->setSubTotal($data['price']);
        }
        if($particular->getRestaurantConfig()->isProduction() == 1 and $particular->getService()->getSlug() == 'product'){
            $entity->setPurchasePrice($particular->getProductionElementAmount());
        }else{
            $entity->setPurchasePrice($particular->getPurchasePrice());
        }
        $entity->setTableInvoice($invoice);
        $entity->setParticular($particular);
        $em->persist($entity);
        $em->flush();

    }


    public function getSalesGridItems(RestaurantTableInvoice $invoice)
    {
        $entities = $invoice->getInvoiceItems();
        $data = '';
        $i = 1;
        /* @var $entity RestaurantTableInvoiceItem */
        foreach ($entities as $entity) {
            $checked = ($entity->isPrint() == 1) ? 'checked="checked"' : '';
            $data .= "<tr id='remove-{$entity->getId()}'>";
            $data .= " <td><input type='checkbox' value='{$entity->getId()}' class='checkbox' id='isPrint-{$entity->getId()}' name='isPrint[]' {$checked} ></td>";
            $data .= "<td>{$entity->getParticular()->getName()}</td>";
            $data .= "<td>{$entity->getSalesPrice()}</td>";
            $data .= "<td><div class='input-append' style='margin-bottom: 0!important;'> <span class='input-group-btn'> <a href='javascript:' data-action='/restaurant/table-invoice/{$entity->getParticular()->getId()}/product-update' class='btn yellow btn-number mini' data-type='minus' data-id='{$entity->getId()}'  data-text='{$entity->getId()}' data-title='{$entity->getSalesPrice()}'  data-field='quantity'> <span class='fa fa-minus'></span> </a> <input type='text' class='form-control inline-m-wrap updateProduct btn-qnt-particular' data-action='/restaurant/table-invoice/{$entity->getParticular()->getId()}/product-update' id='quantity-{$entity->getId()}' data-id='{$entity->getId()}' data-title='{$entity->getSalesPrice()}' name='quantity-{$entity->getId()}' value='{$entity->getQuantity()}'  min='1' max='1000'> <a href='javascript:' data-action='/restaurant/table-invoice/{$entity->getParticular()->getId()}/product-update' class='btn green btn-number mini'  data-type='plus' data-id='{$entity->getId()}' data-title='{$entity->getSalesPrice()}'  data-text='{$entity->getId()}' data-field='quantity'><span class='fa fa-plus'></span></a> </span></div></td>";
            $data .= "<td>{$entity->getSubTotal()}</td>";
            $data .= "<td> <a id='{$entity->getId()}' data-id='{$entity->getId()}' data-url='/restaurant/table-invoice/{$invoice->getId()}/{$entity->getId()}/particular-delete' href='javascript:' class='btn red mini particularDelete' ><i class='icon-trash'></i></a></td></tr>";
            $i++;
        }
        return $data;
    }


    public function removeInitialParticular(User $user)
    {
        $em = $this->_em;
        $config = $user->getGlobalOption()->getRestaurantConfig()->getId();
        $entity = $em->createQuery('DELETE RestaurantBundle:RestaurantTemporary e WHERE e.restaurantConfig = '.$config.' and e.user = '.$user->getId());
        $entity->execute();
    }

}
