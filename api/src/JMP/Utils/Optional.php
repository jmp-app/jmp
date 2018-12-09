<?php


namespace JMP\Utils;


class Optional
{

    /**
     * @var array
     */
    private $data;

    /**
     * @var bool
     */
    private $success;

    /**
     * Optional constructor.
     * @param bool $success
     */
    private function __construct(bool $success)
    {
        $this->success = $success;
        $this->data = null;
    }

    /**
     * @param array $data
     * @return Optional
     */
    public static function success(array $data): Optional
    {
        $optional = new Optional(true);
        return $optional->setData($data);
    }

    /**
     * @param array $data
     * @return Optional
     */
    private function setData(array $data): Optional
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return Optional
     */
    public static function failure(): Optional
    {
        return new Optional(false);
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function isFailure(): bool
    {
        return !$this->success;
    }


}