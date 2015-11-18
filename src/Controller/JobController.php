<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Job;
use AppBundle\Entity\JobInfo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class JobController extends Controller
{
    public function setResultAction($jobId, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $job = $em->find(Job::class, $jobId);

        /* @var $job Job */

        $job->setResult(
            $request->request->all()
        );

        $em->flush();

        $data = [
            'status' => 'success',
        ];

        return new JsonResponse($data);
    }

    public function createAction($jobName, $jobId, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $jobInfo = $em->find(
            JobInfo::class,
            $jobName
        );

        /* @var $jobInfo JobInfo */

        $parameters = $request->request->get('parameters');

        $output = [];

        $execute = $request->request->get('execute');

        if ('sync' === $execute) {
            exec(
                sprintf(
                    '%s %s',
                    $jobInfo->getExecuteCommand(),
                    escapeshellarg(
                        json_encode($parameters)
                    )
                ),
                $output
            );

            $result = json_decode($output[0]);
        } else {
            $job = new Job();

            $job->setId($jobId);

            $job->setParameters($parameters);

            $job->setJobInfo($jobInfo);

            $em->persist($job);

            $em->flush();

            exec(
                sprintf(
                    '%s %s',
                    $jobInfo->getExecuteCommand(),
                    escapeshellarg(
                        json_encode($parameters)
                    )
                ),
                $output
            );
        }

        $data = [
            'status' => 'success',
            'jobId' => $jobId,
        ];

        if (isset($result)) {
            $data['result'] = $result;
        }

        return new JsonResponse($data);
    }

    public function getResultAction(Request $request)
    {
    }
}
