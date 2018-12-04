<?php

namespace JMP\Controllers;

use JMP\Services\Auth;
use PDO;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class EventController
{
    /**
     * @var \PDO
     */
    protected $db;
    /**
     * @var Auth
     */
    private $auth;

    /**
     * EventController constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->db = $container->get('database');
        $this->auth = $container->get('auth');
    }


    public function listEvents(Request $request, Response $response, array $args)
    {
        if ($this->auth->requestUser($request) === false) {
            return $response->withStatus(401); //TODO: create custom middleware to check authenthication
        }

        if ($request->getAttribute('has_errors')) {
            $errors = $request->getAttribute('errors');
            return $response->withJson(['errors' => $errors]);
        } // TODO: create custom middleware to handle validation errors

        if ($this->extendedValidation($request->getQueryParams()) === false) {
            return $response->withJson([
                'errors' => "Wrong parameter combination: limit or offset are both or neither required",
                'params' => $request->getQueryParams()
            ])->withStatus(400);
        }

        $val = $this->getLimitAndOffset($request->getQueryParams());

        $query = $request->getQueryParams();

        return $response->withJson(
            $this->getEvents($query, $val)
        );
    }

    private function getEvents(array $query, array $val)
    {
        $SQL = "SELECT *
                FROM event
                RIGHT JOIN event_has_group ehg ON event.id = ehg.event_id
                RIGHT JOIN event_type ON event.event_type_id = event_type.id
                WHERE (:groupId IS NULL OR ehg.group_id = :groupId)
                  AND (:eventType IS NULL OR event_type_id = :eventType)
                ORDER BY event.`from` DESC " .
            (empty($val) ? "LIMIT :lim OFFSET :off" : "");

        $stmt = $this->db->prepare($SQL);

        $groupId = isset($query['group']) ? $query['group'] : null;
        $eventType = isset($query['eventType']) ? $query['eventType'] : null;

        $stmt->bindValue(':groupId', $groupId, PDO::PARAM_INT);
        $stmt->bindValue(':eventType', $eventType, PDO::PARAM_INT);

        if (empty($val)) {
            $limit = empty($val) ? $val['limit'] : 0;
            $offset = empty($val) ? $val['offset'] : 0;
            $stmt->bindParam(':lim', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':off', $offset, PDO::PARAM_INT);
        }

        $stmt->execute();

        return $stmt->fetchAll();
    }

    private function extendedValidation(array $query)
    {
        if (isset($query['limit'])) {
            if (isset($query['offset'])) {
                return true;
            }
        }

        if (!isset($query['limit'])) {
            if (!isset($query['offset'])) {
                return true;
            }
        }
        return false;
    }

    private function getLimitAndOffset(array $query)
    {
        return [
            "limit" => $query['limit'],
            "offset" => $query['offset'],
        ];
    }

}