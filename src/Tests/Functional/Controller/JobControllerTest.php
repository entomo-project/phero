<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\Functional\AbstractBaseFunctionalTest;

class JobControllerTest extends AbstractBaseFunctionalTest
{
    public function testSubmitActionSync()
    {
        $jobInfo = new \AppBundle\Entity\JobInfo();

        $jobInfo->setJobName('test');

        $jobInfo->setExecuteCommand('echo "hello world"');

        $em = $this->getEntityManager();

        $em->persist($jobInfo);

        $em->flush();

        $jobInfo->setExecuteCommand('echo');

        $this->jsonRequest(
            'POST',
            '/api/v1/job/test',
            [
                'parameters' => [
                    'var' => 'foobar',
                ],
                'execute' => 'sync',
            ]
        );

        $this->assertResponseHttpOk();

        $jsonResponse = $this->getJsonResponse();

        $this->assertSame(
            [
                'status' => 'success',
                'result' => [
                    'var' => 'foobar',
                ],
            ],
            $jsonResponse
        );

        $this->assertCount(
            0,
            $em->getRepository(\AppBundle\Entity\Job::class)->findAll()
        );
    }

    public function testSubmitActionAsync()
    {
        $jobInfo = new \AppBundle\Entity\JobInfo();

        $jobInfo->setJobName('test');

        $jobInfo->setExecuteCommand('echo "hello world"');

        $em = $this->getEntityManager();

        $em->persist($jobInfo);

        $em->flush();

        $jobInfo->setExecuteCommand('echo');

        $this->jsonRequest(
            'POST',
            '/api/v1/job/test',
            [
                'parameters' => [
                    'var' => 'foobar',
                ],
                'execute' => 'async',
            ]
        );

        $this->assertResponseHttpOk();

        $jsonResponse = $this->getJsonResponse();

        $this->assertSame(
            [
                'status' => 'success',
            ],
            $jsonResponse
        );

        $jobs = $em->getRepository(\AppBundle\Entity\Job::class)->findAll();

        $this->assertCount(
            1,
            $jobs
        );

        /* @var $jobs \AppBundle\Entity\Job[] */

        $this->assertSame(
            $jobInfo,
            $jobs[0]->getJobInfo()
        );

        $this->assertSame(
            [
                'var' => 'foobar',
            ],
            $jobs[0]->getParameters()
        );
    }
}
