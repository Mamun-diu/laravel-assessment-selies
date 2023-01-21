<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, ApiResponse;

    protected const ROWS_PER_PAGE = 20;
    protected const MAX_PER_PAGE = 100;

    /**
     * Pagination
     * @param  array  $options
     * @param  array $request
     * @return array
     */
    protected function initialize($options = [], $request = null)
    {
        if (!isset($request['rows_per_page']) || empty($request['rows_per_page']) || !preg_match('/^[1-9]+[0-9]*$/', $request['rows_per_page'])) {
            $options['rows_per_page'] = self::ROWS_PER_PAGE;
        } else {
            $options['rows_per_page'] = $request['rows_per_page'];
        }
        $max = isset($options['max_per_page']) && !empty($options['max_per_page']) ? $options['max_per_page'] : self::MAX_PER_PAGE;
        $options['rows_per_page'] = $options['rows_per_page'] > $max ? $max : $options['rows_per_page'];

        return $options;
    }

    /**
     * Get All Status Code Texts
     * @param  integer $code
     * @return string
     */
    public function getStatus($code = 444)
    {
        $messages = array(
            200 => 'Ok',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            408 => 'Request Timeout',
            416 => 'Requested Range Not Satisfiable',
            444 => 'No Response',
            500 => 'Internal Server Error',
            503 => 'Be right back',
        );
        if (!array_key_exists($code, $messages)) {
            $code = 444;
        }
        return ['code' => $code, 'text' => $messages[$code]];
    }

    /**
     * Get the instance as an array.
     * @return array
     */
    public function toArray($data)
    {
        if (!empty($data)) {
            $array = $data->toArray();
            return [
                'total' => $array['total'],
                'rows_per_page' => $array['per_page'],
                'next_page_url' => $array['next_page_url'],
                'current_page' => $array['current_page'],
                'prev_page_url' => $array['prev_page_url'],
            ];
        }
    }
}
