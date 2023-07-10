<?php

namespace App\Http\Controllers;

use App\Models\Build;
use App\Models\TechsCodePlugin;
use App\Models\ReleaseVersion;
use App\Models\UpdateRequest;
use App\Models\UpdateRequestStatus;
use App\Services\MavenDownloader;
use Nette\Utils\Random;

class ApiController extends Controller
{
    public function onNewRelease(){
        $plugin_value_raw = request()->input('plugin_name');
        $release_title = request()->input('release_title');
        $release_description = request()->input('release_description');

        if (empty($plugin_value_raw) || empty($release_title)) {
            return response()->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'Missing required parameters. plugin_name, release_title are required.'
            ], 400);
        }

        $techscode_plugin = new TechsCodePlugin($plugin_value_raw);
        if (!$techscode_plugin->isValidPlugin()){
            return response()->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'Unknown plugin.'
            ], 400);
        }
        $plugin_value = $techscode_plugin->getValue();

        $release_title_parts = explode(' ', $release_title);

        $version = $release_title_parts[1];
        $build_text = explode('-', $release_title_parts[2]);
        $build_number = $build_text[1];

        if (empty($version) || empty($build_number)) {
            throw new \InvalidArgumentException('Invalid release title.');
        }

        if (!$this->isValidVersion($version)){
            return response()->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'Invalid version format. Must be in the format of x.x.x'
            ], 400);
        }

        $build_exists = Build::query()
            ->where('plugin_name', $plugin_value)
            ->where('build_number', $build_number)
            ->exists();
        if ($build_exists){
            return response()->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'Build already exists.'
            ], 400);
        }

        $build = new Build();
        $build->plugin_name = $plugin_value;
        $build->plugin_version = $version;
        $build->build_number = $build_number;
        $build->description = $release_description;
        $build->save();

        $is_new_release_version = ReleaseVersion::query()
            ->where('plugin_name', $plugin_value)
            ->where('plugin_version', $version)
            ->exists();

        if (!$is_new_release_version){
            $release_version = new ReleaseVersion();
            $release_version->build_id = $build->id;
            $release_version->plugin_name = $plugin_value;
            $release_version->plugin_version = $version;
            $release_version->save();
        }

        return response()->json([
            'message' => 'New release processed.'
        ]);
    }

    public function onVersionCheck(){
        $plugin_value_raw = request()->input('plugin_name');
        $current_version = request()->input('current_version');

        if (empty($plugin_value_raw) || empty($current_version)) {
            return response()->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'Missing required parameters. plugin_name, current_version are required'
            ], 400);
        }

        $techscode_plugin = new TechsCodePlugin($plugin_value_raw);
        if (!$techscode_plugin->isValidPlugin()){
            return response()->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'Unknown plugin.'
            ], 400);
        }
        $plugin_value = $techscode_plugin->getValue();

        if (!$this->isValidVersion($current_version)){
            return response()->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'Invalid version format. Must be in the format of x.x.x'
            ], 400);
        }

        /** @var ReleaseVersion $latest_version */
        $latest_version = ReleaseVersion::query()
            ->where('plugin_name', $plugin_value)
            ->orderBy('plugin_version', 'desc')
            ->first();
        if (!$latest_version){
            return response()->json([
                'status' => 'error',
                'code' => 404,
                'message' => 'Plugin not found.'
            ], 404);
        }

        if (version_compare($latest_version->plugin_version, $current_version) <= 0){
            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Plugin is up to date.',
                'is_up_to_date' => true,
                'latest_version' => $latest_version->plugin_version
            ], 200);
        }
        return response()->json([
            'status' => 'success',
            'code' => 200,
            'message' => 'Plugin is out of date.',
            'is_up_to_date' => false,
            'latest_version' => $latest_version->plugin_version
        ], 200);
    }

    public function onGetPlugins(){
        $update_token = request()->input('update_token');

        if (empty($update_token)){
            return response()->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'Missing required parameters. update_token is required.'
            ], 400);
        }

        /** @var UpdateRequest $update_request */
        $update_request = UpdateRequest::query()
            ->where('update_token', $update_token)
            ->first();
        if (!$update_request){
            return response()->json([
                'status' => 'error',
                'code' => 404,
                'message' => 'Update request not found.'
            ], 404);
        }

        $plugins = TechsCodePlugin::$valid_plugins;
        $response = [];

        foreach ($plugins as $key => $plugin){
            $allowed_plugins = $update_request->allowed_plugins;
            $plugins_allowed = $allowed_plugins === '*' || in_array($key, $allowed_plugins);

            /** @var ReleaseVersion $versions */
            $versions = ReleaseVersion::query()
                ->where('plugin_name', $key)
                ->orderBy('plugin_version', 'desc')
                ->get();
            $versions->makeHidden(['build', 'plugin_name', 'updated_at', 'id']);

            if($update_request->has_beta_access){
                /** @var Build $latest_build */
                $latest_build = Build::query()
                    ->where('plugin_name', $key)
                    ->orderBy('build_number', 'desc')
                    ->first()->build_number ?? 0;
            }else{
                $latest_build = null;
            }

            $response[$key] = [
                'name' => $plugin,
                'value' => $key,
                'versions' => $versions,
                'latest_version' => $versions->first()->plugin_version ?? '0.0.0',
                'latest_build' => $latest_build,
                'is_allowed' => $plugins_allowed
            ];
        }

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'message' => 'Plugins retrieved.',
            'data' => $response
        ]);
    }

    public function onRequestUpdateCreate(){
        $plugin_value_raw = request()->input('plugin_name');
        $current_version = request()->input('current_version');

        if (!empty($current_version) && !$this->isValidVersion($current_version)){
            return response()->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'Invalid version format. Must be in the format of x.x.x'
            ], 400);
        }

        $plugin_version = null;
        if (!empty($plugin_value_raw)) {
            $techscode_plugin = new TechsCodePlugin($plugin_value_raw);
            if (!$techscode_plugin->isValidPlugin()){
                return response()->json([
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'Unknown plugin.'
                ], 400);
            }
            $plugin_value = $techscode_plugin->getValue();

            /** @var ReleaseVersion $latest_version */
            $latest_version = ReleaseVersion::query()
                ->where('plugin_name', $plugin_value)
                ->orderBy('plugin_version', 'desc')
                ->first();
            if ($latest_version) {
                $plugin_version = $latest_version->plugin_version;
            }
        }else{
            $plugin_value = null;
        }

        $update_token = Random::generate(64);
        $update_request = new UpdateRequest();
        $update_request->update_token = $update_token;
        $update_request->plugin_name = $plugin_value;
        $update_request->current_version = $current_version;
        $update_request->update_to = $plugin_version;
        $update_request->save();

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'message' => 'Update request processed.',
            'update_token' => $update_token
        ]);
    }

    public function onRequestUpdateCheck(){
        $update_token = request()->input('update_token');

        /** @var UpdateRequest $update_request */
        $update_request = UpdateRequest::query()
            ->where('update_token', $update_token)
            ->first();
        if (!$update_request){
            return response()->json([
                'status' => 'error',
                'code' => 404,
                'message' => 'Update request not found.'
            ], 404);
        }

        $update_request->makeHidden('id');

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'message' => 'Update request found.',
            'update_request' => $update_request
        ]);
    }

    public function onRequestUpdateUpdate(){
        $update_token = request()->input('update_token');

        /** @var UpdateRequest $update_request */
        $update_request = UpdateRequest::query()
            ->where('update_token', $update_token)
            ->first();
        if (!$update_request){
            return response()->json([
                'status' => 'error',
                'code' => 404,
                'message' => 'Update request not found.'
            ], 404);
        }

        if($update_request->status === UpdateRequestStatus::DOWNLOADED){
            return response()->json([
                'status' => 'error',
                'code' => 403,
                'message' => 'Update request already completed.'
            ], 403);
        }
        if($update_request->status != UpdateRequestStatus::AUTHORIZED){
            return response()->json([
                'status' => 'error',
                'code' => 403,
                'message' => 'Update request not authorized.'
            ], 403);
        }

        $plugin_value_raw = request()->input('plugin_name');
        $current_version = request()->input('current_version');
        $update_to = request()->input('update_to');

        if (empty($plugin_value_raw) || empty($current_version) || empty($update_to)){
            return response()->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'Missing required fields. (plugin_name, current_version, update_to)'
            ], 400);
        }

        $techscode_plugin = new TechsCodePlugin($plugin_value_raw);
        if (!$techscode_plugin->isValidPlugin()){
            return response()->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'Unknown plugin.'
            ], 400);
        }
        $plugin_value = $techscode_plugin->getValue();

        if ($this->isPluginAllowed($update_request, $plugin_value) === false){
            return response()->json([
                'status' => 'error',
                'code' => 400,
                'message' => "Plugin not allowed."
            ], 400);
        }

        $update_request->plugin_name = $plugin_value;

        if (!$this->isValidVersion($current_version)){
            return response()->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'Invalid version format. Must be in the format of x.x.x'
            ], 400);
        }
        $update_request->current_version = $current_version;

        if ($update_to === "latest"){
            /** @var ReleaseVersion $latest_release_version */
            $latest_release_version = ReleaseVersion::query()
                ->where('plugin_name', $plugin_value)
                ->orderBy('plugin_version', 'desc')
                ->first();
            if (!$latest_release_version){
                return response()->json([
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'Plugin not found.'
                ], 404);
            }
            $update_to = $latest_release_version->plugin_version;
        }
        else if($update_to === "latest-beta"){
            if ($update_request->has_beta_access !== true){
                return response()->json([
                    'status' => 'error',
                    'code' => 403,
                    'message' => 'Beta builds are not allowed for this update request.'
                ], 403);
            }

            /** @var Build $latest_build_version */
            $latest_build_version = Build::query()
                ->where('plugin_name', $plugin_value)
                ->orderBy('plugin_version', 'desc')
                ->first();
            if (!$latest_build_version){
                return response()->json([
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'Plugin not found.'
                ], 404);
            }
            $update_to = $latest_build_version->plugin_version."_build-".$latest_build_version->build_number;
        }
        if (!$this->isValidVersion($update_to)){
            return response()->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'Invalid version format. Must be in the format of x.x.x'
            ], 400);
        }
        $update_request->update_to = $update_to;

        $update_request->save();

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'message' => 'Update request updated.',
            'update_request' => $update_request
        ]);
    }

    public function onDownloadJar(){
        $update_token = request()->input('update_token');
        if (empty($update_token)){
            return response()->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'Missing required parameter update_token.'
            ], 400);
        }

        /** @var UpdateRequest $update_request */
        $update_request = UpdateRequest::query()
            ->where('update_token', $update_token)
            ->first();
        if (!$update_request){
            return response()->json([
                'status' => 'error',
                'code' => 404,
                'message' => 'Update request not found.'
            ], 404);
        }

        if($update_request->status === UpdateRequestStatus::DOWNLOADED){
            return response()->json([
                'status' => 'error',
                'code' => 403,
                'message' => 'Update request already completed.'
            ], 403);
        }
        if($update_request->status != UpdateRequestStatus::AUTHORIZED){
            return response()->json([
                'status' => 'error',
                'code' => 403,
                'message' => 'Update request not authorized.'
            ], 403);
        }

        if ($this->isPluginAllowed($update_request, $update_request->plugin_name) === false){
            return response()->json([
                'status' => 'error',
                'code' => 400,
                'message' => "Plugin not allowed."
            ], 400);
        }

        $techscode_plugin = new TechsCodePlugin($update_request->plugin_name);
        if (!$techscode_plugin->isValidPlugin()){
            return response()->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'Unknown plugin.'
            ], 400);
        }

        $plugin_name = $techscode_plugin->getName();
        $plugin_version = $update_request->update_to;

        $mavenDownloader = new MavenDownloader();
        $mavenDownloader->setPluginName($plugin_name);
        $mavenDownloader->setPluginVersion($plugin_version);

        $filename = $mavenDownloader->download();
        if ($filename){
            $update_request->status = UpdateRequestStatus::DOWNLOADED;
            $update_request->save();

            $file = \Storage::drive('plugins')->path($filename);
            return response()->download($file, $plugin_name.".jar");
        } else {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => 'Failed to download plugin.'
            ], 500);
        }
    }




    private function isValidVersion(string $version): bool
    {
        return preg_match('#^(\d+\.)?(\d+\.)?(\d+)(-[a-z0-9]+)?(_build-\d+)?$#i', $version, $matches) !== 0;
    }

    private function isPluginAllowed(UpdateRequest $update_request, string $plugin_name){
        $allowed_plugins = $update_request->allowed_plugins;
        return !empty($allowed_plugins) && in_array($plugin_name, $allowed_plugins);
    }

}
