<?php

namespace JustBetter\MagentoClient\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use JustBetter\MagentoClient\Actions\OAuth\RequestAccessToken;
use JustBetter\MagentoClient\Contracts\OAuth\ManagesKeys;
use JustBetter\MagentoClient\Http\Requests\CallbackRequest;
use JustBetter\MagentoClient\Http\Requests\IdentityRequest;
use Symfony\Component\HttpFoundation\Response;

class OAuthController extends Controller
{
    public function identity(IdentityRequest $request, ManagesKeys $keys): RedirectResponse
    {
        $keys->merge(
            $request->validated(),
        );

        return redirect()->to($request->success_call_back);
    }

    public function callback(CallbackRequest $request, RequestAccessToken $requestAccessToken): Response
    {
        $requestAccessToken->request(
            $request->validated(),
        );

        return response()->json();
    }
}
