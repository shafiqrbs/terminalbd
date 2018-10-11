<?php

namespace Appstore\Bundle\ElectionBundle\Repository;
use Appstore\Bundle\DomainUserBundle\Entity\Customer;
use Appstore\Bundle\ElectionBundle\Entity\ElectionCommittee;
use Appstore\Bundle\ElectionBundle\Entity\ElectionConfig;
use Appstore\Bundle\ElectionBundle\Entity\ElectionLocation;
use Appstore\Bundle\ElectionBundle\Entity\ElectionVoteCenter;
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
class ElectionMemberRepository extends EntityRepository
{
    public function checkDuplicateCustomer(ElectionConfig $config, $mobile)
    {
        $em = $this->_em;
        $entity = $em->getRepository('ElectionBundle:ElectionMember')->findOneBy(array('config' => $config,'mobile' => $mobile));
        if($entity) {
            return false;
        }else{
            return true;
        }

    }

    public function getGenderBaseMember(ElectionConfig $config){

	    $qb = $this->createQueryBuilder('e');
	    $qb->select('e.gender as gender, COUNT(e.id) as countId');
	    $qb->where('e.electionConfig='.$config->getId());
	    $qb->andWhere("e.memberType = :type");
	    $qb->setParameter('type', 'member');
	    $qb->andWhere("e.status = :status");
	    $qb->setParameter('status', 1);
	    $qb->groupBy('e.gender');
	    $results = $qb->getQuery()->getArrayResult();
	    return $results;
    }

    public function getUnionWiseMember(ElectionConfig $config){

	    $qb = $this->createQueryBuilder('e');
	    $qb->select('e.memberUnion as unionName,COUNT(e.id) as countId');
	    $qb->where('e.electionConfig='.$config->getId());
	    $qb->andWhere("e.memberType = :type");
	    $qb->setParameter('type', 'member');
	    $qb->andWhere("e.status = :status");
	    $qb->setParameter('status', 1);
	    $qb->groupBy('e.memberUnion');
	    $results = $qb->getQuery()->getArrayResult();
	    return $results;
    }

	public function getWardWiseMember(ElectionConfig $config){

	    $qb = $this->createQueryBuilder('e');
	    $qb->select('e.ward as wardName,e.memberUnion as unionName,COUNT(e.id) as countId');
	    $qb->where('e.electionConfig='.$config->getId());
		$qb->andWhere("e.memberType = :type");
		$qb->setParameter('type', 'member');
	    $qb->andWhere("e.status = :status");
	    $qb->setParameter('status', 1);
		$qb->groupBy('e.ward, e.memberUnion');
	    $results = $qb->getQuery()->getArrayResult();
	    return $results;

    }


    public function getImportCount(ElectionConfig $config,$process){


	    $qb = $this->createQueryBuilder('e');
	    $qb->select('COUNT(e.id) as countId');
	    $qb->where('e.electionConfig='.$config->getId());
	    $qb->andWhere("e.process = :process");
	    $qb->setParameter('process', $process);
	    $results = $qb->getQuery()->getOneOrNullResult();
	    return $results['countId'];
	}

    public function getLocationBaseMembers(ElectionCommittee $committee)
    {
	    /* @var $location ElectionLocation */
    	$location = $committee->getLocation();

	    $config = $committee->getElectionConfig()->getId();
	    $qb = $this->createQueryBuilder('e');
	    $orX = $qb->expr()->orX();
	    $orX->add("node.path like '%" .$location->getId(). "/%'");
	    $orX->add("center.path like '%" .$location->getId(). "/%'");
	    $qb->leftJoin('e.location','node');
	    $qb->leftJoin('e.voteCenter','center');
		$qb->orderBy('node.level, node.name', 'ASC');
		$qb->where('e.electionConfig='.$config);
		$qb->andWhere($orX);
	    $results = $qb->getQuery()->getResult();
	    $choices = [];
	    foreach ($results as $product) {
		    $choices[$product->getId()] =  $product->getName().' [ '.$product->getLocation()->villageName().' ]';
	    }
	    return $choices;
    }

