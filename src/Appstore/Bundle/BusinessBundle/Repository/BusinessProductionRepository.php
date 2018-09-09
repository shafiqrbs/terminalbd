<?php

namespace Appstore\Bundle\BusinessBundle\Repository;
use Appstore\Bundle\BusinessBundle\Entity\BusinessParticular;
use Appstore\Bundle\BusinessBundle\Entity\BusinessProduction;
use Appstore\Bundle\BusinessBundle\Entity\BusinessProductionElement;
use Appstore\Bundle\BusinessBundle\Entity\BusinessProductionExpense;
use Doctrine\ORM\EntityRepository;


/**
 * ProductionElementRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BusinessProductionRepository extends EntityRepository
{


	public function findWithSearch($config, $data){

		$name = isset($data['name'])? $data['name'] :'';
		$category = isset($data['category'])? $data['category'] :'';
		$type = isset($data['type'])? $data['type'] :'';
		$qb = $this->createQueryBuilder('p');
		$qb ->join('p.businessParticular','e');
		$qb->where('e.businessConfig = :config')->setParameter('config', $config) ;
		if (!empty($name)) {
			$qb->andWhere($qb->expr()->like("e.name", "'%$name%'"  ));
		}
		if(!empty($category)){
			$qb->andWhere("e.category = :category");
			$qb->setParameter('category', $category);
		}
		if(!empty($type)){
			$qb->andWhere("e.businessParticularType = :type");
			$qb->setParameter('type', $type);
		}
		$qb->orderBy('e.name','ASC');
		$qb->getQuery();
		return  $qb;
	}

	public function insertProduction($particular, $data)
    {
        $em = $this->_em;
	    $entity = new BusinessProduction();
	    $entity->setBusinessParticular($particular);
	    $particular = $this->_em->getRepository('BusinessBundle:BusinessParticular')->find($data['particularId']);
	    $entity->setBusinessParticular($particular);
	    $entity->setPurchasePrice($data['purchasePrice']);
	    $entity->setSalesPrice($data['salesPrice']);
	    $entity->setQuantity($data['quantity']);
	    $entity->setPurchaseSubTotal($data['purchasePrice'] * $data['quantity']);
	    $entity->setSalesSubTotal($data['salesPrice'] * $data['quantity']);
	    $em->persist($entity);
	    $em->flush();
    }


    public function particularProductionElements(BusinessParticular $particular)
    {
        $entities = $particular->getProductionElements();
        $data = '';
        $i = 1;

        /* @var $entity BusinessProductionElement */

        foreach ($entities as $entity) {

            $subTotal = $entity->getSalesPrice() * $entity->getQuantity() ;
	        $unit = !empty($entity->getParticular()->getUnit() && !empty($entity->getParticular()->getUnit()->getName())) ? $entity->getParticular()->getUnit()->getName():'Unit';

            $data .= "<tr id='remove-{$entity->getId()}'>";
            $data .= "<td class='span1' >{$i}</td>";
            $data .= "<td class='span1' >{$entity->getParticular()->getParticularCode()}</td>";
            $data .= "<td class='span3' >{$entity->getParticular()->getName()}</td>";
            $data .= "<td class='span1' >{$entity->getPurchasePrice()}</td>";
            $data .= "<td class='span1' >{$entity->getSalesPrice()}</td>";
            $data .= "<td class='span1' >{$entity->getQuantity()}</td>";
            $data .= "<td class='span1' >{$unit}</td>";
            $data .= "<td class='span1' >{$subTotal}</td>";
            $data .= "<td class='span1' ><a id='{$entity->getId()}' data-url='/business/product-production/{$particular->getId()}/{$entity->getId()}/delete' href='javascript:' class='btn red mini delete' ><i class='icon-trash'></i></a></td>";
            $data .= '</tr>';
            $i++;
        }
        return $data;
    }




    public function insertStockProductionItem(BusinessProduction $production)
    {
	    
		$em = $this->_em;
        $particular = $production->getBusinessParticular();
	    $qb = $this->createQueryBuilder('e');
	    $qb->select('SUM(e.quantity) AS quantity');
	    $qb->where('e.businessParticular = :particular')->setParameter('particular', $particular->getId());
	    $qnt = $qb->getQuery()->getOneOrNullResult();
	    $productionQnt = ($qnt['quantity'] == 'NULL') ? 0 : $qnt['quantity'];
        $particular->setPurchaseQuantity($productionQnt);
        $em->persist($particular);
        $em->flush();
		$this->_em->getRepository('BusinessBundle:BusinessParticular')->remainingQnt($particular);
	    $this->productionExpense($production,$particular);
    }


	public function productionExpense(BusinessProduction $production,BusinessParticular  $item)
	{

		if(!empty($item->getProductionElements())){

			$productionElements = $item->getProductionElements();

			/* @var $element BusinessProductionElement */

			if($productionElements) {

				foreach ($productionElements as $element) {

					$entity = new BusinessProductionExpense();
					$entity->setBusinessProduction($production);
					$entity->setProductionItem($item);
					$entity->setProductionElement($element->getParticular());
					$entity->setPurchasePrice($element->getPurchasePrice());
					$entity->setSalesPrice($element->getSalesPrice());
					$entity->setQuantity($production->getQuantity());
					$this->_em->persist($entity);
					$this->_em->flush();
					$this->_em->getRepository('BusinessBundle:BusinessParticular')->salesProductionQnt($element);
				}
			}
		}
	}

}
