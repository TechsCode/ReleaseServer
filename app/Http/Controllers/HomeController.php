<?php

namespace App\Http\Controllers;

use App\Models\TechsCodePlugin;
use App\Models\UpdateRequest;
use App\Models\UpdateRequestStatus;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function homePage()
    {
        return view('pages.home');
    }

    public function authorizePage(Request $request, string $update_token)
    {
        if (empty($update_token)) {
            return view('pages.authorize')
                ->with([
                    'update_token' => null,
                    'plugin_name' => null,
                    'current_version' => null,
                    'update_to' => null,
                    'show_auth_button' => false,
                    'error_message' => 'Invalid update token.',
                ]);
        }

        /** @var UpdateRequest $update_request */
        $update_request = UpdateRequest::query()
            ->where('update_token', $update_token)
            ->first();
        if (empty($update_request)) {
            return view('pages.authorize')
                ->with([
                    'update_token' => null,
                    'plugin_name' => null,
                    'current_version' => null,
                    'update_to' => null,
                    'show_auth_button' => false,
                    'error_message' => 'Invalid update token.',
                ]);
        }

        $techscode_plugin = new TechsCodePlugin($update_request->plugin_name);
        if (!$techscode_plugin->isValidPlugin()) {
            return view('pages.authorize')
                ->with([
                    'update_token' => null,
                    'plugin_name' => null,
                    'current_version' => null,
                    'update_to' => null,
                    'show_auth_button' => false,
                    'error_message' => 'Invalid plugin name.',
                ]);
        }

        if (
            $update_request->status === UpdateRequestStatus::AUTHORIZED ||
            $update_request->status === UpdateRequestStatus::UPDATED
        ){
            return view('pages.authorize')
                ->with([
                    'update_token' => null,
                    'plugin_name' => null,
                    'current_version' => null,
                    'update_to' => null,
                    'show_auth_button' => false,
                    'error_message' => 'This update request has already been authorized.',
                ]);
        }

        return view('pages.authorize')
            ->with([
                'update_token' => $update_token,
                'plugin_name' => $techscode_plugin->getName(),
                'current_version' => $update_request->current_version,
                'update_to' => $update_request->update_to,
                'show_auth_button' => true,
                'error_message' => '',
            ]);
    }
}
