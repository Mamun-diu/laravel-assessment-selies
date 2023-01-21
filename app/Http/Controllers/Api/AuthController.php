<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\{
    LoginUserRequest,
    RegisterUserRequest
};
use App\Models\User;
use Laravel\Passport\Client as OClient;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Auth;

class AuthController extends Controller
{
    /**
     * User Authentication
     *
     * @param LoginUserRequest $request
     * @return json
     */
    public function login(LoginUserRequest $request)
    {
        if (!auth()->attempt($request->only(['email', 'password']))) {
            return $this->unprocessableResponse(['message' => 'Email or Password is invalid.']);
        }

        $user = User::select('first_name', 'last_name', 'email', 'type')->firstWhere('email', $request->email)->toArray();
        $response = $user + $this->getTokenAndRefreshToken($request->email, $request->password);

        return $this->successResponse($response);
    }

    /**
     * User Registration
     *
     * @param RegisterUserRequest $request
     * @return json
     */
    public function register(RegisterUserRequest $request)
    {
        $password = $request->password;

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);

        if (User::create($input)) {
            return $this->successResponse(['user_type' => 'user'] + $this->getTokenAndRefreshToken($request->email, $password));
        }

        return $this->errorResponse('Something went wrong please try again.');
    }

    /**
     * Generate new token with refresh token
     *
     * @param Request $request
     * @return json
     */
    public function refreshToken(Request $request)
    {
        $oClient = OClient::where('password_client', 1)->first();

        $response = Http::asForm()->post(env('APP_URL') . 'oauth/token', [
            'grant_type' => 'refresh_token',
            'refresh_token' => $request->token,
            'client_id' => $oClient->id,
            'client_secret' => $oClient->secret,
            'scope' => '',
        ]);

        $result = json_decode((string) $response->getBody(), true);

        if (isset($result['error'])) {
            return $this->unprocessableResponse($result);
        }

        return $this->successResponse($result);
    }

    /**
     * Get token and refresh token
     *
     * @param string $email
     * @param string $password
     * @return array
     */
    private function getTokenAndRefreshToken($email, $password)
    {
        $http = new Client;
        $oClient = OClient::where('password_client', 1)->first();

        $response = $http->request('POST', env('APP_URL') . 'oauth/token', [
            'form_params' => [
                'grant_type' => 'password',
                'client_id' => $oClient->id,
                'client_secret' => $oClient->secret,
                'username' => $email,
                'password' => $password,
                'scope' => '*',
            ],
        ]);
        $result = json_decode((string) $response->getBody(), true);

        return $result;
    }

    /**
     * User Logout
     *
     * @return json
     */
    public function logout()
    {
        if (Auth::guard('api')->user()->token()->delete()) {
            return $this->successResponse('You have successfully logout');
        }

        return $this->errorResponse('Something went wrong please try again.');
    }
}
