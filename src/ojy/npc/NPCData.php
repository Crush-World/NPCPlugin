<?php

namespace ojy\npc;

class NPCData
{

    protected $name = '';

    protected $message = '';

    protected $command = '';

    /**
     * NPCData constructor.
     * @param string $name
     * @param string $message
     * @param string $command
     */
    public function __construct(string $name, string $message = '', string $command = '')
    {
        $this->name = $name;
        $this->message = $message;
        $this->command = $command;
    }

    public static function deserialize(string $data)
    {
        $data = explode("⊙", $data);
        return new self(...$data);
    }

    public function serialize()
    {
        return $this->name . "⊙" . $this->message . "⊙" . $this->command;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getCommand(): string
    {
        return $this->command;
    }

    /**
     * @param string $command
     */
    public function setCommand(string $command): void
    {
        $this->command = $command;
    }
}