<?php

namespace Appstore\Bundle\ElectionBundle\Repository;
use Appstore\Bundle\ElectionBundle\Entity\ElectionConfig;
use Appstore\Bundle\ElectionBundle\Entity\ElectionSetup;
use Appstore\Bundle\ElectionBundle\Entity\ElectionVoteCenter;
use Appstore\Bundle\ElectionBundle\Entity\ElectionVoteCenterMember;
use Doctrine\ORM\EntityRepository;


/**
 * ElectionVoteCenterRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ElectionVoteCenterRepository extends EntityRepository
{

	protected function handleSearchBetween($qb,$data)
	{
		if(!empty($data))
		{

			$keyword =    isset($data['keyword'])? $data['keyword'] :'';
			$thana =    isset($data['thana'])? $data['thana'] :'';
			$union =    isset($data['union'])? $data['union'] :'';
			$voteCenter =    isset($data['voteCenter'])? $data['voteCenter'] :'';
			$district =    isset($data['district'])? $data['district'] :'';

			if (!empty($keyword)) {
				$qb->andWhere("e.name LIKE :name");
				$qb->setParameter('name','%'. $keyword.'%');
				$qb->orWhere("e.mobile LIKE :mobile");
				$qb->setParameter('mobile','%'. $keyword.'%');
			}

			if (!empty($district)) {
				$qb->andWhere("e.district LIKE :district");
				$qb->setParameter('district','%'. $district.'%');
			}

			if (!empty($thana)) {
				$val = explode(',',$thana);
				$name = $val[0];
				$qb->andWhere($qb->expr()->like("e.thana", "'%$name%'"  ));
			}

			if (!empty($union)) {
				$val = explode(',',$union);
				$name = $val[0];
				$parent = $val[1];
				$qb->andWhere($qb->expr()->like("e.memberUnion", "'%$name%'"  ));
				$qb->andWhere($qb->expr()->like("e.thana", "'%$parent%'"  ));

			}

			if (!empty($voteCenter)) {
				$val = explode(',',$voteCenter);
				$name = $val[0];
				$parent = $val[1];
				$qb->andWhere($qb->expr()->like("e.voteCenterName", "'%$name%'"  ));
				$qb->andWhere($qb->expr()->like("e.memberUnion", "'%$parent%'"  ));

			}

		}

	}

	public function findVoteCenter(ElectionConfig $config , $data , $type = '')
	{
		$voteCenter =    isset($data['voteCenter'])? $data['voteCenter'] :'';
		$setup = $config ->getSetup()->getId();
		$qb = $this->createQueryBuilder('e');
		$qb->where("e.electionSetup = :setup");
		$qb->setParameter('setup', $setup);
		if (!empty($voteCenter)) {
			$val = explode(',',$voteCenter);
			$name = $val[0];
			$parent = $val[1];
			$qb->andWhere($qb->expr()->like("e.voteCenterName", "'%$name%'"  ));
			$qb->andWhere($qb->expr()->like("e.memberUnion", "'%$parent%'"  ));

		}
		$result = $qb->getQuery()->getOneOrNullResult();
		return  $result;

	}



	public function findWithSearch(ElectionConfig $config , $data , $type = '')
	{
		$setup = $config ->getSetup()->getId();

		$sort = isset($data['sort'])? $data['sort'] :'e.voteCenterName';
		$direction = isset($data['direction'])? $data['direction'] :'ASC';
		$qb = $this->createQueryBuilder('e');
		$qb->where("e.electionSetup = :setup");
		$qb->setParameter('setup', $setup);
		$this->handleSearchBetween($qb,$data);
		$qb->orderBy("{$sort}",$direction);
		$qb->getQuery();
		return  $qb;

	}

	public function getUnionWiseVoter(ElectionConfig $config,$data){

		$thana =    isset($data['thana'])? $data['thana'] :'';
		$qb = $this->createQueryBuilder('e');
		$qb->select('e.memberUnion as unionName, e.thana as thana,COUNT(e.id) as totalCenter,SUM(e.totalVoter) as totalVoter,SUM(e.maleVoter) as maleVoter,SUM(e.femaleVoter) as femaleVoter,SUM(e.otherVoter) as otherVoter');
		$qb->where('e.electionConfig='.$config->getId());
		$qb->andWhere("e.status = :status");
		if (!empty($thana)) {
			$val = explode(',',$thana);
			$name = $val[0];
			$qb->andWhere($qb->expr()->like("e.thana", "'%$name%'"  ));
		}
		$qb->setParameter('status', 1);
		$qb->groupBy('e.memberUnion');
		$results = $qb->getQuery()->getArrayResult();
		return $results;
	}


	public function getUnionBaseVoteCenter(ElectionConfig $config)
	{

		$qb = $this->createQueryBuilder('e');
		$qb->join('e.location','t');
		$qb->leftJoin('e.centerMembers','members');
		$qb->join('t.parent','parent');
		$qb->select('parent.name as locationName , COUNT(e.id) as countCenter, COUNT(members.id) as countMember');
		$qb->where('e.electionConfig='.$config->getId());
		$qb->andWhere("e.status = :status");
		$qb->setParameter('status', 1);
		$qb->andWhere("members.personType = :person");
		$qb->setParameter('person', 'agent');
		$qb->groupBy('parent.name');
		$results = $qb->getQuery()->getArrayResult();
		return $results;

	}


	public function updateTotalVote(ElectionSetup $setup)
	{

		$qb = $this->createQueryBuilder('e');
		$qb->select('SUM(e.resultTotalVote) as resultTotalVote');
		$qb->addSelect('SUM(e.resultInvalidVote) as resultInvalidVote');
		$qb->addSelect('SUM(e.resultMaleVote) as resultMaleVote');
		$qb->addSelect('SUM(e.resultFemaleVote) as resultFemaleVote');
		$qb->addSelect('SUM(e.resultOtherVote) as resultOtherVote');
		$qb->where('e.electionSetup = :electionSetup');
		$qb->setParameter('electionSetup', $setup->getId());
		$result  = $qb->getQuery()->getOneOrNullResult();
		return $result;
	}

	public function castVoteCenter(ElectionSetup $setup)
	{
		$process = array('Active','Hold');
		$qb = $this->createQueryBuilder('e');
		$qb->addSelect('process, COUNT(e.process) as totalVoteCenter');
		$qb->where('e.electionSetup ='.$setup->getId());
		$qb->groupBy('e.process');
		$result  = $qb->getQuery()->getArrayResult();
		return $result;
	}

	public function castVoteCenterCount(ElectionSetup $setup)
	{
		$process = array('Hold','Rejected','Active');
		$qb = $this->createQueryBuilder('e');
		$qb->addSelect('COUNT(e.process) as process');
		$qb->where('e.electionSetup ='.$setup->getId());
		$qb->andWhere("e.process IN (:process)");
		$qb->setParameter('process', $process);
		$result  = $qb->getQuery()->getOneOrNullResult();
		return $result['process'];

	}


	public function getMemberLists(ElectionVoteCenter $committee)
	{
		$entities = $committee->getCenterMembers();
		$data = '';
		$i = 1;

		/* @var $entity ElectionVoteCenterMember */

		foreach ($entities as $entity) {
			if ( $entity->getPersonType() == 'agent' ) {
				$data .= "<tr id='remove-{$entity->getId()}'>";
				$data .= "<td>{$entity->getBoothNo()}</td>";
				$data .= "<td>{$entity->getMember()->getNid()}</td>";
				$data .= "<td>{$entity->getMember()->getName()}</td>";
				$data .= "<td><a href='tel:+88 {$entity->getMember()->getMobile()}'>{$entity->getMember()->getMobile()}</a></td>";
				$data .= "<td>{$entity->getMember()->getLocation()->getName()}</td>";
				$data .= "<td>{$entity->getMember()->getVoteCenter()->getName()}</td>";
				$data .= "<td>{$entity->getMember()->getLocation()->wardName()}</td>";
				$data .= "<td>{$entity->getMember()->getLocation()->unionName()}</td>";
				$data .= "<td>{$entity->getMember()->getLocation()->thanaName()}</td>";
				$data .= "<td>";
				$data .= "<a data-id='{$entity->getId()}' data-url='/election/vote-center/{$entity->getId()}/member-delete' href='javascript:' class='btn red mini delete' ><i class='icon-trash'></i></a></td>";
				if ( $entity->isMaster() == 1) {
				$data .= "<a  href='javascript:' class='btn blue mini' ><i class='icon-user'></i> Lead</a>";
				}
				$data .= "</td>";
				$data .= "</tr>";
				$i ++;
			}
		}
		return $data;
	}

	public function getPoolingLists(ElectionVoteCenter $committee)
	{
		$entities = $committee->getCenterMembers();
		$data = '';
		$i = 1;

		/* @var $entity ElectionVoteCenterMember */

		foreach ($entities as $entity) {
			if ( $entity->getPersonType() == 'pooling' ) {
				$data .= "<tr id='remove-{$entity->getId()}'>";
				$data .= "<td>{$entity->getBoothNo()}</td>";
				$data .= "<td>{$entity->getPoolingOfficer()}</td>";
				$data .= "<td>{$entity->getPoolingMobile()}</td>";
				$data .= "<td><a data-id='{$entity->getId()}' data-url='/election/vote-center/{$entity->getId()}/member-delete' href='javascript:' class='btn red mini delete' ><i class='icon-trash'></i></a></td>";
				$data .= '</tr>';
				$i ++;
			}
		}
		return $data;
	}

	public function searchAutoComplete(ElectionConfig $config, $q)
	{
		$query = $this->createQueryBuilder('e');
		$query->select('e.id as id');
		$query->addSelect('e.voteCenterName AS text');
		$query->where($query->expr()->like("e.voteCenterName", "'$q%'"  ));
		$query->andWhere("e.electionSetup = :config");
		$query->setParameter('config', $config->getSetup()->getId());
		$query->orderBy('e.voteCenterName', 'ASC');
		$query->setMaxResults( '10' );
		return $query->getQuery()->getResult();

	}


}
