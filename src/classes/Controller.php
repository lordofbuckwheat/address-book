<?php

use Slim\Http\Request;
use Slim\Http\Response;

class Controller
{

    /** @var \Psr\Container\ContainerInterface $container */
    protected $container;
    /** @var RecordManager $db */
    protected $rm;

    public function __construct(\Psr\Container\ContainerInterface $container)
    {
        $this->container = $container;
        $this->rm = new RecordManager($container->get('db'));
    }

    public function index(Request $request, Response $response, $args): \Psr\Http\Message\ResponseInterface {
        /** @var Slim\Views\PhpRenderer $renderer */
        $renderer = $this->container->get('renderer');
        $search = $request->getParam('search');
        if (empty($search)) {
            $result = $this->rm->get();
        } else {
            $result = $this->rm->search($search);
        }
        return $renderer->render($response, 'index.phtml', [
            'records' => json_encode($result)
        ]);
    }

    public function dummy(Request $request, Response $response, $args): Response {
        $count = $request->getParam('count') ?: 100;
        $people = [];
        for ($i = 0; $i < $count; $i++) {
            $person = @json_decode(file_get_contents('http://randus.ru/api.php'), true);
            $people[] = [
                'name' => $person['fname'].' '.$person['lname'],
                'email' => mb_strtolower($person['login']).'@dummy.ru',
                'phone' => $person['phone']
            ];
        }
        $this->rm->insertMany($people);
        return $response->withJson([
            'success' => true,
            'message' => 'Records successfully added'
        ]);
    }

    public function create(Request $request, Response $response, $args): Response {
        $id = $this->rm->insert([
            'name' => $request->getParam('name', ''),
            'phone' => $request->getParam('phone', ''),
            'email' => $request->getParam('email', '')
        ]);
        return $response->withJson([
            'success' => true,
            'message' => 'Record successfully added',
            'data' => $id
        ]);
    }

    public function update(Request $request, Response $response, $args): Response {
        $this->rm->update($args['recordID'], [
            'name' => $request->getParam('name', ''),
            'phone' => $request->getParam('phone', ''),
            'email' => $request->getParam('email', '')
        ]);
        return $response->withJson([
            'success' => true,
            'message' => 'Record successfully updated',
        ]);
    }

    public function delete(Request $request, Response $response, $args): Response {
        $this->rm->delete($args['recordID']);
        return $response->withJson([
            'success' => true,
            'message' => 'Record successfully deleted',
        ]);
    }

    public function read(Request $request, Response $response, $args): Response {
        $search = $request->getParam('search');
        if (empty($search)) {
            $result = $this->rm->get();
        } else {
            $result = $this->rm->search($search);
        }
        if (empty($result)) {
            return $response->withJson([
                'success' => false,
                'message' => 'No records found'
            ]);
        }
        return $response->withJson([
            'success' => true,
            'message' => 'Records successfully loaded',
            'data' => $result
        ]);
    }

    public function middleware(Request $request, Response $response, $next)
    {
        try {
            return $next($request, $response);
        } catch (Exception $e) {
            return $response->withJson([
                'message' => $e->getMessage(),
                'success' => false
            ]);
        }
    }

}