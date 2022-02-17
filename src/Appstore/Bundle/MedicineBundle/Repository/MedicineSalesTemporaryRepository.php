<?php

namespace Appstore\Bundle\MedicineBundle\Repository;
use Appstore\Bundle\MedicineBundle\Controller\MedicineSalesTemporaryController;
use Appstore\Bundle\MedicineBundle\Entity\MedicinePurchaseItem;
use Appstore\Bundle\MedicineBundle\Entity\MedicineSalesTemporary;
use Appstore\Bundle\MedicineBundle\Entity\MedicineStock;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;


/**
 * InvoiceTemporaryParticularRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MedicineSalesTemporaryRepository extends EntityRepository
{

    public function getSubTotalAmount(User $user)
    {
        $config = $user->getGlobalOption()->getMedicineConfig()->getId();
        $qb = $this->createQueryBuilder('e');
        $qb->select('SUM(e.subTotal) AS subTotal,SUM(e.purchasePrice * e.quantity) AS purchaseSubTotal ');
        $qb->where('e.medicineConfig = :config');
        $qb->setParameter('config', $config);
        $qb->andWhere("e.user =".$user->getId());
        $res = $qb->getQuery()->getOneOrNullResult();
        return $res;
    }

    public function insertInvoiceItems(User $user, $data)
    {
        $quantity = empty($data['quantity']) ? 1 : $data['quantity'];
        $isShort = empty($data['isShort']) ? 0 : 1;
        $purchaseItem = isset($data['purchaseItem']) and !empty($data['purchaseItem']) ? $data['purchaseItem'] : '';
        /* @var $stockItem MedicineStock */
        $stockItem = $this->_em->getRepository('MedicineBundle:MedicineStock')->find($data['stockName']);
        $purchaseStockItem = $this->_em->getRepository('MedicineBundle:MedicinePurchaseItem')->find($purchaseItem);
        $em = $this->_em;
        $entity = new MedicineSalesTemporary();
        $invoiceParticular = $this->_em->getRepository('MedicineBundle:MedicineSalesTemporary')->findOneBy(array('user' => $user,'medicineStock' => $stockItem));
        if(empty($invoiceParticular)) {
	        $entity->setQuantity($quantity);
            if($data['itemPercent'] > 0){
                $entity->setItemPercent( $data['itemPercent'] );
                $salesPrice = $data['salesPrice'];
                $initialDiscount = (($salesPrice *  $data['itemPercent'])/100);
                $initialGrandTotal =($salesPrice  - $initialDiscount);
                $entity->setSalesPrice( round( $initialGrandTotal, 2 ) );
            }else{
                $entity->setSalesPrice( round( $data['salesPrice'], 2 ) );
            }
            $entity->setIsShort($isShort);
            $entity->setEstimatePrice($stockItem->getSalesprice());
	        $entity->setSubTotal( round(($entity->getSalesPrice()*$quantity), 2 ) );
	        $entity->setUser( $user );
	        $entity->setMedicineConfig( $user->getGlobalOption()->getMedicineConfig() );
	        $entity->setMedicineStock( $stockItem );
            if($stockItem->getMedicineConfig()->isProfitLastpp() == 1){
                $entity->setPurchasePrice( round( $stockItem->getPurchasePrice(), 2 ) );
            }else{
                $entity->setPurchasePrice( round( $stockItem->getAveragePurchasePrice(), 2 ) );
            }
	        if(!empty($purchaseStockItem)){
				 $entity->setPurchasePrice(round($purchaseStockItem->getPurchasePrice(),2));
				 $entity->setMedicinePurchaseItem($purchaseStockItem);
			}
	        $em->persist( $entity );
	        $em->flush();

        }

    }

    public function insertBarcodeInvoiceItems(User $user,MedicineStock $stockItem)
    {
        $em = $this->_em;
        $entity = new MedicineSalesTemporary();
        $invoiceParticular = $this->_em->getRepository('MedicineBundle:MedicineSalesTemporary')->findOneBy(array('user' => $user,'medicineStock' => $stockItem));
        if(empty($invoiceParticular)) {
	        $entity->setQuantity(1);
            $entity->setSalesPrice($stockItem->getSalesprice());
            $entity->setEstimatePrice($stockItem->getSalesprice());
	        $entity->setSubTotal( round(($entity->getSalesPrice()), 2 ) );
	        $entity->setUser($user);
	        $entity->setMedicineConfig( $user->getGlobalOption()->getMedicineConfig() );
	        $entity->setMedicineStock( $stockItem );
	        if($stockItem->getMedicineConfig()->isProfitLastpp() == 1){
                $entity->setPurchasePrice( round( $stockItem->getPurchasePrice(), 2 ) );
            }else{
                $entity->setPurchasePrice( round( $stockItem->getAveragePurchasePrice(), 2 ) );
            }
	        $em->persist( $entity );
	        $em->flush();
        }

    }

    public function insertGenericInvoiceItems(User $user,MedicineStock $stockItem,$data)
    {
        $em = $this->_em;
        $quantity = empty($data['quantity']) or $data['quantity'] == 'NaN'  ? 1 : $data['quantity'];
        $entity = new MedicineSalesTemporary();
        $invoiceParticular = $this->_em->getRepository('MedicineBundle:MedicineSalesTemporary')->findOneBy(array('user' => $user,'medicineStock' => $stockItem));
        if(empty($invoiceParticular)) {
            $entity->setQuantity($quantity);
            if($data['itemPercent'] > 0){
                $entity->setItemPercent( $data['itemPercent'] );
                $salesPrice = $data['salesPrice'];
                $initialDiscount = (($salesPrice *  $data['itemPercent'])/100);
                $initialGrandTotal =($salesPrice  - $initialDiscount);
                $entity->setSalesPrice( round( $initialGrandTotal, 2 ) );
            }else{
                $entity->setSalesPrice( round( $data['salesPrice'], 2 ) );
            }
            $entity->setIsShort(false);
            $entity->setEstimatePrice($stockItem->getSalesprice());
            $entity->setSubTotal( round(($entity->getSalesPrice()*$quantity), 2 ) );
            $entity->setUser( $user );
            $entity->setMedicineConfig( $user->getGlobalOption()->getMedicineConfig() );
            $entity->setMedicineStock( $stockItem );
            if($stockItem->getMedicineConfig()->isProfitLastpp() == 1){
                $entity->setPurchasePrice( round( $stockItem->getPurchasePrice(), 2 ) );
            }else{
                $entity->setPurchasePrice( round( $stockItem->getAveragePurchasePrice(), 2 ) );
            }
            $em->persist( $entity );
            $em->flush();

        }

    }

    public function updateInvoiceItems(User $user, $data)
    {

        $em = $this->_em;
        $entity = new MedicineSalesTemporary();
        /* @var $invoiceParticular MedicineSalesTemporary*/
        $invoiceParticular = $this->_em->getRepository('MedicineBundle:MedicineSalesTemporary')->find($data['salesItemId']);
        if(!empty($invoiceParticular)) {
            $entity = $invoiceParticular;
            $entity->setQuantity($data['quantity']);
            $entity->setSalesPrice($data['salesPrice']);
            $entity->setItemPercent($data['itemPercent']);
            if($data['itemPercent'] > 0){
                $entity->setItemPercent( $data['itemPercent'] );
                $salesPrice = $data['salesPrice'];
                $initialDiscount = (($salesPrice *  $data['itemPercent'])/100);
                $initialGrandTotal =($salesPrice  - $initialDiscount);
                $entity->setSalesPrice( round( $initialGrandTotal, 2 ) );
            }else{
                $entity->setSalesPrice( round( $data['salesPrice'], 2 ) );
            }
            if($entity->getMedicineStock()->getMedicineConfig()->isProfitLastpp() == 1){
                $entity->setPurchasePrice( round( $entity->getMedicineStock()->getPurchasePrice(), 2 ) );
            }else{
                $entity->setPurchasePrice( round( $entity->getMedicineStock()->getAveragePurchasePrice(), 2 ) );
            }
            $entity->setSubTotal($entity->getSalesPrice() * $entity->getQuantity());
        }
        $em->persist($entity);
        $em->flush();

    }


    public function insertInstantSalesTemporaryItem(User $user , MedicinePurchaseItem $item,$data){


        $em = $this->_em;
        $entity = new MedicineSalesTemporary();
        $entity->setUser($user);
        $entity->setMedicineConfig($user->getGlobalOption()->getMedicineConfig());
        $entity->setMedicineStock($item->getMedicineStock());
        $entity->setMedicinePurchaseItem($item);
        $entity->setQuantity($data['salesQuantity']);
        $entity->setSubTotal($item->getSalesPrice() * $data['salesQuantity']);
        $entity->setSalesPrice($item->getSalesPrice());
        $entity->setPurchasePrice($item->getPurchasePrice());
        $em->persist($entity);
        $em->flush();

    }

    public function insertDirectInvoiceItems(User $user ,MedicineStock $stock,$data){

        $em = $this->_em;
        $entity = new MedicineSalesTemporary();
        $entity->setUser($user);
        $entity->setMedicineConfig($user->getGlobalOption()->getMedicineConfig());
        $entity->setMedicineStock($stock);
        $entity->setQuantity($stock->getSalesQuantity());
        $entity->setEstimatePrice($stock->getSalesprice());
        $entity->setSalesPrice($stock->getSalesPrice());
        $entity->setPurchasePrice($stock->getPurchasePrice());
        $entity->setSubTotal($stock->getSalesPrice() * $entity->getQuantity());
        $em->persist($entity);
        $em->flush();

    }



    public function getSalesItems(User $user)
    {
        $entities = $user->getMedicineSalesTemporary();
        $data = '';
        $i = 1;
        /* @var $entity MedicineSalesTemporary */
        foreach ($entities as $entity) {

            $rack ="";
            $barcode ="";
            if(!empty($entity->getMedicineStock()->getRackNo())){
                $rack = $entity->getMedicineStock()->getRackNo()->getName();
            }
            if(!empty($entity->getMedicinePurchaseItem())){
	            $barcode = $entity->getMedicinePurchaseItem()->getBarcode();
            }

            $purchasePrice = number_format((float) $entity->getPurchasePrice(), 2, '.', '');
            $salesPrice = number_format((float)$entity->getSalesPrice(), 2, '.', '');
            $subTotal = number_format((float)$entity->getSubTotal(), 2, '.', '');
            $data .= '<tr id="remove-'. $entity->getId() . '">';
            /*$data .= '<td class="span1" >' . $barcode . '</td>';*/
            $data .= "<td class='span4'>{$i}. {$entity->getMedicineStock()->getName()}</td>";
            $data .= '<td class="span1" >' . $rack. '</td>';
            $data .= "<td class='span1' >{$purchasePrice}</td>";
            $data .= "<td class='span1' >";
            $data .= "<input type='number' class='numeric td-inline-input salesPrice' data-id='{$entity->getid()}' autocomplete='off' id='salesPrice-{$entity->getId()}' name='salesPrice' value='{$salesPrice}'>";
            $data .= "</td>";
            $data .= "<td class='span1' >";
            $data .= "<input type='number' class='numeric td-inline-input quantity' data-id='{$entity->getid()}' autocomplete='off' id='quantity-{$entity->getId()}' name='quantity' value='{$entity->getQuantity()}'>";
            $data .= "</td>";
            $data .= '<td class="span1" >' . $entity->getItemPercent().'</td>';
            $data .= "<td class='span1' id='subTotal-{$entity->getid()}'>{$subTotal}</td>";
            $data .= '<td class="span1" >
            <a data-id="'.$entity->getid().'" title="" data-url="/medicine/sales-temporary/sales-item-update" href="javascript:" class="btn blue mini itemUpdate"><i class="icon-save"></i></a>
            <a id="'.$entity->getId().'" data-id="'.$entity->getId().'" data-url="/medicine/sales-temporary/' . $entity->getId() . '/item-delete" href="javascript:" class="btn red mini temporaryDelete" ><i class="icon-trash"></i></a>
            </td>';
            $data .= '</tr>';
            $i++;
        }
        return $data;
    }

    public function removeSalesTemporary(User $user)
    {
        $em = $this->_em;
        $config = $user->getGlobalOption()->getMedicineConfig()->getId();
        $DoctorInvoice = $em->createQuery('DELETE MedicineBundle:MedicineSalesTemporary e WHERE e.medicineConfig = '.$config.' and e.user = '.$user->getId());
        $DoctorInvoice->execute();
    }

}
