<?php

namespace Appstore\Bundle\MedicineBundle\Repository;


use Appstore\Bundle\MedicineBundle\Entity\MedicineBrand;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * MedicineCompanyRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */


class MedicineBrandRepository extends EntityRepository
{

    public function findWithSearch($data)
    {
        $name = isset($data['name'])? $data['name'] :'';
        $company = isset($data['company'])? $data['company'] :'';
        $generic = isset($data['generic'])? $data['generic'] :'';
        $sort = isset($data['sort'])? $data['sort'] :'e.name';
        $direction = isset($data['direction'])? $data['direction'] :'ASC';

        $query = $this->createQueryBuilder('e');
        $query->join('e.medicineGeneric','g');
        $query->join('e.medicineCompany','c');
        $query->select('e.id as id','e.price as salesPrice','e.medicineForm as medicineForm','e.strength as strength', 'e.name as name','g.name as genericName','c.name as medicineCompany','e.dar as dar','e.packSize as pack','e.path as path');
        $query->where('e.name IS NOT NULL');
        if($name){
            $query->andWhere($query->expr()->like("e.name", "'$name%'"  ));
        }
        if($generic){
            $query->andWhere($query->expr()->like("g.name", "'$generic%'"  ));
        }
        if($company){
            $query->andWhere($query->expr()->like("c.name", "'$company%'"  ));
        }
        $query->orderBy("{$sort}",$direction);
        $result = $query->getQuery();
        return $result;
    }

    public function getMedicineBrandSearch($data)
    {

        //$name = $data['webName'];
        $name = $data['item']['webName'];
       // $brand = $data['item']['brand'];
        $query = $this->createQueryBuilder('e');
        $query->leftJoin('e.medicineGeneric','g');
        $query->leftJoin('e.medicineCompany','c');
        $query->select('e.*');
        $query->select('e.id','e.price as salesPrice','e.medicineForm','e.strength', 'e.name as webName','g.name as genericName', 'c.name as brand');
        $query->where($query->expr()->like("e.name", "'$name%'"  ));
        $query->orWhere($query->expr()->like("g.name", "'$name%'"  ));
        $query->orWhere($query->expr()->like("c.name", "'$name%'"  ));
        $query->groupBy('e.name');
        $query->orderBy('e.name', 'ASC');
        $result = $query->getQuery()->getArrayResult();
        return $result;
    }

    public function getApiDims(GlobalOption $option,$data)
    {
        $offset = (int)$data['offset'];
        $limit = (int)$data['limit'];

        $qb = $this->createQueryBuilder('e');
        $qb->leftJoin('e.medicineCompany','b');
        $qb->leftJoin('e.medicineGeneric','g');
        $qb->select('e.id as medicineId','e.price as salesPrice','e.medicineForm as medicineForm','e.strength as strength', 'e.name as medicineName', 'e.useFor as useFor');
        $qb->addSelect('g.name as genericName');
        $qb->addSelect( 'b.name as brand');
        $qb->setFirstResult( $offset );
        $qb->setMaxResults( $limit );
        $qb->orderBy('e.name','ASC');
        $result = $qb->getQuery()->getArrayResult();

        $data = array();

        foreach($result as $key => $row) {

            $data[$key]['global_id']            = (int) $option->getId();
            $data[$key]['medicineId']           = (int) $row['medicineId'];
            $data[$key]['medicineName']         = $row['medicineName'];
            $data[$key]['salesPrice']           = $row['salesPrice'];
            $data[$key]['medicineForm']         = $row['medicineForm'];
            $data[$key]['strength']             = $row['strength'];
            $data[$key]['genericName']          = $row['genericName'];
            $data[$key]['brand']                = $row['brand'];
            $data[$key]['useFor']               = $row['useFor'];
        }

        return $data;

    }

    public function updateMedicine()
    {
        $results = $this->findAll();

        /** @var $row MedicineBrand */
        foreach ($results as $row ){
            $company = $this->_em->getRepository('MedicineBundle:MedicineCompany')->findOneBy(array('companyId'=> $row->getCompanyId()));
            $generic = $this->_em->getRepository('MedicineBundle:MedicineGeneric')->findOneBy(array('genericId'=> $row->getGenericId()));
            $row->setMedicineGeneric($generic);
            $row->setMedicineCompany($company);
            $this->_em->persist($row);
            $this->_em->flush();

        }

    }

    public function searchBrandNameAutoComplete($q)
    {
        $query = $this->createQueryBuilder('e');
        $query->select('e.id as id');
        $query->addSelect('e.name AS text');
        $query->where($query->expr()->like("e.name", "'$q%'"  ));
        $query->groupBy('e.name');
        $query->orderBy('e.name', 'ASC');
        $query->setMaxResults( '20' );
        return $query->getQuery()->getResult();

    }

