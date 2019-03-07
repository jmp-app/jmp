<?php

namespace JMP\Services;


use JMP\Models\RegistrationState;
use JMP\Utils\Optional;
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
     * @return Optional
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
        $registrationState = $stmt->fetch();
        if ($registrationState === false) {
            return Optional::failure();
        } else {
            return Optional::success(new RegistrationState($registrationState));
        }
    }


    /**
     * Checks whether a registration state with the given id already exists
     * @param int $registrationStateId
     * @return bool
     */
    public function registrationStateExists(int $registrationStateId): bool
    {
        $sql = <<<SQL
            SELECT id
            FROM jmp.`registration_state`
            WHERE id = :id
SQL;

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $registrationStateId);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}