<?php


namespace App\Classes\Security;


class SecurityResponseObject
{
    private $data;

    //@var int $code
    private $code;

    //@var string $place
    private $location;

    public function __construct($data, int $code, string $location = null)
    {
        $this->data = $data;
        $this->code = $code;
        $this->location = $location;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getLocation(): string
    {
        return $this->location;
    }


}