    public function searchCompanyNameAutoComplete($q)
    {
        $query = $this->_em->createQueryBuilder();
        $query->from('MedicineBundle:MedicineCompany','e');
        $query->select('e.id as id');
        $query->addSelect('e.name AS text');
        $query->where($query->expr()->like("e.name", "'$q%'"  ));
        $query->groupBy('e.name');
        $query->orderBy('e.name', 'ASC');
        $query->setMaxResults( '20' );
        return $query->getQuery()->getResult();

    }


    public function searchGenericNameAutoComplete($q)
    {
        $query = $this->_em->createQueryBuilder();
        $query->from('MedicineBundle:MedicineGeneric','g');
        $query->select('g.id as id');
        $query->addSelect('g.name AS text');
        $query->where($query->expr()->like("g.name", "'$q%'"  ));
        $query->groupBy('g.name');
        $query->orderBy('g.name', 'ASC');
        $query->setMaxResults( '20' );
        return $query->getQuery()->getResult();

    }

    public function searchPackSizeAutoComplete($q)
    {
        $query = $this->createQueryBuilder('e');
        $query->select('e.packSize as id');
        $query->addSelect('e.packSize AS text');
        $query->where($query->expr()->like("e.packSize", "'$q%'"  ));
        $query->groupBy('e.packSize');
        $query->orderBy('e.packSize', 'ASC');
        $query->setMaxResults( '20' );
        return $query->getQuery()->getResult();

    }

    public function searchStrengthAutoComplete($q)
    {
        $query = $this->createQueryBuilder('e');
        $query->select('e.strength as id');
        $query->addSelect('e.strength AS text');
        $query->where($query->expr()->like("e.strength", "'$q%'"  ));
        $query->groupBy('e.strength');
        $query->orderBy('e.strength', 'ASC');
        $query->setMaxResults( '20' );
        return $query->getQuery()->getResult();

    }

    public function searchMedicineFormAutoComplete($q)
    {
        $query = $this->createQueryBuilder('e');
        $query->select('e.medicineForm as id');
        $query->addSelect('e.medicineForm AS text');
        $query->where($query->expr()->like("e.medicineForm", "'$q%'"  ));
        $query->groupBy('e.medicineForm');
        $query->orderBy('e.medicineForm', 'ASC');
        $query->setMaxResults( '20' );
        return $query->getQuery()->getResult();

    }


    public function searchMedicineAutoComplete($q)
    {
        $query = $this->createQueryBuilder('e');
        $query->join('e.medicineGeneric','g');
        $query->join('e.medicineCompany','c');
        $query->select('e.id as id');
        $query->addSelect("CASE WHEN (e.strength IS NULL) THEN CONCAT(e.name, ' ', e.medicineForm,' ',c.name)  ELSE CONCAT(e.name, ' ', e.medicineForm,' ',e.strength,' ',c.name)  END as text");
        $query->where($query->expr()->like("e.name", "'$q%'"  ));
        $query->orderBy('e.name', 'ASC');
        $query->setMaxResults( '50' );
        return $query->getQuery()->getResult();

    }

    public function searchGenericAutoComplete($q)
    {
        $query = $this->createQueryBuilder('e');
        $query->join('e.medicineGeneric','g');
        $query->join('e.medicineCompany','c');
        $query->select('e.id as id');
        $query->addSelect("CASE WHEN (e.strength IS NULL) THEN CONCAT(e.name, ' ', e.medicineForm,' ',c.name)  ELSE CONCAT(e.name, ' ', e.medicineForm,' ',e.strength,' ',c.name)  END as text");
        $query->where($query->expr()->like("g.name", "'$q%'"  ));
      //  $query->groupBy('g.name');
        $query->orderBy('e.name', 'ASC');
        $query->setMaxResults( '50' );
        return $query->getQuery()->getResult();
    }

	public function searchMedicineGenericAutoComplete($q)
	{
		$query = $this->createQueryBuilder('e');
		$query->join('e.medicineGeneric','g');
		$query->join('e.medicineCompany','c');
		$query->select('e.id as id');
		//$query->addSelect('CONCAT(e.medicineForm, \' \', e.name, \' \', g.name, \' \', e.strength, \' \', c.name) AS text');
        $query->addSelect("CASE WHEN (e.strength IS NULL) THEN CONCAT(e.medicineForm,' ', e.name,' ',g.name, ' ', c.name)  ELSE CONCAT(e.medicineForm,' ',e.name, ' ',e.strength,' ', g.name,' ',c.name)  END as text");
        $query->where($query->expr()->like("e.name", "'$q%'"  ));
		$query->orWhere($query->expr()->like("g.name", "'$q%'"  ));
	//	$query->groupBy('e.name');
		$query->orderBy('e.name', 'ASC');
		$query->setMaxResults( '50' );
		return $query->getQuery()->getResult();

	}

}
