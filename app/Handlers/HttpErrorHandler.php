<?php

declare(strict_types=1);

namespace JEXServer\Handlers;

use DOMDocument;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpException;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpNotImplementedException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Handlers\ErrorHandler as SlimErrorHandler;
use Throwable;

/**
 * Class HttpErrorHandler
 *
 * @package JEXServer\Handlers
 */
class HttpErrorHandler extends SlimErrorHandler
{
    public const BAD_REQUEST = 'BAD_REQUEST';
    public const INSUFFICIENT_PRIVILEGES = 'INSUFFICIENT_PRIVILEGES';
    public const NOT_ALLOWED = 'NOT_ALLOWED';
    public const NOT_IMPLEMENTED = 'NOT_IMPLEMENTED';
    public const RESOURCE_NOT_FOUND = 'RESOURCE_NOT_FOUND';
    public const SERVER_ERROR = 'SERVER_ERROR';
    public const UNAUTHENTICATED = 'UNAUTHENTICATED';
    public const VALIDATION_ERROR = 'VALIDATION_ERROR';
    public const VERIFICATION_ERROR = 'VERIFICATION_ERROR';

    /**
     * Process the error to format the response.
     *
     * @return ResponseInterface
     */
    protected function respond(): Response
    {
        $code = 500;
        $dom = new DOMDocument('1.0', 'utf-8');

        $type = self::SERVER_ERROR;
        $description = 'An internal error has occurred while processing your request.';

        if ($this->exception instanceof HttpException) {
            $code = $this->exception->getCode();
            $description = $this->exception->getMessage();

            if ($this->exception instanceof HttpNotFoundException) {
                $type = self::RESOURCE_NOT_FOUND;
            } elseif ($this->exception instanceof HttpMethodNotAllowedException) {
                $type = self::NOT_ALLOWED;
            } elseif ($this->exception instanceof HttpUnauthorizedException) {
                $type = self::UNAUTHENTICATED;
            } elseif ($this->exception instanceof HttpForbiddenException) {
                $type = self::INSUFFICIENT_PRIVILEGES;
            } elseif ($this->exception instanceof HttpBadRequestException) {
                $type = self::BAD_REQUEST;
            } elseif ($this->exception instanceof HttpNotImplementedException) {
                $type = self::NOT_IMPLEMENTED;
            }
        }

        if (
            !($this->exception instanceof HttpException)
            && $this->exception instanceof Throwable
            && $this->displayErrorDetails
        ) {
            $description = $this->exception->getMessage();
        }

        $error = $dom->createElement('error');
        $error->appendChild($dom->createElement('code', "$code"));
        $error->appendChild($dom->createElement('type', $type));
        $error->appendChild($dom->createElement('description', $description));

        $dom->appendChild($error);

        $response = $this->responseFactory->createResponse($code);
        $response->getBody()->write($dom->saveXML());

        return $response->withHeader('Content-Type', 'application/xml');
    }
}