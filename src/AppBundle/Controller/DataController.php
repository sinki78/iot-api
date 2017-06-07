<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Data;
use Doctrine\DBAL\Driver\PDOException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\DateTime;
use Doctrine\DBAL\Exception\ConstraintViolationException;

/**
 * @Route("/api")
 *
 */
class DataController extends Controller
{



//{long}{dLat}{dLong}{dateD}{dateF}{type}
    /**
     * @Route("/getBy/")
     * @Method({"GET"})
     */
    public function getBy(Request $request)
    {

        ini_set("memory_limit", "512M");
        $lat = $request->query->get('lat');
        $long = $request->query->get('long');
        $dLat = $request->query->get('dLat');
        $dLong = $request->query->get('dLong');
        $dateD = $request->query->get('dateD');
        $dateF = $request->query->get('dateF');
        $type = $request->query->get('type');


        try {
            $dbh = new \PDO("pgsql:dbname=iotapi;host=lpdw.ddns.net", 'iotapi', 'iotapi');
//            $dbh = new \PDO('mysql:host=192.168.1.26;dbname=iotapi', 'iotapi', 'iotapi');
        } catch (\PDOException $e) {
            print "Erreur !: " . $e->getMessage() . "<br/>";
            die();
        }
        $minLat = $lat - $dLat;
        $maxLat = $lat + $dLat;
        $minLon = $long - $dLong;
        $maxLon = $long + $dLong;


        $lattitudeNordEst = $lat + $dLat;
        $lattitudeSouthWest = $lat - $dLat;

        $longitudeNordEst = $long + $dLong;
        $longitudeSouthWest = $long - $dLong;

        $parts = 10;

        $deltaLat = ($lattitudeNordEst - $lattitudeSouthWest) / $parts;
        $deltaLon = ($longitudeNordEst - $longitudeSouthWest) / $parts;
        $sql = '';
        $values = [];

        for ($i = 0; $i < $parts; $i++) {
            $maxLat = $lattitudeNordEst - $i * $deltaLat;
            $minLat = $maxLat - $deltaLat;
            for ($j = 0; $j < $parts; $j++) {
                $maxLon = $longitudeNordEst - $j * $deltaLon;
                $minLon = $maxLon - $deltaLon;
                if (!($i == 0 && $j == 0)) {
                    $sql .= " UNION ALL ";
                }
                $values['minLat' . $i . $j] = $minLat;
                $values['maxLat' . $i . $j] = $maxLat;
                $values['minLon' . $i . $j] = $minLon;
                $values['maxLon' . $i . $j] = $maxLon;

                $sql .= '(SELECT AVG(latitude) as latitude,AVG(longitude) as longitude,AVG(valeur) as valeur FROM data WHERE latitude BETWEEN :minLat' . $i . $j . ' AND :maxLat' . $i . $j . ' AND longitude BETWEEN :minLon' . $i . $j . ' AND :maxLon' . $i . $j . ' AND dtype=:dtype)';

            }
        }

        $rs = $dbh->prepare($sql);

        foreach ($values as $key => $value) {
            $rs->bindValue(':' . $key, $value);
        }
        $rs->bindValue(':dtype', $type);

        $rs->execute();

        $tmpResult[] = $rs->fetchAll(\PDO::FETCH_ASSOC);
        $result = [];
        for ($i = 0; $i < count($tmpResult[0]); $i++) {
            if($tmpResult[0][$i]['latitude'] != NULL){
                $tmpResult[0][$i]['type'] = $type;
                $result[] = $tmpResult[0][$i];
            }
        }

        return new JsonResponse($result);
    }

    /**
     * @Route("/setData")
     * @Method({"POST"})
     */
    public function postLocalisation(Request $request)
    {
        $content = $request->getContent();
        if (!empty($content)) {
            $params = json_decode($content, true); // 2nd param to get as array

            foreach ($params as $line) {

                $CO2 = new Data();
                $PF = new Data();
                foreach ($line as $key => $value) {

                    if ($key == 'date') {
                        $transitionToDst = $value;
                        try {
                            $date = new \DateTime($transitionToDst);

                        } catch (Exception $e) {
                            return new JsonResponse($e);
                        }
                        $CO2->setDate($date);
                        $PF->setDate($date);
                    }

                    if ($key == 'lon') {

                        $CO2->setLongitude($value);
                        $PF->setLongitude($value);
                    }


                    if ($key == 'lat') {
                        $CO2->setLatitude($value);
                        $PF->setLatitude($value);
                    }
                    if ($key == 'co2' || $key == 'CO2') {

                        $CO2->setType(strtoupper($key));
                        $CO2->setValeur($value);
                    }
                    if ($key == 'pf' || $key == 'PF') {

                        $PF->setType(strtoupper($key));
                        $PF->setValeur($value);
                    }

                }

                $em = $this->getDoctrine()->getManager();
                try {
                    $em->persist($CO2);
                    $em->persist($PF);

                    $em->flush();
                } catch (ConstraintViolationException $e) {
                    return new Response('Estatus code 400', 400);
                }


            }


        }
        return new JsonResponse('success');
    }

}
