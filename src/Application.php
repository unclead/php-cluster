<?php

namespace unclead\phpcluster;

use Exception;
use React\EventLoop\LoopInterface;
use React\Http\Request;
use React\Http\Response;
use React\EventLoop\Factory as LoopFactory;
use React\Socket\Server as ServerSocket;
use React\Http\Server as HttpServer;
use Phroute\Phroute\RouteCollector;
use Phroute\Phroute\Dispatcher;
use unclead\phpcluster\collections\CmdCollection;
use unclead\phpcluster\commands\InitCollectionCommand;
use unclead\phpcluster\controllers\CmdController;
use Phroute\Phroute\Exception\HttpMethodNotAllowedException;
use Phroute\Phroute\Exception\HttpRouteNotFoundException;

/**
 * Class Application
 * @package unclead\phpcluster
 */
class Application
{
    /**
     * @var LoopInterface
     */
    private $loop;


    /**
     * @var Config
     */
    private $config;


    /**
     * @var ConsoleLogger
     */
    private $logger;

    /**
     * @var RouteCollector
     */
    private $router;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var Response
     */
    private $response;

    /**
     * @var CmdCollection
     */
    private $cmdCollection;


    /**
     * @var string
     */
    private $responseContentType = 'plain/text';


    public function __construct($argv)
    {
        $this->config = new Config($argv);
        $this->logger = new ConsoleLogger();
        $this->cmdCollection = new CmdCollection();

        $this->initHttpServer();
        $this->initRouter();

        $this->afterInit();
    }

    private function initHttpServer()
    {
        $socket = new ServerSocket($this->getLoop());
        $socket->listen($this->config->getPort(), $this->config->getHost());

        $http = new HttpServer($socket, $this->getLoop());
        $http->on('request', [$this, 'handleRequest']);
    }


    private function afterInit()
    {
        $this->getLogger()->info('Sync collection with other instances in cluster');
        $command = new InitCollectionCommand($this->getCmdCollection(), $this->getConfig());
        $command->execute();
    }

    /**
     * Run application.
     */
    public function run()
    {
        $this->logger->debug('Application is started on ' . $this->config->getHost() . ':' . $this->config->getPort());
        $this->logger->debug('Partner application ports: ' . implode(',', $this->config->getPartnerPorts()));
        $this->getLoop()->run();
    }

    private function initRouter()
    {
        $this->router = new RouteCollector();
        $this->router->get('/cmd{id:\d+}', function($id) {
            $controller = new CmdController($this);

            $content = $controller->actionIncrement($id);
            return $content;
        });

        $this->router->get('/cmd', function() {
            $controller = new CmdController($this);
            $this->responseContentType = 'application/json';
            $content = $controller->actionSummary();
            return $content;
        });

        $this->router->post('/cmd{id:\d+}', function($id) {
            $this->request->on('data', function($data) use ($id){
                parse_str($data, $data);
                $controller = new CmdController($this);
                $content = $controller->actionUpdate($id, $data);
                return $content;
            });
        });
    }

    public function handleRequest(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;

        $dispatcher = new Dispatcher($this->router->getData());

        try {
            $content = $dispatcher->dispatch($request->getMethod(), $request->getPath());
            $response->writeHead(200, ['Content-Type' => $this->responseContentType]);
            $response->write($content);
        } catch (HttpRouteNotFoundException $e) {
            $response->writeHead(404, ['Content-Type' => 'text/plain']);
        } catch (HttpMethodNotAllowedException $e) {
            $response->writeHead(403, ['Content-Type' => 'text/plain']);
        } catch (Exception $e) {
            $this->getLogger()->error($e->getMessage());
            $response->writeHead(500, ['Content-Type' => 'text/plain']);
        }

        $response->end();
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return LoopInterface
     */
    public function getLoop()
    {
        if (is_null($this->loop)) {
            $this->loop = LoopFactory::create();
        }
        return $this->loop;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return CmdCollection
     */
    public function getCmdCollection()
    {
        return $this->cmdCollection;
    }

    /**
     * @return ConsoleLogger
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }
}