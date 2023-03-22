<?php
declare(strict_types=1);

namespace App\Presenters;

use \Nette;
use \App\Models\Admin as Admin;
use \App\Models\Detail as Detail;

final class AdminPresenter extends \Nette\Application\UI\Presenter
{
    public $admin;
    public $search;

    public function __construct(Admin $admin, Detail $search){
        $this->admin = $admin;
    }

    protected function startup()
    {
        parent::startup();
        if (!$this->getUser()->isLoggedIn()) {
            $this->redirectUrl('/auth/login');
            exit();
        }
    }

    public function beforeRender(){
        $this->getVehicles();
    }

    public function actionValidate(int $id = -1){
        try{
            if($id == -1) 
                throw new \Nette\InvalidArgumentException("ID nenalezeno");
            
            $this->admin->validate($id);
            
            $this->flashMessage("Schváleno");
        } catch (\Exception $e){
            $this->flashMessage($e->getMessage());
        } catch (\Error $e){
            $this->flashMessage($e->getMessage());
        } 

        $this->setView("default");
    }

    public function actionDelete(int $id = -1){
        try{
            if($id == -1) 
                throw new \Nette\InvalidArgumentException("ID nenalezeno");
            
            $this->admin->delete($id);
            
            $this->flashMessage("Smazáno");
        } catch (\Exception $e){
            $this->flashMessage($e->getMessage());
        } catch (\Error $e){
            $this->flashMessage($e->getMessage());
        } 

        $this->setView("default");
    }

    private function getVehicles(){
        $vehicles = $this->admin->getVehicles();

        $this->template->vehicles = $vehicles;
    }
}
