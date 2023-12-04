<?php

declare(strict_types=1);

namespace App\Exception;

use App\Dto\ErrorMessage;
use App\Dto\RestResponse;
use Exception;
use Throwable;

class RestException extends Exception
{
    private RestResponse $restResponse;

    public function __construct($message = '', $code = 400, Throwable $previous = null)
    {
        $this->restResponse = RestResponse::new($code);

        if ($previous !== null) {
            $this->restResponse->addError(ErrorMessage::fromThrowable($previous));
        }

        parent::__construct($message, $code, $previous);
    }

    public function getRestResponse(): RestResponse
    {
        return $this->restResponse;
    }
}
