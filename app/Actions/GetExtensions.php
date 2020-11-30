<?php

declare(strict_types=1);

namespace JEXServer\Actions;

use Exception;
use JEXUpdate\Extensions\Application\Queries\GetExtensionCollection;
use JEXUpdate\Extensions\Domain\Contracts\ExtensionRepository;
use JEXUpdate\Extensions\Infrastructure\HTTP\XMLExtensionsResponder;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Class GetExtensionCollection
 *
 * @package JEXServer\Actions
 */
final class GetExtensions
{
    /**
     * The action responder.
     *
     * @var XMLExtensionsResponder
     */
    private XMLExtensionsResponder $responder;

    /**
     * The main use case to execute.
     *
     * @var GetExtensionCollection
     */
    private GetExtensionCollection $useCase;

    /**
     * GetExtensions constructor.
     *
     * @param  ExtensionRepository  $repository
     */
    public function __construct(ExtensionRepository $repository)
    {
        $this->useCase = new GetExtensionCollection($repository);
        $this->responder = new XMLExtensionsResponder();
    }

    /**
     * Execute the action.
     *
     * @param  Request  $request
     * @param  Response  $response
     *
     * @return Response
     * @throws Exception
     */
    public function __invoke(Request $request, Response $response): Response
    {
        return $this->responder->index(
            $request,
            $response,
            ['extensions' => $this->useCase->execute()]
        );
    }
}
