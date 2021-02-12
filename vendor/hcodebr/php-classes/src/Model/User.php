<?php

namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;

class User extends Model {

    const SESSION = "User";

    public  static function login($login, $password) 
    {

		if (!User::reCAPTCHA()) throw new \Exception("Erro no captcha");



        $sql = new Sql();

        $results = $sql->select("SELECT * FROM tb_users WHERE deslogin = :LOGIN", array(

            ":LOGIN"=>$login

        ));

        if (count($results) === 0)
        {
            throw new \Exception("Usuario inexistente ou senha invalida.");
            
        }

        $data = $results[0];

        if (password_verify($password, $data["despassword"]) === true)
        {


            $user = new User();

            $user->setData($data);

            $_SESSION[User::SESSION] = $user->getValues();

            return $user;



        } else 
        {
            throw new \Exception("Usuario inexistente ou senha invalida.");
            
        }

    }

    public static function verifyLogin($inadmin = true)
    {
        if (
            !isset($_SESSION[User::SESSION])
			||
			!$_SESSION[User::SESSION]
			||
			!(int)$_SESSION[User::SESSION]["iduser"] > 0
            ||
            (bool)$_SESSION[User::SESSION]['inadmin'] !== $inadmin
        ) {
            header("Location: /admin/login");
            exit;
        }
    }


	public static function reCAPTCHA()
	{
		   
		$token = $_POST['token'];
		$action = $_POST['action'];

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");

		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
			"secret"=>"6LfXYVIaAAAAAP3lOerLKwoomDx2OiBP_B5soHlq",
			"response"=>$token,
			"remoteip"=>$_SERVER["REMOTE_ADDR"]
		)));



		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$recaptcha = json_decode(curl_exec($ch), true);

		curl_close($ch);

		// $final = [];


		if ($recaptcha["success"] && $recaptcha["action"] == $action && $recaptcha["score"] >= 0.6)
		{
			
			return true;
			
		} else 
		{
			return false;
		}
 
	}

    public static function logout()
    {
        $_SESSION[User::SESSION] = NULL;
    }

    public static function listAll() // Lista todos usuarios
    {
        $sql = new Sql();
        return $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) ORDER BY b.desperson");
    }

    public function save() // Salva novo usuario
    {
        $sql = new Sql();

        $results = $sql->select("CALL sp_users_save(:desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(

            ":desperson"=> $this->getdesperson(),
            ":deslogin"=> $this->getdeslogin(),
            ":despassword"=> $this->getdespassword(),
            ":desemail"=> $this->getdesemail(),
            ":nrphone"=> $this->getnrphone(),
            ":inadmin"=> $this->getinadmin()
        ));

        $this->setData($results[0]);
    }

    public function update() // Atualiza usuario
    {
        $sql = new Sql();

        $results = $sql->select("CALL sp_usersupdate_save(:iduser, :desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(

            ":iduser"=>$this->getiduser(),
            ":desperson"=> $this->getdesperson(),
            ":deslogin"=> $this->getdeslogin(),
            ":despassword"=> $this->getdespassword(),
            ":desemail"=> $this->getdesemail(),
            ":nrphone"=> $this->getnrphone(),
            ":inadmin"=> $this->getinadmin()
        ));

        $this->setData($results[0]);
    }   

    public function get($iduser)
    {
        $sql = new Sql();
        $results = $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) WHERE a.iduser = :iduser", array(

            ":iduser"=>$iduser
        ));

        $this->setData($results[0]);
    }

    public function delete() // Deleta usuario
    {
        $sql = new Sql();
        $sql->query("CALL sp_users_delete(:iduser)", array(
            ":iduser"=>$this->getiduser()
        ));
    }

    public static function getPasswordHash($password)
	{

		return password_hash($password, PASSWORD_DEFAULT, [
			'cost'=>12
		]);

	}


}


?>