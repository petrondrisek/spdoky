<?php
declare(strict_types=1);

namespace App\Presenters;

use Nette;
use App\Models\Detail;

class DetailPresenter extends Nette\Application\UI\Presenter
{
    public $search;

    public function __construct(Detail $search){
        $this->search = $search;
    }
    
    public function actionAddnew(){
        if(empty($_POST["vehicleID"]) || ($_POST["vehicleID"] < 400 || $_POST["vehicleID"] > 611))
                $this->sendJson(["success"=>0, "message"=>"ID musí být v rozmezí 400 - 611"]);
        
        if(empty($_POST["vehicleName"]))
            $this->sendJson(["success"=>0, "message"=>"Nebylo zadáno jméno vozidla."]);
        
        if(!isset($_FILES["vehicleIMG"]))
            $this->sendJson(["success"=>0, "message"=>"Nebyly vloženy obrázky."]);
        
        if(explode(".", $_FILES["vehicleIMG"]["name"])[1] != "webp")
            $this->sendJson(["success"=>0, "message"=>"Je povoleno vkládat obrázky pouze ve formátu .webp, použijte prosím konvertor."]);
                
        $this->search->insertNewVehicle(intval($_POST["vehicleID"]), $_POST["vehicleName"], $_POST["vehicleDesc"], $_FILES["vehicleIMG"], floatval($_POST["mapTop"]), floatval($_POST["mapLeft"]));
        $this->sendJson(["success"=>1, "message"=>"Posláno ke schválení, děkujeme."]);
    }

    public function actionMap(string $left, string $top){
        $this->setView("map");
        $this->template->left = $left;
        $this->template->top = $top;
        $this->sendTemplate();
    }

    public function actionDetail(string $title){
        $this->sendJson($this->search->showVehicleInfo($title));
    }

    public function actionWhisperer(string $like = ""){
        $this->sendJson($this->search->findVehicleLike($like));
    }
}

?>
