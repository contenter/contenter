<?php

/**
 * Work with GMaps and distance.
 *
 * Gets geo-informations from the Google Maps API
 * http://code.google.com/apis/maps/index.html
 *
 * FinalView (c).
 */
class FinalView_Geocoder
{

    private $_url = 'http://maps.google.com/maps/geo';
    //for api v2
    private $_apiKey;

    //in meters.
    //If need to carry out all operations, for example, miles - override the constant in miles.
    const C_EARTH = 6372795;

    public function __construct()
    {
        //$this->_apiKey = $apiKey;
    }

    /**
     * Get google geocode.<p>
     *
     * @param string $request Address or coordinates.
     * @return mixed Decoding json object.
     */
    protected function _getGeocodedObj($request)
    {
        $client = new Zend_Http_Client();
        $client->setUri($this->_url);
        $client->setParameterGet('q', $request)
                ->setParameterGet('hl', 'en')
                ->setParameterGet('output', 'json')
                ->setParameterGet('sensor', 'false');
        //->setParameterGet('key', $this->_apiKey);

        $result = $client->request('GET');
        $response = Zend_Json_Decoder::decode($result->getBody(),
                        Zend_Json::TYPE_OBJECT);
        return $response;
    }

    /**
     * Get coordinates.<p>
     *
     * @param string $address Address.
     * @return mixed Array ('lat', 'lng') on success or false when address not found.
     */
    public function getLatLng($address)
    {
        $response = $this->_getGeocodedObj($address);
        if (isset($response->Placemark[0]->Point->coordinates[1])) {
            $return = array(
                'lat' => $response->Placemark[0]->Point->coordinates[1],
                'lng' => $response->Placemark[0]->Point->coordinates[0]
            );
        } else {
            $return = false;
        }
        return $return;
    }

    /**
     * Get address by coordinates.<p>
     *
     * @param string $latLng Separator ','.
     * @return mixed String on success or false when coordinates not found.
     */
    public function getAddress($latLng)
    {
        $response = $this->_getGeocodedObj($latLng);

        if (isset($response->Placemark[0])) {

            return $response->Placemark[0]->address;
        }

        return false;
    }

    /**
     * Get address detail by coordinates.<p>
     *
     * @param string $latLng Separator ','.
     * @return mixed Array on success or false when address not found.
     */
    public function getAddressDetail($latLng)
    {
        $response = $this->_getGeocodedObj($latLng);
        $result = array();

        if (isset($response->Placemark[0]->AddressDetails->Country)) {

            $tmp = $response->Placemark[0]->AddressDetails->Country->
                    AdministrativeArea;

            if (isset($tmp->SubAdministrativeArea)) {

                $result['city'] = $tmp->SubAdministrativeArea->Locality->LocalityName;
            } else {
                $result['city'] = '';
            }

            $result['state'] = $tmp->AdministrativeAreaName;

            return $result;
        }

        return false;
    }

    /**
     * Get state by coordinates.<p>
     *
     * @param string $latLng Separator ','.
     * @return mixed String on success or false when coordinates not found.
     */
    public function getState($latLng)
    {
        $response = $this->_getGeocodedObj($latLng);

        if (isset($response->Placemark[0]->AddressDetails->Country->
                        AdministrativeArea->AdministrativeAreaName)) {

            return $response->Placemark[0]->AddressDetails->Country->
            AdministrativeArea->AdministrativeAreaName;
        }

        return false;
    }

    /**
     * Get meters by coordinates.<p>
     *
     * -103.8, 40.0, -103.7, 40.0 ~ 8530 meters
     * -103.88, 40.0, -103.87, 40.0 ~ 853 - meters
     *
     * -103.8, 40.0, -103.8, 40.1 ~ 11094 meters
     * -103.8, 40.00, -103.8, 40.01 ~ 1109 meters
     *
     * @param float $long_1
     * @param float $lat_1
     * @param float $long_2
     * @param float $lat_2
     * @return int Distance.
     */
    function distance($long_1, $lat_1, $long_2, $lat_2)
    {
        $C_A = 1 / 298.257223563;
        $C_E2 = 2 * $C_A - $C_A * $C_A;

        $fSinB1 = sin($lat_1 * pi() / 180);
        $fCosB1 = cos($lat_1 * pi() / 180);
        $fSinL1 = sin($long_1 * pi() / 180);
        $fCosL1 = cos($long_1 * pi() / 180);

        $N1 = self::C_EARTH / sqrt(1 - $C_E2 * $fSinB1 * $fSinB1);

        $X1 = $N1 * $fCosB1 * $fCosL1;
        $Y1 = $N1 * $fCosB1 * $fSinL1;
        $Z1 = (1 - $C_E2) * $N1 * $fSinB1;

        $fSinB2 = sin($lat_2 * pi() / 180);
        $fCosB2 = cos($lat_2 * pi() / 180);
        $fSinL2 = sin($long_2 * pi() / 180);
        $fCosL2 = cos($long_2 * pi() / 180);

        $N2 = self::C_EARTH / sqrt(1 - $C_E2 * $fSinB2 * $fSinB2);

        $X2 = $N2 * $fCosB2 * $fCosL2;
        $Y2 = $N2 * $fCosB2 * $fSinL2;
        $Z2 = (1 - $C_E2) * $N2 * $fSinB2;

        $D = sqrt(($X1 - $X2) * ($X1 - $X2) + ($Y1 - $Y2) * ($Y1 - $Y2) + ($Z1 - $Z2) * ($Z1 - $Z2));
        $R = $N1;
        $D2 = 2 * $R * asin(0.5 * $D / $R);
        return $D2;
    }

    /**
     * Get SQL select part with distance<p>
     *
     * @param float $latitude
     * @param float $longitude
     * @param string $tableLatitude default - lat
     * @param string $tableLongitude default - lng
     * @return string SQL part.
     */
    static public function getSqlCulcDistance($latitude, $longitude, $tableLatitude = 'lat', $tableLongitude = 'lng')
    {
        $condition = self::C_EARTH . " * ACOS( COS( RADIANS({$latitude}) )
              * COS( radians( {$tableLatitude} ) )
              * COS( radians( {$tableLongitude} ) - radians({$longitude}) )
              + SIN( radians({$latitude}) )
              * SIN( radians( {$tableLatitude} ) ) )";

        return $condition;
    }

}