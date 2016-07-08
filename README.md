# php-usps

library for usps api price calculator

####Sample

```
include 'usps.php';
$simple_usps = new simple_usps();
//test data
$data = array(
    'Package' => '1ST',
    'Service' => 'PRIORITY',
    'ZipOrigination' => 92676,
    'ZipDestination' => 20770,
    'Pounds' => 2,
    'Ounces' => 4,
    'Container' =>'NONRECTANGULAR',
    'Size'   =>'LARGE',
    'Width'  =>15,
    'Length' =>30,
    'Height' =>15,
    'Girth'  =>40
);
$resp = $simple_usps->DomesticRateCalculator(array(
    'version'  => 'RateV4',
    'username' =>'X82XXXXX69XX',
    'body'     => $data
));
print_r($resp);
```
