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

    public function getId()
    {
        return $this->id;
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
}