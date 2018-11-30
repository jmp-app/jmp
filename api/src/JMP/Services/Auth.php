<?php

namespace JMP\Services;

use DateTime;
use Firebase\JWT\JWT;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;

class Auth
{

    const SUBJECT_IDENTIFIER = 'username';

    private $db;
    /**
     * @var array
     */
    private $appConfig;

    /**
     * Auth constructor.
     */
    public function __construct(ContainerInterface $container)
    {
        //$this->db = $container->get('database');
        $this->appConfig = $container->get('settings')['jwt'];
        $this->db = $container->get('database');
    }

    public function generateToken(array $user)
    {
        $now = new DateTime();
        $future = new DateTime("now +2 hours");

        $payload = [
            "iat" => $now->getTimeStamp(),
            "exp" => $future->getTimeStamp(),
            "jti" => base64_encode(random_bytes(16)),
            'iss' => 'change_it',  // Issuer
            "sub" => $user[self::SUBJECT_IDENTIFIER],
            "admin" => true
        ];

        $secret = $this->appConfig['jwt']['secret'];
        $token = JWT::encode($payload, $secret, "HS256");

        return $token;
    }

    public function attempt($username, $password)
    {
        $stmt = $this->db->prepare("Select * from user where username = :username");
        $stmt->bindParam(':username', $username);

        $stmt->execute();

        $data = $stmt->fetch();

        if (sizeof($data) === 0) {
            return false;
        }

        return password_verify($password, $data['passwort']);

        // TODO: select user where username = username
        // TODO: hash given password
        // TODO: password_verify
        // TODO: return user when true else false
    }

    public function requestUser(Request $request)
    {
        // TODO: select user where username = token['sub']
        // TODO: return user when one exists else null
    }

}