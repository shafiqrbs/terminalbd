<?php

namespace Appstore\Bundle\AssetsBundle\Repository;
use Appstore\Bundle\AssetsBundle\Entity\DepreciationBatch;
use Appstore\Bundle\AssetsBundle\Entity\DepreciationModel;
use \Doctrine\ORM\EntityRepository;

/**
 * ProductDistributionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DepreciationBatchRepository extends EntityRepository
{


    public function existDepreciation($config)
    {
        $array = array();
        $qb = $this->createQueryBuilder('e');
        $qb->join("e.depreciation","d");
        $qb->select("d.id AS depreciationId");
        $qb->where("e.config = :config")->setParameter('config', $config);
        $qb->groupBy('depreciationId');
        $result = $qb->getQuery()->getArrayResult();
        foreach ($result as $value):
            $array[] = $value['depreciationId'];
        endforeach;
        return $array;
    }

    public function insertBatch(DepreciationModel $depreciation)
    {
        $em = $this->_em;
        $entity = new DepreciationBatch();
        $entity->setDepreciation($depreciation);
        $entity->setConfig($depreciation->getConfig());
        $date = new \DateTime('now');
        $entity->setDepreciationDate($date);
        $em->persist($entity);
        $em->flush();
        return $entity;
    }

    public function updateBatch(DepreciationBatch $batch)
    {
        $em = $this->_em;
        $array = array();
        $result = $em->getRepository('AssetsBundle:ProductLedger')->getBatchWiseDepreciation($batch->getId());
        $totalAmount = 0;
        foreach ($result as $row):
            $totalAmount += $totalAmount +$row['amount'];
            $array[$row['itemId']] = round($row['amount']);
        endforeach;
        if($totalAmount > 0){
            $jsonInputItem = json_encode($array);
            $batch->setItems($jsonInputItem);
            $batch->setAmount(round($totalAmount));
            $em->persist($batch);
            $em->flush();
            $em->getRepository('AccountingBundle:Transaction')->insertDepreciation($batch,$result);
        }
    }

}