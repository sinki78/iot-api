<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Data;
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

    /**
     * @Route("/getAll")
     * @Method({"GET","HEAD"})
     */
    public function getAll($latitude = '48.856614', $longitude = '2.352222', $deltaLatitude = '1', $deltaLongitude = '1', $dateMin = NULL, $dateMax = NULL, $typeData = 'PF')
    {
        $datas = $this->getDoctrine()->getRepository('AppBundle:Data')->findAll();
        $result = [];
        foreach ($datas as $key => $data) {
            $line['date'] = $data->getDate();
            $line['longitude'] = $data->getLongitude();
            $line['latitude'] = $data->getLatitude();
            $line['type'] = $data->getType();
            $line['valeur'] = $data->getValeur();
            $result[] = $line;
        }

        return new JsonResponse($result);
    }

    /**
     * @Route("/getAllByType/{typeData}")
     * @Method({"GET","HEAD"})
     */
    public function getAllByType($latitude = '48.856614', $longitude = '2.352222', $deltaLatitude = '1', $deltaLongitude = '1', $dateMin = NULL, $dateMax = NULL, $typeData = 'PF')
    {



        $datas = $this->getDoctrine()->getRepository('AppBundle:Data')->findAllByType($typeData);
	
        $result = [];
        foreach ($datas as $key => $data) {
            $line['date'] = $data->getDate();
            $line['longitude'] = $data->getLongitude();
            $line['latitude'] = $data->getLatitude();
            $line['valeur'] = $data->getValeur();
            $result[] = $line;
        }
        return new JsonResponse($result);
    }


    /**
     * @Route("/getByLocalisation/{latitude}/{longitude}/{deltaLat}/{deltaLon}")
     * @Method({"GET","HEAD"})
     */
    public function getByLocalisation($latitude = '48.856614', $longitude = '2.352222', $deltaLat = '1', $deltaLon = '1', $dateMin = NULL, $dateMax = NULL, $typeData = 'PF')
    {
        $datas = $this->getDoctrine()->getRepository('AppBundle:Data')->findByLocalisation($latitude, $longitude, $deltaLat, $deltaLon);
        $result = [];
        foreach ($datas as $key => $data) {
            $line['date'] = $data->getDate();
            $line['longitude'] = $data->getLongitude();
            $line['latitude'] = $data->getLatitude();
            $line['valeur'] = $data->getValeur();
            $result[] = $line;
        }
        return new JsonResponse($result);
    }
//{long}{dLat}{dLong}{dateD}{dateF}{type}
    /**
     * @Route("/getBy/")
     * @Method({"GET"})
     */
    public function getBy(Request $request)
    {

	ini_set("memory_limit","512M");
        $lat = $request->query->get('lat');
        $long = $request->query->get('long');
        $dLat = $request->query->get('dLat');
        $dLong = $request->query->get('dLong');
        $dateD = $request->query->get('dateD');
        $dateF = $request->query->get('dateF');
        $type = $request->query->get('type');
	$lat = (int)$lat;
	$long = (int)$long;
	$dLat =(int)$dLat;
	$dLong = (int)$dLong;

        $dbh = new \PDO('mysql:host=localhost;dbname=iotapi', 'iotapi', 'iotapi');

        $minLat = $lat - $dLat;
        $maxLat = $lat + $dLat;
        $minLon = $long - $dLong;
        $maxLon = $long + $dLong;

        $rs = $dbh->prepare("SELECT 'id' FROM 'iotapi' WHERE 'latitude' BETWEEN :minLat AND :maxLat AND 'longitude' BETWEEN :minLon AND :maxLon AND 'type' = :type LIMIT 1000");
        $rs->execute([':minLat' => $minLat, ':maxLat' => $maxLat, ':minLon' => $minLon, ':maxLon' => $maxLon, ':type' => $type]);
        $result = $rs->fetchAll();
        dump($result);die;

        if($lat != NULL && $long != NULL && $dLat != NULL && $dLong != NULL && $dateD == NULL && $dateF == NULL && $type == NULL){
            $datas = $this->getDoctrine()->getRepository('AppBundle:Data')->findWithDelta((int)$lat,(int)$long,(int)$dLat,(int)$dLong);


        }
        if($lat != NULL && $long != NULL && $dLat != NULL && $dLong != NULL && $dateD == NULL && $dateF == NULL && $type != NULL){
            $datas = $this->getDoctrine()->getRepository('AppBundle:Data')->findWithDeltaType($lat,$long,$dLat,$dLong,$type);
        }
        else if($lat != NULL && $long != NULL && $dLat != NULL && $dLong != NULL && $dateD != NULL && $dateF == NULL && $type == NULL){
            $dateF = new \DateTime();
            $datas = $this->getDoctrine()->getRepository('AppBundle:Data')->findWithDeltaDate($lat,$long,$dLat,$dLong,$dateD,$dateF);
        }
        else if($lat != NULL && $long != NULL && $dLat != NULL && $dLong != NULL && $dateD != NULL && $dateF != NULL && $type == NULL){
            $datas = $this->getDoctrine()->getRepository('AppBundle:Data')->findWithDeltaDate($lat,$long,$dLat,$dLong,$dateD,$dateF);
        }
        else if($lat != NULL && $long != NULL && $dLat != NULL && $dLong != NULL && $dateD == NULL && $dateF == NULL && $type != NULL){
            $dateF = new \DateTime();
            $datas = $this->getDoctrine()->getRepository('AppBundle:Data')->findWithDeltaDateType($lat,$long,$dLat,$dLong,$dateD,$dateF,$type);
        }

        else if($lat != NULL && $long != NULL && $dLat != NULL && $dLong != NULL && $dateD != NULL && $dateF != NULL && $type != NULL){
            $datas = $this->getDoctrine()->getRepository('AppBundle:Data')->findWithDeltaDateType($lat,$long,$dLat,$dLong,$dateD,$dateF,$type);
        }
        $result = [];
        foreach ($datas as $key => $data) {
            $line['date'] = $data->getDate();
            $line['longitude'] = $data->getLongitude();
            $line['latitude'] = $data->getLatitude();
            $line['type'] = $data->getType();
            $line['valeur'] = $data->getValeur();
            $result[] = $line;
        }




        return new JsonResponse($result);


//        $datas = $this->getDoctrine()->getRepository('AppBundle:Data')->findByLocalisation($latitude, $longitude, $deltaLat, $deltaLon);
//        $result = [];
//        foreach ($datas as $key => $data) {
//            $line['date'] = $data->getDate();
//            $line['longitude'] = $data->getLongitude();
//            $line['latitude'] = $data->getLatitude();
//            $line['valeur'] = $data->getValeur();
//            $result[] = $line;
//        }
//        return new JsonResponse($result);
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
                        try{
                            $date = new \DateTime($transitionToDst);

                        } catch (Exception $e){
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
                try{
                    $em->persist($CO2);
                    $em->persist($PF);

                    $em->flush();
                } catch (ConstraintViolationException $e){
                    return new Response('Estatus code 400',400);
                }


            }


        }
        return new JsonResponse('success');
    }

}
