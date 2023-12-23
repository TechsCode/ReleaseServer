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
                    'error_title' => 'Invalid Request',
                    'error_message' => 'Missing update token.',
                ]);
        }

        /** @var UpdateRequest $update_request */
        $update_request = UpdateRequest::query()
            ->where('update_token', $update_token)
            ->first();
        if (empty($update_request)) {
            return view('pages.authorize')
                ->with([
                    'error_title' => 'Invalid Request',
                    'error_message' => 'Invalid update token.',
                ]);
        }

        $techscode_plugin = new TechsCodePlugin($update_request->plugin_name);
        if (!$techscode_plugin->isValidPlugin()) {
            return view('pages.authorize')
                ->with([
                    'error_title' => 'Update Failed',
                    'error_message' => 'This plugin is not registered with TechsCode.',
                ]);
        }

        if (
            $update_request->status === UpdateRequestStatus::AUTHORIZED ||
            $update_request->status === UpdateRequestStatus::DOWNLOADED
        ){
            return view('pages.authorize')
                ->with([
                    'error_title' => 'Update Failed',
                    'error_message' => 'This update request has already been authorized.',
                ]);
        }

        return view('pages.authorize')
            ->with([
                'update_token' => $update_token,
                'plugin_name' => $techscode_plugin->getName(),
                'current_version' => $update_request->current_version,
                'current_version_date' => $update_request->current_version_date,
                'update_to' => $update_request->update_to,
            ]);
    }
}
