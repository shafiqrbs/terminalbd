<?php

namespace Appstore\Bundle\MedicineBundle\Repository;
use Appstore\Bundle\MedicineBundle\Entity\MedicineConfig;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;


/**
 * HmsVendorRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MedicineParticularRepository extends EntityRepository
{
    public function searchAutoComplete($q, MedicineConfig $config)
    {
        $query = $this->createQueryBuilder('e');
        $query->join('e.medicineConfig', 'ic');
        $query->select('e.companyName as id');
        $query->addSelect('e.companyName as text');
        $query->where($query->expr()->like("e.companyName", "'$q%'"  ));
        $query->andWhere("ic.id = :config");
        $query->setParameter('config', $config->getId());
        $query->groupBy('e.id');
        $query->orderBy('e.companyName', 'ASC');
        $query->setMaxResults( '30' );
        return $query->getQuery()->getResult();

    }

    public function apiInvoiceMessage(GlobalOption $option)
    {
        $query = $this->createQueryBuilder('e');
        $query->join('e.medicineConfig', 'ic');
        $query->join("e.particularType","pt");
        $query->select('e.name');
        $query->where("ic.id = :config");
        $query->setParameter('config', $option->getMedicineConfig()->getId());
        $query->andWhere("pt.slug = 'print-message'");
        $query->orderBy('e.name', 'ASC');
        $result = $query->getQuery()->getArrayResult();
        return $result;

    }

}
