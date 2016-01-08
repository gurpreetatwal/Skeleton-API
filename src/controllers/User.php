<?php
namespace SkeletonAPI\Controllers;

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
     * @todo Figure out way to make exceptions more DRY, the way its currently set up each method would have all of these
     *       blocks.
     */
    public function create(Request $request, Response $response, array $args)
    {
        $logger = $this->getLogger();
        $data = $request->getParsedBody();
        $logger->addInfo('Creating new user', $data);
        $user = UserModel::create($data)->toArray();
        $jwt = [
            "email" => $user["email"],
            "id" => $user["uid"]
        ];
        $jwt = $this->encodeJWT($jwt);
        return $response->withJson($jwt);

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