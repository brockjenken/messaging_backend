<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Response;
use Opis\JsonSchema\{Validator, Schema};
use Symfony\Component\HttpKernel\Exception\{BadRequestHttpException, ConflictHttpException, HttpException};


class AppController extends BaseController
{    
    /**
     * @var string
     */
    protected const ADDITIONAL_PROPERTY_CAUSE = "additionalProperties";

    /**
     * @var string
     */
    protected const REQUIRED_CAUSE = "required";

    /**
     * Used to provide responses with a given status, body, and optional error message.
     * 
     * @param integer $status
     * @param string|null $body
     * @param string $error_message
     * @return Response
     */
    protected function response(int $status, ?string $body=null, string $error_message=null) : Response
    {
        if ($error_message){
            $body = json_encode(["error"=> $error_message]);
        }

        
        if (!$body){
            $body = "";  // Empty string is used as default as that's what the Response object defaults to.
        }

        return new Response($body, $status, ["Content-Type"=> "application/json"]);
    }

    /**
     * This used to validate incoming request bodies. Uses JSON Schema to standardize process.
     * 
     * Schema are divided by whether they are used to create or update data.
     *
     * @param string $method
     * @param string $type
     * @param array $data
     * @return bool
     * @throws BadRequestHttpException
     * @throws HttpException
     */
    protected function validateData(string $method, string $type, array $data) : bool
    {
        $schema = Schema::fromJsonString(file_get_contents(base_path() . "/app/Resources/Schema/" . ucfirst($method) . "/{$type}.json"));
        $validator = new Validator();

        $result = $validator->schemaValidation((object) $data, $schema);

        if (!$result->isValid()) {
            $cause = $result->getFirstError()->keyword();

            if ($cause === self::ADDITIONAL_PROPERTY_CAUSE){
                throw new BadRequestHttpException("Additional properties not allowed");
            } elseif ($cause === self::REQUIRED_CAUSE){
                throw new BadRequestHttpException("Ensure that all fields are included");
            } else {
                throw new HttpException("Your request was refused for unknown reasons.");
            }
        }

        return TRUE;
    }
}
 