	public function getVotecenterBaseMembers(ElectionVoteCenter $committee)
	{
		/* @var $location ElectionLocation */
		$location = $committee->getLocation();

		$config = $committee->getElectionConfig()->getId();
		$qb = $this->createQueryBuilder('e');
		$orX = $qb->expr()->orX();
		$orX->add("center.path like '%" .$location->getId(). "/%'");
		$qb->leftJoin('e.location','node');
		$qb->leftJoin('e.voteCenter','center');
		$qb->orderBy('node.level, node.name', 'ASC');
		$qb->where('e.electionConfig='.$config);
		$qb->andWhere($orX);
		$results = $qb->getQuery()->getResult();
		$choices = [];
		foreach ($results as $product) {
			$choices[$product->getId()] =  $product->getName().' [ '.$product->getLocation()->villageName().' ]';
		}
		return $choices;
	}

    public function findWithSearch( $config , $data , $type = 'member')
    {
	    $sort = isset($data['sort'])? $data['sort'] :'e.name';
	    $direction = isset($data['direction'])? $data['direction'] :'ASC';
        $qb = $this->createQueryBuilder('e');
        $qb->where("e.electionConfig = :config");
        $qb->setParameter('config', $config);
	    $qb->andWhere("e.memberType = :type");
        $qb->setParameter('type', $type);
	    $qb->leftJoin('e.location','node');
	    $qb->leftJoin('e.voteCenter','center');
	    $this->handleSearchBetween($qb,$data);
	    $qb->orderBy("{$sort}",$direction);
        $qb->getQuery();
        return  $qb;

    }

    protected function handleSearchBetween($qb,$data)
    {
        if(!empty($data))
        {

            $mobile =    isset($data['mobile'])? $data['mobile'] :'';
            $name =    isset($data['name'])? $data['name'] :'';
            $thana =    isset($data['thana'])? $data['thana'] :'';
            $union =    isset($data['union'])? $data['union'] :'';
            $ward =    isset($data['ward'])? $data['ward'] :'';
            $village =    isset($data['village'])? $data['village'] :'';
            $voteCenter =    isset($data['voteCenter'])? $data['voteCenter'] :'';
            $district =    isset($data['district'])? $data['district'] :'';
            if (!empty($mobile)) {
                $qb->andWhere("e.mobile = :mobile");
                $qb->setParameter('mobile', $mobile);
            }

	        if (!empty($name)) {
		        $qb->andWhere("e.mobile LIKE :name");
		        $qb->setParameter('name','%'. $name.'%');
	        }

	        if (!empty($district)) {
                $qb->andWhere("e.district LIKE :district");
                $qb->setParameter('district','%'. $district.'%');
            }

	        if (!empty($thana)) {
                $qb->andWhere("e.thana LIKE :thana");
                $qb->setParameter('thana','%'. $thana.'%');
            }

	        if (!empty($union)) {
		        $qb->andWhere("e.memberUnion LIKE :union");
		        $qb->setParameter('union','%'. $union.'%');
	        }

            if (!empty($ward)) {
		        $qb->andWhere("e.ward LIKE :ward");
		        $qb->setParameter('ward','%'. $ward.'%');
	        }

            if (!empty($village)) {
		        $qb->andWhere("e.village LIKE :village");
		        $qb->setParameter('village','%'. $village.'%');
	        }
	        if (!empty($voteCenter)) {
		        $qb->andWhere("e.voteCenterName LIKE :voteCenter");
		        $qb->setParameter('voteCenter','%'.$voteCenter.'%');
	        }

        }

    }


