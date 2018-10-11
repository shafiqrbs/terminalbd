<?php

namespace Appstore\Bundle\ElectionBundle\Repository;
use Appstore\Bundle\DomainUserBundle\Entity\Customer;
use Appstore\Bundle\ElectionBundle\Entity\ElectionConfig;
use Appstore\Bundle\HospitalBundle\Entity\Invoice;
use Appstore\Bundle\InventoryBundle\Entity\Sales;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * CustomerRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ElectionCampaignAnalysisRepository extends EntityRepository
{

	public function getAnalysisBaseEvent(ElectionConfig $config){

		$qb = $this->createQueryBuilder('e');
		$qb->join('e.analysisType','t');
		$qb->select('t.name as analysisName , COUNT(e.id) as countId');
		$qb->where('e.electionConfig='.$config->getId());
		$qb->andWhere("e.status = :status");
		$qb->setParameter('status', 1);
		$qb->groupBy('t.name');
		$results = $qb->getQuery()->getArrayResult();
		return $results;
	}

	public function getPriorityBaseEvent(ElectionConfig $config){

		$qb = $this->createQueryBuilder('e');
		$qb->join('e.priority','t');
		$qb->select('t.name as priorityName , COUNT(e.id) as countId');
		$qb->where('e.electionConfig='.$config->getId());
		$qb->andWhere("e.status = :status");
		$qb->setParameter('status', 1);
		$qb->groupBy('t.name');
		$results = $qb->getQuery()->getArrayResult();
		return $results;
	}

	public function getLocationBaseEvent(ElectionConfig $config){

		$qb = $this->createQueryBuilder('e');
		$qb->join('e.location','t');
		$qb->select('t.name as locationName , COUNT(e.id) as countId');
		$qb->where('e.electionConfig='.$config->getId());
		$qb->andWhere("e.status = :status");
		$qb->setParameter('status', 1);
		$qb->groupBy('t.name');
		$results = $qb->getQuery()->getArrayResult();
		return $results;
	}

}
