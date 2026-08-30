<?php

namespace App\Http\Controller;

use Pop\Dispatch\HttpTrait;
use Pop\Http\Server\Response;
use Pop\View\View;

abstract class AbstractController extends \Pop\Controller\AbstractController
{

    /**
     * Traits
     */
    use HttpTrait;

    /**
     * View path
     * @var string
     */
    protected string $viewPath = __DIR__ . '/../../../view';

    /**
     * View object
     * @var ?View
     */
    protected ?View $view = null;

    /**
     * Get view object
     *
     * @return View
     */
    public function getView(): View
    {
        return $this->view;
    }

    /**
     * Determine if the controller has a view
     *
     * @return bool
     */
    public function hasView(): bool
    {
        return ($this->view !== null);
    }

    /**
     * Redirect method
     *
     * @param  string $url
     * @param  int    $code
     * @param  string $version
     * @return void
     */
    public function redirect(string $url, int $code = 302, string $version = '1.1'): void
    {
        Response::redirect($url, $code, $version);
        exit();
    }

    /**
     * Send method (renders the current view as an HTML response body)
     *
     * @param  int     $code
     * @param  mixed   $body
     * @param  ?string $message
     * @param  ?array  $headers
     * @return void
     */
    public function send(int $code = 200, mixed $body = null, ?string $message = null, ?array $headers = null): void
    {
        if (($body === null) && ($this->view !== null)) {
            $body = $this->view->render();
        }

        if ($message !== null) {
            $this->response->setMessage($message);
        }

        $this->response->addHeader('Content-Type', 'text/html');
        $this->response->setCode($code);
        $this->response->setBody($body . PHP_EOL . PHP_EOL);
        $this->response->send(null, $headers);
    }

    /**
     * Send JSON method
     *
     * @param  int     $code
     * @param  mixed   $body
     * @param  ?string $message
     * @param  ?array  $headers
     * @return void
     */
    public function sendJson(int $code = 200, mixed $body = null, ?string $message = null, ?array $headers = null): void
    {
        $this->response->setCode($code);

        if ($message !== null) {
            $this->response->setMessage($message);
        }

        $this->response->addHeaders($this->application->config['http_options_headers'] ?? []);

        $responseBody = (($this->response->getHeaderValue('Content-Type') == 'application/json') && ($body !== null) && ($body != '')) ?
            json_encode($body, JSON_PRETTY_PRINT) : $body;

        $this->response->setBody($responseBody . PHP_EOL . PHP_EOL);
        $this->response->send(null, $headers);
    }

    /**
     * Send options
     *
     * @param  int     $code
     * @param  ?string $message
     * @param  ?array  $headers
     * @return void
     */
    public function sendOptions(int $code = 200, ?string $message = null, ?array $headers = null): void
    {
        $this->sendJson($code, '', $message, $headers);
    }

    /**
     * Send error
     *
     * @param  int     $code
     * @param  ?string $message
     * @return void
     */
    public function error(int $code = 404, ?string $message = null): void
    {
        if ($message === null) {
            $message = Response::getMessageFromCode($code);
        }

        $responseBody = json_encode(['code' => $code, 'message' => $message], JSON_PRETTY_PRINT) . PHP_EOL . PHP_EOL;

        $this->response->setCode($code)
            ->setMessage($message)
            ->addHeaders($this->application->config['http_options_headers'] ?? [])
            ->setBody($responseBody)
            ->sendAndExit();
    }

    /**
     * Send maintenance
     *
     * @param  int     $code
     * @param  ?string $message
     * @return void
     */
    public function maintenance(int $code = 503, ?string $message = null): void
    {
        $this->error($code, $message);
    }

    /**
     * Prepare view
     *
     * @param  string $template
     * @return void
     */
    protected function prepareView(string $template): void
    {
        $this->view = new View($this->viewPath . '/' . $template);
    }

    /**
     * Render a page view
     *
     * The 3-line helper every static page action calls. $page is read by the
     * nav partial for the current-page state; $description feeds the meta and
     * social tags in the head partial.
     *
     * @param  string  $template
     * @param  string  $title
     * @param  ?string $page
     * @param  ?string $description
     * @return void
     */
    protected function renderPage(string $template, string $title, ?string $page = null, ?string $description = null): void
    {
        $this->prepareView($template);
        $this->view->title = $title;
        $this->view->page  = $page;

        if ($description !== null) {
            $this->view->description = $description;
        }

        $this->send();
    }

}
