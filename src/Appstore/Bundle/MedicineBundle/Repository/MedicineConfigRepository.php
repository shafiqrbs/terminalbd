<?php

namespace Appstore\Bundle\MedicineBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;


/**
 * DpsInvoiceParticularRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MedicineConfigRepository extends EntityRepository
{
    public function medicineReset(GlobalOption $option)
    {

        $em = $this->_em;
        $config = $option->getMedicineConfig()->getId();

        $sales = $em->createQuery('DELETE MedicineBundle:MedicineSales e WHERE e.medicineConfig = '.$config);
        $sales->execute();

        $purchase = $em->createQuery('DELETE MedicineBundle:MedicinePrepurchase e WHERE e.medicineConfig = '.$config);
        $purchase->execute();

        $purchase = $em->createQuery('DELETE MedicineBundle:MedicinePurchase e WHERE e.medicineConfig = '.$config);
        $purchase->execute();

        $house = $em->createQuery('DELETE MedicineBundle:MedicineStockHouse e WHERE e.medicineConfig = '.$config);
        $house->execute();

        $damage = $em->createQuery('DELETE MedicineBundle:MedicineDamage e WHERE e.medicineConfig = '.$config);
        $damage->execute();

        $adjustment = $em->createQuery('DELETE MedicineBundle:MedicineStockAdjustment e WHERE e.medicineConfig = '.$config);
        $adjustment->execute();

        $android = $em->createQuery('DELETE MedicineBundle:MedicineAndroidProcess e WHERE e.medicineConfig = '.$config);
        $android->execute();

        $qb = $this->_em->createQueryBuilder();
        $q = $qb->update('MedicineBundle:MedicineStock', 's')
            ->set('s.remainingQuantity', '?1')
            ->set('s.purchaseQuantity', '?2')
            ->set('s.purchaseReturnQuantity', '?3')
            ->set('s.salesQuantity', '?4')
            ->set('s.salesReturnQuantity', '?5')
            ->set('s.damageQuantity', '?6')
            ->set('s.minQuantity', '?9')
            ->set('s.averageSalesPrice', '?12')
            ->set('s.openingQuantity', '?13')
            ->set('s.adjustmentQuantity', '?14')
            ->set('s.bonusAdjustment', '?15')
            ->where('s.medicineConfig = ?10')
            ->setParameter(1, 0)
            ->setParameter(2, 0)
            ->setParameter(3, 0)
            ->setParameter(4, 0)
            ->setParameter(5, 0)
            ->setParameter(6, 0)
            ->setParameter(9, 0)
            ->setParameter(12, 0)
            ->setParameter(13, 0)
            ->setParameter(14, 0)
            ->setParameter(15, 0)
            ->setParameter(10, $config)
            ->getQuery();
        $q->execute();
    }

    public function medicineDelete(GlobalOption $option)
    {

        $em = $this->_em;
        $config = $option->getMedicineConfig()->getId();

        $sales = $em->createQuery('DELETE MedicineBundle:MedicineSales e WHERE e.medicineConfig = '.$config);
        $sales->execute();

        $sales = $em->createQuery('DELETE MedicineBundle:MedicineSalesReturn e WHERE e.medicineConfig = '.$config);
        $sales->execute();

        $sales1 = $em->createQuery('DELETE MedicineBundle:MedicineImport e WHERE e.medicineConfig = '.$config);
        $sales1->execute();

        $sales = $em->createQuery('DELETE MedicineBundle:MedicineSalesReturnInvoice e WHERE e.medicineConfig = '.$config);
        $sales->execute();

        $purchase = $em->createQuery('DELETE MedicineBundle:MedicinePrepurchase e WHERE e.medicineConfig = '.$config);
        $purchase->execute();

        $purchase = $em->createQuery('DELETE MedicineBundle:MedicinePurchaseReturn e WHERE e.medicineConfig = '.$config);
        $purchase->execute();

        $purchase = $em->createQuery('DELETE MedicineBundle:MedicinePurchase e WHERE e.medicineConfig = '.$config);
        $purchase->execute();

        $house = $em->createQuery('DELETE MedicineBundle:MedicineStockHouse e WHERE e.medicineConfig = '.$config);
        $house->execute();

        $house = $em->createQuery('DELETE MedicineBundle:MedicineStockHouse e WHERE e.medicineConfig = '.$config);
        $house->execute();

        $damage = $em->createQuery('DELETE MedicineBundle:MedicineDamage e WHERE e.medicineConfig = '.$config);
        $damage->execute();

        $adjustment = $em->createQuery('DELETE MedicineBundle:MedicineStockAdjustment e WHERE e.medicineConfig = '.$config);
        $adjustment->execute();

        $android = $em->createQuery('DELETE MedicineBundle:MedicineAndroidProcess e WHERE e.medicineConfig = '.$config);
        $android->execute();

        $MedicineStock = $em->createQuery('DELETE MedicineBundle:MedicineStock e WHERE e.medicineConfig = '.$config);
        $MedicineStock->execute();

        $MedicineVendor = $em->createQuery('DELETE MedicineBundle:MedicineVendor e WHERE e.medicineConfig = '.$config);
        $MedicineVendor->execute();

    }

    public function discountPercentList()
    {
        $array = array(
            1=>1,
            2=>2,
            3=>3,
            4=>4,
            5=>5,
            6=>6,
            7=>7,
            8=>8,
            9=>9,
            10=>10,
            11=>11,
            12=>12,
            13=>13,
            14=>14,
            15=>15,
            16=>16,
            17=>17,
            18=>18,
            19=>19,
            20=>20,
            21=>21,
            22=>22,
            23=>23,
            24=>24,
            25=>25,
            26=>26,
            27=>27,
            28=>28,
            29=>29,
            30=>30
        );

        return $array;


    }
}
