<?php
namespace App\Models;

use \Nette;

class Detail {
    public $search;
    const VEH_TABLE = "vehicles";
    const USR_TABLE = "users";

    public function __construct(\Nette\Database\Connection $explorer){
        $this->search = $explorer;
    }

    public function insertNewVehicle(int $id, string $name, string $desc, $vehicleIMG, float $mapTop, float $mapLeft){
        $fileNameVEHICLE = "./assets/locations/$_POST[vehicleID]".uniqid("-".strtotime("now")).".webp";
        if(!move_uploaded_file($vehicleIMG["tmp_name"], $fileNameVEHICLE)) throw new \Exception("Nepodařilo se nahrát obrázek s lokací vozidla");

        return $this->search->query("INSERT INTO `".self::VEH_TABLE."` ?", [
            "title"=>$id." - ".$name,
            "data"=>json_encode(["desc"=>$desc, "map_top"=>$mapTop, "map_left"=>$mapLeft, "image"=>$fileNameVEHICLE]),
            "added_by"=>1,
            "added_at"=>"now()"
        ]);
    }

    public function findVehicleLike(string $like){
        return $this->search->fetchAll("SELECT DISTINCT ".self::VEH_TABLE.".title FROM `".self::VEH_TABLE."` WHERE ".self::VEH_TABLE.".title LIKE '%".$like."%' AND ".self::VEH_TABLE.".validate = 1");
    }

    public function showVehicleInfo(string $title){
        return $this->search->fetchAll("SELECT ".self::VEH_TABLE.".*, ".self::USR_TABLE.".username FROM `".self::VEH_TABLE."` LEFT JOIN `".self::USR_TABLE."` ON ".self::VEH_TABLE.".added_by = ".self::USR_TABLE.".id WHERE ".self::VEH_TABLE.".title = ? AND ".self::VEH_TABLE.".validate = 1", $title);
    }
}

?>