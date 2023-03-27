<?php

namespace JustBetter\MagentoClient\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use JustBetter\MagentoClient\Contracts\OAuth\StoresIntegrationDetails;
use Symfony\Component\HttpFoundation\Response;

class OAuthController extends Controller
{
    public function identity(Request $request): RedirectResponse
    {
        // store consumer key

        return redirect()->to($request->get('success_call_back'));
    }

    public function callback(Request $request, StoresIntegrationDetails $contract): Response
    {
        $contract->store($request->all());

        return response()->json();
    }
}
