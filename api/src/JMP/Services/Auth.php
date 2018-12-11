<?php

namespace JMP\Services;

use DateTime;
use Firebase\JWT\JWT;
use JMP\Utils\Optional;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;

class Auth
{

    private $subjectIdentifier;
    private $adminGroupName;

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
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->db = $container->get('database');
        $this->logger = $container->get('logger');
        $this->appConfig = $container->get('settings');

        $this->subjectIdentifier = $this->appConfig['auth']['subjectIdentifier'];
        $this->adminGroupName = $this->appConfig['auth']['adminGroupName'];
    }

    /**
     * @param array $user
     * @return string
     * @throws \Exception
     */
    public function generateToken(array $user): string
    {
        $now = new DateTime();
        $future = new DateTime("now +2 hours");

        $payload = [
            "iat" => $now->getTimeStamp(),
            "exp" => $future->getTimeStamp(),
            "jti" => base64_encode(random_bytes(16)),
            'iss' => $this->appConfig['app']['url'],
            "sub" => $user[$this->subjectIdentifier],
        ];

        $secret = $this->appConfig['jwt']['secret'];
        $token = JWT::encode($payload, $secret, "HS256");

        return $token;
    }

    /**
     * Verify the login request by username and password
     * @param $username string
     * @param $password string
     * @return Optional
     */
    public function attempt($username, $password)
    {
        // search user in db
        $stmt = $this->db->prepare("SELECT * FROM user WHERE username=:username");
        $stmt->bindParam(':username', $username);

        $stmt->execute();

        // check if results are present
        if ($stmt->rowCount() === 0) {
            return Optional::failure();
        }

        $data = $stmt->fetch();

        // 
        if (password_verify($password, $data['password'])) {
            unset($data['password']);
            unset($data['token']);
            return Optional::success($data);
        }

        return Optional::failure();
    }

    /**
     * Returns the user if the jwt token is valid and authenticated and the subject exists
     * @param Request $request
     * @return Optional
     */
    public function requestUser(Request $request)
    {
        if ($token = $request->getAttribute('token')) {

            $sql = <<<SQL
SELECT id, username, lastname, firstname, email, token, password_change AS passwordChange
FROM user
WHERE username=:username
SQL;

            $stmt = $this->db->prepare($sql);

            $stmt->bindParam(':username', $token['sub']);

            $stmt->execute();

            if ($stmt->rowCount() === 0) {
                return Optional::failure();
            }

            $data = $stmt->fetch();

            return Optional::success($data);
        } else {
            return Optional::failure();
        }
    }

    public function requestAdmin(Request $request)
    {
        if ($token = $request->getAttribute('token')) {
            $sql = <<<SQL
SELECT user.id, username, lastname, firstname, email, token, password_change AS passwordChange
FROM user
       LEFT JOIN membership m on user.id = m.user_id
       LEFT JOIN `group` g on m.group_id = g.id
WHERE username = :username
  AND g.name = :adminGroupName
SQL;

            $stmt = $this->db->prepare($sql);

            $stmt->bindParam(':username', $token['sub']);
            $stmt->bindParam(':adminGroupName', $this->adminGroupName);

            $stmt->execute();

            if ($stmt->rowCount() === 0) {
                return Optional::failure();
            }

            $data = $stmt->fetch();

            return Optional::success($data);
        } else {
            return Optional::failure();
        }

    }

}