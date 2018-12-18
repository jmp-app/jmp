<?php

namespace JMP\Services;

use DateTime;
use Firebase\JWT\JWT;
use JMP\Models\User;
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
        $optional = $this->getUser($username);
        if ($optional->isFailure()) {
            // No User found
            return $optional;
        }

        $data = $optional->getData();

        // verify password
        if (password_verify($password, $data['password'])) {
            // valid password
            unset($data['password']);
            return Optional::success(new User($data));
        }

        // invalid password
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
            $optional = $this->getUser($token['sub']);
            if ($optional->isFailure()) {
                return $optional;
            } else {
                $user = $optional->getData();
                unset($user['password']);
                return Optional::success($user);
            }
        } else {
            return Optional::failure();
        }
    }


    /**
     * Returns the user if the jwt token is valid and authenticated and the user is an admin
     * @param Request $request
     * @return Optional
     */
    public function requestAdmin(Request $request)
    {
        $optional = $this->requestUser($request);

        if ($optional->isFailure()) {
            // No token supplied or invalid username
            return $optional;
        }

        if ($optional->getData()['isAdmin'] === "1") {
            // User has admin permissions
            return $optional;
        }

        // User hasn't admin permissions
        return Optional::failure();
    }

    /**
     * Selects the user by username
     * Note: the password is also selected and must be removed!
     * @param string $username
     * @return Optional
     */
    private function getUser(string $username): Optional
    {
        $sql = <<<SQL
SELECT user.id, username, lastname, firstname, email, token, password, password_change AS passwordChange,
#        Check if the user is an admin, 1-> admin, 0-> no admin
       NOT ISNULL((SELECT username
                   FROM user
                          LEFT JOIN membership m ON user.id = m.user_id
                          LEFT JOIN `group` g ON m.group_id = g.id
                   WHERE username = :username
                     AND g.name = :adminGroupName
       )) AS isAdmin
FROM user
WHERE username = :username
SQL;

        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':adminGroupName', $this->adminGroupName);

        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            return Optional::failure();
        }

        $data = $stmt->fetch();

        return Optional::success($data);
    }

}