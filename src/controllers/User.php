<?php
namespace SkeletonAPI\Controllers;

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
     * @todo Validate data before creating
     * @todo Create the JWT
     * @todo Log the request and response
     */
    public function create(Request $request, Response $response, array $args)
    {
        $data = $request->getParsedBody();
        $user = UserModel::create($data);
        $jwt = '';
        return $response->withJson($jwt);
    }

    public function find(Request $request, Response $response, array $args)
    {

        return $response->withJson(UserModel::all());
    }

    public function update(Request $request, Response $response, array $args)
    {

    }

    public function delete(Request $request, Response $response, array $args)
    {

    }
}