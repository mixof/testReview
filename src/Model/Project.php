<?php

namespace App\Model;

class Project
{
    /**
     * @var array
     */
    public $_data;

	//Need to add param type because now in the $data can be any object, but we expected that there are an array
    public function __construct($data)
    {
        $this->_data = $data;
    }

    /**
     * @return int
     */
    public function getId()
    {
		//Need to check if value by key "id" exists
        return (int) $this->_data['id'];
    }

    /**
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->_data);
    }
}
