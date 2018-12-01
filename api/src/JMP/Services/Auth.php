<?php

namespace JMP\Services;

use DateTime;
use Firebase\JWT\JWT;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;

class Auth
{

    const SUBJECT_IDENTIFIER = 'username';

    /**
     * @var \PDO
     */
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
        $this->appConfig = $container->get('settings')['jwt'];
        $this->db = $container->get('database');
        $this->logger = $container->get('logger');
    }

    /**
     * @param array $user
     * @return string
     * @throws \Exception
     */
    public function generateToken(array $user)
    {
        $now = new DateTime();
        $future = new DateTime("now +2 hours");//TODO: expiration of token?

        $payload = [
            "iat" => $now->getTimeStamp(),
            "exp" => $future->getTimeStamp(),
            "jti" => base64_encode(random_bytes(16)),
            'iss' => 'change_it',  // TODO: Issuer
            "sub" => $user[self::SUBJECT_IDENTIFIER],
            "admin" => true
        ];

        $secret = $this->appConfig['jwt']['secret'];
        $token = JWT::encode($payload, $secret, "HS256");

        return $token;
    }

    /**
     * @param $username string
     * @param $password string
     * @return bool|array
     */
    public function attempt($username, $password)
    {
        $stmt = $this->db->prepare("Select * from user where username = :username");
        $stmt->bindParam(':username', $username);

        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            return false;
        }

        $data = $stmt->fetch();

        if (password_verify($password, $data['password'])) {
            unset($data['password']);
            return $data;
        }

        return false;
    }

    /**
     * @param Request $request
     * @return bool|array
     */
    public function requestUser(Request $request)
    {
        if ($token = $request->getAttribute('token')) {

            $stmt = $this->db->prepare(
                "select id, username, lastname, firstname, email, token, password_change from user where username = :username"
            );

            $stmt->bindParam(':username', $token['sub']);

            $stmt->execute();

            if ($stmt->rowCount() === 0) {
                return false;
            }

            $data = $stmt->fetch();

            return $data;
        } else {
            return false;
        }
    }

}