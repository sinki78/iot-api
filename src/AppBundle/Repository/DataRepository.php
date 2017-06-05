<?php

namespace AppBundle\Repository;

/**
 * DataRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DataRepository extends \Doctrine\ORM\EntityRepository
{
    public function findAllByType($type){
        $result = $this->createQueryBuilder('d')
            ->where('d.dtype = :type')
            ->setParameter("type", $type)
            ->setMaxResults(100000)
	    ->getQuery()
            ->getResult();

        return $result;
    }

    public function findByLocalisation($latitude, $longitude,$deltaLat, $deltaLon){
        $minLat = $latitude - $deltaLat;
        $maxLat = $latitude + $deltaLat;
        $minLon = $longitude - $deltaLon;
        $maxLon = $longitude + $deltaLon;

        $result = $this->createQueryBuilder('d')
            ->where('d.latitude BETWEEN :minLat AND :maxLat')
            ->AndWhere('d.longitude BETWEEN :minLon AND :maxLon')
            ->setParameter("minLat", $minLat)
            ->setParameter("maxLat", $maxLat)
            ->setParameter("minLon", $minLon)
            ->setParameter("maxLon", $maxLon)
	    ->setMaxResults(100000)
            ->getQuery()
            ->getResult();

        return $result;
    }


    public function findWithDelta($latitude, $longitude,$deltaLat, $deltaLon){
        $minLat = $latitude - $deltaLat;
        $maxLat = $latitude + $deltaLat;
        $minLon = $longitude - $deltaLon;
        $maxLon = $longitude + $deltaLon;

        $result = $this->createQueryBuilder('d')
            ->where('d.latitude BETWEEN :minLat AND :maxLat')
            ->AndWhere('d.longitude BETWEEN :minLon AND :maxLon')
            ->setParameter("minLat", $minLat)
            ->setParameter("maxLat", $maxLat)
            ->setParameter("minLon", $minLon)
            ->setParameter("maxLon", $maxLon)
            ->setMaxResults(100000)
            ->getQuery()
            ->getResult();

        return $result;
    }
    public function findWithDeltaType($latitude, $longitude,$deltaLat, $deltaLon, $type){
        $minLat = $latitude - $deltaLat;
        $maxLat = $latitude + $deltaLat;
        $minLon = $longitude - $deltaLon;
        $maxLon = $longitude + $deltaLon;

        $result = $this->createQueryBuilder('d')
            ->where('d.latitude BETWEEN :minLat AND :maxLat')
            ->AndWhere('d.longitude BETWEEN :minLon AND :maxLon')
            ->AndWhere('d.dtype = :type')
            ->setParameter("minLat", $minLat)
            ->setParameter("maxLat", $maxLat)
            ->setParameter("minLon", $minLon)
            ->setParameter("maxLon", $maxLon)
            ->setParameter("type", $type)
            ->setMaxResults(100000)
            ->getQuery()
            ->getResult();

        return $result;
    }
    public function findWithDeltaDateType($latitude, $longitude,$deltaLat, $deltaLon,$dateD,$dateF, $type){
        $minLat = $latitude - $deltaLat;
        $maxLat = $latitude + $deltaLat;
        $minLon = $longitude - $deltaLon;
        $maxLon = $longitude + $deltaLon;

        $result = $this->createQueryBuilder('d')
            ->where('d.latitude BETWEEN :minLat AND :maxLat')
            ->AndWhere('d.longitude BETWEEN :minLon AND :maxLon')
            ->AndWhere('d.date BETWEEN :dateD AND :dateF')
            ->AndWhere('d.dtype = :type')
            ->setParameter("minLat", $minLat)
            ->setParameter("maxLat", $maxLat)
            ->setParameter("minLon", $minLon)
            ->setParameter("maxLon", $maxLon)
            ->setParameter("dateD", $dateD)
            ->setParameter("dateF", $dateF)
            ->setParameter("type", $type)
	    ->setMaxResults(100000)
            ->getQuery()
            ->getResult();

        return $result;
    }
    public function findWithDeltaDate($latitude, $longitude,$deltaLat, $deltaLon,$dateD,$dateF){
        $minLat = $latitude - $deltaLat;
        $maxLat = $latitude + $deltaLat;
        $minLon = $longitude - $deltaLon;
        $maxLon = $longitude + $deltaLon;

        $result = $this->createQueryBuilder('d')
            ->where('d.latitude BETWEEN :minLat AND :maxLat')
            ->AndWhere('d.longitude BETWEEN :minLon AND :maxLon')
            ->AndWhere('d.date BETWEEN :dateD AND :dateF')
            ->setParameter("minLat", $minLat)
            ->setParameter("maxLat", $maxLat)
            ->setParameter("minLon", $minLon)
            ->setParameter("maxLon", $maxLon)
            ->setParameter("dateD", $dateD)
            ->setParameter("dateF", $dateF)
            ->setMaxResults(100000)
            ->getQuery()
            ->getResult();

        return $result;
    }
}

