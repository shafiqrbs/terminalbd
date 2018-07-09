<?php

namespace Appstore\Bundle\MedicineBundle\Repository;


use Appstore\Bundle\MedicineBundle\Entity\MedicineBrand;
use Doctrine\ORM\EntityRepository;

/**
 * MedicineCompanyRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */


class MedicineBrandRepository extends EntityRepository
{
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
        $query->addSelect('CONCAT(e.medicineForm, \' \', e.name, \' \', g.name, \' \', e.strength, \' \', c.name) AS text');
        $query->where($query->expr()->like("e.name", "'$q%'"  ));
        $query->groupBy('e.name');
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
        $query->addSelect('CONCAT(e.medicineForm, \' \', e.name, \' \', e.strength, \' \', g.name, \' \', c.name) AS text');
        $query->where($query->expr()->like("g.name", "'$q%'"  ));
        $query->groupBy('g.name');
        $query->orderBy('e.name', 'ASC');
        $query->setMaxResults( '50' );
        return $query->getQuery()->getResult();

    }
}
