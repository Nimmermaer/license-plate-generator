<?php
header('Content-type: text/html; charset=utf-8');

$factory = (new licencePlateFactory());
$factory->generate();

class LicencePlate
{
    public function __construct(public string $federalState = '', public string $identificationNumber = '')
    {
    }
}

class Database
{
    public static function generateDatabase()
    {
        $pdo = new PDO('mysql:host=db;dbname=db', 'db', 'db');
        $pdo->exec("CREATE TABLE IF NOT EXISTS mydb.licence_plate ('federal_state', 'town')");
        $statement = $pdo->prepare("INSERT INTO users (federal_state, town) VALUES (:federal_state, :town)");

        $count = 0;
        if (($handle = fopen("https://raw.githubusercontent.com/Octoate/KfzKennzeichen/master/src/de/octoate/kfzkennzeichen/data/kfzkennzeichen-deutschland.csv",
                "r")) !== false) {
            while (($data = fgetcsv($handle)) !== false) {
                $federalState = array('federal_state' => $data[0] , 'town' => $data[1]);
                $statement->execute($federalState);
            }
            fclose($handle);
        }
    }
    public static function findVehicleCountryCodes(): array
    {
        return str_getcsv(mb_convert_encoding(file_get_contents('https://raw.githubusercontent.com/Octoate/KfzKennzeichen/master/src/de/octoate/kfzkennzeichen/data/kfzkennzeichen-deutschland.csv'),
            'UTF-8', mb_list_encodings()));
    }

    public static function findOneRandomVehicleCountryCodes(): array
    {
        $countries = [];
        $count = 0;
        if (($handle = fopen("https://raw.githubusercontent.com/Octoate/KfzKennzeichen/master/src/de/octoate/kfzkennzeichen/data/kfzkennzeichen-deutschland.csv",
                "r")) !== false) {
            while (($data = fgetcsv($handle)) !== false) {
                ++$count;
                $countries[] = $data;
            }
            fclose($handle);
        }
        $rand = rand(0, $count);
        return $countries[$rand];
    }

    public static function findOneCountryCodeByFederalState(string $federalState):string 
    {
    }

}

class licencePlateFactory
{
    const MAX_LENGTH = 8;

    public function generate(string $federalState = '', string $desiredLetters = '')
    {
        if ($federalState) {
            $federalState = Database::findOneCountryCodeByFederalState($federalState);
        }
        $licencePlateWithoutWhitespace = trim($federalState . $desiredLetters);
        var_dump($licencePlateWithoutWhitespace);
        var_dump(strlen($licencePlateWithoutWhitespace));
        
        if (strlen($licencePlateWithoutWhitespace) <= self::MAX_LENGTH) {
            $country = Database::findOneRandomVehicleCountryCodes();
        }
        $licencePlate = new LicencePlate();

    }
}
