<?php

namespace App\Http\Controller;

use Pop\Http\Server\Response;

class IndexController extends AbstractController
{

    /**
     * Index action
     *
     * @return void
     */
    public function index(): void
    {
        $this->renderPage(
            'index.phtml',
            'Pop PDF — A standalone PDF library for PHP',
            'home',
            'Generate documents, import and modify the ones you have, merge them, and read the content back out — one dependency.'
        );
    }

    /**
     * Build action
     *
     * @return void
     */
    public function build(): void
    {
        $this->renderPage(
            'build.phtml',
            'Build — Pop PDF',
            'build',
            'Make a PDF from nothing. Documents, fonts, annotations, paths, HTML-to-PDF and forms.'
        );
    }

    /**
     * Import action
     *
     * @return void
     */
    public function import(): void
    {
        $this->renderPage(
            'import.phtml',
            'Import — Pop PDF',
            'import',
            'Change a PDF you already have: import and modify, merge, and build a PDF from images.'
        );
    }

    /**
     * Extract action
     *
     * @return void
     */
    public function extract(): void
    {
        $this->renderPage(
            'extract.phtml',
            'Extract — Pop PDF',
            'extract',
            'Get the content back out: native text extraction, image-only detection, and page rasterization.'
        );
    }

    /**
     * License action
     *
     * Served as plain text, not a view - the same as popphp.org/license.
     *
     * @return void
     */
    public function license(): void
    {
        $this->send(
            200, file_get_contents($this->viewPath . '/license.txt'), 'OK', ['Content-Type' => 'text/plain']
        );
    }

    /**
     * Error action
     *
     * @param  int     $code
     * @param  ?string $message
     * @return void
     */
    public function error(int $code = 404, ?string $message = null): void
    {
        if ($this->request->acceptsHtml()) {
            $message = $message ?? Response::getMessageFromCode($code);

            $this->prepareView('error.phtml');
            $this->view->title   = $code . ' ' . $message . ' — Pop PDF';
            $this->view->code    = $code;
            $this->view->message = $message;
            $this->send($code);
        } else {
            parent::error($code, $message);
        }
    }

    /**
     * Maintenance action
     *
     * @param  int     $code
     * @param  ?string $message
     * @return void
     */
    public function maintenance(int $code = 503, ?string $message = null): void
    {
        if ($this->request->acceptsHtml()) {
            $this->prepareView('maintenance.phtml');
            $this->view->title = 'Back shortly — Pop PDF';
            $this->send($code);
        } else {
            parent::error($code, $message);
        }
    }

}
