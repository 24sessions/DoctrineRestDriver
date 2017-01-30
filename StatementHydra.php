<?php

namespace DoctrineRestDriver;

use DoctrineRestDriver\Enums\HttpMethods;
use DoctrineRestDriver\Types\Request;
use DoctrineRestDriver\Types\Result;
use Symfony\Component\HttpFoundation\Response;

class StatementHydra extends Statement {
    /**
     * @var integer
     */
    protected $loadedPage;
    /**
     * @var integer
     */
    protected $totalPages;
    /**
     * @var string
     */
    protected $initialQuery;

    /**
     * {@inheritdoc}
     */
    public function execute($params = null) {
        $rawRequest = $this->mysqlToRequest->transform($this->query, $this->params);
        $request    = $this->authStrategy->transformRequest($rawRequest);
        $restClient = $this->restClientFactory->createOne($request->getCurlOptions());

        $method = strtolower($request->getMethod());
        $successedStatusCode = $this->getSuccessStatusCode($method);
        $this->initialQuery = $request->getQuery();
        $this->loadedPage = 0;
        $result = [];

        do {
            $this->loadedPage++;
            $this->setLoadedPage($request);

            $response   = $method === HttpMethods::GET || $method === HttpMethods::DELETE ? $restClient->$method($request->getUrlAndQuery()) : $restClient->$method($request->getUrlAndQuery(), $request->getPayload());
            /** @var $response Response */
            $statusCode = $response->getStatusCode();

            if ($statusCode === $successedStatusCode) {
                $result = array_merge($result, $this->onSuccess($response, $method)); // parse and collect data
            } else {
                return $this->onError($request, $response);
            }

        } while ($this->loadedPage < $this->totalPages);

        $this->result = Result::create($this->query, $result);
        $this->id     = $method === HttpMethods::POST ? $this->result['id'] : null;
        krsort($this->result);

        return true;
    }

    protected function setLoadedPage(Request $request) {
        if ($this->loadedPage > 1) {
            $separator = !empty($this->initialQuery) ? '&' : '';
            $request->setQuery("{$this->initialQuery}{$separator}page={$this->loadedPage}");
        }
    }

    /**
     * @param string $body
     */
    protected function setTotalPages($body) {

        if (!empty($body['hydra:totalItems']) && !empty($body['hydra:itemsPerPage'])) {

            $this->totalPages = round($body['hydra:totalItems'] / $body['hydra:itemsPerPage']);
            if ($this->totalPages <= 0) {
                $this->totalPages = 1;
            }
        } else {
            $this->totalPages = 1;
        }
    }

    /**
     * Handles the statement if the execution succeeded
     *
     * @param  Response $response
     * @param  string   $method
     * @return array
     *
     * @SuppressWarnings("PHPMD.StaticAccess")
     */
    protected function onSuccess(Response $response, $method) {
        $body = json_decode($response->getContent(), true);

        $this->setTotalPages($body);

        if (!empty($body['hydra:totalItems'])) {
            if ($body['hydra:totalItems'] > 0 && !empty($body['hydra:member'])) {
                foreach ($body['hydra:member'] as &$item) {
                    $item = $this->parseIds($item);
                }
                return $body['hydra:member'];
            } else {
                return [];
            }
        } else {
            return $this->parseIds($body);
        }
    }

    protected function getSuccessStatusCode($method) {
        $statusByMethod = [
            HttpMethods::POST => 201,
            HttpMethods::DELETE => 204,
        ];
        return !empty($statusByMethod[$method]) ? $statusByMethod[$method] : 200;
    }

    /**
     * @param string $input
     * @return int
     */
    protected function parseIds($input) {
        $result = array();
        foreach ($input as $key => $value) {
            if ($key == '@id') {
                $result['id'] = (int)substr($value, strrpos($value, '/') + 1);
            }
            if (is_array($value)) {
                $value = $this->parseIds($value);
            }
            $result[$key] = $value;
        }
        return $result;
    }
}