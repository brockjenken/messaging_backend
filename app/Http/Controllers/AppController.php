<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Http\{Request, Response};
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Response as HttpResponse;
use Opis\JsonSchema\{
    Validator, ValidationResult, ValidationError, Schema
};
use Symfony\Component\HttpKernel\Exception\{BadRequestHttpException, ConflictHttpException, HttpException};


class AppController extends BaseController
{    
    protected const ADDITIONAL_PROPERTY_CAUSE = "additionalProperties";
    protected const REQUIRED_CAUSE = "required";

    protected function response(int $status, ?string $body=null, string $error_message=null)
    {
        if ($error_message){
            $body = json_encode(["error"=> $error_message]);
        }

        if (!$body){
            $body = "";
        }

        return new HttpResponse($body, $status, ["Content-Type"=> "application/json"]);
    }

    protected function validate_data(string $method, string $type, array $data)
    {
        $schema = Schema::fromJsonString(file_get_contents(base_path() . "/app/Resources/Schema/" . ucfirst($method) . "/{$type}.json"));
        $validator = new Validator();

        $result = $validator->schemaValidation((object) $data, $schema);

        if (!$result->isValid()) {
            $cause = $result->getFirstError()->keyword();

            if ($cause === self::ADDITIONAL_PROPERTY_CAUSE){
                throw new ConflictHttpException("Additional properties not allowed");
            } elseif ($cause === self::REQUIRED_CAUSE){
                throw new BadRequestHttpException("Ensure that all fields are included");
            } else {
                throw new HttpException("Your request was refused for unknown reasons.");
            }
        }

        return TRUE;
    }
}
 