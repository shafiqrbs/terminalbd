<?php

namespace Appstore\Bundle\AssetsBundle\Repository;
use Appstore\Bundle\AssetsBundle\Entity\Product;
use Appstore\Bundle\AssetsBundle\Entity\ProductLedger;
use \Doctrine\ORM\EntityRepository;

/**
 * ParticularRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProductLedgerRepository extends EntityRepository
{

	public function findWithSearch($data)
	{

		$item = isset($data['item'])? $data['item'] :'';
		$branch = isset($data['branch'])? $data['branch'] :'';
		$category = isset($data['category'])? $data['category'] :'';
		$serialNo = isset($data['serialNo'])? $data['serialNo'] :'';

		$qb = $this->createQueryBuilder('item');
		$qb->join('item.product', 'm');
		$qb->where("item.process IS NOT NULL");
		if (!empty($serialNo)) {
			$qb->andWhere("m.serialNo = :serial");
			$qb->setParameter('serial', $serialNo);
		}
		if (!empty($item)) {
			$qb->andWhere("m.name = :name");
			$qb->setParameter('name', $item);
		}
		if (!empty($category)) {
			$qb->join('item.category', 'c');
			$qb->andWhere("c.name = :category");
			$qb->setParameter('category', $category);
		}
		if (!empty($branch)) {
			$qb->join('item.branch', 'b');
			$qb->andWhere("b.name = :branch");
			$qb->setParameter('branch', $branch);
		}
		$qb->orderBy('item.updated','DESC');
		$qb->getQuery();
		return  $qb;

	}

	public function getLastId($inventory)
	{
		$qb = $this->createQueryBuilder('e');
		$qb->select('count(e.id)');
		$count = $qb->getQuery()->getSingleScalarResult();
		if($count > 0 ){
			return $count+1;
		}else{
			return 1;
		}

	}

	public function searchAutoComplete($q)
	{
		$query = $this->createQueryBuilder('e');
		$query->select('e.name as id');
		$query->addSelect('e.name as text');
		$query->where($query->expr()->like("e.name", "'$q%'"  ));
		$query->groupBy('e.id');
		$query->orderBy('e.name', 'ASC');
		$query->setMaxResults( '10' );
		return $query->getQuery()->getResult();

	}


	public function updateCustomerBalance(ProductLedger $ledger){

		$product = $ledger->getProduct()->getId();
		$qb = $this->createQueryBuilder('e');
		$qb->select('SUM(e.debit) AS debit, SUM(e.credit) AS credit');
		$qb->where("e.product = :product");
		$qb->setParameter('product', $product);
		$result = $qb->getQuery()->getSingleResult();
		$balance = ($result['debit'] -  $result['credit']);
		$ledger->setBalance($balance);
		$this->_em->flush();
		return $ledger;

	}

	public function insertProductLedger(Product $entity)
	{

		$em = $this->_em;
		$accountSales = new ProductLedger();

		$accountSales->setProduct($entity);
		$accountSales->setItem($entity->getItem());
		$accountSales->setCategory($entity->getCategory());
		$accountSales->setBranch($entity->getBranch());
		$accountSales->setDebit($entity->getPurchasePrice());
		$accountSales->setNarration('Receive Product on the opening item');
		$accountSales->setProcess('approved');
		$em->persist($accountSales);
		$em->flush();
		$accountSalesClose = $this->updateCustomerBalance($accountSales);
		return $accountSalesClose;

	}

	public function insertProductDepreciation(Product $entity,$amount)
	{

		$em = $this->_em;
		$accountSales = new ProductLedger();

		$accountSales->setProduct($entity);
		$accountSales->setItem($entity->getItem());
		$accountSales->setCategory($entity->getCategory());
		$accountSales->setBranch($entity->getBranch());
		$accountSales->setCredit($amount);
		$accountSales->setNarration('Receive Product on the opening item');
		$accountSales->setProcess('approved');
		$em->persist($accountSales);
		$em->flush();
		$accountSalesClose = $this->updateCustomerBalance($accountSales);
		return $accountSalesClose;

	}

	public function insertProductService(Product $entity)
	{

		$em = $this->_em;
		$accountSales = new ProductLedger();

		$accountSales->setProduct($entity);
		$accountSales->setItem($entity->getItem());
		$accountSales->setCategory($entity->getCategory());
		$accountSales->setBranch($entity->getBranch());
		$accountSales->setDebit($entity->getPurchasePrice());
		$accountSales->setNarration('Receive Product on the opening item');
		$accountSales->setProcess('approved');
		$em->persist($accountSales);
		$em->flush();
		$accountSalesClose = $this->updateCustomerBalance($accountSales);
		return $accountSalesClose;

	}


}
