<?php

namespace AppBundle\Entity;

class JobInfo
{
    protected $jobName;

    protected $executeCommand;

    public function getJobName()
    {
        return $this->jobName;
    }

    public function setJobName($jobName)
    {
        $this->jobName = $jobName;

        return $this;
    }

    public function getExecuteCommand()
    {
        return $this->executeCommand;
    }

    public function setExecuteCommand($executeCommand)
    {
        $this->executeCommand = $executeCommand;

        return $this;
    }
}
