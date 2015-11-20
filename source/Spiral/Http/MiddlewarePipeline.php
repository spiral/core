<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
namespace Spiral\Http;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Spiral\Core\Component;
use Spiral\Core\ContainerInterface;
use Spiral\Core\Exceptions\SugarException;
use Spiral\Core\Traits\SaturateTrait;
use Spiral\Http\Traits\JsonTrait;

/**
 * Pipeline used to pass request and response thought the chain of middlewares.
 */
class MiddlewarePipeline extends Component
{
    /**
     * Sugar.
     */
    use SaturateTrait, JsonTrait;

    /**
     * Pipeline automatically replaces outer request with active instance for internal endpoint.
     *
     * @var mixed
     */
    private $requestScope = null;

    /**
     * @var mixed
     */
    private $responseScope = null;

    /**
     * Endpoint should be called at the deepest level of pipeline.
     *
     * @var callable
     */
    private $target = null;

    /**
     * Pipeline middlewares.
     *
     * @var callable[]|MiddlewareInterface[]
     */
    protected $middlewares = [];

    /**
     * @invisible
     * @var ContainerInterface
     */
    protected $container = null;

    /**
     * @param callable[]|MiddlewareInterface[] $middleware
     * @param ContainerInterface               $container Spiral container is needed, due scoping.
     * @throws SugarException
     */
    public function __construct(
        array $middleware = [],
        ContainerInterface $container = null
    ) {
        $this->middlewares = $middleware;
        $this->container = $this->saturate($container, ContainerInterface::class);
    }

    /**
     * Register new middleware at the end of chain.
     *
     * @param callable $middleware Can accept middleware class name.
     * @return $this
     */
    public function add($middleware)
    {
        $this->middlewares[] = $middleware;

        return $this;
    }

    /**
     * Set pipeline target.
     *
     * @param callable $target
     * @return $this
     */
    public function target($target)
    {
        $this->target = $target;

        return $this;
    }

    /**
     * Pass request and response though every middleware to target and return generated and wrapped
     * response.
     *
     * @param Request  $request
     * @param Response $response
     * @return Response
     */
    public function run(Request $request, Response $response)
    {
        return $this->next(0, $request, $response);
    }

    /**
     * Get next chain to be called. Exceptions will be converted to responses.
     *
     * @param int      $position
     * @param Request  $request
     * @param Response $response
     * @return null|Response
     * @throws \Exception
     */
    protected function next($position, Request $request, Response $response)
    {
        if (!isset($this->middlewares[$position])) {
            //Middleware target endpoint to be called and converted into response
            return $this->mountResponse($request, $response);
        }

        /**
         * @var callable $next
         */
        $next = $this->middlewares[$position];

        if (is_string($next)) {
            //Resolve using container
            $next = $this->container->construct($next);
        }

        //Executing next middleware
        return $next($request, $response, $this->getNext($position, $request, $response));
    }

    /**
     * Run pipeline target and return generated response.
     *
     * @param Request  $request
     * @param Response $response
     * @return Response
     */
    protected function mountResponse(Request $request, Response $response)
    {
        $this->openScope($request, $response);

        ob_start();
        $outputLevel = ob_get_level();
        $output = '';
        $result = null;

        try {

            /**
             * Debug: this method contain code to open and close scope for [ServerRequestInterface]
             * and [ResponseInterface].
             */
            $result = $this->execute($request, $response);
        } finally {
            while (ob_get_level() > $outputLevel) {
                $output = ob_get_clean() . $output;
            }

            //Closing request/response scope
            $this->restoreScope();
        }

        return $this->wrapResponse($response, $result, ob_get_clean() . $output);
    }

    /**
     * Execute endpoint and return it's result.
     *
     * @param Request  $request
     * @param Response $response
     * @return mixed
     */
    protected function execute(Request $request, Response $response)
    {
        return call_user_func($this->target, $request, $response);
    }

    /**
     * Convert endpoint result into valid response.
     *
     * @param Response $response Initial pipeline response.
     * @param mixed    $result   Generated endpoint output.
     * @param string   $output   Buffer output.
     * @return Response
     */
    private function wrapResponse(Response $response, $result = null, $output = '')
    {
        if ($result instanceof Response) {
            if (!empty($output) && $result->getBody()->isWritable()) {
                $result->getBody()->write($output);
            }

            return $result;
        }

        if (is_array($result) || $result instanceof \JsonSerializable) {
            return $this->writeJson($response, $result);
        }

        $response->getBody()->write($result . $output);

        return $response;
    }

    /**
     * Get next callable element.
     *
     * @param int      $position
     * @param Request  $outerRequest
     * @param Response $outerResponse
     * @return \Closure
     */
    private function getNext($position, Request $outerRequest, Response $outerResponse)
    {
        $next = function ($request = null, $response = null) use (
            $position,
            $outerRequest,
            $outerResponse
        ) {
            //This function will be provided to next (deeper) middleware
            return $this->next(
                ++$position,
                !empty($request) ? $request : $outerRequest,
                !empty($response) ? $response : $outerResponse
            );
        };

        return $next;
    }

    /**
     * Open container scope and share instances of request and response.
     *
     * @param Request  $request
     * @param Response $response
     */
    private function openScope(Request $request, Response $response)
    {
        $this->requestScope = $this->container->replace(Request::class, $request);
        $this->responseScope = $this->container->replace(Response::class, $response);
    }

    /**
     * Restore initial (pre pipeline) request and response.
     */
    private function restoreScope()
    {
        $this->container->restore($this->requestScope);
        $this->container->restore($this->responseScope);
    }
}