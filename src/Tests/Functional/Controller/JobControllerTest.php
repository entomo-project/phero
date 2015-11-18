<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Entity\Job;
use AppBundle\Entity\JobInfo;
use AppBundle\Tests\Functional\AbstractBaseFunctionalTest;

class JobControllerTest extends AbstractBaseFunctionalTest
{
    public function testSetResultAction()
    {
        $job = new Job();

        $job->setId('42');

        $em = $this->getEntityManager();

        $em->persist($job);

        $em->flush();

        $this->jsonRequest(
            'PUT',
            '/api/v1/job/result/'.$job->getId(),
            [
                'foo' => 'bar',
            ]
        );

        $updatedJob = $em->merge($job);

        $this->assertResponseHttpOk();

        $jsonResponse = $this->getJsonResponse();

        $this->assertSame(
            [
                'status' => 'success',
            ],
            $jsonResponse
        );

        $this->assertSame(
            [
                'foo' => 'bar',
            ],
            $updatedJob->getResult()
        );
    }

    public function testSubmitActionSync()
    {
        $jobInfo = new JobInfo();

        $jobInfo->setJobName('test');

        $jobInfo->setExecuteCommand('echo "hello world"');

        $em = $this->getEntityManager();

        $em->persist($jobInfo);

        $em->flush();

        $jobInfo->setExecuteCommand('echo');

        $this->jsonRequest(
            'POST',
            '/api/v1/job/test/42',
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
                'jobId' => '42',
                'result' => [
                    'var' => 'foobar',
                ],
            ],
            $jsonResponse
        );

        $this->assertCount(
            0,
            $em->getRepository(Job::class)->findAll()
        );
    }

    public function testSubmitActionAsync()
    {
        $jobInfo = new JobInfo();

        $jobInfo->setJobName('test');

        $jobInfo->setExecuteCommand('echo "hello world"');

        $em = $this->getEntityManager();

        $em->persist($jobInfo);

        $em->flush();

        $jobInfo->setExecuteCommand('echo');

        $this->jsonRequest(
            'POST',
            '/api/v1/job/test/42',
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
                'jobId' => '42',
            ],
            $jsonResponse
        );

        $jobs = $em->getRepository(Job::class)->findAll();

        $this->assertCount(
            1,
            $jobs
        );

        /* @var $jobs Job[] */

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
