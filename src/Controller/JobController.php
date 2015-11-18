<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Job;
use AppBundle\Entity\JobInfo;
use AppBundle\Manager\CallbackManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class JobController extends Controller
{
    /**
     * @return CallbackManager
     */
    protected function getCallbackManager()
    {
        return $this->get('app.manager.callback');
    }

    public function setResultAction($jobId, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $job = $em->find(Job::class, $jobId);

        /* @var $job Job */

        $result = $request->request->all();

        $callbackResult = $this->getCallbackManager()->sendBackResult(
            $jobId,
            $result
        );

        if (isset($callbackResult['status']) && $callbackResult['status'] === 'success') {
            $em->remove($job);
        } else {
            $job->setResult($result);
        }

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
