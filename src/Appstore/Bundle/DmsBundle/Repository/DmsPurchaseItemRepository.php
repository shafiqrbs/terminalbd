<?php

namespace Appstore\Bundle\DmsBundle\Repository;
use Appstore\Bundle\DmsBundle\Entity\HmsPurchase;
use Appstore\Bundle\DmsBundle\Entity\DmsPurchaseItem;
use Appstore\Bundle\DmsBundle\Entity\Invoice;
use Appstore\Bundle\DmsBundle\Entity\DmsParticular;
use Doctrine\ORM\EntityRepository;


/**
 * DmsPurchaseItemRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DmsPurchaseItemRepository extends EntityRepository
{

    public function getPurchaseAveragePrice(DmsParticular $particular)
    {

        $qb = $this->_em->createQueryBuilder();
        $qb->from('DmsBundle:DmsPurchaseItem','e');
        $qb->select('AVG(e.purchasePrice) AS avgPurchasePrice');
        $qb->where('e.particular = :particular')->setParameter('particular', $particular) ;
        $res = $qb->getQuery()->getOneOrNullResult();
        if(!empty($res)){
            $particular->setPurchaseAverage($res['avgPurchasePrice']);
            $this->_em->persist($particular);
            $this->_em->flush($particular);
        }
    }

    public function insertPurchaseItems($invoice, $data)
    {
        $particular = $this->_em->getRepository('DmsBundle:DmsParticular')->find($data['particularId']);
        $em = $this->_em;
        $entity = new DmsPurchaseItem();
        $entity->setPurchase($invoice);
        $entity->setDmsParticular($particular);
        $entity->setSalesPrice($particular->getPrice());
        $entity->setPurchasePrice($particular->getPurchasePrice());
        $entity->setQuantity($data['quantity']);
        $entity->setPurchaseSubTotal($data['quantity'] * $particular->getPurchasePrice());
        $em->persist($entity);
        $em->flush();
        $this->getPurchaseAveragePrice($particular);

    }

    public function getPurchaseItems(HmsPurchase $sales)
    {
        $entities = $sales->getPurchaseItems();
        $data = '';
        $i = 1;
        foreach ($entities as $entity) {
            $data .= '<tr id="remove-'. $entity->getId() .'">';
            $data .= '<td class="span1" >' . $i . '</td>';
            $data .= '<td class="span1" >' . $entity->getDmsParticular()->getDmsParticularCode() . '</td>';
            $data .= '<td class="span4" >' . $entity->getDmsParticular()->getName() . '</td>';
            $data .= '<td class="span1" >' . $entity->getQuantity() . '</td>';
            $data .= '<td class="span1" >' . $entity->getSalesPrice() . '</td>';
            $data .= '<td class="span1" >' . $entity->getPurchasePrice() . '</td>';
            $data .= '<td class="span1" >' . $entity->getPurchaseSubTotal() . '</td>';
            $data .= '<td class="span1" >
                     <a id="'.$entity->getId(). '" title="Are you sure went to delete ?" data-url="/hms/purchase/' . $sales->getId() . '/' . $entity->getId() . '/particular-delete" href="javascript:" class="btn red mini delete" ><i class="icon-trash"></i></a>
                     </td>';
            $data .= '</tr>';
            $i++;
        }
        return $data;
    }

    public function invoiceDmsParticularLists($hospital,$data = array()){

        $invoice = isset($data['invoice'])? $data['invoice'] :'';
        $particular = isset($data['particular'])? $data['particular'] :'';
        $category = isset($data['category'])? $data['category'] :'';

        $qb = $this->createQueryBuilder('e');
        $qb->select('e');
        $qb->join('e.invoice','invoice');
        $qb->join('e.particular','particular');
        $qb->join('particular.category','category');
        $qb->where('particular.service = :service')->setParameter('service', 1) ;
        /*            $qb->andWhere('invoice.hospitalConfig = :hospital')->setParameter('hospital', $hospital) ;
                    $qb->andWhere('particular.process IN(:process)');
                    $qb->setParameter('process',array_values(array('In-progress','Damage','Impossible')));
                    if (!empty($invoice)) {
                        $qb->andWhere($qb->expr()->like("invoice.invoice", "'%$invoice%'"  ));
                    }
                    if (!empty($particular)) {
                        $qb->andWhere('particular.name = :partName')->setParameter('partName', $particular) ;
                    }
                    if (!empty($category)) {
                        $qb->andWhere('category.name = :catName')->setParameter('catName', $category) ;
                    }*/
        $qb->orderBy('e.updated','DESC');
        $qb->getQuery();
        return  $qb;

    }
}
