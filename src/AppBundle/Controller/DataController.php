<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;


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
    public function getAll($latitude = '48.856614',$longitude = '2.352222',$deltaLatitude = '1',$deltaLongitude = '1',$dateMin = NULL,$dateMax = NULL,$typeData = 'PF')
    {
        $datas = $this->getDoctrine()->getRepository('AppBundle:Data')->findAll();
        $result = [];
        foreach ($datas as $key => $data){
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
    public function getAllByType($latitude = '48.856614',$longitude = '2.352222',$deltaLatitude = '1',$deltaLongitude = '1',$dateMin = NULL,$dateMax = NULL,$typeData = 'PF')
    {
        $datas = $this->getDoctrine()->getRepository('AppBundle:Data')->findAllByType($typeData);
        $result = [];
        foreach ($datas as $key => $data){
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
    public function getByLocalisation($latitude = '48.856614',$longitude = '2.352222',$deltaLat = '1',$deltaLon = '1',$dateMin = NULL,$dateMax = NULL,$typeData = 'PF')
    {
        $datas = $this->getDoctrine()->getRepository('AppBundle:Data')->findByLocalisation($latitude,$longitude,$deltaLat,$deltaLon);
        $result = [];
        foreach ($datas as $key => $data){
            $line['date'] = $data->getDate();
            $line['longitude'] = $data->getLongitude();
            $line['latitude'] = $data->getLatitude();
            $line['valeur'] = $data->getValeur();
            $result[] = $line;
        }
        return new JsonResponse($result);
    }
}
