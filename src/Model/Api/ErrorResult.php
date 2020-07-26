<?php declare(strict_types=1);

namespace App\Model\Api;

use JMS\Serializer\Annotation as JMS;

class ErrorResult extends AbstractApiResult
{
    /**
     * @JMS\Expose()
     */
    protected int $httpStatusCode;

    /**
     * @JMS\Expose()
     * @JMS\Type("array<string>")
     */
    protected array $errorMessageList;

    public function getHttpStatusCode(): int
    {
        return $this->httpStatusCode;
    }

    public function getErrorMessageList(): array
    {
        return $this->errorMessageList;
    }
}
