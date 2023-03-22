<?php
namespace App\Models;

use \Nette;

class Admin {
    protected $database;
    protected $user;
    const TABLE = "vehicles";

    public function __construct(\Nette\Database\Connection $explorer, \Nette\Security\User $user){
        $this->database = $explorer;
        $this->user = $user;
    }

    public function getVehicles($limit = 20){
        $vehicles = $this->database->fetchAll("SELECT `id`, `title`, JSON_EXTRACT(`data`, '$.image') as `image`, JSON_EXTRACT(`data`, '$.desc') as `desc`, JSON_EXTRACT(`data`, '$.map_left') as `mapLeft`, JSON_EXTRACT(`data`, '$.map_top') as `mapTop`, `added_at` FROM `".self::TABLE."` WHERE `validate` IS NULL");

        return $vehicles;
    }

    public function validate($id){
        return $this->database->table(self::TABLE)->where('id = ? AND (validate = 0 OR validate IS NULL)', $id)->update(['validate'=>1, 'validate_by'=>$this->user->getId()]);
    }

    public function delete($id){
        return $this->database->table(self::TABLE)->where('id = ?', $id)->delete();
    }
}

?>