    public function insertSMSCustomer($data)
    {
        $em = $this->_em;
        $entity = $em->getRepository('DomainUserBundle:Customer')->findOneBy(array('config'=>$data['config'],'mobile' => $data['mobile']));
        if($entity){
            return $entity;
        }else{
            $entity = new Customer();
            $config = $this->_em->getRepository('SettingToolBundle:GlobalOption')->find($data['config']);
            $entity->setMobile($data['mobile']);
            $entity->setName($data['name']);
            $entity->setGlobalOption($config);
            $entity->setCustomerType('sms');
            $em->persist($entity);
            $em->flush();
            return $entity;
        }

    }

    public function searchAutoComplete(ElectionConfig $config, $q , $type='member')
    {
        $query = $this->createQueryBuilder('e');
        $query->select('e.mobile as id');
        $query->addSelect('CONCAT(e.mobile, \' - \', e.name) AS text');
        $query->where($query->expr()->like("e.mobile", "'$q%'"  ));
        $query->orWhere($query->expr()->like("e.name", "'%$q%'"  ));
        $query->andWhere("e.electionConfig = :config");
        $query->setParameter('config', $config->getId());
        $query->andWhere("e.memberType = :type");
        $query->setParameter('type', $type);
        $query->orderBy('e.name', 'ASC');
        $query->groupBy('e.mobile');
        $query->setMaxResults( '20' );
        return $query->getQuery()->getResult();

    }

     public function searchMobileAutoComplete(GlobalOption $config, $q, $type = 'member')
    {
        $query = $this->createQueryBuilder('e');

        $query->select('e.mobile as id');
        $query->addSelect('e.id as e');
        $query->addSelect('CONCAT(e.mobile, \'-\', e.name) AS text');
        $query->where($query->expr()->like("e.mobile", "'$q%'"  ));
        $query->andWhere("e.config = :config");
        $query->setParameter('config', $config->getId());
	    $query->andWhere("e.memberType = :type");
	    $query->setParameter('type', $type);
        $query->orderBy('e.mobile', 'ASC');
        $query->groupBy('e.mobile');
        $query->setMaxResults( '10' );
        return $query->getQuery()->getResult();

    }

    public function searchCustomerAutoComplete(GlobalOption $config, $q, $type = 'member')
    {
        $query = $this->createQueryBuilder('e');
        $query->select('e.name as id');
        $query->addSelect('e.id as name');
        $query->addSelect('e.name as text');
        $query->where($query->expr()->like("e.mobile", "'$q%'"  ));
        $query->andWhere("e.config = :config");
        $query->setParameter('config', $config->getId());
	    $query->andWhere("e.memberType = :type");
	    $query->setParameter('type', $type);
        $query->orderBy('e.name', 'ASC');
        $query->groupBy('e.mobile');
        $query->setMaxResults( '10' );
        return $query->getQuery()->getResult();

    }

    public function searchAutoCompleteName(GlobalOption $config, $q)
    {
        $query = $this->createQueryBuilder('e');
        $query->select('e.name as id');
        $query->addSelect('e.id as e');
        $query->addSelect('e.name as text');
        $query->where($query->expr()->like("e.name", "'$q%'"  ));
        $query->andWhere("e.config = :config");
        $query->setParameter('config', $config->getId());
        $query->groupBy('e.name');
        $query->orderBy('e.name', 'ASC');
        $query->setMaxResults( '10' );
        return $query->getQuery()->getResult();

    }

    public function searchAutoCompleteCode(GlobalOption $config, $q)
    {
        $query = $this->createQueryBuilder('e');

        $query->select('e.mobile as id');
        $query->addSelect('e.id as e');
        $query->addSelect('e.eId as text');
        //$query->addSelect('CONCAT(e.eId, " - ", e.name) AS text');
        $query->where($query->expr()->like("e.eId", "'$q%'"  ));
        $query->andWhere("e.config = :config");
        $query->setParameter('config', $config->getId());
        $query->orderBy('e.eId', 'ASC');
        $query->setMaxResults( '10' );
        return $query->getQuery()->getResult();
    }



}
