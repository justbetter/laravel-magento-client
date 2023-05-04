<?php

namespace JustBetter\MagentoClient\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use JustBetter\MagentoClient\Contracts\OAuth\ManagesKeys;
use JustBetter\MagentoClient\Contracts\OAuth\RequestsAccessToken;
use JustBetter\MagentoClient\Http\Requests\CallbackRequest;
use JustBetter\MagentoClient\Http\Requests\IdentityRequest;
use Symfony\Component\HttpFoundation\Response;

class OAuthController extends Controller
{
    public function callback(CallbackRequest $request, ManagesKeys $contract): Response
    {
        $contract->merge([
            'callback' => $request->validated(),
        ]);

        return response()->json();
    }

    public function identity(IdentityRequest $request, RequestsAccessToken $contract): RedirectResponse
    {
        $contract->request(
            $request->oauth_consumer_key,
        );

        return redirect()->to($request->success_call_back);
    }
}
