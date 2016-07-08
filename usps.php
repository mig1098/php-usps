<?php
/**
 * @author mig1098@hotmail.com
 * @date 07/08/2016
 */
class Simple_usps{
    private $devurl  = "http://production.shippingapis.com/ShippingAPITest.dll";
    private $produrl = "http://production.shippingapis.com/ShippingAPI.dll";
    private $test = false;
    private $url;
    private $data;
    private $queryparams='';
    private $apicodes = [
        'RateV2'                          => 'RateV2Request',
        'RateV4'                          => 'RateV4Request',
        'IntlRateV2'                      => 'IntlRateV2Request',
        'Verify'                          => 'AddressValidateRequest',
        'ZipCodeLookup'                   => 'ZipCodeLookupRequest',
        'CityStateLookup'                 => 'CityStateLookupRequest',
        'TrackV2'                         => 'TrackFieldRequest',
        'FirstClassMail'                  => 'FirstClassMailRequest',
        'SDCGetLocations'                 => 'SDCGetLocationsRequest',
        'ExpressMailLabel'                => 'ExpressMailLabelRequest',
        'PriorityMail'                    => 'PriorityMailRequest',
        'OpenDistributePriorityV2'        => 'OpenDistributePriorityV2.0Request',
        'OpenDistributePriorityV2Certify' => 'OpenDistributePriorityV2.0CertifyRequest',
        'ExpressMailIntl'                 => 'ExpressMailIntlRequest',
        'PriorityMailIntl'                => 'PriorityMailIntlRequest',
        'FirstClassMailIntl'              => 'FirstClassMailIntlRequest',
    ];
    public function __construct(){
        $this->url = $this->test ? $this->devurl : $this->produrl ;
    }
    public function test($bool){
        $this->test = $bool;
        return $this;
    }
    public function DomesticRateCalculator($data){
        $this->data = $data;
        //
        $body   = $this->buildPriceCalculatorXML();
        $header = $this->buildHeader($body);
        $url    = $this->buildUrl($body);
        //header('Content-type: text/xml');echo $body;exit;
        $ch     = curl_init();
        //
        $results = $this->curlGet($ch,$url,$header);
        try{
            $xml = @simplexml_load_string((string)$results);
            if(!$xml){ throw new \Exception();}
        }catch(\Exception $e){
            var_dump($results);exit;
        }
        return $xml;
    }
    public function InternationalPriceCalculator(){
        //expected
    }
    private function buildUrl($body=''){
        $params = array(
            'API'  => $this->data['version'],
            'XML'  => $body
        );
        $params = !empty($this->queryparams)?array_merge($params,$this->queryparams):$params;
        $url = $this->url.'?'.http_build_query($params);
        return $url;
    }
    private function buildHeader($body){
        return array(
            'Content-Action'=>'cargosurf-vendors',
            'Content-Length'=>strlen($body),
            'Content-Type'=>'text/xml charset=utf-8'
        );
    }
    private function curlGet($ch,$url,$header){
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $results = curl_exec($ch);
        if ($results) {
            //echo 'realizado';
        }else{
           // echo 'no realiza';
        }
        curl_close($ch);
        return $results;
    }
    private function buildPriceCalculatorXML(){
        $xml = '<'.$this->apicodes[$this->data['version']].' USERID="'.$this->data['username'].'">';
        if(!empty($this->data['body']['Package'])){
            $xml .= '<Package ID="'.$this->data['body']['Package'].'">';
            $xml .= !empty($this->data['body']['Service'])       ? '<Service>'.$this->data['body']['Service'].'</Service>':'';
            $xml .= !empty($this->data['body']['ZipOrigination'])? '<ZipOrigination>'.$this->data['body']['ZipOrigination'].'</ZipOrigination>':'';
            $xml .= !empty($this->data['body']['ZipDestination'])? '<ZipDestination>'.$this->data['body']['ZipDestination'].'</ZipDestination>':'';
            $xml .= !empty($this->data['body']['Pounds'])        ? '<Pounds>'.$this->data['body']['Pounds'].'</Pounds>':'';
            $xml .= !empty($this->data['body']['Ounces'])        ? '<Ounces>'.$this->data['body']['Ounces'].'</Ounces>':'';
            $xml .= !empty($this->data['body']['Container'])     ? '<Container>'.$this->data['body']['Container'].'</Container>':'';
            $xml .= !empty($this->data['body']['Size'])          ? '<Size>'.$this->data['body']['Size'].'</Size>':'';
            $xml .= !empty($this->data['body']['Width'])         ? '<Width>'.$this->data['body']['Width'].'</Width>':'';
            $xml .= !empty($this->data['body']['Length'])        ? '<Length>'.$this->data['body']['Length'].'</Length>':'';
            $xml .= !empty($this->data['body']['Height'])        ? '<Height>'.$this->data['body']['Height'].'</Height>':'';
            $xml .= !empty($this->data['body']['Girth'])         ? '<Girth>'.$this->data['body']['Girth'].'</Girth>':'';
            $xml .= '</Package>';
        }
        $xml .= '';
        $xml .= '</'.$this->apicodes[$this->data['version']].'>';
        return $xml;
    }
}
