<?php

namespace App\Model;

use PDO;

class UserManager extends AbstractManager
{
    public const TABLE = 'user';

    public function validateUser($email, $password): array|false
    {
        $statement = $this->pdo->prepare("SELECT * FROM " . self::TABLE . " WHERE email = :email AND password = :password");
        $statement->bindValue("email", $email, PDO::PARAM_STR);
        $statement->bindValue("password", $password, PDO::PARAM_STR);
        $statement->execute();

        $user = $statement->fetch(PDO::FETCH_ASSOC);

        return $user;
    }
}
