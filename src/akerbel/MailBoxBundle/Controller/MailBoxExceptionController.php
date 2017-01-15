<?php

namespace akerbel\MailBoxBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Symfony\Component\HttpFoundation\Request;

class MailBoxExceptionController
{
    public function showAction(Request $request, FlattenException $exception, DebugLoggerInterface $logger = null)
    {
        return new JsonResponse(
            [
                'result' => 'error',
                'error_code' => $exception->getCode(),
                'error_text' => $exception->getMessage(),
            ],
            ($exception->getCode() ? $exception->getCode() : 500)
        );
    }

}
