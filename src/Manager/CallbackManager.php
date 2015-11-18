<?php

namespace AppBundle\Manager;

class CallbackManager
{
    /**
     * @var \GuzzleHttp\ClientInterface
     */
    protected $guzzleClient;

    public function __construct(\GuzzleHttp\ClientInterface $guzzleClient)
    {
        $this->guzzleClient = $guzzleClient;
    }

    /**
     * @param string $jobId
     * @param array $jobResult
     *
     * @return array
     */
    public function sendBackResult($jobId, array $jobResult)
    {
        $response = $this->guzzleClient->request(
            'POST',
            '',//TODO
            [
                'json' => $jobResult
            ]
        );

        return json_decode($response->getBody()->getContents());
    }
}
