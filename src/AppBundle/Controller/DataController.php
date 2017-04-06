<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Data;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints\DateTime;


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
                        $date = new \DateTime($transitionToDst);
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
                    if ($key == 'co2') {

                        $CO2->setType($key);
                        $CO2->setValeur($value);
                    }
                    if ($key == 'pf') {

                        $PF->setType($key);
                        $PF->setValeur($value);
                    }

                }

                $em = $this->getDoctrine()->getManager();
                $em->persist($CO2);
                $em->persist($PF);
                $em->flush();

            }


        }
    }
}
}

return new JsonResponse('success');
}

}
