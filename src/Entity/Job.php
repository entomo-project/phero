<?php

namespace AppBundle\Entity;

class Job
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var JobInfo
     */
    protected $jobInfo;

    /**
     * @var array
     */
    protected $parameters;

    /**
     * @var string
     */
    protected $result;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getJobInfo()
    {
        return $this->jobInfo;
    }

    public function setJobInfo(JobInfo $jobInfo)
    {
        $this->jobInfo = $jobInfo;

        return $this;
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    public function getResult()
    {
        return $this->result;
    }

    public function setResult($result)
    {
        $this->result = $result;

        return $this;
    }
}