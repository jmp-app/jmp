<?php


namespace jmp\Utils;


class Optional
{

    /**
     * @var mixed
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
     * @param mixed $data
     * @return Optional
     */
    public static function success($data): Optional
    {
        $optional = new Optional(true);
        return $optional->setData($data);
    }

    /**
     * @param mixed $data
     * @return Optional
     */
    private function setData($data): Optional
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
     * @return mixed
     */
    public function getData()
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

    /**
     * @return bool
     */
    public function isFailure(): bool
    {
        return !$this->success;
    }


}