<?php

declare(strict_types=1);

namespace JEXServer\Actions;

use Exception;
use JEXUpdate\Updates\Application\Queries\GetExtensionUpdatesCollection;
use JEXUpdate\Updates\Domain\Contracts\UpdateRepository;
use JEXUpdate\Updates\Infrastructure\HTTP\XMLUpdatesResponder;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Class GetExtensionUpdates
 *
 * @package JEXServer\Actions
 */
final class GetExtensionUpdates
{
    /**
     * The action responder.
     *
     * @var XMLUpdatesResponder
     */
    private XMLUpdatesResponder $responder;

    /**
     * The main use case to execute.
     *
     * @var GetExtensionUpdatesCollection
     */
    private GetExtensionUpdatesCollection $useCase;

    /**
     * GetExtensionUpdates constructor.
     *
     * @param  UpdateRepository  $repository
     */
    public function __construct(UpdateRepository $repository)
    {
        $this->useCase = new GetExtensionUpdatesCollection($repository);
        $this->responder = new XMLUpdatesResponder();
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
            ['updates' => $this->useCase->execute($request->getAttribute('extension'))]
        );
    }
}
