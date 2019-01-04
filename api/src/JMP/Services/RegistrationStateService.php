<?php

namespace JMP\Services;


use JMP\Models\RegistrationState;
use Psr\Container\ContainerInterface;

class RegistrationStateService
{
    /**
     * @var \PDO
     */
    protected $db;

    /**
     * EventService constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->db = $container->get('database');
    }

    /**
     * @return array RegistrationStates
     */
    public function getAllRegStates()
    {
        $sql = <<< SQL
SELECT id, name, reason_required as reasonRequired
FROM registration_state
SQL;

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $registrationStates = $stmt->fetchAll();

        foreach ($registrationStates as $key => $val) {
            $registrationStates[$key] = new RegistrationState($val);
        }

        return $registrationStates;
    }

    /**
     * @param int $registrationStateId
     * @return RegistrationState
     */
    public function getRegistrationTypeById(int $registrationStateId)
    {
        $sql = <<< SQL
SELECT id, name, reason_required as reasonRequired
FROM registration_state
WHERE id = :registrationStateId
SQL;

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':registrationStateId', $registrationStateId);
        $stmt->execute();
        return new RegistrationState($stmt->fetch());
    }
}