<?php

namespace App\Http\Controllers;

use App\Exception\BadRequestException;
use App\Http\Request;
use App\Http\Response;

/**
 * Class Controller
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  25.03.2019
 */
class RestController extends Controller
{
    /**
     * Response validation failure type value
     * @string
     */
    protected const FAILURE_TYPE_INVALID_DATA = "Invalid data";

    /**
     * @inheritdoc
     * @see Controller::__construct()
     *
     * @throws BadRequestException
     */
    public function __construct(Request $request)
    {
        if (!$request->isJson()) {
            throw new BadRequestException("Request is not JSON");
        }

        parent::__construct($request);

        $this->response->addHeaders([
            "Access-Control-Allow-Origin" => "*",
            "Access-Control-Allow-Methods" => "*",
            "Content-Type" => "application/json",
        ]);
    }

    /**
     * Processes failed response
     *
     * @param string     $failure
     * @param array|null $data
     *
     * @return Response
     */
    protected function fail(string $failure, array $data = null): Response
    {
        $code = 400;

        if ($failure === static::FAILURE_TYPE_INVALID_DATA) {
            $code = 412;
        }

        return $this->response->setStatusCode($code)
            ->setBody(json_encode($data));
    }

    /**
     * Processes successful response
     *
     * @param array $data
     * @param int   $code
     *
     * @return Response
     */
    protected function success(array $data, int $code = 200): Response
    {
        return $this->response->setStatusCode($code)
            ->setBody(json_encode($data));
    }
}
