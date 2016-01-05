<?php
namespace SkeletonAPI\Controllers;

use Illuminate\Database\QueryException;
use Respect\Validation\Exceptions\NestedValidationException;
use SkeletonAPI\lib\UtilTrait;
use SkeletonAPI\Models\User as UserModel;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class User
 * @package SkeletonAPI\Controllers
 *
 * The user controller, allows for
 */
class User extends AbstractController
{

    use UtilTrait;

    /**
     * Return all the users that are in the database
     * @todo Log the response
     */
    public function all(Request $request, Response $response, array $args)
    {
        return $response->withJson(UserModel::all());
    }

    /**
     * Create a new user with the data provided in the request body and return a JWT to start the User's session
     * @todo Create the JWT
     * @todo Log the request and response
     */
    public function create(Request $request, Response $response, array $args)
    {
        try {
            $data = $request->getParsedBody();
            $user = UserModel::create($data)->toArray();
            $jwt = [
                "email" => $user["email"],
                "id" => $user["uid"]
            ];
            $jwt = $this->encodeJWT($jwt);
            return $response->withJson($jwt);
        } catch (NestedValidationException $e) {
            //todo log
            $messages = $this->formatMessages($e);
            return $response->withJson($messages, 400);
        } catch (QueryException $e) {
            //todo log
            return $response->withStatus(500);
        }
    }

    /**
     * Find a user
     */
    public function find(Request $request, Response $response, array $args)
    {
        return $response->withJson(UserModel::find($args["uid"]));
    }

    /**
     * Update a user
     */
    public function update(Request $request, Response $response, array $args)
    {
        $uid = $args["uid"];
    }

    /**
     * Delete a user
     */
    public function delete(Request $request, Response $response, array $args)
    {
        $uid = $args["uid"];
    }
}