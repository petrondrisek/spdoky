<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use App\Models\Auth;
use Nette\Application\UI\Form as Form;
use Nette\Security\User;


final class AuthPresenter extends Nette\Application\UI\Presenter
{
    protected $auth;
    protected $user;

    public function __construct(Auth $auth, User $user){
        $this->auth = $auth;
        $this->user = $user;
    }

    public function beforeRender(){
        if($this->user->isLoggedIn() == 'yes') $this->redirectUrl('/');
    }

    public function actionLogin(){
        $this->setView("login");
    }

    public function actionLogout(){
        $this->user->logout();
        $this->redirectUrl('/');
        exit();
    }

    public function createComponentSignInForm(): Form 
    {
        $form = new Form;
        $form->addText('name', 'Username:');
        $form->addPassword('password', 'Heslo:');
        $form->addSubmit('send', 'Přihlásit se');
        $form->onSuccess[] = [$this, 'signInFormSuccess'];

        return $form;
    }

    public function signInFormSuccess(Form $form, $data): void 
    {
        try{
            $this->getUser()->login($data["name"], $data["password"]);

            if($this->getUser()->isLoggedIn() == 'yes') $this->FlashMessage("Přihlášen");
            else throw new Exception("Nastala neočekávaná chyba.");
        } catch (Nette\Security\AuthenticationException $e) {
            $this->FlashMessage($e->getMessage()); 
        } catch (\Exception $e){
            $this->FlashMessage($e->getMessage());
        } catch (\Error $e){
            $this->FlashMessage($e->getMessage());
        }
        
         
    }
}
