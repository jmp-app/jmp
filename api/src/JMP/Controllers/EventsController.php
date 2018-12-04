<?php

namespace JMP\Controllers;

use JMP\Services\Auth;
use PDO;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class EventsController
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
            return $response->withStatus(401);
        }

        if ($request->getAttribute('has_errors')) {
            $errors = $request->getAttribute('errors');
            return $response->withJson(['errors' => $errors])->withStatus(400);
        }

        if ($this->extendedValidation($request->getQueryParams()) === false) {
            return $response->withJson([
                'errors' => [
                    'message' => "Wrong parameter combination: limit or offset are both or neither required",
                    'params' => $request->getQueryParams()
                ]
            ])->withStatus(400);
        }

        $data = $this->fetchData($request);

        return $response->withJson($data);
    }

    /**
     * @param Request $request
     * @return array
     */
    private function fetchData(Request $request)
    {
        $val = $this->getLimitAndOffset($request->getQueryParams());

        $query = $request->getQueryParams();
        $events = $this->getEvents($query, $val);

        $events = $this->fetchAdditionalEventData($events);

        return $events;
    }

    private function fetchAdditionalEventData(array $events)
    {
        foreach ($events as $key => $event) {
            $events[$key]['groups'] = $this->getGroupsOfEvent($event);
            $events[$key]['eventType'] = $this->getEventTypeOfEvent($event);
            $events[$key]['defaultRegistrationState'] = $this->getDefaultRegistrationStateOfEvent($event);
        }

        return $events;

    }

    private function getDefaultRegistrationStateOfEvent($event)
    {
        $sql = <<< SQL
SELECT id, name, reason_required
FROM registration_state
WHERE id = :defaultRegistrationStateId
SQL;

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':defaultRegistrationStateId', $event['defaultRegistrationState']);
        $stmt->execute();
        return $stmt->fetch();
    }

    private function getEventTypeOfEvent($event)
    {
        $sql = <<< SQL
SELECT *
FROM event_type
WHERE id = :eventTypeId
SQL;

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':eventTypeId', $event['eventType']);
        $stmt->execute();
        return $stmt->fetch();
    }

    private function getGroupsOfEvent($event)
    {

        $sql = <<< SQL
SELECT id, name
FROM `group`
LEFT JOIN event_has_group ehg on `group`.id = ehg.group_id
WHERE ehg.event_id = :eventId
SQL;

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':eventId', $event['id']);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    private function getEvents(array $query, array $val)
    {
        $sql = <<< SQL
                SELECT event.id,
                       event.title,
                       event.description,
                       `from`,
                       `to`,
                       place,
                       event_type_id AS eventType,
                       default_registration_state_id AS defaultRegistrationState
                FROM event
                RIGHT JOIN event_has_group ehg ON event.id = ehg.event_id
                RIGHT JOIN event_type ON event.event_type_id = event_type.id
                WHERE (:groupId IS NULL OR ehg.group_id = :groupId)
                  AND (:eventType IS NULL OR event_type_id = :eventType)
                ORDER BY event.`from` DESC 
SQL;

        $sql .= (empty($val) ? "LIMIT :lim OFFSET :off" : "");

        $stmt = $this->db->prepare($sql);

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