<?php

namespace Appstore\Bundle\MedicineBundle\Repository;
use Appstore\Bundle\MedicineBundle\Controller\MedicineSalesTemporaryController;
use Appstore\Bundle\MedicineBundle\Entity\MedicinePurchaseItem;
use Appstore\Bundle\MedicineBundle\Entity\MedicineSalesTemporary;
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
        $qb->select('SUM(e.subTotal) AS subTotal');
        $qb->where('e.medicineConfig = :config');
        $qb->setParameter('config', $config);
        $qb->andWhere("e.user =".$user->getId());
        $res = $qb->getQuery()->getOneOrNullResult();
        return $res['subTotal'];
    }

    public function insertInvoiceItems(User $user, $data)
    {

        $stockItem = $this->_em->getRepository('MedicineBundle:MedicineStock')->find($data['stockName']);
        $purchaseStockItem = $this->_em->getRepository('MedicineBundle:MedicinePurchaseItem')->find($data['barcode']);

        $em = $this->_em;
        $entity = new MedicineSalesTemporary();
        $invoiceParticular = $this->_em->getRepository('MedicineBundle:MedicineSalesTemporary')->findOneBy(array('user' => $user,'medicineStock' => $stockItem,'medicinePurchaseItem' => $purchaseStockItem));

        if(!empty($invoiceParticular)) {
            $entity = $invoiceParticular;
            $entity->setQuantity($invoiceParticular->getQuantity() + $data['quantity']);
            $entity->setSubTotal($data['salesPrice'] * $entity->getQuantity());

        }else{

            $entity->setQuantity($data['quantity']);
            $entity->setSalesPrice($data['salesPrice']);
            $entity->setSubTotal($data['salesPrice'] * $data['quantity']);
        }

        $entity->setUser($user);
        $entity->setMedicineConfig($user->getGlobalOption()->getMedicineConfig());
        $entity->setMedicineStock($stockItem);
        $entity->setMedicinePurchaseItem($purchaseStockItem);
        $em->persist($entity);
        $em->flush();

    }

    public function updateInvoiceItems(User $user, $data)
    {

        $em = $this->_em;
        $entity = new MedicineSalesTemporary();
        $invoiceParticular = $this->_em->getRepository('MedicineBundle:MedicineSalesTemporary')->find($data['salesItemId']);
        if(!empty($invoiceParticular)) {
            $entity = $invoiceParticular;
            $entity->setQuantity($data['quantity']);
            $entity->setSalesPrice($data['salesPrice']);
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
            $data .= '<tr id="remove-'. $entity->getId() . '">';
            $data .= '<td class="span1" >' . $entity->getMedicinePurchaseItem()->getBarcode() . '</td>';
            $data .= '<td class="span4" >' . $entity->getMedicineStock()->getName() . '</td>';
            $data .= "<td class='span1' >";
            $data .= "<input type='number' class='numeric td-inline-input salesPrice' data-id='{$entity->getid()}' autocomplete='off' id='salesPrice-{$entity->getId()}' name='salesPrice' value='{$entity->getSalesPrice()}'>";
            $data .= "</td>";
            $data .= "<td class='span1' >";
            $data .= "<input type='number' class='numeric td-inline-input quantity' data-id='{$entity->getid()}' autocomplete='off' id='quantity-{$entity->getId()}' name='quantity' value='{$entity->getQuantity()}'>";
            $data .= "</td>";
            $data .= "<td class='span1' id='subTotal-{$entity->getid()}'>{$entity->getSubTotal()}</td>";
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
