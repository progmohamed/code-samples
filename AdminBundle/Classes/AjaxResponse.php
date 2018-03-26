<?php

namespace AdminBundle\Classes;

class AjaxResponse
{
    private $data = [];

    private $success = true;

    private $message;

    /**
     * @return boolean
     */
    public function getSuccess()
    {
        return $this->success;
    }

    /**
     * @param boolean $success
     * @return AjaxResponse
     */
    public function setSuccess($success)
    {
        $this->success = $success;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     * @return AjaxResponse
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    public function getData($key)
    {
        if(array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }
    }

    public function setData($key, $value)
    {
        $this->data[$key] = $value;
        return $this;
    }

    public function getArray()
    {
        return array_merge($this->data, [
            'success' => $this->getSuccess(),
            'message' => $this->getMessage()
        ]);
    }

}